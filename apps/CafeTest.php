<?php 
namespace XEUnitTest;

use \XE\UITest\Selenium\Manager\Admin\AdminLoader;
use \XE\UITest\Selenium\Manager\Autoinstall\AutoinstallLoader;
use \XE\UITest\Selenium\Manager\Cafe\CafeLoader;
use \XE\UITest\Selenium\Manager\Board\BoardLoader;
use \XE\UITest\Selenium\Manager\User\UserLoader;

require_once __DIR__ . '/../vendor/autoload.php';

/**
  * @file CafeTest.php
  * @brief 관리자 페이지 테스트
  * @author NAVER (developers@xpressengine.com)
  */
class CafeTest extends \PHPUnit_Framework_TestCase
{
    /**
      * @brief 테스트 종료시 호출됨
      * @return void
      */
    public static function tearDownAfterClass()
    {
        $oAdmin = AdminLoader::getInstance();
        //$oAdmin->oSelenium->close();
    }

    /**
      * @brief 관리자 로그인
      * @return void
      */
    public function testAdminLogin()
    {
        try {
            $oCafe = CafeLoader::getInstance();

            $oAdmin = AdminLoader::getInstance();
            $oAdmin->login();

            $this->assertTrue($oAdmin->checkMessageAlert());
        } catch (\Exception $e) {
            $oAdmin->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 카페 설치
      * @return void
      * @depends testAdminLogin
      */
    public function testInstallCafe()
    {
        try {
            $oCafe = CafeLoader::getInstance();

            $oAutoinstall = AutoinstallLoader::getInstance();
            $result = $oAutoinstall->install();

            $this->assertTrue($result);
        } catch (\Exception $e) {
            $oAutoinstall->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 카페 생성
      * @return void
      * @depends testInstallCafe
      */
    public function testMakeCafe()
    {
        try {
            $oCafe = CafeLoader::getInstance();
            $site_srl = $oCafe->makeCafe();

            $this->assertGreaterThan(0, $site_srl);
        } catch (\Exception $e) {
            $oCafe->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 관리자 카페 가입 
      * @return void
      * @depends testMakeCafe
      */
    public function testAdminSignupCafe()
    {
        try {
            $oCafe = CafeLoader::getInstance();

            $oCafe->signup();

            $this->assertTrue($oCafe->checkSignup());
        } catch (\Exception $e) {
            $oCafe->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 카페 신규 가입 테스트
      * @return void
      * @depends testAdminSignupCafe
      */
    public function testSignup()
    {
        try {
            $oCafe = CafeLoader::getInstance();
            $oCafe->logout();

            $info = $oCafe->oConfig->getCafe();

            $oUser = UserLoader::getInstance();
            $cafeUrl = sprintf('/index.php?mid=home&vid=%s&act=dispMemberSignUpForm', $info['vid']);

            $oUser->oSelenium->pageMove($cafeUrl);
            $oUser->signup($info['user']);

            $this->assertTrue($oCafe->checkSignin());
        } catch (\Exception $e) {
            $oUser->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 카페 메뉴 관리
      * @return void
      * @depends testSignup
      */
    public function testMakeMenu()
    {
        try {
            $oCafe = CafeLoader::getInstance();
            $oCafe->logout();

            $oAdmin = AdminLoader::getInstance();
            $oAdmin->login();

            $oCafe->makeMenu();

            $this->assertTrue($oCafe->checkMakeManu());
        } catch (\Exception $e) {
            $oCafe->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 가입한 사용자 로그인 테스트
      * @return void
      * @depends testMakeMenu
      */
    public function testLogin()
    {
        try {
            $oCafe = CafeLoader::getInstance();
            $info = $oCafe->oConfig->getCafe();
            $oCafe->logout();

            $oCafe->login($info['user']);

            $this->assertTrue($oCafe->checkSignin());
        } catch (\Exception $e) {
            $oCafe->setScreenshot();
            throw $e;
        }

    }

    /**
      * @brief  생성한 게시판에 글 작성
      * @return int
      * @depends testLogin
      */
    public function testDocuemntWrite()
    {
        try {
            $oBoard = BoardLoader::getInstance();
            $info = $oBoard->oConfig->getCafe();

            $oCafe = CafeLoader::getInstance();

            $url = sprintf('/%s/%s/', $info['vid'], $oCafe->board_mid);
            $oBoard->oSelenium->pageMove($url);

            $document_srl = $oBoard->documentWrite();

            $this->assertGreaterThan(0, $document_srl);
            return array('document_srl'=>$document_srl);
        } catch (\Exception $e) {
            $oBoard->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 댓글 작성
      * @return array|void
      * @depends testDocuemntWrite
      */
    public function testCommentWrite()
    {
        try {
            $oBoard = BoardLoader::getInstance();
            $info = $oBoard->oConfig->getCafe();

            $oCafe = CafeLoader::getInstance();

            $url = sprintf('/%s/%s/', $info['vid'], $oCafe->board_mid);
            $oBoard->oSelenium->pageMove($url);

            $document_srl = $oBoard->documentWrite();
            $comment_srl = $oBoard->commentWrite();

            $this->assertGreaterThan(0, $comment_srl);

            return array('comment_srl'=>$comment_srl);
        } catch (\Exception $e) {
            $oBoard->setScreenshot();
            throw $e;
        }
    }


    /**
      * @brief 메뉴 접근 권한 설정에 따른 테스트
      * @return void
      * @depends testCommentWrite 
      */
    public function testBoardAccess()
    {
        try {
            $oCafe = CafeLoader::getInstance();
            $info = $oCafe->oConfig->getCafe();
            $config = $oCafe->oConfig->getConfig();
            $oCafe->logout();

            // 대기회원 설정 시 접그 체크
            $oAdmin = AdminLoader::getInstance();
            $oAdmin->login();
            $oCafe->boardGrantUpdate('access_default', '로그인 사용자');
            $oCafe->logout();

            $this->assertFalse($oCafe->checkBoardAccessGroup());
            
            // 카페 사용자
            $oCafe->login($info['user']);
            $this->assertTrue($oCafe->checkBoardAccessGroup());
            $oCafe->logout();

            $oAdmin->login();
            $oCafe->boardGrantUpdate('access_default', '가입한 사용자');
            $oCafe->logout();

            $this->assertFalse($oCafe->checkBoardAccessGroup());

            $oCafe->login($info['user']);
            $this->assertTrue($oCafe->checkBoardAccessGroup());
            $oCafe->logout();

            // 카페에 가입하지 않는 사용자
            $oCafe->login($config['XE_MEMBER'][4]);
            $this->assertFalse($oCafe->checkBoardAccessGroup());

            $oCafe->logout();

            $oAdmin->login();
            $oCafe->boardGrantUpdate('access_default', '모든 사용자');
            $oCafe->logout();
        } catch (\Exception $e) {
            $oCafe->setScreenshot();
            throw $e;
        }
    }
}
