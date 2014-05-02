<?php
namespace XE\UITest\Selenium\Util;

/**
  * @file ConfigLoaderAbstract.php
  * @brief 다양한 형태의 config를 동일한 형태로 읽어들이기 위한 추상 class
  * @author NAVER (developers@xpressengine.com)
  */
abstract class ConfigLoaderAbstract
{
    /**
      * @brief config
      */
    private $_config;
    /**
      * @brief database, webserver 폴더에 적용할 이름
      */
    private $_prefix = 'seleniumXE';

    /**
      * @brief singleton
      */
    private static $_instance;

    /**
      * @brief get instance
      * @param string $configPath
      * @return object
      */
    final public function __construct($configPath)
    {
        $this->_config = $this->setConfig($configPath);
        if (!$this->_config) {
            throw new ConfigException('Config empty. Config file form ' . $configPath);
        }

        $this->_setInstallPath();
    }

    /**
      * @brief $configPath의 파일을 읽어 array 로 return
      * @param string $configPath
      */
    abstract public function setConfig($configPath);

    /**
      * @brief get config
      * @return array
      */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
      * @brief get TARGET_SERVER config
      * @return array
      */
    public function getTargetServer()
    {
        return $this->_config['TARGET_SERVER'];
    }

    /**
      * @brief get DATABASE config
      * @return array
      */
    public function getDatabase()
    {
        return $this->_config['DATABASE'];
    }

    /**
      * @brief get SELENIUM config
      * @return array
      */
    public function getSelenium()
    {
        return $this->_config['SELENIUM'];
    }

    /**
      * @brief get INSTALL config
      * @return array
      */
    public function getInstall()
    {
        return $this->_config['INSTALL'];
    }

    /**
      * @brief get XE_INFO config
      * @return array
      */
    public function getXEInfo()
    {
        return $this->_config['XE_INFO'];
    }

    /**
      * @brief get XE_INFO config
      * @return array
      */
    public function getAdmin()
    {
        return $this->_config['XE_ADMIN'];
    }

    /**
      * @brief get XE_BOARD config
      * @return array
      */
    public function getBoard()
    {
        return $this->_config['XE_BOARD'];
    }

    /**
      * @brief get XE_MEMBER config
      * @return array
      */
    public function getMember()
    {
        return $this->_config['XE_MEMBER'];
    }

    /**
      * @brief get Cafe config
      * @return array
      */
    public function getCafe()
    {
        return $this->_config['XE_CAFE'];
    }

    /**
      * @brief 설치 경로 설정 
      * @return void 
      */
    private function _setInstallPath()
    {
        $prefix = $this->_getPrefix();
        $this->_config['DATABASE']['database_org'] = $this->_config['DATABASE']['database'];
        $this->_config['DATABASE']['database'] .= $this->_prefix . $prefix;
        $this->_config['TARGET_SERVER']['url'] .= $this->_prefix . $prefix;
        $this->_config['TARGET_SERVER']['document_root_org'] = $this->_config['TARGET_SERVER']['document_root'];
        $this->_config['TARGET_SERVER']['document_root'] .= $this->_prefix . $prefix;
    }

    /**
      * @brief 설정에 따른 prefix 변경 처리
      * @return string
      */
    private function _getPrefix()
    {
        $prefix = "default";

        if (!isset($this->_config['INSTALL']['prefix'])) {
            return $prefix;
        }

        $configPrefix = $this->_config['INSTALL']['prefix'];

        if (!$configPrefix) {
            return $prefix;
        }

        switch ($configPrefix) {
            case 'DATE':
                $prefix = date("YmdHi") .'_' . rand(0, 999);
                break;
            default:
                $prefix = $configPrefix;
                break;
        }
        return $prefix;
    }
}
