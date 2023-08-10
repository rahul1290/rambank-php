<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Feed extends REST_Controller {
	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation','session']);
		$this->load->helper(['url', 'language']);
        $this->load->library(array('session'));
		$this->load->model(array('Feed_model','Auth_model'));
	}
	
	function online_recordfeeder_get(){
		$results = $this->db->query("SELECT DISTINCT(account_no) FROM `feeds`WHERE account_no not in(SELECT DISTINCT(account_no) FROM `feed_detail`)")->result_array();
		if(count($results)>0){
			$insertdata = array();
			foreach($results as $result){
				$temp = array();
				$temp['account_no'] = $result['account_no'];
				$temp['total_bal'] = 0;
				$temp['online_total'] = 0;
				$temp['created_at'] = date('Y-m-d H:i:s');
				$insertdata[] = $temp;
			}
			$this->db->insert_batch('feed_detail',$insertdata);
		}
	}
	
	function feed_post(){
	    $data['account_no'] = trim($this->post('account_no'));
	    $data['feed'] = $this->post('feed');
		$data['device_id'] = trim($this->post('device_id'));
		
	    $data['created_at'] = $this->post('created_at') ?? date('Y-m-d H:i:s');
		
	    $this->db->select('*');
		$result = $this->db->get_where('users',array('device_id'=>$data['device_id'],'account_no'=>$data['account_no'],'status'=>1))->result_array();
// 		echo json_encode(array('query'=>$this->db->last_query(),'status'=>200));
// 		die;
		if(count($result)>0){ 
			if($this->Feed_model->feed($data)){
				$data1['account_no'] = $data['account_no'];
				$result1 = $this->Auth_model->is_active($data1);
				$result = $result1[0];
				$last_update = $this->Auth_model->last_update($data['account_no']);
				
				$feed_detail = $this->Auth_model->get_feed_detail($data['account_no']);
				$result['month_count'] = $feed_detail[0]['this_month'];
				$result['total_count'] = $feed_detail[0]['total'];
				$result['today_count'] = $feed_detail[0]['today'];
				if(count($last_update)>0){ 
					$result['last_update'] = $last_update[0]['last_update'];    
				} else {
					$result['last_update'] = date('Y-m-d H:i:s');
				}
				$this->response($result,200);
			} 
		} else {
			$this->response("आप किसी अन्य मोबाइल पर लॉग इन हैं ,\n कृप्या पुनः लॉग इन करें|",500);
		}
	}
	
	
	
	function feed_bulk_v2_post($force_logout = false){
        $account_no     = trim($this->post('account_no'));
        $today_count    = trim($this->post('today_count'));
        $month_count    = trim($this->post('month_count'));
        $total          = trim($this->post('total'));
        $device_id      = trim($this->post('device_id'));
        
        $this->db->insert('testing',array('account'=>$account_no,'total'=>$total,'created_at'=>date("Y-m-d H:i:s")));
        $lastinsertId = $this->db->insert_id();
        // if(intval($total) > 1000){
        //     $this->response('total not grater then 500',502);
        //     exit;
        // }
        $this->db->select('*');
        if($force_logout){
            $result = $this->db->get_where('users',array('account_no'=>$account_no,'status'=>1))->result_array();    
        } else {
            $result = $this->db->get_where('users',array('device_id'=>$device_id,'account_no'=>$account_no,'status'=>1))->result_array();
            if(!count($result)>0){
                $result = $this->db->get_where('users',array('account_no'=>$account_no,'status'=>1))->result_array();        
            }
        }
        
        if(count($result)>0){
            if($force_logout){
                $bulkfeed = array(array('1','2')); 
            }else {
                $bulkfeed = $this->db->query("select * from feed_detail WHERE account_no=".$account_no." AND (bulk_upload_at <='".date('Y-m-d H:i:s',strtotime("-4 minutes"))."' OR bulk_upload_at IS NULL)  AND status= 1")->result_array();   
            }
            if(count($bulkfeed)){
                $this->db->query("update feed_detail set today = today + ".$today_count.",month = month + ".$month_count.",online_total = online_total + ".$total.",created_at = '".date('Y-m-d H:i:s')."',bulk_upload_at = '".date('Y-m-d H:i:s')."' where account_no = ".$account_no);
                
                $this->db->where('id', $lastinsertId);
                $this->db->update('testing', array('bulk'=>1));
                
                       
                $this->db->select('*');
                $result1 = $this->db->get_where('users',array('account_no'=>$account_no))->result_array();
                $result =  $result1[0];
    	        $last_update = $this->Auth_model->last_update($result['account_no']);
    	        
    	        $feed_detail = $this->Auth_model->get_feed_detail($result['account_no']);
    	        $result['month_count'] = $feed_detail[0]['this_month'];
    	        $result['total_count'] = $feed_detail[0]['total'];
    	        $result['today_count'] = $feed_detail[0]['today'];
    	        if(count($last_update)>0){ 
    	            $result['last_update'] = $last_update[0]['last_update'];    
    	        } else {
    	            $result['last_update'] = date('Y-m-d H:i:s');
    	        }
                $this->response($result,200);
            }
            else {
                $this->db->select('*');
                $bulkfeed = $this->db->get_where('feed_detail',array('account_no'=>$account_no,'status'=>1))->result_array();
                
                $datetime1 = new DateTime($bulkfeed[0]['bulk_upload_at']);
                $datetime2 = new DateTime(date('Y-m-d H:i:s'));
                $interval = $datetime1->diff($datetime2);
                $time = 3 - $interval->format('%i');
                
                $second = 60 -  $interval->format('%s');
                $time .= ':'.$second.' मिनट';
                
                $this->response('रिकॉर्ड अपलोड करने के लिए कृपया  '.$time.' की  प्रतीक्षा करें|',501);
            }
        } else {
            $this->response("आप किसी अन्य मोबाइल पर लॉग इन हैं ,\n कृप्या पुनः लॉग इन करें|",500);
        }
    }
	
	
	function feed_bulk_post(){ 
	    $feeds = json_decode($this->post('jsonData'),true);
		$data['device_id'] = $this->post('device_id');
		
		$this->db->select('*');
		$result = $this->db->get_where('users',array('device_id'=>$data['device_id'],'account_no'=>$feeds[0]['account_no'],'status'=>1))->result_array(); 
		
		if(count($result)>0){
// 			$this->db->select('*');
// 			$bulkfeed = $this->db->get_where('feed_detail',array('account_no'=>$feeds[0]['account_no'],'bulk_upload_at <= '=>date('Y-m-d H:i:s',strtotime("-15 minutes")),'status'=>1))->result_array();
		
		    $bulkfeed = $this->db->query("select * from feed_detail WHERE account_no=".$feeds[0]['account_no']." AND (bulk_upload_at <='".date('Y-m-d H:i:s',strtotime("-15 minutes"))."' OR bulk_upload_at IS NULL)  AND status= 1")->result_array();
		    	
			if(count($bulkfeed)){
				if(count($feeds)>0){	        
					$account_no = $feeds[0]['account_no'];
					((int)count($feeds) > 500)? $today =  500 : $today = (int)count($feeds);
					((int)count($feeds) > 500)? $month = 500 : $month = (int)count($feeds);
					((int)count($feeds) > 500)? $online_total = 500 : $online_total = (int)count($feeds);
					$this->db->query("update feed_detail set today = today + ".$today.",month = month + ".$month.",online_total = online_total + ".$online_total.",created_at = '".date('Y-m-d H:i:s')."',bulk_upload_at = '".date('Y-m-d H:i:s')."' where account_no = ".$account_no);
					$this->response('success',200);
				}
			} else {
				$this->db->select('*');
				$bulkfeed = $this->db->get_where('feed_detail',array('account_no'=>$feeds[0]['account_no'],'status'=>1))->result_array();
				
				$datetime1 = new DateTime($bulkfeed[0]['bulk_upload_at']);
				$datetime2 = new DateTime(date('Y-m-d H:i:s'));
				$interval = $datetime1->diff($datetime2);
				$time = 15 - $interval->format('%i');
				
				$second = 60 -  $interval->format('%s');
				$time .= ':'.$second.' मिनट';
				
				$this->response('रिकॉर्ड अपलोड करने के लिए कृपया  '.$time.' की  प्रतीक्षा करें|',501);
			}
		} else {
			$this->response("आप किसी अन्य मोबाइल पर लॉग इन हैं ,\n कृप्या पुनः लॉग इन करें|",500);
		}
	}
	
	
	function feed_bulktest_post(){
	    $feeds = json_decode($this->post('jsonData'),true);
	    
	    $insert_data = array();
	    if(count($feeds)>0){
	        foreach($feeds as $feed){
	        $temp = array();
	            $temp['created_at'] = $feed['created_at'];
    	        $temp['account_no'] = $feed['account_no'];
    	        $temp['f_text'] = $feed['f_text'];
    	   $insert_data[] = $temp;
	        }
	   print_r($insert_data); die;  
	   if($this->db->insert_batch('feeds',$insert_data)){
	    $this->response('success',200);
	   } else {
	    $this->response('failed',500);   
	   }
	    }
	}
}