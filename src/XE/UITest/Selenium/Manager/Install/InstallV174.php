<?php
namespace XE\UITest\Selenium\Manager\Install;

use XE\UITest\Selenium\Manager\ManagerAbstract;
use XE\UITest\Selenium\Util\Output;
use XE\UITest\Selenium\Util\SshHandler;

/**
  * @file InstallV174.php
  * @brief XE 설치 테스트, version for 1.7.4
  * @author NAVER (developers@xpressengine.com)
  */
class InstallV174 extends ManagerAbstract implements InstallInterface
{
    /**
      * @brief SshHandler object
      */
    private $ssh;

    /**
      * @brief 테스트 완료 후 처리
      * @return void
      */
    public function endTest()
    {
        $installInfo = $this->oConfig->getInstall();
        if ($installInfo['test_remove'] === true) {
            $config = $this->oConfig->getConfig();
            $this->_uninstall($config);
        }
    }

    /**
      * @brief 테스트 수행하기 전 설정에 따른 uninstall 수행 및 XE download, Database create
      * @return void
      */
    public function preset()
    {
        $config = $this->oConfig->getConfig();

        if ($config['INSTALL']['reset'] === true) {
            $this->_reset($config);
        }

        if ($config['INSTALL']['reset'] !== true && $config['INSTALL']['prefix'] == "" && $config['INSTALL']['overwrite'] === true) {
            $this->_uninstall($config);
        }

        try {
            $this->_install($config);
        } catch (\Exception $e) {
            $this->_uninstall($config);
            Output::error(trim($e->getMessage()));
        }
    }

    /**
      * @brief install. XE download, Database create
      * @param array $config
      * @return void
      */
    private function _install($config)
    {
        $this->_connectSSH($config['TARGET_SERVER']);
        $this->_setTargetServerSource($config);
        $this->_disconnectSSH();
        $this->_createDatabase($config['DATABASE']);
    }

    /**
      * @brief 설치된 모든 XE test 관련 파일 및 Database 삭제
      * @param array $config
      * @return void
      */
    private function _reset($config)
    {
        // reset all ../ directory
        $this->_connectSSH($config['TARGET_SERVER']);
        $this->_removeTargetServerSource($config, true);
        $this->_disconnectSSH();
        $this->_dropDatabaseAll($config['DATABASE']);
    }

    /**
      * @brief uninstall. XE 파일 삭제, Database drop
      * @param array $config
      * @return void
      */
    private function _uninstall($config)
    {
        try {
            $this->_connectSSH($config['TARGET_SERVER']);
            $this->_removeTargetServerSource($config);
            $this->_disconnectSSH();
            $this->_dropDatabase($config['DATABASE']);
        } catch (\Exception $e) {
            Output::error(trim($e->getMessage()));
        }
    }

    /**
      * @brief SshHandler 를 이용해서 web server 에 SSH 로그인
      * @param array $config
      * @return void
      * @see SshHandler
      */
    private function _connectSSH($config)
    {
        try {
            if (isset($config['ssh_ip'])) {
                $host = $config['ssh_ip'];
            } else {
                $parseUrl = parse_url($config['url']);
                $host = $parseUrl['host'];
            }
            $port = $config['ssh_port'];
            $this->ssh = new SshHandler($host, $port);
            $this->ssh->auth($config['ssh_userid'], $config['ssh_password']);
        } catch (\Exception $e) {
            Output::error(trim($e->getMessage()));
            $this->_disconnectSSH();
        }
    }

    /**
      * @brief webserver source download, XE download
      * @param array $config
      * @return void
      * @see SshHandler
      */
    private function _setTargetServerSource($config)
    {
        $cmd = sprintf("mkdir %s && ", $config['TARGET_SERVER']['document_root']);
        $cmd .= sprintf("cd %s && ", $config['TARGET_SERVER']['document_root']);
        $cmd .= join(" && ", $config['SOURCE_CMD']);
        $resp = $this->ssh->exec($cmd);
        Output::info("Process Commend : {$cmd}");
        Output::warning($resp);
    }

    /**
      * @brief webserver source remove, XE file 삭제
      * @param array $config
      * @param boolean $isReset
      * @return void
      * @see SshHandler
      */
    private function _removeTargetServerSource($config, $isReset = false)
    {
        if ($isReset !== true) {
            $this->ssh->ssh->setTimeout(2);
            $cmd = sprintf("rm -Rf %s", $config['TARGET_SERVER']['document_root']);
        } else {
            $this->ssh->ssh->setTimeout(2);
            $cmd = sprintf("rm -Rf %s && mkdir %s", $config['TARGET_SERVER']['document_root_org'], $config['TARGET_SERVER']['document_root_org']);
        }
        $this->ssh->write($cmd . "\n");
        $resp = $this->ssh->read();
    }

    /**
      * @brief SSH disconnect
      * @return void
      */
    private function _disconnectSSH()
    {
        $this->ssh->disconnect();
    }

