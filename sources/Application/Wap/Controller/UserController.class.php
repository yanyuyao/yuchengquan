<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Wap\Controller;
use User\Api\UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends HomeController {

	/* 用户中心首页 */
	public function index(){
	}
	public function center(){
		$usertype = "user";
		if($usertype == 'user'){
			$this->user_center();
		}else if($usertype == 'employer'){
			$this->employer_center();
		}
	}
	//会员主页
	public function user_center(){
		$phone = '';
                $uid = 0;
                if(!$is_allow_anyone){
                    if(!checkuserlogin()){ $this->error('请先登录',U('/User/login'));}
                        $user_auth = session('user_auth');
                        $uid = $user_auth['uid'];
                        $phone = $user_auth['phone'];
                }
                $userinfo = M('user')->where("id = ".$uid)->find();
                
                $this->assign("userinfo",$userinfo);
		$this->display('user_center');
		
	}
	//会员信息页
	public function user_info(){
			$this->display('user_info');
		
	}
	//会员信息页
	public function user_info_edit(){
			$this->display('user_info_edit');
		
	}
	//会员升级页面
	public function user_upgrade(){
		$this->display('user_upgrade');
	}
	
	
	//雇员主页
	public function employer_center(){
		
		$this->display('employer_center');
		
	}
	
	
	
	/* 注册页面 */
	public function register($username = '', $password = '', $repassword = '', $email = '', $verify = ''){
        if(!C('USER_ALLOW_REGISTER')){
            $this->error('注册已关闭');
        }
		if(IS_POST){ //注册用户
			/* 检测验证码 */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}

			/* 检测密码 */
			if($password != $repassword){
				$this->error('密码和重复密码不一致！');
			}			

                        //echo $username;
                        $data = array(
                            "username"=>$username,
                            "password"=>md5($password),
                            "created_at"=>date("Y-m-d H:i:s",time()),
                            "status"=>'1',
                            "last_login_at"=>date("Y-m-d H:i:s",time())
                        );
                        
                        $check = M('user')->where('username = "'.$username.'"')->find();
                        if($check){
                            $this->error('此号码已存在~~~');
                        }
                        M('user')->add($data);
                        $this->success('注册成功！',U('login'));
			/* 调用注册接口注册用户 */
//            $User = new UserApi;
//			$uid = $User->register($username, $password, $email);
//			if(0 < $uid){ //注册成功
//				//TODO: 发送验证邮件
//				$this->success('注册成功！',U('login'));
//			} else { //注册失败，显示错误信息
//				$this->error($this->showRegError($uid));
//			}

		} else { //显示注册表单
			$this->display();
		}
	}

	/* 登录页面 */
	public function login($username = '', $password = '', $verify = ''){
		if(IS_POST){ //登录验证
			/* 检测验证码 */
			if(!check_verify($verify)){
				$this->error('验证码输入错误！');
			}
                        //echo $username;
                        $data = array(
                            "username"=>$username,
                            "password"=>md5($password),
                            "created_at"=>date("Y-m-d H:i:s",time()),
                            "status"=>'1',
                            "last_login_at"=>date("Y-m-d H:i:s",time())
                        );
                        
                        $check = M('user')->where('username = "'.$username.'" and password = "'.md5($password).'"')->find();
                        if($check){
                            $auth = array(
                                'uid'             => $check['id'],
                                'phone'        => $check['username']
                            );
                            
                            session('user_auth', $auth);
                            session('user_auth_sign', data_auth_sign($auth));
        
                            $this->success('登录成功！',U('Wap/Index/index'));
                        }else{
                            $this->error('账户错误，请重新输入');
                        }
                        
			/* 调用UC登录接口登录 */
//			$user = new UserApi;
//			$uid = $user->login($username, $password);
//			if(0 < $uid){ //UC登录成功
//				/* 登录用户 */
//				$Member = D('Member');
//				if($Member->login($uid)){ //登录用户
//					//TODO:跳转到登录前页面
//					$this->success('登录成功！',U('Home/Index/index'));
//				} else {
//					$this->error($Member->getError());
//				}
//
//			} else { //登录失败
//				switch($uid) {
//					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
//					case -2: $error = '密码错误！'; break;
//					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
//				}
//				$this->error($error);
//			}

		} else { //显示登录表单
			$this->display();
		}
	}

	/* 退出登录 */
	public function logout(){
		if(is_login()){
			D('Member')->logout();
			$this->success('退出成功！', U('User/login'));
		} else {
			$this->redirect('User/login');
		}
	}

	/* 验证码，用于登录和注册 */
	public function verify(){
		$verify = new \Think\Verify();
		$verify->entry(1);
	}

	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}


    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function profile(){
		if ( !is_login() ) {
			$this->error( '您还没有登陆',U('User/login') );
		}
        if ( IS_POST ) {
            //获取参数
            $uid        =   is_login();
            $password   =   I('post.old');
            $repassword = I('post.repassword');
            $data['password'] = I('post.password');
            empty($password) && $this->error('请输入原密码');
            empty($data['password']) && $this->error('请输入新密码');
            empty($repassword) && $this->error('请输入确认密码');

            if($data['password'] !== $repassword){
                $this->error('您输入的新密码与确认密码不一致');
            }

            $Api = new UserApi();
            $res = $Api->updateInfo($uid, $password, $data);
            if($res['status']){
                $this->success('修改密码成功！');
            }else{
                $this->error($res['info']);
            }
        }else{
            $this->display();
        }
    }

}
