<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//根据ip获取地址
function ip2addr($ip='')
{
	if (empty($ip)) {
		$ip = getIp();
	}
	$host = "http://saip.market.alicloudapi.com";
    $path = "/ip";
    $method = "GET";
    $appcode = "27d9fe77de7d425eb5fb353bf4fba9ea";
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    $querys = "ip=".$ip;
    $bodys = "";
    $url = $host . $path . "?" . $querys;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    $info = json_decode(curl_exec($curl),true);
    if ($info['showapi_res_body']['ret_code']!==0) {
    	return $ip;
    }
    return $info['showapi_res_body']['country'].$info['showapi_res_body']['region'].$info['showapi_res_body']['city'].$info['showapi_res_body']['county'];
}
/*
获取用户ip
 */
function getIp()
{

    if(!empty($_SERVER["HTTP_CLIENT_IP"]))
    {
        $cip = $_SERVER["HTTP_CLIENT_IP"];
    }
    else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    else if(!empty($_SERVER["REMOTE_ADDR"]))
    {
        $cip = $_SERVER["REMOTE_ADDR"];
    }
    else
    {
        $cip = '';
    }
    preg_match("/[\d\.]{7,15}/", $cip, $cips);
    $cip = isset($cips[0]) ? $cips[0] : 'unknown';
    unset($cips);

    return $cip;
}


/*
json返回 针对后台的返回 
info信息 
code错误号 0代表没错误 其他有错误
url 地址 暂无使用
 */ 
function xreturn($info,$code=0,$url='')
{
    return json(['info'=>$info,'code'=>$code,'url'=>$url]);
}



//树形类别
function type2tree($value)
{
    if ($value==='0,') {
       return '|-';
    }
    $arr = explode(',',rtrim($value,','));
    $num = count($arr);
    return '|-'.str_repeat('-',($num-1)*4);
}

/*
权限不够隐藏按钮
 */
function role()
{
    if ((int)session('userinfo.role')!==1) {
        return "style='display:none;'";
    }
}

function sendTemplateSMS($to,$datas,$tempId)
{

     // 初始化REST SDK
     //主帐号,对应开官网发者主账号下的 ACCOUNT SID
        $accountSid= '8a216da85a1158e2015a16d1a288016b';

        //主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
        $accountToken= '80c8de5382554ec1949ead9fade3aace';

        //应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
        //在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
        $appId='8a216da85a1158e2015a16d1a2fe016f';

        //请求地址
        //沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
        //生产环境（用户应用上线使用）：app.cloopen.com
        $serverIP='app.cloopen.com';


        //请求端口，生产环境和沙盒环境一致
        $serverPort='8883';

        //REST版本号，在官网文档REST介绍中获得。
        $softVersion='2013-12-26';
     $rest = new \ms\Rest($serverIP,$serverPort,$softVersion);
     $rest->setAccount($accountSid,$accountToken);
     $rest->setAppId($appId);
    
     // 发送模板短信
     // echo "Sending TemplateSMS to $to <br/>";
     $result = $rest->sendTemplateSMS($to,$datas,$tempId);
     // if($result == NULL ) {
     //     echo "result error!";
     //     break;
     // }
     if($result->statusCode!=0) {
         // echo "error code :" . $result->statusCode . "<br>";
         // echo "error msg :" . $result->statusMsg . "<br>";
         //TODO 添加错误处理逻辑
        echo "2";
     }else{
         // echo "Sendind TemplateSMS success!<br/>";
         // // 获取返回信息
         // $smsmessage = $result->TemplateSMS;
         // echo "dateCreated:".$smsmessage->dateCreated."<br/>";
         // echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
         //TODO 添加成功处理逻辑
        echo "1";
     }
}
