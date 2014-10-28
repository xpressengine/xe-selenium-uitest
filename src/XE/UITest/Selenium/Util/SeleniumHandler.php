<?php
namespace XE\UITest\Selenium\Util;

/**
  * @file SeleniumHandler.php
  * @brief SeleniumHandler 
  * @author NAVER (developers@xpressengine.com)
  * @see \PHPWebDriver_WebDriver \n
  *        https://github.com/Element-34/php-webdriver
  */
class SeleniumHandler
{
    /**
      * @brief base url
      */
    private $_url;

    /**
      * @brief Selenium에서 사용할 브라우져
      */
    private $browser;
    /**
      * @brief 설정
      */
    private $args;
    /**
      * @brief webdriver
      */
    private $_driver;
    /**
      * @brief webdriver의 session(윈도우)
      */
    public static $_session;
    /**
      * @brief webdriver에서 session(윈도우)가 활성화 되어 있는지 확인
      */
    public static $_isActivate = false;

    /**
      * @brief singleton
      */
    private static $_instance;
    
    /**
      * @brief get instance
      * @param string $wd_host Selenium 서버 host
      * @param string $browser
      * @param string $args
      * @return object
      */
    public static function getInstance($wd_host, $browser, $args)
    {
        if (self::$_instance === null) {
            self::$_instance = new SeleniumHandler($wd_host, $browser, $args);
        }

        if (self::$_isActivate === false) {
            self::$_instance = new SeleniumHandler($wd_host, $browser, $args);
        }

        return self::$_instance;
    }

    /**
      * @brief construct
      * @param string $wd_host Selenium 서버 host
      * @param string $browser
      * @param string $args
      * return void
      */
    public function __construct($wd_host, $browser, $args)
    {
        $this->_driver = $this->getDriver($wd_host);
        $this->browser = $browser;
        $this->args = $args;

        $this->setSession($this->browser, $this->args);

        self::$_isActivate = true;
    }

    /**
      * @brief Selenium 서버에 연결 하여 webdriver instance 생성
      * @param string $wd_host Selenium 서버 host
      * @return object
      */
    public function getDriver($wd_host)
    {
        return new \PHPWebDriver_WebDriver($wd_host);
    }

    /**
      * @brief webdriver 에 session 생성
      * @param string $browser
      * @param string $args
      * @return void
      */
    public function setSession($browser, $args)
    {
        if (!isset($args['url'])) {
            throw new \Exception('check url');
        }

        self::$_session = $this->_driver->session($browser, $args);
        self::$_session->window()->maximize();
        $this->setUrl($args['url']);
    }

    /**
      * @brief 매칭되는 하나의 element를 가져옴
      * @param string $selector
      * @param string $strSelector
      * @return object
      */
    public function element($selector, $strSelector)
    {
		$this->waitElement($selector, $strSelector);
        return self::$_session->element($selector, $strSelector);
    }

    /**
      * @brief 매칭되는 모든 element를 가져옴
      * @param string $selector
      * @param string $strSelector
      * @return array
      */
    public function elements($selector, $strSelector)
	{
		$this->waitElement($selector, $strSelector);
        return self::$_session->elements($selector, $strSelector);
    }

    /**
      * @brief value 요소를 갖는 element에 $value 작성
      * @param string $selector
      * @param string $strSelector
      * @param string $value
      * @return void
      */
    public function setValue($selector, $strSelector, $value)
    {
		$e = $this->element($selector, $strSelector);
        $e->clear();
        $e->value(array('value'=>array($value)));
    }

    /**
      * @brief select element에 option 선택
      * @param string $selector
      * @param string $strSelector
      * @param string $match want select infomation
      * @param string $match_type 
      * @return void
      */
    public function setSelect($selector, $strSelector, $match, $match_type = 'value')
    {
		$e = $this->element($selector, $strSelector);
        if ($match_type == 'value') {
            $opts = $e->elements('css selector', 'option[value="'.$match.'"]');
            if (count($opts)>0) {
                $opts[0]->click();
            }
        } elseif ($match_type == 'text') {
            $opts = $e->elements('css selector', 'option');
            foreach ($opts as $opt) {
                if ($opt->text() == $match) {
                    $opt->click();
                }
            }
        }
    }

    /**
      * @brief 페이지 이동
      * @param string @path 이동 경로
      * @return void
      */
    public function pageMove($path)
    {
        if (self::$_isActivate === false) {
            $this->setSession($this->browser, $this->args);
        }

        $url = sprintf("%s%s", $this->getUrl(), $path);
        self::$_session->open($url);

        $this->_pageMoveCompleteWait();
    }

    /**
      * @brief 이동 후 페이지 load 가 완료되기를 기다림. 사용할 수 있는 이벤트가 없기 때문에 sleep 으로 처리함
      * @return void
	  *	@deprecated page load 후 기다리지 않아도 오류발생하지 않도록 수정됨
      */
    private function _pageMoveCompleteWait()
    {
        $sleepTime = 2;
        if (isset($this->args['pageMoveSleepTime'])) {
            $sleepTime = $this->args['pageMoveSleepTime'];
        }
        //sleep($sleepTime);
    }

