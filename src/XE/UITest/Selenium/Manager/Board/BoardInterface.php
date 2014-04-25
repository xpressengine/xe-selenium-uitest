<?php
namespace XE\UITest\Selenium\Manager\Board;

/**
  * @file BoardInterface.php
  * @brief interface
  * @author NAVER (developers@xpressengine.com)
  */
interface BoardInterface
{
    /**
      * @brief 게시물 작성
      */
    public function documentWrite();

    /**
      * @brief 게시물 수정
      */
    public function documentEdit($document_srl);

    /**
      * @brief 게시물 삭제
      */
    public function documentDelete($document_srl);

    /**
      * @brief 댓글 작성
      */
    public function commentWrite();

    /**
      * @brief 댓글 수정
      */
    public function commentEdit($comment_srl);

    /**
      * @brief 댓글 삭정
      */
    public function commentDelete($comment_srl);
}
