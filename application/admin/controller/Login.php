<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Users as M;
/**
 * 登陆控制器
 * 登陆
 * 注册
 * 退出
 * 继承Controller基类控制器
 */

class Login extends Controller
{
	/*
	加载登陆页面
	 */
	public function login()
	{
		return view('login');
	}

	/*
	执行登陆
	 */
	public function doLogin()
	{
		$data = input('post.');//接收数据
		$m = new M();//实例化模型Users
		//登陆验证,返回错误信息
		return $m->verLogin($data);
	}

	/*
	执行退出
	 */
	public function logout()
	{
		session('userinfo',null);
		$this->redirect('login');
	}
	public function ms()
	{
		$code = '9871';
		session('code',$code);
		echo sendTemplateSMS("15110495729",array($code,'1'),"1");
	}
}