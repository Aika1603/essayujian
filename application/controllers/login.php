<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {

	public function index() {
		$this->load->view('login');	
	}

	public function getlogin() {
		$u = $this->input->post('username');
		$p = $this->input->post('password');
		$this->load->model('login_model');
		$this->login_model->getlogin($u,$p);
	}
}