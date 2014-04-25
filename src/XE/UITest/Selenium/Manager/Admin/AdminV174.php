<?php
namespace XE\UITest\Selenium\Manager\Admin;

use XE\UITest\Selenium\Manager\ManagerAbstract;

/**
  * @file AdminV174.php
  * @brief 관리자 테스트, version for 1.7.4
  * @author NAVER (developers@xpressengine.com)
  */
class AdminV174 extends ManagerAbstract implements AdminInterface
{
    /**
      * @biref 관리자 로그인 처리
      * @return void
      */
    public function login()
    {
        $config =  $this->oConfig->getConfig();
        $memberInfo = $config['XE_ADMIN'];

        $this->oSelenium->pageMove('/index.php?module=admin');
        $this->oSelenium->setValue('name', 'user_id', $memberInfo['email']);
        $this->oSelenium->setValue('name', 'password', $memberInfo['password']);
        $this->oSelenium->element('css selector', 'input[type="submit"]')->click();
    }

    /**
      * @brief 게시판 메뉴 생성
      * @return void
      */
    public function makeBoardMenu()
    {
        $config =  $this->oConfig->getConfig();
        $menuInfo = $config['XE_BOARD'];

        $this->oSelenium->element('css selector', 'a[title="사이트 제작/편집"]')->click();
        $this->oSelenium->element('css selector', 'a[title="사이트 메뉴 편집"]')->click();
        $this->oSelenium->waitElement('css selector', '#siteMapTree ul');

        // 사이트 맵 클릭
        $this->oSelenium->element('css selector', '#siteMapTree a:nth-of-type(1)')->click();
        $this->oSelenium->waitElement('css selector', '#propertiesRoot ul');

        // 메뉴 추가 클릭
        $this->oSelenium->element('css selector', 'a[data-admin-show="#add"]')->click();
        $this->oSelenium->waitElement('css selector', '#add ul li');
        
        // 메뉴 추가 게시판 클릭
        $this->oSelenium->element('css selector', '#add ul li a[data-param*="\"board\""]')->click();
        $this->oSelenium->waitElement('css selector', '#add_menu ul li');
    
        // 메뉴 추가
        $this->oSelenium->setValue('css selector', '#add_menu #lang_menuName2', $menuInfo['menu_name']);
        $this->oSelenium->setValue('css selector', '#add_menu #mid1', $menuInfo['mid']);
        $this->oSelenium->element('css selector', '#add_menu fieldset button._save')->click();
    }

    /**
      * @brief 생성된 게시판 메뉴 확인
      * @return void
      */
    public function checkBoardMenuAdd()
    {
        // 메뉴 추가 게시판 등록 후 alert 레이어가 나타나면 false
        $count = $this->oSelenium->waitElement('css selector', 'section._type_alert', 2);
        if ($count) {
            return false;
        }
        return true;
    }
}
