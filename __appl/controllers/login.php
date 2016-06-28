<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library('encrypt');
	}
	
	public function index(){
		//print_r($_POST);
		$user=$this->db->escape_str($this->input->post('user'));
		$pass=$this->db->escape_str($this->input->post('pwd'));
		$error=false;
		if($user && $pass){
			$cek_user=$this->mhome->getdata('data_login',$user);
			//print_r($cek_user);exit;
			if(count($cek_user)>0){
				if(isset($cek_user['status']) && $cek_user['status']==1){
					//echo $cek_user['status'];exit;
					//echo $pass.'->'.$this->encrypt->decode($cek_user['pwd']);exit;
					if($pass==$this->encrypt->decode($cek_user['pwd'])){
						//echo $pass;exit;
						$this->session->set_userdata($this->config->item('user_data'), base64_encode(serialize($cek_user)));	
					}
					else{
						$error=true;
						$this->session->set_flashdata('error', 'Password Invalid');
					}
				}
				else{
					$error=true;
					$this->session->set_flashdata('error', 'USER Sudah Tidak Aktif Lagi');
				}
			}
			else{
				$error=true;
				$this->session->set_flashdata('error', 'User Tidak Terdaftar');
			}
			//if(isset($cek_u))
		}
		else{
			$error=true;
			$this->session->set_flashdata('error', 'Isi User Dan Password');
		}
		//echo $error;exit;
		if($error==true)header("Location: {$this->host}login");
		else header("Location: {$this->host}kelas");
	
		
	}
	
	function logout(){
		$this->session->unset_userdata($this->config->item('user_data'), 'limit');
		$this->session->unset_userdata($this->config->item('modeling'), 'limit');
		$this->session->sess_destroy();
		header("Location: " . $this->host);
	}

}
