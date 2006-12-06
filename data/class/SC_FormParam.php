<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* �ѥ�᡼���������饹 */
class SC_FormParam {

	var $param;	
	var $disp_name;
	var $keyname;
	var $length;
	var $convert;
	var $arrCheck;
	var $default;	// �������Ϥ���Ƥ��ʤ��Ȥ���ɽ��������
	var $input_db;	// DB�ˤ��Τޤ�������ǽ���ݤ�
	var $html_disp_name;
	
	// ���󥹥ȥ饯��
	function SC_FormParam() {
		$this->check_dir = IMAGE_SAVE_DIR;
		$this->disp_name = array();
		$this->keyname = array();
		$this->length = array();
		$this->convert = array();
		$this->arrCheck = array();
		$this->default = array();
		$this->input_db = array();
	}
	
	// �ѥ�᡼�����ɲ�
	function addParam($disp_name, $keyname, $length="", $convert="", $arrCheck=array(), $default="", $input_db="true") {
		$this->disp_name[] = $disp_name;
		$this->keyname[] = $keyname;
		$this->length[] = $length;
		$this->convert[] = $convert;
		$this->arrCheck[] = $arrCheck;
		$this->default[] = $default;
		$this->input_db[] = $input_db;
	}
	
	// �ѥ�᡼��������
	// $arrVal	:$arrVal['keyname']�������������פ��������Υ��󥹥��󥹤˳�Ǽ����
	// $seq		:true�ξ�硢$arrVal[0]������������Ͽ��˥��󥹥��󥹤˳�Ǽ����
	function setParam($arrVal, $seq = false) {
		$cnt = 0;
		if(!$seq){
			foreach($this->keyname as $val) {
				if(isset($arrVal[$val])) {
					$this->setValue($val, $arrVal[$val]);
				}
			}		
		} else {
			foreach($this->keyname as $val) {
				$this->param[$cnt] = $arrVal[$cnt];
				$cnt++;
			}
		}
	}
	
	// ����ɽ���ѥ����ȥ�����
	function setHtmlDispNameArray() {
		$cnt = 0;
		foreach($this->keyname as $val) {
			$find = false;
			foreach($this->arrCheck[$cnt] as $val) {
				if($val == "EXIST_CHECK") {
					$find = true;
				}
			}
			
			if($find) {
				$this->html_disp_name[$cnt] = $this->disp_name[$cnt] . "<span class='red'>(�� ɬ��)</span>";					
			} else {
				$this->html_disp_name[$cnt] = $this->disp_name[$cnt];
			}								
			$cnt++;
		}
	}
	
	// ����ɽ���ѥ����ȥ����
	function getHtmlDispNameArray() {
		return $this->html_disp_name;
	}
		
	// ʣ����ѥ�᡼���μ���
	function setParamList($arrVal, $keyname) {
		// DB�η����������롣
		$count = count($arrVal);
		$no = 1;
		for($cnt = 0; $cnt < $count; $cnt++) {
			$key = $keyname.$no;
			if($arrVal[$cnt][$keyname] != "") {
				$this->setValue($key, $arrVal[$cnt][$keyname]);
			}
			$no++;
		}
	}
	
	function setDBDate($db_date, $year_key = 'year', $month_key = 'month', $day_key = 'day') {
		list($y, $m, $d) = split("[- ]", $db_date);
		$this->setValue($year_key, $y);
		$this->setValue($month_key, $m);
		$this->setValue($day_key, $d);
	}
	
