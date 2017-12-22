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
        $jobtype = isset($_REQUEST['jobtype'])?$_REQUEST['jobtype']:'';
        
        $page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
        $num = 200;
        
        $where = array();
        
        $where['status'] = 1;
        
        if(isset($_REQUEST['istop'])){
            $where['istop'] = 1;
            $pagetitle = "推荐";
        }
        if($jobtype){
            if($jobtype == 'quanzhi'){
                $where['job_type'] = '全职';
            }
            if($jobtype == 'jianzhi'){
                $where['job_type'] = '兼职';
            }
        }
        
        if($isnew){
            $list = M('job')->where($where)->limit($page,$num)->order('push_at desc,id desc')->select();
            $pagetitle = '最新';
        }else{
            $list = M('job')->where($where)->limit($page,$num)->order("id desc")->select();
        }
       
        
        if($datatype == 'ajax'){
            $this->ajaxReturn(array("code"=>$list?"1":'0',"list"=>$list),'JSON');
        }else{
            $this->assign("istop",$istop);
            $this->assign("page",$page);
            $this->assign("isnew",$isnew);
            $this->assign("list",$list);
            $this->assign("pagetitle",$pagetitle?'找工作 - '.$pagetitle:'找工作');
            $this->display('job');
        }
    }
	
    public function pushjob(){
        $is_allow_anyone = 0;
       
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
        $commision = isset($_REQUEST['commision'])?($_REQUEST['commision']):'';
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
        $this->assign("pagetitle",'发布招聘信息');
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
    
    public function addToMyJob(){
        $jobId = isset($_REQUEST['jobId'])?$_REQUEST['jobId']:0;
        $user_auth = session('user_auth');
        $uid = $user_auth['uid'];
        
        if($jobId && $uid){
            $data = array(
                "iUserId"=>$uid,
                "iJobId"=>$jobId,
                "created_at"=>date("Y-m-d H:i:s",time()),
                "status"=>0
            );
            M('applications')->add($data);
        }
        return 1;
    }
    
    public function joblist(){
        $user_auth = session('user_auth');
        $uid = $user_auth['uid'];
        $pagetitle = '';
        $jobtype = isset($_REQUEST['type'])?$_REQUEST['type']:'new';

        //我感兴趣的
        if($jobtype == 'my'){
            $sql = "select * from ycq_job job left join ycq_applications a on job.id = a.iJobId Where a.iUserId = $uid ";
            $pagetitle = '我刚兴趣的工作';
        }
        if($jobtype == 'new'){
            $sql = "select * from ycq_job job where status = 1 order by push_at desc ";
             $pagetitle = '最新工作';
        }
        if($jobtype == 'mypush'){
            $sql = "select * from ycq_job job where status = 1 and iUserId = '".$uid."' order by push_at desc ";
             $pagetitle = '我发布的';
        }
        $page = isset($_REQUEST['page'])?$_REQUEST['page']:0;
        $num = 200;
//        echo $sql;
//        $list = M('job')->where($where)->limit($page,$num)->order('push_at desc')->select();
//        
        $Model = new \Think\Model(); // 实例化一个model对象 没有对应任何数据表
        $list = $Model->query($sql);
        
        $this->assign("page",$page);
        $this->assign("list",$list);
        $this->assign("pagetitle",$pagetitle);
        $this->display('joblist');
    }
}