    /**
      * @brief connect database by PDO
      * @param array $dbInfo
      * @return object|boolean
      */
    private function _connectDatabse($dbInfo)
    {
        //mysql connect
        $dns = sprintf('%s:host=%s;dbname=%s;', $dbInfo['type'], $dbInfo['host'], $dbInfo['database']);
        if (isset($dbInfo['port'])) {
            $dns .= sprintf('port=%s;', $dbInfo['port']);
        }
        if (isset($dbInfo['charset'])) {
            $dns .= sprintf('charset=%s;', $dbInfo['charset']);
        }

        try {
            $connection = new \PDO(
                $dns,
                $dbInfo['userid'],
                $dbInfo['password']
            );

            return $connection;
        } catch (\Exception $e) {
            // DB 가 없음
            $dns = sprintf('%s:host=%s;', $dbInfo['type'], $dbInfo['host']);
            if (isset($dbInfo['port'])) {
                $dns .= sprintf('port=%s;', $dbInfo['port']);
            }
            if (isset($dbInfo['charset'])) {
                $dns .= sprintf('charset=%s;', $dbInfo['charset']);
            }

            try {
                $connection = new \PDO(
                    $dns,
                    $dbInfo['userid'],
                    $dbInfo['password']
                );
                return $connection;
            } catch (\Exception $e) {
                throw new \Exception("Failed connect database : " . $e->getMessage());
                return false;
            }
        }
    }

    /**
      * @brief Create Database
      * @param array $dbInfo
      * @return boolean
      */
    public function _createDatabase($dbInfo)
    {
        $connection = $this->_connectDatabse($dbInfo);
        if (!$connection) {
            return false;
        }

        $query = sprintf("CREATE DATABASE %s", $dbInfo['database']);
        $stmt = $connection->prepare($query);
        $stmt->execute();
        $errorInfo = $stmt->errorInfo();
        if ($errorInfo[0] != "00000") {
            throw new \Exception(sprintf("Create Database Error : %s", $errorInfo[2]));
        }
        return true;
    }

    /**
      * @brief Create Database
      * @param array $dbInfo
      * @return boolean
      */
    private function _dropDatabaseAll($dbInfo)
    {
        $connection = $this->_connectDatabse($dbInfo);
        if (!$connection) {
            return false;
        }

        $query = "SHOW DATABASES";
        $stmt = $connection->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($result as $value) {
            $pos = strpos($value['Database'], $dbInfo['database_org']);
            if ($pos === false) {
                continue;
            }
            
            $query = sprintf("DROP DATABASE %s", $value['Database']);
            $stmt = $connection->prepare($query);
            $stmt->execute();
            $errorInfo = $stmt->errorInfo();
            if ($errorInfo[0] != "00000") {
                throw new \Exception(sprintf("Drop Database Error : %s", $errorInfo[2]));
            }
        }
        
        return true;
    }

    /**
      * @brief Drop Database
      * @param array $dbInfo
      * @return boolean
      */
    private function _dropDatabase($dbInfo)
    {
        $connection = $this->_connectDatabse($dbInfo);
        if (!$connection) {
            return false;
        }

        $query = sprintf("DROP DATABASE %s", $dbInfo['database']);
        $stmt = $connection->prepare($query);
        $stmt->execute();
        $errorInfo = $stmt->errorInfo();
        if ($errorInfo[0] != "00000") {
            throw new \Exception(sprintf("Drop Database Error : %s", $errorInfo[2]));
        }
        return true;
    }

    /**
      * @brief 언어 선택
      * @return void
      */
    public function setLanguage()
    {
        // 한국어 선택
        $this->oSelenium->element('link text', '한국어')->click();
        $this->oSelenium->element('id', 'task-choose-language')->click();
    }

    /**
      * @brief 체크 리스트 확인
      * @return void
      */
    public function confirmCheckList()
    {
        $this->oSelenium->element('id', 'task-checklist-confirm')->click();
    }

    /**
      * @brief Database 타입 설정
      * @return void
      */
    public function setDatabaseType()
    {
        $config = $this->oConfig->getConfig();
        // mysqli_innodb 선택
        $this->oSelenium->element('id', $config['DATABASE']['database_type'])->click();
        $this->oSelenium->element('id', 'task-db-select')->click();
    }

    /**
      * @brief Database 정보 입력
      * @return void
      */
    public function setDatabaseInfo()
    {
        $config = $this->oConfig->getConfig();
        $dbInfo = $config['DATABASE'];

        $this->oSelenium->setValue('name', 'db_userid', $dbInfo['userid']);
        $this->oSelenium->setValue('name', 'db_password', $dbInfo['password']);
        $this->oSelenium->setValue('name', 'db_database', $dbInfo['database']);
        $this->oSelenium->setValue('name', 'db_hostname', $dbInfo['host']);
        $this->oSelenium->setValue('name', 'db_port', $dbInfo['port']);
        $this->oSelenium->setValue('name', 'db_table_prefix', $dbInfo['table_prefix']);
        $this->oSelenium->element('id', 'task-db-info')->click();
    }

    /**
      * @brief 시간대 설정
      * @return void
     */
    public function confirmTimezone()
    {
        $this->oSelenium->element('id', 'task-settings')->click();
    }

    /**
      * @brief 관리자 정보 등록
      * @return void
      */
    public function setAdminInfo()
    {
        $config = $this->oConfig->getConfig();
        $adminInfo = $config['XE_ADMIN'];
        $this->oSelenium->setValue('name', 'email_address', $adminInfo['email']);
        $this->oSelenium->setValue('name', 'password', $adminInfo['password']);
        $this->oSelenium->setValue('name', 'password2', $adminInfo['password']);
        $this->oSelenium->setValue('name', 'nick_name', $adminInfo['nickname']);
        $this->oSelenium->setValue('name', 'user_id', $adminInfo['id']);
        $this->oSelenium->element('id', 'task-done')->click();
    }
}
