<?php
namespace XE\UITest\Selenium\Manager\Cafe;

use XE\UITest\Selenium\Manager\ManagerAbstract;

/**
  * @file CafeV174.php
  * @brief Cafe Module 테스트, version for 1.7.4
  * @author NAVER (developers@xpressengine.com)
  */
class CafeV174 extends ManagerAbstract implements CafeInterface
{
    public $board_menu_name = 'CafeBoard';
    public $board_mid = '';
    /**
      * @biref Cafe 생성
      * @return void 
      */
    public function makeCafe()
    {
        $info = $this->oConfig->getCafe();

        // 카페 생성 정보 입력
        $this->oSelenium->pageMove('/index.php?module=admin&act=dispHomepageAdminInsert');
        $this->oSelenium->setValue('name', 'cafe_title', $info['title']);
        $this->oSelenium->setValue('name', 'cafe_vid', $info['vid']);

        $arrElement = $this->oSelenium->elements('css selector', 'button[type="submit"]');
        foreach ($arrElement as $e) {
            if ($e->text() == '등록') {
                $e->click();
                break;
            }
        }
        
        // 생성된 site_srl 확인
        $arrUrl = parse_url($this->oSelenium->getCurrentPath());

        if (!isset($arrUrl['query'])) {
            return 0;
        }

        $arrQuery = explode("&", $arrUrl['query']);

        foreach ($arrQuery as $val) {
            $arrKeyValue = explode('=', $val);
            if ($arrKeyValue[0] == 'site_srl') {
                return (int)$arrKeyValue[1];
            }
        }

        return 0;
    }

    /**
      * @brief 로그인 상태에서 Cafe 가입
      * @return void
      */
    public function signup()
    {
        $info = $this->oConfig->getCafe();
        $cafeUrl = sprintf('/%s', $info['vid']);
        $this->oSelenium->pageMove($cafeUrl);

        $this->oSelenium->element('css selector', 'input[value="가입"]')->click();
        sleep(2);
    }

    /**
      * @brief 가입 처리 후 탈퇴 버튼이 생성되는지 확인
      * @return boolean
      */
    public function checkSignup()
    {
        $arrElements = $this->oSelenium->elements('css selector', 'input[value="탈퇴"]');
        if (count($arrElements) == 0) {
            return false;
        }
        return true;
    }

    /**
      * @brief 로그인 확인, 회원 정보 페이지에 메시지 창이 있으면 로그인 실패로 확인 
      * @return boolean
      */
    public function checkSignin()
    {
        $info = $this->oConfig->getCafe();
        $cafeUrl = sprintf('/index.php?mid=home&vid=%s&act=dispMemberInfo', $info['vid']);
        $this->oSelenium->pageMove($cafeUrl);

        return $this->checkMessageAlert();
    }

    /**
      * @brief 카페 가입 정보로 로그인 처리
      * @param array $memberInfo
      * @return void
      */
    public function login($memberInfo = array())
    {
        $info = $this->oConfig->getCafe();
        $cafeUrl = sprintf('/%s', $info['vid']);
        $this->oSelenium->pageMove($cafeUrl);

        if (!$memberInfo) {
            $info = $this->oConfig->getCafe();
            $memberInfo = $info['user'];
        }

        $this->oSelenium->setValue('css selector', '#formLogin input[name="user_id"]', $memberInfo['email']);
        $this->oSelenium->setValue('css selector', '#formLogin input[name="password"]', $memberInfo['password']);
        $this->oSelenium->element('css selector', '#formLogin input[type="submit"]')->click();
    }

    /**
      * @brief 카페 로그아웃
      * @return void
      */
    public function logout()
    {
        $info = $this->oConfig->getCafe();
        $cafeUrl = sprintf('/index.php?mid=home&vid=%s&act=dispMemberLogout', $info['vid']);
        $this->oSelenium->pageMove($cafeUrl);
    }


    /**
      * @brief 메뉴 관리
      * @return void
      */
    public function makeMenu()
    {
        $info = $this->oConfig->getCafe();
        $url = sprintf('/index.php?vid=%s&act=dispHomepageAdminSiteTopMenu', $info['vid']);
        $this->oSelenium->pageMove($url);

        $e = $this->oSelenium->waitElement('css selector', 'a[href="#__menu_info"]');

        $arrElements = $this->oSelenium->elements('css selector', '.modalAnchor');
        if (!count($arrElements)) {
            throw new \Exception('추가할 수 있는 메뉴 버튼이 없습니다.');
        }

        $arrElements[0]->click();

        $e = $this->oSelenium->waitElement('css selector', '#fo_menu #lang_menu_name');
        if ($e == 0) {
            throw new \Exception('메뉴 정보 입력창을 불러오는데 실패했습니다222.');
        }


        $e = $this->oSelenium->waitElementExpect('css selector', '#__menu_info', 'displayed');
        if ($e == 0) {
            throw new \Exception('메뉴 정보 입력창을 불러오는데 실패했습니다.');
        }

        $this->oSelenium->setSelect('css selector', '#module_type', 'board');    // page, url
        $this->oSelenium->setValue('css selector', '#fo_menu #lang_menu_name', $this->board_menu_name);
        $this->oSelenium->element('css selector', '#fo_menu button[type="submit"]')->click();
    }

