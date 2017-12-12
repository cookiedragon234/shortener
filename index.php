<?php

//Your secret key which you recieve from google
$_SECRET = "YOUR SECRET KEY";

//Your API key which you recieve from google
$_API_KEY = "YOUR API KEY";

$echo = false;

if(isset($_GET['debug'])){
	if($_GET['debug']){
		$echo = true;
		echo 'Debugging on<br>';
	} else{
		$echo = false;
	}
}

function trace($message){
	global $echo;
	if ($echo == true){
		echo $message;
	}
}


trace('Debugging function activated<br>');
$res_lUrl = '';
$res_sUrl = '';
trace('<br>--Begin Debug Console--<br>');
trace('Start php<br>');
if(isset($_GET['url'])){
	$url = $_GET['url'];
	$captcha = $_GET['g-recaptcha-response'];
	$captcha_r=json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$_SECRET."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
	if(strlen($captcha) > 3){
	if(isset($url) && $captcha_r['success'] == true){
		trace('Form valid, fetching scripts...<br>');
				
				/**
				* This file is part of googl-php
				*
				* https://github.com/sebi/googl-php
				*
				* googl-php is free software: you can redistribute it and/or modify
				* it under the terms of the GNU General Public License as published by
				* the Free Software Foundation, either version 3 of the License, or
				* (at your option) any later version.
				*
				* This program is distributed in the hope that it will be useful,
				* but WITHOUT ANY WARRANTY; without even the implied warranty of
				* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
				* GNU General Public License for more details.
				*
				* You should have received a copy of the GNU General Public License
				* along with this program.  If not, see <http://www.gnu.org/licenses/>.
				*/
				
				class Googl
				{
					public $extended;
					private $target;
					private $apiKey;
					private $ch;
					
					private static $buffer = array();
					function __construct($apiKey = null) {
						trace('Entering construction function...<br>');
						# Extended output mode
						$extended = false;
						# Set Google Shortener API target
						$this->target = 'https://www.googleapis.com/urlshortener/v1/url?';
						# Set API key if available
						if ( $apiKey != null ) {
							$this->apiKey = $apiKey;
							$this->target .= 'key='.$apiKey.'&';
						} else{
							trace('Api was not null<br>');
						}
						# Initialize cURL
						$this->ch = curl_init();
						# Set our default target URL
						curl_setopt($this->ch, CURLOPT_URL, $this->target);
						# We don't want the return data to be directly outputted, so set RETURNTRANSFER to true
						curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
					}
					public function shorten($url, $extended = false) {
						trace('Shorten process started<br>');
						# Check buffer
						if ( !$extended && !$this->extended && !empty(self::$buffer[$url]) ) {
							return self::$buffer[$url];
						} else{
							if($extended){
								trace('Extended was set<br>');
							} if($this->extended){
								trace('$this->extended was set<br>');
							} if(empty(self::$buffer[$url])){
								trace('Buffer was true<br>');
							}
						}
						
						# Payload
						$data = array( 'longUrl' => $url );
						$data_string = '{ "longUrl": "'.$url.'" }';
						# Set cURL options
						curl_setopt($this->ch, CURLOPT_POST, count($data));
						curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data_string);
						curl_setopt($this->ch, CURLOPT_HTTPHEADER, Array('Content-Type: application/json'));
						if ( $extended || $this->extended) {
							return json_decode(curl_exec($this->ch));
							trace('Extended or $this->extended was true. Curl was not executed.<br>');
						} else {
							trace('Extended or $this->extended was not true<br>');
							$result = json_decode(curl_exec($this->ch));
							$res_lUrl = $result->longUrl;
							trace('Long URL is: '.$res_lUrl.'');
							$res_sUrl = $result->id;
							
							$_lurl = $res_lUrl;
							$_surl = $res_sUrl;
							echo '<center>';
							echo 'Short URL is: <p id="surl">'.$res_sUrl.'</p>';
							echo '<a href="download.php?lurl='.$_surl.'&surl='.$_lurl.'"><input type="submit" value="Download this to a text file"></input></a>';
							echo '</center>';
							
							self::$buffer[$url] = $result;
							return $result;
						}
					}
					public function expand($url, $extended = false) {
						trace('Expansion started<br>');
						# Set cURL options
						curl_setopt($this->ch, CURLOPT_HTTPGET, true);
						curl_setopt($this->ch, CURLOPT_URL, $this->target.'shortUrl='.$url);
						
						if ( $extended || $this->extended ) {
							return json_decode(curl_exec($this->ch));
						} else {
							return json_decode(curl_exec($this->ch))->longUrl;
						}
					}
					function __destruct() {
						trace('Destruction started<br>');
						# Close the curl handle
						curl_close($this->ch);
						# Nulling the curl handle
						$this->ch = null;
					}
				}
		$googl = new Googl($_API_KEY);
		trace('Submitted API Key, Executing function...<br>');
		trace('---FUNCTION---<br><br>');
		$googl->shorten($url);
		trace('<br><br>---/FUNCTION---<br>');
		unset($googl);
		trace('<br>Unset<br><br>');
		echo '
		<script>
			function SelectText(element) {
				var doc = document
					, text = doc.getElementById(element)
					, range, selection
				;    
				if (doc.body.createTextRange) {
					range = document.body.createTextRange();
					range.moveToElementText(text);
					range.select();
				} else if (window.getSelection) {
					selection = window.getSelection();        
					range = document.createRange();
					range.selectNodeContents(text);
					selection.removeAllRanges();
					selection.addRange(range);
				}
			}
		</script>
		';
		echo '<script> SelectText("surl"); </script>';
	} else{
		trace('Captcha Failed<br>');
	}
	} else{
		echo '<script>window.alert("Please tick the recaptcha");window.history.back();</script>';
	}
} else{
	trace('No input<br>');
}
?>
<html>
<!-- This web page and its function was coded by Cyrus Massey-Cook. Use it for yourself at https://goo.gl/pihgP2 -->
<head>
	<title>Short</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
	<script>
	$(document).ready(function() {
		$.getScript("https://www.google.com/recaptcha/api.js");
		jQuery('#g_captcha').replaceWith(jQuery('<div class="g-recaptcha" style="width:304;height:78;" id="grecaptcha" data-callback="recaptchaCallback" data-sitekey="6Lf5LQcUAAAAALTvKVKhFjguQ28fNLoP0LMLwpm7"></div>'));
	});
	</script>
