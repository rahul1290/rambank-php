<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model{
    
    function login($data){
        $result = $this->db->query("SELECT * FROM users where (mobile_no = '".$data['identity']."' or account_no = '".$data['identity']."') AND password = '".$data['password']."' AND status = 1 limit 1")->result_array();
		if(count($result) == 1){
			$device_detail = $this->db->query("select device_id from users where user_id = ".$result[0]['user_id']." AND device_id ='".$data['device_id']."'")->result_array();
			
			if($data['device_token'] != ''){
		        $this->db->query("update users set device_token = '".$data['device_token']."' where user_id= '".$result[0]['user_id']."'");    
		    }
			
			if(count($device_detail)>0){
			        $new_result = $this->db->query("select * from users where user_id =".$result[0]['user_id'])->result_array();
					return $new_result;
				} else{
					if($result[0]['device_id'] == null OR $result[0]['device_id'] == ''){
					    $this->db->query("update users set device_id = '".$data['device_id']."' where user_id= '".$result[0]['user_id']."'");
					    $result[0]['device_id'] = $data['device_id'];
						$result[0]['device_token'] = $data['device_token'];
						return $result;
					}else {
						return '2';			//force login
					}
				}
        } else {
            return '1';		// login failed
        }
    }
	
	function force_login($data){
        $result = $this->db->query("SELECT * FROM users where (mobile_no = '".$data['identity']."' or account_no = '".$data['identity']."') AND password = '".$data['password']."' AND status = 1 limit 1")->result_array();
        if(count($result) == 1){
			//$this->db->query("update users set device_id = '".$data['device_id']."' where account_no = '".$data['identity']."'");
			$this->db->query("update users set device_id = '".$data['device_id']."' where account_no = '".$result[0]['account_no']."'");
			$result = $this->db->query("select * from users where account_no ='".$result[0]['account_no']."' AND status = 1 ")->result_array();
			return $result;	
		}
	}
    
    function get_feed_detail($account_no){
        $this->db->select('month as this_month,today,online_total as total');
        return $result = $this->db->get_where('feed_detail',array('account_no'=>$account_no))->result_array();
    } 
    
    function previouscount($account_no){
        $this->db->select('fd_id,(total_bal + online_total) total_bal');
        return $result = $this->db->get_where('feed_detail',array('account_no'=>$account_no,'status'=>1))->result_array();
    }
    
    function last_update($account_no){
        return $result = $this->db->query("select created_at as last_update from feed_detail where account_no = '".$account_no."' AND status = 1 limit 1")->result_array();
    }
    
    function is_active($data){
        return $this->db->query("select * from users where account_no = '".$data['account_no']."' AND status = 1 limit 1")->result_array();
    }
    
    function registration($data){
        $this->db->trans_begin(); 
        $result = $this->db->query("select * from users where (mobile_no = ".$data['contact_no']." OR mail_id = '".$data['mail_id']."') AND status = 1")->result_array();
        
        if(count($result)>0){
            return false;
        } else {
            $this->db->select('max(account_no) as count');
            $result = $this->db->get_where('users',array('u_type'=>'devotee','status'=>1))->result_array();
            
            $account_no = (int)$result[0]['count'] + 1;
            $user = array( 
                'name' => $data['name'],
                //'u_name' => $data['u_name'],
                'u_name' => 'ramram',
                'password' => $data['password'],
                'account_no' => $account_no,
                'u_type' => 'devotee',
				'device_id' => $data['device_id'],
				'device_token' => $data['device_token'],
                'mobile_no' => $data['contact_no'],
                'address' => $data['address'],
                'profile_photo' => $data['photo'],
                'created_at' => date('Y-m-d H:i:s'),
                'gender' => $data['gender'],
                'mail_id' => $data['mail_id']
            );
            $this->db->insert('users',$user);   
        
            $feed_detail = array(
                'account_no' => $account_no,
                'total_bal' => 0,
                'online_total' => 0,
                'today' => 0,
                'month' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'bulk_upload_at' => date('Y-m-d H:i:s')
            );
            $this->db->insert('feed_detail',$feed_detail);  
            if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    return false;
            } else {
                    $this->db->trans_commit();
                    return $account_no;
            }
        }
    }
	
	function profile_update($data){
		$this->db->trans_begin(); 
		
		$this->db->where('account_no',$data['account_no']);
		$this->db->update('users',array('name'=>$data['name'],
										'address' => $data['address'],
										'mail_id' => $data['mail_id']
										//'mobile_no' => $data['mobile_no']
										)
						);
        $this->db->select('*');
        $result = $this->db->get_where('users',array('account_no'=>$data['account_no']))->result_array();
		if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return false;
		} else {
				$this->db->trans_commit();
				return $result;
		}
		
	}
    
    function forgot_pass($data){
        $this->db->select('*');
        $result = $this->db->get_where('users',array('account_no'=>$data['account_no'],'password'=>$data['oldpassword'],'status'=>1))->result_array();
        
        if(count($result)>0){
            $this->db->where('account_no',$data['account_no']);
            if($this->db->update('users',array('password'=>$data['newpassword']))){
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    function top_scorer(){
        //$this->db->SELECT('u.user_id,u.u_type,fd.account_no,u.name,u.name as u_name,u.address,u.profile_photo,u.mobile_no,u.gender,u.created_at,u.created_by,fd.total_bal,fd.online_total as feed,FORMAT(fd.online_total,0,"en_IN") as total');
        $this->db->SELECT('u.user_id,u.name,u.name as u_name,fd.total_bal,fd.online_total as feed,FORMAT(fd.online_total,0,"en_IN") as total');
        $this->db->JOIN('users u','u.account_no = fd.account_no');
        $this->db->ORDER_by('fd.online_total','DESC');
        //$this->db->LIMIT(100);
        return $result = $this->db->get_where('feed_detail fd',array('u.status'=>1,'fd.online_total >'=> '125000'))->result_array();
    }
}