<?php
namespace app\admin\controller;

use app\admin\model\Type as M;
use think\Controller;
class Type extends Base
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
    	$m = new M();
    	$list = $m->order('concat(path,id)')->select();
        return view('add',['list'=>$list]);
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
        return $m->forbidden($id);
    }
    /**
     * 启用
     */
    public function show($id)
    {
        $m = new M();
        return $m->star($id);
    }
    /**
     * 进制删除
     */
    public function undel($id)
    {
        $m = new M();
        return $m->undel($id);
    }
    /**
     * 删除
     */
    public function del($id)
    {
        $m = new M();
        return $m->del($id);
    }
    /**
     * 加载修改页面
     */
    public function edit($id)
    {
        $m = new M();
        $select = 1;
        if ($m->where('pid',$id)->find()) {
        	$select=2;
        }
        $type = $m->order('concat(path,id)')->select();
        $list = $m->field('id,pid,name')->find($id);
        if (empty($list)) {
            return xreturn('失败，请重试',1);  
        }
        return view('edit',['list'=>$list,'type'=>$type,'select'=>$select]);
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