<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

$SC_UPLOADFILE_DIR = realpath(dirname( __FILE__));
require_once($SC_UPLOADFILE_DIR . "/../lib/gdthumb.php");
require_once($SC_UPLOADFILE_DIR . "/../include/ftp.php");

/* ���åץ��ɥե�����������饹 */
class SC_UploadFile {
    var $temp_dir;                  // ����ե�������¸�ǥ��쥯�ȥ�
    var $save_dir;                  // �ե�������¸�ǥ��쥯��
    var $ftp_temp_dir;              // FTP�����ǥ��쥯�ȥ�(���ʬ��������)
    var $ftp_save_dir;              // FTP��ե�������¸�ǥ��쥯�ȥ�(���ʬ��������)
    var $multi_web_server_mode;     // ���ʬ���ե饰(���ʬ��������)
    var $keyname;                   // �ե�����input������name
    var $width;                     // ��������
    var $height;                    // �ĥ�����
    var $arrExt;                    // ���ꤹ���ĥ��
    var $temp_file;                 // ��¸���줿�ե�����̾
    var $save_file;                 // DB�����ɤ߽Ф����ե�����̾
    var $disp_name;                 // ����̾
    var $size;                      // ���¥�����
    var $necessary;                 // ɬ�ܤξ��:true
    var $image;                     // �����ξ��:true

    // �ե�����������饹
    function SC_UploadFile($temp_dir, $save_dir, $ftp_temp_dir = "", $ftp_save_dir = "", $multi_web_server_mode = false) {
        $this->temp_dir = $temp_dir;
        $this->save_dir = $save_dir;
        $this->ftp_temp_dir = $ftp_temp_dir;
        $this->ftp_save_dir = $ftp_save_dir;
        $this->multi_web_server_mode = $multi_web_server_mode;
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
        
        $objThumb = new gdthumb();
        
        // WEB���������ʬ���Ķ��ξ��
        if($this->multi_web_server_mode === true) {
            
            // FTP�ѥե���������Ǽ�ѥǥ��쥯�ȥ����
            $ftp_temp_dir = $this->makeFtpTempDir($this->temp_dir);
            $dst_file = $ftp_temp_dir . $uniqname;
            $ret = $objThumb->Main($src_file, $width, $height, $dst_file);
            $this->ftpMoveFile($this->ftp_temp_dir . basename($ret[1]), $ret[1], true);
        } else {
            $dst_file = $this->temp_dir . $uniqname;
            $ret = $objThumb->Main($src_file, $width, $height, $dst_file);          
        }
        
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
                    // ���ʬ�����Ϥ��٤ƤΥ����Ф�����
                    if($this->multi_web_server_mode === true) {
                        $this->ftpDeleteFile($this->ftp_temp_dir . $this->temp_file[$cnt]);
                    } else {
                        $objImage->deleteImage($this->temp_file[$cnt], $this->save_dir);
                    }
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
                // ���ʬ�����Ϥ��٤ƤΥ����Фǰ�ư��¹�
                if($this->multi_web_server_mode === true) {
                    $dist_path = $this->ftp_save_dir . $this->temp_file[$cnt];
                    $src_path = $this->temp_dir . $this->temp_file[$cnt];
                    $this->ftpMoveFile($dist_path, $src_path);
                } else {
                    $objImage->moveTempImage($this->temp_file[$cnt], $this->save_dir);
                }
                
                // ���Ǥ���¸�ե����뤬���ä����Ϻ�����롣
                if($this->save_file[$cnt] != "" && !ereg("^sub/", $this->save_file[$cnt])) {
                    // ���ʬ�����Ϥ��٤ƤΥ����Фǥե�������
                    if($this->multi_web_server_mode === true) {
                        $this->ftpDeleteFile($this->ftp_save_dir . $this->save_file[$cnt]);
                    } else {
                        $objImage->deleteImage($this->save_file[$cnt], $this->save_dir);
                    }
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
                $arrRet[$val]['real_filepath'] = $this->temp_dir . $this->temp_file[$cnt];
            } elseif ($this->save_file[$cnt] != "") {
                // �ե�����ѥ������å�(�ѥ��Υ���å���/��Ϣ³���ʤ��褦�ˤ��롣)
                if(ereg("/$", $save_url)) {
                    $arrRet[$val]['filepath'] = $save_url . $this->save_file[$cnt];
                } else {
                    $arrRet[$val]['filepath'] = $save_url . "/" . $this->save_file[$cnt];
                }
                $arrRet[$val]['real_filepath'] = $this->save_dir . $this->save_file[$cnt];
            }
            if($arrRet[$val]['filepath'] != "") {
                if($real_size){
                    if(is_file($arrRet[$val]['real_filepath'])) {
                        list($width, $height) = getimagesize($arrRet[$val]['real_filepath']);
                    }
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
                    
                    // ���ʬ�����Ϥ��٤ƤΥ����Фǥե�������
                    if($this->multi_web_server_mode === true) {
                        $this->ftpDeleteFile($this->ftp_save_dir . $arrVal[$val]);
                    } else {
                        $objImage->deleteImage($arrVal[$val], $this->save_dir);
                    }
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

    /**         
     * �ե���������Ƥ�WEB�����Фإ��ԡ�
     *
     * @param string $dst_path ���ԡ���ե�����ѥ�(���Хѥ�)
     * @param string $src_path ���ԡ����ե�����ѥ�(���Хѥ�)
     * @param boolean $del_flag ��ư�ե������������ե饰
     * @return void
     */
    function ftpMoveFile($dst_path, $src_path, $del_flag = false) {
        global $arrWEB_SERVERS;

        // ���ƤΥ����Ф˥ե�����򥳥ԡ�����
        foreach($arrWEB_SERVERS as $array) {
            sfFtpCopy($array['host'], $array['user'], $array['pass'], $dst_path, $src_path);
        }
        // ��ư��ϥե��������
        if($del_flag === true) {
            @unlink($src_path);
        }
    }

    /**         
     * �ե���������Ƥ�WEB�����о夫����
     *
     * @param string $dst_path ���ԡ���ե�����ѥ�(���Хѥ�)
     * @return void
     */
    function ftpDeleteFile($dst_path) {
        global $arrWEB_SERVERS;

        // ���ƤΥ����Ф˥ե�����򥳥ԡ�����
        foreach($arrWEB_SERVERS as $array) {
            sfFtpDelete($array['host'], $array['user'], $array['pass'], $dst_path);
        }
    }


    /**
     * FTP�ѥե���������Ǽ�ǥ��쥯�ȥ����
     *
     * @param string $dir �����ǥ��쥯�ȥ�
     * @return string $ftp_temp_dir ���������Ǽ�ǥ��쥯�ȥ�ѥ�
     */
    function makeFtpTempDir($dir) {
        // FTP�ѥե���������Ǽ�ѥǥ��쥯�ȥ�
        $ftp_temp_dir = $this->temp_dir . "ftp_temp/";
        // �ǥ��쥯�ȥ꤬¸�ߤ��ʤ��ä������
        if(!file_exists($ftp_temp_dir)) {
            mkdir($ftp_temp_dir);
            chmod($ftp_temp_dir, 0777);
        }
        
        return $ftp_temp_dir;
    }
}
?>