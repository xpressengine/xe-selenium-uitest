<?php
namespace XE\UITest\Selenium\Manager\User;

use XE\UITest\Selenium\Manager\ManagerAbstract;

/**
  * @file UserV174.php
  * @brief 회원 테스트, version for 1.7.4
  * @author NAVER (developers@xpressengine.com)
  */
class UserV174 extends ManagerAbstract implements UserInterface
{
    /**
      * @brief 회원 가입
      * @param array $memberInfo
      * @return void
      */
    public function signup($memberInfo)
    {
        $this->oSelenium->setValue('css selector', '#fo_insert_member input[name="email_address"]', $memberInfo['email']);
        $this->oSelenium->setValue('css selector', '#fo_insert_member input[name="password"]', $memberInfo['password']);
        $this->oSelenium->setValue('css selector', '#fo_insert_member input[name="password2"]', $memberInfo['password']);
        $this->oSelenium->setValue('css selector', '#fo_insert_member input[name="user_id"]', $memberInfo['id']);
        $this->oSelenium->setValue('css selector', '#fo_insert_member input[name="user_name"]', $memberInfo['name']);
        $this->oSelenium->setValue('css selector', '#fo_insert_member input[name="nick_name"]', $memberInfo['nickname']);

        $this->oSelenium->setSelect('css selector', '#fo_insert_member select[name="find_account_question"]', $memberInfo['find_question']);

        $this->oSelenium->setValue('css selector', '#fo_insert_member input[name="find_account_answer"]', $memberInfo['find_answer']);

        $this->oSelenium->element('css selector', '#fo_insert_member input[type="submit"]')->click();
    }

    /**
      * @brief 설정 파일에 있는 모든 회원저보에 대해서 회원가입
      * @return void
      */
    public function signupAll()
    {
        $config = $this->oConfig->getConfig();
        foreach ($config['XE_MEMBER'] as $memberInfo) {
            $this->oSelenium->pageMove('/index.php?act=dispMemberSignUpForm');
            $this->signup($memberInfo);
            $this->logout($session);
        }
    }

    /**
      * @brief 로그아웃
      * @return void
      */
    public function logout()
    {
        $this->oSelenium->pageMove('/index.php?act=dispMemberLogout');
    }

    /**
      * @brief 로그인
      * @param array $memberInfo
      * @return void
      */
    public function login($memberInfo = array())
    {
        if (!$memberInfo) {
            $config = $this->oConfig->getConfig();
            $memberInfo = $config['XE_MEMBER'][0];
        }

        $this->oSelenium->pageMove('/index.php?act=dispMemberLoginForm');
        $this->oSelenium->setValue('css selector', '#fo_member_login input[name="user_id"]', $memberInfo['email']);
        $this->oSelenium->setValue('css selector', '#fo_member_login input[name="password"]', $memberInfo['password']);
        $this->oSelenium->element('css selector', '#fo_member_login input[type="submit"]')->click();
    }
}
