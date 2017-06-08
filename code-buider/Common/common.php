<?php
use think\Db;
// 应用公共文件

//获取表名列表
function get_table_name_list(){
	$dbType = config('type');
	if(in_array($dbType, array('mysql', 'mysqli'))){
		$dbName = config('database');
		$result = [];
		$tempArray = Db::query("select table_name from information_schema.tables where table_schema='".$dbName."' and table_type='base table'");
		foreach($tempArray as $temp){
			$result[] = $temp['table_name'];
		}
		return $result;
	}else{ 
		$this->error('数据库类型不支持');
	} 
}

//获取列名列表
function get_table_column($tableName){
	$dbType = config('type');
	if(in_array($dbType, array('mysql', 'mysqli'))){
		$dbName = config('database');
		$result = Db::query("select * from information_schema.columns where table_schema='".$dbName."' and table_name='".config('prefix').$tableName."'");
		$result = changeColumCase($result); //修正information_schema大小写问题
		return $result;
	}else{ 
	   $this->error('数据库类型不支持');
	} 
}

// 转化键名为小写-用于修正mysql information_schema返回键名在不同环境下大小写不同的问题
//$columInfoArray 返回的表信息
function changeColumCase($columInfoArray){
	foreach($columInfoArray as $columInfo){
		$res[] = array_change_key_case($columInfo, CASE_LOWER);
	}
	return $res;
}