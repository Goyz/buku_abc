<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class mhome extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata($this->config->item('user_data'))));
	}
	
	function getdata($type="", $p1="", $p2="",$p3="",$p4=""){
		$where = " WHERE 1=1 ";
		$footer="";
		$table="";
		$bulan=$this->input->post('bulan');
		$tahun=$this->input->post('tahun');
		$id=$this->input->post('id');
		switch($type){
			case "get_waktu":
				$sql="SELECT * FROM tbl_waktu 
				WHERE cl_peserta_id=".$this->auth['peserta_id']." 
				AND flag=1";
				return $this->db->query($sql)->row_array();
			break;	
			case "get_video":
				$modul=$this->input->post('mod');
				if($modul=='konten'){
					$sql="SELECT * FROM tbl_file_materi WHERE tbl_konten_materi_id=".$p1;
				}else{
					$sql="SELECT * FROM tbl_file_bank_soal WHERE tbl_bank_soal_id=".$p1;
				}
				return $this->result_query($sql);
			break;
			case "data_login":
				$ex=$this->db->query("SELECT A.* FROM tbl_user A WHERE A.nama_user='".$p1."'")->row_array();
				if(isset($ex['nama_user'])){
					if($ex['cl_user_group_id']!=3){
						$sql="SELECT A.*,B.group_user,C.nama_lengkap,C.id as id_identitas
							FROM tbl_user A 
							LEFT JOIN cl_user_group B ON A.cl_user_group_id=B.id
							LEFT JOIN cl_guru C ON C.tbl_user_id=A.nama_user
							WHERE A.nama_user = '".$p1."' AND C.flag='".($ex['cl_user_group_id']==1 ? 'A' : 'G')."'";
					}else{
						$sql = "
							SELECT A.*,B.group_user,C.cl_kelas_id,D.kelas,C.id as peserta_id
							FROM tbl_user A 
							LEFT JOIN cl_user_group B ON A.cl_user_group_id=B.id
							LEFT JOIN cl_peserta C ON C.tbl_user_id=A.nama_user
							LEFT JOIN cl_kelas D ON C.cl_kelas_id=D.id
							WHERE A.nama_user = '".$p1."'
						";
					}
				}else{return $ex;}
				
				//echo $sql;
				$data=$this->result_query($sql,'row_array');
				//print_r($data);exit;
				return $data;
			break;
			case "cek_user":
				if($p1=='get'){
					$sql = " SELECT A.*,B.Email	FROM tbl_user A LEFT JOIN cl_peserta B ON B.tbl_user_id=A.nama_user
							WHERE A.nama_user='".$p2."'";
					$res=$this->db->query($sql)->row_array();
					//echo $sql;
					return $res;
					//echo $sql;
				}
				else{
					$sql = " SELECT A.*	FROM tbl_user A LEFT JOIN cl_peserta B ON B.tbl_user_id=A.nama_user
							WHERE A.nama_user='".$this->input->post('usr')."' OR B.Email='".$this->input->post('email')."'";
							//echo $sql;
					$res=$this->db->query($sql)->row_array();
					if(isset($res['nama_user'])){echo 2;}
					else echo 1;
					exit;
				}
			break;	
			case "combo_matapelajaran":
				$kelas=$this->input->post('id_kelas');
				if($kelas){
					$sql="SELECT A.id,CONCAT('Kelas : ',B.kelas) as kelas,A.nama_pelajaran
							FROM cl_mata_pelajaran A
							LEFT JOIN cl_kelas B ON A.cl_kelas_id=B.id
							where A.cl_kelas_id IN (".join(',',$kelas).")
							GROUP BY A.id,A.nama_pelajaran ";
					$res=$this->db->query($sql)->result_array();
					if(count($res)>0){
						echo json_encode($res);exit;
					}else{
						echo json_encode(array());exit;
					}
				}
				else{echo json_encode(array());exit;}
				//echo $sql;exit;
				//echo "<pre>";print_r($kelas);exit;
			break;
			case "cl_propinsi":
				$sql="SELECT * FROM cl_provinsi";
				return $this->result_query($sql);
			break;
			case "cl_kab_kota":
				$filter=$this->input->post('v2');
				$sql="SELECT kode_kab_kota as id,kab_kota as txt FROM cl_kab_kota ".$where;
				if($filter){$sql .=" AND cl_provinsi_kode=".$filter;}
				else{ $sql .=" AND cl_provinsi_kode=-1";}
				return $this->result_query($sql);
			break;
			case "c_mata_pelajaran":
				$filter=$this->input->post('v2');
				
				$sql="SELECT C.id as id,C.nama_pelajaran as txt FROM cl_mata_pelajaran C ".$where;
				if($this->auth['cl_user_group_id']==2){
					$sql="SELECT A.cl_mata_pelajaran_id as id,C.nama_pelajaran as txt
						FROM tbl_mengajar_pelajaran A 
						LEFT JOIN cl_guru B ON A.cl_guru_id=B.id
						LEFT JOIN cl_mata_pelajaran C ON A.cl_mata_pelajaran_id=C.id
						LEFT JOIN cl_kelas D ON C.cl_kelas_id=D.id
						".$where." AND A.cl_guru_id= ".$this->auth['id_identitas'];
				}
				//echo $sql;print_r($this->auth);
				if($filter){$sql .=" AND C.cl_kelas_id=".$filter;}
				else{ $sql .=" AND C.cl_kelas_id=-1";}
				return $this->result_query($sql);
			break;
			case "c_bab":
				$filter=$this->input->post('v2');
				$sql="SELECT id as id,judul_bab as txt FROM tbl_bab_pelajaran ".$where;
				if($filter){$sql .=" AND cl_mata_pelajaran_id=".$filter;}
				else{ $sql .=" AND cl_mata_pelajaran_id=-1";}
				return $this->result_query($sql);
			break;
			case "c_sub_bab":
				$soal=$this->input->post('v4');
				$filter=$this->input->post('v2');
				if($soal){
					$sql="SELECT tbl_sub_bab_id FROM tbl_konten_materi GROUP BY tbl_sub_bab_id";
					$exist=$this->db->query($sql)->result_array();
					if(count($exist)>0){
						$id=array();
						foreach($exist as $v){
							$id[]=$v['tbl_sub_bab_id'];
							
						}
						$where .=" AND id NOT IN (".join(',',$id).")";
					}
					
					$sql="SELECT id as id,judul_sub_bab as txt FROM tbl_sub_bab_pelajaran ".$where;
					if($filter){$sql .=" AND tbl_bab_id=".$filter;}
					return $this->result_query($sql);
				}else{
					
					$sql="SELECT id as id,judul_sub_bab as txt FROM tbl_sub_bab_pelajaran ".$where;
					if($filter){$sql .=" AND tbl_bab_id=".$filter;}
					else{ $sql .=" AND tbl_bab_id=-1";}
					return $this->result_query($sql);
				}
			break;
			case "cl_kecamatan":
				$filter=$this->input->post('v2');
				$sql="SELECT kode_kecamatan as id,kecamatan as txt FROM cl_kecamatan ".$where;
				if($filter){$sql  .=" AND cl_kab_kota_kode=".$filter;}
				else{ $sql .=" AND cl_kab_kota_kode=-1";}
				return $this->result_query($sql);
			break;
			case "cl_mata_pelajaran":
				if($p1!=""){
					$where .=" AND A.cl_kelas_id = '".$p1."'";
				}
				
				if($id){
					$where .=" AND A.id = '".$id."'";
				}
				$sql = " SELECT A.*,B.kelas,A.nama_pelajaran as txt 
						FROM cl_mata_pelajaran A 
						LEFT JOIN cl_kelas B ON A.cl_kelas_id=B.id ".$where;
						
				
				//echo $sql;
				if($p1!=""){
					$data=$this->result_query($sql);
					
					return $data;
				}
				if($id){
					return $this->result_query($sql,'row_array');
				}
			break;
			case "tree_pelajaran":
				$json=array();
				$id_pel=$this->input->post('pel');
				switch($p1){
					
				case  "kelas":
					if($id_pel){
						$sql="SELECT * FROM tbl_bab_pelajaran WHERE cl_mata_pelajaran_id=".$id_pel;
						$res=$this->db->query($sql)->result_array();
						if(count($res)>0){
							$idx=0;
							foreach($res as $v){
								$sql="SELECT * FROM tbl_sub_bab_pelajaran WHERE tbl_bab_id=".$v['id'];
								$sub_bab=$this->db->query($sql)->result_array();
								$child=array();
								$json[$idx]=array("id"=>'BAB-'.$v['id'],
											  "text"=>" <span style='color:navy'>".$v['judul_bab']." (".count($sub_bab).")</span>",
											  "flag"=>"bab",
											  "state"=>'closed'
								);
								if(count($sub_bab)>0){
									foreach($sub_bab as $x){
										$child[]=array("id"=>$x['id'],
													  "text"=>"<span style='color:navy'>".$x['judul_sub_bab']."</span>",
													  "flag"=>"sub_bab"
										);
									}
								}else{
									$child[]=array("text"=>'Tidak Ada SubBab',"flag"=>"none");
								}
								$json[$idx]["children"]=$child;
								$idx++;
							}
						}
					}
				break;
				case "l_soal":
					if($id_pel){
						$thn=((int)date('Y')-5);
						$cat=array('UN (Ujian Nasional)','SMBPTN','Latihan Soal');
						$idx=0;
						foreach($cat as $v){
							$child=array();
							if($v=='UN (Ujian Nasional)')$flag='U';
							if($v=='SMBPTN')$flag='S';
							if($v=='Latihan Soal')$flag='L';
							
							$sql="SELECT A.*,D.nama_pelajaran 
								FROM tbl_bank_soal A
								LEFT JOIN tbl_sub_bab_pelajaran B ON A.tbl_sub_bab_id=B.id
								LEFT JOIN tbl_bab_pelajaran C ON B.tbl_bab_id=C.id
								LEFT JOIN cl_mata_pelajaran D ON C.cl_mata_pelajaran_id=D.id
								WHERE D.cl_kelas_id=".$this->auth['cl_kelas_id']." 
								AND A.tahun >= ".$thn." AND A.tbl_sub_bab_id=".$id_pel." 
								AND A.flag='".$flag."'";
							$soal=$this->db->query($sql)->result_array();
							$json[$idx]=array("id"=>'CAT-'.$v,
											  "text"=>" <span style='color:navy'>".$v." (".count($soal).")</span>",
											  "flag"=>"cat",
											  "state"=>'closed'
							);
							if(count($soal)>0){
								foreach($soal as $x){
									$child[]=array("id"=>$x['id'],
												   "text"=>"<span style='color:navy'>".$x['judul']." ".($v!='Latihan Soal' ? '(Tahun:'.$x['tahun'].')':'')."</span>",
												   "flag"=>"sub_bab"
									);	
								}
							}else{
								$child[]=array("text"=>'Tidak Ada Soal',"flag"=>"none");
							}
							$json[$idx]["children"]=$child;
							$idx++;
						}
					}
				break;
				case "soal":
					if($id_pel){
						$cek_bank=$this->db->get_where('tbl_bank_soal',array('tahun'=>$id_pel))->result_array();
						if(count($cek_bank)>0){
							$sql="SELECT * FROM cl_mata_pelajaran WHERE cl_kelas_id=".$this->auth['cl_kelas_id'];
							$pel=$this->db->query($sql)->result_array();
							if(count($pel)>0){
								$no=0;
								
								foreach($pel as $a){
									$child_bab=array();
									$sql="SELECT * FROM tbl_bab_pelajaran WHERE cl_mata_pelajaran_id=".$a['id'];
									$res=$this->db->query($sql)->result_array();
									//print_r($res);
									$json[$no]=array("id"=>'PELAJARAN-'.$a['id'],
													  "text"=>" <span style='color:navy'>".$a['nama_pelajaran']." (".count($res).")</span>",
													  "flag"=>"pelajaran",
													  "state"=>'closed'
									);
									
									if(count($res)>0){
										$idx=0;
										foreach($res as $v){
											
											$sql="SELECT * FROM tbl_sub_bab_pelajaran WHERE tbl_bab_id=".$v['id'];
											$sub_bab=$this->db->query($sql)->result_array();
											$child_bab[$idx]=array("id"=>$v['id'],
															   "text"=>" <span style='color:navy'>".$v['judul_bab']." (".count($sub_bab).")</span>",
															   "flag"=>"bab",
															   "state"=>'closed'
											);
											$child=array();
											if(count($sub_bab)>0){
												foreach($sub_bab as $x){
													$child[]=array("id"=>$x['id'],
																  "text"=>"<span style='color:navy'>".$x['judul_sub_bab']."</span>",
																  "flag"=>"sub_bab",
																  "tahun"=>$id_pel
													);
												}
											}else{
												$child[]=array("text"=>'Tidak Ada SubBab',"flag"=>"none");
											}
											//$json[$no]["children"]=$child_bab;
											$child_bab[$idx]["children"]=$child;
											$idx++;
										}
									}else{
										$child_bab[]=array("text"=>'Tidak Ada Bab',"flag"=>"none");
									}
									$json[$no]["children"]=$child_bab;
									$no++;
								}
							}
						}
					}
				break;
				case "un":
				case "smbptn":
					if($id_pel){
						if($p1=="un")$flag='U';
						if($p1=="smbptn")$flag='S';
						$idx=0;
						$sql="SELECT * FROM cl_mata_pelajaran WHERE cl_kelas_id=".$this->auth['cl_kelas_id'];
						$pel=$this->db->query($sql)->result_array();
						if(count($pel)>0){
							foreach($pel as $v){
								$sql="SELECT A.*,D.nama_pelajaran 
								FROM tbl_bank_soal A
								LEFT JOIN tbl_sub_bab_pelajaran B ON A.tbl_sub_bab_id=B.id
								LEFT JOIN tbl_bab_pelajaran C ON B.tbl_bab_id=C.id
								LEFT JOIN cl_mata_pelajaran D ON C.cl_mata_pelajaran_id=D.id
								WHERE D.cl_kelas_id=".$this->auth['cl_kelas_id']." 
								AND A.tahun=".$id_pel." AND D.id=".$v['id']." AND A.flag='".$flag."'";
								$soal=$this->db->query($sql)->result_array();
								$child=array();
								$json[$idx]=array("id"=>'PEL-'.$v['id'],
											  "text"=>" <span style='color:navy'>".$v['nama_pelajaran']." (".count($soal).")</span>",
											  "flag"=>"pel",
											  "state"=>'closed'
								);
								if(count($soal)>0){
									foreach($soal as $x){
										$child[]=array("id"=>$x['id'],
													  "text"=>"<span style='color:navy'>".$x['judul']."</span>",
													  "flag"=>"sub_bab"
										);
									}
								}else{
									$child[]=array("text"=>'Tidak Ada Soal',"flag"=>"none");
								}
								$json[$idx]["children"]=$child;
								$idx++;
							}
						}
						
						
						
					}
				break;					
				}
				return json_encode($json);
			break;
			case "tbl_bab_pelajaran":
				
				if($id){
					$where .=" AND A.id = '".$id."'";
				}
				$sql="SELECT A.*,B.nama_pelajaran,B.cl_kelas_id 
						FROM tbl_bab_pelajaran A 
						LEFT JOIN cl_mata_pelajaran B ON A.cl_mata_pelajaran_id=B.id
						LEFT JOIN cl_kelas C ON B.cl_kelas_id=C.id ".$where;
				//echo $sql;
				if($id){
					return $this->result_query($sql,'row_array');
				}
			break;
			case "tbl_sub_bab_pelajaran":
				
				if($id){
					$where .=" AND A.id = '".$id."'";
				}
				$sql="SELECT A.*,B.judul_bab,C.cl_kelas_id 
						FROM tbl_sub_bab_pelajaran A 
						LEFT JOIN tbl_bab_pelajaran B ON A.tbl_bab_id=B.id
						LEFT JOIN cl_mata_pelajaran C ON B.cl_mata_pelajaran_id=C.id
						LEFT JOIN cl_kelas D ON C.cl_kelas_id=D.id ".$where;
				//echo $sql;
				if($id){
					return $this->result_query($sql,'row_array');
				}
			break;
			case "bab":
				if($p1!="all"){$where .=" AND cl_mata_pelajaran_id=".$this->input->post('id');}
				$sql="SELECT * FROM tbl_bab_pelajaran ".$where; 
				return $this->result_query($sql);
			break;
			case "mata_pel":
				$sql="SELECT * FROM cl_mata_pelajaran ";
				return $this->result_query($sql);
			break;
			case "sub_bab":
				
				if($p1!="all"){$where .=" AND tbl_bab_id=".$this->input->post('id_bab');}
				$sql="SELECT * FROM tbl_sub_bab_pelajaran ".$where;
				return $this->result_query($sql);
			break;
			case "materi":
				$sql="SELECT * FROM tbl_materi WHERE tbl_sub_bab_id=".$this->input->post('id_sub_bab');
				return $this->result_query($sql);
			break;
			case "konten":
				$sql="SELECT A.*,B.judul_sub_bab 
				FROM tbl_konten_materi A
				LEFT JOIN tbl_sub_bab_pelajaran B ON A.tbl_sub_bab_id=B.id
				WHERE A.tbl_sub_bab_id=".$this->input->post('id');
				$res=$this->result_query($sql,'row_array');
				$file=array();
				if(isset($res['id'])){
					$sql="SELECT A.*,C.judul_sub_bab,B.tbl_sub_bab_id,D.tayang  
						  FROM tbl_file_materi A
						  LEFT JOIN tbl_konten_materi B ON A.tbl_konten_materi_id=B.id
						  LEFT JOIN tbl_sub_bab_pelajaran C ON B.tbl_sub_bab_id=C.id
						  LEFT JOIN tbl_tayang_video D ON D.tbl_file_id=A.id AND D.flag='M'
						  WHERE tbl_konten_materi_id=".$res['id'];
					$file=$this->db->query($sql)->result_array();
					//echo $sql;
				}
				$data=array();
				$data['konten']=$res;
				$data['file']=$file;
				
				return $data;
			break;
			case "banksoal":
				$sql="SELECT A.*,A.isi_soal as isi_konten,B.judul_sub_bab 
				FROM tbl_bank_soal A
				LEFT JOIN tbl_sub_bab_pelajaran B ON A.tbl_sub_bab_id=B.id
				WHERE A.id=".$this->input->post('id');
				//echo $sql;
				$res=$this->result_query($sql,'row_array');
				$file=array();
				if(isset($res['id'])){
					$sql="SELECT A.*,C.judul_sub_bab,B.tbl_sub_bab_id,D.tayang
						  FROM tbl_file_bank_soal A
						  LEFT JOIN tbl_bank_soal B ON A.tbl_bank_soal_id=B.id
						  LEFT JOIN tbl_sub_bab_pelajaran C ON B.tbl_sub_bab_id=C.id
						  LEFT JOIN tbl_tayang_video D ON D.tbl_file_id=A.id AND D.flag='B'
					WHERE tbl_bank_soal_id=".$res['id'];
					$file=$this->db->query($sql)->result_array();
				}
				$data=array();
				$data['konten']=$res;
				$data['file']=$file;
				
				return $data;
			break;
			case "video":
				$data=array();
				$sub=$this->input->post('sub_bab');
				switch($p1){
					case "un":
					case "smbptn":
					case "soal":
						$flag='B';
						$table='tbl_file_bank_soal';
						$sql="SELECT A.*,C.judul_sub_bab,D.tayang 
						  FROM tbl_file_bank_soal A
						  LEFT JOIN tbl_bank_soal B ON A.tbl_bank_soal_id=B.id
						  LEFT JOIN tbl_sub_bab_pelajaran C ON B.tbl_sub_bab_id=C.id 
						  LEFT JOIN tbl_tayang_video D ON D.tbl_file_id=A.id AND D.flag='B'
						  WHERE 1=1 ";
							$a=" AND A.id=".$this->input->post('id');
							$b=" AND B.tbl_sub_bab_id=".$sub." AND A.id <> ".$this->input->post('id');
							$file=$this->db->query($sql.$a)->result_array();
							$lain=$this->db->query($sql.$b)->result_array();
					break;
					default:
						$flag='M';
						$table='tbl_file_materi';
						$sql="SELECT A.*,C.judul_sub_bab,D.tayang  
						  FROM tbl_file_materi A
						  LEFT JOIN tbl_konten_materi B ON A.tbl_konten_materi_id=B.id
						  LEFT JOIN tbl_sub_bab_pelajaran C ON B.tbl_sub_bab_id=C.id
						  LEFT JOIN tbl_tayang_video D ON D.tbl_file_id=A.id AND D.flag='M'
						 WHERE 1=1 ";
						$a=" AND A.id=".$this->input->post('id');
						$b=" AND B.tbl_sub_bab_id=".$sub." AND A.id <> ".$this->input->post('id');
						$file=$this->db->query($sql.$a)->result_array();
						$lain=$this->db->query($sql.$b)->result_array();
						//echo $this->db->last_query();
					break;
					
				}
				$data_tayang=array('tbl_file_id'=>$this->input->post('id'),
								   'flag'=>$flag
				);
				$ex=$this->db->get_where('tbl_tayang_video',array('tbl_file_id'=>$this->input->post('id'),'flag'=>$flag))->row_array();
				if(isset($ex['id'])){
					$data_tayang['tayang']=$ex['tayang']+1;
					$this->db->where('id',$ex['id']);
					$this->db->update('tbl_tayang_video',$data_tayang);
					
				}else{
					$data_tayang['tayang']=1;
					$this->db->insert('tbl_tayang_video',$data_tayang);
				}
				$data['tayang']=$data_tayang['tayang'];
				$data['file']=$file;
				$data['lain']=$lain;
				return $data;
			break;
			case "soal":
				$sql="SELECT A.*,A.isi_soal as isi_konten,B.judul_sub_bab 
				FROM tbl_bank_soal A
				LEFT JOIN tbl_sub_bab_pelajaran B ON A.tbl_sub_bab_id=B.id
				WHERE A.tbl_sub_bab_id=".$this->input->post('id')." 
				AND A.tahun=".$this->input->post('tahun')." AND A.flag='L'";
				$res=$this->result_query($sql);
				
				return $res;
			break;
			case "cl_kelas":
				$sql="SELECT * FROM cl_kelas ";
				if($this->auth['cl_user_group_id']==2){
					$sql="SELECT A.cl_kelas_id as id,C.kelas 
						FROM tbl_mengajar_kelas A 
						LEFT JOIN cl_guru B ON A.cl_guru_id=B.id
						LEFT JOIN cl_kelas C ON A.cl_kelas_id=C.id
						WHERE A.cl_guru_id=".$this->auth['cl_user_group_id'];
				}
				if($p1=='get'){
					$kelas=array();
					if($p2=='un'){
						
						$kelas[]=array('id'=>6,'kelas'=>'VI (Enam)');
						$kelas[]=array('id'=>9,'kelas'=>'IX (Sembilan)');
						$kelas[]=array('id'=>12,'kelas'=>'XII (Dua Belas)');
					}
					else if($p2=='smbptn')$kelas[]=array('id'=>12,'kelas'=>'XII (Dua Belas)');
					else $kelas=$this->result_query($sql);
					return $kelas; 
				}
				else{
					if($id){
						$where .=" AND id = '".$id."'";
						$sql = $sql.$where;
					}
					if($id){
						return $this->result_query($sql,'row_array');
					}
				}
				
			break;
			case "tbl_konten_materi":
				//echo "<pre>";print_r($this->auth);
				if($id){
					$where .=" AND A.id = '".$id."'";
				}
				if($this->auth['cl_user_group_id']==2){$where .=" AND A.create_by='".$this->auth['nama_user']."'";}
				$sql="SELECT A.*,B.judul_sub_bab,C.judul_bab,D.nama_pelajaran,E.kelas  
					FROM tbl_konten_materi A
					LEFT JOIN tbl_sub_bab_pelajaran B ON A.tbl_sub_bab_id=B.id
					LEFT JOIN tbl_bab_pelajaran C ON B.tbl_bab_id=C.id
					LEFT JOIN cl_mata_pelajaran D ON C.cl_mata_pelajaran_id=D.id
					LEFT JOIN cl_kelas E ON D.cl_kelas_id=E.id ".$where;
					//echo $sql;
				if($id){
					$data=array();
					$data['materi']= $this->result_query($sql,'row_array');
					$data['file']= $this->db->get_where('tbl_file_materi',array('tbl_konten_materi_id'=>$id))->result_array();
					//echo $this->db->last_query();
					return $data;
				}
			break;
			case "tbl_bank_soal":
				$cat=$this->input->post('cat');
				if($this->auth['cl_user_group_id']==2){$where .=" AND A.create_by='".$this->auth['nama_user']."'";}
				if($id){
					$where .=" AND A.id = '".$id."'";
				}
				
				if($cat){
					if($cat=='un')$where .=" AND A.flag = 'U'";
					if($cat=='smbptn')$where .=" AND A.flag = 'S'";
					if($cat=='latihan')$where .=" AND A.flag = 'L'";

				}
				$sql="SELECT A.*,B.judul_sub_bab 
					FROM tbl_bank_soal A
					LEFT JOIN tbl_sub_bab_pelajaran B ON A.tbl_sub_bab_id=B.id ".$where;
					//echo $sql;
				if($id){
					$data=array();
					$data['soal']= $this->result_query($sql,'row_array');
					$data['file']= $this->db->get_where('tbl_file_bank_soal',array('tbl_bank_soal_id'=>$id))->result_array();
					//echo $this->db->last_query();
					return $data;
				}
			break;
			case "tbl_materi":
				if($p1=='get'){
					$sql="SELECT tbl_sub_bab_id FROM tbl_konten_materi GROUP BY tbl_sub_bab_id";
					$exist=$this->db->query($sql)->result_array();
					if(count($exist)>0){
						$id=array();
						foreach($exist as $v){
							$id[]=$v['tbl_sub_bab_id'];
							
						}
						$where .=" AND id NOT IN (".join(',',$id).")";
					}
					$sql="SELECT * FROM tbl_sub_bab_pelajaran ".$where;
					return $this->result_query($sql);
				}else{
					if($id){
						$where .=" AND A.id = '".$id."'";
					}
					$sql="SELECT A.*,B.judul_sub_bab 
						FROM tbl_materi A LEFT JOIN tbl_sub_bab_pelajaran B ON A.tbl_sub_bab_id=B.id ".$where;
					//echo $sql;
					if($id){
						return $this->result_query($sql,'row_array');
					}
				}
			break;
			case "tbl_file_materi":
				if($id){
					$where .=" AND A.id = '".$id."'";
				}
					$sql="SELECT A.* FROM tbl_file_materi A ".$where;
					//echo $sql;exit;
				if($id){
					return $this->result_query($sql,'row_array');
				}else{
					return $this->result_query($sql);
				}
			break;
			case "cl_peserta":
				if($id){
					$where .=" AND A.id = '".$id."'";
				}
				$sql="SELECT A.*,B.kelas,C.kecamatan,D.kab_kota,E.provinsi,F.`status` as sts,F.reg_date 
					FROM cl_peserta A
					LEFT JOIN cl_kelas B ON A.cl_kelas_id=B.id
					LEFT JOIN cl_kecamatan C ON A.cl_kecamatan_kode=C.kode_kecamatan
					LEFT JOIN cl_kab_kota D ON A.cl_kab_kota_kode=D.kode_kab_kota
					LEFT JOIN cl_provinsi E ON A.cl_propinsi_kode=E.kode_prov
					LEFT JOIN tbl_user F ON A.tbl_user_id=F.nama_user ".$where;
				if($id)	return $this->result_query($sql,'row_array');
			break;
			case "cl_guru":
				//echo $id;exit;
				if($id){
					$where .=" AND A.id = '".$id."'";
				}
				$sql="SELECT A.*,B.`status` as sts,B.reg_date 
					FROM cl_guru A
					LEFT JOIN tbl_user B ON A.tbl_user_id=B.nama_user ".$where;
					//echo $sql;
				if($id)	{
					$data=array();
					$data['guru']=$this->result_query($sql,'row_array');
					$sql_kel="SELECT * FROM tbl_mengajar_kelas WHERE cl_guru_id=".$id;
					$kel=$this->result_query($sql_kel);
					$j_kel=array();
					foreach($kel as $x){$j_kel[]=$x['cl_kelas_id'];}
					
					//$data['kel']=$this->result_query($sql);
					$sql_pel="SELECT * FROM tbl_mengajar_pelajaran WHERE cl_guru_id=".$id;
					$pel=$this->result_query($sql_pel);
					$j_pel=array();
					foreach($pel as $x){$j_pel[]=$x['cl_mata_pelajaran_id'];}
					$data['kel']=json_encode($j_kel);
					$data['pel']=json_encode($j_pel);
					//echo "<pre>";print_r($data);
					return $data;
				}
			break;
			
		}
		return $this->result_query($sql,'json',$table,$footer);
	}
	function hitung_total_cost_act($id_act){
		$sql="SELECT SUM(total_cost)as total_cost 
				FROM tbl_are 
				WHERE tbl_acm_id=".$id_act;
		return $this->db->query($sql)->row('total_cost');
	}
	
	function get_combo($p1,$p2="",$p3=""){
		switch($p1){
			case "tbl_loc":
				$sql = "
					SELECT id, CONCAT(costcenter,' - ',loc_name) as txt
					FROM tbl_loc
					WHERE tbl_model_id = '".$this->modeling['id']."'
				";
			break;
			case "tbl_rdm":
				$sql = "
					SELECT id, CONCAT(resource,' - ',descript) as txt
					FROM tbl_rdm
					WHERE tbl_model_id = '".$this->modeling['id']."'
				";
			break;
			case "tbl_cdm":
				$sql = "
					SELECT id, cost_driver as txt
					FROM tbl_cdm
					WHERE tbl_model_id = '".$this->modeling['id']."'
				";
			break;
			case "cl_user_group":
				$sql = "
					SELECT id, group_user as txt
					FROM cl_user_group
				";
			break;
			case "segment_id":
				$sql = "
					SELECT id, seg_servicegroup_name as txt
					FROM tbl_seg_servicegroup
					WHERE pid IS NULL
				";
			break;
			case "service_group_id":				
				$sql = "
					SELECT id, seg_servicegroup_name as txt
					FROM tbl_seg_servicegroup
					WHERE pid = '".$p3."'
				";
			break;
			
		}
		return $this->db->query($sql)->result_array();
	}
	
	
	function result_query($sql,$type="",$table="",$footer=""){
		//print_r($footer);
		switch($type){
			case "json":
				$page = (integer) (($this->input->post('page')) ? $this->input->post('page') : "1");
				$limit = (integer) (($this->input->post('rows')) ? $this->input->post('rows') : "10");
				$count = $this->db->query($sql)->num_rows();
				
				if( $count >0 ) { $total_pages = ceil($count/$limit); } else { $total_pages = 0; } 
				if ($page > $total_pages) $page=$total_pages; 
				$start = $limit*$page - $limit; // do not put $limit*($page - 1)
				if($start<0) $start=0;
				 
				if($table == 'tbl_loc'){
					$sql .= " ORDER BY A.costcenter ASC ";
				}
				
				$sql = $sql . " LIMIT $start,$limit";
							
				$data=$this->db->query($sql)->result_array();  
				$key=$this->input->post('key');		
				if($data){
				   $responce = new stdClass();
				   $responce->rows= $data;
				   $responce->total =$count;
				   if($key){$responce->key =$key;}else{$responce->key='off';}
					if($footer!=""){
						$responce->footer =array($footer);
					}
				   return json_encode($responce);
				   
				}else{ 
				   $responce = new stdClass();
				   $responce->rows = 0;
				   $responce->total = 0;
				   if($key){$responce->key =$key;}else{$responce->key='off';}
				   if($footer!=""){
						$responce->footer =array($footer);
					}
				   return json_encode($responce);
				} 
			break;
			case "row_obj":return $this->db->query($sql)->row();break;
			case "row_array":return $this->db->query($sql)->row_array();break;
			default:return $this->db->query($sql)->result_array();break;
		}
	}
	
	function simpansavedata($table,$data,$sts_crud){ //$sts_crud --> STATUS NYEE INSERT, UPDATE, DELETE
		$this->db->trans_begin();
		
		if(isset($data['id']))unset($data['id']);
		
		switch ($table){
			case "cl_mata_pelajaran":
			case "tbl_bab_pelajaran":
			case "tbl_sub_bab_pelajaran":
			case "tbl_materi":
				//print_r($data);exit;
				if($table=='tbl_bab_pelajaran')unset($data['kelas']);
				if($table=='tbl_sub_bab_pelajaran')unset($data['kelas']);unset($data['cl_mata_pelajaran_id']);
				$data['create_date']=date('Y-m-d H:i:s');
				$data['create_by']=$this->auth['nama_user'];
				if($sts_crud=='edit'){
					$array_where=array('id'=>$this->input->post('id'));
				}
				if($sts_crud=='delete'){
					if($table=="tbl_materi"){
						$sql="SELECT * FROM tbl_konten_materi WHERE tbl_materi_id=".$this->input->post('id');
						$konten=$this->db->query($sql)->result_array();
						if(count($konten)>0){
							foreach($konten as $a){
								$sql="SELECT * FROM tbl_file_materi WHERE tbl_konten_materi_id=".$a['id'];
								$file=$this->db->query($sql)->result_array();
								if(count($file)>0){
									foreach($file as $x){
										if($x['file_materi']!=''){
											$path='__repository/file_materi/'.$x['create_by'].'/';
											chmod($path.$x['file_materi'],0777);
											unlink($path.$x['file_materi']);
										}
										$sql="DELETE FROM tbl_file_materi WHERE tbl_konten_materi_id=".$x['id'];
										$this->db->query($sql);
									}
									
								}
								$sql="DELETE FROM tbl_konten_materi WHERE id=".$a['id'];
								$this->db->query($sql);
							}
							
						}
					}
					if($table=="tbl_sub_bab_pelajaran"){
						$sql="SELECT * FROM tbl_materi WHERE tbl_sub_bab_id=".$this->input->post('id');
						$res=$this->db->query($sql)->result_array();
						if(count($res)>0){
							foreach($res as $v){
								$sql="SELECT * FROM tbl_file_materi WHERE tbl_konten_materi_id=".$v['id'];
								$file=$this->db->query($sql)->result_array();
								if(count($file)>0){
									foreach($file as $x){
										if($x['file_materi']!=''){
											$path='__repository/file_materi/'.$x['create_by'].'/';
											chmod($path.$x['file_materi'],0777);
											unlink($path.$x['file_materi']);
										}
										
									}
									
								}
								$sql="DELETE FROM tbl_file_materi WHERE tbl_konten_materi_id=".$v['id'];
								$this->db->query($sql);
							}
							$sql="DELETE FROM tbl_materi WHERE tbl_sub_bab_id=".$this->input->post('id');
							$this->db->query($sql);
						}
					}
					if($table=="tbl_bab_pelajaran"){
						$sql="SELECT * FROM tbl_sub_bab_pelajaran WHERE tbl_bab_id=".$this->input->post('id');
						$res_bab=$this->db->query($sql)->result_array();
						if(count($res_bab)>0){
							foreach($res_bab as $a){
								$sql="SELECT * FROM tbl_materi WHERE tbl_sub_bab_id=".$a['id'];
								$res=$this->db->query($sql)->result_array();
								if(count($res)>0){
									foreach($res as $v){
										$sql="SELECT * FROM tbl_file_materi WHERE tbl_konten_materi_id=".$v['id'];
										$file=$this->db->query($sql)->result_array();
										if(count($file)>0){
											foreach($file as $x){
												if($x['file_materi']!=''){
													$path='__repository/file_materi/'.$x['create_by'].'/';
													chmod($path.$x['file_materi'],0777);
													unlink($path.$x['file_materi']);
												}
											}
											$sql="DELETE FROM tbl_file_materi WHERE tbl_konten_materi_id=".$v['id'];
											$this->db->query($sql);
										}
										
									}
								}
								$sql="DELETE FROM tbl_materi WHERE tbl_sub_bab_id=".$a['id'];
								$this->db->query($sql);
							}
							$sql="DELETE FROM tbl_sub_bab_pelajaran WHERE tbl_bab_id=".$this->input->post('id');
							$this->db->query($sql);
						}
					}
					if($table=="cl_mata_pelajaran"){
						$sql="SELECT * FROM tbl_bab_pelajaran WHERE cl_mata_pelajaran_id=".$this->input->post('id');
						$res_pel=$this->db->query($sql)->result_array();
						if(count($res_pel)>0){
							foreach($res_pel as $b){
								$sql="SELECT * FROM tbl_sub_bab_pelajaran WHERE tbl_bab_id=".$b['id'];
								$res_bab=$this->db->query($sql)->result_array();
								if(count($res_bab)>0){
									foreach($res_bab as $a){
										$sql="SELECT * FROM tbl_materi WHERE tbl_sub_bab_id=".$a['id'];
										$res=$this->db->query($sql)->result_array();
										if(count($res)>0){
											foreach($res as $v){
												$sql="SELECT * FROM tbl_file_materi WHERE tbl_konten_materi_id=".$v['id'];
												$file=$this->db->query($sql)->result_array();
												if(count($file)>0){
													foreach($file as $x){
														if($x['file_materi']!=''){
															$path='__repository/file_materi/'.$x['create_by'].'/';
															chmod($path.$x['file_materi'],0777);
															unlink($path.$x['file_materi']);
														}
													}
													$sql="DELETE FROM tbl_file_materi WHERE tbl_konten_materi_id=".$v['id'];
													$this->db->query($sql);
												}
												
											}
											$sql="DELETE FROM tbl_materi WHERE tbl_sub_bab_id=".$a['id'];
											$this->db->query($sql);
										}
									}
									$sql="DELETE FROM tbl_sub_bab_pelajaran WHERE tbl_bab_id=".$b['id'];
									$this->db->query($sql);
								}
							}
							$sql="DELETE FROM tbl_bab_pelajaran WHERE cl_mata_pelajaran_id=".$this->input->post('id');
							$this->db->query($sql);
						}
					}
				}
			break;
			
			case "tbl_bank_soal":
				
				$id=$this->input->post('id');
				$data['create_date']=date('Y-m-d H:i:s');
				$data['create_by']=$this->auth['nama_user'];
				$flag=$this->input->post('flag');
				$embed_url=$this->input->post('embed_url');
				$data['tahun']=$data['tahunYear'];
				unset($data['tahunYear']);
				unset($data['kelas']);
				unset($data['mat_pel']);
				unset($data['bab']);
				//print_r($data);exit;
				unset($data['embed_url']);
				if($sts_crud=='edit'){
					$array_where=array('id'=>$id);
				}
				if($sts_crud=='delete'){
					$sql="SELECT * FROM tbl_file_bank_soal WHERE tbl_bank_soal_id=".$id;
					$file=$this->db->query($sql)->result_array();
					foreach($file as $v){
						if($v['file_materi']!=''){
							$path='__repository/bank_soal/'.$v['create_by'].'/';
							chmod($path.$v['file_materi'],0777);
							unlink($path.$v['file_materi']);
						}
					}
					$sql="DELETE FROM tbl_file_bank_soal WHERE tbl_bank_soal_id=".$id;
					$this->db->query($sql);
				}
			break;
			case "tbl_konten_materi":
				//print_r($data);exit;
				$id=$this->input->post('id');
				$data['create_date']=date('Y-m-d H:i:s');
				$data['create_by']=$this->auth['nama_user'];
				$flag=$this->input->post('flag');
				$embed_url=$this->input->post('embed_url');
				//unset($data['flag']);
				unset($data['kelas']);
				unset($data['mat_pel']);
				unset($data['bab']);
				unset($data['embed_url']);
				if($sts_crud=='edit'){
					$array_where=array('id'=>$id);
				}
				if($sts_crud=='delete'){
					$sql="SELECT * FROM tbl_file_materi WHERE tbl_konten_materi_id=".$id;
					$file=$this->db->query($sql)->result_array();
					foreach($file as $v){
						if($v['file_materi']!=''){
							$path='__repository/file_materi/'.$v['create_by'].'/';
							chmod($path.$v['file_materi'],0777);
							unlink($path.$v['file_materi']);
						}
					}
					$sql="DELETE FROM tbl_file_materi WHERE tbl_konten_materi_id=".$id;
					$this->db->query($sql);
				}
			break;
			case "cl_kelas":
				$data['create_date']=date('Y-m-d H:i:s');
				$data['create_by']=$this->auth['nama_user'];
				if($sts_crud=='edit'){
					$array_where=array('id'=>$this->input->post('id'));
				}
			break;
			case "cl_peserta":
			case "cl_guru":
				//print_r($data);exit;
				if($table=='cl_guru'){
					unset($data['kelas_na']);unset($data['mat_pel']);
					unset($data['cl_kelas_id']);unset($data['cl_mata_pelajaran_id']);
					
				}
				if($sts_crud=='add'){
					$data['tbl_user_id']=$data['nama_user'];
					$pwd=$this->encrypt->encode($data['pwd']);
					unset($data['nama_user']);
					unset($data['pwd']);
					$data_user=array('nama_user'=>$data['tbl_user_id'],
									 'pwd'=>$pwd,
									 'cl_user_group_id'=>($table=='cl_guru' ? 2 : 3),
									 'status'=>1,
									 'reg_date'=>date('Y-m-d H:i:s')				
					);
					$this->db->insert('tbl_user',$data_user);
				}
				if($sts_crud=='edit'){$array_where=array('id'=>$this->input->post('id'));}
				if($sts_crud=='delete'){
					if($table=='cl_peserta')$user=$this->db->get_where('cl_peserta',array('id'=>$this->input->post('id')))->row_array();
					else $user=$this->db->get_where('cl_guru',array('id'=>$this->input->post('id')))->row_array();
					if(isset($user['tbl_user_id'])){
						$sql="DELETE FROM tbl_user WHERE nama_user='".$user['tbl_user_id']."'";
						$this->db->query($sql);
					}
				}
			break;
			case "tbl_waktu":
				$ex=$this->db->get_where('tbl_waktu',array('cl_peserta_id'=>$data["cl_peserta_id"],'flag'=>1))->row_array();
				if(isset($ex["id"])){
					$sql="UPDATE tbl_waktu set flag=0,pakai='".$this->input->post('waktu')."' 
							WHERE id=".$ex["id"];
					$this->db->query($sql);
				}
			break;
			case "video_soal":
			case "video_materi":
				//print_r($data);exit;
				$judul=$data['judul_embed'];
				$embed=$data['embed_url'];
				$id_materi=$data['id_materi'];
				$data_insert=array('flag'=>'Y',
							'create_by'=>$this->auth['nama_user'],
							'create_date'=>date('Y-m-d H:i:s')
				);
				//echo count($judul);exit;
				if(count($judul)>0){
					$idx=0;
					foreach($judul as $v=>$x){
						if($x!=""){
							$idx++;
							//echo $x.'-'.$embed[$v];
							$data_insert['judul']=$x;
							$data_insert['embed_url']=$embed[$v];
							if($table=='video_materi'){
								$data_insert['tbl_konten_materi_id']=$id_materi;
								$this->db->insert('tbl_file_materi',$data_insert);
							}else{
								$data_insert['tbl_bank_soal_id']=$id_materi;
								$this->db->insert('tbl_file_bank_soal',$data_insert);
							}
						}
					}
					if($idx > 0){
						if($table=='video_materi'){
							$sql="UPDATE tbl_konten_materi SET flag_video='Y' WHERE id=".$id_materi;
						}else{
							$sql="UPDATE tbl_bank_soal SET flag_video='Y' WHERE id=".$id_materi;
						}
						$this->db->query($sql);
					}
				}
				if($this->db->trans_status() == false){
					$this->db->trans_rollback();
					return 0;
				}else{
					return $this->db->trans_commit();
				}
				//exit;
			break;
			default:
				if($sts_crud=='edit'){
					//unset($data['id']);
					$array_where=array('id'=>$this->input->post('id'));
				}
			break;
		}
		
		switch ($sts_crud){
			case "add":
					$this->db->insert($table,$data);
					$id=$this->db->insert_id();
					//$id=5;
					if($table=="tbl_konten_materi"){
						if($flag=='Y'){
							$data_file=array('tbl_konten_materi_id'=>$id,
											 'flag'=>$flag,
											 'embed_url'=>$embed_url,
											 'create_date'=>date('Y-m-d H:i:s'),
											 'create_by'=>$this->auth['nama_user']
											 
							);
							$this->db->insert('tbl_file_materi',$data_file);
						}
					}
					
					
					if($table=="cl_guru"){
						$p_kel=$this->input->post('cl_kelas_id');
						$p_pel=$this->input->post('cl_mata_pelajaran_id');
						$kel=explode(',',$p_kel);
						$pel=explode(',',$p_pel);
						if(count($kel)){
							foreach($kel as $x){
								$data_kel=array('cl_guru_id'=>$id,
												'cl_kelas_id'=>$x,
												'create_date'=>date('Y-m-d H:i:s'),
												'create_by'=>$this->auth['nama_user']
								);
								$this->db->insert('tbl_mengajar_kelas',$data_kel);
							}
						}
						if(count($pel)){
							foreach($pel as $x){
								$data_pel=array('cl_guru_id'=>$id,
												'cl_mata_pelajaran_id'=>$x,
												'create_date'=>date('Y-m-d H:i:s'),
												'create_by'=>$this->auth['nama_user']
								);
								$this->db->insert('tbl_mengajar_pelajaran',$data_pel);
							}
						}
					}
			break;
			case "edit":
				//$this->db->where($field_id,$id);
				$this->db->where($array_where);
				$this->db->update($table,$data);
					if($table=="cl_guru"){
						$id=$this->input->post('id');
						$this->db->delete('tbl_mengajar_kelas',array('cl_guru_id'=>$id));
						$this->db->delete('tbl_mengajar_pelajaran',array('cl_guru_id'=>$id));
						$p_kel=$this->input->post('cl_kelas_id');
						$p_pel=$this->input->post('cl_mata_pelajaran_id');
						$kel=explode(',',$p_kel);
						$pel=explode(',',$p_pel);
						if(count($kel)){
							foreach($kel as $x){
								$data_kel=array('cl_guru_id'=>$id,
												'cl_kelas_id'=>$x,
												'create_date'=>date('Y-m-d H:i:s'),
												'create_by'=>$this->auth['nama_user']
								);
								$this->db->insert('tbl_mengajar_kelas',$data_kel);
							}
						}
						if(count($pel)){
							foreach($pel as $x){
								$data_pel=array('cl_guru_id'=>$id,
												'cl_mata_pelajaran_id'=>$x,
												'create_date'=>date('Y-m-d H:i:s'),
												'create_by'=>$this->auth['nama_user']
								);
								$this->db->insert('tbl_mengajar_pelajaran',$data_pel);
							}
						}
					}
				
				//echo $this->db->last_query();
				
			break;
			case "delete":
				$id=$this->input->post('id');
				$this->db->where('id',$id);
				$this->db->delete($table);
			break;
		}
		//echo $this->db->last_query();exit;
		if($this->db->trans_status() == false){
			if($table!="tbl_konten_materi" && $table!="tbl_bank_soal"){
				$this->db->trans_rollback();
				return 0;
			}else{
				$this->db->trans_rollback();
				return json_encode(array('msg'=>0,'id'=>0));
			}		
			
		} else{
			if($table!="tbl_konten_materi" && $table!="tbl_bank_soal"){
				return $this->db->trans_commit();
			}else{
				$this->db->trans_commit();
				if($sts_crud=='delete'){return $this->db->trans_commit();}
				else{return json_encode(array('msg'=>1,'id'=>$id));}
			}		
			
		}
		
	}		
	function has_child($id){
		$rs = $this->db->get_where("tbl_acm",array('pid'=>$id))->result_array();
		
		return count($rs) > 0 ? true : false;
	}
	
	function config_act($id_grid,$id_tree,$sts=""){
		$this->db->trans_begin();
		if($sts!=""){//remove
			
			//$sql="update tbl_acm set pid=NULL WHERE id=".$id_tree;
			$sql="delete from tbl_acm WHERE id=".$id_tree;
			$this->db->query($sql);
		}
		else{
			foreach($id_grid as $v){
				$mst=$this->db->get_where('tbl_acm',array('id'=>$v,'tbl_model_id'=>$this->modeling['id']))->row();//IDENTIFIKASI GRID;
				$sts=0;
				//if($id_tree!=0){//MAIN ROOT
					$ex=$this->db->get_where('tbl_acm',array('pid'=>$id_tree,'tbl_model_id'=>$this->modeling['id']))->result_array();//CEK EXIST CHILD
					if(count($ex)>0){
						//$sql="SELECT * FROM tbl_acm "
						foreach($ex as $x){
							if($x['activity_code']==$mst->activity_code){
								$sts=1;
							}
						}
						
						if($sts==0){
							/*$sql="INSERT INTO tbl_acm (pid,tbl_model_id,descript,activity_code)
								SELECT $id_tree,tbl_model_id,descript,activity_code
								FROM tbl_acm WHERE id=".$v;
							$this->db->query($sql);
							*/
							$this->db->where(array('id'=>$v));
							$this->db->update('tbl_acm',array('pid'=>$id_tree));
						}
						
						
					}
					else{
						/*$sql="INSERT INTO tbl_acm (pid,tbl_model_id,descript,activity_code)
								SELECT $id_tree,tbl_model_id,descript,activity_code
								FROM tbl_acm WHERE id=".$v;
						$this->db->query($sql);
						*/
						$this->db->where(array('id'=>$v));
						$this->db->update('tbl_acm',array('pid'=>$id_tree));
					}
				/*}else{
					//$this->db->where(array('id'=>$v));
					//$this->db->update('tbl_acm',array('pid'=>$id_tree));
					$sql="INSERT INTO tbl_acm (pid,tbl_model_id,descript,activity_code)
								SELECT $id_tree,tbl_model_id,descript,activity_code
								FROM tbl_acm WHERE id=".$v;
					$this->db->query($sql);
				}*/
			}
		}
		
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return 0;
		} else{
			return $this->db->trans_commit();
		}
	}
	
	function crud_file($p1,$p2,$obj='',$nama_file=''){
		if($p1=='upload'){
		$this->db->trans_begin();
		
		$this->load->library("PHPExcel");
		$this->load->library('lib');
		$path="__repository/tmp_upload/";
		//echo $nama_file;exit;
		//$obj="file_are";
		//$nama_file="temp_are";
		$file_name=$this->lib->uploadnong($path,$obj,$nama_file);
		$folder_aplod = $path.$file_name;
		
		$ext = explode('.',$_FILES[$obj]['name']);
		$exttemp = sizeof($ext) - 1;
		$extension = $ext[$exttemp];
		
		//set php excel settings
		$cacheMethod   = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize' => '1600MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
		if($extension=='xls'){
			$lib="Excel5";
		}else{
			$lib="Excel2007";
		}
		$objReader =  PHPExcel_IOFactory::createReader($lib);//excel2007
		ini_set('max_execution_time', 123456);
		//end set
		
		$objPHPExcel = $objReader->load($folder_aplod); 
		$objReader->setReadDataOnly(true);
		$nama_sheet=$objPHPExcel->getSheetNames();
		$worksheet = $objPHPExcel->getSheet(0);
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
		/*echo "<pre>";
		print_r($sheetData);
		echo "</pre>";exit;
		*/
		switch($p2){
			case "are_emp":
				$bulan=$this->input->post('bulan_empMonth');
				$tahun=$this->input->post('tahun_empYear');
				$act_id=$this->input->post('act_id');
				for($i=5; $i <= $worksheet->getHighestRow(); $i++){
					$sql="SELECT A.*,B.rdm_qty FROM tbl_emp A 
					LEFT JOIN tbl_rdm B ON A.tbl_rdm_id=B.id 
					WHERE A.employee_id='".$worksheet->getCell("D".$i)->getCalculatedValue()."'";
					$get_emp_id=$this->db->query($sql)->row_array();
					if(!empty($get_emp_id)){
						if($worksheet->getCell("G".$i)->getCalculatedValue()!=''){
							$total_cost=$worksheet->getCell("G".$i)->getCalculatedValue();
							$persen=($total_cost/$get_emp_id['wages'])*100;
							$cost=$worksheet->getCell("G".$i)->getCalculatedValue();
						}
						if($worksheet->getCell("H".$i)->getCalculatedValue()!=''){
							$total_cost=($get_emp_id['wages'] * $worksheet->getCell("H".$i)->getCalculatedValue())/100;
							$persen=$worksheet->getCell("H".$i)->getCalculatedValue();
							$cost=$total_cost;
						}
						if($worksheet->getCell("I".$i)->getCalculatedValue()!=''){
							$total_cost=($get_emp_id['wages']/$get_emp_id['rdm_qty']) * $worksheet->getCell("I".$i)->getCalculatedValue();
							$cost=$total_cost;
							$persen=($cost/$get_emp_id['wages'])*100;
						}
						$array_na = array(
								"tbl_acm_id"=>$act_id,
								"tbl_emp_id"=>$get_emp_id['id'],
								
								"cost"=>$cost,
								"percent"=>$persen,
								"rd_qty"=>($worksheet->getCell("I".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("I".$i)->getCalculatedValue()),
								
								"cost_type"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
								"budget_type"=>$worksheet->getCell("K".$i)->getCalculatedValue(),
								"bulan"=>$bulan,
								"tahun"=>$tahun,
								"total_cost"=>$total_cost
						);
						$cek_data = $this->db->get_where('tbl_are', array('bulan'=>$bulan,'tahun'=>$tahun,'tbl_acm_id'=>$act_id,'tbl_emp_id'=>$get_emp_id['id']))->row_array();						
						if(empty($cek_data)){
							$this->db->insert('tbl_are',$array_na);
						}else{
							$this->db->where('id',$cek_data['id']);
							$this->db->update('tbl_are',$array_na);
						}
					}
					
				}			
				$total_cost_act=$this->hitung_total_cost_act($act_id);
				$ex=$this->db->get_where('tbl_acm_total_cost',array('tbl_acm_id'=>$act_id))->row();
				$data_total=array('tbl_acm_id'=>$act_id,
								  'bulan'=>$bulan,
								  'tahun'=>$tahun,
								  'total_cost'=>$total_cost_act
				);
				if(isset($ex->id)){
					$this->db->where('id',$ex->id);
					$this->db->update('tbl_acm_total_cost',$data_total);
				}else{
					$this->db->insert('tbl_acm_total_cost',$data_total);
				}

					
			break;
			case "are_exp":
				$act_id=$this->input->post('act_id_exp');
				$bulan=$this->input->post('bulan_expMonth');
				$tahun=$this->input->post('tahun_expYear');
				$grand_total=0;
				for($i=5; $i <= $worksheet->getHighestRow(); $i++){
					$get_loc=$this->db->get_where('tbl_loc',array('location'=>$worksheet->getCell("C".$i)->getCalculatedValue(),'costcenter'=>$worksheet->getCell("D".$i)->getCalculatedValue()))->row_array();
					//echo $this->db->last_query();exit;
					if(!empty($get_loc)){
						$sql="SELECT A.*,B.rdm_qty FROM tbl_exp A 
								LEFT JOIN tbl_rdm B ON A.tbl_rdm_id=B.id 
								WHERE A.account='".$worksheet->getCell("F".$i)->getCalculatedValue()."' 
								AND A.tbl_loc_id='".$get_loc['id']."'";
						$get_exp_id=$this->db->query($sql)->row_array();
						if(!empty($get_exp_id)){
							if($worksheet->getCell("I".$i)->getCalculatedValue()!=''){
								$total_cost=$worksheet->getCell("I".$i)->getCalculatedValue();
								$persen=($total_cost/$get_exp_id['amount'])*100;
								$cost=$worksheet->getCell("I".$i)->getCalculatedValue();
							}
							if($worksheet->getCell("J".$i)->getCalculatedValue()!=''){
								$total_cost=($get_exp_id['amount'] * $worksheet->getCell("J".$i)->getCalculatedValue())/100;
								$persen=$worksheet->getCell("J".$i)->getCalculatedValue();
								$cost=$total_cost;
							}
							if($worksheet->getCell("K".$i)->getCalculatedValue()!=''){
								$total_cost=($get_exp_id['amount']/$get_exp_id['rdm_qty']) * $worksheet->getCell("K".$i)->getCalculatedValue();
								$cost=$total_cost;
								$persen=($cost/$get_exp_id['amount'])*100;
							}
							//$grand_total =$grand_total+$total_cost;
							
							$array_na = array(
									"tbl_acm_id"=>$act_id,
									"tbl_exp_id"=>$get_exp_id['id'],
									
									"cost"=>$cost,
									"percent"=>$persen,
									"rd_qty"=>($worksheet->getCell("K".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("K".$i)->getCalculatedValue()),
									
									"cost_type"=>$worksheet->getCell("L".$i)->getCalculatedValue(),
									"costcenter"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
									"budget_type"=>$worksheet->getCell("M".$i)->getCalculatedValue(),
									"coefficient"=>($worksheet->getCell("N".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("N".$i)->getCalculatedValue()),
									"bulan"=>$bulan,
									"tahun"=>$tahun,
									"total_cost"=>$total_cost
							);
							$cek_data = $this->db->get_where('tbl_are', array('bulan'=>$bulan,'tahun'=>$tahun,'tbl_acm_id'=>$act_id,'tbl_exp_id'=>$get_exp_id['id']))->row_array();						
							if(empty($cek_data)){
								$this->db->insert('tbl_are',$array_na);
							}else{
								$this->db->where('id',$cek_data['id']);
								$this->db->update('tbl_are',$array_na);
							}
						}
					}
					
					
				}
				$total_cost_act=$this->hitung_total_cost_act($act_id);
				$ex=$this->db->get_where('tbl_acm_total_cost',array('tbl_acm_id'=>$act_id))->row();
				$data_total=array('tbl_acm_id'=>$act_id,
								  'bulan'=>$bulan,
								  'tahun'=>$tahun,
								  'total_cost'=>$total_cost_act
				);
				if(isset($ex->id)){
					$this->db->where('id',$ex->id);
					$this->db->update('tbl_acm_total_cost',$data_total);
				}else{
					$this->db->insert('tbl_acm_total_cost',$data_total);
				}
			break;
			case "act":
				$bulan=$this->input->post('bulan_upl');
				$tahun=$this->input->post('tahun_upl');
				$act_id=$this->input->post('act_id_act');
				for($i=5; $i <= $worksheet->getHighestRow(); $i++){
					$get_acm=$this->db->get_where('tbl_acm',array('activity_code'=>$worksheet->getCell("C".$i)->getCalculatedValue(),'tbl_model_id'=>$this->modeling['id']))->row_array();
					if(empty($get_acm)){
						$data=array('tbl_model_id'=>$this->modeling['id'],
									'pid'=>$act_id,
									'activity_code'=>$worksheet->getCell("C".$i)->getCalculatedValue(),
									'descript'=>$worksheet->getCell("D".$i)->getCalculatedValue()
						);
						$this->db->insert('tbl_acm',$data);
						$id_child=$this->db->insert_id();
					}
					else{
						$id_child=$get_acm['id'];
						if(!isset($get_acm['pid']) || $get_acm['pid']==''){
							$sql="update tbl_acm set pid=".$act_id." WHERE id=".$get_acm['id'];
							$this->db->query($sql);
						}
					}
					
					
					$array_na = array(
								"tbl_acm_id"=>$act_id,
								"tbl_acm_child_id"=>$id_child,
								"percent"=>($worksheet->getCell("E".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("E".$i)->getCalculatedValue()),
								"rd_qty"=>($worksheet->getCell("F".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("F".$i)->getCalculatedValue()),
								"cost_type"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
								"budget_type"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
								"bulan"=>$bulan,
								"tahun"=>$tahun,
						);
					
					$cek_data = $this->db->get_where('tbl_act_to_act', array('bulan'=>$bulan,'tahun'=>$tahun,'tbl_acm_id'=>$act_id,'tbl_acm_child_id'=>$id_child))->row_array();						
						if(empty($cek_data)){
							$this->db->insert('tbl_act_to_act',$array_na);
						}else{
							$this->db->where('id',$cek_data['id']);
							$this->db->update('tbl_act_to_act',$array_na);
						}
					
				}									
			break;
			
			case "acm":
				$bulan=$this->input->post('bulan_upl');
				$tahun=$this->input->post('tahun_upl');
				//print_r($_POST);exit;
			//	echo $bulan;exit;
				for($i=5; $i <= $worksheet->getHighestRow(); $i++){
					$array_na = array(
								"bulan"=>$bulan,
								"tahun"=>$tahun,
								"activity_code"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
								"descript"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
								"tbl_model_id"=>$this->modeling['id']
						);
					
					$cek_data = $this->db->get_where('tbl_acm', array('activity_code'=>$worksheet->getCell("C".$i)->getCalculatedValue(),'tbl_model_id'=>$this->modeling['id'],'bulan'=>$bulan,'tahun'=>$tahun))->row_array();						
					if(empty($cek_data)){
						$this->db->insert('tbl_acm',$array_na);
					}else{
						//$this->db->where('id',$cek_data['id']);
						//$this->db->update('tbl_acm',$array_na);
					}	
				}
			break;
		}
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return 0;
		} else{
			return $this->db->trans_commit();
		}
		
		}
		
		else{
			$this->load->helper('download');
			$data = file_get_contents("__repository/template_import/".$p2.".xlsx");
			force_download('Template', $data);
		}
		
	}
	function get_report($p1){
		$bulan=$this->input->post('bulan');
		$tahun=$this->input->post('tahun');
		switch($p1){
			case "sum_costing":
				
				$sql_cost_exp="SELECT SUM(total_cost)as total 
						FROM tbl_are A
						LEFT JOIN tbl_acm B ON A.tbl_acm_id=B.id
						WHERE B.tbl_model_id=".$this->modeling['id']." 
						AND A.tbl_exp_id IS NOT NULL
						AND A.bulan=".$bulan." AND A.tahun=".$tahun;
				$sql_cost_emp="SELECT SUM(total_cost)as total 
						FROM tbl_are A
						LEFT JOIN tbl_acm B ON A.tbl_acm_id=B.id
						WHERE B.tbl_model_id=".$this->modeling['id']."
						AND A.tbl_emp_id IS NOT NULL
						AND A.bulan=".$bulan." AND A.tahun=".$tahun;
				$sql_emp="SELECT sum(total)as total FROM tbl_emp 
						WHERE bulan=".$bulan." AND tahun=".$tahun." AND tbl_model_id=".$this->modeling['id'];
				$sql_exp="SELECT sum(amount)as total FROM tbl_exp 
						WHERE bulan=".$bulan." AND tahun=".$tahun." AND tbl_model_id=".$this->modeling['id'];
				$data=array();
				$data["tot_emp"]=number_format($this->db->query($sql_emp)->row('total'),2);
				$data["tot_exp"]=number_format($this->db->query($sql_exp)->row('total'),2);
				$data["tot_cost_emp"]=number_format($this->db->query($sql_cost_emp)->row('total'),2);
				$data["tot_cost_exp"]=number_format($this->db->query($sql_cost_exp)->row('total'),2);
				return json_encode($data);
				
				//$this->modeling['id'];
				
			break;
			case "sum_fte":
				$sql="SELECT A.id,A.employee_id,A.total,CONCAT(A.first,A.last)as name_na,B.fte_na,(A.total*B.fte_na)/100 as fte_cost
						FROM tbl_emp A
						LEFT JOIN(
							SELECT A.tbl_emp_id,sum(A.percent)as fte_na 
							FROM tbl_are A
							LEFT JOIN tbl_acm B ON A.tbl_acm_id=B.id
							WHERE A.bulan=".$bulan." 
							AND A.tahun=".$tahun." 
							AND B.tbl_model_id=".$this->modeling['id']."
							AND tbl_emp_id IS NOT NULL
							GROUP BY A.tbl_emp_id
						)AS B ON B.tbl_emp_id=A.id
						WHERE A.bulan=".$bulan." 
						AND A.tahun=".$tahun." 
						AND A.tbl_model_id=".$this->modeling['id'];
				return $this->result_query($sql);
			break;
			case "sum_exp":
				$sql="SELECT A.id,A.account,A.amount,A.descript as name_na,B.fte_na,(A.amount*B.fte_na)/100 as fte_cost
						FROM tbl_exp A
						LEFT JOIN(
							SELECT A.tbl_exp_id,sum(A.percent)as fte_na 
							FROM tbl_are A
							LEFT JOIN tbl_acm B ON A.tbl_acm_id=B.id
							WHERE A.bulan=".$bulan."
							AND A.tahun=".$tahun." 
							AND B.tbl_model_id=".$this->modeling['id']."
							AND tbl_exp_id IS NOT NULL
							GROUP BY A.tbl_exp_id
						)AS B ON B.tbl_exp_id=A.id
						WHERE A.bulan=".$bulan." 
						AND A.tahun=".$tahun."
						AND A.tbl_model_id=".$this->modeling['id'];
				return $this->result_query($sql);
			break;
		}
		
	}
	
	function duplicate_model($post){
		$this->db->trans_begin();
		//print_r($post);exit;
		$id_model_ex=$post['id_model'];
		//$id_model_ex=2;
		$costing=$post['costing'];
		$post['create_date']=date('Y-m-d H:i:s');
		$post['create_by']=$this->auth["nama_user"];
		unset($post['costing']);
		unset($post['id_model']);
		$this->db->insert('tbl_model',$post);
		$id_baru=$this->db->insert_id();
		//$id_baru=11;
		if(count($costing)>0){
			foreach($costing as $v){
				switch($v){
					case "emp":
						$sql="INSERT INTO tbl_emp (tbl_model_id,tbl_loc_id,employee_id,ssn,`first`,last,mi,wages,ot_premium,
							benefits,total,class,position,budget_1,budget_2,head_count,fte_count,
							tbl_rdm_id,rd_tot_qty,bugettype,cost_nbr,bulan,tahun)
							SELECT ".$id_baru.",tbl_loc_id,employee_id,ssn,`first`,last,mi,wages,ot_premium,
								benefits,total,class,position,budget_1,budget_2,head_count,fte_count,
								tbl_rdm_id,rd_tot_qty,bugettype,cost_nbr,bulan,tahun 
								FROM tbl_emp where tbl_model_id=".$id_model_ex;
						$this->db->query($sql);
					break;
					case "exp":
						$sql="INSERT INTO tbl_exp (tbl_model_id,tbl_loc_id,account,descript,amount,budget_1,budget_2,exp_level,tbl_rdm_id,rd_tot_qty,budgettype,budgetchg,bulan,tahun)
								SELECT ".$id_baru.",tbl_loc_id,account,descript,amount,budget_1,budget_2,exp_level,tbl_rdm_id,rd_tot_qty,budgettype,budgetchg,bulan,tahun 
								FROM tbl_exp WHERE tbl_model_id=".$id_model_ex;
						$this->db->query($sql);
							
					break;
					case "assets":
						$sql="INSERT INTO tbl_assets (tbl_model_id,tbl_loc_id,assets_id,assets_name,assets_description,cost,amount,budget_1,budget_2,tbl_rdm_id,rd_tot_qty,cost_type,cost_bucket,bulan,tahun,create_by,create_date)
								SELECT ".$id_baru.",tbl_loc_id,assets_id,assets_name,assets_description,cost,amount,budget_1,budget_2,tbl_rdm_id,rd_tot_qty,cost_type,cost_bucket,bulan,tahun,'".$this->auth["nama_user"]."','".date('Y-m-d H:i:s')."' 
								FROM tbl_assets WHERE tbl_model_id=".$id_model_ex;
						$this->db->query($sql);
					break;
					case "act":
						$sql="INSERT INTO tbl_acm (tbl_model_id,tbl_cdm_id,activity_code,
								descript,quantity,value_add,costtype,fte,fte_cost,`level`,head_count,val_cost,
								tbl_rdm_id,rd_tot_qty,note,bulan,tahun,budget,standart,capacity,target_quantity,
								budget_type,cost_type,cl_segment_id,cl_center_id,cl_class_id,cl_improvment_id,
								process_time,waiting_time,inspection_time,moving_time,nva_cost,tbl_process_id,
								tbl_root_couses_id,quantity_process,inefficiency_cost)
							SELECT ".$id_baru.",tbl_cdm_id,activity_code,
								descript,quantity,value_add,costtype,fte,fte_cost,`level`,head_count,val_cost,
								tbl_rdm_id,rd_tot_qty,note,bulan,tahun,budget,standart,capacity,target_quantity,
								budget_type,cost_type,cl_segment_id,cl_center_id,cl_class_id,cl_improvment_id,
								process_time,waiting_time,inspection_time,moving_time,nva_cost,tbl_process_id,
								tbl_root_couses_id,quantity_process,inefficiency_cost
								 FROM tbl_acm WHERE tbl_model_id=".$id_model_ex;
						$this->db->query($sql);
					break;
					case "costing":
						//GET EMP FROM ARE
						$sql_emp="SELECT A.tbl_acm_id,B.activity_code,B.descript,A.tbl_emp_id,C.employee_id,
							C.`last`,A.percent,A.cost,A.rd_qty,A.bulan,A.tahun,A.create_date,A.create_by,A.total_cost 
							FROM tbl_are A
							LEFT JOIN tbl_acm B ON A.tbl_acm_id=B.id
							LEFT JOIN tbl_emp C ON A.tbl_emp_id=C.id
							WHERE B.tbl_model_id=".$id_model_ex." AND tbl_emp_id IS NOT NULL";
							//echo $sql_emp;exit;
							$ex_emp=$this->db->query($sql_emp)->result_array();
						//GET EMP FROM EFX
						$sql_emp_efx="SELECT A.*,B.employee_id,C.account,C.descript,C.amount
							FROM tbl_efx A
							LEFT JOIN tbl_emp B ON A.tbl_emp_id=B.id
							LEFT JOIN tbl_exp C ON A.tbl_exp_id=C.id
							WHERE (B.tbl_model_id=".$id_model_ex." AND C.tbl_model_id=".$id_model_ex.") 
							AND A.tbl_emp_id <> 0 ";
							
							//echo $sql_emp;exit;
							$ex_emp_efx=$this->db->query($sql_emp_efx)->result_array();
						//GET ASSET FROM EFX	
						$sql_asset_efx="SELECT A.*,B.assets_id,B.assets_name,C.account,C.descript,C.amount
							FROM tbl_efx A
							LEFT JOIN tbl_assets B ON A.tbl_assets_id=B.id
							LEFT JOIN tbl_exp C ON A.tbl_exp_id=C.id
							WHERE (B.tbl_model_id=".$id_model_ex." AND C.tbl_model_id=".$id_model_ex.") 
							AND A.tbl_assets_id <> 0 ";
							
							//echo $sql_emp;exit;
							$ex_asset_efx=$this->db->query($sql_asset_efx)->result_array();
							
							
							
							
							//INSERT ARE ID BARU EMP
							foreach($ex_emp as $x){
								$sql="SELECT * FROM tbl_acm WHERE activity_code='".$x['activity_code']."' AND descript='".$x['descript']."' AND tbl_model_id=".$id_baru;
								$act_id=$this->db->query($sql)->row_array();
								$sql="SELECT * FROM tbl_emp WHERE employee_id='".$x['employee_id']."' AND tbl_model_id=".$id_baru;
								$emp_id=$this->db->query($sql)->row_array();
								if(count($act_id)>0){
									$data_are_emp=array('tbl_acm_id'=>$act_id['id'],
														'tbl_emp_id'=>$emp_id['id'],
														'percent'=>($x['percent']!="" ? $x['percent'] : 0),
														'cost'=>$x['cost'],
														'rd_qty'=>$x['rd_qty'],
														'bulan'=>$x['bulan'],
														'tahun'=>$x['tahun'],
														'create_date'=>date('Y-m-d H:i:s'),
														'create_by'=>$this->auth["nama_user"],
														'total_cost'=>$x['total_cost']
									);
									$this->db->insert('tbl_are',$data_are_emp);
								}
							}
							
							
							//INSERT EFX ID BARU EMP
							foreach($ex_emp_efx as $x){
								$sql="SELECT * FROM tbl_exp WHERE account='".$x['account']."' AND descript='".$x['descript']."' AND amount='".$x['amount']."'  AND tbl_model_id=".$id_baru;
								$exp_id=$this->db->query($sql)->row_array();
								//$sql="SELECT * FROM tbl_acm WHERE activity_code='".$x['activity_code']."' AND descript='".$x['descript']."' AND tbl_model_id=".$id_baru;
								//$act_id=$this->db->query($sql)->row_array();
								$sql="SELECT * FROM tbl_emp WHERE employee_id='".$x['employee_id']."' AND tbl_model_id=".$id_baru;
								$emp_id=$this->db->query($sql)->row_array();
								if(count($exp_id)>0){
									$data_efx_emp=array('tbl_exp_id'=>$exp_id['id'],
														'tbl_emp_id'=>$emp_id['id'],
														'tbl_assets_id'=>0,
														'percent'=>($x['percent']!="" ? $x['percent'] : 0),
														'cost_nbr'=>($x['cost_nbr']!="" ? $x['cost_nbr'] : 0),
														'rd_qty'=>($x['rd_qty']!="" ? $x['rd_qty'] : 0),
														'cost'=>($x['cost']!="" ? $x['cost'] : 0),
														'coeffisient'=>($x['coeffisient']!="" ? $x['coeffisient'] : 0),
														'budgettime'=>($x['budgettime']!="" ? $x['budgettime'] : 0),
														'budgetchg'=>($x['budgetchg']!="" ? $x['budgetchg'] : 0),
														'input_rate'=>($x['input_rate']!="" ? $x['input_rate'] : 0),
														'output_rate'=>($x['output_rate']!="" ? $x['output_rate'] : 0),
														'cost_type'=>$x['cost_type'],
														'create_date'=>date('Y-m-d H:i:s'),
														'create_by'=>$this->auth["nama_user"]
														
									);
									$this->db->insert('tbl_efx',$data_efx_emp);
								}
							}
							//INSERT EFX ID BARU ASSET
							foreach($ex_asset_efx as $x){
								$sql="SELECT * FROM tbl_exp WHERE account='".$x['account']."' AND descript='".$x['descript']."' AND amount='".$x['amount']."'  AND tbl_model_id=".$id_baru;
								$exp_id=$this->db->query($sql)->row_array();
								//$sql="SELECT * FROM tbl_acm WHERE activity_code='".$x['activity_code']."' AND descript='".$x['descript']."' AND tbl_model_id=".$id_baru;
								//$act_id=$this->db->query($sql)->row_array();
								$sql="SELECT * FROM tbl_assets WHERE assets_id='".$x['assets_id']."' AND assets_name='".$x['assets_name']."' AND tbl_model_id=".$id_baru;
								$asset_id=$this->db->query($sql)->row_array();
								if(count($exp_id)>0){
									$data_efx_asset=array('tbl_exp_id'=>$exp_id['id'],
														'tbl_emp_id'=>0,
														'tbl_assets_id'=>$asset_id['id'],
														'percent'=>($x['percent']!="" ? $x['percent'] : 0),
														'cost_nbr'=>($x['cost_nbr']!="" ? $x['cost_nbr'] : 0),
														'rd_qty'=>($x['rd_qty']!="" ? $x['rd_qty'] : 0),
														'cost'=>($x['cost']!="" ? $x['cost'] : 0),
														'coeffisient'=>($x['coeffisient']!="" ? $x['coeffisient'] : 0),
														'budgettime'=>($x['budgettime']!="" ? $x['budgettime'] : 0),
														'budgetchg'=>($x['budgetchg']!="" ? $x['budgetchg'] : 0),
														'input_rate'=>($x['input_rate']!="" ? $x['input_rate'] : 0),
														'output_rate'=>($x['output_rate']!="" ? $x['output_rate'] : 0),
														'cost_type'=>$x['cost_type'],
														'create_date'=>date('Y-m-d H:i:s'),
														'create_by'=>$this->auth["nama_user"]
														
									);
									$this->db->insert('tbl_efx',$data_efx_asset);
								}
							}
							
							$sql_exp="SELECT A.tbl_acm_id,B.activity_code,B.descript as act_des,A.tbl_exp_id,C.account,C.descript,C.amount,
							A.percent,A.cost,A.rd_qty,A.bulan,A.tahun,A.create_date,A.create_by,A.total_cost 
							FROM tbl_are A
							LEFT JOIN tbl_acm B ON A.tbl_acm_id=B.id
							LEFT JOIN tbl_exp C ON A.tbl_exp_id=C.id
							WHERE B.tbl_model_id=".$id_model_ex." AND tbl_exp_id IS NOT NULL";
							//echo $sql_exp;exit;
							$ex_exp=$this->db->query($sql_exp)->result_array();
							foreach($ex_exp as $y){
								$sql_act="SELECT * FROM tbl_acm WHERE activity_code='".$y['activity_code']."' AND descript='".$y['act_des']."' AND tbl_model_id=".$id_baru;
								//echo $sql_act;exit;
								$act_id_na=$this->db->query($sql_act)->row_array();
								//print_r($act_id_na);exit;
								//echo $act_id['id'];exit;
								$sql="SELECT * FROM tbl_exp WHERE account='".$y['account']."' AND descript='".$y['descript']."' AND amount='".$y['amount']."'  AND tbl_model_id=".$id_baru;
								
								$exp_id=$this->db->query($sql)->row_array();
								
								if(count($act_id_na)>0){
									
									$data_are_exp=array('tbl_acm_id'=>$act_id_na['id'],
														'tbl_exp_id'=>$exp_id['id'],
														'percent'=>($y['percent']!="" ? $y['percent'] : 0),
														'cost'=>$y['cost'],
														'rd_qty'=>$y['rd_qty'],
														'bulan'=>$y['bulan'],
														'tahun'=>$y['tahun'],
														'create_date'=>date('Y-m-d H:i:s'),
														'create_by'=>$this->auth["nama_user"],
														'total_cost'=>$y['total_cost']
									);
									$this->db->insert('tbl_are',$data_are_exp);
								}
							}
							
							$sql_asset="SELECT A.tbl_acm_id,B.activity_code,B.descript,A.tbl_assets_id,C.assets_id,C.assets_name,
							A.percent,A.cost,A.rd_qty,A.bulan,A.tahun,A.create_date,A.create_by,A.total_cost 
							FROM tbl_are A
							LEFT JOIN tbl_acm B ON A.tbl_acm_id=B.id
							LEFT JOIN tbl_assets C ON A.tbl_assets_id=C.id
							WHERE B.tbl_model_id=".$id_model_ex." AND tbl_assets_id IS NOT NULL;";
							//echo $sql_exp;exit;
							$ex_asset=$this->db->query($sql_asset)->result_array();
							foreach($ex_asset as $y){
								$sql_act="SELECT * FROM tbl_acm WHERE activity_code='".$y['activity_code']."' AND descript='".$y['descript']."' AND tbl_model_id=".$id_baru;
								//echo $sql_act;exit;
								$act_id_na=$this->db->query($sql_act)->row_array();
								//print_r($act_id_na);exit;
								//echo $act_id['id'];exit;
								$sql="SELECT * FROM tbl_assets WHERE assets_id='".$y['assets_id']."' AND assets_name='".$y['assets_name']."' AND tbl_model_id=".$id_baru;
								//echo $sql;exit;
								$asset_id=$this->db->query($sql)->row_array();
								
								if(count($act_id_na)>0){
									
									$data_are_asset=array('tbl_acm_id'=>$act_id_na['id'],
														'tbl_assets_id'=>$asset_id['id'],
														'percent'=>($y['percent']!="" ? $y['percent'] : 0),
														'cost'=>$y['cost'],
														'rd_qty'=>$y['rd_qty'],
														'bulan'=>$y['bulan'],
														'tahun'=>$y['tahun'],
														'create_date'=>date('Y-m-d H:i:s'),
														'create_by'=>$this->auth["nama_user"],
														'total_cost'=>$y['total_cost']
									);
									$this->db->insert('tbl_are',$data_are_asset);
								}
							}
							
							$sql_act_child="SELECT A.tbl_acm_id,B.activity_code,B.descript,A.tbl_acm_child_id,C.activity_code as act_code_child,C.descript as act_child,
							A.percent,A.cost,A.rd_qty,A.bulan,A.tahun,A.create_date,A.create_by,A.total_cost 
							FROM tbl_are A
							LEFT JOIN tbl_acm B ON A.tbl_acm_id=B.id
							LEFT JOIN tbl_acm C ON A.tbl_acm_child_id=C.id
							WHERE B.tbl_model_id=".$id_model_ex." AND tbl_acm_child_id IS NOT NULL;";
							//echo $sql_exp;exit;
							$ex_act=$this->db->query($sql_act_child)->result_array();
							foreach($ex_act as $y){
								$sql_act="SELECT * FROM tbl_acm WHERE activity_code='".$y['activity_code']."' AND descript='".$y['descript']."' AND tbl_model_id=".$id_baru;
								//echo $sql_act;exit;
								$act_id_na=$this->db->query($sql_act)->row_array();
								//print_r($act_id_na);exit;
								//echo $act_id['id'];exit;
								$sql_act_child="SELECT * FROM tbl_acm WHERE activity_code='".$y['act_code_child']."' AND descript='".$y['act_child']."' AND tbl_model_id=".$id_baru;
								//echo $sql_act;exit;
								$act_id_child=$this->db->query($sql_act_child)->row_array();
								
								if(count($act_id_na)>0){
									
									$data_are_act=array('tbl_acm_id'=>$act_id_na['id'],
														'tbl_acm_child_id'=>$act_id_child['id'],
														'percent'=>($y['percent']!="" ? $y['percent'] : 0),
														'cost'=>$y['cost'],
														'rd_qty'=>$y['rd_qty'],
														'bulan'=>$y['bulan'],
														'tahun'=>$y['tahun'],
														'create_date'=>date('Y-m-d H:i:s'),
														'create_by'=>$this->auth["nama_user"],
														'total_cost'=>$y['total_cost']
									);
									$this->db->insert('tbl_are',$data_are_act);
								}
							}
							
							$sql_total="SELECT A.tbl_acm_id,B.activity_code,B.descript,A.total_cost,A.bulan,A.tahun 
							FROM tbl_acm_total_cost A
							LEFT JOIN tbl_acm B ON A.tbl_acm_id=B.id
							WHERE B.tbl_model_id=".$id_model_ex."";
							//echo $sql_exp;exit;
							$ex_total=$this->db->query($sql_total)->result_array();
							foreach($ex_total as $y){
								$sql_act="SELECT * FROM tbl_acm WHERE activity_code='".$y['activity_code']."' AND descript='".$y['descript']."' AND tbl_model_id=".$id_baru;
								$act_id_na=$this->db->query($sql_act)->row_array();
								
								if(count($act_id_na)>0){
									$data_are_total=array('tbl_acm_id'=>$act_id_na['id'],
														'bulan'=>$y['bulan'],
														'tahun'=>$y['tahun'],
														'total_cost'=>$y['total_cost']
									);
									$this->db->insert('tbl_acm_total_cost',$data_are_total);
								}
							}
							
					break;
					case "cost_object":
						$sql="INSERT tbl_prm (
							tbl_model_id,prod_id,`level`,descript,reduction,net_revenue,
							activity_cost,direct_cost,profit_lost,uom,prod_qty,target_qty,segment_id,
							service_group_id,cost_rate,target_rate,qtyproduce,unit_cost,abc_cost,
							ovh_cost,revenue,profitable,abc_lower,ovh_lower,abc_cost_r,ovh_cost_r,rlu_date,rlu_time,bulan,tahun
						)
							SELECT ".$id_baru.",prod_id,`level`,descript,reduction,net_revenue,
							activity_cost,direct_cost,profit_lost,uom,prod_qty,target_qty,segment_id,
							service_group_id,cost_rate,target_rate,qtyproduce,unit_cost,abc_cost,
							ovh_cost,revenue,profitable,abc_lower,ovh_lower,abc_cost_r,ovh_cost_r,rlu_date,rlu_time,bulan,tahun
							FROM tbl_prm WHERE tbl_model_id=".$id_model_ex;
							
						$this->db->query($sql);
						
						$sql="SELECT A.*,B.prod_id,B.descript as desc_prm,C.activity_code,C.descript as desc_act
								FROM tbl_prd A
								LEFT JOIN tbl_prm B ON A.tbl_prm_id=B.id
								LEFT JOIN tbl_acm C ON A.tbl_acm_id=C.id
								WHERE A.tbl_model_id=".$id_model_ex;
						$ex_prd=$this->db->query($sql)->result_array();
						
						foreach($ex_prd as $x){
								$sql="SELECT * FROM tbl_prm WHERE prod_id='".$x['prod_id']."' AND descript='".$x['desc_prm']."' AND tbl_model_id=".$id_baru;
								$prm_id=$this->db->query($sql)->row_array();
								$sql="SELECT * FROM tbl_acm WHERE activity_code='".$x['activity_code']."' AND descript='".$x['desc_act']."' AND tbl_model_id=".$id_baru;
								$act_id=$this->db->query($sql)->row_array();
								if(count($prm_id)>0){
									$data_prd=array('tbl_model_id'=>$id_baru,
														'tbl_prm_id'=>$prm_id['id'],
														'tbl_cdm_id'=>$x['tbl_cdm_id'],
														'tbl_acm_id'=>$act_id['id'],
														'quantity'=>($x['quantity']!="" ? $x['quantity'] : 0),
														'cost_rate'=>($x['cost_rate']!="" ? $x['cost_rate'] : 0),
														'cost'=>($x['cost']!="" ? $x['cost'] : 0),
														'weight'=>($x['weight']!="" ? $x['weight'] : 0),
														'unweight'=>($x['unweight']!="" ? $x['unweight'] : 0),
														'bulan'=>$x['bulan'],
														'tahun'=>$x['tahun'],
														'create_date'=>date('Y-m-d H:i:s'),
														'create_by'=>$this->auth["nama_user"]		
									);
									$this->db->insert('tbl_prd',$data_prd);
								}
							}
						
					break;
				}
				
				//echo $v;
			}
			
		}
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return 0;
		} else{
			return $this->db->trans_commit();
		}
	}
	function simpan_reg($p1="",$p2=""){
		$this->db->trans_begin();
		$post = array();
        foreach($_POST as $k=>$v){if($this->input->post($k)!=""){$post[$k] = $this->db->escape_str($this->input->post($k));}}
		//print_r($post);exit;
		$post_user=array();
		$post_user['nama_user']=$post['nama_user'];
		$post['tbl_user_id']=$post['nama_user'];
		$post_user['pwd']=$this->encrypt->encode($post['pwd']);
		$post_user['cl_user_group_id']=3;
		$post_user['status']=0;
		$post_user['is_login']=0;
		$post_user['reg_date']=date('Y-m-d H:i:s');
		unset($post['nama_user']);unset($post['pwd']);unset($post['pwd2']);unset($post['sec']);
		if($p1=="act"){
			$post['status']=1;
			$post['act_date']=date('Y-m-d H:i:s');
			$this->db->where('nama_user',$p2);
			$this->db->update('tbl_user',$post);
		}else{
			$this->db->insert('tbl_user', $post_user);
			$this->db->insert('cl_peserta', $post);
		}
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return 0;
		}else{
			return $this->db->trans_commit();
			
		}
	}
}