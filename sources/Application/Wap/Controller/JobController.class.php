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
        
        $datatype = isset($_REQUEST['datatype'])?$_REQUEST['datatype']:'';
        $istop = isset($_REQUEST['istop'])?$_REQUEST['istop']:0;
        $isnew = isset($_REQUEST['isnew'])?$_REQUEST['isnew']:0;
        
        $page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
        $num = 5;
        
        $where = array();
        
        $where['status'] = 1;
        
        if(isset($_REQUEST['istop'])){
            $where['istop'] = 1;
        }
        $list = M('job')->where($where)->limit($page,$num)->select();
        
        if($datatype == 'ajax'){
            $this->ajaxReturn(array("code"=>$list?"1":'0',"list"=>$list),'JSON');
        }else{
            $this->assign("istop",$istop);
            $this->assign("page",$page);
            $this->assign("isnew",$isnew);
            $this->assign("list",$list);
            $this->display('job');
        }
    }
	
    public function pushjob(){
        $this->display('job_push');
    }
    
    public function detail(){
        $jobId = isset($_REQUEST['jobId'])?$_REQUEST['jobId']:0;
        
        if(!$jobId){
            $this->redirect('Index/index');
        }
        
        $detail = M('job')->where('id = "'.$jobId.'"')->find();
        $this->assign("data",$detail);
        
        $this->display('job_detail');
    }
	
	public function mydetail(){
        $this->display('job_mydetail');
    }
}