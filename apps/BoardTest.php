<?php
namespace XEUnitTest;

use \XE\UITest\Selenium\Manager\User\UserLoader;
use \XE\UITest\Selenium\Manager\Board\BoardLoader;

require_once __DIR__ . '/../vendor/autoload.php';

/**
  * @file BoardTest.php
  * @brief 게시글/댓글 작성 테스트
  * @author NAVER (developers@xpressengine.com)
  */
class BoardTest extends \PHPUnit_Framework_TestCase
{
    /**
      * @brief 테스트 종료시 호출됨
      * @return void
      */
    public static function tearDownAfterClass()
    {
        $oBoard = BoardLoader::getInstance();
        $oBoard->oSelenium->close();
    }

    /**
      * @brief 로그인 후 글 작성 테스트
      * @return array|void 
      */
    public function testDocumentWrite()
    {
        try {
            $oBoard = BoardLoader::getInstance();
            $config = $oBoard->oConfig->getConfig();

            $oUser = UserLoader::getInstance();
            $oUser->login($config['XE_MEMBER'][0]);

            $oBoard->oSelenium->pageMove('/' . $config['XE_BOARD']['mid']);

            $document_srl = $oBoard->documentWrite();

            $this->assertGreaterThan(0, $document_srl);
            return array('document_srl'=>$document_srl);
        } catch (\Exception $e) {
            $oBoard->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 글 수정
      * @param array $args 글번호
      * @return array|void 
      * @depends testDocumentWrite
      */
    public function testDocumentEdit(array $args)
    {
        try {
            $pre_document_srl = $args['document_srl'];

            $oBoard = BoardLoader::getInstance();
            $document_srl = $oBoard->documentEdit($pre_document_srl);

            $this->assertEquals($pre_document_srl, $document_srl);

            return array('document_srl'=>$document_srl);
        } catch (\Exception $e) {
            $oBoard->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 글 삭제
      * @param array $args 글번호
      * @return array|void 
      * @depends testDocumentEdit
      */
    public function testDocumentDelete(array $args)
    {
        try {
            $document_srl = $args['document_srl'];

            $oBoard = BoardLoader::getInstance();
            $isDelete = $oBoard->documentDelete($document_srl);

            $this->assertTrue($isDelete);
        } catch (\Exception $e) {
            $oBoard->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 댓글 작성
      * @return array|void
      * @depends testDocumentDelete
      */
    public function testCommentWrite()
    {
        try {
            $oBoard = BoardLoader::getInstance();
            $config = $oBoard->oConfig->getConfig();

            $oBoard->oSelenium->pageMove('/' . $config['XE_BOARD']['mid']);

            $document_srl = $oBoard->documentWrite();
            $comment_srl = $oBoard->commentWrite();

            $this->assertGreaterThan(0, $comment_srl);

            return array('comment_srl'=>$comment_srl);
        } catch (\Exception $e) {
            $oBoard->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 댓글 수정
      * @param array $args 글번호
      * @return array|void 
      * @depends testCommentWrite
      */
    public function testCommentEdit(array $args)
    {
        try {
            $pre_comment_srl = $args['comment_srl'];

            $oBoard = BoardLoader::getInstance();
            $comment_srl = $oBoard->commentEdit($pre_comment_srl);

            $this->assertEquals($pre_comment_srl, $comment_srl);

            return array('comment_srl'=>$comment_srl);
        } catch (\Exception $e) {
            $oBoard->setScreenshot();
            throw $e;
        }
    }

    /**
      * @brief 댓글 삭제
      * @param array $args 글번호
      * @return array|void 
      * @depends testCommentEdit
      */
    public function testCommentDelete(array $args)
    {
        try {
            $pre_comment_srl = $args['comment_srl'];

            $oBoard = BoardLoader::getInstance();
            $isDelete = $oBoard->commentDelete($pre_comment_srl);

            $this->assertTrue($isDelete);
        } catch (\Exception $e) {
            $oBoard->setScreenshot();
            throw $e;
        }
    }
}
