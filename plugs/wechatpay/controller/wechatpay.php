<?php

namespace plugs\wechatpay\controller;
use app\system\controller\Addons;
use think\Db;
use think\Log;
use think\Session;

class wechatpay extends Addons{
    /**
     * @var string 微信配置信息
     */
    private $wehcat_pay_config = '';

    //初始化获取微信配置信息
    protected function _initialize()
    {
        $this->wehcat_pay_config = Db::name('addons')->where(['name' => 'wechatpay'])->find();
        $this->wehcat_pay_config = json_decode($this->wehcat_pay_config['config'],true);
    }

    //发起支付请求
    public function gopay(){
        $appid = Session::get('wechat_appid');
        $wechatkey = $this->wehcat_pay_config['wechat_secret'];
        $mchid = $this->wehcat_pay_config['wechat_mchid'];
        $body = $this->request->param('body');
        $money = $this->request->param('money') * 100; //支付金额单位 分
        $attach = $this->request->param('attach');//附加数据包
        $time = time();
        $out_trade_no = date('Y') . $time . rand(1000, 9999);
        $result = $this->unifiedorder($appid,$mchid,$body,$out_trade_no,
            $money,'127.0.0.1',url('system/plugs/open',['plugname'=>'wechatpay','plugaction'=>'wechatpay','plugfun'=>'notify'],true,true),Session::get('wechat_openid'),$wechatkey,$attach);
        // var_dump($result);
        $param = [
            'appId' =>  $appid,
            'timeStamp' => "$time",
            'package'   => 'prepay_id=' . $result['prepay_id'],
            'nonceStr' =>  getRandChar(32),
            'signType' =>  'MD5',
        ];

        $sign = $this->paySign($param, $wechatkey);
        $param['sign'] = $sign['sign'];
        return $param;
    }

    //微信支付回调
    public function notify(){
        $xml = file_get_contents("php://input");
        $p = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $arr = json_decode(json_encode($p), TRUE);
        $sign = $arr['sign'];
        //处理支付结果
        unset($arr['sign']);
        $ret = $this->paySign($arr, $this->wehcat_pay_config['wechat_secret']);
        $xml = '<xml>';
        $xml .= '<return_code><![CDATA[SUCCESS]]></return_code>';
        $xml .= '<return_msg><![CDATA[OK]]></return_msg>';
        $xml .= '</xml>';
        if ($ret['sign'] === $sign) {
            //签名成功
            if ($arr['result_code'] == "SUCCESS") {
                $out_trade_no = $arr['out_trade_no'];
                $openid = $arr['openid'];
                $money = $arr['total_fee'] / 100;
                $record = Db::name('wechat_pay')->where(['out_trade_no' => $out_trade_no])->find();
                if (empty($record)) {
                    $data = [
                        'openid'    =>  $openid,
                        'money'    =>  $money,
                        'out_trade_no'  =>  $out_trade_no,
                        'transaction_id'  =>  $arr['transaction_id'],
                        'create_time'=> time()
                    ];
                    $res = Db::name('wechat_pay')->insert($data);
                    if(!$res){
                        Log::error('wechatpay_error---------:'.$out_trade_no);
                    }
                }

                $class = $this->wehcat_pay_config['wechat_redirects'];
                if(class_exists($class)) {
                    $class = new $class;
                    $class->notify($arr);
                }
            }
        } else {
            //失败
            Log::error('wechatpay_ret---------:'.$ret['sign']);
            foreach ($arr as $key=>$value){
                Log::error($key,$value);
            }
            Log::error('wechatpay_sign:'.$sign);
        }
        echo $xml;
    }
    /* JSSDK */
    public function getSign($appid, $secret, $host) {
        
        //生成JSSDK签名
        if (!Session::has('jsapi_ticket')) {
            $this->getJsApi($appid, $secret);
        }
        $noncestr = getRandChar(18);
        $jsapi_ticket = Session::get('jsapi_ticket');

        $timestamp = time();
        $strSign = sha1("jsapi_ticket=$jsapi_ticket&noncestr=$noncestr&timestamp=$timestamp&url=$host");
        $arr = array(
            'appId' => $appid,
            'sign' => $strSign,
            'timestamp' => $timestamp,
            'noncestr' => $noncestr,
        );
        return $arr;
    }

    public function getJsApi($appid, $secret) {
        if (!Session::has('p_access_token')) {
            $this->getAccessToken_p($appid, $secret);
        }
        $access_token = Session::get('p_access_token');
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$access_token&type=jsapi";
        $output = https_request($url);
        $output = json_decode($output, true);
        if ($output['errmsg'] == 'ok') {
            Session::set('jsapi_ticket', $output['ticket']);
        } else {
            echo "jsapi_ticket异常";
        }
    }

    //获取普通access
    public function getAccessToken_p($appid, $secret) {
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret;
        $output = https_request($url);
        $output = json_decode($output, true);
        if (!empty($output['access_token'])) {
            Session::set('p_access_token', $output['access_token']);
        } else {
            echo "access_token异常";
        }
    }

    /**
     * 统一下单支付
     * @param $appid        微信支付分配的公众账号ID（企业号corpid即为此appId）
     * @param $mch_id       微信支付分配的商户号
     * @param $body         商品简单描述
     * @param $out_trade_no 商户系统内部订单号，要求32个字符内、且在同一个商户号下唯一。
     * @param $total_fee    订单总金额，单位为分
     * @param $spbill_create_ip APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
     * @param $notify_url   异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
     * @param $openid       用户openid
     * @param $key          微信商户平台key
     */
    public function unifiedorder($appid, $mch_id, $body, $out_trade_no, $total_fee, $spbill_create_ip, $notify_url, $openid, $key, $attach = 'attach') {
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $nonce_str = getRandChar(32);   //随机字符串，长度要求在32位以内。
        $param = [
            'appid' => $appid,
            'mch_id' => $mch_id,
            'nonce_str' => $nonce_str,
            'body' => $body,
            'out_trade_no' => $out_trade_no,
            'total_fee' => $total_fee,
            'spbill_create_ip' => $spbill_create_ip,
            'notify_url' => $notify_url,
            'trade_type' => 'JSAPI',
            'openid' => $openid,
            'attach' => $attach,
        ];

        $xml = $this->paySign($param, $key);

        $http_result = https_request($url, $xml['xml']);
        $xml = simplexml_load_string($http_result, NULL, LIBXML_NOCDATA);
        $result_arr = json_decode(json_encode($xml), true);
        return $result_arr;
    }

    // 支付签名
    public function paySign($param, $wechatkey) {
        ksort($param);
        $stringSignTemp = '';
        $xml = '<xml>';
        foreach ($param as $key => $vo) {
            $stringSignTemp .= $key . '=' . $vo . '&';
            $xml .= '<' . $key . '>' . $vo . '</' . $key . '>';
        }
        //  $stringSignTemp = rtrim($stringSignTemp,"&");
        $stringSignTemp .= 'key=' . $wechatkey;
        $sign = strtoupper(md5($stringSignTemp));
        $xml .= '<sign>' . $sign . '</sign>';
        $xml .= '</xml>';
        $result_arr = [
            'sign' => $sign,
            'xml' => $xml
        ];
        return $result_arr;
    }
}
