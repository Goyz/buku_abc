<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function upload_ftp($local_file,$path_ftp,$file_name){
	$ci =& get_instance();
	$ftp_server=$ci->config->item('FTP_host');
	$ftp_user_name=$ci->config->item('FTP_user');
	$ftp_user_pass=$ci->config->item('FTP_pwd');
	$conn_id = ftp_connect($ftp_server)or die("Couldn't connect to $ftp_server");
	$msg=0;
	//$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	if (@ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
		ftp_pasv($conn_id, true);
		$mkdir=get_direktorina_ftp($conn_id,$path_ftp);
		if($mkdir==1){
			if(ftp_put($conn_id, $file_name, $local_file, FTP_BINARY)){$msg=1;}
			else{$msg=4;}
		}else{$msg=3;}
	}
	else{$msg=2;}
	
	ftp_close($conn_id);
	return $msg;
}

function download_ftp($path_ftp,$path_local){
	//$remote_file = $com.'/'.$vr.'/'.$io.'/partial_'.$partial.'/'.$ls.'/'.$file;
	//$local_file = 'repositories/fd/'.$file;
	$ci =& get_instance();
	$ftp_server=$ci->config->item('FTP_host');
	$ftp_user_name=$ci->config->item('FTP_user');
	$ftp_user_pass=$ci->config->item('FTP_pwd');
	$conn_id = ftp_connect($ftp_server)or die("Couldn't connect to $ftp_server");
	if (@ftp_login($conn_id, $ftp_user_name, $ftp_user_pass)) {
		ftp_pasv($conn_id, true);
		if (ftp_get($conn_id, $path_local, $path_ftp, FTP_BINARY)) {
				header('Location: '.base_url().$path_local);
		} else {
			 echo "There was a problem while downloading $path_ftp to $path_local\n";
		}
		ftp_close($conn_id);
	} else {
		echo "Couldn't connect as $ftp_user_name\n";
	}
}

function send_email($email,$html="",$subject="",$file_na="",$from="",$cc=""){
	$ci =& get_instance();
	$ci->load->library('phpmailer');
	try {
			$mail = new PHPMailer();
			$body            = $html;
			
			if($ci->config->item('SMTP')) $mail->IsSMTP();
			//$mail->Host       = $this->config->item('Host'); 
			$mail->SMTPDebug  = 2;                                  
			$mail->SMTPAuth   = $ci->config->item('SMTPAuth'); 
			$mail->SMTPSecure = $ci->config->item('SMTPSecure');;
			$mail->Port       = $ci->config->item('Port');                 
			$mail->Host       = $ci->config->item('Host'); 
			$mail->Username   = $ci->config->item('Username');     
			$mail->Password   = $ci->config->item('Password');            

			$mail->AddReplyTo($ci->config->item('EmaiFrom'),$ci->config->item('EmaiFromName'));

			$mail->From       = $ci->config->item('EmaiFrom');
			if (!empty($from)) {
				$mail->FromName   = $ci->config->item($from);
			} else {
				$mail->FromName   = $ci->config->item('EmaiFromName');
			}
			if(count($email) > 0){
				foreach($email as $v){
						$mail->AddAddress(trim($v['email_address']));
						//echo trim($v['email_address']);
				}
			}
			
			else{return 2;}
			//exit;
			if(count($cc) > 0){
				foreach($cc as $v){
					$mail->AddCC(trim($v['email_address']));
					//$mail->AddAddress(trim($v['email_address']));
				}
			}
			//$mail->AddCC('yogi_p4try4@yahoo.com', 'Yogi');
			$mail->Subject   = $subject;
			$mail->AltBody   = "To view the message, please use an HTML compatible email viewer!"; 
			$mail->WordWrap  = 100; 
			$mail->MsgHTML($body);
			$mail->IsHTML(true);
			//echo $file_na;
			if($file_na!=''){$mail->AddAttachment($file_na);}
			$mail->Send();
			return 1;
		} catch (phpmailerException $e) {
			return $e->errorMessage();
		}
			
}
function hitung_waktu($waktu,$waktu_data){
	$get_jam = date_parse($waktu);
	$get_data = date_parse($waktu_data);
	//print_r($get_data);exit;
	$int_waktu = $get_jam['hour'] * 3600 + $get_jam['minute'] * 60 + $get_jam['second'];
	$int_data = $get_data['hour'] * 3600 + $get_data['minute'] * 60 + $get_data['second'];
	$sisa=($int_data-$int_waktu);
	//echo $sisa;
	return gmdate("H:i:s", $sisa);
}