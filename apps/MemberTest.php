<?php
namespace XEUnitTest;

use XE\UITest\Selenium\Manager\User\UserLoader;

require_once __DIR__ . '/../vendor/autoload.php';

/**
  * @file MemberTest.php
  * @brief 회원가입 로그인 테스트
  * @author NAVER (developers@xpressengine.com)
  */
class MemberTest extends \PHPUnit_Framework_TestCase
{
    /**
      * @brief 테스트 종료시 호출됨
      * @return void
      */
    public static function tearDownAfterClass()
    {
        $oUser = UserLoader::getInstance();
        $oUser->oSelenium->close();
    }


    /**
      * @brief 회원가입
      * @return void 
      */
    public function testMemberInsert()
    {
        try {
            $oUser = UserLoader::getInstance();
            $oUser->signupAll();
            // 다음 이동페이지 url 확인
        } catch (\Exception $e) {
            $oUser->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 로그인
      * @return void 
      * @depends testMemberInsert
      */
    public function testLogin()
    {
        try {
            $oUser = UserLoader::getInstance();
            $oUser->login();
            // 다음 이동페이지 url 확인
        } catch (\Exception $e) {
            $oUser->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 로그아웃
      * @return void 
      * @depends testLogin
      */
    public function testLogout()
    {
        try {
            $oUser = UserLoader::getInstance();
            $oUser->logout();
            // 다음 이동페이지 url 확인
        } catch (\Exception $e) {
            $oUser->setScreenshot();
            throw $e;
        }
    }
}
