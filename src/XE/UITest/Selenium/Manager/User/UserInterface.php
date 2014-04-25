<?php
namespace XE\UITest\Selenium\Manager\User;

/**
  * @file UserInterface.php
  * @brief interface
  * @author NAVER (developers@xpressengine.com)
  */
interface UserInterface
{
    /**
      * @brief 회원가입
      */
    public function signup($memberInfo);

    /**
      * @brief 설정파일에 있는 회원가입 처리 
      */
    public function signupAll();

    /**
      * @brief 로그아웃
      */
    public function logout();

    /**
      * @brief 로그인
      */
    public function login($memberInfo);
}
