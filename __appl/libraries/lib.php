<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
	LIBRARY CIPTAAN JINGGA LINTAS IMAJI
	KONTEN LIBRARY :
	- Upload File
	- Upload File Multiple
	- RandomString
	- CutString
	- Kirim Email
	- Konversi Bulan
	- Fillcombo
*/
class lib {
	public function __construct(){
		
	}
	
	//class Upload File Version 1.0 - Beta
	function uploadnong($upload_path="", $object="", $file=""){
		//$upload_path = "./__repository/".$folder."/";
		
		$ext = explode('.',$_FILES[$object]['name']);
		$exttemp = sizeof($ext) - 1;
		$extension = $ext[$exttemp];
		
		$filename =  $file.'.'.$extension;
		
		$files = $_FILES[$object]['name'];
		$tmp  = $_FILES[$object]['tmp_name'];
		if(file_exists($upload_path.$filename)){
			unlink($upload_path.$filename);
			$uploadfile = $upload_path.$filename;
		}else{
			$uploadfile = $upload_path.$filename;
		} 
		
		move_uploaded_file($tmp, $uploadfile);
		if (!chmod($uploadfile, 0775)) {
			echo "Gagal mengupload file";
			exit;
		}
		
		return $filename;
	}
	// end class Upload File
	
	//class Upload File Multiple Version 1.0 - Beta
	function uploadmultiplenong($upload_path="", $object="", $file="", $idx=""){
		$ext = explode('.',$_FILES[$object]['name'][$idx]);
		$exttemp = sizeof($ext) - 1;
		$extension = $ext[$exttemp];
		
		$filename =  $file.'.'.$extension;
		
		$files = $_FILES[$object]['name'][$idx];
		$tmp  = $_FILES[$object]['tmp_name'][$idx];
		if(file_exists($upload_path.$filename)){
			unlink($upload_path.$filename);
			$uploadfile = $upload_path.$filename;
		}else{
			$uploadfile = $upload_path.$filename;
		} 
		
		move_uploaded_file($tmp, $uploadfile);
		if (!chmod($uploadfile, 0775)) {
			echo "Gagal mengupload file";
			exit;
		}
		
		return $filename;
	}
	//end Class Upload File
	
	//class Random String Version 1.0
	function randomString($length,$parameter="") {
        $str = "";
		$rangehuruf = range('A','Z');
		$rangeangka = range('0','9');
		if($parameter == 'reg'){
			$characters = array_merge($rangeangka);
		}else{
			$characters = array_merge($rangehuruf, $rangeangka);
		}
         $max = count($characters) - 1;
         for ($i = 0; $i < $length; $i++) {
              $rand = mt_rand(0, $max);
              $str .= $characters[$rand];
         }
         return $str;
    }
	//end Class Random String
	
	//Class CutString
	function cutstring($text, $length) {
		$isi_teks = htmlentities(strip_tags($text));
		$isi = substr($isi_teks,0,$length);
		$isi = substr($isi_teks,0,strrpos($isi," "));
		$isi = $isi.' ...';
		return $isi;
	}
	//end Class CutString
	
	//Class Kirim Email
	function kirimemail($type="", $email="", $p1="", $p2="", $p3=""){
		$ci =& get_instance();
		
		$ci->load->library('email');
		$html = "";
		$subject = "";
		switch($type){
			case "email_test":
				
			break;
			case "email_notif":
				$subject = "Aktivasi User";
				$html = "
					<center>SELAMAT DATANG </center>
					<br/>
					<br/>
					<h1>Aktivasi USER Jingga Resto</h1>
					<br/>
					Berikut User dan password yang anda registrasikan <br>
					UserName : ".$p1."<br>
					Password : ".$ci->encrypt->decode($p2)."<br>
					silahkan klik link dibawah ini untuk melakukan aktivasi user. terima kasih atas partisipasinya
					<br/>
					<br/>
					<a href='".$ci->config->item('base_url')."login/register/act/".base64_encode($p1)."'>aktivasi</a>
				";
			break;
		}
		//echo $html;exit;
		$config = array();
		$config["SMTPAuth"]=TRUE;
		$config["SMTP"]=TRUE;
		$config["SMTPSecure"]= "tls";
		$config["Port"]=587;
		$config["Host"]='smtp.gmail.com';
		$config["Username"]='goyz87@gmail.com';
		$config["Password"]='plokiju_123';
		$config["EmaiFrom"]='goyz87@gmail.com';
		$config["EmaiFromName"]='Goyz Coyzzzz';

		/*$config = array(
			"protocol"	=>"smtp"
			,"mailtype" => "html"
			,"smtp_host" => "smtp.gmail.com"
			,"smtp_user" => "triwahyunugros@gmail.com"
			,"smtp_pass" => "ms6713saa"
			,"smtp_port" => 465
		);*/
		
		$ci->email->initialize($config);
		$ci->email->from("triwahyunugros@gmail.com");
		$ci->email->to($email);
		$ci->email->subject($subject);
		$ci->email->message($html);
		$ci->email->set_newline("\r\n");
		if($ci->email->send())
			//echo "<h3> SUKSES EMAIL ke $email </h3>";
			return 1;
		else
			//echo $this->email->print_debugger();
			return $ci->email->print_debugger();
	}	
	//End Class KirimEmail
	
