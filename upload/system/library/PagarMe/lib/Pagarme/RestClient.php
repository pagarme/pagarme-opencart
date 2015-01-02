<?php

class RestClient 
{
	private $http_client;
	private $method;
	private $url;
	private $headers = Array();
	private $parameters =  Array();
	private $curl;

	public function __construct($params = array()) 
	{
			$this->curl = curl_init();
			$this->headers = array(
				'Accept: application/json',
				'Content-Type: application/json',
			);

			if(!$params["url"]) {
				throw new PagarMe_Exception("You must set the URL to make a request.");
			} else {
				$this->url = $params["url"];
			}

			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

			if($params["parameters"]) {
				$this->parameters = array_merge($this->parameters, $params["parameters"]);
			}

			if($params["method"]) {
				$this->method = $params["method"];
			}

			$params =  self::_encodeObjects($this->parameters);
			$this->parameters = http_build_query($params);

			if ($this->method){
				switch($this->method) {
				case 'post':
				case 'POST':
					curl_setopt($this->curl, CURLOPT_POST, true);
					curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parameters);	
					break;
				case 'get':
				case 'GET':
					$this->url .= '?'.$this->parameters;
					break;
				case 'put':
				case 'PUT':
					$this->method = 'HTTP_METH_PUT';
					curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
					curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parameters);
					break;
				case 'delete':
				case 'DELETE':
					$this->method = HTTP_METH_DELETE;
					curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
					curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->parameters);
					break;

				}
			}

			curl_setopt($this->curl, CURLOPT_URL, $this->url);	

			if(isset($params["headers"])) {
				$this->headers = array_merge($this->headers, $params["headers"]);
				curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
			}
	}


	public static function _encodeObjects($d)
	{
		if ($d === true) {
			return 'true';
		} else if ($d === false) {
			return 'false';
		} else if (is_array($d)) {
			$res = array();
			foreach ($d as $k => $v)
				$res[$k] = self::_encodeObjects($v);
			return $res;
		} else {
			return self::utf8($d);
		}
	}

	public static function utf8($value)
	{
		if (is_string($value) && mb_detect_encoding($value, "UTF-8", TRUE) != "UTF-8")
			return utf8_encode($value);
		else
			return $value;
	}

	public static function encode($arr, $prefix=null)
	{
		if (!is_array($arr))
			return $arr;

		$r = array();
		foreach ($arr as $k => $v) {
			if (is_null($v))
				continue;

			if ($prefix && $k && !is_int($k))
				$k = $prefix."[".$k."]";
			else if ($prefix)
				$k = $prefix."[]";

			if (is_array($v)) {
				$r[] = self::encode($v, $k, true);
			} else {
				$r[] = rawurlencode($k)."=".rawurlencode($v);
			}
		}

		return implode("&", $r);
	}


	public function run() 
	{
			$response = curl_exec($this->curl);

			if (!defined('CURLE_SSL_CACERT_BADFILE')) {
				define('CURLE_SSL_CACERT_BADFILE', 77);
			}

			$errno = curl_errno($this->curl);
			if ($errno == CURLE_SSL_CACERT || $errno == CURLE_SSL_PEER_CERTIFICATE ||$errno == CURLE_SSL_CACERT_BADFILE) {
				curl_setopt($this->curl, CURLOPT_CAINFO, dirname(__FILE__) . '/ca-certificates.crt');
				$response = curl_exec($this->curl);
			}

			$error = curl_error($this->curl);
			if($error) {
				throw new PagarMe_Exception("error: ".$error);
			}
			$code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
			curl_close($this->curl);
			return array("code" => $code, "body" => $response);
	}

}

