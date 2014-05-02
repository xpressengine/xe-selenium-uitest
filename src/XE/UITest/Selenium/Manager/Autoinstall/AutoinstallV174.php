<?php
namespace XE\UITest\Selenium\Manager\Autoinstall;

use XE\UITest\Selenium\Manager\ManagerAbstract;

/**
  * @file AutoinstallV174.php
  * @brief 쉬운설치 처리, version for 1.7.4
  * @author NAVER (developers@xpressengine.com)
  */
class AutoinstallV174 extends ManagerAbstract implements AutoinstallInterface
{
    /**
      * @biref Cafe module 다운로드 및 설치 
      * @return boolean 
      */
    public function install()
    {
        $this->regFTPInfo();
        $this->oSelenium->pageMove('/index.php?act=dispAutoinstallAdminInstall&module=admin&search_keyword=cafe&package_srl=18324168');
        // FTP 비밀번호를 입력해야 하는지 체크
        $arrElements = $this->oSelenium->elements('name', 'ftp_password');
        if (count($arrElements)>0) {
            $info =  $this->oConfig->getTargetServer();
            $this->oSelenium->setValue('name', 'ftp_password', $info['ssh_password']);
            $this->oSelenium->element('css selector', 'input[type="submit"]')->click();
        } else {
            $arrElements = $this->oSelenium->elements('css selector', '.x_clearfix a.x_btn-primary');
            foreach ($arrElements as $e) {
                if ($e->text() == '다운로드') {
                    $e->click();
                }
            }
        }

        // 설치되었는지 확인, 카페 관리 페이지로 접근해서 경고창이 나오는지 확인
        $this->oSelenium->pageMove('/index.php?module=admin&act=dispHomepageAdminContent');
        $arrElements = $this->oSelenium->elements('id', 'access');
        if (count($arrElements)>0) {
            return false;
        } else {
            return true;
        }
    }

    /**
      * @brief 쉬운 설치를 이용하기 위해 ftp 설정 등록 처리
      * @return void
      */
    public function regFTPInfo()
    {
        $info =  $this->oConfig->getTargetServer();
        $this->oSelenium->pageMove('/index.php?module=admin&act=dispAdminConfigFtp');
        $this->oSelenium->setValue('name', 'ftp_user', $info['ssh_userid']);
        $this->oSelenium->setValue('name', 'ftp_password', $info['ssh_password']);
        $this->oSelenium->element('css selector', 'input[type="submit"]')->click();

        sleep(2);
    }
}