    /**
      * @brief makeMenu 에 의해 생성된 메뉴가 생성 되었는지 확인 
      * @ return boolean
      */
    public function checkMakeManu()
    {
        $e = $this->oSelenium->element('class name', 'widgetTree');
        $arrElements = $e->elements('tag name', 'a');
        foreach ($arrElements as $e) {
            if ($e->text() == $this->board_menu_name) {
                $e->click();
                $arrUrl = parse_url($this->oSelenium->getCurrentPath());
                $arrPath = explode('/', $arrUrl['path']);
                $this->board_mid = $arrPath[2];
                return true;
            }
        }
        return false;
    }

    /**
      * @brief 메뉴 노출 설정 
      * @param array $arr_group_name 대기회원, 준회원, 정회원 에서 선택
      * @return void
      */
    public function menuViewGroupUpdate($arr_group_name)
    {
        $info = $this->oConfig->getCafe();
        $url = sprintf('/index.php?vid=%s&act=dispHomepageAdminSiteTopMenu', $info['vid']);
        $this->oSelenium->pageMove($url);

        // 게시판 설정 수정 선택
        $e = $this->oSelenium->waitElement('css selector', 'a[href="#__menu_info"]');

        $arrElements = $this->oSelenium->elements('css selector', '.simpleTree ul li');
        foreach ($arrElements as $e) {
            if ($e->text() == $this->board_menu_name) {
                $e->element('css selector', 'a.modify')->click();
            }
        }

        $e = $this->oSelenium->waitElementExpect('css selector', '#__menu_info', 'displayed');
        if ($e == 0) {
            throw new \Exception('메뉴 정보 입력창을 불러오는데 실패했습니다.');
        }

        $this->board_mid = $this->oSelenium->element('css selector', '#fo_menu input[name="module_id"]')->attribute('value');
        $arrElements = $this->oSelenium->elements('css selector', '#fo_menu label[for^="group"]');
        foreach ($arrElements as $eLabel) {
            if (in_array($eLabel->text(), $arr_group_name)) {
                $e = $eLabel->element('css selector', 'input[type="checkbox"]');
                if (!$e->selected()) {
                    $e->click();
                }
            } else {
                $e = $eLabel->element('css selector', 'input[type="checkbox"]');
                if ($e->selected()) {
                    $e->click();
                }
            }
        }

        $this->oSelenium->element('css selector', '#fo_menu button[type="submit"]')->click();
    }

    /**
      * @brief 게시판 접근 권한 설정
      * @param array $arr_group_name 대기회원, 준회원, 정회원 에서 선택
      * @return void
      */
    public function boardGrantUpdate($grant_id, $select_option_text)
    {
        $info = $this->oConfig->getCafe();
        $url = sprintf('/index.php?vid=%s&act=dispHomepageAdminSiteTopMenu', $info['vid']);
        $this->oSelenium->pageMove($url);

        // 게시판 설정 수정 선택
        $e = $this->oSelenium->waitElement('css selector', 'a[href="#__menu_info"]');

        $arrElements = $this->oSelenium->elements('css selector', '.simpleTree ul li');
        foreach ($arrElements as $e) {
            if ($e->text() == $this->board_menu_name) {
                $e->element('css selector', 'a.modify')->click();
            }
        }

        $e = $this->oSelenium->waitElementExpect('css selector', '#__menu_info', 'displayed');
        if ($e == 0) {
            throw new \Exception('메뉴 정보 입력창을 불러오는데 실패했습니다.');
        }

        $this->board_mid = $this->oSelenium->element('css selector', '#fo_menu input[name="module_id"]')->attribute('value');

        
        $url = sprintf('/index.php?vid=%s&mid=%s&act=dispBoardAdminGrantInfo', $info['vid'], $this->board_mid);
        $this->oSelenium->pageMove($url);


        $this->oSelenium->setSelect('css selector', '#fo_obj #'.$grant_id, $select_option_text, 'text');
        $this->oSelenium->element('css selector', '#fo_obj button[type="submit"]')->click();
    }



    /**
      * @brief 접근 권한에 따른 체크
      * @retrun boolean
      */
    public function checkBoardAccessGroup()
    {
        $info = $this->oConfig->getCafe();
        $url = sprintf('/%s/%s/', $info['vid'], $this->board_mid);
        $this->oSelenium->pageMove($url);

        $arrElements = $this->oSelenium->elements('css selector', '#content #access');
        if (count($arrElements) == 0) {
            return true;
        } else {
            return false;
        }
    }
}
