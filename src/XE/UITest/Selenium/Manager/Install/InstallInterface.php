<?php
namespace XE\UITest\Selenium\Manager\Install;

/**
  * @file InstallInterface.php
  * @brief interface
  * @author NAVER (developers@xpressengine.com)
  */
interface InstallInterface
{
    /**
      * @brief XE 다운로드 및 DB 생성
      */
    public function preset();

    /**
      * @brief 테스트 종료 후 처리
      */
    public function endTest();

    /**
      * @brief install 시 언어 선택
      */
    public function setLanguage();

    /**
      * @brief 체크리스트 페이지 처리
      */
    public function confirmCheckList();

    /**
      * @brief Database 타입 선택
      */
    public function setDatabaseType();

    /**
      * @brief Database 정보 입력
      */
    public function setDatabaseInfo();

    /**
      * @brief 시간대 선택
      */
    public function confirmTimezone();

    /**
      * @brief 관리자 정보 입력
      */
    public function setAdminInfo();
}
