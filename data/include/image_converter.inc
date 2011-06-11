<?php
/**
 * 画像ファイルの変換を行う
 */
class ImageConverter {
    var $outputImageDir;         // 変換後の画像の保存先
    var $outputImageType;        // 変換後の画像の形式
    var $outputImageWidth;       // 変換後の画像の横幅
    var $outputImageHeight;      // 変換後の画像の高さ
    var $outputImageFileSize;    // 変換後の画像のファイルサイズ

    // コンストラクタ
    function ImageConverter() {
        $this->outputImageDir    = realpath(realpath(dirname(__FILE__)));
        $this->outputImageType   = 'jpg';
        $this->outputImageWidth  = 320;
        $this->outputImageHeight = NULL;
        $this->outputFileSize    = 20000;
    }

    // 変換実行
    function execute($inputImagePath) {
        // 前処理
        $filestat         = @stat($inputImagePath);
        $imagesize        = getimagesize($inputImagePath);
        $inputImageWidth  = $imagesize[0];
        $inputImageHeight = $imagesize[1];
        $inputImageType   = $imagesize[2];

        // GIF 画像は縮小後も GIF 画像で出力する。旧機種対応などで、機種用の画像形式に変換したい場合、この処理は削除する。
        if ($inputImageType == IMAGETYPE_GIF) {
            $this->outputImageType = 'gif';
        }

        $outputImageName  = sha1($inputImagePath . '_' . $this->outputImageWidth . '_' . $this->outputFileSize . '_' . $filestat['mtime']) . '.' . $this->outputImageType;
        $outputImagePath  = $this->outputImageDir . '/' . $outputImageName;

        if (is_null($this->outputImageHeight)) {
            $height_was_null = TRUE;
            $this->outputImageHeight = $inputImageHeight * ($this->outputImageWidth / $inputImageWidth);
        } else {
            $height_was_null = FALSE;
        }
        if ($inputImageWidth <= $this->outputImageWidth) {
            if ($inputImageHeight <= $this->outputImageHeight) {
                $this->outputImageWidth  = $inputImageWidth;
                $this->outputImageHeight = $inputImageHeight;
            } else {
                $this->outputImageWidth = $inputImageWidth * ($this->outputImageHeight / $inputImageHeight);
            }
        } else {
            if ($inputImageHeight <= $this->outputImageHeight) {
                $this->outputImageHeight = $inputImageHeight * ($this->outputImageWidth / $inputImageWidth);
            } else {
                if ($this->outputImageWidth / $inputImageWidth < $this->outputImageHeight / $inputImageHeight) {
                    $this->outputImageHeight = $inputImageHeight * ($this->outputImageWidth / $inputImageWidth);
                } else {
                    $this->outputImageWidth = $inputImageWidth * ($this->outputImageHeight / $inputImageHeight);
                }
            }
        }

        // ファイルが存在するか確認し、存在しない場合のみ作成する
        if (file_exists($outputImagePath)) {
            $info['convert'] = FALSE;
        } else {
            // 元ファイル作成
            switch($inputImageType) {
                case IMAGETYPE_GIF:
                    $tempImage = imagecreatefromgif($inputImagePath);
                    $arrTransparentColor = $this->getTransparentColor($tempImage);
                    if (!empty($arrTransparentColor)) {
                        imagecolortransparent($tempImage, $arrTransparentColor);
                    }
                    break;

                case IMAGETYPE_JPEG:
                    $tempImage = imagecreatefromjpeg($inputImagePath);
                    break;

                case IMAGETYPE_PNG:
                    $tempImage = imagecreatefrompng($inputImagePath);
                    break;

                case IMAGETYPE_WBMP:
                    $tempImage = imagecreatefromwbmp($inputImagePath);
                    break;
            }

            if (!$tempImage) {
                return false;
            }

            $scale = 1.0;
            $outputImagePathTemp = $outputImagePath . '.tmp-' . rand();
            do {
                // 空ファイル作成
                if ($this->outputImageType == 'gif') {
                    // 縮小時のノイズ防止のためインデックスカラーで処理する。特に透過色を扱う上で重要。
                    $outputImage = ImageCreate($this->outputImageWidth * $scale, $this->outputImageHeight * $scale);
                } else {
                    $outputImage = ImageCreateTruecolor($this->outputImageWidth * $scale, $this->outputImageHeight * $scale);
                }

                ImageCopyResampled($outputImage, $tempImage, 0, 0, 0, 0, $this->outputImageWidth * $scale, $this->outputImageHeight * $scale, $inputImageWidth, $inputImageHeight);

                // ファイル出力

                @unlink($outputImagePathTemp);

                switch ($this->outputImageType) {
                    case 'gif':
                        if (!empty($arrTransparentColor)) {
                            $transparent_color_id = imagecolorexact($outputImage, $arrTransparentColor['red'], $arrTransparentColor['green'], $arrTransparentColor['blue']);
                            imagecolortransparent($outputImage, $transparent_color_id);
                        }
                        imagegif($outputImage, $outputImagePathTemp);
                        break;

                    case 'jpg':
                        $quality = 75;
                        // 表示可能なファイルサイズ以下になるまで、10%ずつクオリティを調整する
                        do {
                            @unlink($outputImagePathTemp);
                            imagejpeg($outputImage, $outputImagePathTemp, $quality);
                            $quality -= 10;
                            clearstatcache();
                        } while (filesize($outputImagePathTemp) > $this->outputFileSize && $quality > 0);
                        break;

                    case 'png':
                        imagepng($outputImage, $outputImagePathTemp);
                        break;

                    case 'bmp':
                        imagewbmp($outputImage, $outputImagePathTemp);
                        break;

                    default:
                        GC_Utils_Ex::gfPrintLog('不正な画像タイプ: ');
                        break;
                }

                // メモリ開放
                imagedestroy($outputImage);

                $scale -= 0.1;
                clearstatcache();
            } while (filesize($outputImagePathTemp) > $this->outputFileSize && $scale >= 0.5);

            rename($outputImagePathTemp, $outputImagePath);

            // メモリ開放
            imagedestroy($tempImage);

            $info['convert'] = TRUE;
        }

        if ($height_was_null) {
            $this->outputImageHeigh = NULL;
        }

        $info['outputImagePath']  = $outputImagePath;
        $info['outputImageName']  = $outputImageName;
        return $info;

    }

