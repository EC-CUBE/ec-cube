<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

$SC_UPLOADFILE_DIR = realpath(dirname( __FILE__));
require_once($SC_UPLOADFILE_DIR . "/../lib/gdthumb.php");	

/* ���åץ��ɥե�����������饹 */
class SC_UploadFile {
	var $temp_dir;
	var $save_dir;
	var $keyname;	// �ե�����input������name
	var $width;		// ��������
	var $height;	// �ĥ�����
	var $arrExt;	// ���ꤹ���ĥ��
	var $temp_file;	// ��¸���줿�ե�����̾
	var $save_file; // DB�����ɤ߽Ф����ե�����̾
	var $disp_name;	// ����̾
	var $size;		// ���¥�����
	var $necessary; // ɬ�ܤξ��:true
	var $image;		// �����ξ��:true
	
	// �ե�����������饹
	function SC_UploadFile($temp_dir, $save_dir) {
		$this->temp_dir = $temp_dir;
		$this->save_dir = $save_dir;
		$this->file_max = 0;
	}

	// �ե���������ɲ�
	function addFile($disp_name, $keyname, $arrExt, $size, $necessary=false, $width=0, $height=0, $image=true) {
		$this->disp_name[] = $disp_name;
		$this->keyname[] = $keyname;
		$this->width[] = $width;
		$this->height[] = $height;
		$this->arrExt[] = $arrExt;
		$this->size[] = $size;
		$this->necessary[] = $necessary;
		$this->image[] = $image;
	}
	// ����ͥ�������κ���
	function makeThumb($src_file, $width, $height) {
		// ��դ�ID��������롣
		$uniqname = date("mdHi") . "_" . uniqid("");
		
		$dst_file = $this->temp_dir . $uniqname;
		
		$objThumb = new gdthumb();
		$ret = $objThumb->Main($src_file, $width, $height, $dst_file);
		
		if($ret[0] != 1) {
			// ���顼��å�������ɽ��
			print($ret[1]);
			exit;
		}
		
		return basename($ret[1]);
	}
		
	// ���åץ��ɤ��줿�ե��������¸���롣
	function makeTempFile($keyname, $rename = true) {
		$objErr = new SC_CheckError();
		$cnt = 0;
		$arrKeyname = array_flip($this->keyname);
		
		if(!($_FILES[$keyname]['size'] > 0)) {
			$objErr->arrErr[$keyname] = "�� " . $this->disp_name[$arrKeyname[$keyname]] . "�����åץ��ɤ���Ƥ��ޤ���<br />";
		} else {
			foreach($this->keyname as $val) {
				// ���פ��������Υե�����˾������¸���롣
				if ($val == $keyname) {
					// ��ĥ�ҥ����å�
					$objErr->doFunc(array($this->disp_name[$cnt], $keyname, $this->arrExt[$cnt]), array("FILE_EXT_CHECK"));
					// �ե����륵���������å�
					$objErr->doFunc(array($this->disp_name[$cnt], $keyname, $this->size[$cnt]), array("FILE_SIZE_CHECK"));
					// ���顼���ʤ����
					if(!isset($objErr->arrErr[$keyname])) {
						// �����ե�����ξ��
						if($this->image[$cnt]) {
							$this->temp_file[$cnt] = $this->makeThumb($_FILES[$keyname]['tmp_name'], $this->width[$cnt], $this->height[$cnt]);
						// �����ե�����ʳ��ξ��
						} else {
							// ��դʥե�����̾��������롣
							if($rename) {
								$uniqname = date("mdHi") . "_" . uniqid("").".";
								$this->temp_file[$cnt] = ereg_replace("^.*\.",$uniqname, $_FILES[$keyname]['name']);
							} else {
								$this->temp_file[$cnt] = $_FILES[$keyname]['name'];	
							}
							$result  = copy($_FILES[$keyname]['tmp_name'], $this->temp_dir. "/". $this->temp_file[$cnt]);
							gfPrintLog($_FILES[$keyname]['name']." -> ".$this->temp_dir. "/". $this->temp_file[$cnt]);
						}
					}
				}
				$cnt++;
			}
		}
		return $objErr->arrErr[$keyname];
	}

	// �����������롣
	function deleteFile($keyname) {
		$objImage = new SC_Image($this->temp_dir);
		$cnt = 0;
		foreach($this->keyname as $val) {
			if ($val == $keyname) {
				// ����ե�����ξ�������롣
				if($this->temp_file[$cnt] != "") {
					$objImage->deleteImage($this->temp_file[$cnt], $this->save_dir);
				}
				$this->temp_file[$cnt] = "";
				$this->save_file[$cnt] = "";
			}
			$cnt++;
		}
	}
	
	// ����ե�����ѥ���������롣
	function getTempFilePath($keyname) {
		$cnt = 0;
		$filepath = "";
		foreach($this->keyname as $val) {
			if ($val == $keyname) {
				if($this->temp_file[$cnt] != "") {
					$filepath = $this->temp_dir . "/" . $this->temp_file[$cnt];
				}
			}
			$cnt++;
		}
		return $filepath;
	}
	
	// ����ե��������¸�ǥ��쥯�ȥ�˰ܤ�
	function moveTempFile() {
		$cnt = 0;
		$objImage = new SC_Image($this->temp_dir);
		
		foreach($this->keyname as $val) {
			if($this->temp_file[$cnt] != "") {
													
				$objImage->moveTempImage($this->temp_file[$cnt], $this->save_dir);
				// ���Ǥ���¸�ե����뤬���ä����Ϻ�����롣
				if($this->save_file[$cnt] != "" && !ereg("^sub/", $this->save_file[$cnt])) {
					$objImage->deleteImage($this->save_file[$cnt], $this->save_dir);
				}
			}
			$cnt++;
		}
	}
	
