<?php
namespace app\admin\validate;
use think\Validate;

class User extends Validate
{
	protected $rule = [
		['username','unique:users','用户名已存在'],
		['phone','unique:users','该手机号已注册'],
		['email','unique:users','邮箱已存在'],
	];
	protected $scene = [
		'add'=>['username','phone','email'],
		'edit'=>['username','phone','email'],
	];
}