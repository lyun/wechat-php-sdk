<?php
include_once "PKCS7Encoder.class.php";
include_once "Prpcrypt.class.php";
include_once "ErrorCode.class.php";
include_once "qywechat.class.php";

/**
 *	微信公众平台PHP-SDK, ThinkPHP实例
 *  @author dodgepudding@gmail.com
 *  @link https://github.com/dodgepudding/wechat-php-sdk
 *  @version 1.2
 *  usage:
 *   $options = array(
 *			'token'=>'tokenaccesskey', //填写你设定的key
 *			'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
 *			'appid'=>'wxdk1234567890', //填写高级调用功能的app id
 *			'appsecret'=>'xxxxxxxxxxxxxxxxxxx' //填写高级调用功能的密钥
 *		);
 *	 $weObj = new TPWechat($options);
 *   $weObj->valid();
 *   ...
 *
 */
class TPWechatQy extends WechatQy
{
	/**
	 * log overwrite
	 * @see Wechat::log()
	 */
	protected function log($log){
		if ($this->debug) {
			if (function_exists($this->logcallback)) {
				if (is_array($log)) $log = print_r($log,true);
				return call_user_func($this->logcallback,$log);
			}elseif (class_exists('Log')) {
				Log::write('wechat：'.$log, Log::DEBUG);
				return true;
			}
		}
		return false;
	}

	/**
	 * 重载设置缓存
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		return S($cachename,$value,$expired);
	}

	/**
	 * 重载获取缓存
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		return S($cachename);
	}

	/**
	 * 重载清除缓存
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		return S($cachename,null);
	}

	/**
	 * =================================================
	 * lyun
	 * =================================================
	 */

	/**
	 * 快速发送文本
	 * @param $openid
	 * @param $msg
	 * @return array|bool
	 */
	public function sendCustomMessageText($openid,$msg) {
		$data = array("touser"=>$openid, "msgtype"=>"text","text"=>array("content"=>$msg));
		$result = $this->sendCustomMessage($data);
		return $result;
	}

	/**
	 * 快速发送单图文
	 * @param $openid
	 * @param $subject
	 * @param $desc
	 * @param $url
	 * @param $picurl
	 * @return array|bool
	 */
	public function sendCustomMessageNews($openid,$subject,$desc,$url,$picurl) {
		$data = array("touser"=>$openid, "msgtype"=>"news",
			"news"=>array(
				"articles"=>array(
					array("title"=>$subject,
						"description"=>$desc,
						"url"=>$url,
						"picurl"=>$picurl)
				)
			));
		$result = $this->sendCustomMessage($data);
		return $result;
	}

	/**
	 * 快速发送图片
	 * @param $openid
	 * @param $filepath
	 * @return bool
	 */
	public function sendCustomMessageImg($openid,$filepath) {
		$data = array('media'=>$filepath);
		$json = $this->uploadMedia($data,'image');
		Log::write("上传图片：".var_export($json,true),Log::DEBUG);
		if($json != false){
			$media_id = $json["media_id"];
			$data = array("touser"=>$openid, "msgtype"=>"image","image"=>array("media_id"=>$media_id));
			$result = $this->sendCustomMessage($data);
			if($result == false ){
				$this->resetAuth();
				$result = $this->sendCustomMessage($data);
			}

			return result;
		}
		return false;
	}
}