	// HIDDEN�ѤΥե�����̾������֤�
	function getHiddenFileList() {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($this->temp_file[$cnt] != "") {
				$arrRet["temp_" . $val] = $this->temp_file[$cnt];
			}
			if($this->save_file[$cnt] != "") {
				$arrRet["save_" . $val] = $this->save_file[$cnt];
			}
			$cnt++; 
		}
		return $arrRet;
	}
	
	// HIDDEN�������Ƥ����ե�����̾���������
	function setHiddenFileList($arrPOST) {
		$cnt = 0;
		foreach($this->keyname as $val) {
			$key = "temp_" . $val;
			if($arrPOST[$key] != "") {
				$this->temp_file[$cnt] = $arrPOST[$key];
			}
			$key = "save_" . $val;
			if($arrPOST[$key] != "") {
				$this->save_file[$cnt] = $arrPOST[$key];
			}
			$cnt++;
		}
	}
	
	// �ե�������Ϥ��ѤΥե��������������֤�
	function getFormFileList($temp_url, $save_url, $real_size = false) {

		$cnt = 0;
		foreach($this->keyname as $val) {
			if($this->temp_file[$cnt] != "") {
				// �ե�����ѥ������å�(�ѥ��Υ���å���/��Ϣ³���ʤ��褦�ˤ��롣)
				if(ereg("/$", $temp_url)) {
					$arrRet[$val]['filepath'] = $temp_url . $this->temp_file[$cnt];
				} else {
					$arrRet[$val]['filepath'] = $temp_url . "/" . $this->temp_file[$cnt];
				}
				$arrRet[$val]['filepath_dir'] = $this->temp_dir . $this->temp_file[$cnt];
			} elseif ($this->save_file[$cnt] != "") {
				// �ե�����ѥ������å�(�ѥ��Υ���å���/��Ϣ³���ʤ��褦�ˤ��롣)
				if(ereg("/$", $save_url)) {
					$arrRet[$val]['filepath'] = $save_url . $this->save_file[$cnt];
				} else {
					$arrRet[$val]['filepath'] = $save_url . "/" . $this->save_file[$cnt];
				}
				$arrRet[$val]['filepath_dir'] = $this->save_dir . $this->save_file[$cnt];
			}
			if($arrRet[$val]['filepath'] != "") {
				
				if($real_size){
					list($width, $height) = getimagesize($arrRet[$val]['filepath_dir']);
					// �ե����벣��
					$arrRet[$val]['width'] = $width;
					// �ե��������
					$arrRet[$val]['height'] = $height;
				}else{
					// �ե����벣��
					$arrRet[$val]['width'] = $this->width[$cnt];
					// �ե��������
					$arrRet[$val]['height'] = $this->height[$cnt];
				}
				// ɽ��̾
				$arrRet[$val]['disp_name'] = $this->disp_name[$cnt];
			}
			$cnt++;
		}
		return $arrRet;
	}
	
	// DB��¸�ѤΥե�����̾������֤�
	function getDBFileList() {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($this->temp_file[$cnt] != "") {
				$arrRet[$val] = $this->temp_file[$cnt];
			} else  {
				$arrRet[$val] = $this->save_file[$cnt];
			}
			$cnt++;
		}
		return $arrRet;
	}
	
	// DB����¸���줿�ե�����̾����򥻥åȤ���
	function setDBFileList($arrVal) {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($arrVal[$val] != "") {
				$this->save_file[$cnt] = $arrVal[$val];
			}
			$cnt++; 
		}
	}
	
	// �����򥻥åȤ���
	function setDBImageList($arrVal) {
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($arrVal[$val] != "" && $val == 'tv_products_image') {
				$this->save_file[$cnt] = $arrVal[$val];
			}
			$cnt++; 
		}
	}
	
	// DB��Υե�����������׵᤬���ä��ե�����������롣 
	function deleteDBFile($arrVal) {
		$objImage = new SC_Image($this->temp_dir);
		$cnt = 0;
		foreach($this->keyname as $val) {
			if($arrVal[$val] != "") {
				if($this->save_file[$cnt] == "" && !ereg("^sub/", $arrVal[$val])) {
					$objImage->deleteImage($arrVal[$val], $this->save_dir);
				}
			}
			$cnt++; 
		}
	}
	
	// ɬ��Ƚ��
	function checkEXISTS($keyname = "") {
		$cnt = 0;
		$arrRet = array();
		foreach($this->keyname as $val) {
			if($val == $keyname || $keyname == "") {
				// ɬ�ܤǤ���Х��顼�����å�
				if ($this->necessary[$cnt] == true) {
					if($this->save_file[$cnt] == "" && $this->temp_file[$cnt] == "") {
						$arrRet[$val] = "�� " . $this->disp_name[$cnt] . "�����åץ��ɤ���Ƥ��ޤ���<br>";
					}
				}
			}
			$cnt++;
		}
		return $arrRet;
	}
		
	// ����Ψ����ꤷ�Ʋ�����¸
	function saveResizeImage($keyname, $to_w, $to_h) {
		$path = "";
		
		// keyname��ź�եե���������
		$arrImageKey = array_flip($this->keyname);
		$file = $this->temp_file[$arrImageKey[$keyname]];
		$filepath = $this->temp_dir . $file;
		
		$path = $this->makeThumb($filepath, $to_w, $to_h);
		
		// �ե�����̾�����֤�
		return basename($path);
	}
}
?>