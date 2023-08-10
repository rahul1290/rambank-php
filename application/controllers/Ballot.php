<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Ballot extends CI_Controller {
    
    public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->library(['ion_auth', 'form_validation','session']);
		$this->load->helper(['url', 'language']);
        $this->load->library(array('session'));
		$this->load->model('Auth_model');
	}
	
	function index(){
	    $result = $this->db->get('ballot')->result_array();
	    echo json_encode(array('data'=>$result));
	}
	
	function get_users(){
	    $data['result'] = $this->db->get('ballot')->result_array();
	    $this->load->view('ballot/ballot_view',$data);
	}
	
	function user_add(){
	    $this->load->view('ballot/ballot_create');
	}
	
	function add_user(){
	    $name = $this->input->post('name');
	    $token = $this->input->post('token');
	    $address = $this->input->post('address');
	    
	    $this->db->insert('ballot',array('token'=> $token, 'name'=>$name,'address'=>$address));
	    redirect(base_url().'/ballot/get_users', 'refresh');
	}
	
	function delete_user($id){
	    $this->db->where('id',$id);
	    if($this->db->delete('ballot')){
	        redirect(base_url().'/ballot/get_users', 'refresh');
	    }
	}
	
	function set_winner($id){
	    $this->db->update('ballot',array('is_winner'=>0));
	    
	    $this->db->where('id',$id);
	    $this->db->update('ballot',array('is_winner'=>1));
	    redirect(base_url().'/ballot/get_users', 'refresh');
	}
}