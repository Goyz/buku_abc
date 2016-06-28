<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {
	
	function __construct(){
		parent::__construct();
		$this->load->library(array('encrypt','lib'));
	}
	
	public function index(){
		//print_r($_POST);exit;
		$this->session->unset_userdata('err');
		$user=$this->db->escape_str($this->input->post('user'));
		$pass=$this->db->escape_str($this->input->post('pwd'));
		$error=false;$msg='';
		if($user && $pass){
			$cek_user=$this->mhome->getdata('data_login',$user);
			//print_r($cek_user);exit;
			if(isset($cek_user['nama_user'])){
				if(isset($cek_user['status']) && $cek_user['status']==1){
					if($pass==$this->encrypt->decode($cek_user['pwd'])){
						$this->session->set_userdata($this->config->item('user_data'), base64_encode(serialize($cek_user)));	
					}
					else{
						$error=true;
						//$this->session->set_flashdata('err', 'Password Invalid');
						$msg='Password Salah';
					}
				}
				else{
					$error=true;
					//$this->session->set_flashdata('err', 'USER Sudah Tidak Aktif Lagi');
					$msg='USER Sudah Tidak Aktif Lagi';
				}
			}
			else{
				$error=true;
				//$this->session->set_flashdata('err', 'User Tidak Terdaftar');
				$msg='User Tidak Terdaftar';
			}
			//if(isset($cek_u))
		}
		else{
			$error=true;
			//$this->session->set_flashdata('err', 'Isi User Dan Password');
			$msg='Isi User Dan Password';
		}
		if($error==true){
			$this->session->set_userdata('err',$msg);
			//$this->session->set_flashdata('err', $msg);
			//$this->session->keep_flashdata('err');
			//echo $this->session->userdata('err');exit;
			echo $msg;exit;
			//header("Location: {$this->host}login");
		}else{
			//$this->session->unset_userdata('err');
			//header("Location: {$this->host}");
			echo 1;
			
		}
	
		
	}
	
	function logout(){
		$this->session->unset_userdata($this->config->item('user_data'), 'limit');
		$this->session->unset_userdata($this->config->item('modeling'), 'limit');
		$this->session->sess_destroy();
		header("Location: " . $this->host);
	}
	function cek_user(){
		echo $this->mhome->getdata('cek_user');
	}
	function simpan_reg(){
		echo $this->mhome->simpan_reg();
	}
	function register($p1="",$p2=""){
		$usr="";
		if($p2!=""){$usr=base64_decode($p2);}
		if($p1=="notif"){
			$this->load->library('lib');
			$data=$this->mhome->getdata('cek_user','get',$usr);
			//print_r($data);exit;
			if(isset($data['nama_user'])){
				$this->lib->kirimemail("email_notif", $data['Email'],$data['nama_user'],$data['pwd']);
			}else{
				$this->smarty->assign('msg',1);
			}
			$temp='modul/notif.html';
			$this->smarty->assign('temp',$temp);
			return $this->smarty->display('index.html');
			//return $this->smarty->display('modul/notif.html');
		}else if($p1=="act"){
			$data=$this->mbackend->getdata('cek_user','get',$usr);
			if($this->mbackend->simpan_reg("act",$data['nama_user'])==1){
				$temp='modul/act.html';
				$this->smarty->assign('temp',$temp);
				return $this->smarty->display('index.html');
			}
		}
		$opt="<option value='L'>Laki - laki </option><option value='L'>Wanita</option>";
		$this->smarty->assign('opt',$opt);
		$this->smarty->display('registrasi/register.html');
	}

}
