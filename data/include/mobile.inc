<?php
/**
 * モバイルサイト共有関数ファイル
 */

/**
 * EC-CUBE がサポートする携帯端末かどうかをチェックする。
 * 非対応端末の場合は unsupported/index.php へリダイレクトする。
 *
 * @return void
 */
function lfMobileCheckCompatibility() {
	if (!GC_MobileUserAgent::isSupported()) {
		header('Location: ' . URL_DIR . 'unsupported/index.php');
		exit;
	}
}

/**
 * 入力データを内部エンコーディングに変換し、絵文字を除去する。
 *
 * @param string &$value 入力データへの参照
 * @return void
 */
function lfMobileConvertInputValue(&$value) {
	// Shift JIS から内部エンコーディングに変換する。
	// SoftBank 以外の絵文字は外字領域に含まれるため、この段階で除去される。
	$value = mb_convert_encoding($value, CHAR_CODE, 'SJIS');

	// SoftBank の絵文字を除去する。
	$value = preg_replace('/\\x1b\\$[^\\x0f]*\\x0f/', '', $value);
}

/**
 * モバイルサイト用の入力の初期処理を行う。
 *
 * @return void
 */
function lfMobileInitInput() {
	array_walk($_GET, 'lfMobileConvertInputValue');
	array_walk($_POST, 'lfMobileConvertInputValue');
	array_walk($_REQUEST, 'lfMobileConvertInputValue');
}

/**
 * パラメーターから有効なセッションIDを取得する。
 *
 * @return string|false 取得した有効なセッションIDを返す。
 *                      取得できなかった場合は false を返す。
 */
function lfMobileGetSessionId() {
	// パラメーターからセッションIDを取得する。
	$sessionId = @$_POST[session_name()];
	if (!isset($sessionId)) {
		$sessionId = @$_GET[session_name()];
	}
	if (!isset($sessionId)) {
		return false;
	}

	// セッションIDのフォーマットをチェックする。
	if (preg_match('/^[0-9a-zA-Z,-]{32,}$/', $sessionId) < 1) {
		gfPrintLog("Invalid session id : sid=$sessionId");
		return false;
	}

	// セッションIDの存在をチェックする。
	if (sfSessRead($sessionId) === null) {
		gfPrintLog("Non-existent session id : sid=$sessionId");
		return false;
	}

	return session_id($sessionId);
}

/**
 * セッションデータが有効かどうかをチェックする。
 *
 * @return boolean セッションデータが有効な場合は true、無効な場合は false を返す。
 */
function lfMobileValidateSession() {
	// 配列 mobile が登録されているかどうかをチェックする。
	if (!is_array(@$_SESSION['mobile'])) {
		return false;
	}

	// 有効期限を過ぎていないかどうかをチェックする。
	if (intval(@$_SESSION['mobile']['expires']) < time()) {
		gfPrintLog("Session expired at " .
		           date('Y/m/d H:i:s', @$_SESSION['mobile']['expires']) .
		           ' : sid=' . session_id());

		return false;
	}

	// 携帯端末の機種が一致するかどうかをチェックする。
	$model = GC_MobileUserAgent::getModel();
	if (@$_SESSION['mobile']['model'] != $model) {
		gfPrintLog("User agent model mismatch : " .
		           "\"$model\" != \"" . @$_SESSION['mobile']['model'] .
		           '" (expected), sid=' . session_id());
		return false;
	}

	return true;
}

/**
 * モバイルサイト用のセッション関連の初期処理を行う。
 *
 * @return void
 */
function lfMobileInitSession() {
	// セッションIDの受け渡しにクッキーを使用しない。
	ini_set('session.use_cookies', '0');
	ini_set('session.use_trans_sid', '1');

	// パラメーターから有効なセッションIDを取得する。
	$sessionId = lfMobileGetSessionId();

	session_start();

	// セッションIDまたはセッションデータが無効な場合は、セッションIDを再生成
	// し、セッションデータを初期化する。
	if ($sessionId === false || !lfMobileValidateSession()) {
		session_regenerate_id();
		$_SESSION = array('mobile' => array('model'    => GC_MobileUserAgent::getModel(),
		                                    'phone_id' => GC_MobileUserAgent::getId(),
		                                    'expires'  => time() + MOBILE_SESSION_LIFETIME));

		// 新しいセッションIDを付加してリダイレクトする。
		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
			// GET の場合は同じページにリダイレクトする。
			require_once(DATA_PATH . 'module/Net/URL.php');
			$url = new Net_URL();
			$url->addQueryString(session_name(), session_id());
			header('Location: ' . $url->getURL());
		} else {
			// GET 以外の場合はトップページへリダイレクトする。
			header('Location: ' . URL_SITE_TOP . '?' . SID);
		}
		exit;
	}

	// 携帯端末IDを取得できた場合はセッションデータに保存する。
	$phoneId = GC_MobileUserAgent::getId();
	if ($phoneId !== false) {
		$_SESSION['mobile']['phone_id'] = $phoneId;
	}

	// セッションの有効期限を更新する。
	$_SESSION['mobile']['expires'] = time() + MOBILE_SESSION_LIFETIME;
}

/**
 * モバイルサイト用の出力の初期処理を行う。
 *
 * 出力の流れ
 * 1. Smarty
 * 2. 内部エンコーディングから Shift JIS に変換する。
 * 3. 全角カタカナを半角カタカナに変換する。
 * 4. 絵文字タグを絵文字コードに変換する。
 * 5. 出力
 *
 * @return void
 */
function lfMobileInitOutput() {
	// Smarty 用のディレクトリーを作成する。
	@mkdir(COMPILE_DIR);

	// 出力用のエンコーディングを Shift JIS に固定する。
	mb_http_output('SJIS-win');

	// 絵文字タグを絵文字コードに変換する。
	ob_start(array('GC_MobileEmoji', 'handler'));

	// 全角カタカナを半角カタカナに変換する。
	ob_start(create_function('$buffer', 'return mb_convert_kana($buffer, "k", "SJIS-win");'));

	// 内部エンコーディングから Shift JIS に変換する。
	ob_start('mb_output_handler');
}

/**
 * モバイルサイト用の初期処理を行う。
 *
 * @return void
 */
function sfMobileInit() {
	lfMobileInitInput();

	if (basename(dirname($_SERVER['SCRIPT_NAME'])) != 'unsupported') {
		lfMobileCheckCompatibility();
		lfMobileInitSession();
	}

	lfMobileInitOutput();
}
?>
