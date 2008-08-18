<?php
/**
 * 画像ファイルの変換を行う
 */
class ImageConverter {
	var $outputImageDir;		// 変換後の画像の保存先
	var $outputImageType;		// 変換後の画像の形式
	var $outputImageWidth;		// 変換後の画像の横幅
	var $outputImageHeight;		// 変換後の画像の高さ
	var $outputImageFileSize;	// 変換後の画像のファイルサイズ

	// コンストラクタ
	function ImageConverter() {
		$this->outputImageDir    = realpath(realpath(dirname(__FILE__)));
		$this->outputImageType   = 'jpeg';
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
		$outputImageName  = sha1($inputImagePath . '_' . $this->outputImageWidth . '_' . $this->outputFileSize . '_' . $filestat['mtime']) . '.' . $this->outputImageType;
		$outputImagePath  = $this->outputImageDir . '/' . $outputImageName;

		// 変換後の画像の高さが指定されていない場合、変換後の画像の横幅から求める
		if (is_null($this->outputImageHeight)) {
			$this->outputImageHeight = $inputImageHeight * ($this->outputImageWidth / $inputImageWidth);
		}

		// ファイルが存在するか確認し、存在しない場合のみ作成する
		if (file_exists($outputImagePath)) {
			$info['convert'] = FALSE;
		} else {
			// 元ファイル作成
			switch($inputImageType) {
			case 1:
				// gif
				$tempImage = imagecreatefromgif($inputImagePath);
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

			$scale = 1.0;
			$outputImagePathTemp = $outputImagePath . '.tmp-' . rand();
			do {
				// 空ファイル作成
				$outputImage = ImageCreateTruecolor($this->outputImageWidth * $scale, $this->outputImageHeight * $scale);
				ImageCopyResampled($outputImage, $tempImage, 0, 0, 0, 0, $this->outputImageWidth * $scale, $this->outputImageHeight * $scale, $inputImageWidth, $inputImageHeight);

				// ファイル出力

				@unlink($outputImagePathTemp);

				switch ($this->outputImageType) {
				case 1:
				case 'gif':
					imagegif($outputImage, $outputImagePathTemp);
					break;
				default:
				case 2:
				case 'jpg':
				case 'jpeg':
					$quality = 75;
					// 表示可能なファイルサイズ以下になるまで、10%ずつクオリティを調整する
					do {
						@unlink($outputImagePathTemp);
						imagejpeg($outputImage, $outputImagePathTemp, $quality);
						$quality -= 10;
						clearstatcache();
					} while (filesize($outputImagePathTemp) > $this->outputFileSize && $quality > 0);
					break;
				case 3:
				case 'png':
					imagepng($outputImage, $outputImagePathTemp);
					break;
				case 6:
				case 'bmp':
					imagewbmp($outputImage, $outputImagePathTemp);
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

		$info['outputImagePath']  = $outputImagePath;
		$info['outputImageName']  = $outputImageName;
		return $info;

	}

	// Setter
	function setOutputDir($outputDir)   { $this->outputImageDir   = $outputDir;  }
	function setImageType($imageType)   { $this->outputImageType  = $imageType;  }
	function setImageWidth($imageWidth) { $this->outputImageWidth = $imageWidth; }
	function setFileSize($fileSize)     { $this->outputFileSize   = $fileSize;   }
	// function setImageHeight($imageHeight) { $this->outputImageHeight = $imageHeight; }

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
}
?>
