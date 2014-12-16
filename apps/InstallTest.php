<?php
namespace XEUnitTest;

use XE\UITest\Selenium\Manager\Install\InstallLoader;

require_once __DIR__ . '/../vendor/autoload.php';

/**
  * @file InstallTest.php
  * @brief XE 설치 테스트
  * @author NAVER (developers@xpressengine.com)
  */
class InstallTest extends \PHPUnit_Framework_TestCase
{
    /**
      * @brief 테스트 종료시 호출됨
      * @return void
      */
    public static function setUpBeforeClass()
    {
        $oInstall = InstallLoader::getInstance();
        $oInstall->preset();
    }

    public static function tearDownAfterClass()
    {
        $oInstall = InstallLoader::getInstance();
        $oInstall->oSelenium->close();
    }

    /**
      * @ brief 설치 언어 선택
      * @return void
      */
    public function testInstallStep1()
    {
        try {
            $oInstall = InstallLoader::getInstance();

            $oInstall->oSelenium->pageMove('/');
            $oInstall->setLanguage();

            // 다음 페이지 url 체크
            $checkUrl = '/index.php?act=dispInstallLicenseAgreement';
            $this->assertEquals($oInstall->oSelenium->getCurrentPath(), $checkUrl);
        } catch (\Exception $e) {
            $oInstall->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 라이선스 동의
      * @return void
      * @depends testInstallStep1
      */
    public function testInstallStep2()
    {
        try {
            $oInstall = InstallLoader::getInstance();
            $oInstall->licenseAgreement();

            // 다음 페이지 url 체크
            $checkUrl = '/index.php?module=admin&act=dispInstallCheckEnv';
            $this->assertEquals($oInstall->oSelenium->getCurrentPath(), $checkUrl);
        } catch (\Exception $e) {
            $oInstall->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 설치 조건 확인
      * @return void
      * @depends testInstallStep2
      */
    public function testInstallStep3()
    {
        try {
            $oInstall = InstallLoader::getInstance();
            $oInstall->confirmCheckList();

            // 다음 페이지 url 체크
            $checkUrl = '/index.php?act=dispInstallSelectDB';
            $this->assertEquals($oInstall->oSelenium->getCurrentPath(), $checkUrl);
        } catch (\Exception $e) {
            $oInstall->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief DB 선택
      * @return void
      * @depends testInstallStep3
      */
    public function testInstallStep4()
    {
        try {
            $oInstall = InstallLoader::getInstance();
            $oInstall->setDatabaseType();

            // 다음 페이지 url 체크
            $checkUrl = '/';
            $this->assertEquals($oInstall->oSelenium->getCurrentPath(), $checkUrl);
        } catch (\Exception $e) {
            $oInstall->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief DB 정보 입력
      * @return void
      * @depends testInstallStep4
      */
    public function testInstallStep5()
    {
        try {
            $oInstall = InstallLoader::getInstance();
            $oInstall->setDatabaseInfo();
            
            // 다음 페이지 url 체크
            $checkUrl = '/index.php?act=dispInstallConfigForm';
            $this->assertEquals($oInstall->oSelenium->getCurrentPath(), $checkUrl);
        } catch (\Exception $e) {
            $oInstall->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 환경 설정
      * @return void
      * @depends testInstallStep5
      */
    public function testInstallStep6()
    {
        try {
            $oInstall = InstallLoader::getInstance();
            $oInstall->confirmTimezone();

            // 다음 페이지 url 체크
            $checkUrl = '/index.php?act=dispInstallManagerForm';
            $this->assertEquals($oInstall->oSelenium->getCurrentPath(), $checkUrl);
        } catch (\Exception $e) {
            $oInstall->setScreenshot();
            throw $e;
        }
    }

    /**
      * @biref 관리자 정보 입력
      * @depends testInstallStep6
      */
    public function testInstallStep7()
    {
        try {
            $oInstall = InstallLoader::getInstance();
            $oInstall->setAdminInfo();

            // 다음 페이지 url 체크
            $checkUrl = '/';
            $this->assertEquals($oInstall->oSelenium->getCurrentPath(), $checkUrl);
        } catch (\Exception $e) {
            $oInstall->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 설치 완료, 로그인 된 사이트 메인 페이지
      * @return void
      * @depends testInstallStep7
      */
    public function testInstallStep8()
    {
        try {
            $oInstall = InstallLoader::getInstance();

            // 관리자 접근
            $oInstall->oSelenium->pageMove('/index.php?module=admin');

            // 다음 페이지 url 체크
            $checkUrl = '/index.php?module=admin';
            $this->assertEquals($oInstall->oSelenium->getCurrentPath(), $checkUrl);
        } catch (\Exception $e) {
            $oInstall->setScreenshot();
            throw $e;
        }
    }
}
