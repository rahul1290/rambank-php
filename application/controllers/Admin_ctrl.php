<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Admin_ctrl extends REST_Controller {
    
    public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation','session']);
		$this->load->helper(['url', 'language','directory','file']);
        $this->load->library(array('session'));
		$this->load->model('Auth_model');
	}
	
	function session_get(){
	    print_r($this->session->all_userdata());
	}
	
	function login_get(){
	    $this->load->view('admin/login');
	}
	
	function device_tokens_get(){
	    $this->db->select('device_token');
	    $result = $this->db->get_where('users',array('device_token !='=>NULL))->result_array();
	    $this->response($result,200);
	}
	
	function login_post(){
	    $identity = trim($this->post('identity'));
	    $password = trim($this->post('password'));
	    
	    $result = $this->db->query("select * from users where (u_name = '".$identity."' OR mobile_no = '".$identity."') AND u_type = 'admin' AND status = 1")->result_array();
	
	    if(count($result)>0){
	        $newdata = array(
            'user_id'  => $result[0]['user_id']
            );
            $this->session->set_userdata($newdata);
            
	        redirect('Admin_ctrl/users');
	    } else {
	        redirect('Admin_ctrl/login');
	    }
	}
	
	function users_ajax_get($draw=0){
	   
						
		echo json_encode(array('total'=>$total[0]['total'],'record'=>$users));  
	}
	
	
	function users_get($draw=1,$search=''){
	$page = ($draw-1) * 10;
		if($search != ''){
		$total = $this->db->query("select count(*) as total
			from users u
			join feed_detail fd on fd.account_no = u.account_no
			AND(u.account_no like '".$search."%' OR u.name like '".$search."%' OR u.mobile_no like '".$search."%')
			AND u.status = 1
		") ->result_array();
		
        $users = $this->db->query("select 
			u.account_no,
			u.profile_photo,
			u.name,
			u.mobile_no,
			u.password,
			u.address,
			u.mail_id,
			u.gender,
			u.u_name,
			u.device_token,
			ifnull(fd.online_total,0) as feeds,
			ifnull(fd.today,0) as today,
			ifnull(fd.month,0) as this_month,
			ifnull(fd.total_bal,0) as previous, 
			ifnull((ifnull(fd.total_bal,0) + ifnull(fd.online_total,0)),0) as total
			from users u
			join feed_detail fd on fd.account_no = u.account_no
			AND u.status = 1 
			AND(u.account_no like '".$search."%' OR u.name like '".$search."%' OR u.mobile_no like '".$search."%')
			limit ".$page.", 10
		") ->result_array();
		} else {
		    $total = $this->db->query("select count(*) as total
			from users u
			join feed_detail fd on fd.account_no = u.account_no
			AND u.status = 1
		") ->result_array();
		
		    $users = $this->db->query("select 
			u.account_no,
			u.profile_photo,
			u.name,
			u.mobile_no,
			u.password,
			u.address,
			u.mail_id,
			u.gender,
			u.u_name,
			u.device_token,
			ifnull(fd.online_total,0) as feeds,
			ifnull(fd.today,0) as today,
			ifnull(fd.month,0) as this_month,
			ifnull(fd.total_bal,0) as previous, 
			ifnull((ifnull(fd.total_bal,0) + ifnull(fd.online_total,0)),0) as total
			from users u
			join feed_detail fd on fd.account_no = u.account_no
			AND u.status = 1 
			limit ".$page.", 10
		") ->result_array();
		}
		
		
		$data['total'] = $total[0]['total'];
		$data['has_next'] = ($draw-1) ? (($data['total'] / 10) > $draw) ? 1 : 0 : 1;
		$data['has_prev'] = ($draw-1) ? 1 : 0;
		$data['draw'] = (int)$draw;
		$data['users'] = $users;
        $this->load->view('admin/users',$data);
	}
	
	function users_old_get(){
	   if($this->session->userdata('user_id') != ''){
// 		$data['users'] = $this->db->query("select u.account_no,u.profile_photo,u.name,u.mobile_no,u.password,u.address,u.gender,u.u_name,ifnull(t1.feeds,0) as feeds,ifnull(t2.today,0) as today,ifnull(t3.this_month,0) as this_month,ifnull(t4.total_bal,0) as previous, ifnull((t4.total_bal + t1.feeds),0) as total from users u
// 						left JOIN (select count(*) as feeds,f.account_no from feeds f WHERE f.status = 1 GROUP by f.account_no) as t1 on t1.account_no = u.account_no
// 						left JOIN (SELECT COUNT(*) as today,f.account_no from feeds f WHERE date_format(f.created_at,'%Y-%m-%d') = '".date('Y-m-d')."' AND f.status = 1 GROUP BY f.account_no) as t2 on t2.account_no = u.account_no
// 						left JOIN (SELECT COUNT(*) as this_month,f.account_no from feeds f WHERE date_format(f.created_at,'%Y-%m-%d') >= '".date('Y-m-01')."' AND f.status = 1 GROUP BY f.account_no) as t3 on t3.account_no = u.account_no
// 						LEFT JOIN (SELECT total_bal,account_no FROM feed_detail fd WHERE fd.status = 1) as t4 on t4.account_no = u.account_no
// 						ORDER by u.account_no ASC")->result_array();
		
		$data['users'] = $this->db->query("select 
	u.account_no,
	u.profile_photo,
	u.name,
	u.mobile_no,
	u.password,
	u.address,
	u.gender,
	u.u_name,
	ifnull(t1.feeds,0) as feeds,
	ifnull(t2.today,0) as today,
	ifnull(t3.this_month,0) as this_month,
	ifnull(t4.total_bal,0) as previous, 
	ifnull((ifnull(t4.total_bal,0) + ifnull(t4.online_total,0)+ ifnull(t1.feeds,0)),0) as total
from users u
	left JOIN (select count(*) as feeds,f.account_no from feeds f WHERE f.status = 1 GROUP by f.account_no) as t1 on t1.account_no = u.account_no
	left JOIN (SELECT COUNT(*) as today,f.account_no from feeds f WHERE date_format(f.created_at,'%Y-%m-%d') = '".date('Y-m-d')."' AND f.status = 1 GROUP BY f.account_no) as t2 on t2.account_no = u.account_no
	left JOIN (SELECT COUNT(*) as this_month,f.account_no from feeds f WHERE date_format(f.created_at,'%Y-%m-%d') >= '".date('Y-m-01')."' AND f.status = 1 GROUP BY f.account_no) as t3 on t3.account_no = u.account_no
	LEFT JOIN (SELECT total_bal,online_total,account_no FROM feed_detail fd WHERE fd.status = 1) as t4 on t4.account_no = u.account_no
ORDER by u.account_no ASC")->result_array();
        $this->load->view('admin/users',$data);
	    }
	}
	
	function add_devotee_post(){
	    if($this->session->userdata('user_id') != ''){ 
	        $data['name'] = $this->post("name");
	        $data['u_name'] = $this->post("u_name");
            $data['mobile_no'] = $this->post("contact_no");
            $data['password'] = $this->post("password");
            $data['address'] = $this->post("address");
            $data['gender'] = $this->post("gender");
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->session->userdata('user_id');
            
            $this->db->trans_begin();
            $this->db->select('max(account_no) as count');
            $result = $this->db->get_where('users',array('u_type'=>'devotee','status'=>1))->result_array();
            
            $data['account_no'] = (int)$result[0]['count'] + 1; 
            $this->db->insert('users',$data);
            
            $this->db->insert('feed_detail',array(
                'account_no' => $data['account_no'],
                'total_bal' => (int)$this->post("previous"),
				'month' => $this->post('month'),
				'today' => $this->post('today'),	
				'online_total' => $this->post('total_feed'),
                'created_at' => date('Y-m-d')
                )
            );
            
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                echo json_encode(array('status'=>500));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status'=>200));
            }
	    }
	}
	
	
	function update_devotee_post(){
	    if($this->session->userdata('user_id') != ''){
	        $account_no = $this->post('account_no');
			$data['name'] = $this->post("name");
            $data['u_name'] = $this->post("u_name");
            $data['mobile_no'] = $this->post("contact_no");
            $data['password'] = $this->post("password");
            $data['address'] = $this->post("address");
            $data['gender'] = $this->post("gender");
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] = $this->session->userdata('user_id');
            $this->db->trans_begin();
            
			$x = $this->db->get_where('feed_detail',array('account_no'=>$account_no,'status'=>1))->result_array();
			if(count($x)>0){
			    $this->db->query("update feed_detail set online_total = (online_total - (today + month)) where account_no =".$account_no);
			    
			    $this->db->where('account_no',$account_no);
				$this->db->update('users',$data);
			} else {
				$this->db->insert('feed_detail',array(
					'account_no' => (int)$account_no,
					'total_bal' => (int)$this->post("previous"),
					'month' => (int)$this->post('month'),
					'today' => (int)$this->post('today'),
					'created_at' => date('Y-m-d h:i:s'),
					'status' => 1
				));
			}
            
            $this->db->query("update feed_detail set total_bal=".(int)$this->post("previous").",online_total = ".$this->post('total_feed').",today = ".(int)$this->post("today").",month=".(int)$this->post("month")." where account_no =".$account_no);
            
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                echo json_encode(array('status'=>500));
            } else {
                $this->db->trans_commit();
                echo json_encode(array('status'=>200));
            }
	    }
	}
	
	
	function user_detail_get($account_no){
	    
	    $data['users'] = $this->db->query("SELECT `u`.*,fd.total_bal as previous_count ,fd.total_bal + (select count(*) from feeds f WHERE f.account_no = fd.account_no AND f.status = 1) as total,
(select count(*) from feeds f WHERE f.account_no = ".$account_no." AND f.status = 1 AND date(created_at) >= '".date('Y-m-01')."') as month,
(select count(*) from feeds f WHERE f.account_no = ".$account_no." AND f.status = 1 AND date(created_at) = '".date('Y-m-d')."') as today
FROM `users` `u` LEFT JOIN `feed_detail` `fd` ON `fd`.`account_no` = ".$account_no." WHERE `u`.`status` = 1 AND u.account_no = ".$account_no)->result_array();


	    
	    $this->db->select('*,date_format(created_at,"%d/%m/%Y - %H:%i") as created_at');
	    $this->db->order_by('created_at','desc');
	    $data['feeds'] = $this->db->get_where('feeds',array('account_no'=>$account_no,'status'=>1))->result_array();
	    
	    $this->load->view('admin/user_detail',$data);
	}
	
	
	
	function delete_devotee_post(){
	    $data['account_no'] = $this->post('account_no');
	    
	    $this->db->where('account_no',$data['account_no']);
	    if($this->db->update('users',array('status'=>0))){
	        echo json_encode(array('status'=>500));
	    }
	}
	
	function get_users_get(){
	   $result = $this->db->get('ballot')->result_array();
	   echo json_encode(array('data'=>$result));
	}
	
	
	function logout_get(){
		$this->session->sess_destroy();
		redirect('Admin_ctrl/login','refresh');
	}
	
	function send_password_post(){
	    
	   // $this->load->library('email');
	    
    //     $this->email->from('ramnaambankdevker@gmail.com', 'RamBank');
    //     $this->email->to('sinha.rahulsinha.sinha@gmail.com');
    //     $this->email->cc('another@another-example.com');
    //     $this->email->bcc('them@their-example.com');
        
    //     $this->email->subject('Email Test');
    //     $this->email->message('Testing the email class.');
        
    //     if($this->email->send()){
    //                 echo json_encode(array('status'=>200,'msg'=>'पासवर्ड सेंड किया गया है'));   
    //     } else {
	   //     echo json_encode(array('status'=>500,'msg'=>'ईमेल आईडी मैच नहीं हो रहा'));
	   // }
	    
	    $data['mail'] = $this->post('mail');
	    $this->db->select('*');
	    $result = $this->db->get_where('users',array('mail_id'=>$data['mail'],'status'=>1))->result_array();
	    
	    if(count($result)>0){
	        $headers = "From: RamNaamBank <ramnaambankdevker@gmail.com>\r\n";
            $headers .= "Reply-To: ramnaambankdevker@gmail.com\r\n";
            $headers .= "Return-Path: ramnaambankdevker@gmail.com\r\n";
            $headers .= "CC: sombodyelse@example.com\r\n";
            $headers .= "BCC: hidden@example.com\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
            // $headers .= "X-Priority: 1 (Highest)\n";
            // $headers .= "X-MSMail-Priority: High\n";
            $headers .= "Importance: High\n";
	       
	       $to = $data['mail'];
            $subject = 'Regarding password';
            $message = '<html><body>';
            $message .= '<h1>Your Password</h1>';
            $message .= '<p>' . $result[0]['password'] . '</p>';
            $message .= '</body></html>';
	       
	        if(mail($to,$subject,$message,$headers)){
	            echo json_encode(array('status'=>200,'msg'=>'पासवर्ड सेंड किया गया है'));
	        } else {
	            echo json_encode(array('status'=>400,'msg'=>'mail not sent'));
	        }
	    } else {
	        echo json_encode(array('status'=>500,'msg'=>'ईमेल आईडी मैच नहीं हो रहा'));
	    }
	}
	
	function fetchData($page){
	    $apiKey = 'AIzaSyBw8z2KZO2qECs___A93iPQffxevhtI-jY';
	    $googleSheetUrl = 'https://sheets.googleapis.com/v4/spreadsheets/';
	    $sheetId = '1MgZhMgijAh-roIYdsmAhGYQ43-C-xZwmr-2ez58_eBc';
	    $temp = [];
	    $data = file_get_contents($googleSheetUrl.$sheetId.'/values/'.$page.'?key='.$apiKey,true);
	    $data = json_decode($data,true);
	    //print_r($data['values']);
	   foreach($data['values'] as $key => $val){
	        $temp[$val[0]] =  trim($val[1]);
	    } 
	    return $temp;
	}
	
	function getsheet_data_get(){
	    header('Content-Type: application/json; charset=utf-8');
	    
	    $string = read_file(APPPATH.'../data.json');
	    echo $string;
	    die;
	}
	
	
	function app_images_get(){
	    $map = directory_map('./app_images/',1);
	    print_r($map);
	}
	
	function fetchsheet_data_get(){
	    $jsonData = [];
	    $page = 'registration';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    $page = 'config';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    $page = 'login';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    $page = 'instruction';
	    $tempArray = $this->fetchData($page);
	   
	    $counter = 1;
	    $temp = [];
	    for($i=1;$i < count($tempArray); $i=$i+2){
	        $inner_temp = [];
	        $inner_temp['instruction'] = $tempArray['label'.$counter];
	        $inner_temp['img'] = $tempArray['label'.$counter.'_img'];
	        $temp[] = $inner_temp;
	        $counter++;
	    }
	    
	    $final['title'] =  $tempArray['title'];
	    $final['images'] = $temp;
	    $jsonData['instruction'] = $final;
	    $temp = [];
	    
	    $page = 'temple';
	    $tempArray = $this->fetchData($page);
	    
	    $temp['title'] = $tempArray['page_title'];
	    $images = [];
	    for($i=1; $i < count($tempArray); $i++){
	        $images[] = $tempArray['image'.$i]; 
	    }
	    $temp['images'] = $images;
	    $jsonData[$page] = $temp;
	    
	    $page = 'sidebar';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    $page = 'dashboard';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    $page = 'after_login';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    $page = 'static_pages';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    $page = 'top_score';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    $page = 'forgotpassword';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    $page = 'profile';
	    $jsonData[$page] = $this->fetchData($page);
	    
	    /////////////////////
	    
	    $page = 'about_us';
	    $tempArray = $this->fetchData($page);
	   
	   
	    $counter = 1;
	    $temp = [];
	    for($i=1;$i < count($tempArray); $i=$i+3){
	        $inner_temp = [];
	        $inner_temp['image'] = $tempArray['image'.$counter];
	        $inner_temp['name'] = $tempArray['image'.$counter.'_leb_text1'];
	        $inner_temp['designation'] = $tempArray['image'.$counter.'_leb_text2'];
	        $temp[] = $inner_temp;
	        $counter++;
	    }
	    $final['title'] = $tempArray['title'];
	    $final['images'] = $temp;
	    $jsonData[$page] = $final;
	    $temp = [];
	    
	    /////////////////////
	    
	    //echo json_encode($jsonData,JSON_UNESCAPED_UNICODE);
	    $data = json_encode($jsonData,JSON_UNESCAPED_UNICODE);
        if ( ! write_file(APPPATH.'../data.json', $data)){
            echo 'Unable to write the file';
        }
        else{
            echo 'File updated.';
        }
	}
	
}