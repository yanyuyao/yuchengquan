<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Wap\Controller;
use Think\Controller;
use Vendor\Wechat\Wechat;
/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class Home2Controller extends Controller {
        public function _initialize() {
//            parent::_initialize();
            /* 读取站点配置 */
            $config = api('Config/lists');
            C($config); //添加配置

            if(!C('WEB_SITE_CLOSE')){
                $this->error('站点已经关闭，请稍后访问~');
            }      
	}
        
        public function createmenu(){
            //创建菜单
                $options = array(
				'token'=>'zhida', //填写你设定的key
				'encodingaeskey'=>'et5BMmHcVMpTnwhjEaw5e6PQf0VnWg4rqHc1xUI9NKq', //填写加密用的EncodingAESKey
				'appid'=>'wx229adf13706c23e9', //填写高级调用功能的app id 
				'appsecret'=>'dbcfacb447eba4cf5193bbd8bf4f7607' //填写高级调用功能的密钥
			);
			
			
                $weObj = new Wechat($options);

                $newmenu =  array(
                                "button"=>
                                        array(
                                                array('type'=>'view','name'=>'首页','url'=>'http://8.heyukj.com/wap.php'),
                                                array('type'=>'view','name'=>'找工作','url'=>'http://8.heyukj.com/wap.php?s=/Job/index'),
                                                array('type'=>'view','name'=>'招人才','url'=>'http://8.heyukj.com/wap.php?s=/Job/pushjob')
                                            )
                                );
                    $result = $weObj->createMenu($newmenu);
                    var_dump($result);
                    return 1;
        }
	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/index');
	}



	/* 用户登录检测 */
	protected function login(){
		/* 用户登录检测 */
		is_login() || $this->error('您还没有登录，请先登录！', U('User/login'));
	}

}