	//Class Konversi Bulan
	function konversi_bulan($bln){
		switch($bln){
			case 1:$bulan='Januari';break;
			case 2:$bulan='Februari';break;
			case 3:$bulan='Maret';break;
			case 4:$bulan='April';break;
			case 5:$bulan='Mei';break;
			case 6:$bulan='Juni';break;
			case 7:$bulan='Juli';break;
			case 8:$bulan='Agustus';break;
			case 9:$bulan='September';break;
			case 10:$bulan='Oktober';break;
			case 11:$bulan='November';break;
			case 12:$bulan='Desember';break;
		}
		return $bulan;
	}
	//End Class Konversi Bulan
	
	//Class Fillcombo
	function fillcombo($type="", $balikan="", $p1="", $p2="", $p3=""){
		$ci =& get_instance();
		$ci->load->model('mhome');
		
		$v = $ci->input->post('v');
		if($v != ""){
			$selTxt = $v;
		}else{
			$selTxt = $p1;
		}
		
		$pid = $ci->input->post('v2');
		if($pid){
			$p2 = $pid;
		}else{
			$p2 = $p2;
		}

		if($type == 'tbl_loc_search'){
			$optTemp = '';
		}else{
			$optTemp = '<option value="0"> -- Choose -- </option>';
		}
		switch($type){
			case "tbl_loc_search":
				$type = 'tbl_loc';
				$data = $ci->mhome->get_combo($type, $p1, $p2);
			break;
			case "import_resource":
				$data = array(
					'0' => array('id'=>'tbl_emp','txt'=>'Data Employee'),
					'1' => array('id'=>'tbl_exp','txt'=>'Data Expense'),
					'2' => array('id'=>'tbl_assets','txt'=>'Data Assets'),
					'3' => array('id'=>'tbl_tester','txt'=>'Data tester'),
				);
			break;
			case "import_cost_object":
				$data = array(
					'0' => array('id'=>'tbl_prm','txt'=>'Data Cost Object'),
					'1' => array('id'=>'tbl_cust','txt'=>'Data Customer'),
					'2' => array('id'=>'tbl_location','txt'=>'Data Location'),
				);
			break;
			case "import_parameter":
				$data = array(
					'0' => array('id'=>'tbl_loc','txt'=>'Data Cost Center'),
					'1' => array('id'=>'tbl_rdm','txt'=>'Data Resource Driver'),
					'2' => array('id'=>'tbl_cdm','txt'=>'Data Cost Driver'),
				);
			break;
			case "jenis_kelamin":
				$data = array(
					'0' => array('id'=>'L','txt'=>'Laki-Laki'),
					'1' => array('id'=>'P','txt'=>'Perempuan'),
				);
			break;
			case "status":
				$data = array(
					'0' => array('id'=>'1','txt'=>'Active'),
					'1' => array('id'=>'0','txt'=>'Inactive'),
				);
			break;
			case "bulan" :
				$ci->load->helper('db_helper');
				$data = arraydate('bulan');
				$optTemp = '<option value=""> -- Month -- </option>';
			break;
			case "tahun" :
				$ci->load->helper('db_helper');
				$data = arraydate('tahun');
				$optTemp = '<option value=""> -- Year -- </option>';
			break;
			case "cost_type":
				$data = array(
					'0' => array('id'=>'fixed','txt'=>'Fixed'),
					'1' => array('id'=>'variable','txt'=>'Variable'),
					'2' => array('id'=>'step','txt'=>'Step'),
				);
			break;
			case "cost_bucket":
				$data = array(
					'0' => array('id'=>'unit','txt'=>'Unit'),
					'1' => array('id'=>'batch','txt'=>'Batch'),
					'2' => array('id'=>'product','txt'=>'Product'),
					'2' => array('id'=>'facility','txt'=>'Facility'),
				);
			break;
			default:
				$data = $ci->mhome->get_combo($type, $p1, $p2);
			break;
		}
		
		if($data){
			foreach($data as $k=>$v){
				if($selTxt == $v['id']){
					$optTemp .= '<option selected value="'.$v['id'].'">'.$v['txt'].'</option>';
				}else{ 
					$optTemp .= '<option value="'.$v['id'].'">'.$v['txt'].'</option>';	
				}
			}
		}
		
		if($balikan == 'return'){
			return $optTemp;
		}elseif($balikan == 'echo'){
			echo $optTemp;
		}
		
	}
	//End Class Fillcombo
}