<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Artical extends Model
{
	protected function getTidAttr($value)
	{
		return DB::name('arttype')->field('name')->find($value)['name'];
	}
}