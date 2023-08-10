<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_function{
    
    protected $CI;
    
    public function __construct()
    {
        $this->CI =& get_instance();
    }
    
    public function permission_link(){
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->database();
        $user_id = $CI->session->userdata('user_id');
        
        $CI->db->select('g.name');
        $CI->db->join('users u','u.id = ug.user_id');
        $CI->db->join('groups g','g.id = ug.group_id');
        $result = $CI->db->get_where('users_groups ug',array('u.id'=>$user_id))->result_array();
        return $result[0]['name'];
    }
    
    function add_log($ses_id,$sch_id,$user,$event_name,$table_name,$table_id){
        $CI =& get_instance();
        $CI->load->database();
        $data['ses_id'] = $ses_id;
        $data['sch_id'] = $sch_id;
        $data['user_id'] = $user;
        $data['user_ip'] = $CI->input->ip_address();
        $data['event_name'] = $event_name;
        $data['event_time'] = date('Y-m-d H:i:s');
        $data['table_name'] = $table_name;
        $data['table_id'] = $table_id;
        return $res = $CI->db->insert('log_report',$data);
    }
    
    public function user_permission(){
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->database();
        $user_id = $CI->session->userdata('user_id');
        
        $CI->db->select('permission');
        $result = $CI->db->get_where('users',array('id'=>$user_id))->result_array();
        
        $data = $result[0]['permission'];
        $data = explode(",",$data);
        return $data;
        //return $result;
    }
    
    
    function send_sms($mobile,$sms){
        $contacts = $mobile;
        $sms_text = urlencode($sms);
        $sms_username = 'shakuntala';
        $password = '7810c37c3rl2ttygs';
        $from = 'SHKNTL';
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, "http://sms.medialab.in/pushsms.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=".$sms_username."&api_password=".$password."&sender=".$from."&to=".$contacts."&message=".$sms_text.'&priority=11');
        $response = curl_exec($ch);
        curl_close($ch);
        return true;
    }
 
//     function send_sms($mobile,$sms){
//         //return true;
//         $api_key = '25D105E607A4B8';
//         $contacts = $mobile;
//         $from = 'SHKVID';
//         $sms_text = urlencode($sms);
//         //Submit to server
//         $ch = curl_init();
//         curl_setopt($ch,CURLOPT_URL, "http://login.aronixcreativepoint.com/app/smsapi/index.php");
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         curl_setopt($ch, CURLOPT_POST, 1);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, "key=".$api_key."&campaign=0&routeid=31=TRANS(31)&type=text&contacts=".$contacts."&senderid=".$from."&msg=".$sms_text);
//         $response = curl_exec($ch);
//         curl_close($ch);
//         return true;
//     }
    
    function generateNumericOTP() {
        $generator = "1357902468";
        $result = "";
        for ($i = 1; $i <= 6; $i++) {
            $result .= substr($generator, (rand()%(strlen($generator))), 1);
        }
        return $result;
    }
    
    function fetch_school($str){
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->database();
        
        $CI->db->select('sch_id');
        $result = $CI->db->get_where('school',array('school_code'=>$str,'status'=>1))->result_array();
        
        return $result[0]['sch_id'];
    }
    
    function fetch_school_code($sid){
        $CI =& get_instance();
        $CI->load->database();
        
        $CI->db->select('school_code');
        $result = $CI->db->get_where('school',array('sch_id'=>(int)$sid,'status'=>1))->result_array();
        if(count($result) > 0){
            return $result[0]['school_code'];
        }else{
            return 'shakuntalavidyalaya.edu.in';
        }   
    }
    
    function fetch_school_name($str){
        $CI =& get_instance();
        $CI->load->database();
  
        $CI->db->select('school_name');
        $result = $CI->db->get_where('school',array('school_code'=>$str,'status'=>1))->result_array();
        if(count($result) > 0){
            return $result[0]['school_name'];
        }else{
            return 'Shakuntala Group';
        }
        
    }
    
    function maskPhoneNumber($number){
        $mask_number =  str_repeat("*", strlen($number)-4) . substr($number, -4);
        return $mask_number;
    }
    
    
    //////////////////////////////////////////////////////////////////////////////////
    function  fetch_admin_school($id){
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->database();
        
        $CI->db->select('school_id');
        $result = $CI->db->get_where('users',array('id'=>(int)$id))->result_array();
        return $result[0]['school_id'];   
    }
    
    function current_session(){
        $CI =& get_instance();
        $CI->load->library('session');
        $CI->load->database();
        
        $CI->db->select('ses_id');
        $result = $CI->db->get_where('session',array('set_ses'=>1,'status'=>1))->result_array();
        return $result[0]['ses_id'];
    }
}
