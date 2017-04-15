本项目 为微信一系列接口 
重点在微信和支付和企业付款

环境为thinkphp



pay.html为前端支付页面  应该放在view 文件夹下
weixinpay/example/jsapi.php 为支付ajax请求地址 本人放在根目录下  里面有设置回调地址  填写就是notcontroll.class.php；
weixinpay/lib/Wxpay.config.php为配置文件 填写 商户号 秘钥和 appid
notcontroll.class.php 为微信回调控制器  应该放在controll文件夹下  (地址是在weixinpay/example/jsapi.php)
Wepaycontroll.class.php为企业付款控制器 需要证书 证书放在和本控制器同级目录下  eg:controll/cert/证书