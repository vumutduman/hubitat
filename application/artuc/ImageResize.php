<?php
	class ImageResize{
		public function load($filename){
			$image_info = getimagesize($filename);
			$this->image_type = $image_info[2];
			if($this->image_type== IMAGETYPE_JPEG){
				$this->image = imagecreatefromjpeg($filename);
			}elseif($this->image_type== IMAGETYPE_GIF){
				$this->image = imagecreatefromgif($filename);
			}elseif($this->image_type== IMAGETYPE_PNG){
				//$this->image = imagecreatefrompng($filename);
				$this->image = imagecreatetruecolor($image_info[0], $image_info[1]);
				$white = imagecolorallocate($this->image,  255, 255, 255);
				imagefilledrectangle($this->image, 0, 0, $image_info[0], $image_info[1], $white);
				$input = imagecreatefrompng($filename);
				imagecopy($this->image, $input, 0, 0, 0, 0, $image_info[0], $image_info[1]);
			}
		}
		
		public function save($filename, $image_type = IMAGETYPE_JPEG, $compression = 80, $permissions = null){
			if($image_type== IMAGETYPE_JPEG){
				imagejpeg($this->image, $filename, $compression);
			}elseif($image_type== IMAGETYPE_GIF){
				imagegif($this->image, $filename);
			}elseif($image_type== IMAGETYPE_PNG){
				imagepng($this->image, $filename);
			}
			
			if($permissions!= null){
				//chmod($filename, $permissions);
			}
		}
		
		public function output($image_type = IMAGETYPE_JPEG){
			if($image_type== IMAGETYPE_JPEG){
				imagejpeg($this->image);
			}elseif($image_type== IMAGETYPE_GIF){
				imagegif($this->image);
			}elseif($image_type== IMAGETYPE_PNG){
				imagepng($this->image);
			}
		}
		
		public function getWidth(){
			return imagesx($this->image);
		}
		
		public function getHeight(){
			return imagesy($this->image);
		}
		
		public function resizeToHeight($height){
			if($this->getHeight() > $height){
				$ratio = $height/ $this->getHeight();
				$width = $this->getWidth()* $ratio;
				$this->resize($width, $height);
			}else{
				$this->resize($this->getWidth(), $height);
			}
		}
		
		public function resizeToWidth($width){
			if($this->getWidth() > $width){
				$ratio = $width/$this->getWidth();
				$height = $this->getHeight()* $ratio;
				$this->resize($width, $height);
			}else{
				$this->resize($this->getWidth(), $this->getHeight());
			}
		}
		
		public function scale($scale){
			$width = $this->getWidth()* $scale/ 100;
			$height = $this->getheight()* $scale/ 100;
			$this->resize($width, $height);
		}

		public function doResize($width, $height){
			if($this->getHeight() > $this->getWidth()){
				$scale = $height/$this->getHeight();
			}else{
				$scale = $width/$this->getWidth();
			}
			
			$mWidth =  $this->getWidth() * $scale;
			$mHeight =  $this->getHeight() * $scale;
			
			$offestX = ($width - $mWidth) / 2;
			$offestY = ($height - $mHeight) / 2;
			
			$new_img = imagecreatetruecolor($width, $height);
			$bgcolor = imagecolorallocate($new_img, 255, 255, 255);
			imagefill($new_img, 0, 0, $bgcolor);
			
			imagecopyresampled($new_img, $this->image, $offestX, $offestY, 0, 0, $mWidth, $mHeight, $this->getWidth(), $this->getHeight());
			$this->image = $new_img;
		}
		
		public function resize($width, $height){
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
			$this->image = $new_image;
		}
		
		public function cropFile($cropWidth, $cropHeight, $filename){
			$originalWidth = $this->getWidth();
			$originalHeight = $this->getHeight();
			if($originalWidth > $cropWidth && $originalHeight > $cropHeight):
				if($originalHeight > $originalWidth){
					$originalWidth = $this->getWidth();
					$originalHeight = $this->getHeight();
					$cropStartWidth = ($originalWidth/2)-($cropWidth/2);
					$cropStartHeight = ($originalHeight/2)-($cropHeight/2);					
				}else{
					$originalWidth = $this->getWidth();
					$originalHeight = $this->getHeight();
					
					$cropStartWidth = ($originalWidth/2)-($cropWidth/2);
					$cropStartHeight = ($originalHeight/2)-($cropHeight/2);
				}
				$canvas = imagecreatetruecolor($cropWidth, $cropHeight);
				$image_info = getimagesize($filename);
				$this->image_type = $image_info[2];
				if($this->image_type== IMAGETYPE_JPEG){
					$current_image = imagecreatefromjpeg($filename);
				}elseif($this->image_type== IMAGETYPE_GIF){
					$current_image = imagecreatefromgif($filename);
				}elseif($this->image_type== IMAGETYPE_PNG){
					$current_image = imagecreatefrompng($filename);
				}
				imagecopy($canvas, $current_image, 0, 0, $cropStartWidth, $cropStartHeight, $originalWidth, $originalHeight);
				$this->image = $canvas;
			else:
				//$this->resize($cropWidth, $cropHeight);				
			endif;
		}
		
		public static function renametotr($text){
			$text = trim($text);
			$text = str_replace('?','', $text);
			$text = str_replace("ş","s", $text);
			$text = str_replace("ç","c", $text);
			$text = str_replace("ğ","g", $text);
			$text = str_replace("ı","i", $text);
			$text = str_replace(" ","-", $text);
			$text = str_replace("ö","o", $text);
			$text = str_replace("ü","u", $text);
			$text = str_replace("Ş","s", $text);
			$text = str_replace("Ç","c", $text);
			$text = str_replace("Ğ","g", $text);
			$text = str_replace("İ","i", $text);
			$text = str_replace("Ö","o", $text);
			$text = str_replace("Ü","u", $text);
			$text = preg_replace('[^a-zA-Z0-9\-]', '', $text);
			$text = str_replace(" ","-", $text);
			$text = str_replace("--","-", $text);
			
			return $text;
		}
		
		//Filter Image
		public function filterImg(){
			//$im = imagecreatefromjpeg($this->image);
			$new_image = imagecreatetruecolor($this->getWidth(), $this->getHeight());
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
			imagefilter($new_image, IMG_FILTER_GRAYSCALE);
			imagefilter($new_image, IMG_FILTER_COLORIZE, 57, 17, 140);
			imagefilter($new_image, IMG_FILTER_BRIGHTNESS, -50);
			
			$this->image = $new_image;
		}
		
		//Filter Gallery Image
		public function filterGalleryImg(){
			$new_image = imagecreatetruecolor($this->getWidth(), $this->getHeight());
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $this->getWidth(), $this->getHeight());
			imagefilter($new_image, IMG_FILTER_GRAYSCALE);
			imagefilter($new_image, IMG_FILTER_COLORIZE, 147, 183, 6);
			imagefilter($new_image, IMG_FILTER_BRIGHTNESS, -100);
			
			$this->image = $new_image;
		}
		
		public function rename($path, $fileName, $i=1){
			$fileExtension = '.jpg';
			$fileName = str_replace($fileExtension, '', $fileName);
			$fileName = $this->renametotr($fileName);
			$fileName = strtolower($fileName).$fileExtension;

			//For performance issues.
			if(file_exists($path.$fileName)){
				//Check for previous renamed file name (file_1.jpg, file_1_1.jpg and correct the file renamer for incremental filename renaming (Ex: file_1.jpg, file_2.jpg)
				$checkPosition = strrpos($fileName, '_');
				if($checkPosition === false){
					$fileName = substr($fileName, 0, strlen($fileName)-4);
					$fileName = $fileName.'_'.$i.$fileExtension;
				}else{
					$fileName = substr($fileName, 0, $checkPosition);
					$i++;
					$fileName = $fileName.'_'.$i.$fileExtension;
				}
				
				return $this->rename($path, $fileName, $i++);
			}else{
				return $fileName;
			}
		}
	}
?>