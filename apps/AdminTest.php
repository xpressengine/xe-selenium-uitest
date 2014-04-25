<?php 
namespace XEUnitTest;

use \XE\UITest\Selenium\Manager\Admin\AdminLoader;

require_once __DIR__ . '/../vendor/autoload.php';

/**
  * @file AdminTest.php
  * @brief 관리자 페이지 테스트
  * @author NAVER (developers@xpressengine.com)
  */
class AdminTest extends \PHPUnit_Framework_TestCase
{
    /**
      * @brief 테스트 종료시 호출됨
      * @return void
      */
    public static function tearDownAfterClass()
    {
        $oAdmin = AdminLoader::getInstance();
        $oAdmin->oSelenium->close();
    }

    /**
      * @ brief 관리자 로그인
      * @return void
      */
    public function testLogin()
    {
        try {
            $oAdmin = AdminLoader::getInstance();
            $oAdmin->login();

            // 로그인 완료 페이지에서 관리자로 다시 이동
            $oAdmin->oSelenium->pageMove('/index.php?module=admin');

            // 로그인이 실패 했으면 다른 url에 페이지가 있음
            $checkUrl = '/index.php?module=admin';
            $this->assertEquals($oAdmin->oSelenium->getCurrentPath(), $checkUrl);
        } catch (\Exception $e) {
            $oAdmin->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 게시판 메뉴 생성
      * @return void
      * @depends testLogin
      */
    public function testMakeBoardMenu()
    {
        try {
            $oAdmin = AdminLoader::getInstance();
            $oAdmin->makeBoardMenu();

            $this->assertTrue($oAdmin->checkBoardMenuAdd());
        } catch (\Exception $e) {
            $oAdmin->setScreenshot();
            throw $e;
        }
    }
}
