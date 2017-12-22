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
class ResumeController extends HomeController {
	
    public function resume(){
		$this->display('resume');
	}
	
	public function edit(){
		$this->display('edit');
	}
	
	public function resumelist(){
		$this->display('resumelist');
	}
	
	public function resumelistmylike(){
		$this->display('resumelist_mylike');
	}
}