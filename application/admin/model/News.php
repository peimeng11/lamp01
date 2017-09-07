<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class News extends Model
{
    //自动写入时间戳
    protected $autoWriteTimestamp = true;
    //关闭自动添加修改时间
    protected $updateTime = false;
    protected $createTime = 'ctime';


    protected function getTidAttr($value)
    {
        return DB::name('ntype')->field('name')->find($value)['name'];
    }
    protected function getUidAttr($value)
    {
        return DB::name('users')->field('username')->find($value)['username'];
    }

    //文章列表页截取关键字
    protected function getContentAttr($value)
    {
        //去除html标记
        return mb_substr(strip_tags($value),0,20,'utf-8').'...';
    }
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
    是否置顶
     */
    protected function getStickAttr($value)
    {
        $status = ['1'=>'<span class="layui-btn layui-btn-normal layui-btn-mini">YES</span>','2'=>'<span class="layui-btn layui-btn-danger layui-btn-mini">NO</span>'];
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
    		$where['title|content'] = ['like',"%$name%"];
    		$data['name'] = $name;
    	}
    	//获取数据
    	$res['num'] = $this->where($where)->count();
    	$res['list'] = $this
    					->where($where)
    					->order('ctime desc')
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
        $data['uid'] = session('userinfo.id');
        $result = $this ->allowField(true)
                        ->save($data);
        if ($result !==false) {
            $file = ROOT_PATH . 'public' . DS . '/static/admin/uploads/temp/'.$data['newsimg'];
            $newFile = ROOT_PATH . 'public' . DS . '/static/admin/uploads/news/'.$data['newsimg'];
            copy($file,$newFile);
            return xreturn('添加成功');
        }else{
            return xreturn($this->getError(),1);
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
    置顶
     */
    public function stick($id)
    {
        if (empty($id)) {
            $id = input('get.id');
        }
        if (!($list = $this->find($id))) {
            return xreturn('错误，请重试',1);
        }
        
        $save=1;
        if ($list->getData('stick')==1){
            $save=2;
        }

        if($this->save(['stick'=>$save],['id'=>$id])){
             return xreturn('成功');
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
        // dump($data);die();
        unset($data['file']);
        $field = ['title','newsimg','content','stick','tid'];
        if (empty($data['newsimg'])) {
            unset($data['newsimg']);
            
            unset($field['newsimg']);
        }else{
            $list = $this->field('newsimg')->find($data['id']);
            @unlink(ROOT_PATH . 'public' . DS . '/static/admin/uploads/news/'.$list['newsimg']);   
            $file = ROOT_PATH . 'public' . DS . '/static/admin/uploads/temp/'.$data['newsimg'];
            $newFile = ROOT_PATH . 'public' . DS . '/static/admin/uploads/news/'.$data['newsimg'];
            copy($file,$newFile);
        }
        $result = $this->allowField($field)->update($data);
        if ($result !==false) {
            return xreturn('成功');
        }else{
            return xreturn('失败',1);
        }
    }
}