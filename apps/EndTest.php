<?php
namespace XEUnitTest;

use XE\UITest\Selenium\Manager\Install\InstallLoader;

require_once __DIR__ . '/../vendor/autoload.php';

/**
  * @file EndTest.php
  * @brief 테스트 종료 후 설정에 따른 처리
  * @author NAVER (developers@xpressengine.com)
  */
class EndTest extends \PHPUnit_Framework_TestCase
{
    /**
      * @brief 테스트 종료 후 호출 되며 config 에서 설정된 내용에 따른 처리
      * @return void
      */
    public function testEnd()
    {
        $oInstall = InstallLoader::getInstance();
        $oInstall->oSelenium->close();
        $oInstall->endTest();

        $this->assertTrue(true);
    }
}
