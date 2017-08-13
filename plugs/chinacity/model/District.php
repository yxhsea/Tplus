<?php

namespace plugs\chinacity\model;
use Think\Model;

/**
 * 全国城市乡镇信息模型
 */
class District extends Model{
	
	public function _list($map){
		$order = 'id ASC';
		$data = $this->where($map)->order($order)->select();
		return $data;
	}
}
