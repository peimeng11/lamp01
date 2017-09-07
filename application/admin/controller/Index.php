<?php
namespace app\admin\controller;
use app\admin\model\Users as M;
class Index extends Base
{
    /*
    加载首页
     */
    public function index()
    {
        return view('index');
    }
    /*
    首页默认内容区域
     */
    public function welcome()
    {
        return view('welcome');
    }
     /*
    加载弹出层
     */
    public function userinfo($id)
    {   
        $m = new M();
        $list = $m->find($id);
        return view('userinfo',['list'=>$list]);
    }
}
