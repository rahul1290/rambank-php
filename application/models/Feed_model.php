<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Feed_model extends CI_Model {
 
	function feed($data){
		$this->db->select('date_format(created_at,"%Y-%m-%d") as created_at');
		$db_records = $this->db->get_where('feed_detail',array('account_no'=>$data['account_no'],'status'=>1))->result_array();
		
		if($db_records[0]['created_at'] == date('Y-m-d',strtotime($data['created_at']))){
		//if($db_records[0]['created_at'] == $data['created_at']){
			$this->db->query("update feed_detail set today = today + 1,month = month + 1,online_total = online_total + 1,created_at='".date('Y-m-d H:i:s')."' where account_no = ".$data['account_no']);
		} else {
			if(date('n',strtotime($data['created_at'])) == date('n',strtotime($db_records[0]['created_at']))){
				$this->db->query("update feed_detail set today =1 ,month = month + 1,online_total = online_total + 1,created_at='".date('Y-m-d H:i:s')."' where account_no = ".$data['account_no']);
			} else {
				$this->db->query("update feed_detail set today =1 ,month = 1,online_total = online_total + 1,created_at='".date('Y-m-d H:i:s')."' where account_no = ".$data['account_no']);
			}
		}
		return true;
	}
}