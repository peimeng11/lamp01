<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Nav extends Model
{
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
    //关闭自动添加修改时间
    protected $updateTime = false;
    protected $createTime = 'ctime';
	/*
	是否显示
	 */
	protected function getShowAttr($value)
	{
		$status = ['1'=>'<span class="layui-btn layui-btn-normal layui-btn-mini">显示</span>','2'=>'<span class="layui-btn layui-btn-danger layui-btn-mini">隐藏</span>'];
		return $status[$value];
	}
	
	 /*
    搜索分页
     */
    public function pageQuery()
    {
    	$pagesize = input('pagesize/d')?input('pagesize/d'):10;
    	
    	//获取数据
    	$res['num'] = $this->count();
    	$res['list'] = $this
    					->order('sort')
    					->paginate($pagesize);
    	$res['pagesize'] = $pagesize;
    	$res['pagenum'] = ceil($res['num']/$pagesize);
    	return $res;
    }

    /*
    执行添加
     */
    public function add()
    {
        $data = input('post.');
        $result = $this ->allowField(true)
                        ->save($data);
        if ($result !==false) {
            return xreturn('添加成功');
        }else{
            return xreturn('添加失败',1);
        }
    }
    /*
    隐藏
     */
    public function none($id)
    {
        if (empty($id)) {
            $id = input('get.id');
        }
        if (!$this->find($id)) {
            return xreturn('错误，请重试',1);
        }
        if ($this->save(['show'=>2],['id'=>$id])>0) {
             return xreturn('已隐藏');
        }else{
             return xreturn('失败',1);
        }
       
    }
     /*
    显示
     */
    public function show($id)
    {
        if (empty($id)) {
            $id = input('get.id');
        }
        if (!$this->find($id)) {
            return xreturn('错误，请重试',1);
        }
        if ($this->save(['show'=>1],['id'=>$id])>0) {
             return xreturn('已显示');
        }else{
             return xreturn('失败',1);
        }
    }
     /*
    执行修改
     */
    public function xupdate()
    {
        $data = input('post.');
        $result = $this->update($data);
        if ($result !==false) {
            return xreturn('成功');
        }else{
            return xreturn('失败',1);
        }
    }
}