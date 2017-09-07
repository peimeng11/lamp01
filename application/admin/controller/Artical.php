<?php
namespace app\admin\controller;
use app\admin\model\Artical as M;
use app\admin\model\Arttype as aM;

class Artical extends Base
{
	/*
	浏览新闻类别
	 */
	public function index()
	{
		$m = new M();
		$list = $m->order('id desc')->paginate(10);
		return view('index',['list'=>$list]);
	}

	/*
	加载添加页面
	 */
	public  function add($info='')
	{
		$am = new aM();
		$list = $am->field('id,name,pid')->select();
		$pid = [];
		foreach ($list as $k=> $v) {

			$pid[] =$v->getData('pid');;
		}
		$linfo = $am->where('id','not in',$pid)->select();
		return view('add',['list'=>$linfo,'info'=>$info]);
	}

	/*
	执行添加
	 */
	public  function save()
	{
		$m = new M(input('post.'));
		$m->allowField(true)->save();
		if ($m->id>0) {
            $this->redirect('add',['info'=>'添加成功']);
        }else{
            $this->redirect('add',['info'=>'添加失败']);
        }
	}
}
