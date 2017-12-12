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
        if($isnew){
            $list = M('job')->where($where)->limit($page,$num)->order('push_at desc')->select();
        }else{
            $list = M('job')->where($where)->limit($page,$num)->select();
        }
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
        $is_allow_anyone = 1;
       
        $phone = '';
        $uid = 0;
        if(!$is_allow_anyone){
            if(!checkuserlogin()){ $this->error('请先登录',U('/User/login'));}
                $user_auth = session('user_auth');
                $uid = $user_auth['uid'];
                $phone = $user_auth['phone'];
        }
        
        $jobtype = isset($_REQUEST['jobtype'])?$_REQUEST['jobtype']:'quanzhi';
        $title = isset($_REQUEST['title'])?$_REQUEST['title']:'';
        $company = isset($_REQUEST['company'])?$_REQUEST['company']:'';
        $peoplenum = isset($_REQUEST['peoplenum'])?$_REQUEST['peoplenum']:'';
        $commision = isset($_REQUEST['commision'])?intval($_REQUEST['commision']):'';
        $workday = isset($_REQUEST['workday'])?intval($_REQUEST['workday']):0;
        $workhours = isset($_REQUEST['workhours'])?intval($_REQUEST['workhours']):0;
        $contactname = isset($_REQUEST['contactname'])?$_REQUEST['contactname']:'';
        $contactphone = isset($_REQUEST['contactphone'])?$_REQUEST['contactphone']:'';
        $workaddress = isset($_REQUEST['workaddress'])?$_REQUEST['workaddress']:'';
        $content = isset($_REQUEST['content'])?$_REQUEST['content']:'';
        
        
        if($title && $content){
            $data = array(
                "iUserId"=> $uid,
                "title"=>  addslashes($title),
                "companyname"=>  addslashes($company),
                "content"=>  addslashes($content),
                "peoplenums"=>$peoplenum,
                "commission"=>$commision,
                "workhours"=>$workday*24 + $workhours,
                "contactname"=>  addslashes($contactname),
                "contactphone"=>$contactphone,
                "workaddress"=>$workaddress,
                "status"=>1,
                "created_at"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
                "push_at"=>date("Y-m-d H:i:s",time()),
                "job_type"=>$jobtype=='jianzhi'?'兼职':'全职',
                "changqizhaopin"=>'长期',
                "sexlimit"=>'不限',
            );
            if(M('job')->add($data)){
                $this->success('发布成功！',U('Job/index'));
                return true;
            }
        }
        $this->assign("jobtype",$jobtype);
        $this->assign("userphone",$phone);
        $this->assign("userid",$uid);
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