</head>
<body>
	<div style="padding-left:10%; padding-right:10%; padding-top:10%;">
		<form name="sformsub" action="index.php" method="get">
			<center>
				<input type="text" name="url" style="width:100%;" required></input><br><br>
				<div id="g_captcha" style="width:303;height:77; border: 1px solid transparent; margin:0px;padding:0px;"></div><br>
				<input type="checkbox" name="debug" id="debugbut" value="true"></input>
				<label for="debugbut">Debug</label><br><br>
				<button id="sformsub" value="Shorten" style="width:182.970588775; height:40px;">Shorten</button>
			</center>
		</form>
	</div>
	
	<style>
	button.sformsub {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 14px;
		color: #050505;
		padding: 10px 20px;
		background: -moz-linear-gradient(
			top,
			#ffffff 0%,
			#ebebeb 50%,
			#dbdbdb 50%,
			#b5b5b5);
		background: -webkit-gradient(
			linear, left top, left bottom,
			from(#ffffff),
			color-stop(0.50, #ebebeb),
			color-stop(0.50, #dbdbdb),
			to(#b5b5b5));
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		border-radius: 10px;
		border: 1px solid #949494;
		-moz-box-shadow:
			0px 1px 3px rgba(000,000,000,0),
			inset 0px 0px 2px rgba(255,255,255,0);
		-webkit-box-shadow:
			0px 1px 3px rgba(000,000,000,0),
			inset 0px 0px 2px rgba(255,255,255,0);
		box-shadow:
			0px 1px 3px rgba(000,000,000,0),
			inset 0px 0px 2px rgba(255,255,255,0);
		text-shadow:
			0px -1px 0px rgba(000,000,000,0.2),
			0px 1px 0px rgba(255,255,255,1);
	}
	</style>
	
</body>
</html>