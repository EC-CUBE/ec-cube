<?php
require_once(realpath(dirname(__FILE__)) . "/../../../data/class/SC_Response.php");
/**
 * テスト用にexitしないSC_Responseクラスです。
 */
class SC_Response_Ex extends SC_Response {

  /** exitしたかどうかを保持するフラグ */
  var $exited = FALSE;
  /** リダイレクト先のパスを保持するフラグ */
  var $redirectPath = '';

  /**
   * SC_Response::actionExit()をラップし、PHPをexitさせないようにします。
   */
  function actionExit() {
    $this->exited = TRUE;
  }

  /**
   * SC_Response::sendRedirect()をラップし、PHPをexitさせないようにします。
   * また、リダイレクト先のパスを取得できるようにします。
   */
  function sendRedirect($location, $arrQueryString = array(), $inheritQuerySring = false, $useSsl = null) {
    $this->exited = TRUE;
    $this->redirectPath = $location;
  }

  /**
   * actionExit()が呼ばれたかどうかを取得します。
   */
  public function isExited() {
    return $this->exited;
  }

  /**
   * sendRedirect()で指定されたパスを取得します。 
   */
  public function getRedirectPath() {
    return $this->redirectPath;
  }
}


