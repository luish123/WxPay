<?php
namespace Home\Controller;
use Think\Controller;


//环境是tp框架 
//证书放在和本控制器同级目录下
// 官方文档 https://pay.weixin.qq.com/wiki/doc/api/tools/mch_pay.php?chapter=14_1


/**
 * 微信企业付款操作类
 * Author  :  Max.wen
 * DateTime: <15/9/16 11:00>
 */
class WpayController extends Controller
{
    /**
     * API 参数
     * @var array
     * 'mch_appid'         # 公众号APPID
     * 'mchid'             # 商户号
     * 'device_info'       # 设备号
     * 'nonce_str'         # 随机字符串
     * 'partner_trade_no'  # 商户订单号
     * 'openid'            # 收款用户openid
     * 'check_name'        # 校验用户姓名选项 针对实名认证的用户
     * 're_user_name'      # 收款用户姓名
     * 'amount'            # 付款金额
     * 'desc'              # 企业付款描述信息
     * 'spbill_create_ip'  # Ip地址
     * 'sign'              # 签名
     */
    public $parameters = [];
	public $curl_timeout;
	public $url;
	//test
    public function __construct()
    {
        $this->url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $this->curl_timeout =30;
    }

    /**
     * 生成请求xml数据
     * @return string
     */
    public function createXml()
    {


    }



    /**
     * openid 用户openid
     * amount 金额
     * desc 描述
     * 	spbill_create_ip 调用接口的ip地址
     * 订单号
     * @param $money
     * @param $ss
     */
    public function send()
    {
		$amount=floatval($_POST['money'])*100;
		$amount=intval("$amount");
		$openid=M("user")->where("id=".cookie("dfhId"))->find()['pid'];
//        exit;
        $orderNumber = $this->order();

        $sign = "amount={$amount}&check_name=NO_CHECK&desc=ss&mch_appid=mch_appid&mchid=mchid&nonce_str=3PG2J4ILTKCH16CQ2502SI8ZNMTM67VS&openid={$openid}&partner_trade_no=".$orderNumber."&spbill_create_ip=192.168.0.102&key=key";
        var_dump($sign);
     
        $sign = strtoupper(md5($sign));
       // var_dump($sign);
        return  "<xml>
	<amount>{$amount}</amount>
	<check_name>NO_CHECK</check_name>
	<desc>desc</desc>
	<mch_appid>mch_appid</mch_appid>
	<mchid>mchid</mchid>
	<nonce_str>3PG2J4ILTKCH16CQ2502SI8ZNMTM67VS</nonce_str>
	<openid>{$openid}</openid>
	<partner_trade_no>{$orderNumber}</partner_trade_no>
	<spbill_create_ip>192.168.0.102</spbill_create_ip>
	<sign>{$sign}</sign>
</xml>";



    }
	public function xml_to_data($xml){
        if(!$xml){
            return false;
        }
//将XML转为array
//禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $data;
    }
	
	// 企业付款接口
	function sendmoney(){
		
		$data=$this->send();
		$response = $this->postXmlSSLCurl($data,"https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers");
		dump(1);
		if($_POST['is_money']){
			$m=M("user")->where("id=".cookie("dfhId"))->save(array("commission"=>0));
		}
		dump($response);
	}

    public function order()
    {
        $rand = rand(1000,9999);
        $str = date('YmdHis',time()).$rand;
        return "$str";
    }
    /**
     *     作用：使用证书，以post方式提交xml到对应的接口url
     */
    function postXmlSSLCurl($xml,$url,$second=30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
		
		
		//证书的交互//
        curl_setopt($ch, CURLOPT_SSLCERT, dirname(__FILE__).'/cert'.DIRECTORY_SEPARATOR.'apiclient_cert.pem');
        curl_setopt($ch, CURLOPT_SSLKEY, dirname(__FILE__).'/cert'.DIRECTORY_SEPARATOR.'apiclient_key.pem');
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/cert'.DIRECTORY_SEPARATOR.'rootca.pem');
		
        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }

        function  sign(){
            $stringA="appid=wxd930ea5d5a258f4f&body=test&device_info=1000&mch_id=10000100&nonce_str=ibuaiVcKdpRxkhJA";

        }

}
