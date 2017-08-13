<?php 
namespace plugs\FocusImg\model;
use think\Model;
class FocusCat extends Model{

	public function testm(){
		$object = $this->select();
		return $this->objToArr($object);
	}

	protected function objToArr($obj){
		foreach ($obj as $key => $value) {
			$data[$key] = $value->data;
		}
		return $data;
	}
}