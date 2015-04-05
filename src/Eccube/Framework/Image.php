<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Util\GcUtils;

//---- アップロードファイル加工クラス(thumb.phpとセットで使用する)
class Image
{
    public $tmp_dir;

    public function __construct($tmp_dir)
    {
        // ヘッダファイル読込
        $this->tmp_dir = rtrim($tmp_dir, '/') . '/';
    }

    //--- 一時ファイル生成(サムネイル画像生成用)
    public function makeTempImage($keyname, $max_width, $max_height)
    {
        // 一意なIDを取得する。
        $mainname = uniqid('').'.';
        // 拡張子以外を置き換える。
        $newFileName = preg_replace("/^.*\./", $mainname, $_FILES[$keyname]['name']);
        $result  = $this->MakeThumb($_FILES[$keyname]['tmp_name'], $this->tmp_dir, $max_width, $max_height, $newFileName);
        GcUtils::gfDebugLog($result);

        return $newFileName;
    }

    //--- ファイルを指定保存DIRへ移動
    public function moveTempImage($filename, $save_dir)
    {
        // コピー元ファイル、コピー先ディレクトリが存在する場合にのみ実行する
        $from_path = $this->tmp_dir.$filename;
        $to_path = $save_dir.'/'.$filename;
        if (file_exists($from_path) && file_exists($save_dir)) {
            if (copy($from_path, $to_path)) {
                unlink($from_path);
            }
        } else {
            GcUtils::gfDebugLog($from_path.'->'.$to_path.'のcopyに失敗しました。');
        }
    }

    //---- 指定ファイルを削除
    public function deleteImage($filename, $dir)
    {
        if (file_exists($dir.'/'.$filename)) {
            unlink($dir.'/'.$filename);
        }
    }

