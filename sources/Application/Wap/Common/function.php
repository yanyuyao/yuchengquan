<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}

function checkuserlogin(){
    $user_auth = session('user_auth');
    if(!$user_auth){
        return false;
    }
    return true;
}



function send_wxtemp($weObj,$data){
    
//        $options = array(
//				'token'=>'feiteng2986', //填写你设定的key
//				'encodingaeskey'=>'3Wnly9bJP3RDzf4S19fXfS9txn0KmUU6MtOtMmIDn9h', //填写加密用的EncodingAESKey
//				'appid'=>'wx23a2016c198ffa37', //填写高级调用功能的app id 
//				'appsecret'=>'9aad9f0f6792fa31c54b32ad408868f3' //填写高级调用功能的密钥
//			);
//        $weObj = new Wechat($options);
        
       
        //var_dump($data);
        
        if($data != ''){
            $msg = $weObj->sendTemplateMessage($data);
            //$weObj->addTemplateMessage();
            //echo $weObj->errCode."<br>";
            //echo $weObj->errMsg;
        }
}
	//工号审核后，发送账户开通通知
function send_zhiwu_template($weObj,$uid){
        $openid = M('wxuser')->where("uid = '".$uid."'")->getField('openid');
        if($openid == ''){
                return 0;
        }

        $data = array(
                "touser"=>$openid,
                "template_id"=>"xL2KjaETCdkTy4foB09hxBLQMZ77e-w9PofWpO0ff9Q",
                "url"=>"http://gdfeiteng.com/index.php/Home/Index/user", 
                "data"=>array(
                                "first"=>array(
                                        "value"=>"恭喜您通过审核!",
                                        "color"=>"#173177"
                                ),
                                "VIPName"=>array(
                                        "value"=>"工号",
                                        "color"=>"#173177"
                                ),
                                "VIPMobilePhone"=>array(
                                        "value"=>"15253179513",
                                        "color"=>"#173177"
                                ),
                                "remark"=>array(
                                        "value"=>"欢迎再次购买！",
                                        "color"=>"#173177"
                                )
                )
        );
        
        send_wxtemp($weObj,$data);
        
}