    /**
      * @brief url 설정
      * @param string $url
      * @return void
      */
    public function setUrl($url)
    {
        $arrUrl = parse_url($url);
        $this->_url = sprintf("%s://%s", $arrUrl['scheme'], $arrUrl['host']);
        if (isset($arrUrl['port'])&&$arrUrl['port']!="80") {
            $this->_url = sprintf("%s://%s:%s", $arrUrl['scheme'], $arrUrl['host'], $arrUrl['port']);
        }

        if (isset($arrUrl['path'])) {
            if (substr($arrUrl['path'], -1) == "/") {
                $arrUrl['path'] = substr($arrUrl['path'], 0, -1);
            }
            $this->_url .= $arrUrl['path'];
        }
    }

    /**
      * @brief get url
      * @return string
      */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
      * @brief 현재 session 의 url return
      * @return string
      */
    public function getCurrentPath()
    {
        $currentUrl = self::$_session->url();
        $arrUrl = parse_url($currentUrl);

        $currentPath = $arrUrl['path'];
        if ($arrUrl['query']) {
            $currentPath = sprintf("%s?%s", $arrUrl['path'], $arrUrl['query']);
        }

        $arrUrl = parse_url($this->_url);
        if ($arrUrl['path']) {
            $pos = strpos($currentPath, $arrUrl['path']);
            if ($pos!==false) {
                $currentPath = substr($currentPath, $pos+strlen($arrUrl['path']));
            }
        }

        return $currentPath;
    }

    /**
      * @brief 현재 session 의 path와 $checkPath 비교
      * @param string $checkPath
      * @return boolean
      */
    public function checkCurrentPath($checkPath)
    {
        $currentPath = $this->getCurrentPath();
        if ($currentPath!=$checkPath) {
            return false;
        }
        return true;
    }

    /**
      * @brief session(윈도우)에 element 가 나올 때 까지 기다림
      * @param string $selector
      * @param string $strSelector
      * @param int $timeout 기다리는 시간(초)
      * @param float $poll_frequency 주기적으로 확인하는 시간(초)
      * @return int
      */
    public function waitElement($selector, $strSelector, $timeout = 30, $poll_frequency = 0.5)
    {
        $extra_vars = array(
            "selector" => $selector,
            "strSelector" => $strSelector,
        );
        $w = new \PHPWebDriver_WebDriverWait(self::$_session, $timeout, $poll_frequency, $extra_vars);
        try {
            return $w->until(function ($session, $extra_vars) {
                return count($session->elements($extra_vars['selector'], $extra_vars['strSelector']));
            });
        } catch (\PHPWebDriver_TimeOutWebDriverError $e) {
            return 0;
        }
    }

    /**
      * @brief session(윈도우)에 element 의 css 속성이 원하는 상태가 될때까지 기다림 
      * @param string $selector
      * @param string $strSelector
      * @param string $expect 'displayed', 'css', ...
      * @param int $timeout 기다리는 시간(초)
      * @param float $poll_frequency 주기적으로 확인하는 시간(초)
      * @return int
      */
    public function waitElementExpect($selector, $strSelector, $expect, $timeout = 30, $poll_frequency = 0.5)
    {
        $extra_vars = array(
            "selector" => $selector,
            "strSelector" => $strSelector,
            "expect" => $expect,
        );
        $w = new \PHPWebDriver_WebDriverWait(self::$_session, $timeout, $poll_frequency, $extra_vars);
        try {
            return $w->until(function ($session, $extra_vars) {
                $e = $session->elements($extra_vars['selector'], $extra_vars['strSelector']);
                if (count($e)==0) {
                    return 0;
                }
                $element = $e[0];
                switch ($extra_vars['expect']) {
                    case 'displayed':
                        if ($element->displayed()) {
                            return 1;
                        }
                        break;
                    default:
                        return 0;
                        break;
                }
                return 0;
            });
        } catch (\PHPWebDriver_TimeOutWebDriverError $e) {
            return 0;
        }
    }

    /**
      * @brief 키 입력
      * @param string @content
      * @return void
      */
    public function sendKeys($content)
    {
        $action = new \PHPWebDriver_WebDriverActionChains(self::$_session);
        $action->sendKeys($content);
        $action->perform();
    }

    /**
      * @brief session 에 있는 frame으로 focus 이동
      * @param object $frameElement
      * @return void
      */
    public function switchToFrame($frameElement = null)
    {
        self::$_session->switch_to_frame($frameElement);
    }

    /**
      * @brief session 의 alert창으로 focus 이동
      * @return void
      */
    public function switchToAlert()
    {
        return self::$_session->switch_to_alert();
    }

    /**
      * @brief session(윈도우) 닫기
      * @return void
      */
    public function close()
    {
        self::$_session->close();
        self::$_isActivate = false;
    }

    /**
      * @brief session(윈도우) 스크린 샷 (이미지)
      * @return string
      */
    public function getScreenshot()
    {
        $img = self::$_session->screenshot();
        $data = base64_decode($img);
        return $data;
    }
}