	// �������б������ͤ򥻥åȤ��롣
	function setValue($key, $param) {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($val == $key) {
				$this->param[$cnt] = $param;
				break;
			}
			$cnt++;
		}
	}

	function toLower($key) {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($val == $key) {
				$this->param[$cnt] = strtolower($this->param[$cnt]);
				break;
			}
			$cnt++;
		}
	}
	
	// ���顼�����å�
	function checkError($br = true, $keyname = "") {
		// Ϣ������μ���
		$arrRet = $this->getHashArray($keyname);
		$objErr = new SC_CheckError($arrRet);
		$cnt = 0;
		foreach($this->keyname as $val) {
			foreach($this->arrCheck[$cnt] as $func) {
				switch($func) {
				case 'EXIST_CHECK':
				case 'NUM_CHECK':
				case 'EMAIL_CHECK':
				case 'EMAIL_CHAR_CHECK':
				case 'ALNUM_CHECK':
				case 'KANA_CHECK':
				case 'URL_CHECK':
				case 'SPTAB_CHECK':
				case 'ZERO_CHECK':
				case 'ALPHA_CHECK':
				case 'ZERO_START':
				case 'FIND_FILE':
				case 'NO_SPTAB':
				case 'DIR_CHECK':
				case 'DOMAIN_CHECK':
				case 'FILE_NAME_CHECK':
				
					if(!is_array($this->param[$cnt])) {
						$objErr->doFunc(array($this->disp_name[$cnt], $val), array($func));
					} else {
						$max = count($this->param[$cnt]);
						for($i = 0; $i < $max; $i++) {
							$objSubErr = new SC_CheckError($this->param[$cnt]);
							$objSubErr->doFunc(array($this->disp_name[$cnt], $i), array($func));
							if(count($objSubErr->arrErr) > 0) {
								foreach($objSubErr->arrErr as $mess) {
									if($mess != "") {
										$objErr->arrErr[$val] = $mess;
									}
								}
							}
						}
					}
					break;
				case 'MAX_LENGTH_CHECK':
				case 'NUM_COUNT_CHECK':
					if(!is_array($this->param[$cnt])) {
						$objErr->doFunc(array($this->disp_name[$cnt], $val, $this->length[$cnt]), array($func));
					} else {
						$max = count($this->param[$cnt]);
						for($i = 0; $i < $max; $i++) {
							$objSubErr = new SC_CheckError($this->param[$cnt]);
							$objSubErr->doFunc(array($this->disp_name[$cnt], $i, $this->length[$cnt]), array($func));
							if(count($objSubErr->arrErr) > 0) {
								foreach($objSubErr->arrErr as $mess) {
									if($mess != "") {
										$objErr->arrErr[$val] = $mess;
									}
								}
							}
						}
					}
					break;
				// ��ʸ�����Ѵ�
				case 'CHANGE_LOWER':
					$this->param[$cnt] = strtolower($this->param[$cnt]);
					break;
				// �ե������¸�ߥ����å�
				case 'FILE_EXISTS':
					if($this->param[$cnt] != "" && !file_exists($this->check_dir . $this->param[$cnt])) {
						$objErr->arrErr[$val] = "�� " . $this->disp_name[$cnt] . "�Υե����뤬¸�ߤ��ޤ���<br>";					
					}
					break;
				default:
					$objErr->arrErr[$val] = "���������顼�����å�����($func)�ˤ��б����Ƥ��ޤ��󡡢��� <br>";
					break;
				}
			}
			
			if (isset($objErr->arrErr[$val]) && !$br) {
				$objErr->arrErr[$val] = ereg_replace("<br>$", "", $objErr->arrErr[$val]);
			}
			$cnt++;
		}
		return $objErr->arrErr;
	}
	
	// ����ʸ�����Ѵ�
	function convParam() {
		/*
		 *	ʸ������Ѵ�
		 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
		 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
		 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
		 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
		 *  a :  �����ѡױѻ����Ⱦ�ѡױѻ����Ѵ�
		 */
		$cnt = 0;
		foreach ($this->keyname as $val) {
			if(!is_array($this->param[$cnt])) {
				if($this->convert[$cnt] != "") {
					$this->param[$cnt] = mb_convert_kana($this->param[$cnt] ,$this->convert[$cnt]);
				}
			} else {
				$max = count($this->param[$cnt]);
				for($i = 0; $i < $max; $i++) {
					if($this->convert[$cnt] != "") {
						$this->param[$cnt][$i] = mb_convert_kana($this->param[$cnt][$i] ,$this->convert[$cnt]);
					}
				}
			}
			$cnt++;
		}
	}
	
	// Ϣ������κ���
	function getHashArray($keyname = "") {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($keyname == "" || $keyname == $val) {
				$arrRet[$val] = $this->param[$cnt];
				$cnt++;
			}
		}
		return $arrRet;
	}
	
	// DB��Ǽ������κ���
	function getDbArray() {
		$cnt = 0;
		foreach ($this->keyname as $val) {
			if ($this->input_db[$cnt]) {
				$arrRet[$val] = $this->param[$cnt];
			}
			$cnt++;
		}
		return $arrRet;
	}
	
	// ����νĲ��������ؤ����֤�
	function getSwapArray($arrKey) {
		foreach($arrKey as $keyname) {
			$arrVal = $this->getValue($keyname);
			$max = count($arrVal);
			for($i = 0; $i < $max; $i++) {
				$arrRet[$i][$keyname] = $arrVal[$i];
			}
		}
		return $arrRet;
	}
	
	// ����̾�����μ���
	function getTitleArray() {
		return $this->disp_name;
	}
	
	// ���ܿ����֤�
	function getCount() {
		$count = count($this->keyname);
		return $count;
	}
	
	// �ե�������Ϥ��ѤΥѥ�᡼�����֤�
	function getFormParamList() {
		$cnt = 0;
		foreach($this->keyname as $val) {
			// ����̾
			$arrRet[$val]['keyname'] = $this->keyname[$cnt];
			// ʸ��������
			$arrRet[$val]['length'] = $this->length[$cnt];
			// ������
			$arrRet[$val]['value'] = $this->param[$cnt];
			
			if($this->default[$cnt] != "" && $this->param[$cnt] == "") {
				$arrRet[$val]['value'] = $this->default[$cnt];
			}
					
			$cnt++;
		}
		return $arrRet;
	}
	
	// ����̾�ΰ������֤�
	function getKeyList() {
		foreach($this->keyname as $val) {
			$arrRet[] = $val;
		}
		return $arrRet;
	}
	
	// ����̾�Ȱ��פ����ͤ��֤�
	function getValue($keyname) {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($val == $keyname) {
				$ret = $this->param[$cnt];
				break;
			}
			$cnt++;
		}
		return $ret;
	}
	
	function splitCheckBoxes($keyname) {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($val == $keyname) {
				$this->param[$cnt] = sfSplitCheckBoxes($this->param[$cnt]);
			}
			$cnt++;
		}
	}
	
	function splitParamCheckBoxes($keyname) {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($val == $keyname) {
				if(!is_array($this->param[$cnt])) {
					$this->param[$cnt] = split("-", $this->param[$cnt]);
				}
			}
			$cnt++;
		}
	}
}
?>