    // Setter
    function setOutputDir($outputDir)   { $this->outputImageDir   = $outputDir;  }
    function setImageType($imageType)   { $this->outputImageType  = $imageType;  }
    function setImageWidth($imageWidth) { $this->outputImageWidth = $imageWidth; }
    function setImageHeight($imageHeight) { $this->outputImageHeight = $imageHeight; }
    function setFileSize($fileSize)     { $this->outputFileSize   = $fileSize;   }

    // Getter
    function getOutputDir()   { return $this->outputDir;         }
    function getImageType()   { return $this->outputImageType;   }
    function getImageWidth()  { return $this->outputImageWidth;  }
    function getImageHeight() { return $this->outputImageHeight; }

    /*
     * PrivateMethod
     */
    function beforeExecute() {
    }

    /**
     * 透過GIFの色情報を返す
     *
     * @access private
     * @param resource $image imagecreatetruecolor() のような画像作成関数が返す画像リソース。
     * @return array 色情報
     */
    function getTransparentColor($image) {
        $max_x = imagesx($image) - 1;
        $max_y = imagesy($image) - 1;
        for ($x = 0; $x <= $max_x; $x++) {
            for ($y = 0; $y <= $max_y; $y++) {
                $color_index = imagecolorat($image, $x, $y);
                $arrColors = imagecolorsforindex($image, $color_index);
                if ($arrColors['alpha'] !== 0){
                    return $arrColors;
                }
            }
        }
        return array();
    }
}
?>
