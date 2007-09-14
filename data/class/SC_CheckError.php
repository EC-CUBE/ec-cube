<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*----------------------------------------------------------------------
 * [̾��] SC_CheckError
 * [����] ���顼�����å����饹
 *----------------------------------------------------------------------
 */
class SC_CheckError {
	var $arrErr;
	var $arrParam;
	
	// �����å��оݤ��ͤ��ޤޤ������򥻥åȤ��롣
	function SC_CheckError($array = "") {
		if($array != "") {
			$this->arrParam = $array;
		} else {
			$this->arrParam = $_POST;
		}

	}
	
	function doFunc($value, $arrFunc) {
		foreach ( $arrFunc as $key ) {
			$this->$key($value);
		}
	}
	
	/* HTML�Υ���������å����� */
	// value[0] = ����̾ value[1] = Ƚ���о� value[2] = ���Ĥ��륿������Ǽ���줿����
	function HTML_TAG_CHECK($value) {
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		
		// �ޤޤ�Ƥ��륿������Ф���
		preg_match_all("/<([\/]?[a-z]+)/", $this->arrParam[$value[1]], $arrTag);

		foreach($arrTag[1] as $val) {
			$find = false;
			
			foreach($value[2] as $tag) {
				if(eregi("^" . $tag . "$", $val)) {
					$find = true;
				} else {
				}
			}
			
			if(!$find) {
				$this->arrErr[$value[1]] = "�� " . $value[0] . "�˵��Ĥ���Ƥ��ʤ�����[" . strtoupper($val) . "]���ޤޤ�Ƥ��ޤ���<br />";
				return;
			}		
		}		
	}

	/*��ɬ�����Ϥ�Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�
	function EXIST_CHECK( $value ) {			// ������꤬�ʤ���票�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if( strlen($this->arrParam[$value[1]]) == 0 ){					
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�����Ϥ���Ƥ��ޤ���<br />";
		}
	}
	
	/*�����ڡ��������֤�Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�
	function SPTAB_CHECK( $value ) {			// ������꤬�ʤ���票�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(strlen($this->arrParam[$value[1]]) != 0 && ereg("^[ ��\t\r\n]+$", $this->arrParam[$value[1]])){						
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�˥��ڡ��������֡����ԤΤߤ����ϤϤǤ��ޤ���<br />";
		}
	}
	
	/*�����ڡ��������֤�Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�
	function NO_SPTAB( $value ) {			// ������꤬�ʤ���票�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(strlen($this->arrParam[$value[1]]) != 0 && mb_ereg("[�� \t\r\n]+", $this->arrParam[$value[1]])){						
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�˥��ڡ��������֡����Ԥϴޤ�ʤ��ǲ�������<br />";
		}
	}
	
	/* ����ǳ��Ϥ���Ƥ�����ͤ�Ƚ�� */
	function ZERO_START($value) {
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(strlen($this->arrParam[$value[1]]) != 0 && ereg("^[0]+[0-9]+$", $this->arrParam[$value[1]])){						
			$this->arrErr[$value[1]] = "�� " . $value[0] . "��0�ǻϤޤ���ͤ����Ϥ���Ƥ��ޤ���<br />";
		}
	}
	
