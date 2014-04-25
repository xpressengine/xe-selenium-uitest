<?php
namespace XE\UITest\Selenium\Manager;

use \XE\UITest\Selenium\Util\ConfigLoader;
use \XE\UITest\Selenium\Util\SeleniumHandler;

/**
  * @file ManagerAbstract.php
  * @brief Selenium 테스트를 수행하는 class를 위한 추상 class 
  * @author NAVER (developers@xpressengine.com)
  */
abstract class ManagerAbstract
{
    /**
      * @see ConfigLoader
      */
    public $oConfig;
    /**
      * @see SeleniumHandler
      */
    public $oSelenium;

    private $storagePath = '/../files';
    private $storageScreenshot = '/screenshots';
    private $currentDate = '';

    /**
      * @brief Selenium 테스트를 위한 설정 및 Selenium session 설정
      * @return void
      */
    final public function __construct()
    {
        $this->oConfig = ConfigLoader::getInstance();
        $config = $this->oConfig->getConfig();

        $wd_host = sprintf("http://%s:%s%s", $config['SELENIUM']['host'], $config['SELENIUM']['port'], $config['SELENIUM']['path']);
        $this->oSelenium = SeleniumHandler::getInstance($wd_host, $config['SELENIUM']['browser'], $config['TARGET_SERVER']);
    }

    /**
      * @brief Selenium 에서 screen shot 을 가져와 저장 할 때 경로
      * @return string
      */
    public function getScreenshotPath()
    {
        $path = __DIR__ . $this->storagePath . $this->storageScreenshot;
        if (!is_dir($path)) {
            throw new \Exception("Not exists Screenshot directory to ($path)");
        }

        $currentDate = $this->getCurrentDate();
        if (!$currentDate) {
            throw new \Exception("Current Date Error");
        }

        $year = substr($currentDate, 0, 4);
        $month = substr($currentDate, 4, 2);

        $path = $path . '/' . $year;
        if (!file_exists($path)) {
            mkdir($path, 0707);
        }
        if (!is_dir($path)) {
            throw new \Exception("Not exists Screenshot directory to ($path)");
        }

        $path = $path . '/' . $month;
        if (!file_exists($path)) {
            mkdir($path, 0707);
        }
        if (!is_dir($path)) {
            throw new \Exception("Not exists Screenshot directory to ($path)");
        }

        return $path;
    }

    /**
      * @brief selenium 에서 screen shot 을 가져와 저장
      * @return int
      */
    public function setScreenshot()
    {
        $img = $this->oSelenium->getScreenshot();
        $path = $this->getScreenshotPath();
        $attachFileName = urlencode($this->oSelenium->getCurrentPath());
        $fileName = $this->getScreenshotFilename($attachFileName);

        $file = sprintf("%s/%s", $path, $fileName);
        $fileSize = file_put_contents($file, $img);
        return $fileSize;
    }

    /**
      * @brief 저장 할 screen shot 파일 명
      * @return string
      */
    public static function getScreenshotFilename($attachFileName)
    {
        $filename = date("dHis") . '-' .$attachFileName . '.png';
        return $filename;
    }

    /**
      * @brief 현재 날짜
      * @return string
      */
    public function getCurrentDate()
    {
        if ($this->currentDate == "") {
            $this->currentDate = date("Ymd");
        }
        return $this->currentDate;
    }
}
