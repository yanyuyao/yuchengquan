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
class IndexController extends HomeController {

	//系统首页
    public function index(){

        $category = D('Category')->getTree();
        $lists    = D('Document')->lists(null);

        $this->assign('category',$category);//栏目
        $this->assign('lists',$lists);//列表
        $this->assign('page',D('Document')->page);//分页
        
        $user_auth = session('user_auth');
         
        $home_quanzhi_job = M('job')->where("status = 1 and ishome = 1 and job_type = '全职'")->select(); //
        $home_jianzhi_job = M('job')->where("status = 1 and ishome = 1 and job_type = '兼职'")->select();
        
        $this->assign("home_quanzhi_job",$home_quanzhi_job);
        $this->assign("home_jianzhi_job",$home_jianzhi_job);
        $this->display();
    }

}