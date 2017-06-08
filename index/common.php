<?php
use think\Db;

// 应用公共文件
function get_table(){
    $database = config('database.database');
    $query = 'select TABLE_NAME as name from information_schema.TABLES where TABLE_SCHEMA="'.$database.'"and TABLE_TYPE="BASE TABLE"';
    $list = Db::query($query);
    return $list;
}
/*
 * 获得列名
 */
/*
function get_column($table){
    $database = config('database.database');
    $query = 'select COLUMN_NAME as Field,COLUMN_COMMENT as Comment,COLUMN_KEY as `Key` from information_schema.COLUMNS where TABLE_SCHEMA="'.$database.'" and TABLE_NAME="'.$table.'"';
  //  dump($query);exit;
    $list = Db::query($query);
    return $list;
}

function get_column1($table){
    $columns = Db::query("SHOW FULL COLUMNS FROM ".$table);
    return $columns;
}
*/
function get_column($table){
    $database = config('database.database');
    $query = 'select COLUMN_NAME as Field,COLUMN_COMMENT as Comment from information_schema.COLUMNS where TABLE_SCHEMA="'.$database.'" and TABLE_NAME="'.$table.'" and COLUMN_KEY!="PRI"';
    //  dump($query);exit;
    $list = Db::query($query);
    return $list;
}
//table转为类名
function table_to_class($table){
    if(empty($table)){
        $this->error('表不存在');
    }
    $prefix = config('database.prefix');
    if(empty($prefix)){
        if(strstr($table, '_')){
            $data = explode("_",$table);
            foreach ($data as $v){
                $re[] = ucfirst($v);
            }
            $datas = implode('', $re);
        }else{
            $datas = ucfirst($table);
        }
    }else{
        if(strstr($table, $prefix)){
            $data = explode($prefix,$table);
            if(strstr($data[1], '_')){
                $data1 = explode("_",$data[1]);
               // print_r($data1);exit;
                foreach ($data1 as $v){
                    $re[] = ucfirst($v);
                }
                $datas = implode('', $re);
            }else{
                $datas = ucfirst($table);
            }
        }else{
            if(strstr($table, '_')){
                $data = explode("_",$table);
                foreach ($data as $v){
                    $re[] = ucfirst($v);
                }
                $datas = implode('', $re);
            }else{
                $datas = ucfirst($table);
            }
        }
    }
    return $datas;
}