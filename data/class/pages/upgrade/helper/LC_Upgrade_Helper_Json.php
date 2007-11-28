<?php
require_once DATA_PATH . 'module/Services/Json.php';
/**
 * Enter description here...
 *
 */
class LC_Upgrade_Helper_Json extends Services_JSON {
    /** */
    var $arrData = array(
        'status'  => null,
        'errcode' => null,
        'msg'     => null,
        'data'    => array()
    );

    /**
     * Enter description here...
     *
     * @return SC_Upgrade_Helper_Json
     */
    function LC_Upgrade_Helper_Json() {
        parent::Services_JSON();
    }

    /**
     * Enter description here...
     *
     */
    function isError() {
        return $this->isSuccess() ? false : true;
    }

    function isSuccess() {
        if ($this->arrData['status'] === OSTORE_STATUS_SUCCESS) {
            return true;
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $errCode
     * @param unknown_type $errMessage
     */
    function setError($errCode) {
        $masterData = new SC_DB_MasterData();
        $arrOStoreErrMsg = $masterData->getMasterData("mtb_ownersstore_err");

        $this->arrData['status']  = OSTORE_STATUS_ERROR;
        $this->arrData['errcode'] = $errCode;
        $this->arrData['msg']  = isset($arrOStoreErrMsg[$errCode])
            ? $arrOStoreErrMsg[$errCode]
            : $arrOStoreErrMsg[OSTORE_E_UNKNOWN];
    }

    /**
     * Enter description here...
     *
     * @param mixed $data
     */
    function setSuccess($data = array(), $msg = '') {
        $this->arrData['status'] = OSTORE_STATUS_SUCCESS;
        $this->arrData['data']   = $data;
        $this->arrData['msg']    = $msg;
    }

    /**
     * Enter description here...
     *
     */
    function display() {
        header("Content-Type: text/javascript; charset=UTF-8");
        echo $this->encode($this->arrData);
    }
}
