<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: ebis_tag.php,v 1.0 2006/10/26 04:02:40 naka Exp $
 * @link		http://www.lockon.co.jp/
 *
 */
 
//ページ管理クラス
class LC_Page {
	//コンストラクタ
	function LC_Page() {
		//メインテンプレートの指定
		$this->tpl_mainpage = MODULE_PATH . 'security/security.tpl';
		$this->tpl_subtitle = 'セキュリティチェック';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

switch($_POST['mode']) {
case 'edit':
    $inst_inc = DATA_PATH . 'install.php';
    // install.phpの隠蔽
    $hidden_inc = MODULE_PATH . 'security/install_inc.php';
    if(sfIsNormalInstallInc()) {
        if(copy($inst_inc, $hidden_inc)) {
            if(file_exists($hidden_inc)) {
		        $require = "<?php\n".
		        		   "    require_once('$hidden_inc');\n".
		        		   "?>";
		        if($fp = fopen($inst_inc,"w")) {
					fwrite($fp, $require);
					fclose($fp);
		        }
            }
        }
	}
	break;
default:
    break;
}

$arrList[] = sfCheckOpenData();
$arrList[] = sfCheckInstall();
$arrList[] = sfCheckIDPass('admin', 'password');
$arrList[] = sfCheckInstallInc();

$objPage->arrList = $arrList;

$objView->assignobj($objPage);					//変数をテンプレートにアサインする
$objView->display($objPage->tpl_mainpage);		//テンプレートの出力
//-------------------------------------------------------------------------------------------------------
// 設定ファイル(data)のパスが公開パスでないか確認する
function sfCheckOpenData() {
    // ドキュメントルートのパスを推測する。
    $doc_root = ereg_replace(URL_DIR . "$","/",HTML_PATH);
    $data_path = realpath(DATA_PATH);
    
    // dataのパスがドキュメントルート以下にあるか判定
    if(ereg("^".$doc_root, $data_path)) {
        $arrResult['result'] = "×";
        $arrResult['detail'] = "設定ファイルが、公開されている可能性があります。<br>";
        $arrResult['detail'].= "/data/ディレクトリは、非公開のパスに設置して下さい。";
    } else {
        $arrResult['result'] = "○";
        $arrResult['detail'] = "設定ファイルは、公開パス配下に存在しません。";        
    }
    
    $arrResult['title'] = "設定ファイルの保存パス";
    return $arrResult;
}

// インストールファイルが存在するか確認する
function sfCheckInstall() {
    // インストールファイルの存在チェック
    $inst_path = HTML_PATH . "install/index.php";
    
    if(file_exists($inst_path)) {
        $arrResult['result'] = "×";
        $arrResult['detail'] = "/install/index.phpは、インストール完了後にファイルを削除してください。";            
    } else {
        $arrResult['result'] = "○";
        $arrResult['detail'] = "/install/index.phpは、見つかりませんでした。";    
    }
    
    $arrResult['title'] = "インストールファイルのチェック";
    return $arrResult;
}

// 管理者ユーザのID/パスワードチェック
function sfCheckIDPass($user, $password) {
    $objQuery = new SC_Query();
    $sql = "SELECT password FROM dtb_member WHERE login_id = ? AND del_flg = 0";
	// DBから暗号化パスワードを取得する。
	$arrRet = $objQuery->getAll($sql, array($user));
	// ユーザ入力パスワードの判定
	$ret = sha1($password . ":" . AUTH_MAGIC);
    
    if($ret == $arrRet[0]['password']) {
        $arrResult['result'] = "×";
        $arrResult['detail'] = "非常に推測のしやすい管理者IDとなっています。個人情報漏洩の危険性があります。";       
    } else {
        if(count($arrRet) > 0) {
	        $arrResult['result'] = "△";
	        $arrResult['detail'] = "管理者名に「admin」を利用しないようにして下さい。";               
        } else {
            $arrResult['result'] = "○";
            $arrResult['detail'] = "独自のID、パスワードが設定されているようです。";               
        }
    }
    
    $arrResult['title'] = "ID/パスワードのチェック";
    return $arrResult;
}


// install.phpのファイルをチェックする
function sfCheckInstallInc() {
    // install.phpが隠蔽後のものか判定する
    if(sfIsNormalInstallInc()) {
        $arrResult['result'] = "×";
        $arrResult['detail'] = "install.phpを簡単に表示できなくすることができます。内容を隠蔽しますか？";
        $arrResult['detail'].= "<input type='submit' value='隠蔽する'>";        
    } else {
        $arrResult['result'] = "○";
        $arrResult['detail'] = "install.phpの隠蔽対策がとられています。";                       
    }
    $arrResult['title'] = "install.phpの可読性チェック";
    return $arrResult;
}

// install.phpが隠蔽後のものか判定する
function sfIsNormalInstallInc() {
    // install.phpのパスを取得する
    $inst_inc = DATA_PATH . 'install.php';
    if(file_exists($inst_inc)) {
        if($fp = fopen($inst_inc, "r")) {
            $data = fread($fp, filesize($inst_inc));
            fclose($fp);
        }
        if(ereg("DB_PASSWORD", $data)) {
            return true;
        }
    }
    return false;
}

?>