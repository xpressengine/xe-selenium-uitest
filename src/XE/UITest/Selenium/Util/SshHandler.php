<?php
namespace XE\UITest\Selenium\Util;

/**
  * @file ManagerAbstract.php
  * @brief \Net_SSH2 class를 이용해서 SSH로 서버에 접속해서 command 를 실행할 수 있도록함
  * @author NAVER (developers@xpressengine.com)
  * @seen \Net_SSH2
  */
class SshHandler
{
    /**
      * @brief ssh instance
      */
    public $ssh;

    private $host;
    private $username;
    private $password;
    private $port;

    /**
      * @brief SSH 연결
      * @param string $host
      * @param string $port
      * @return void
      */
    public function __construct($host, $port = '22')
    {
        $this->host = $host;
        $this->port = $port;
        $this->ssh = new \Net_SSH2($this->host, $this->port);
    }
    
    /**
      * @brief SSH 로그인
      * @param string $username
      * @param string $password
      * @return void
      */
    public function auth($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        if (!$this->ssh->login($username, $password)) {
            throw new \Exception('SSH Login Failed');
        }
    }

    /**
      * @brief 명령어 실행
      * @param string $cmd 명령어
      * @return string 
      */
    public function exec($cmd)
    {
        $result = $this->ssh->exec($cmd);
        return $result;
    }

    /**
      * @brief 명령어 실행, 리턴을 받아오지 않음
      * @param string $cmd
      * @return string
      */
    public function write($cmd)
    {
        $this->ssh->write($cmd);
    }

    /**
      * @brief write 로 실행한 결과 가져오기
      * @param string $expact 
      * @return string
      */
    public function read($expact = '')
    {
        return $this->ssh->read($expact);
    }

    /**
      * @brief disconnect
      * @return void
      */
    public function disconnect()
    {
        $this->ssh->disconnect();
    }
}
