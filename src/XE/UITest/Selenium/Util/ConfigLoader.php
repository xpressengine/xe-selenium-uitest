<?php
namespace XE\UITest\Selenium\Util;

use XE\UITest\Selenium\Util\Exception\ConfigException;

/**
  * @file ConfigLoader.php
  * @brief Config Loader 
  * @author NAVER (developers@xpressengine.com)
  */
class ConfigLoader
{
    /**
      * @brief 기본 config 파일 명 
      */
    const CONFIG_FILE = "/config.php";

    /**
      * @brief singleton
      */
    private static $_instance;

    /**
      * @brief get instance
      * @return object
      */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            if (isset($_ENV['XE_SELENIUM_CONFIG'])) {
                $pathParts = pathinfo($_ENV['XE_SELENIUM_CONFIG']);
                $configPath = realpath(__DIR__ . '/..') . $_ENV['XE_SELENIUM_CONFIG'];
                if (!file_exists($configPath)) {
                    throw new ConfigException('CONFIG_FILE not exists.');
                }
            } else {
                $pathParts = pathinfo(self::CONFIG_FILE);
                $configPath = realpath(__DIR__ . '/..') . self::CONFIG_FILE;
            }

            switch (strtolower($pathParts['extension'])) {
                case 'php':
                    self::$_instance = new ArrayConfigLoader($configPath);
                    break;
                case 'json':
                    self::$_instance = new JsonConfigLoader($configPath);
                    break;
                case 'xml':
                    self::$_instance = new XmlConfigLoader($configPath);
                    break;
                default:
                    throw new ConfigException('CONFIG_FILE not matched.');
                    break;
            }

            if (self::$_instance === null) {
                throw new ConfigException('Config Load Failed');
            }
        }

        return self::$_instance;
    }
}
