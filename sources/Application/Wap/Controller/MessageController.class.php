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
 * 主要获取首页聚合数据$
 */
class MessageController extends Home2Controller {
	
    public function mlist(){
		$mtype = isset($_REQUEST['type'])?$_REQUEST['type']:0;// 0=chat, 1=notice, 2=public
		$user_auth = session('user_auth');
		
	//{{{ chat list
		//{{{ 与本人聊天的人员列表
		$list = array();
		$list = M('message')->distinct(true)->field('fromid as fromid,toid as toid')->where("(fromid =". $user_auth['uid']." or  toid =". $user_auth['uid'].") and type = 0")->order('sendtime desc')->select();
		
		$useridlist = array();
		//排除本人和重复的数据
		foreach($list as $k=>$v){
			if($v['fromid'] != $user_auth['uid'] && !in_array($v['fromid'],$useridlist)){
				$useridlist[] = $v['fromid'];
			}
			if($v['toid'] != $user_auth['uid']  && !in_array($v['toid'],$useridlist)){
				$useridlist[] = $v['toid'];
			}
    	}
		//}}}
		 
		$usermsglist = array(); 
		foreach($useridlist as $k=>$v){
			//初始化变量
			$username = '';
			$useravatar = '';
			$lastmsg = '';
			
			
			//获取用户username, avatar
			$userinfo = M('user')->where("id = $v")->find();
			
			if($userinfo){
				$username = $userinfo['username'];
				$useravatar = $userinfo['avatar'];
			}
			
			//获取最后发送的msg和时间
			$lastmsg = M('message')->where("(fromid = $v and toid = ".$user_auth['uid'].") OR ( fromid = ".$user_auth['uid']." and toid = $v)")->order("sendtime desc")->find();
			
			
			//只有有username和最后信息的才放到数组里显示
			if($username && $lastmsg){
				$usermsglist[$v] = array(
					"uid"=>$v,
					"uname"=>$username,
					"uavatar"=>$useravatar,
					"fromid"=>$lastmsg['fromid'],
					"lastmsg"=>$lastmsg['msg'],
					"lastsendtime"=>date("H:i",strtotime($lastmsg['sendtime'])),
					"lastlooktime"=>$lastmsg['looktime']
				);
			}
		}
		
		$this->assign("usermsglist",$usermsglist);
	//}}} chat list
	
	
	//{{{ public list
		$publiclist = M('message')->where("type = 2 and toid = ".$user_auth['uid'])->order("sendtime desc")->select();
		
		$this->assign("publiclist",$publiclist);
	
	//}}} public list
	
	
		$this->assign("type",$mtype);
		
		$this->display('list');
	}
	
	public function mchat(){
		$chatid = isset($_REQUEST['uid'])?$_REQUEST['uid']:0;
		$mymsg = isset($_REQUEST['mymsg'])?$_REQUEST['mymsg']:'';
		$act = isset($_REQUEST['act'])?$_REQUEST['act']:'';
		
		
		$user_auth = session('user_auth');
		
		if(!$chatid){
			$this->redirect('Index/index');
		}
		
		$chatmsginfo = M('message')->where("((fromid = $chatid and toid = ". $user_auth['uid'].") or (fromid = ".$user_auth['uid']." and toid = $chatid)) and type = 0")->order("sendtime")->select();
		
		$chatuserinfo = M('user')->where("id = $chatid")->find();
		$myinfo = M('user')->where("id = ".$user_auth['uid'])->find();
		
		foreach ($chatmsginfo as $k=>$v){
			if($v['looktime'] == ''){
				$chatmsglist[] = array(
					"fromid" => $v['fromid'],
					"msg" => $v['msg'],
					"sendtime" => date("m-d H:i",strtotime($v['sendtime'])),
					"looktime" => $v['looktime']
				);
			}else{
				$chatmsglist[] = array(
					"fromid" => $v['fromid'],
					"msg" => $v['msg'],
					"sendtime" => date("m-d H:i",strtotime($v['sendtime'])),
					"looktime" => date("m-d H:i",strtotime($v['looktime']))
				);
			}
		}
		
		//每30s刷新请求
		if($act == "getajax"){
			$msgnum = $_REQUEST['msgnum'];
			$newmsgnum = count($chatmsglist);
			if($newmsgnum > $msgnum){
				$mewchatmsglist = array_slice($chatmsglist,$msgnum);
				echo json_encode($mewchatmsglist);
			}else{
				echo 0;
			};
			exit;
		};
		
		//提交 my-chat-msg
		if($act == "ajax"){
			$pushmsg = array(
				"msg" => $mymsg,
				"type" => $_REQUEST['type'],
				"fromid" => $user_auth['uid'],
				"toid" => $chatid,
				"sendtime" => date("Y-m-d H:i:s")
			);
			if(M("message")->add($pushmsg)){
				echo 1;
				exit;
			}else{
				echo 0;
				exit;
			}
		}
		
		
		
		
		$this->assign("chatid",$chatid);
		$this->assign("chatname",$chatuserinfo['username']);
		$this->assign("chatavatar",$chatuserinfo['avatar']);
		$this->assign("myavatar",$myinfo['avatar']);
		
		$this->assign("chatmsglist",$chatmsglist);
		
		$this->display('chat');
	}
	
	public function mnotice(){
		$this->display('notice');
	}
	
	public function mpublic(){
		$pid = isset($_REQUEST['pid'])?$_REQUEST['pid']:"";
		if(!$pid){
			$this->redirect("Index/index");
		}
		
		$msginfo = M("message")->where("id = $pid")->find();
		
		$this->assign("msginfo",$msginfo);
		$this->display('public');
	}
	
}