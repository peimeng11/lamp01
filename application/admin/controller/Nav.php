<?php
namespace app\admin\controller;

use app\admin\model\Nav as M;
use think\Controller;
class Nav extends Base
{
	/**
     * 列表查看
     */
    public function index()
    {	
        //查询数据
        $m = new M();
        $res = $m->pageQuery();
        return view('index',$res);
    }
    /**
     * 加载添加页面
     */
    public function add()
    {	
        return view('add');
    }
    /**
     * 添加
     */
    public function save()
    {
        $m = new M();
        return $m->add();
    }
    /**
     * 禁用
     */
    public function hidden($id)
    {
        $m = new M();
        return $m->none($id);
    }
    /**
     * 启用
     */
    public function show($id)
    {
        $m = new M();
        return $m->show($id);
    }
    /**
     * 加载修改页面
     */
    public function edit($id)
    {
        $m = new M();
        $list = $m->field('id,name,desc,link,sort')->find($id);
        if (empty($list)) {
            return xreturn('失败，请重试',1);  
        }
        return view('edit',['list'=>$list]);
    }
    /**
     * 保存更新的资源
     */
    public function update()
    {
        $m = new M();
        return $m->xupdate();
    }

}