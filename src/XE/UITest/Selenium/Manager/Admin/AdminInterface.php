<?php
namespace XE\UITest\Selenium\Manager\Admin;

/**
  * @file AdminInterface.php
  * @brief interface
  * @author NAVER (developers@xpressengine.com)
  */
interface AdminInterface
{
    /**
      * @brief 관리자 로그인 처리
      */
    public function login();

    /**
      * @brief 게시판 메뉴 생성
      */
    public function makeBoardMenu();

    /**
      * @brief 생성된 게시판 메뉴 확인
      */
    public function checkBoardMenuAdd();
}
