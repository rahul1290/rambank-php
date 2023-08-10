<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Auth extends REST_Controller {
	public $data = [];

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation','session']);
		$this->load->helper(['url', 'language']);
        $this->load->library(array('session'));
		$this->load->model('Auth_model');
	}
	
	
	//cron job update today as Zero
	function feeds_get(){
	    $result = $this->db->query("select * from feeds")->result_array();
	    print_r($result);
	}
	function feed_get(){
	    $result = $this->db->query("select * from feed_detail")->result_array();
	    print_r($result);
	}
	function cron_get(){
	    if(date('H',strtotime(date('Y-m-d H:i:s'))) == 00){
			$this->db->insert('feeds',array('f_text'=>'ram','account_no'=>160,'created_at'=>date('Y-m-d H:i:s'),'status'=>1));
	    } 
	}
	function today_get(){
	    if(date('H',strtotime(date('Y-m-d H:i:s'))) == 00){
	        $this->db->query("UPDATE feed_detail SET today = 0");
	    }
	}
	//cron job update month as Zero
	function month_get(){
		$this->db->query("UPDATE `feed_detail` SET `month` = 0");
	}
	
	function device_check_post(){
		$data['account_no'] = $this->post('account_no');
		$data['device_id'] = $this->post('device_id');
		
		$this->db->select('*');
		$result = $this->db->get_where('users',array('device_id'=>$data['device_id'],'account_no'=>$data['account_no'],'status'=>1))->result_array();
		if(count($result)>0){
			$this->response($result,200);
		} else {
			$this->response("आप किसी अन्य मोबाइल पर लॉग इन हैं ,\n कृप्या पुनः लॉग इन करें|",500);
		}
		
	}
	
	function version_get(){
        $this->db->select('*');
        $result = $this->db->get_where('app_version',array('status'=>1))->result_array();
        $current_date = date('Y-m-d');
        $app_release = date("Y-m-d", strtotime($result[0]['release_date']));
        $diff = strtotime($current_date) - strtotime($app_release); 
        $released_days = abs(round($diff / 86400));
        if($result){
            $this->response(array('data'=>$result,'released_days'=>$released_days),200);
        }else{
            $this->response(array('msg'=>'record not found'),500);
        }
    }

	function totalCount_post(){
	    $this->db->select('(select count(*) from users where status = 1) total_users,(sum(total_bal) + sum(online_total)) as total_ram_naam');
        $details = $this->db->get_where('feed_detail',array('status'=>1))->result_array();
		
        $result = array();
        $result['total_users'] = $details[0]['total_users'];
        $result['total_ram_naam'] = (int)$details[0]['total_ram_naam'];
        
	    if(count($result) > 0){
	        $this->output->cache(15);
	        $this->response($result,200);
	    }else{
	        $this->response(array('msg'=>'Record not found.'),500);
	    }
	    
	}
	
	function logout_post(){
		$data['account_no'] = $this->post('identity');
		$this->db->where(array('account_no'=>$data['account_no'],'status'=>1));
		$result = $this->db->update('users',array('device_id'=>null));
		if($result){
			$this->response(array('msg'=>'Log out successfully.'),200);
		} else {
			$this->response(array('msg'=>'Log out failed.'),500);
		}
	}
	
	function login_post(){
	    $data['identity'] = trim($this->post('identity'));
	    $data['password'] = trim($this->post('password'));
	    $data['device_id'] = trim($this->post('device_id'));
	    $data['device_token'] = trim($this->post('device_token'));
	    $result1 = $this->Auth_model->login($data);
	    
	    if($result1 == '1'){
			$this->response(array('msg'=>'कुछ गलत हो गया। कृपया पुन: प्रयास करें।'),500);
		}
		if($result1 == '2'){
			$this->response(array('msg'=>'आप पहले से ही किसी अन्य डिवाइस  मे लॉग इन हैं।'),501);
		}
		else{ 
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
	}
	
	function force_login_post(){
	    $data['identity'] = trim($this->post('identity'));
	    $data['password'] = trim($this->post('password'));
	    $data['device_id'] = trim($this->post('device_id'));
	    $result1 = $this->Auth_model->force_login($data);
	    
	    if($result1){
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
	}
	
	
	
	function is_active_post(){
	    $data['account_no'] = trim($this->post('account_no'));
		
			$result1 = $this->Auth_model->is_active($data);
			if(count($result1)){
				$result =  $result1[0];
				$this->db->select('today as today_count,month as month_count,(total_bal+online_total) as total_count,created_at as last_update');
				$detail = $this->db->get_where('feed_detail',array('account_no'=>$data['account_no']))->result_array();
				$result['month_count'] = $detail[0]['month_count'];
				$result['total_count'] = $detail[0]['total_count'];
				$result['today_count'] = $detail[0]['today_count'];
				$result['last_update'] = $detail[0]['last_update'];
				
				if(count($result)>0){
					$this->response($result,200);
				} else {
					$this->response(array('msg'=>'Not logined.'),500);
				}
			}
		
	}
	
	function registration_post(){
	    $data['name'] = strtolower($this->post('name'));
	    $data['u_name'] = strtolower(trim($this->post('user_name')));
	    $data['contact_no'] = trim($this->post('contact_no'));
	    $data['address'] = trim($this->post('address'));
    	$data['photo'] = $this->post('photo');
	    $data['gender'] = $this->post('gender');
		$data['device_id'] = $this->post('device_id');
		$data['device_token'] = $this->post('device_token');
	    $data['password'] = $this->post('password');
	    $data['mail_id'] = $this->post('mail');
	    
	    $account_no = $this->Auth_model->registration($data);
	    if($account_no != ''){
	        $data1['account_no'] = $account_no;
	        $result1 = $this->Auth_model->is_active($data1);
	        $result = $result1[0];
	        $last_update = $this->Auth_model->last_update($account_no);
	        
	        $feed_detail = $this->Auth_model->get_feed_detail($account_no);
	        $result['month_count'] = $feed_detail[0]['this_month'];
	        $result['total_count'] = $feed_detail[0]['total'];
	        $result['today_count'] = $feed_detail[0]['today'];
	        if(count($last_update)>0){ 
	            $result['last_update'] = $last_update[0]['last_update'];    
	        } else {
	            $result['last_update'] = date('Y-m-d H:i:s');
	        }
	        $this->response($result,200);
	    }else{
	        $this->response(array('msg'=>'आपका फोन नंबर या मेल आईडी हमारे साथ पहले से रजिस्टर्ड है।'),500);
	    }
	}
	
	
	function is_unique_contact_post(){
	    $mobile_no = $this->post('contact_no');
	    $this->db->select('*');
	    $result = $this->db->get_where('users',array('mobile_no'=>$mobile_no,'status'=>1))->result_array();
	    if(count($result)>0){
	        $this->response(array('msg'=>'Already registred.'),500);
	    } else {
	      $this->response(array('msg'=>'valid mobile no.'),200);  
	    }
	}
	
	function profile_update_post(){
		$data['account_no']	= 	$this->post('account_no');
		$data['name']	= 	$this->post('name');
		//$data['mobile_no'] = 	$this->post('contact_no');
		$data['address'] 	= 	$this->post('address');	
		$data['mail_id'] = $this->post('mail');
		$result1 = $this->Auth_model->profile_update($data);
		if($result1){
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
		} else {
			$this->response(array('msg'=>'.'),500);
		}
	}
	
	function forgot_password_post(){
	    $data['account_no'] = trim($this->post('account_no'));
	    $data['oldpassword'] = trim($this->post('old_pass'));
	    $data['newpassword'] = trim($this->post('new_pass'));
	    $result = $this->Auth_model->forgot_pass($data);
	    if($result){
	        $this->response(array('msg'=>'Password changed successfully.'),200);
	    } else {
	        $this->response(array('msg'=>'Password not changed.'),500);
	    }
	}
	
	function top_scorer_get(){
	    $result = $this->Auth_model->top_scorer();
	    if(count($result)>0){
	        $this->output->cache(60); //min
	        $this->response($result,200);
	    } else {
	        $this->response(array('msg'=>'No record found.'),500);
	    }
	}
}