	/*��ɬ�������Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о� 
	function SELECT_CHECK( $value ) {			// �ץ������ʤɤ����򤵤�Ƥ��ʤ���票�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if( strlen($this->arrParam[$value[1]]) == 0 ){						
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�����򤵤�Ƥ��ޤ���<br />";
		}
	}

	/*��Ʊ������Ƚ�ꡡ*/
	// value[0] = ����̾1 value[1] = ����̾2 value[2] = Ƚ���о�ʸ����1  value[3] = Ƚ���о�ʸ����2
	function EQUAL_CHECK( $value ) {		// ���Ϥ�����ʸ�����ʾ�ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[3]])) {
			return;
		}
		// ʸ�����μ���			
		if( $this->arrParam[$value[2]] != $this->arrParam[$value[3]]) {
			$this->arrErr[$value[2]] = "�� " . $value[0] . "��" . $value[1] . "�����פ��ޤ���<br />";
		}
	}
	
	/*���ͤ��ۤʤ뤳�Ȥ�Ƚ�ꡡ*/
	// value[0] = ����̾1 value[1] = ����̾2 value[2] = Ƚ���о�ʸ����1  value[3] = Ƚ���о�ʸ����2
	function DIFFERENT_CHECK( $value ) {		// ���Ϥ�����ʸ�����ʾ�ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[3]])) {
			return;
		}
		// ʸ�����μ���			
		if( $this->arrParam[$value[2]] == $this->arrParam[$value[3]]) {
			$this->arrErr[$value[2]] = "�� " . $value[0] . "��" . $value[1] . "�ϡ�Ʊ���ͤ���ѤǤ��ޤ���<br />";
		}
	}
	
	/*���ͤ��礭������Ӥ��� value[2] < value[3]�Ǥʤ���Х��顼��*/
	// value[0] = ����̾1 value[1] = ����̾2 value[2] = Ƚ���о�ʸ����1  value[3] = Ƚ���о�ʸ����2
	function GREATER_CHECK($value) {		// ���Ϥ�����ʸ�����ʾ�ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[3]])) {
			return;
		}
		// ʸ�����μ���			
		if($this->arrParam[$value[2]] != "" && $this->arrParam[$value[3]] != "" && ($this->arrParam[$value[2]] > $this->arrParam[$value[3]])) {
			$this->arrErr[$value[2]] = "�� " . $value[0] . "��" . $value[1] . "����礭���ͤ����ϤǤ��ޤ���<br />";
		}
	}
	
	
	/*������ʸ�������¤�Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ����  value[2] = ����ʸ����(Ⱦ�Ѥ����Ѥ�1ʸ���Ȥ��ƿ�����)
	function MAX_LENGTH_CHECK( $value ) {		// ���Ϥ�����ʸ�����ʾ�ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		// ʸ�����μ���			
		if( mb_strlen($this->arrParam[$value[1]]) > $value[2] ) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "��" . $value[2] . "���ʲ������Ϥ��Ƥ���������<br />";
		}
	}
	
	/*���Ǿ�ʸ�������¤�Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ���� value[2] = �Ǿ�ʸ����(Ⱦ�Ѥ����Ѥ�1ʸ���Ȥ��ƿ�����)
	function MIN_LENGTH_CHECK( $value ) {		// ���Ϥ�����ʸ����̤���ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}										
		// ʸ�����μ���		
		if( mb_strlen($this->arrParam[$value[1]]) < $value[2] ) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "��" . $value[2] . "���ʾ�����Ϥ��Ƥ���������<br />";
		}
	}
	
	/*������ʸ�������¤�Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ����  value[2] = �����
	function MAX_CHECK( $value ) {		// ���Ϥ�������ʾ�ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		// ʸ�����μ���			
		if($this->arrParam[$value[1]] > $value[2] ) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "��" . $value[2] . "�ʲ������Ϥ��Ƥ���������<br />";
		}
	}
	
	/*���Ǿ��������¤�Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ����  value[2] = �Ǿ���
	function MIN_CHECK( $value ) {		// ���Ϥ��Ǿ���̤���ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if($this->arrParam[$value[1]] < $value[2] ) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "��" . $value[2] . "�ʾ�����Ϥ��Ƥ���������<br />";
		}
	}
	
		
	/*��������Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ���� 
	function NUM_CHECK( $value ) {				// ����ʸ���������ʳ��ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if( strlen($this->arrParam[$value[1]]) > 0 && !EregI("^[[:digit:]]+$", $this->arrParam[$value[1]])) { 
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�Ͽ��������Ϥ��Ƥ���������<br />";	
		}
	}
	
		/*����������ޤ������Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ���� 
	function NUM_POINT_CHECK( $value ) {				// ����ʸ���������ʳ��ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if( strlen($this->arrParam[$value[1]]) > 0 && !EregI("^[[:digit:]]+[\.]?[[:digit:]]+$", $this->arrParam[$value[1]])) { 
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�Ͽ��������Ϥ��Ƥ���������<br />";	
		}
	}
		
	function ALPHA_CHECK($value) {
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if( strlen($this->arrParam[$value[1]]) > 0 && !EregI("^[[:alpha:]]+$", $this->arrParam[$value[1]])) { 
			$this->arrErr[$value[1]] = "�� " . $value[0] . "��Ⱦ�ѱѻ������Ϥ��Ƥ���������<br />";	
		}
	}
	
	/* �����ֹ��Ƚ�� �ʿ��������å���ʸ���������å���»ܤ��롣)
		value[0] : ����̾
		value[1] : ����1������
		value[2] : ����2������
		value[3] : ����3������
		value[4] : ʸ��������
	*/
	function TEL_CHECK($value) {
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		
		$cnt = 0;
		
		for($i = 1; $i <= 3; $i++) {
			if(strlen($this->arrParam[$value[$i]]) > 0) {
				$cnt++;
			}
		}
		
		// ���٤Ƥι��ܤ���������Ƥ��ʤ�����Ƚ��(�����������Ϥ���Ƥ������)
		if($cnt > 0 && $cnt < 3) {
			$this->arrErr[$value[1]] .= "�� " . $value[0] . "�Ϥ��٤Ƥι��ܤ����Ϥ��Ƥ���������<br />";
		}
					
		for($i = 1; $i <= 3; $i++) {
			if(strlen($this->arrParam[$value[$i]]) > 0 && strlen($this->arrParam[$value[$i]]) > $value[4]) {
				$this->arrErr[$value[$i]] .= "�� " . $value[0] . $i . "��" . $value[4] . "����������Ϥ��Ƥ���������<br />";
			} else if (strlen($this->arrParam[$value[$i]]) > 0 && !EregI("^[[:digit:]]+$", $this->arrParam[$value[$i]])) {
				$this->arrErr[$value[$i]] .= "�� " . $value[0] . $i . "�Ͽ��������Ϥ��Ƥ���������<br />";
			}
		}
	}
	
	/* ��Ϣ���ܤ���������������Ƥ��뤫Ƚ�� 
		value[0]		: ����̾
		value[1]		: Ƚ���о�����̾
	*/
	function FULL_EXIST_CHECK($value) {
		$max = count($value);
			
		// ���˳������ܤ˥��顼��������ϡ�Ƚ�ꤷ�ʤ���
		for($i = 1; $i < $max; $i++) {
			if(isset($this->arrErr[$value[$i]])) {
				return;
			}
		}
		
		$blank = false;
		
		// ���٤Ƥι��ܤ��֥�󥯤Ǥʤ��������٤Ƥι��ܤ����Ϥ���Ƥ��ʤ����ϥ��顼�Ȥ��롣
		for($i = 1; $i < $max; $i++) {
			if(strlen($this->arrParam[$value[$i]]) <= 0) {
				$blank = true;
			}
		}
		
		if($blank) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�����Ϥ���Ƥ��ޤ���<br />";
		}
	}
		
	/* ��Ϣ���ܤ����٤���������Ƥ��뤫Ƚ�� 
		value[0]		: ����̾
		value[1]		: Ƚ���о�����̾
	*/
	function ALL_EXIST_CHECK($value) {
		$max = count($value);
			
		// ���˳������ܤ˥��顼��������ϡ�Ƚ�ꤷ�ʤ���
		for($i = 1; $i < $max; $i++) {
			if(isset($this->arrErr[$value[$i]])) {
				return;
			}
		}
		
		$blank = false;
		$input = false;
		
		// ���٤Ƥι��ܤ��֥�󥯤Ǥʤ��������٤Ƥι��ܤ����Ϥ���Ƥ��ʤ����ϥ��顼�Ȥ��롣
		for($i = 1; $i < $max; $i++) {
			if(strlen($this->arrParam[$value[$i]]) <= 0) {
				$blank = true;
			} else {
				$input = true;
			}
		}
		
		if($blank && $input) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�Ϥ��٤Ƥι��ܤ����Ϥ��Ʋ�������<br />";
		}
	}
	
		/* ��Ϣ���ܤ��ɤ줫�����������Ƥ��뤫Ƚ�� 
		value[0]		: ����̾
		value[1]		: Ƚ���о�����̾
	*/
	function ONE_EXIST_CHECK($value) {
		$max = count($value);
			
		// ���˳������ܤ˥��顼��������ϡ�Ƚ�ꤷ�ʤ���
		for($i = 1; $i < $max; $i++) {
			if(isset($this->arrErr[$value[$i]])) {
				return;
			}
		}
		
		$input = false;
		
		// ���٤Ƥι��ܤ��֥�󥯤Ǥʤ��������٤Ƥι��ܤ����Ϥ���Ƥ��ʤ����ϥ��顼�Ȥ��롣
		for($i = 1; $i < $max; $i++) {
			if(strlen($this->arrParam[$value[$i]]) > 0) {
				$input = true;
			}
		}
		
		if(!$input) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�����Ϥ���Ƥ��ޤ���<br />";
		}
	}
	
	/* ��̤ι��ܤ���������Ƥ��뤫Ƚ��
		value[0]		: ����̾
		value[1]		: Ƚ���о�����̾
	*/
	function TOP_EXIST_CHECK($value) {
		$max = count($value);
			
		// ���˳������ܤ˥��顼��������ϡ�Ƚ�ꤷ�ʤ���
		for($i = 1; $i < $max; $i++) {
			if(isset($this->arrErr[$value[$i]])) {
				return;
			}
		}
		
		$blank = false;
		$error = false;
				
		// ���٤Ƥι��ܤ��֥�󥯤Ǥʤ��������٤Ƥι��ܤ����Ϥ���Ƥ��ʤ����ϥ��顼�Ȥ��롣
		for($i = 1; $i < $max; $i++) {
			if(strlen($this->arrParam[$value[$i]]) <= 0) {
				$blank = true;
			} else {
				if($blank) {
					$error = true;
				}
			}
		}
		
		if($error) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "����Ƭ�ι��ܤ�����֤����Ϥ��Ʋ�������<br />";
		}
	}
	
	
	/*���������ʤ�Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ���� 
	function KANA_CHECK( $value ) {				// ����ʸ�������ʰʳ��ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(strlen($this->arrParam[$value[1]]) > 0 && ! mb_ereg("^[��-����-�ߡ�]+$", $this->arrParam[$value[1]])) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�ϥ������ʤ����Ϥ��Ƥ���������<br />";	
		}
	}
	
	/*���������ʤ�Ƚ��2�ʥ��֡����ڡ����ϵ��Ĥ���ˡ�*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ���� 
	function KANABLANK_CHECK( $value ) {				// ����ʸ�������ʰʳ��ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(strlen($this->arrParam[$value[1]]) > 0 && ! mb_ereg("^([�� \t\r\n]|[��-��]|[��])+$", $this->arrParam[$value[1]])) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�ϥ������ʤ����Ϥ��Ƥ���������<br />";	
		}
	}

	/*���ѿ�����Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ���� 
	function ALNUM_CHECK( $value ) {				// ����ʸ�����ѿ����ʳ��ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}										
		if( strlen($this->arrParam[$value[1]]) > 0 && ! EregI("^[[:alnum:]]+$", $this->arrParam[$value[1]] ) ) { 
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�ϱѿ��������Ϥ��Ƥ���������<br />";	
		}
	}

	/*��ɬ�������Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�
	function ZERO_CHECK( $value ) {				// �����ͤ�0��������ʤ���票�顼���֤�
		
		if($this->arrParam[$value[1]] == "0" ){						
			$this->arrErr[$value[1]] = "�� " . $value[0] . "��1�ʾ�����Ϥ��Ƥ���������<br />";
		}
	}

	/*�������Ƚ�ꡡ�ʺǾ������*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ���� value[2] = �Ǿ���� value[3] = ������
	function NUM_RANGE_CHECK( $value ) {		// ����ʸ���η��Ƚ�ꡡ�����Ǿ����������ʸ����������
		if(isset($this->arrErr[$value[1]])) {
			return;
		}										 
		// $this->arrParam[$value[0]] = mb_convert_kana($this->arrParam[$value[0]], "n");										
		$count = strlen($this->arrParam[$value[1]]);
		if( ( $count > 0 ) && $value[2] > $count || $value[3] < $count ) {  
			$this->arrErr[$value[1]] =  "�� $value[0]��$value[2]���$value[3]������Ϥ��Ʋ�������<br />";
		}
	}

	/*�������Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ���� value[2] = ��� 
	function NUM_COUNT_CHECK( $value ) {		// ����ʸ���η��Ƚ�ꡡ��������ʸ���� = ������ʳ���NG�ξ��
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		$count = strlen($this->arrParam[$value[1]]);
		if(($count > 0) && $count != $value[2] ) {  
			$this->arrErr[$value[1]] =  "�� $value[0]��$value[2]������Ϥ��Ʋ�������<br />";
		}
	}				
	
	/*���᡼�륢�ɥ쥹������Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���оݥ᡼�륢�ɥ쥹
	function EMAIL_CHECK( $value ){				//���᡼�륢�ɥ쥹������ɽ����Ƚ�ꤹ��
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(strlen($this->arrParam[$value[1]]) > 0 && !ereg("^[^@]+@[^.]+\..+", $this->arrParam[$value[1]])) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�η����������Ǥ���<br />";
		}
	}		
		
	/*���᡼�륢�ɥ쥹�˻��ѤǤ���ʸ����Ƚ�ꡡ*/
	//  value[0] = ����̾ value[1] = Ƚ���оݥ᡼�륢�ɥ쥹
	function EMAIL_CHAR_CHECK( $value ){				//���᡼�륢�ɥ쥹�˻��Ѥ���ʸ��������ɽ����Ƚ�ꤹ��
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(strlen($this->arrParam[$value[1]]) > 0 && !ereg("^[a-zA-Z0-9_\.@\+\?-]+$",$this->arrParam[$value[1]]) ) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�˻��Ѥ���ʸ�������������Ϥ��Ƥ���������<br />";
		}
	}		
	
	/*��URL������Ƚ�ꡡ*/
	//  value[0] = ����̾ value[1] = Ƚ���о�URL
	function URL_CHECK( $value ){				//��URL������ɽ����Ƚ�ꤹ�롣�ǥե���Ȥ�http://�����äƤ�OK
	 	if(isset($this->arrErr[$value[1]])) {
			return;
		}										
        if( strlen($this->arrParam[$value[1]]) > 0 && !ereg( "^https?://+($|[a-zA-Z0-9_~=&\?\.\/-])+$", $this->arrParam[$value[1]] ) ) {
            $this->arrErr[$value[1]] = "�� " . $value[0] . "�����������Ϥ��Ƥ���������<br />";     
        }
    }
  	    
	/*����ĥ�Ҥ�Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о� value[2]=array(��ĥ��)	
	function FILE_EXT_CHECK( $value ) {			// ������꤬�ʤ���票�顼���֤�
		if(isset($this->arrErr[$value[1]]) || count($value[2]) == 0) {
			return;
		}
		
		if($_FILES[$value[1]]['name'] != "" ) {										
			$errFlag = 1;
			$array_ext = explode(".", $_FILES[$value[1]]['name']);
			$ext = $array_ext[ count ( $array_ext ) - 1 ];
			$ext = strtolower($ext);
			
			$strExt = "";
			
			foreach ( $value[2] as $checkExt ){
				if ( $ext == $checkExt) {
					$errFlag = 0;
				}
				
				if($strExt == "") {
					$strExt.= $checkExt;
				} else {
					$strExt.= "��$checkExt";
				}
			}
		}
		if ($errFlag == 1) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�ǵ��Ĥ���Ƥ�������ϡ�" . $strExt . "�Ǥ���<br />";
		}
	}
	
	/* �ե����뤬¸�ߤ��뤫�����å����� */
	// value[0] = ����̾ value[1] = Ƚ���о�  value[2] = ����ǥ��쥯�ȥ�
	function FIND_FILE( $value ) {			// ������꤬�ʤ���票�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		
		if($value[2] != "") {
			$dir = $value[2];
		} else {
			$dir = IMAGE_SAVE_DIR;
		}
		
		$path = $dir . "/" . $this->arrParam[$value[1]];
		$path = ereg_replace("//", "/", $path);
				
		if($this->arrParam[$value[1]] != "" && !file_exists($path)){
			$this->arrErr[$value[1]] = "�� " . $path . "�����Ĥ���ޤ���<br />";
		}
	}
	
	/*���ե����뤬�夲��줿����ǧ��*/
	// value[0] = ����̾ value[1] = Ƚ���о�  value[2] = ���ꥵ������KB)
	function FILE_EXIST_CHECK( $value ) {			// ������꤬�ʤ���票�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}											
		if(!($_FILES[$value[1]]['size'] != "" && $_FILES[$value[1]]['size'] > 0)){
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�򥢥åץ��ɤ��Ʋ�������<br />";
		}
	}
	
	/*���ե����륵������Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�  value[2] = ���ꥵ������KB)
	function FILE_SIZE_CHECK( $value ) {			// ������꤬�ʤ���票�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}											
		if( $_FILES[$value[1]]['size'] > $value[2] *  1024 ){
			$byte = "KB";
			if( $value[2] >= 1000 ) {
				$value[2] = $value[2] / 1000; 
				$byte = "MB";
			}
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�Υե����륵������" . $value[2] . $byte . "�ʲ��Τ�Τ���Ѥ��Ƥ���������<br />";
		}
	}

	/*���ե�����̾��Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ����
	function FILE_NAME_CHECK( $value ) {				// ����ʸ�����ѿ���,"_","-"�ʳ��ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if( strlen($_FILES[$value[1]]['name']) > 0 && ! EregI("^[[:alnum:]_\.-]+$", $_FILES[$value[1]]['name']) ) { 
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�Υե�����̾�����ܸ�䥹�ڡ����ϻ��Ѥ��ʤ��ǲ�������<br />";	
		}
	}

	/*���ե�����̾��Ƚ��(���åץ��ɰʳ��λ�)��*/
	// value[0] = ����̾ value[1] = Ƚ���о�ʸ����
	function FILE_NAME_CHECK_BY_NOUPLOAD( $value ) {			// ����ʸ�����ѿ���,"_","-"�ʳ��ʤ饨�顼���֤�
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
	
		if( strlen($this->arrParam[$value[1]]) > 0 && ! EregI("^[[:alnum:]_\.-]+$", $this->arrParam[$value[1]]) || EregI("[\\]" ,$this->arrParam[$value[1]])) { 
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�Υե�����̾�����ܸ�䥹�ڡ����ϻ��Ѥ��ʤ��ǲ�������<br />";	
		}
	}
		
	//���ե����å�
	// value[0] = ����̾
	// value[1] = YYYY
	// value[2] = MM
	// value[3] = DD
	function CHECK_DATE($value) {						
		if(isset($this->arrErr[$value[1]])) {
			return;
		}										
		// ���ʤ��Ȥ�ɤ줫��Ĥ����Ϥ���Ƥ��롣
		if($this->arrParam[$value[1]] > 0 || $this->arrParam[$value[2]] > 0 || $this->arrParam[$value[3]] > 0) {
			// ǯ�����Τɤ줫�����Ϥ���Ƥ��ʤ���
			if(!(strlen($this->arrParam[$value[1]]) > 0 && strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0)) {
				$this->arrErr[$value[1]] = "�� " . $value[0] . "�Ϥ��٤Ƥι��ܤ����Ϥ��Ʋ�������<br />";
			} else if ( ! checkdate($this->arrParam[$value[2]], $this->arrParam[$value[3]], $this->arrParam[$value[1]])) {						
				$this->arrErr[$value[1]] = "�� " . $value[0] . "������������ޤ���<br />";
			}
		}
	}
	
	//���ե����å�
	// value[0] = ����̾
	// value[1] = YYYY
	// value[2] = MM
	// value[3] = DD
	// value[4] = HH
	// value[5] = mm
	function CHECK_DATE2($value) {						
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
									
		// ���ʤ��Ȥ�ɤ줫��Ĥ����Ϥ���Ƥ��롣
		if($this->arrParam[$value[1]] > 0 || $this->arrParam[$value[2]] > 0 || $this->arrParam[$value[3]] > 0 || $this->arrParam[$value[4]] >= 0 || $this->arrParam[$value[5]] >= 0) {
			// ǯ�������Τɤ줫�����Ϥ���Ƥ��ʤ���
			if(!(strlen($this->arrParam[$value[1]]) > 0 && strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && strlen($this->arrParam[$value[4]]) > 0 && strlen($this->arrParam[$value[5]]) > 0 )) {
				$this->arrErr[$value[1]] = "�� " . $value[0] . "�Ϥ��٤Ƥι��ܤ����Ϥ��Ʋ�������<br />";
			} else if ( ! checkdate($this->arrParam[$value[2]], $this->arrParam[$value[3]], $this->arrParam[$value[1]])) {
				$this->arrErr[$value[1]] = "�� " . $value[0] . "������������ޤ���<br />";
			}
		}
	}

	//���ե����å�
	// value[0] = ����̾
	// value[1] = YYYY
	// value[2] = MM
	function CHECK_DATE3($value) {						
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
									
		// ���ʤ��Ȥ�ɤ줫��Ĥ����Ϥ���Ƥ��롣
		if($this->arrParam[$value[1]] > 0 || $this->arrParam[$value[2]] > 0) {
			// ǯ�������Τɤ줫�����Ϥ���Ƥ��ʤ���
			if(!(strlen($this->arrParam[$value[1]]) > 0 && strlen($this->arrParam[$value[2]]) > 0)) {
				$this->arrErr[$value[1]] = "�� " . $value[0] . "�Ϥ��٤Ƥι��ܤ����Ϥ��Ʋ�������<br />";
			} else if ( ! checkdate($this->arrParam[$value[2]], 1, $this->arrParam[$value[1]])) {
				$this->arrErr[$value[1]] = "�� " . $value[0] . "������������ޤ���<br />";
			}
		}
	}
	
	/*-----------------------------------------------------------------*/
	/*	CHECK_SET_TERM
	/*	ǯ�������̤줿2�Ĥδ��֤�������������å������������ȴ��֤��֤�
	/*������ (����ǯ,���Ϸ�,������,��λǯ,��λ��,��λ��)
	/*������ array(������������
	/*  		��������ǯ���� (YYYYMMDD 000000)
	/*			������λǯ���� (YYYYMMDD 235959)
	/*			�������顼 ( 0 = OK, 1 = NG )
	/*-----------------------------------------------------------------*/
	// value[0] = ����̾1
	// value[1] = ����̾2
	// value[2] = start_year
	// value[3] = start_month
	// value[4] = start_day
	// value[5] = end_year
	// value[6] = end_month
	// value[7] = end_day
	function CHECK_SET_TERM ($value) {

		// ���ֻ���
		if(isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[5]])) {
			return;
		}	
		$error = 0;
		if ( (strlen($this->arrParam[$value[2]]) > 0 || strlen($this->arrParam[$value[3]]) > 0 || strlen($this->arrParam[$value[4]] ) > 0) && ! checkdate($this->arrParam[$value[3]], $this->arrParam[$value[4]], $this->arrParam[$value[2]]) ) {
			$this->arrErr[$value[2]] = "�� " . $value[0] . "�����������ꤷ�Ƥ���������<br />";
		}
		if ( (strlen($this->arrParam[$value[5]]) > 0 || strlen($this->arrParam[$value[6]]) > 0 || strlen($this->arrParam[$value[7]] ) > 0) && ! checkdate($this->arrParam[$value[6]], $this->arrParam[$value[7]], $this->arrParam[$value[5]]) ) {
			$this->arrErr[$value[5]] = "�� " . $value[1] . "�����������ꤷ�Ƥ���������<br />";
		}
		if ( (strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && strlen($this->arrParam[$value[4]] ) > 0) &&  (strlen($this->arrParam[$value[5]]) > 0 || strlen($this->arrParam[$value[6]]) > 0 || strlen($this->arrParam[$value[7]] ) > 0) ){

			$date1 = $this->arrParam[$value[2]] .sprintf("%02d", $this->arrParam[$value[3]]) .sprintf("%02d",$this->arrParam[$value[4]]) ."000000";
			$date2 = $this->arrParam[$value[5]] .sprintf("%02d", $this->arrParam[$value[6]]) .sprintf("%02d",$this->arrParam[$value[7]]) ."235959";
			
			if (($this->arrErr[$value[2]] == "" && $this->arrErr[$value[5]] == "") && $date1 > $date2) {
				$this->arrErr[$value[2]] = "�� " .$value[0]. "��" .$value[1]. "�δ��ֻ��꤬�����Ǥ���<br />";
			}
		}
	}
	
	/*-----------------------------------------------------------------*/
	/*	CHECK_SET_TERM2
	/*	ǯ���������̤줿2�Ĥδ��֤�������������å������������ȴ��֤��֤�
	/*������ (����ǯ,���Ϸ�,������,���ϻ���,����ʬ,������,
	/*        ��λǯ,��λ��,��λ��,��λ����,��λʬ,��λ��)
	/*������ array(������������
	/*  		��������ǯ���� (YYYYMMDDHHmmss)
	/*			������λǯ���� (YYYYMMDDHHmmss)
	/*			�������顼 ( 0 = OK, 1 = NG )
	/*-----------------------------------------------------------------*/
	// value[0] = ����̾1
	// value[1] = ����̾2
	// value[2] = start_year
	// value[3] = start_month
	// value[4] = start_day
	// value[5] = start_hour
	// value[6] = start_minute
	// value[7] = start_second
	// value[8] = end_year
	// value[9] = end_month
	// value[10] = end_day
	// value[11] = end_hour
	// value[12] = end_minute	
	// value[13] = end_second
	
	/*-----------------------------------------------------------------*/
	function CHECK_SET_TERM2 ($value) {

		// ���ֻ���
		if(isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[8]])) {
			return;
		}	
		$error = 0;
		if ( (strlen($this->arrParam[$value[2]]) > 0 || strlen($this->arrParam[$value[3]]) > 0 || strlen($this->arrParam[$value[4]] ) > 0 || strlen($this->arrParam[$value[5]]) > 0) && ! checkdate($this->arrParam[$value[3]], $this->arrParam[$value[4]], $this->arrParam[$value[2]]) ) {
			$this->arrErr[$value[2]] = "�� " . $value[0] . "�����������ꤷ�Ƥ���������<br />";
		}
		if ( (strlen($this->arrParam[$value[8]]) > 0 || strlen($this->arrParam[$value[9]]) > 0 || strlen($this->arrParam[$value[10]] ) > 0 || strlen($this->arrParam[$value[11]] ) > 0) && ! checkdate($this->arrParam[$value[9]], $this->arrParam[$value[10]], $this->arrParam[$value[8]]) ) {
			$this->arrErr[$value[8]] = "�� " . $value[1] . "�����������ꤷ�Ƥ���������<br />";
		}
		if ( (strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && strlen($this->arrParam[$value[4]] ) > 0 && strlen($this->arrParam[$value[5]] ) > 0) &&  (strlen($this->arrParam[$value[8]]) > 0 || strlen($this->arrParam[$value[9]]) > 0 || strlen($this->arrParam[$value[10]] ) > 0 || strlen($this->arrParam[$value[11]] ) > 0) ){

			$date1 = $this->arrParam[$value[2]] .sprintf("%02d", $this->arrParam[$value[3]]) .sprintf("%02d",$this->arrParam[$value[4]]) .sprintf("%02d",$this->arrParam[$value[5]]).sprintf("%02d",$this->arrParam[$value[6]]).sprintf("%02d",$this->arrParam[$value[7]]);
			$date2 = $this->arrParam[$value[8]] .sprintf("%02d", $this->arrParam[$value[9]]) .sprintf("%02d",$this->arrParam[$value[10]]) .sprintf("%02d",$this->arrParam[$value[11]]).sprintf("%02d",$this->arrParam[$value[12]]).sprintf("%02d",$this->arrParam[$value[13]]);
			
			if (($this->arrErr[$value[2]] == "" && $this->arrErr[$value[8]] == "") && $date1 > $date2) {
				$this->arrErr[$value[2]] = "�� " .$value[0]. "��" .$value[1]. "�δ��ֻ��꤬�����Ǥ���<br />";
			}
			if($date1 == $date2) {
				$this->arrErr[$value[2]] = "�� " .$value[0]. "��" .$value[1]. "�δ��ֻ��꤬�����Ǥ���<br />";
			}
			
		}
	}	

	/*-----------------------------------------------------------------*/
	/*	CHECK_SET_TERM3
	/*	ǯ����̤줿2�Ĥδ��֤�������������å������������ȴ��֤��֤�
	/*������ (����ǯ,���Ϸ�,��λǯ,��λ��)
	/*������ array(������������
	/*  		��������ǯ���� (YYYYMMDD 000000)
	/*			������λǯ���� (YYYYMMDD 235959)
	/*			�������顼 ( 0 = OK, 1 = NG )
	/*-----------------------------------------------------------------*/
	// value[0] = ����̾1
	// value[1] = ����̾2
	// value[2] = start_year
	// value[3] = start_month
	// value[4] = end_year
	// value[5] = end_month
	function CHECK_SET_TERM3 ($value) {

		// ���ֻ���
		if(isset($this->arrErr[$value[2]]) || isset($this->arrErr[$value[4]])) {
			return;
		}	
		$error = 0;
		if ( (strlen($this->arrParam[$value[2]]) > 0 || strlen($this->arrParam[$value[3]]) > 0) && ! checkdate($this->arrParam[$value[3]], 1, $this->arrParam[$value[2]]) ) {
			$this->arrErr[$value[2]] = "�� " . $value[0] . "�����������ꤷ�Ƥ���������<br />";
		}
		if ( (strlen($this->arrParam[$value[4]]) > 0 || strlen($this->arrParam[$value[5]]) > 0) && ! checkdate($this->arrParam[$value[5]], 1, $this->arrParam[$value[4]]) ) {
			$this->arrErr[$value[4]] = "�� " . $value[1] . "�����������ꤷ�Ƥ���������<br />";
		}
		if ( (strlen($this->arrParam[$value[2]]) > 0 && strlen($this->arrParam[$value[3]]) > 0 && (strlen($this->arrParam[$value[4]]) > 0 || strlen($this->arrParam[$value[5]]) > 0 ))) {

			$date1 = $this->arrParam[$value[2]] .sprintf("%02d", $this->arrParam[$value[3]]);
			$date2 = $this->arrParam[$value[4]] .sprintf("%02d", $this->arrParam[$value[5]]);
			
			if (($this->arrErr[$value[2]] == "" && $this->arrErr[$value[5]] == "") && $date1 > $date2) {
				$this->arrErr[$value[2]] = "�� " .$value[0]. "��" .$value[1]. "�δ��ֻ��꤬�����Ǥ���<br />";
			}
		}
	}	
	
	//�ǥ��쥯�ȥ�¸�ߥ����å�
	function DIR_CHECK ($value) {
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(!is_dir($this->arrParam[$value[1]])) {
			$this->arrErr[$value[1]] = "�� ���ꤷ��" . $value[0] . "��¸�ߤ��ޤ���<br />";
		}
	}
	
	//�ǥ��쥯�ȥ�¸�ߥ����å�
	function DOMAIN_CHECK ($value) {
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(strlen($this->arrParam[$value[1]]) > 0 && !ereg("^\.[^.]+\..+", $this->arrParam[$value[1]])) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�η����������Ǥ���<br />";
		}
	}	
	
	/*�����ӥ᡼�륢�ɥ쥹��Ƚ�ꡡ*/
	// value[0] = ����̾ value[1] = Ƚ���оݥ᡼�륢�ɥ쥹
	function MOBILE_EMAIL_CHECK( $value ){				//���᡼�륢�ɥ쥹������ɽ����Ƚ�ꤹ��
		if(isset($this->arrErr[$value[1]])) {
			return;
		}
		if(strlen($this->arrParam[$value[1]]) > 0 && !gfIsMobileMailAddress($this->arrParam[$value[1]])) {
			$this->arrErr[$value[1]] = "�� " . $value[0] . "�Ϸ������äΤ�ΤǤϤ���ޤ���<br />";
		}
	}
    /**
     * �ػ�ʸ����Υ����å�
     * value[0] = ����̾ value[1] = Ƚ���о�ʸ����
     * value[2] = ���Ϥ�ػߤ���ʸ����(����)
     * 
     * @example $objErr->doFunc(array("URL", "contents", $arrReviewDenyURL), array("PROHIBITED_STR_CHECK"));
     */ 
    function PROHIBITED_STR_CHECK( $value ) {
        if( isset($this->arrErr[$value[1]]) || empty($this->arrParam[$value[1]]) ) {
            return;
        }
        
        $targetStr     = $this->arrParam[$value[1]];
        $prohibitedStr = str_replace(array('|', '/'), array('\|', '\/'), $value[2]);
        
        $pattern = '/' . join('|', $prohibitedStr) . '/i';
        if(preg_match_all($pattern, $this->arrParam[$value[1]], $matches)) {
            $this->arrErr[$value[1]] = "�� " . $value[0] . "�����ϤǤ��ޤ���<br />";
        }
    }
}
?>
