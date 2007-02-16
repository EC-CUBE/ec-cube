<?php
/*
 * 画像ファイルの変換を行う
 *
 *
 *
 */
class ImageConverter {
    var $outputImageDir;         // 変換後の画像の保存先
    var $outputImageType;        // 変換後の画像の形式
    var $outputImageWidth;       // 変換後の画像の横幅
    var $outputImageHeight;      // 変換後の画像の高さ
    //var $outputImageFileSize;    // 変換後の画像のファイルサイズ

    // コンストラクタ
    function ImageConverter(){
        $this->outputImageDir      = realpath(realpath(dirname( __FILE__)));
        $this->outputImageType     = 'jpeg';
        $this->outputImageWidth    = 100;
        $this->outputImageHeight   = NULL;
        //$this->outputImageFileSize = NULL;
    }

    // 変換実行
    function execute($inputImagePath) {
        // 前処理
        $filestat         = @stat($inputImagePath);
        $imagesize        = getimagesize($inputImagePath);
        $inputImageWidth  = $imagesize[0];
        $inputImageHeight = $imagesize[1];
        $inputImageType   = $imagesize[2];
        $outputImageName  = base64_encode($inputImagePath . '_' . $this->outputImageWidth . '_' . $filestat['mtime']) . '.' . $this->outputImageType;
        $outputImagePath  = $this->outputImageDir . '/' . $outputImageName;

$fp = fopen('/tmp/rebelt', 'a'); fwrite($fp, "*".count($imagesize)."\n"); fclose($fp);

        // 変換後の画像の高さが指定されていない場合、変換後の画像の横幅から求める
        if (is_null($this->outputImageHeight)) {
            $this->outputImageHeight = $inputImageHeight * ($this->outputImageWidth / $inputImageWidth);
        }

        // ファイルが存在するか確認し、存在しない場合のみ作成する
        if (file_exists($outputImagePath)) {
            $info['convert'] = FALSE;
        } else {
            // 元ファイル作成
            switch($inputImageType)
            {
                case 1:
                    // gif
                    $tempImage = imagecreatefromgif ($inputImagePath);
                    break;
                case 2:
                    // jpeg
                    $tempImage = imagecreatefromjpeg($inputImagePath);
                    break;
                case 3:
                    // png
                    $tempImage = imagecreatefrompng($inputImagePath);
                    break;
                case 6:
                    // bmp
                    $tempImage = imagecreatefromwbmp($inputImagePath);
                    break;
            }

            if (!$tempImage) {
                return false;
            }

            // 空ファイル作成
            $outputImage = ImageCreateTruecolor($this->outputImageWidth, $this->outputImageHeight);
            ImageCopyResampled($outputImage, $tempImage, 0, 0, 0, 0, $this->outputImageWidth, $this->outputImageHeight, $inputImageWidth, $inputImageHeight);

            // ファイル出力
            switch($this->outputImageType)
            {
                case 1:
                case 'gif':
                    imagegif($outputImage, $outputImagePath);
                    break;
                default:
                case 2:
                case 'jpg':
                case 'jpeg':
                    imagejpeg($outputImage, $outputImagePath);
                    break;
                case 3:
                case 'png':
                    imagepng($outputImage, $outputImagePath);
                    break;
                case 6:
                case 'bmp':
                    imagewbmp($outputImage, $outputImagePath);
                    break;
            }

            // メモリ開放
            imagedestroy($tempImage);
            imagedestroy($outputImage);

            $info['convert'] = TRUE;
        }

        // return
        /*
        $info['inputImageWidth']   = $inputImageWidth;
        $info['inputImageHeight']  = $inputImageHeight;
        $info['inputImageType']    = $inputImageType;
        $info['inputImagePath']    = $inputImagePath;
        $info['outputImageWidth']  = $this->outputImageWidth;
        $info['outputImageHeight'] = $this->outputImageHeight;
        $info['outputImageType']   = $this->outputImageType;
        */
        $info['outputImagePath']  = $outputImagePath;
        $info['outputImageName']  = $outputImageName;
        return $info;

    }

    // Setter
    function setOutputDir($outputDir)   { $this->outputImageDir    = $outputDir;   }
    function setImageType($imageType)   { $this->outputImageType   = $imageType;   }
    function setImageWidth($imageWidth) { $this->outputImageWidth  = $imageWidth;  }
    // function setImageHeight($imageHeight) { $this->outputImageHeight = $imageHeight; }

    // Getter
    function getOutputDir()   { return $this->outputDir;         }
    function getImageType()   { return $this->outputImageType;   }
    function getImageWidth()  { return $this->outputImageWidth;  }
    function getImageHeight() { return $this->outputImageHeight; }

    /*
     * PrivateMethod
    **/
    function beforeExecute() {
    }
}
?>
