<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Welcome extends CI_Controller {

	public function index(){
	    //redirect(base_url().'visitor/visitor/index','refresh');
	    redirect(base_url().'parent/login','refresh');
	}
}
