<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 2016/12/7
 * Time: 16:28
 */
 namespace Home\Model;
 use Think\Model;
class WeModel extends Model{
    private $appid="appid";
    private $appsecte="appsecte";

    function __construct()
    {
        $this->_db=M("rate");
    }

  public  function getToken(){

        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=". $this->appid."&secret=". $this->appsecte;


        if(file_exists("./token")){
			 $content=file_get_contents("./token");
             $jsondata=json_decode($content);
			 $creattime=$jsondata->creattime;

            if(time()-$creattime>7000){
                $data=$this->curl($url,"get","https");
                $jsondata=json_decode($data);
				$jsondata->creattime=time();
				$_data=json_encode($jsondata);
                //书写到token中
                file_put_contents("./token",$_data);
            }else{
				$data=$this->curl($url,"get","https");
				$jsondata=json_decode($data);
			}
        }else{
            //文件不存在的时候
           $data=$this->curl($url,"get","https");
                $jsondata=json_decode($data);
				$jsondata->creattime=time();
				$_data=json_encode($jsondata);
                //书写到token中
                file_put_contents("./token",$_data);
        }

        $token=$jsondata->access_token;

        return $token;

    }

   public function curl($url,$method="get",$type="http",$data=""){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_HEADER,0);
       if($type=="https"){
           curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//不做服务器认证
           curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//不做客户端认证

       }
       if($method=="post"){
           curl_setopt($ch, CURLOPT_POST, true);//设置请求是POST方式
           curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//设置POST请求的数据
       }
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $data=curl_exec($ch);
        curl_close($ch);
        return $data;

    }
//获取ticket
    public  function getTicket($sceneid){
        $accessToken=$this->getToken();

        $ticketurl="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$accessToken;
        $posfileds='{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": '.$sceneid.'}}}';
       $data= $this->curl($ticketurl,"post","https",$posfileds);

        $jsondata=json_decode($data);
        $ticket=$jsondata->ticket;


        return $ticket;
    }

    //获取二维码
    public function getQR(){
        $sceneid=2;
        $ticket=$this->getTicket($sceneid);


        $url="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);


      // $data=  $this->curl($url,"get","http");
        echo '<img src="'.$url.'" />';

        file_put_contents("img.jpg",$url);
       // $imgurl="img/scene2.jpg";
      //  file_put_contents($imgurl,$data);
      //  imagepng($data,"scenes22.jpg");
      //  header("location:viewImg.php");

//        header("Content-Type:image/jpg");
//        imagepng($data);exit;
       // return $data;
    }

	//userinfo
    public function getUserInfo($openID){

       $token= $this->getToken();
        //exit("token=".$token);
        $url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$openID."&lang=zh_CN";
		
       $data= $this->curl($url,"get","https");
	  // exit("data=".$data);
        $jsondata=json_decode($data);
       
	  
        return $jsondata;

    }

    public function sendTPLInfo($openid,$template_id,$url,$data){
        $token= $this->getToken();

        $arr=array(
            "touser"=>$openid,
            "template_id"=>$template_id,
            "url"=>$url,
             "data"=>$data
            
        );
       $postdata= json_encode($arr);
        //echo $postdata;
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$token;
        $data= $this->curl($url,"post","https",$postdata);
       $jsondata=json_encode($data);
	   return $jsondata;
    }
	//get tpl list
	 public function getTPLList(){
        $token= $this->getToken();
        $url="https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=".$token;
        $data= $this->curl($url,"get","https");
		$jsondata=json_decode($data);
        return $jsondata;

    }

	public function getTPLInfo(){
	}



	//得到所有关注用户的信息
	public function getUSERList(){
		$token= $this->getToken();
		$url="https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$token;
        $data= $this->curl($url,"get","https");
		$jsondata=json_decode($data);
        return $jsondata;
	}

	//获取通过网页进入的威信用户的openid
	//snsapi_userinfo方式 可以不需要关注公众号
	public function getBaseInfo(){
		$redirect_uri=urlencode(__HOSTNAME__."dafuhao/2/index.php?c=index&a=getinfo");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=333#wechat_redirect";
header('location:'.$url);
		//echo $url;
	}
	public function getUserOpenId(){

		
		$code=$_GET["code"];
		//var_dump($code);
		$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->appsecte."&code=".$code."&grant_type=authorization_code";

		$res=$this->curl($url,"get","https");
		$data=json_decode($res);
		//var_dump($data);

		return $data->openid;
	}
}