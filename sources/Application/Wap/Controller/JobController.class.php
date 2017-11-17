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
class JobController extends HomeController {

	//系统首页
    public function index(){
        $this->display('job');
    }
	
	public function pushjob(){
        $this->display('job_push');
    }
	
	public function detail(){
        $this->display('job_detail');
    }
	
	public function mydetail(){
        $this->display('job_mydetail');
    }
}