    /**
     * 指定サイズで画像を出力する.
     *
     * @param string  $FromImgPath ファイル名までのパス
     * @param string  $ToImgPath   出力先パス
     * @param integer $tmpMW       最大横幅
     * @param integer $tmpMH       最大縦幅
     * @param integer $newFileName 新ファイル名
     * @param array 新ファイル名を格納した配列
     */
    public function MakeThumb($FromImgPath, $ToImgPath, $tmpMW, $tmpMH, $newFileName = '')
    {
        // 画像の最大横幅（単位：ピクセル）
        $ThmMaxWidth = LARGE_IMAGE_WIDTH;

        // 画像の最大縦幅（単位：ピクセル）
        $ThmMaxHeight = LARGE_IMAGE_HEIGHT;

        //拡張子取得
        $array_ext = explode('.', $FromImgPath);
        $ext = $array_ext[count($array_ext) - 1];

        $MW = $ThmMaxWidth;
        if ($tmpMW) $MW = $tmpMW; // $MWに最大横幅セット

        $MH = $ThmMaxHeight;
        if ($tmpMH) $MH = $tmpMH; // $MHに最大縦幅セット

        if (empty($FromImgPath) || empty($ToImgPath)) {
            return array(0, '出力元画像パス、または出力先フォルダが指定されていません。');
        }

        if (!file_exists($FromImgPath)) {
            return array(0, '出力元画像が見つかりません。');
        }

        $size = @GetImageSize($FromImgPath);
        $re_size = $size;

        // 画像の種類が不明 or swf
        if (!$size[2] || $size[2] > 3) {
            return array(0, '画像形式がサポートされていません。');
        }

        //アスペクト比固定処理
        $tmp_w = $size[0] / $MW;

        if ($MH != 0) {
            $tmp_h = $size[1] / $MH;
        }

        if ($tmp_w > 1 || $tmp_h > 1) {
            if ($MH == 0) {
                if ($tmp_w > 1) {
                    $re_size[0] = $MW;
                    $re_size[1] = $size[1] * $MW / $size[0];
                }
            } else {
                if ($tmp_w > $tmp_h) {
                    $re_size[0] = $MW;
                    $re_size[1] = $size[1] * $MW / $size[0];
                } else {
                    $re_size[1] = $MH;
                    $re_size[0] = $size[0] * $MH / $size[1];
                }
            }
        }

        // サムネイル画像ファイル名作成処理
        $tmp = array_pop(explode('/', $FromImgPath)); // /の一番最後を切り出し
        $FromFileName = array_shift(explode('.', $tmp)); // .で区切られた部分を切り出し
        $ToFile = $FromFileName; // 拡張子以外の部分までを作成

        $ImgNew = imagecreatetruecolor($re_size[0], $re_size[1]);

        switch ($size[2]) {
            case '1': //gif形式
                if ($tmp_w <= 1 && $tmp_h <= 1) {
                    if ($newFileName) {
                        $ToFile = $newFileName;
                    } elseif ($ext) {
                        $ToFile .= '.' . $ext;
                    } else {
                        $ToFile .= '.gif';
                    }
                    if (!@copy($FromImgPath, $ToImgPath.$ToFile)) { // エラー処理

                        return array(0, 'ファイルのコピーに失敗しました。');
                    }
                    ImageDestroy($ImgNew);

                    return array(1, $ToFile);
                }

                ImageColorAllocate($ImgNew, 255, 235, 214); //背景色
                $black = ImageColorAllocate($ImgNew, 0, 0, 0);
                $red = ImageColorAllocate($ImgNew, 255, 0, 0);
                Imagestring($ImgNew, 4, 5, 5, "GIF $size[0]x$size[1]", $red);
                ImageRectangle($ImgNew, 0, 0, ($re_size[0]-1), ($re_size[1]-1), $black);

                if ($newFileName) {
                    $ToFile = $newFileName;
                } elseif ($ext) {
                    $ToFile .= '.' . $ext;
                } else {
                    $ToFile .= '.png';
                }
                $TmpPath = $ToImgPath.$ToFile;
                @Imagepng($ImgNew, $TmpPath);
                // 画像が作成されていない場合
                if (!@file_exists($TmpPath)) {
                    return array(0, '画像の出力に失敗しました。');
                }
                ImageDestroy($ImgNew);

                return array(1, $ToFile);

            case '2': //jpg形式
                $ImgDefault = ImageCreateFromJpeg($FromImgPath);
                //ImageCopyResized($ImgNew, $ImgDefault, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);

                if ($re_size[0] != $size[0] || $re_size[0] != $size[0]) {
                    ImageCopyResampled($ImgNew, $ImgDefault, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);
                }

                GcUtils::gfDebugLog($size);
                GcUtils::gfDebugLog($re_size);

                if ($newFileName) {
                    $ToFile = $newFileName;
                } elseif ($ext) {
                    $ToFile .= '.' . $ext;
                } else {
                    $ToFile .= '.jpg';
                }
                $TmpPath = $ToImgPath.$ToFile;
                @ImageJpeg($ImgNew, $TmpPath);
                // 画像が作成されていない場合
                if (!@file_exists($TmpPath)) {
                    return array(0, "画像の出力に失敗しました。<br>${ImgNew}<br>${TmpPath}");
                }
                $RetVal = $ToFile;
                break;

            case '3': //png形式
                $ImgDefault = ImageCreateFromPNG($FromImgPath);
                //ImageCopyResized($ImgNew, $ImgDefault, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);
                ImageCopyResampled($ImgNew, $ImgDefault, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);

                if ($newFileName) {
                    $ToFile = $newFileName;
                } elseif ($ext) {
                    $ToFile .= '.' . $ext;
                } else {
                    $ToFile .= '.png';
                }
                $TmpPath = $ToImgPath.$ToFile;
                @ImagePNG($ImgNew, $TmpPath);
                // 画像が作成されていない場合
                if (!@file_exists($TmpPath)) {
                    return array(0, '画像の出力に失敗しました。');
                }
                $RetVal = $ToFile;
                break;
        }

        ImageDestroy($ImgDefault);
        ImageDestroy($ImgNew);

        return array(1, $RetVal);
    }
}
