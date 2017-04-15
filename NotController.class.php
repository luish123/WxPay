<?php
namespace Home\Controller;
use Think\Controller;
use Pay\Request;
//微信支付回调地址
//外网必须访问
class NotController extends Controller{
  function index(){
	  
	  //获取微信传给我们的数据  类型为xml
	  
	  $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	   libxml_disable_entity_loader(true);
		$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
	   $content = json_encode($postObj);  // 写入的内容
	   
	   
	   $obj = json_decode($content);

	   // openid
	   $openid=$obj->openid;
	   //zhifu money_format
	   $money=$obj->total_fee;
	   
	   //在本方法进行我们业务的逻辑处理
	   
	   //由于是微信服务器访问 所以对cookie和session的操作都没有用 

	 //微信服务器会连续访问本网页8次知道succss
	 //需在最后 给微信服务器返回success  
	 echo 'SUCCESS';
	 
  }
}