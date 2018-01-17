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
class IndexController extends Home2Controller {

	//系统首页
    public function index(){
		$jobnum = isset($_REQUEST["jobnum"])?$_REQUEST["jobnum"]:0;
		
		
        $category = D('Category')->getTree();
        $lists    = D('Document')->lists(null);

        $this->assign('category',$category);//栏目
        $this->assign('lists',$lists);//列表
        $this->assign('page',D('Document')->page);//分页
        
        $user_auth = session('user_auth');
        
        $home_quanzhi_job = M('job')->where("status = 1 and ishome = 1 and job_type = '全职'")->limit("$jobnum,5")->select(); //
        $home_jianzhi_job = M('job')->where("status = 1 and ishome = 1 and job_type = '兼职'")->limit("$jobnum,5")->select();
        
		if($jobnum != 0){
			if($_REQUEST["jobtype"] == 0){
				echo json_encode($home_quanzhi_job);
			}else if($_REQUEST["jobtype"] == 1){
				echo json_encode($home_jianzhi_job);
			}
			exit;
		}
		
        $this->assign("home_quanzhi_job",$home_quanzhi_job);
        $this->assign("home_jianzhi_job",$home_jianzhi_job);
        $this->display();
    }

}