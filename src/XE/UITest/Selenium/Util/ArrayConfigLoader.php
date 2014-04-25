<?php
namespace XE\UITest\Selenium\Util;

/**
  * @file ArrayConfigLoader.php
  * @brief array 형태의 config 를 가져옴
  * @author NAVER (developers@xpressengine.com)
  * @seen ConfigLoaderAbstract
  */
class ArrayConfigLoader extends ConfigLoaderAbstract
{
    /**
      * @brief config 가져옴
      * @return array
      */
    public function setConfig($configPath)
    {
        $config = include($configPath);
        return $config;
    }
}
