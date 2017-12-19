<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Admin\Model\AuthGroupModel;
use Think\Page;

/**
 * 后台内容控制器
 * @author huajie <banhuajie@163.com>
 */
class JobController extends AdminController {
   
    public function index(){
    
        $Job   =   M('job');
        $count      = $Job->where('status!=6')->count();
        $Page       = new Page($count,10);
        $show       = $Page->show();

        $list = $Job->where('status!=6')->order('push_at desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('list',$list);
        $this->assign('page',$show);

        $this->display();
    }
    public function getJobDetail(){
        $jobId = isset($_REQUEST['jobId'])?$_REQUEST['jobId']:0;
        if($jobId){
            $data = M('job')->where('id = '.$jobId)->find();
            $this->ajaxReturn($data);
        }else{
            return 0;
        }
    }
    
    public function changeStatus(){
        $method = isset($_REQUEST['method'])?$_REQUEST['method']:'';
        $jobId = isset($_REQUEST['id'])?$_REQUEST['id']:'';
        
        if($method && $jobId){
            if($method == 'avail'){
                $status = 1;
                 $data = array("status",$status);
                M('job')->where("id = ".$jobId)->save();
            }elseif($method == 'disable'){
                $status = 6;
                 $data = array("status",$status);
                M('job')->where("id = ".$jobId)->save();
            }elseif($method == 'delete'){
                M('job')->where("id = ".$jobId)->delete();
            }
            $this->success('操作成功',U('Job/Index'));
        }else{
            $this->error('参数错误！',U('Job/Index'));
        }
    }

    
    /**
     * 文档新增页面初始化
     * @author huajie <banhuajie@163.com>
     */
    public function add(){
        if(isset($_POST) && isset($_POST['title']) && isset($_POST['contactphone'])){
            $_POST['created_at'] = date("Y-m-d H:i:s",time());
            $_POST['updated_at'] = date("Y-m-d H:i:s",time());
            M('job')->data($_POST)->add();
            $this->success('操作成功',U('Job/Index'));
        }
        $this->display();
    }

    /**
     * 文档编辑页面初始化
     * @author huajie <banhuajie@163.com>
     */
    public function edit(){
        //获取左边菜单
        $this->getMenu();

        $id     =   I('get.id','');
        if(empty($id)){
            $this->error('参数不能为空！');
        }

        // 获取详细数据 
        $Document = D('Document');
        $data = $Document->detail($id);
        if(!$data){
            $this->error($Document->getError());
        }

        if($data['pid']){
            // 获取上级文档
            $article        =   $Document->field('id,title,type')->find($data['pid']);
            $this->assign('article',$article);
        }
        // 获取当前的模型信息
        $model    =   get_document_model($data['model_id']);

        $this->assign('data', $data);
        $this->assign('model_id', $data['model_id']);
        $this->assign('model',      $model);

        //获取表单字段排序
        $fields = get_model_attribute($model['id']);
        $this->assign('fields',     $fields);


        //获取当前分类的文档类型
        $this->assign('type_list', get_type_bycate($data['category_id']));

        $this->meta_title   =   '编辑文档';
        $this->display();
    }

    /**
     * 更新一条数据
     * @author huajie <banhuajie@163.com>
     */
    public function update(){
        $document   =   D('Document');
        $res = $document->update();
        if(!$res){
            $this->error($document->getError());
        }else{
            $this->success($res['id']?'更新成功':'新增成功', Cookie('__forward__'));
        }
    }


}