<?php

namespace app\admin\model;

use think\Model;

class Users extends Model
{	
	//自动写入时间戳
	protected $autoWriteTimestamp = true;
	//关闭自动添加修改时间
	protected $updateTime = false;

    // 定义全局的查询范围
    protected function base($query)
    {
           $query->where('role','in',[1,2]);
    }

	/*
	角色获取器
	 */
	protected function getRoleAttr($value)
	{
		$status = ['1'=>'超级管理员','2'=>'普通管理员','3'=>'前台会员'];
		return $status[$value];
	}
    /*
    密码修改七
     */
    protected function setPasswordAttr($value)
    {
        return md5($value);
    }
	/*
	权限获取器
	 */
	protected function getStatusAttr($value)
	{
		$status = ['1'=>'<span class="layui-btn layui-btn-normal layui-btn-mini">已启用</span>','2'=>'<span class="layui-btn layui-btn-danger layui-btn-mini">已禁用</span>'];
		return $status[$value];
	}
	/*
	最有一次登陆时间获取 修改器
	 */
	protected function getLastlogtimeAttr($value)
	{
		return date('Y-m-d H:i:s',$value);
	}
	/*
	会员
	 */
	protected function scopeMember($query)
	{
		$query->where('status',4);
	}
    /*
    登陆验证
     */
    public function verLogin($data)
    {
    	//验证码验证
    	if(!captcha_check($data['captcha'])){
		//验证失败
		return xreturn('验证码错误',1);
		};
    	//账号验证
    	$result = $this->where('username|phone',$data['username'])->find();
    	if (empty($result)) {
    		return xreturn('账号不存在',1);
    	}
    	//密码验证
    	if ($result['password']!==md5($data['password'])) {
    		return xreturn('密码错误',1);
    	}
    	//验证状态
    	if ($result->getData('role')==3) {
    		return xreturn('此账号为普通会员，无法登陆后台',1);
    	}
    	if ($result->getData('status')==2) {
    		return xreturn('此账号已被禁用',1);

    	}

    	//所有验证通过，执行登陆
        $role = $result->getData('role');
        $status = $result->getData('status');
        $userinfo = $result->toArray();
        $userinfo['role'] = $role;
        $userinfo['status'] = $status;
    	session('userinfo',$userinfo);
    	//修改登陆次数，登陆时间，登陆地址
    	$ndata['lognum'] = $userinfo['lognum']+1;
    	$ndata['lastlogtime'] = time();
    	$ndata['lastlogaddr'] = ip2addr();
    	$this->save($ndata,['id'=>$userinfo['id']]);
    	return xreturn('登陆成功',0);
    }
    /*
    搜索分页
     */
    public function pageQuery()
    {
    	$where=[];
    	$data=[];
    	$userName = input('get.username');
    	$pagesize = input('pagesize/d')?input('pagesize/d'):10;
    	if (!empty($userName)) {
    		$where['username'] = ['like',"%$userName%"];
    		$data['username'] = $userName;
    	}
    	//获取数据
    	$res['num'] = $this->where($where)->count();
    	$res['list'] = $this->field(['id','phone','email','username','create_time','role','status'])
    					->where($where)
    					->order('create_time desc')
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
        $data['status']=1;
        $data['avatar']='logo.png';
        $result = $this ->validate('User.add')
                        ->allowField(true)
                        ->save($data);
        if ($result !==false) {
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
        if (!$this->get($id)) {
            return xreturn('错误，请重试',1);
        }
        if ($this->save(['status'=>2],['id'=>$id])>0) {
             return xreturn('已禁用');
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
        if (!$this->get($id)) {
            return xreturn('错误，请重试',1);
        }
        if ($this->save(['status'=>1],['id'=>$id])>0) {
             return xreturn('已启用');
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
        $result = $this->allowField(true)->validate('User.edit')->update($data);
        if ($result !==false) {
            return xreturn('成功');
        }else{
            return xreturn($this->getError(),1);
        }
    }
    /*
    执行删除
     */
    public function del($id)
    {
        $list = $this->find($id);
        if ($id===session('userinfo.id')) {
            return xreturn('此账号正在登陆',1);
        }elseif(session('userinfo.role')!==1){
            return xreturn('权限不够',1);
        }
        $list->delete();
        return xreturn('删除成功');
    }
}
