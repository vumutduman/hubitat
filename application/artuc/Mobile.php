<?php
	class Mobile{
		function Mobile(){
			$userAgent = new Zend_Http_UserAgent();
			$browser = $userAgent->getUserAgent();
			
			if(stristr($browser, 'ndroid') OR stristr($browser, 'iphone') OR stristr($browser,'ipad')){
				$this->isMobile = true;
			}
			
			if(stristr($browser, 'ndroid')){
				$this->isAndroid = true;
			}
			
			if(stristr($browser, 'chrome')){
				$this->isChrome = true;
			}
			
			if(stristr($browser,'ipad')){
				$this->isIpad = true;
			}
			
			return $this;
		}
	}