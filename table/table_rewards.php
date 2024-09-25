<?php
class table_rewards extends discuz_table{
    public function __construct() {
        $this->_table="v50miner_rewards";
        $this->_pk="id";
    }
    public function get_all_rewards(){
        return DB::fetch_all('SELECT * FROM %t',[$this->_table],'id');
    }
    public function add_reward($text,$lvl,$img,$chance,$action){
        DB::insert($this->_table,[
            'text'=>$text,
            'lvl'=>$lvl,
            'img'=>$img,
            'chance'=>$chance,
            'action'=>$action
        ]);
    }
    public function set_reward($id,$text,$lvl,$img,$chance,$action){
        DB::update($this->_table,[
            'text'=>$text,
            'lvl'=>$lvl,
            'img'=>$img,
            'chance'=>$chance,
            'action'=>$action
        ],"id=$id");
    }
}