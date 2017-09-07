<?php
namespace app\admin\controller;
use think\Controller;

/**
 * 基类控制器，判断是否登陆，权限管理等操作
 */

class Base extends Controller
{
	/*
	初始化方法
	 */
	public function _initialize()
	{
		if (empty(session('userinfo'))) {
			$this->redirect('login/login');
		}
	}
	/*
	图片上传 返回json格式  
	{
	  "code": 0
	  ,"msg": ""
	  ,"data": {
	    "src": "./images/logo.png"
	  }
	}
	return json(['code'=>0,'msg'=>'','data'=>['src'=>'/img/1.png']]);
	 */
	public function upload($filed='file',$dir='temp')
	{
		$file = request()->file($filed);
		$info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . "/static/admin/uploads/$dir");
		if($info){
			//返回json格式
			return json(['code'=>0,'msg'=>'','data'=>['src'=>"/static/admin/uploads/$dir/".$info->getFilename(),'title'=>$info->getFilename()]]);
		}else{
			// 上传失败获取错误信息
			return json(['code'=>1,'msg'=>$file->getError(),'data'=>[]]);
		}
	}
}