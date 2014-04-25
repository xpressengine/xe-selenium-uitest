<?php
namespace XE\UITest\Selenium\Manager\Board;

use XE\UITest\Selenium\Manager\ManagerAbstract;

/**
  * @file BoardV174.php
  * @brief 게시판 테스트, version for 1.7.4
  * @author NAVER (developers@xpressengine.com)
  */
class BoardV174 extends ManagerAbstract implements BoardInterface
{
    /**
      * @brief 에디터에 내용 입력
      * @param string $content 작성 내용
      * @return void
      */
    public function writeContent($content)
    {
        $this->oSelenium->waitElement('css selector', '.xpress_xeditor_editing_area_container iframe');
        $e = $this->oSelenium->element('css selector', '.xpress_xeditor_editing_area_container iframe');
        $this->oSelenium->switchToFrame($e);
        $e = $this->oSelenium->element('tag name', 'body');
        $e->click();

        $this->oSelenium->sendKeys($content);

        $this->oSelenium->switchToFrame();
    }

    /**
      * @brief 글 작성
      * @return int 
      */
    public function documentWrite()
    {
        $config = $this->oConfig->getConfig();
        $boardInfo = $config['XE_BOARD'];
        $this->documentButton('쓰기');
        $this->oSelenium->setValue('css selector', '.board_write input[name="title"]', $boardInfo['document']['title'] . date("Y-m-d H:i"));
        $this->writeContent($boardInfo['document']['content'] . date("Y-m-d H:i"));
        $this->oSelenium->element('css selector', 'input[value="등록"]')->click();

        $e = $this->oSelenium->waitElement('css selector', '.board_read');
        if ($e == 0) {
            return 0;
        }

        $arrUrl = parse_url($this->oSelenium->getCurrentPath());

        if (!isset($arrUrl['query'])) {
            return 0;
        }

        $arrQuery = explode("&", $arrUrl['query']);
        if (!$arrQuery) {
            return 0;
        }

        foreach ($arrQuery as $val) {
            $arrKeyValue = explode('=', $val);
            if ($arrKeyValue[0] == 'document_srl') {
                return (int)$arrKeyValue[1];
            }
        }

        return 0;
    }

    /**
      * @brief 글 수정
      * @param int $document_srl 글 번호
      * @return int
      */
    public function documentEdit($document_srl)
    {
        $config = $this->oConfig->getConfig();
        $boardInfo = $config['XE_BOARD'];

        $this->documentButton('수정');

        $this->writeContent('Edit content.');
        $this->oSelenium->element('css selector', 'input[value="등록"]')->click();

        $e = $this->oSelenium->waitElement('css selector', '.board_read');
        if ($e == 0) {
            return 0;
        }

        $arrUrl = parse_url($this->oSelenium->getCurrentPath());
        if (!isset($arrUrl['query'])) {
            return 0;
        }

        $arrQuery = explode("&", $arrUrl['query']);
        if (!$arrQuery) {
            return 0;
        }

        foreach ($arrQuery as $val) {
            $arrKeyValue = explode('=', $val);
            if ($arrKeyValue[0] == 'document_srl') {
                return (int)$arrKeyValue[1];
            }
        }

        return 0;
    }

    /**
      * @brief 글 삭제 
      * @param int $document_srl 글 번호
      * @return boolean
      */
    public function documentDelete($document_srl)
    {
        $config = $this->oConfig->getConfig();

        $this->documentButton('삭제');

        // 이동 페이지에서 삭제 클릭
        $arrElement = $this->oSelenium->elements('css selector', '.btnArea .btn');
        foreach ($arrElement as $e) {
            if ($e->attribute('value') == '삭제') {
                $e->click();
                break;
            }
        }

        // 삭제한 글로 이동
        $moveUrl = sprintf('/index.php?mid=%s&document_srl=%s', $config['XE_BOARD']['mid'], $document_srl);
        $this->oSelenium->pageMove($moveUrl);

        $isAlert = true;
        try {
            $p = $this->oSelenium->switchToAlert();
            $p->accept();
        } catch (\PHPWebDriver_NoAlertOpenWebDriverError $e) {
            $isAlert = false;
        }

        return $isAlert;
    }

    /**
      * @brief 버튼의 text를 확인해서 click
      * @param string $btnName button's text
      * @return void
      */
    public function documentButton($btnName)
    {
        $arrElement = $this->oSelenium->elements('css selector', '.btnArea .btn');
        foreach ($arrElement as $e) {
            if ($e->text() == $btnName) {
                $e->click();
                break;
            }
        }
    }

    /**
      * @brief 댓글 작성
      * @return int
      */
    public function commentWrite()
    {
        $config = $this->oConfig->getConfig();
        $boardInfo = $config['XE_BOARD'];

        $this->writeContent($boardInfo['comment']['content'] . date("Y-m-d H:i"));
        $this->oSelenium->element('css selector', '#comment button[type="submit"]')->click();

        $e = $this->oSelenium->waitElement('css selector', '#comment .fbList');
        if ($e == 0) {
            return 0;
        }

        $arrUrl = parse_url($this->oSelenium->getCurrentPath());

        if (!isset($arrUrl['query'])) {
            return 0;
        }

        $arrQuery = explode("&", $arrUrl['query']);
        if (!$arrQuery) {
            return 0;
        }

        foreach ($arrQuery as $val) {
            $arrKeyValue = explode('=', $val);
            if ($arrKeyValue[0] == 'rnd') {
                return (int)$arrKeyValue[1];
            }
        }

        return 0;
    }

    /**
      * @brief 댓글 수정
      * @param int $comment_srl comment 번호
      * @return int
      */
    public function commentEdit($comment_srl)
    {
        $config = $this->oConfig->getConfig();
        $boardInfo = $config['XE_BOARD'];

        $this->oSelenium->element('css selector', '#comment_'.$comment_srl.' a.modify')->click();

        $this->writeContent('Edit content.');
        $this->oSelenium->element('css selector', '.write_comment button[type="submit"]')->click();

        $e = $this->oSelenium->waitElement('css selector', '#comment .fbList');
        if ($e == 0) {
            return 0;
        }

        $arrUrl = parse_url($this->oSelenium->getCurrentPath());

        if (!isset($arrUrl['query'])) {
            return 0;
        }

        $arrQuery = explode("&", $arrUrl['query']);
        if (!$arrQuery) {
            return 0;
        }

        foreach ($arrQuery as $val) {
            $arrKeyValue = explode('=', $val);
            if ($arrKeyValue[0] == 'rnd') {
                return (int)$arrKeyValue[1];
            }
        }

        return 0;
    }

    /**
      * @brief 댓글 삭제
      * @param int $comment_srl comment 번호
      * @return boolean 
      */
    public function commentDelete($comment_srl)
    {
        $this->oSelenium->element('css selector', '#comment_'.$comment_srl.' a.delete')->click();

        // 이동 페이지에서 삭제 클릭
        $arrElement = $this->oSelenium->elements('css selector', '.btnArea .btn');
        foreach ($arrElement as $e) {
            if ($e->attribute('value') == '삭제') {
                $e->click();
                break;
            }
        }

        $e = $this->oSelenium->elements('css selector', '#comment .fbList');

        $isDelete = true;
        if (count($e)>0) {
            $isDelete = false;
        }

        return $isDelete;
    }
}
