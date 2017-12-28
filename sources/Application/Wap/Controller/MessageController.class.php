<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Wap\Controller;
use OT\DataDictionary;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class MessageController extends HomeController {
	
    public function mlist(){
		$this->display('list');
	}
	
	public function mchat(){
		$this->display('chat');
	}
	
	public function mnotice(){
		$this->display('notice');
	}
	
	public function mpublic(){
		$this->display('public');
	}
	
}