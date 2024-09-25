<?php
class table_log extends discuz_table{
    public function __construct() {
        $this->_table="v50miner_logs";
        $this->_pk="id";
    }
    public function add_log($uid,$itemtxt,$time){
        DB::insert($this->_table,[
            'uid'=>$uid,
            'items'=>$itemtxt,
            'mineat'=>$time
        ]);
    }
    public function query_page($uid,$start,$per){
        return DB::fetch_all('SELECT * FROM %t WHERE uid = %d ORDER BY mineat DESC LIMIT %d,%d' ,[$this->_table,$uid,$start,$per],'id');
    }
    public function query_page_all($start,$per){
        return DB::fetch_all('SELECT * FROM %t ORDER BY mineat DESC LIMIT %d,%d' ,[$this->_table,$start,$per],'id');
    }
    public function count(){
        return DB::result_first('SELECT COUNT(*) FROM %t' ,[$this->_table]);
    }
    public function count_user($uid){
        return DB::result_first('SELECT COUNT(*) FROM %t WHERE uid = %d' ,[$this->_table,$uid]);
    }
}