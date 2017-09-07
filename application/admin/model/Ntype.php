<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Ntype extends Model
{
	//自动写入时间戳
    protected $autoWriteTimestamp = true;
    //关闭自动添加修改时间
    protected $updateTime = false;
    protected $createTime = 'ctime';
	/*
	是否可删除
	 */
	protected function getDelAttr($value)
	{
		$status = ['1'=>'<span class="layui-btn layui-btn-normal layui-btn-mini">YES</span>','2'=>'<span class="layui-btn layui-btn-danger layui-btn-mini">NO</span>'];
		return $status[$value];
	}
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
    	$where=[];
    	$data=[];
    	$name = input('get.name');
    	$pagesize = input('pagesize/d')?input('pagesize/d'):10;
    	if (!empty($name)) {
    		$where['name'] = ['like',"%$name%"];
    		$data['name'] = $name;
    	}
    	//获取数据
    	$res['num'] = $this->where($where)->count();
    	$res['list'] = $this
    					->where($where)
    					->order('id desc')
    					->paginate($pagesize,'',['query'=>$data]);
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
    禁用
     */
    public function forbidden($id)
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
    启用
     */
    public function star($id)
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
    启用
     */
    public function undel($id)
    {
        if (empty($id)) {
            $id = input('get.id');
        }
        if (!($list = $this->find($id))) {
            return xreturn('错误，请重试',1);
        }
        
		$save=1;
        if ($list->getData('del')==1){
        	$save=2;
		}

		if($this->save(['del'=>$save],['id'=>$id])){
             return xreturn('成功');
        }else{
             return xreturn('失败',1);
        }
    }
    /*
    执行删除
     */
    public function del($id)
    {
        $list = $this->find($id);
        if ($list->getData('del')==2) {
            return xreturn('此条信息进制删除',1);
        }
        $list->delete();
        return xreturn('删除成功');
    }
     /*
    执行修改
     */
    public function xupdate()
    {
        $data = input('post.');
        $result = $this->update($data);
        if ($result !=false) {
            return xreturn('成功');
        }else{
            return xreturn('失败',1);
        }
    }
}