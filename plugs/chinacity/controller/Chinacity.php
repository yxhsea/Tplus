<?php
namespace plugs\chinacity\controller;
use app\system\controller\Plugs;
use think\Db;
use think\Request;
class Chinacity extends Plugs{

	/* 载入初始的值!!! */
	public function loadArea($province=0, $city=0, $district=0, $community=0){
		/*$province = 1;
		$city = 37;
		$district = 568;*/
		$data['province'] = Db::name('district')->find($province);

		$data['city'] = Db::name('district')->where(array('upid'=>$province,'id'=>$city))->find();

		$data['district'] = Db::name('district')->where(array('upid'=>$city,'id'=>$district))->find();

		$data['community'] = Db::name('district')->where(array('upid'=>$district,'id'=>$community))->find();
		$area = $this->getAreaList(0);
		$list  = array();
		$list['province'] = "<option value =''>-省份-</option>";
		foreach($area as $val){
			$list['province'] .= "<option value='".$val['id']."'";
			if($province == $val['id']) $list['province'].="selected=selected >";
			else  $list['province'] .= ">";
			$list['province'] .= $val['name'];
			$list['province'] .= "</option>";
		}

		if(!empty($data['city'])){
			$area = $this->getAreaList($province);
			$list['city'] = "<option value =''>-城市-</option>";
			foreach($area as $val){
				$list['city'] .= "<option value='".$val['id']."'";
				if($city == $val['id']) $list['city'].="selected=selected >";
				else  $list['city'] .= ">";
				$list['city'] .= $val['name'];
				$list['city'] .= "</option>";
			}
		}

		if(!empty($data['district'])){
			$area = $this->getAreaList($city);
			$list['district'] = "<option value =''>-州县-</option>";
			foreach($area as $val){
				$list['district'] .= "<option value='".$val['id']."'";
				if($district == $val['id']) $list['district'].="selected=selected >";
				else  $list['district'] .= ">";
				$list['district'] .= $val['name'];
				$list['district'] .= "</option>";
			}
		}

		if(!empty($data['community'])){
			$area = $this->getAreaList($district);
			$list['community'] = "<option value =''>-乡镇-</option>";
			foreach($area as $val){
				$list['community'] .= "<option value='".$val['id']."'";
				if($community == $val['id']) $list['community'].="selected=selected >";
				else  $list['community'] .= ">";
				$list['community'].= $val['name'];
				$list['community'].= "</option>";
			};
		}
		return $list;
	}

	/* 城市列表 */
	public function getAreaList($upid = 0){
		$Request = Request::instance();
		if($upid == 0){
			$upid = $Request->param('upid')?$Request->param('upid'):0;
		}

		if($Request->isAjax()){
			$list = Db::name('district')->where(array('upid'=>$upid))->select();
			$data = '';
			foreach($list as $val){
				$data .= "<option value='".$val['id']."'>";
				$data .= $val['name'];
				$data .= "</option>";
			}
			return json($data);
		}else{
			$res = Db::name('district')->where(['upid'=>$upid])->select();
			return $res;
		}


	}
}