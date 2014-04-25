<?php
namespace XE\UITest\Selenium\Manager\Admin;

use \XE\UITest\Selenium\Util\ConfigLoader;

/**
  * @file AdminLoader.php
  * @brief XE 버전에 따라 test 수행하는 class 를 변경
  * @author NAVER (developers@xpressengine.com)
  */
class AdminLoader
{
    public static $_instance;

    /**
      * @brief get instance
      * return object
      */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            $oConfig = ConfigLoader::getInstance();
            $XEInfo = $oConfig->getXEInfo();

            switch ($XEInfo) {
                default:
                    self::$_instance = new AdminV174();
                    break;
            }
        }
        return self::$_instance;
    }
}
