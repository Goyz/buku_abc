<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/*
	Models Buatan Wahyu Jinggomen
*/

class mhomex extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata($this->config->item('user_data'))));
	}
	
	function getdata($type="", $balikan="json", $p1="", $p2="",$p3="",$p4="", $p5=""){
		$ci =& get_instance();
		$ci->load->model('mhome');
		$where = " WHERE 1=1 ";
		$costcenter = $this->input->post('cost_center');
		$month = $this->input->post('month');
		$year = $this->input->post('year');
		
		if($costcenter){
			$where .= " AND A.tbl_loc_id = '".$costcenter."' ";
		}
		
		if(!empty($month) && !empty($year)){
			$arrayres = $this->modeling;
			$arrayres['month'] = $month;
			$arrayres['year'] = $year;
			$this->session->set_userdata($this->config->item('modeling'), base64_encode(serialize($arrayres)));
		}
		
		if($month){
			$where .= " AND A.bulan = '".$month."' ";
		}
		
		if($year){
			$where .= " AND A.tahun = '".$year."' ";
		}
		
		if($p1){
			$where .= " AND A.id = '".$p1."' ";
		}
		
		switch($type){
			//Modul Resource
			case "tbl_emp":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
				
				$sql = "
					SELECT A.*, B.costcenter, C.resource
					FROM tbl_emp A
					LEFT JOIN tbl_loc B ON B.id = A.tbl_loc_id
					LEFT JOIN tbl_rdm C ON C.id = A.tbl_rdm_id
					$where
					ORDER BY A.employee_id ASC
				";
			break;
			case "tbl_exp":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
			
				$sql = "
					SELECT A.*, B.costcenter, C.resource
					FROM tbl_exp A
					LEFT JOIN tbl_loc B ON B.id = A.tbl_loc_id
					LEFT JOIN tbl_rdm C ON C.id = A.tbl_rdm_id
					$where
					ORDER BY A.account ASC
				";
			break;
			case "tbl_assets":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
				
				$sql = "
					SELECT A.*, B.costcenter, C.resource
					FROM tbl_assets A
					LEFT JOIN tbl_loc B ON B.id = A.tbl_loc_id
					LEFT JOIN tbl_rdm C ON C.id = A.tbl_rdm_id
					$where 
					ORDER BY A.assets_id ASC
				";
			break;
			//End Modul Resource
			
			case "tbl_loc":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
			
				$sql = "
					SELECT A.*
					FROM tbl_loc A
					$where
				";
			break;
			case "tbl_rdm":
					if($this->modeling){
						$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
					}else{
						$where .= " AND A.tbl_model_id = '0' ";
					}
					
					$bulan = $this->input->post('bulan');
					$tahun = $this->input->post('tahun');
					
					if($bulan && $tahun){
						$where .= " AND A.bulan = '".$bulan."' AND A.tahun = '".$tahun."' ";
					}
				//*/
				$sql = "
					SELECT A.*
					FROM tbl_rdm A
					$where
				";
			break;
			case "tbl_cdm":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
				
				$bulan = $this->input->post('bulan');
				$tahun = $this->input->post('tahun');
				
				if($bulan && $tahun){
					$where .= " AND A.bulan = '".$bulan."' AND A.tahun = '".$tahun."' ";
				}
				
				$sql = "
					SELECT A.*
					FROM tbl_cdm A
					$where
				";
			break;			
			
			//Modul Setting
			case "tbl_user":
				$sql = "
					SELECT A.*, B.group_user
					FROM tbl_user A
					LEFT JOIN cl_user_group B ON B.id = A.cl_user_group_id
					$where
				";
			break;
			case "cl_user_group":
				$sql = "
					SELECT A.*
					FROM cl_user_group A
					$where
				";
			break;
			case "cekusername":
				$sql = "
					SELECT id
					FROM tbl_user A
					WHERE A.nama_user = '".$p1."'
				";
			break;
			case "menu_parent":
				$sql = "
					SELECT A.*
					FROM tbl_menu A
					WHERE A.type_menu = 'P' AND A.status = '1'
				";
			break;
			case "menu_child":
				$sql = "
					SELECT A.*
					FROM tbl_menu A
					WHERE A.type_menu = 'C' AND A.parent_id = '".$p1."' AND A.status = '1'
				";
			break;
			case "previliges_menu":
				$sql = "
					SELECT A.*
					FROM tbl_prev_group A
					WHERE A.tbl_menu_id = '".$p1."' AND A.cl_user_group_id = '".$p2."'
				";
			break;
			//EndModul Setting
			
			//Modul Data Production & Reference Product Master
			case "tbl_prm":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
				
				$bulan = $this->input->post('bulan');
				$tahun = $this->input->post('tahun');
				$txtsearch = $this->input->post('txtsearch');
				
				if($bulan && $tahun){
					$where .= "
						AND  A.bulan = '".(int)$bulan."' AND A.tahun = '".$tahun."'
					";
				}
				
				if($txtsearch){
					$where .= "
						AND A.descript like '%".$txtsearch."%'
					";
				}
				
				$sql = "
					SELECT A.*
					FROM tbl_prm A
					$where
					ORDER BY A.prod_id ASC
				";
			break;
			case "tbl_cdms":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
				
				$bulan = $this->input->post('bulan');
				$tahun = $this->input->post('tahun');
				
				if($bulan && $tahun){
					$where .= " AND A.bulan = '".(int)$bulan."' AND A.tahun = '".$tahun."' ";
				}
				
				$sqlprd = "
					SELECT A.id, A.tbl_cdm_id 
					FROM tbl_prd A
					$where
				";
				$queryprd = $this->db->query($sqlprd)->result_array();
				$arraycdm = array();
				//$idx = 0;
				foreach($queryprd as $k=>$v){
					$arraycdm[$k] = $v['tbl_cdm_id'];
					//$idx++;
				}
				if($arraycdm){
					$join_array = join("','",$arraycdm);
					$where .= "
						AND A.id NOT IN ('".$join_array."') 
					";
				}
				
				$sql = "
					SELECT A.*
					FROM tbl_cdm A
					$where
				";
				
				//echo $sql;exit;
			break;			
			case "tbl_prd":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
				
				$bulan = $this->input->post('bulan');
				$tahun = $this->input->post('tahun');
				
				if($bulan && $tahun){
					$where .= "
						AND  A.bulan = '".(int)$bulan."' AND A.tahun = '".$tahun."'
					";
				}
				
				$sql = "
					SELECT A.*, B.cost_driver, C.prod_id
					FROM tbl_prd A
					LEFT JOIN tbl_cdm B ON B.id = A.tbl_cdm_id
					LEFT JOIN tbl_prm C ON C.id = A.tbl_prm_id
					$where
				";
			break;
			//End Data Production
			
			//Data Assignment
			//Tabs Employee
			case "emp_to_act":
			case "exp_to_emp":
				$id_employee = $this->input->post('id_employee');
				if($id_employee){
					$where .= "
						AND  A.tbl_emp_id = '".$id_employee."' 
					";
				}
				
				if($type == 'emp_to_act'){
					$select = " A.*, C.tbl_rdm_id, C.rd_tot_qty, B.descript as activity_name, C.total as gaji, C.bulan, C.tahun, D.descript AS resource_name ";
					$from = "tbl_are";
					$join = "
						LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id 
						LEFT JOIN tbl_emp C ON C.id = A.tbl_emp_id
						LEFT JOIN tbl_rdm D ON C.tbl_rdm_id = D.id
					";
				}elseif($type == 'exp_to_emp'){
					$select = " A.*, C.tbl_rdm_id, C.rd_tot_qty, B.descript as expense_name, B.amount, B.bulan, B.tahun, D.descript AS resource_name ";
					$from = "tbl_efx";
					$join = "
						LEFT JOIN tbl_exp B ON B.id = A.tbl_exp_id 
						LEFT JOIN tbl_emp C ON C.id = A.tbl_emp_id
						LEFT JOIN tbl_rdm D ON C.tbl_rdm_id = D.id
					";
				}
				
				$sql = "
					SELECT $select
					FROM $from A
					$join
					$where
				";
				//echo $sql;exit;
			break;
			
			//Tabs Expense
			case "exp_to_act":
			case "emp_to_exp":
			case "ass_to_exp":
				$id_expense = $this->input->post('id_expense');
				if($id_expense){
					$where .= "
						AND  A.tbl_exp_id = '".$id_expense."' 
					";
				}
				
				if($type == 'exp_to_act'){
					$select = " A.*, C.tbl_rdm_id, C.rd_tot_qty, B.descript as activity_name, C.amount as gaji, C.bulan, C.tahun, D.descript AS resource_name ";
					$from = "tbl_are";
					$join = "
						LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id 
						LEFT JOIN tbl_exp C ON C.id = A.tbl_exp_id
						LEFT JOIN tbl_rdm D ON C.tbl_rdm_id = D.id
					";
					
				}elseif($type == 'emp_to_exp'){
					$select = " A.*, C.tbl_rdm_id, C.rd_tot_qty, B.last as employee_name, B.wages as amount, B.bulan, B.tahun, D.descript AS resource_name ";
					$from = "tbl_efx";
					$join = "
						LEFT JOIN tbl_emp B ON B.id = A.tbl_emp_id
						LEFT JOIN tbl_exp C ON C.id = A.tbl_exp_id
						LEFT JOIN tbl_rdm D ON B.tbl_rdm_id = D.id						
					";
					$where .= " AND (A.tbl_emp_id IS NOT NULL OR A.tbl_emp_id <> 0)";
					
				}elseif($type == 'ass_to_exp'){
					$select = " A.*, C.tbl_rdm_id, C.rd_tot_qty, B.assets_name, B.amount, B.bulan, B.tahun, D.descript AS resource_name  ";
					$from = "tbl_efx";
					$join = "
						LEFT JOIN tbl_assets B ON B.id = A.tbl_assets_id 
						LEFT JOIN tbl_exp C ON C.id = A.tbl_exp_id
						LEFT JOIN tbl_rdm D ON C.tbl_rdm_id = D.id	
					";
					$where .= " AND (A.tbl_assets_id IS NOT NULL OR A.tbl_assets_id <> 0)";
					
				}
				
				$sql = "
					SELECT $select
					FROM $from A
					$join
					$where
				";
				//echo $sql;exit;
			break;
			
			//tabs assets
			case "ass_to_act":
			case "exp_to_ass":
				$id_assets = $this->input->post('id_assets');
				if($id_assets){
					$where .= "
						AND  A.tbl_assets_id = '".$id_assets."' 
					";
				}
				
				if($type == 'ass_to_act'){
					$select = " A.*, C.tbl_rdm_id, C.rd_tot_qty, B.descript as activity_name, C.amount as gaji, C.bulan, C.tahun, D.descript AS resource_name ";
					$from = "tbl_are";
					$join = "
						LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id 
						LEFT JOIN tbl_assets C ON C.id = A.tbl_assets_id
						LEFT JOIN tbl_rdm D ON D.id = B.tbl_rdm_id
					";
					$where .= " AND (A.tbl_assets_id IS NOT NULL OR A.tbl_assets_id <> 0)";
					
				}elseif($type == 'exp_to_ass'){
					$select = " A.*, C.tbl_rdm_id, C.rd_tot_qty, B.descript as expense_name, B.amount, B.bulan, B.tahun, D.descript AS resource_name ";
					$from = "tbl_efx";
					$join = "
						LEFT JOIN tbl_exp B ON B.id = A.tbl_exp_id 
						LEFT JOIN tbl_assets C ON C.id = A.tbl_assets_id
						LEFT JOIN tbl_rdm D ON D.id = B.tbl_rdm_id
					";
					$where .= " AND (A.tbl_assets_id IS NOT NULL AND A.tbl_assets_id <> 0)";
				}
				
				$sql = "
					SELECT $select
					FROM $from A
					$join
					$where
				";
				
				//echo $sql;exit;
			break;
			
			
			//Window Assignment Resource
			case "list_activity_employee":
			case "list_expense_employee":
			case "list_employee_expense":
			case "list_assets_expense":
			case "list_expense_assets":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}

				if($type == 'list_activity_employee'){
					$select = " A.id, A.activity_code, A.descript, A.tbl_rdm_id, A.rd_tot_qty ";
					$from = "tbl_acm";
					$flag = $this->input->post('flag');
					
					if($flag == "employee"){
						$id_beda = $this->input->post('id_employee');
						$field_where = "tbl_emp_id";
					}elseif($flag == "expense"){
						$id_beda = $this->input->post('id_expense');
						$field_where = "tbl_exp_id";	
					}elseif($flag == "assets"){
						$id_beda = $this->input->post('id_assets');
						$field_where = "tbl_assets_id";	
					}
					
					$field_select = "tbl_acm_id";
					$table_beda = "tbl_are";
				}elseif($type == 'list_expense_employee'){
					$select = " A.id, A.account, A.descript, A.tbl_rdm_id, A.rd_tot_qty ";
					$from = "tbl_exp";
					
					$id_beda = $this->input->post('id_employee');
					$field_where = "tbl_emp_id";
					$field_select = "tbl_exp_id";
					$table_beda = "tbl_efx";
				}elseif($type == 'list_employee_expense'){
					$select = " A.id, A.employee_id, A.last, A.tbl_rdm_id, A.rd_tot_qty";
					$from = "tbl_emp";
					
					$id_beda = $this->input->post('id_expense');
					$field_where = "tbl_exp_id";
					$field_select = "tbl_emp_id";
					$table_beda = "tbl_efx";					
					
				}elseif($type == 'list_assets_expense'){
					$select = " A.id, A.assets_name, A.assets_id, A.tbl_rdm_id, A.rd_tot_qty";
					$from = "tbl_assets";
					
					$id_beda = $this->input->post('id_expense');
					$field_where = "tbl_exp_id";
					$field_select = "tbl_assets_id";
					$table_beda = "tbl_efx";			
				}elseif($type == 'list_expense_assets'){
					$select = " A.id, A.account, A.descript, A.tbl_rdm_id, A.rd_tot_qty ";
					$from = "tbl_exp";
					
					$id_beda = $this->input->post('id_assets');
					$field_where = "tbl_assets_id";
					$field_select = "tbl_exp_id";
					$table_beda = "tbl_efx";	
				}
				
				$sql_beda = "
					SELECT ".$field_select." as id
					FROM ".$table_beda."
					WHERE ".$field_where." = '".$id_beda."'
				";
				$query_beda = $this->db->query($sql_beda)->result_array();
				if($query_beda){
					$bedanya = array();
					$idx_s = 0;
					foreach($query_beda as $k => $v){
						$bedanya[$idx_s] = $v['id'];
						$idx_s++;
					}
					if($bedanya){
						$where .= " AND A.id NOT IN ('".join("','",$bedanya)."')";
					}
				}
					
				$sql = "
					SELECT $select
					FROM $from A
					$where 
				";
				//echo $sql;exit;
			break;
			//End Data Assignment
			
			//tabs cost object
			case "act_to_cobj":
			case "cust_to_cobj":
			case "loc_to_cobj":
				$id_prm = $this->input->post('id_prm');
				if($id_prm){
					$where .= "
						AND  A.tbl_prm_id = '".$id_prm."' 
					";
				}
				
				if($type == 'act_to_cobj'){
					$select = " A.*, 
						B.descript as activity_name, C.descript as cost_driver_name, 
						( D.total_cost / B.quantity ) as cost_rate2
					";
					$from = "tbl_prd";
					$join = "
						LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id 
						LEFT JOIN tbl_cdm C ON C.id = A.tbl_cdm_id
						LEFT JOIN tbl_acm_total_cost D ON D.tbl_acm_id = B.id
						
					";
				}elseif($type == 'cust_to_cobj'){
					$select = " A.*, B.customer_name, B.revenue, C.cost_rate, (B.revenue - A.cost) as profit";
					$from = "tbl_ptp";
					$join = "
						LEFT JOIN tbl_cust B ON B.id = A.tbl_cust_id 
						LEFT JOIN tbl_prm C ON C.id = A.tbl_prm_id
					";
					$where .= " AND (tbl_cust_id IS NOT NULL OR tbl_cust_id <> 0) ";
				}elseif($type == 'loc_to_cobj'){
					$select = " A.*, B.location_name, B.revenue, C.cost_rate, (B.revenue - A.cost) as profit ";
					$from = "tbl_ptp";
					$join = "
						LEFT JOIN tbl_location B ON B.id = A.tbl_location_id 
						LEFT JOIN tbl_prm C ON C.id = A.tbl_prm_id
					";
					$where .= " AND (tbl_location_id IS NOT NULL OR tbl_location_id <> 0) ";
				}
				
				$sql = "
					SELECT $select
					FROM $from A
					$join
					$where
				";
				//echo $sql;exit;
			break;
			
			// Tabs Customer
			case "tbl_cust":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
				
				$bulan = $this->input->post('bulan');
				$tahun = $this->input->post('tahun');
				
				if($bulan && $tahun){
					$where .= " AND A.bulan = '".(int)$bulan."' AND A.tahun = '".$tahun."' ";
				}
				
				$sql = "
					SELECT A.*
					FROM tbl_cust A
					$where
					ORDER BY A.customer_id ASC
				";
			break;
			case "cobj_to_cust":
			case "loc_to_cust":
				$id_cust = $this->input->post('id_cust');
				if($id_cust){
					$where .= "
						AND  A.tbl_cust_id = '".$id_cust."' 
					";
				}
				
				if($type == 'cobj_to_cust'){
					$select = " A.*, B.descript as cost_object_name";
					$from = "tbl_ptp";
					$join = "
						LEFT JOIN tbl_prm B ON B.id = A.tbl_prm_id 
					";
					$where .= " AND (tbl_prm_id IS NOT NULL OR tbl_prm_id <> 0) ";
				}elseif($type == 'loc_to_cust'){
					$select = " A.*, B.location_name ";
					$from = "tbl_ptp";
					$join = "
						LEFT JOIN tbl_location B ON B.id = A.tbl_location_id 
					";
					$where .= " AND (tbl_location_id IS NOT NULL OR tbl_location_id <> 0) ";
				}
				
				$sql = "
					SELECT $select
					FROM $from A
					$join
					$where
				";
				
				//echo $sql;
			break;
			// End Tabs Customer
			
			// Tabs Location
			case "tbl_location":
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
				
				$bulan = $this->input->post('bulan');
				$tahun = $this->input->post('tahun');
				
				if($bulan && $tahun){
					$where .= " AND A.bulan = '".(int)$bulan."' AND A.tahun = '".$tahun."' ";
				}
				
				$sql = "
					SELECT A.*
					FROM tbl_location A
					$where
					ORDER BY A.location_id ASC
				";
			break;
			case "cobj_to_location":
			case "cust_to_location":
				$id_location = $this->input->post('id_location');
				if($id_location){
					$where .= "
						AND  A.tbl_location_id = '".$id_location."' 
					";
				}
				
				if($type == 'cobj_to_location'){
					$select = " A.*, B.descript as cost_object_name";
					$from = "tbl_ptp";
					$join = "
						LEFT JOIN tbl_prm B ON B.id = A.tbl_prm_id 
					";
					$where .= " AND (tbl_prm_id IS NOT NULL OR tbl_prm_id <> 0) ";
				}elseif($type == 'cust_to_location'){
					$select = " A.*, B.customer_name ";
					$from = "tbl_ptp";
					$join = "
						LEFT JOIN tbl_cust B ON B.id = A.tbl_cust_id 
					";
					$where .= " AND (tbl_cust_id IS NOT NULL OR tbl_cust_id <> 0) ";
				}
				
				$sql = "
					SELECT $select
					FROM $from A
					$join
					$where
				";
				
				//echo $sql;
			
			break;
			// End Tabs Location
			
			//Window Assignment Cost Object
			case "list_activity_costobject":
			case "list_customer_costobject":
			case "list_location_costobject":
			
			case "list_costobject_customer":
			case "list_location_customer":
			
			case "list_costobject_location":
			case "list_customer_location":
			
				if($this->modeling){
					$where .= " AND A.tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND A.tbl_model_id = '0' ";
				}
				
				$wheretambah = "";
				if($type == 'list_activity_costobject'){
					$select = " A.id, A.activity_code, A.descript, A.tbl_cdm_id ";
					$from = "tbl_acm";
					$where .= " AND A.tbl_cdm_id IS NOT NULL ";
					
					$id_beda = $this->input->post('id_prm');
					$field_where = "tbl_prm_id";
					$field_select = "tbl_acm_id";
					$table_beda = "tbl_prd";
				}elseif($type == 'list_customer_costobject'){
					$select = " A.id, A.customer_name, A.customer_id ";
					$from = "tbl_cust";
					
					$id_beda = $this->input->post('id_prm');
					$field_where = "tbl_prm_id";
					$field_select = "tbl_cust_id";
					$table_beda = "tbl_ptp";
					$wheretambah .= "AND (tbl_cust_id IS NOT NULL OR tbl_cust_id <> 0)";
				}elseif($type == 'list_location_costobject'){
					$select = " A.id, A.location_name, A.location_id ";
					$from = "tbl_location";
					
					$id_beda = $this->input->post('id_prm');
					$field_where = "tbl_prm_id";
					$field_select = "tbl_location_id";
					$table_beda = "tbl_ptp";
					$wheretambah .= "AND (tbl_location_id IS NOT NULL OR tbl_location_id <> 0)";
					
				}elseif($type == 'list_costobject_customer'){
					$select = " A.id, A.descript, A.prod_id ";
					$from = "tbl_prm";
					
					$id_beda = $this->input->post('id_cust');
					$field_where = "tbl_cust_id";
					$field_select = "tbl_prm_id";
					$table_beda = "tbl_ptp";
					$wheretambah .= "AND (tbl_prm_id IS NOT NULL OR tbl_prm_id <> 0)";
				}elseif($type == 'list_location_customer'){
					$select = " A.id, A.location_name, A.location_id ";
					$from = "tbl_location";
					
					$id_beda = $this->input->post('id_cust');
					$field_where = "tbl_cust_id";
					$field_select = "tbl_location_id";
					$table_beda = "tbl_ptp";
					$wheretambah .= "AND (tbl_location_id IS NOT NULL OR tbl_location_id <> 0)";
					
				}elseif($type == 'list_costobject_location'){
					$select = " A.id, A.descript, A.prod_id ";
					$from = "tbl_prm";
					
					$id_beda = $this->input->post('id_location');
					$field_where = "tbl_location_id";
					$field_select = "tbl_prm_id";
					$table_beda = "tbl_ptp";
					$wheretambah .= "AND (tbl_prm_id IS NOT NULL OR tbl_prm_id <> 0)";
				}elseif($type == 'list_customer_location'){
					$select = " A.id, A.customer_name, A.customer_id ";
					$from = "tbl_cust";
					
					$id_beda = $this->input->post('id_location');
					$field_where = "tbl_location_id";
					$field_select = "tbl_cust_id";
					$table_beda = "tbl_ptp";
					$wheretambah .= "AND (tbl_cust_id IS NOT NULL OR tbl_cust_id <> 0)";
					
					
					
				}
				
				$sql_beda = "
					SELECT ".$field_select." as id
					FROM ".$table_beda."
					WHERE ".$field_where." = '".$id_beda."' ".$wheretambah."
				";
				$query_beda = $this->db->query($sql_beda)->result_array();
				if($query_beda){
					$bedanya = array();
					$idx_s = 0;
					foreach($query_beda as $k => $v){
						$bedanya[$idx_s] = $v['id'];
						$idx_s++;
					}
					if($bedanya){
						$where .= " AND A.id NOT IN ('".join("','",$bedanya)."')";
					}
				}
					
				$sql = "
					SELECT $select
					FROM $from A
					$where 
				";
				//echo $sql; exit;
			break;
			
			//Get Total Cost
			case "total_cost":
				$where = "";
				if($p5 == 'expense_emp'){
					$where .= " AND (tbl_emp_id IS NOT NULL OR tbl_emp_id <> 0) ";
				}elseif($p5 == 'expense_ass'){
					$where .= " AND (tbl_assets_id IS NOT NULL OR tbl_assets_id <> 0) ";
				}elseif($p5 == 'customer_costobject'){
					$where .= " AND (tbl_cust_id IS NOT NULL OR tbl_cust_id <> 0) ";
				}elseif($p5 == 'location_costobject'){
					$where .= " AND (tbl_location_id IS NOT NULL OR tbl_location_id <> 0) ";
				}elseif($p5 == 'costobject_customer'){
					$where .= " AND (tbl_prm_id IS NOT NULL OR tbl_prm_id <> 0) ";
				}elseif($p5 == 'location_customer'){
					$where .= " AND (tbl_location_id IS NOT NULL OR tbl_location_id <> 0) ";
				}elseif($p5 == 'costobject_location'){
					$where .= " AND (tbl_prm_id IS NOT NULL OR tbl_prm_id <> 0) ";
				}elseif($p5 == 'customer_location'){
					$where .= " AND (tbl_cust_id IS NOT NULL OR tbl_cust_id <> 0) ";
				}
				
				if($p2 != 'tbl_prd'){
					$select_tambah = " , SUM(percent) as total_percent  ";
				}else{
					$select_tambah = "";
				}
				
				$sql = "
					SELECT SUM(".$p1.") as total_cost ".$select_tambah."
					FROM ".$p2."
					WHERE ".$p3." = '".$p4."' $where
				";
				//echo $sql;exit;
			break;
			case "total_percent_models":
				$where = "";
				if($p5 == 'expense_emp'){
					$where .= " AND (tbl_emp_id IS NOT NULL OR tbl_emp_id <> 0) ";
				}elseif($p5 == 'expense_ass'){
					$where .= " AND (tbl_assets_id IS NOT NULL OR tbl_assets_id <> 0) ";
				}
				
				$sql = "
					SELECT SUM(".$p1.") as total_percent 
					FROM ".$p2."
					WHERE ".$p3." = '".$p4."' $where
				";
				
				//echo $sql;exit;
			break;
			//$total_percent = $this->getdata('total_percent_models', 'row_array', "percent", "tbl_are", "tbl_exp_id", $data['tbl_exp_id']);
			//End Total Cost
			
			case "max_bulan":
				$sql = "
					SELECT max(bulan) as bln, max(tahun) as thn
					FROM ".$p1."
					WHERE tbl_model_id = '".$this->modeling['id']."'
				";
			break;
			
			//get total di expense
			case "total_expense":
				if($this->modeling){
					$where .= " AND tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$where .= " AND tbl_model_id = '0' ";
				}
				
				$sql = "
					SELECT SUM(A.amount) as total_expense
					FROM tbl_exp A
					$where
				";
			break;
			case "total_expense_activity":
			case "total_expense_employee":
			case "total_expense_assets":
				if($this->modeling){
					$whereexp = " AND tbl_model_id = '".$this->modeling['id']."' ";
				}else{
					$whereexp = " AND tbl_model_id = 0 ";
				}
				$sqlexpense = "
					SELECT A.id
					FROM tbl_exp A
					WHERE 1=1 $whereexp 
				";
				$dataexpense = $this->db->query($sqlexpense)->result_array();
				$array = array();
				foreach($dataexpense as $k=>$v){
					array_push($array, $v['id']);
				}
				$join_data = join("','",$array);
				
				if($type == 'total_expense_activity'){
					$sum = 'cost';
					$tabel = 'tbl_are A';
					$where .= " AND (tbl_exp_id <> 0 OR tbl_exp_id IS NOT NULL) ";
				}elseif($type == 'total_expense_employee'){
					$sum = 'cost';
					$tabel = 'tbl_efx A';
					$where .= " AND (tbl_emp_id <> 0 OR tbl_emp_id IS NOT NULL) ";
				}elseif($type == 'total_expense_assets'){
					$sum = 'cost';
					$tabel = 'tbl_efx A';
					$where .= " AND (tbl_assets_id <> 0 OR tbl_assets_id IS NOT NULL) ";
				}
				
				$sql = "
					SELECT SUM($sum) as $type
					FROM $tabel
					$where AND tbl_exp_id IN ('".$join_data."') 
				";
				
			break;
			//end get total di expense
			
		}
		
		if($balikan == 'json'){
			return $ci->mhome->result_query($sql,'json',$type);
		}elseif($balikan == 'row_array'){
			return $this->db->query($sql)->row_array();
		}elseif($balikan == 'result_array'){
			return $this->db->query($sql)->result_array();
		}
	}
	
	function getreport($type="", $p1="", $p2="", $p3=""){
		$array = array();
		$bulan = $this->input->post('bulan');
		$tahun = $this->input->post('tahun');
		
		if(!$bulan){
			$bulan = date('m');
		}
		if(!$tahun){
			$tahun = date('Y');
		}
		
		switch($type){
			case "profit_detail":
				$sql1 = "
					SELECT *
					FROM tbl_seg_servicegroup
					WHERE pid IS NULL
				";
				$query1 = $this->db->query($sql1)->result_array();
				$grand_total_rev  = 0;
				$grand_total_abc = 0;
				$grand_total_prof = 0;
				
				foreach($query1 as $k => $v){
					$total_rev_segment = 0;
					$total_abc_segment = 0;
					$total_prof_segment = 0;
					
					$array['detail'][$k]['id_segment'] = $v['id'];
					$array['detail'][$k]['nama_segment'] = $v['seg_servicegroup_name'];
					$array['detail'][$k]['detail_service'] = array();
					
					$sql2 = "
						SELECT *
						FROM tbl_seg_servicegroup
						WHERE pid = '".$v['id']."'
					";
					$query2 = $this->db->query($sql2)->result_array();
					
					foreach($query2 as $z => $x){
						$total_rev_service = 0;
						$total_abc_service = 0;
						$total_prof_service = 0;
						
						$array['detail'][$k]['detail_service'][$z]['id_service'] = $x['id'];
						$array['detail'][$k]['detail_service'][$z]['nama_service'] = $x['seg_servicegroup_name'];
						$array['detail'][$k]['detail_service'][$z]['detail_prm'] = array();
						
						$sql3 = "
							SELECT *
							FROM tbl_prm
							WHERE service_group_id = '".$x['id']."'
							AND bulan = '".$bulan."' AND tahun = '".$tahun."'
						";
						$query3 = $this->db->query($sql3)->result_array();
						
						foreach($query3 as $y => $w){
							$pendapatan = $w['revenue'];
							$biaya_abc = $w['abc_cost'];
							$profitabilitas = ($w['revenue'] - $w['abc_cost']);
						
							$array['detail'][$k]['detail_service'][$z]['detail_prm'][$y]['nama_prm'] = $w['descript'];
							$array['detail'][$k]['detail_service'][$z]['detail_prm'][$y]['pendapatan'] = number_format($pendapatan,0,",",".");		
							$array['detail'][$k]['detail_service'][$z]['detail_prm'][$y]['biaya_abc'] =  number_format($biaya_abc,0,",",".");
							$array['detail'][$k]['detail_service'][$z]['detail_prm'][$y]['profitabilitas'] = number_format($profitabilitas,0,",",".");
							
							$total_rev_service += $pendapatan;
							$total_abc_service += $biaya_abc;
							$total_prof_service += $profitabilitas;
						}
						
						$array['detail'][$k]['detail_service'][$z]['total_rev_service'] =  number_format($total_rev_service,0,",",".");		
						$array['detail'][$k]['detail_service'][$z]['total_abc_service'] =  number_format($total_abc_service,0,",",".");		
						$array['detail'][$k]['detail_service'][$z]['total_prof_service'] = number_format($total_prof_service,0,",",".");		
						
						$total_rev_segment += $total_rev_service;
						$total_abc_segment += $total_abc_service;
						$total_prof_segment += $total_prof_service;
					}
					
					$array['detail'][$k]['total_rev_segment'] = number_format($total_rev_segment,0,",",".");		
					$array['detail'][$k]['total_abc_segment'] = number_format($total_abc_segment,0,",",".");		
					$array['detail'][$k]['total_prof_segment'] = number_format($total_prof_segment,0,",",".");		
					
					$grand_total_rev += $total_rev_segment;
					$grand_total_abc += $total_abc_segment;
					$grand_total_prof += $total_prof_segment;
				}
				
				$array['grand_total']['grand_total_rev'] = number_format($grand_total_rev,0,",",".");		
				$array['grand_total']['grand_total_abc'] = number_format($grand_total_abc,0,",",".");		
				$array['grand_total']['grand_total_prof'] = number_format($grand_total_prof,0,",",".");		
			break;
			case "resource_employee":
				$sql1 = "
					SELECT * 
					FROM tbl_emp
					WHERE bulan = '".$bulan."' AND tahun = '".$tahun."'
					AND tbl_model_id = '".$this->modeling['id']."'
				";
				$query1 = $this->db->query($sql1)->result_array();
				//echo $sql1;
				//print_r($query1);exit;
				
				$grand_total_emp  = 0;
				foreach($query1 as $k => $v){
					$array['detail'][$k]['id'] = $v['id'];
					$array['detail'][$k]['employee_id'] 	= $v['employee_id'];
					$array['detail'][$k]['employee_name'] 	= $v['last'];
					$array['detail'][$k]['total_cost'] 		= number_format($v['total'],0,",",".");
					$array['detail'][$k]['assign_activity'] = array();
					$array['detail'][$k]['assign_expense']  = array();
					
					$sql2 = "
						SELECT B.descript, A.cost, A.percent
						FROM tbl_are A
						LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id
						WHERE tbl_emp_id = '".$v['id']."'
					";
					$query2 = $this->db->query($sql2)->result_array();
					foreach($query2 as $t => $y){
						$array['detail'][$k]['assign_activity'][$t]['activity_name'] = $y['descript'];
						$array['detail'][$k]['assign_activity'][$t]['percent'] = $y['percent'];
						$array['detail'][$k]['assign_activity'][$t]['cost'] = number_format($y['cost'],0,",",".");
					}
					
					$sql3 = "
						SELECT B.descript, A.cost, A.percent
						FROM tbl_efx A
						LEFT JOIN tbl_exp B ON B.id = A.tbl_exp_id 
						WHERE tbl_emp_id = '".$v['id']."'
					";
					$query3 = $this->db->query($sql3)->result_array();
					foreach($query3 as $x => $z){
						$array['detail'][$k]['assign_expense'][$x]['expense_name'] = $z['descript'];
						$array['detail'][$k]['assign_expense'][$x]['percent'] = $z['percent'];
						$array['detail'][$k]['assign_expense'][$x]['cost'] = number_format($z['cost'],0,",",".");
					}
					
					$grand_total_emp += $v['total'];
				}
				$array['grand_total']['grand_total_emp'] = number_format($grand_total_emp,0,",",".");
			break;
			case "resource_expense":
				$sql1 = "
					SELECT * 
					FROM tbl_exp
					WHERE bulan = '".$bulan."' AND tahun = '".$tahun."'
					AND tbl_model_id = '".$this->modeling['id']."'
				";
				$query1 = $this->db->query($sql1)->result_array();
				
				$grand_total_exp  = 0;
				foreach($query1 as $k => $v){
					$array['detail'][$k]['id'] = $v['id'];
					$array['detail'][$k]['expense_id'] 		= $v['account'];
					$array['detail'][$k]['expense_name'] 	= $v['descript'];
					$array['detail'][$k]['total_cost'] 		= number_format($v['amount'],0,",",".");
					$array['detail'][$k]['assign_activity'] = array();
					$array['detail'][$k]['assign_employee']  = array();
					$array['detail'][$k]['assign_assets']  = array();
					
					$sql2 = "
						SELECT B.descript, A.cost, A.percent
						FROM tbl_are A
						LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id
						WHERE tbl_exp_id = '".$v['id']."'
					";
					$query2 = $this->db->query($sql2)->result_array();
					foreach($query2 as $t => $y){
						$array['detail'][$k]['assign_activity'][$t]['activity_name'] = $y['descript'];
						$array['detail'][$k]['assign_activity'][$t]['percent'] = $y['percent'];
						$array['detail'][$k]['assign_activity'][$t]['cost'] = number_format($y['cost'],0,",",".");
					}
					
					$sql3 = "
						SELECT B.last, A.cost, A.percent
						FROM tbl_efx A
						LEFT JOIN tbl_emp B ON B.id = A.tbl_emp_id 
						WHERE tbl_exp_id = '".$v['id']."'
						AND (tbl_emp_id IS NOT NULL OR tbl_emp_id <> 0)
					";
					$query3 = $this->db->query($sql3)->result_array();
					foreach($query3 as $x => $z){
						$array['detail'][$k]['assign_employee'][$x]['employee_name'] = $z['last'];
						$array['detail'][$k]['assign_employee'][$x]['percent'] = $z['percent'];
						$array['detail'][$k]['assign_employee'][$x]['cost'] = number_format($z['cost'],0,",",".");
					}
					
					$sql4 = "
						SELECT B.assets_name, A.cost, A.percent
						FROM tbl_efx A
						LEFT JOIN tbl_assets B ON B.id = A.tbl_assets_id 
						WHERE tbl_exp_id = '".$v['id']."'
						AND (tbl_assets_id IS NOT NULL OR tbl_assets_id <> 0)
					";
					$query4 = $this->db->query($sql4)->result_array();
					foreach($query4 as $j => $b){
						$array['detail'][$k]['assign_assets'][$j]['assets_name'] 	= $b['assets_name'];
						$array['detail'][$k]['assign_assets'][$j]['percent'] 		= $b['percent'];
						$array['detail'][$k]['assign_assets'][$j]['cost'] 			= number_format($b['cost'],0,",",".");
					}
					
					$grand_total_exp += $v['amount'];
				}
				
				$array['grand_total']['grand_total_exp'] = number_format($grand_total_exp,0,",",".");
			break;
			case "resource_assets":
				$sql1 = "
					SELECT * 
					FROM tbl_assets
					WHERE bulan = '".$bulan."' AND tahun = '".$tahun."'
					AND tbl_model_id = '".$this->modeling['id']."'
				";
				$query1 = $this->db->query($sql1)->result_array();
				
				$grand_total_assets  = 0;
				foreach($query1 as $k => $v){
					$array['detail'][$k]['id'] = $v['id'];
					$array['detail'][$k]['assets_id'] 		= $v['assets_id'];
					$array['detail'][$k]['assets_name'] 	= $v['assets_name'];
					$array['detail'][$k]['total_cost'] 		= number_format($v['amount'],0,",",".");
					$array['detail'][$k]['assign_activity'] = array();
					$array['detail'][$k]['assign_expense']  = array();
					
					$sql2 = "
						SELECT B.descript, A.cost, A.percent
						FROM tbl_are A
						LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id
						WHERE tbl_assets_id = '".$v['id']."'
					";
					$query2 = $this->db->query($sql2)->result_array();
					foreach($query2 as $t => $y){
						$array['detail'][$k]['assign_activity'][$t]['activity_name'] = $y['descript'];
						$array['detail'][$k]['assign_activity'][$t]['percent'] = $y['percent'];
						$array['detail'][$k]['assign_activity'][$t]['cost'] = number_format($y['cost'],0,",",".");
					}
					
					$sql3 = "
						SELECT B.descript, A.cost, A.percent
						FROM tbl_efx A
						LEFT JOIN tbl_exp B ON B.id = A.tbl_exp_id 
						WHERE tbl_exp_id = '".$v['id']."'
					";
					$query3 = $this->db->query($sql3)->result_array();
					foreach($query3 as $x => $z){
						$array['detail'][$k]['assign_expense'][$x]['expense_name'] = $z['descript'];
						$array['detail'][$k]['assign_expense'][$x]['percent'] = $z['percent'];
						$array['detail'][$k]['assign_expense'][$x]['cost'] = number_format($z['cost'],0,",",".");
					}
					
					$grand_total_assets += $v['amount'];
				}
				$array['grand_total']['grand_total_assets'] = number_format($grand_total_assets,0,",",".");
			break;
			case "costobject_cost":
				$sql1 = "
					SELECT * 
					FROM tbl_prm
					WHERE bulan = '".$bulan."' AND tahun = '".$tahun."'
					AND tbl_model_id = '".$this->modeling['id']."'
				";
				$query1 = $this->db->query($sql1)->result_array();
				
				$grand_total_net_revenue  = 0;
				$grand_total_revenue  = 0;
				$grand_total_profitable  = 0;
				foreach($query1 as $k => $v){
					$array['detail'][$k]['id'] = $v['id'];
					$array['detail'][$k]['prod_id'] 		= $v['prod_id'];
					$array['detail'][$k]['prod_name'] 		= $v['descript'];
					$array['detail'][$k]['net_revenue'] 	= number_format($v['net_revenue'],0,",",".");
					$array['detail'][$k]['revenue'] 		= number_format($v['revenue'],0,",",".");
					$array['detail'][$k]['profitable'] 		= number_format($v['profitable'],0,",",".");
					$array['detail'][$k]['assign_activity'] = array();
					$array['detail'][$k]['assign_customer'] = array();
					$array['detail'][$k]['assign_location'] = array();
					
					$sql2 = "
						SELECT A.*, B.descript as acm_desc, C.descript as cdm_desc,
							(B.quantity * B.capacity) as costrate2
						FROM tbl_prd A
						LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id
						LEFT JOIN tbl_cdm C ON C.id = A.tbl_cdm_id
						WHERE tbl_prm_id = '".$v['id']."'
					";
					$query2 = $this->db->query($sql2)->result_array();
					foreach($query2 as $t => $y){
						$array['detail'][$k]['assign_activity'][$t]['activity_name'] 	= $y['acm_desc'];
						$array['detail'][$k]['assign_activity'][$t]['cost_driver'] 		= $y['cdm_desc'];
						$array['detail'][$k]['assign_activity'][$t]['cost_rate'] 		= number_format($y['costrate2'],0,",",".");
						$array['detail'][$k]['assign_activity'][$t]['quantity'] 		= number_format($y['quantity'],0,",",".");
						$array['detail'][$k]['assign_activity'][$t]['total_cost'] 		= number_format($y['cost'],0,",",".");
					}
					
					$sql3 = "
						SELECT A.*, B.customer_name
						FROM tbl_ptp A
						LEFT JOIN tbl_cust B ON B.id = A.tbl_cust_id
						WHERE A.tbl_prm_id = '".$v['id']."'
						AND (A.tbl_cust_id IS NOT NULL OR A.tbl_cust_id <> 0)
					";
					$query3 = $this->db->query($sql3)->result_array();
					foreach($query3 as $x => $z){
						$array['detail'][$k]['assign_customer'][$x]['customer_name'] = $z['customer_name'];
						$array['detail'][$k]['assign_customer'][$x]['quantity'] = $z['quantity'];
						$array['detail'][$k]['assign_customer'][$x]['sell_price'] = number_format($z['sell_price'],0,",",".");
						$array['detail'][$k]['assign_customer'][$x]['cost'] = number_format($z['cost'],0,",",".");
					}
					
					$sql3 = "
						SELECT A.*, B.location_name
						FROM tbl_ptp A
						LEFT JOIN tbl_location B ON B.id = A.tbl_location_id
						WHERE A.tbl_prm_id = '".$v['id']."'
						AND (A.tbl_location_id IS NOT NULL OR A.tbl_location_id <> 0)
					";
					$query3 = $this->db->query($sql3)->result_array();
					foreach($query3 as $o => $n){
						$array['detail'][$k]['assign_location'][$o]['location_name'] = $n['location_name'];
						$array['detail'][$k]['assign_location'][$o]['quantity'] = $n['quantity'];
						$array['detail'][$k]['assign_location'][$o]['sell_price'] = number_format($n['sell_price'],0,",",".");
						$array['detail'][$k]['assign_location'][$o]['cost'] = number_format($n['cost'],0,",",".");
					}
					
					$grand_total_net_revenue += $v['net_revenue'];
					$grand_total_revenue += $v['revenue'];
					$grand_total_profitable += $v['profitable'];
				}
				
				$array['grand_total']['grand_total_net_revenue'] = number_format($grand_total_net_revenue,0,",",".");
				$array['grand_total']['grand_total_revenue'] = number_format($grand_total_revenue,0,",",".");
				$array['grand_total']['grand_total_profitable'] = number_format($grand_total_profitable,0,",",".");
			break;
			case "costobject_customer":
				$sql1 = "
					SELECT * 
					FROM tbl_cust
					WHERE bulan = '".$bulan."' AND tahun = '".$tahun."'
					AND tbl_model_id = '".$this->modeling['id']."'
				";
				$query1 = $this->db->query($sql1)->result_array();
				foreach($query1 as $k => $v){
					$array['detail'][$k]['id'] = $v['id'];
					$array['detail'][$k]['cust_id'] 		= $v['customer_id'];
					$array['detail'][$k]['cust_name'] 		= $v['customer_name'];
					$array['detail'][$k]['assign_costobject'] = array();
					$array['detail'][$k]['assign_location'] = array();
					
					$sql2 = "
						SELECT A.*, B.descript
						FROM tbl_ptp A 
						LEFT JOIN tbl_prm B ON B.id = A.tbl_prm_id
						WHERE tbl_cust_id = '".$v['id']."'
						AND (A.tbl_prm_id IS NOT NULL OR A.tbl_prm_id <> 0)
					";
					$query2 = $this->db->query($sql2)->result_array();
					foreach($query2 as $t => $y){
						$array['detail'][$k]['assign_costobject'][$t]['cost_object_name'] 	= $y['descript'];
						$array['detail'][$k]['assign_costobject'][$t]['quantity'] 			= number_format($y['quantity'],0,",",".");
						$array['detail'][$k]['assign_costobject'][$t]['sell_price'] 		= number_format($y['sell_price'],0,",",".");
						$array['detail'][$k]['assign_costobject'][$t]['cost'] 				= number_format($y['cost'],0,",",".");
					}
					
					$sql3 = "
						SELECT A.*, B.location_name
						FROM tbl_ptp A 
						LEFT JOIN tbl_location B ON B.id = A.tbl_location_id
						WHERE tbl_cust_id = '".$v['id']."'
						AND A.tbl_location_id IS NOT NULL AND A.tbl_location_id <> '0'
					";
					$query3 = $this->db->query($sql3)->result_array();
					foreach($query3 as $x => $z){
						$array['detail'][$k]['assign_location'][$x]['location_name'] 	= $z['location_name'];
						$array['detail'][$k]['assign_location'][$x]['quantity'] 		= number_format($z['quantity'],0,",",".");
						$array['detail'][$k]['assign_location'][$x]['sell_price'] 		= number_format($z['sell_price'],0,",",".");
						$array['detail'][$k]['assign_location'][$x]['cost'] 			= number_format($z['cost'],0,",",".");
					}
				}
			break;
			case "costobject_location":
				$sql1 = "
					SELECT * 
					FROM tbl_location
					WHERE bulan = '".$bulan."' AND tahun = '".$tahun."'
					AND tbl_model_id = '".$this->modeling['id']."'
				";
				$query1 = $this->db->query($sql1)->result_array();
				foreach($query1 as $k => $v){
					$array['detail'][$k]['id'] = $v['id'];
					$array['detail'][$k]['location_id'] 		= $v['location_id'];
					$array['detail'][$k]['location_name'] 		= $v['location_name'];
					$array['detail'][$k]['assign_costobject'] = array();
					$array['detail'][$k]['assign_customer'] = array();
					
					$sql2 = "
						SELECT A.*, B.descript
						FROM tbl_ptp A 
						LEFT JOIN tbl_prm B ON B.id = A.tbl_prm_id
						WHERE tbl_location_id = '".$v['id']."'
						AND (A.tbl_prm_id IS NOT NULL OR A.tbl_prm_id <> 0)
					";
					$query2 = $this->db->query($sql2)->result_array();
					foreach($query2 as $t => $y){
						$array['detail'][$k]['assign_costobject'][$t]['cost_object_name'] 	= $y['descript'];
						$array['detail'][$k]['assign_costobject'][$t]['quantity'] 			= number_format($y['quantity'],0,",",".");
						$array['detail'][$k]['assign_costobject'][$t]['sell_price'] 		= number_format($y['sell_price'],0,",",".");
						$array['detail'][$k]['assign_costobject'][$t]['cost'] 				= number_format($y['cost'],0,",",".");
					}
					
					$sql3 = "
						SELECT A.*, B.customer_name
						FROM tbl_ptp A 
						LEFT JOIN tbl_cust B ON B.id = A.tbl_cust_id
						WHERE tbl_location_id = '".$v['id']."'
						AND (A.tbl_cust_id IS NOT NULL OR A.tbl_cust_id <> 0)
					";
					$query3 = $this->db->query($sql3)->result_array();
					foreach($query3 as $x => $z){
						$array['detail'][$k]['assign_location'][$x]['customer_name'] 	= $z['customer_name'];
						$array['detail'][$k]['assign_location'][$x]['quantity'] 		= number_format($z['quantity'],0,",",".");
						$array['detail'][$k]['assign_location'][$x]['sell_price'] 		= number_format($z['sell_price'],0,",",".");
						$array['detail'][$k]['assign_location'][$x]['cost'] 			= number_format($z['cost'],0,",",".");
					}
					
				}
			break;
		}
		
		/*
		echo "<pre>";
		print_r($array);
		exit;
		//*/
		
		return $array;
	}
	
	function simpansavedata($table,$data,$sts_crud){ //savedata
		$this->db->trans_begin();
		
		$id = $data['id'];
		$field_id = 'id';
		$tot_cost = false;
		unset($data['id']);
		
		switch ($table){
			case "tbl_loc":
			case "tbl_emp":
			case "tbl_exp":
			case "tbl_assets":
			case "tbl_rdm":
			case "tbl_cdm":
			case "tbl_prm":
			case "tbl_prd":
				if($table != "tbl_rdm"){
					if($sts_crud == 'add'){
						$data['tbl_model_id'] = (isset($this->modeling['id']) ? $this->modeling['id'] : 0);
					}
				}
				
				if($table == 'tbl_prd'){
					unset($data['cost_driver']);
					unset($data['prod_id']);
				}
				
				if($table == 'tbl_emp'){
					$data['wages'] = str_replace(".", "", $data['wages']);
					$data['benefits'] = str_replace(".", "", $data['benefits']);
					$data['ot_premium'] = str_replace(".", "", $data['ot_premium']);
					$data['budget_1'] = str_replace(".", "", $data['budget_1']);
					$data['total'] = ($data['wages'] + $data['benefits'] + $data['ot_premium']);
				}
				
				if($table == 'tbl_exp'){
					$data['amount'] = str_replace(".", "", $data['amount']);
					$data['budget_1'] = str_replace(".", "", $data['budget_1']);
				}
				
				if($table == 'tbl_assets'){
					$data['amount'] = str_replace(".", "", $data['amount']);
					$data['budget_1'] = str_replace(".", "", $data['budget_1']);
				}
				
				if($table == 'tbl_prm'){
					$data['revenue'] = str_replace(".", "", $data['revenue']);
					$data['reduction'] = str_replace(".", "", $data['reduction']);
					$data['net_revenue'] = str_replace(".", "", $data['net_revenue']);
					$data['direct_cost'] = str_replace(".", "", $data['direct_cost']);
					//$data['activity_cost'] = str_replace(".", "", $data['activity_cost']);
					$data['abc_cost'] = str_replace(".", "", $data['abc_cost']);
					$data['profit_lost'] = str_replace(".", "", $data['profit_lost']);
					
					$data['prod_qty'] = str_replace(".", "", $data['prod_qty']);
					$data['uom'] = str_replace(".", "", $data['uom']);
					$data['cost_rate'] = str_replace(".", "", $data['cost_rate']);
					$data['target_qty'] = str_replace(".", "", $data['target_qty']);
					$data['target_rate'] = str_replace(".", "", $data['target_rate']);
				}
				
			break;
			case "tbl_user":
				if($sts_crud != 'delete'){
					$this->load->library('encrypt');
					$data['password'] = $this->encrypt->encode($data['password']);
				}
			break;
			case "user_role_group":
				$id_group = $id;
				$this->db->delete('tbl_prev_group', array('cl_user_group_id'=>$id_group) );
				if(isset($data['data'])){
					$postdata = $data['data'];
					$row=array();
					foreach($postdata as $v){
						$pecah = explode("_",$v);
						$row["buat"]=0;
						$row["baca"]=0;
						$row["ubah"]=0;
						$row["hapus"]=0;
						
						switch($pecah[0]){
							case "C":
								$row["buat"]=1;
							break;
							case "R":
								$row["baca"]=1;
							break;
							case "U":
								$row["ubah"]=1;
							break;
							case "D":
								$row["hapus"]=1;
							break;
						}
						
						$row["tbl_menu_id"] = $pecah[1];
						$row["cl_user_group_id"] = $id_group;
						
						$cek_data = $this->db->get_where('tbl_prev_group', array('tbl_menu_id'=>$pecah[1], 'cl_user_group_id'=>$id_group) )->row_array();
						
						if(!$cek_data){
							$this->db->insert('tbl_prev_group', $row);
						}else{
							if($row["buat"]==0)unset($row["buat"]);
							if($row["baca"]==0)unset($row["baca"]);
							if($row["ubah"]==0)unset($row["ubah"]);
							if($row["hapus"]==0)unset($row["hapus"]);
							
							$this->db->update('tbl_prev_group', $row, array('tbl_menu_id'=>$pecah[1], 'cl_user_group_id'=>$id_group) );
						}
					}	
				}
				
			break;
			case "import_data":
				$this->load->library("PHPExcel");
				$type_import = $data['modul_reference'];
				if(!empty($_FILES['file_import']['name'])){
					$ext = explode('.',$_FILES['file_import']['name']);
					$exttemp = sizeof($ext) - 1;
					$extension = $ext[$exttemp];
					
					$upload_path = "./__repository/tmp_upload/";
					$filename =  $this->lib->uploadnong($upload_path, 'file_import', $type_import); //$file.'.'.$extension;
					
					$folder_aplod = $upload_path.$filename;
					//set php excel settings
					$cacheMethod   = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
					$cacheSettings = array('memoryCacheSize' => '1600MB');
					PHPExcel_Settings::setCacheStorageMethod($cacheMethod,$cacheSettings);
					if($extension=='xls'){
						$lib="Excel5";
					}else{
						$lib="Excel2007";
					}
					
					//$inputFileType = PHPExcel_IOFactory::identify($upload_path.$filename);
					//$objReader =  PHPExcel_IOFactory::createReader($inputFileType);//excel2007
					$objReader =  PHPExcel_IOFactory::createReader($lib);//excel2007
					ini_set('max_execution_time', 123456);
					//end set
					
					$objPHPExcel = $objReader->load($folder_aplod); 
					$objReader->setReadDataOnly(true);
					$nama_sheet=$objPHPExcel->getSheetNames();
					$worksheet = $objPHPExcel->setActiveSheetIndex(0);
					$array_batch_insert = array();
					$array_batch_update = array();
					
					switch($type_import){
						case "tbl_tester":
							$datax = array();
							$sss = $objPHPExcel->getSheetByName('emp');
							$rws = $sss->toArray();
							$cols = array_splice($rws[0], 0, 22);
							unset($rws[0]);
							foreach($rws as $k => $v){
								$v[0] = str_replace("'", '', $v[0]);
								$datax[$k] = array_combine($cols, array_splice($v, 0, 22) );
							}
							
							echo "<pre>";
							print_r($datax);
							exit;
						break;
						case "tbl_emp":
							for($i=2; $i <= $worksheet->getHighestRow(); $i++){
								if($worksheet->getCell("B".$i)->getCalculatedValue() != "" || $worksheet->getCell("B".$i)->getCalculatedValue() != null){
								
									$arraynya = array(
										'employee_id'=>$worksheet->getCell("B".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'], 
										'bulan'=>(int)$worksheet->getCell("U".$i)->getCalculatedValue(),
										"tahun"=>(int)$worksheet->getCell("V".$i)->getCalculatedValue(),
									);
									$cek_data = $this->db->get_where('tbl_emp', $arraynya )->row_array();
									$get_loc = $this->db->get_where('tbl_loc', array('costcenter'=>$worksheet->getCell("A".$i)->getCalculatedValue(), 'tbl_model_id'=>$this->modeling['id']) )->row_array();
									
									$arrayrdm = array(
										'employee_id'=> $worksheet->getCell("B".$i)->getCalculatedValue(), 
										'tbl_model_id'=> $this->modeling['id'], 
										'bulan'=> ((int)$worksheet->getCell("U".$i)->getCalculatedValue() - 1),
										"tahun"=> (int)$worksheet->getCell("V".$i)->getCalculatedValue(),
									);
									$rdm = $this->db->get_where('tbl_emp', $arrayrdm )->row_array();
									
									//print_r($rdm);exit;
									
									if(isset($rdm)){
										if(!empty($rdm['tbl_rdm_id']) ){
											$rdm_value = $rdm['tbl_rdm_id'];
											$rdm_qty = $rdm['rd_tot_qty'];
										}else{
											$rdm_value = null;
											$rdm_qty = null;
										}
										
										if(!empty($rdm['id']) ){
											$emp_id = $rdm['id'];
										}else{
											$emp_id = null;
										}
									}else{
										$sqlrdm = "
											SELECT tbl_rdm_id, rd_tot_qty, id, bulan, tahun
												FROM tbl_emp
											WHERE employee_id = '".$worksheet->getCell("B".$i)->getCalculatedValue()."' AND tbl_model_id = '".$this->modeling['id']."' 
											AND bulan = (
												SELECT max(bulan) as bulannya from tbl_emp where tbl_model_id = '".$this->modeling['id']."' AND tahun = (SELECT MAX(tahun) from tbl_emp WHERE tbl_model_id = '".$this->modeling['id']."')
											) 
											AND tahun = (
												SELECT max(tahun) as tahunnya from tbl_emp where tbl_model_id = '".$this->modeling['id']."'
											)
										";
										$qryrdm = $this->db->query($sqlrdm)->row_array();
										
										if(isset($qryrdm)){
											if(!empty($qryrdm['tbl_rdm_id']) ){
												$rdm_value = $qryrdm['tbl_rdm_id'];
												$rdm_qty = $qryrdm['rd_tot_qty'];
											}else{
												$rdm_value = null;
												$rdm_qty = null;
											}
											
											if(!empty($qryrdm['tbl_rdm_id']) ){
												$emp_id = $qryrdm['id'];
											}else{
												$emp_id = null;
											}
										}else{
											$rdm_value = null;
											$rdm_qty = null;
											$emp_id = null;
										}
									}
									
									//echo $emp_id;exit;
									
									if(empty($cek_data)){
										$array_insert = array(
											"tbl_loc_id"=>(isset($get_loc['id']) ? $get_loc['id'] : 0),
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"employee_id"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"ssn"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"first"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"last"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"mi"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"wages"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"ot_premium"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"benefits"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"total"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"class"=>$worksheet->getCell("K".$i)->getCalculatedValue(),
											"position"=>$worksheet->getCell("L".$i)->getCalculatedValue(),
											"budget_1"=>$worksheet->getCell("M".$i)->getCalculatedValue(),
											"budget_2"=>($worksheet->getCell("N".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("N".$i)->getCalculatedValue() ),
											"head_count"=>$worksheet->getCell("O".$i)->getCalculatedValue(),
											"fte_count"=>$worksheet->getCell("P".$i)->getCalculatedValue(),
											"tbl_rdm_id"=>$rdm_value,
											"rd_tot_qty"=>$rdm_qty,
											"bugettype"=>$worksheet->getCell("Q".$i)->getCalculatedValue(),
											"cost_nbr"=>$worksheet->getCell("R".$i)->getCalculatedValue(),
											"bulan"=>(int)$worksheet->getCell("S".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("T".$i)->getCalculatedValue(),
										);
										array_push($array_batch_insert, $array_insert);	
										
										/*
										$employee_id = (int)$worksheet->getCell("B".$i)->getCalculatedValue();
										$bulan = (int)$worksheet->getCell("U".$i)->getCalculatedValue();
										$tahun = (int)$worksheet->getCell("V".$i)->getCalculatedValue();
										$insert = $this->db->insert('tbl_emp', $array_insert);
										
										if($insert){
											if($emp_id != null){
												$array_emp_new = array(
													'employee_id' => $employee_id,
													'bulan' => $bulan,
													'tahun' => $tahun,
												);
												$emp_new = $this->db->get_where('tbl_emp', $array_emp_new )->row_array();
											
												$sql_are = "
													SELECT tbl_acm_id, tbl_rdm_id
													FROM tbl_are
													WHERE tbl_emp_id = '".$emp_id."'
												";
												$qryare = $this->db->query($sql_are)->result_array();
												if($qryare){
													foreach($qryare as $q => $r){
														$sql_get_acm = "
															SELECT tbl_model_id, activity_code, descript
															FROM tbl_acm
															WHERE id = '".$r['tbl_acm_id']."' AND tbl_model_id = '".$this->modeling['id']."'
														";
														$qry_get_acm = $this->db->query($sql_get_acm)->row_array();
														$array_acm = array(
															'tbl_model_id' => $qry_get_acm['tbl_model_id'],
															'activity_code' => $qry_get_acm['activity_code'],
															'descript' => $qry_get_acm['descript'],
															'bulan' => $bulan,
															'tahun' => $tahun,
														);
														$cek_acm = $this->db->get_where('tbl_acm', $array_acm)->row_array();
														if(!$cek_acm){
															$insert_acm = $this->db->insert('tbl_acm', $array_acm);
															if($insert_acm){
																$array_acm_new = array(
																	'activity_code' => $qry_get_acm['activity_code'],
																	'bulan' => $bulan,
																	'tahun' => $tahun,
																);
																$acm_new = $this->db->get_where('tbl_acm', $array_acm_new )->row_array();
															}else{
																$acm_new = $cek_acm;
															}
														}else{
															$acm_new = $cek_acm;
														}
														
														$array_new_are = array(
															'tbl_acm_id' => $acm_new['id'],
															'tbl_emp_id' => $emp_new['id'],
															'bulan' => $bulan,
															'tahun' => $tahun,
															"create_date" => date('Y-m-d H:i:s'),
															"create_by" => $this->auth['nama_lengkap'],
														);
														$this->db->insert('tbl_are', $array_new_are);														
													
													}
													
												}
												
												$sql_efx = "
													SELECT tbl_exp_id
													FROM tbl_efx
													WHERE tbl_emp_id = '".$emp_id."'
												";
												$qryefx = $this->db->query($sql_efx)->result_array();
												if($qryefx){
													foreach($qryefx as $w => $h){
														
														if($h['tbl_exp_id'] != null || $h['tbl_exp_id'] != 0){
															$sql_get_exp = "
																SELECT *
																FROM tbl_exp
																WHERE id = '".$h['tbl_exp_id']."' AND tbl_model_id = '".$this->modeling['id']."'
															";
															$qry_get_exp = $this->db->query($sql_get_exp)->row_array();
															$array_exp = array(
																'tbl_model_id' => $qry_get_exp['tbl_model_id'],
																'account' => $qry_get_exp['account'],
																'descript' => $qry_get_exp['descript'],
																'bulan' => $bulan,
																'tahun' => $tahun,
															);
															$cek_exp = $this->db->get_where('tbl_exp', $array_exp)->row_array();
															if(!$cek_exp){
																$array_exp_new = array(
																	'tbl_model_id' => $qry_get_exp['tbl_model_id'],
																	'tbl_loc_id' => $qry_get_exp['tbl_loc_id'],
																	'account' => $qry_get_exp['account'],
																	'descript' => $qry_get_exp['descript'],
																	'amount' => $qry_get_exp['amount'],
																	'budget_1' => $qry_get_exp['budget_1'],
																	'budget_2' => $qry_get_exp['budget_2'],
																	'exp_level' => $qry_get_exp['exp_level'],
																	'tbl_rdm_id' => $qry_get_exp['tbl_rdm_id'],
																	'rd_tot_qty' => $qry_get_exp['rd_tot_qty'],
																	'bulan' => $bulan,
																	'tahun' => $tahun,
																);
																$insert_exp = $this->db->insert('tbl_exp', $array_exp_new);
																if($insert_exp){
																	$exp_new = $this->db->get_where('tbl_exp', $array_exp )->row_array();
																}else{
																	$exp_new = $cek_exp;
																}
															}else{
																$exp_new = $cek_exp;
															}
															
															$array_new_efx_exp = array(
																'tbl_exp_id' => $exp_new['id'],
																'tbl_emp_id' => $emp_new['id'],
																'bulan' => $bulan,
																'tahun' => $tahun,
																"create_date" => date('Y-m-d H:i:s'),
																"create_by" => $this->auth['nama_lengkap'],
															);
															$this->db->insert('tbl_efx', $array_new_efx_exp);
															
														}
														
													}
													
													
												}
												
												
											}
										}
										*/
										
									}else{
										$array_update = array(
											"tbl_loc_id"=>(isset($get_loc['id']) ? $get_loc['id'] : 0),
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"employee_id"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"ssn"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"first"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"last"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"mi"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"wages"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"ot_premium"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"benefits"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"total"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"class"=>$worksheet->getCell("K".$i)->getCalculatedValue(),
											"position"=>$worksheet->getCell("L".$i)->getCalculatedValue(),
											"budget_1"=>$worksheet->getCell("M".$i)->getCalculatedValue(),
											"budget_2"=>($worksheet->getCell("N".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("N".$i)->getCalculatedValue() ),
											"head_count"=>$worksheet->getCell("O".$i)->getCalculatedValue(),
											"fte_count"=>$worksheet->getCell("P".$i)->getCalculatedValue(),
											"tbl_rdm_id"=>$rdm_value,
											"rd_tot_qty"=>$rdm_qty,
											"bugettype"=>$worksheet->getCell("Q".$i)->getCalculatedValue(),
											"cost_nbr"=>$worksheet->getCell("R".$i)->getCalculatedValue(),
											"bulan"=>(int)$worksheet->getCell("S".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("T".$i)->getCalculatedValue(),
										);
										//array_push($array_batch_update, $array_update);	
										
										$array_where = array(
											'employee_id'=>$worksheet->getCell("B".$i)->getCalculatedValue(), 
											'tbl_model_id'=>$this->modeling['id'], 
											'bulan'=>(int)$worksheet->getCell("U".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("V".$i)->getCalculatedValue(),
										);
										$this->db->update('tbl_emp', $array_update, $array_where);
										
									}
								
								}
							}									
						break;
						case "tbl_exp":
							for($i=2; $i <= $worksheet->getHighestRow(); $i++){
								if($worksheet->getCell("B".$i)->getCalculatedValue() != "" || $worksheet->getCell("B".$i)->getCalculatedValue() != null){
									$arraynya = array(
										'account'=>$worksheet->getCell("B".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'], 
										'bulan'=>(int)$worksheet->getCell("L".$i)->getCalculatedValue(),
										"tahun"=>(int)$worksheet->getCell("M".$i)->getCalculatedValue(),
									);
									$cek_data = $this->db->get_where('tbl_exp', $arraynya )->row_array();
									$get_loc = $this->db->get_where('tbl_loc', array('costcenter'=>$worksheet->getCell("A".$i)->getCalculatedValue(), 'tbl_model_id'=>$this->modeling['id']) )->row_array();
									
									$arrayrdm = array(
										'account'=>$worksheet->getCell("B".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'], 
										'bulan'=>((int)$worksheet->getCell("L".$i)->getCalculatedValue() - 1),
										"tahun"=>(int)$worksheet->getCell("M".$i)->getCalculatedValue(),
									);
									$rdm = $this->db->get_where('tbl_exp', $arrayrdm )->row_array();
									if(isset($rdm)){
										if(!empty($rdm['tbl_rdm_id']) ){
											$rdm_value = $rdm['tbl_rdm_id'];
											$rdm_qty = $rdm['rd_tot_qty'];
										}else{
											$rdm_value = null;
											$rdm_qty = null;
										}
										if(!empty($rdm['id']) ){
											$exp_id = $rdm['id'];
										}else{
											$exp_id = null;
										}
									}else{
										$sqlrdm = "
											SELECT tbl_rdm_id, rd_tot_qty, id, bulan, tahun
												FROM tbl_exp
											WHERE employee_id = '".$worksheet->getCell("B".$i)->getCalculatedValue()."' AND tbl_model_id = '".$this->modeling['id']."' 
											AND bulan = (
												SELECT max(bulan) as bulannya from tbl_exp where tbl_model_id = '".$this->modeling['id']."' AND tahun = (SELECT MAX(tahun) from tbl_exp WHERE tbl_model_id = '".$this->modeling['id']."')
											) 
											AND tahun = (
												SELECT max(tahun) as tahunnya from tbl_exp where tbl_model_id = '".$this->modeling['id']."'
											)
										";
										$qryrdm = $this->db->query($sqlrdm)->row_array();
										
										if(isset($qryrdm)){
											if(!empty($qryrdm['tbl_rdm_id']) ){
												$rdm_value = $qryrdm['tbl_rdm_id'];
												$rdm_qty = $qryrdm['rd_tot_qty'];
											}else{
												$rdm_value = null;
												$rdm_qty = null;
											}
											
											if(!empty($qryrdm['tbl_rdm_id']) ){
												$exp_id = $qryrdm['id'];
											}else{
												$exp_id = null;
											}
										}else{
											$rdm_value = null;
											$rdm_qty = null;
											$exp_id = null;
										}
									}
									
									if(empty($cek_data)){
										$array_insert = array(
											"tbl_loc_id"=>(isset($get_loc['id']) ? $get_loc['id'] : 0),
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"account"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"descript"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"amount"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"budget_1"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"budget_2"=>($worksheet->getCell("F".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("F".$i)->getCalculatedValue() ),
											"exp_level"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"tbl_rdm_id"=>$rdm_value,
											"rd_tot_qty"=>$rdm_qty,
											"budgettype"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"budgetchg"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"bulan"=>(int)$worksheet->getCell("J".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("K".$i)->getCalculatedValue(),
										);
										array_push($array_batch_insert, $array_insert);	
										
										/*
										$account = (int)$worksheet->getCell("B".$i)->getCalculatedValue();
										$bulan = (int)$worksheet->getCell("L".$i)->getCalculatedValue();
										$tahun = (int)$worksheet->getCell("M".$i)->getCalculatedValue();
										$insert = $this->db->insert('tbl_exp', $array_insert);
										
										if($insert){
											if($exp_id != null){
												$array_exp_new = array(
													'account' => $account,
													'bulan' => $bulan,
													'tahun' => $tahun,
													'tbl_model_id' => $this->modeling['id']
												);
												$exp_new = $this->db->get_where('tbl_exp', $array_exp_new )->row_array();
												
												$sql_are = "
													SELECT tbl_acm_id, tbl_rdm_id
													FROM tbl_are
													WHERE tbl_exp_id = '".$exp_id."'
												";
												$qryare = $this->db->query($sql_are)->result_array();
												if($qryare){
													foreach($qryare as $q => $r){
														$sql_get_acm = "
															SELECT tbl_model_id, activity_code, descript
															FROM tbl_acm
															WHERE id = '".$r['tbl_acm_id']."' AND tbl_model_id = '".$this->modeling['id']."'
														";
														$qry_get_acm = $this->db->query($sql_get_acm)->row_array();
														$array_acm = array(
															'tbl_model_id' => $qry_get_acm['tbl_model_id'],
															'activity_code' => $qry_get_acm['activity_code'],
															'descript' => $qry_get_acm['descript'],
															'bulan' => $bulan,
															'tahun' => $tahun,
														);
														$cek_acm = $this->db->get_where('tbl_acm', $array_acm)->row_array();
														if(!$cek_acm){
															$insert_acm = $this->db->insert('tbl_acm', $array_acm);
															if($insert_acm){
																$array_acm_new = array(
																	'activity_code' => $qry_get_acm['activity_code'],
																	'bulan' => $bulan,
																	'tahun' => $tahun,
																);
																$acm_new = $this->db->get_where('tbl_acm', $array_acm_new )->row_array();
															}else{
																$acm_new = $cek_acm;
															}
														}else{
															$acm_new = $cek_acm;
														}
														
														$array_new_are = array(
															'tbl_acm_id' => $acm_new['id'],
															'tbl_exp_id' => $exp_new['id'],
															'bulan' => $bulan,
															'tahun' => $tahun,
															"create_date" => date('Y-m-d H:i:s'),
															"create_by" => $this->auth['nama_lengkap'],
														);
														$this->db->insert('tbl_are', $array_new_are);	
													
													}
												}
												
												$sql_efx = "
													SELECT tbl_emp_id, tbl_assets_id
													FROM tbl_efx
													WHERE tbl_exp_id = '".$exp_id."'
												";
												$qryefx = $this->db->query($sql_efx)->result_array();
												if($qryefx){
													foreach($qryefx as $w => $h){
														
														if($h['tbl_emp_id'] != null || $h['tbl_emp_id'] != 0){
															$sql_get_emp = "
																SELECT *
																FROM tbl_emp
																WHERE id = '".$h['tbl_emp_id']."' AND tbl_model_id = '".$this->modeling['id']."'
															";
															
															$qry_get_emp = $this->db->query($sql_get_emp)->row_array();
															$array_emp = array(
																'tbl_model_id' => $qry_get_emp['tbl_model_id'],
																'employee_id' => $qry_get_emp['employee_id'],
																'last' => $qry_get_emp['last'],
																'bulan' => $bulan,
																'tahun' => $tahun,
															);
															$cek_emp = $this->db->get_where('tbl_emp', $array_emp)->row_array();
															if(!$cek_emp){
																$array_emp_new = array(
																	'tbl_model_id' => $qry_get_emp['tbl_model_id'],
																	'tbl_loc_id' => $qry_get_emp['tbl_loc_id'],
																	'employee_id' => $qry_get_emp['employee_id'],
																	'last' => $qry_get_emp['last'],
																	'wages' => $qry_get_emp['wages'],
																	'ot_premium' => $qry_get_emp['ot_premium'],
																	'benefits' => $qry_get_emp['benefits'],
																	'total' => $qry_get_emp['total'],
																	'class' => $qry_get_emp['class'],
																	'position' => $qry_get_emp['position'],
																	'budget_1' => $qry_get_emp['budget_1'],
																	'budget_2' => $qry_get_emp['budget_2'],
																	'head_count' => $qry_get_emp['head_count'],
																	'fte_count' => $qry_get_emp['fte_count'],
																	'tbl_rdm_id' => $qry_get_emp['tbl_rdm_id'],
																	'rd_tot_qty' => $qry_get_emp['rd_tot_qty'],
																	'cost_type' => $qry_get_emp['cost_type'],
																	'cost_bucket' => $qry_get_emp['cost_bucket'],
																	'bugettype' => $qry_get_emp['bugettype'],
																	'cost_nbr' => $qry_get_emp['cost_nbr'],
																	'bulan' => $bulan,
																	'tahun' => $tahun,
																);
																$insert_emp = $this->db->insert('tbl_emp', $array_emp_new);
																if($insert_emp){
																	$emp_new = $this->db->get_where('tbl_emp', $array_emp )->row_array();
																}else{
																	$emp_new = $cek_emp;
																}
															}else{
																$emp_new = $cek_emp;
															}
															
															$array_new_efx_emp = array(
																'tbl_exp_id' => $exp_new['id'],
																'tbl_emp_id' => $emp_new['id'],
																'bulan' => $bulan,
																'tahun' => $tahun,
																"create_date" => date('Y-m-d H:i:s'),
																"create_by" => $this->auth['nama_lengkap'],
															);
															$this->db->insert('tbl_efx', $array_new_efx_emp);
														}
														
														if($h['tbl_assets_id'] != null || $h['tbl_assets_id'] != 0){
															$sql_get_assets = "
																SELECT *
																FROM tbl_assets
																WHERE id = '".$h['tbl_assets_id']."' AND tbl_model_id = '".$this->modeling['id']."'
															";
															$qry_get_assets = $this->db->query($sql_get_assets)->row_array();
															$array_assets = array(
																'tbl_model_id' => $qry_get_assets['tbl_model_id'],
																'assets_id' => $qry_get_assets['assets_id'],
																'assets_name' => $qry_get_assets['assets_name'],
																'bulan' => $bulan,
																'tahun' => $tahun,
															);
															$cek_assets = $this->db->get_where('tbl_assets', $array_assets)->row_array();
															if(!$cek_assets){
																$array_assets_new = array(
																	'tbl_model_id' => $qry_get_assets['tbl_model_id'],
																	'tbl_loc_id' => $qry_get_assets['tbl_loc_id'],
																	'assets_id' => $qry_get_assets['assets_id'],
																	'assets_name' => $qry_get_assets['assets_name'],
																	'assets_description' => $qry_get_assets['assets_description'],
																	'cost' => $qry_get_assets['cost'],
																	'amount' => $qry_get_assets['amount'],
																	'budget_1' => $qry_get_assets['budget_1'],
																	'budget_2' => $qry_get_assets['budget_2'],
																	'tbl_rdm_id' => $qry_get_assets['tbl_rdm_id'],
																	'rd_tot_qty' => $qry_get_assets['rd_tot_qty'],
																	'cost_type' => $qry_get_assets['cost_type'],
																	'cost_bucket' => $qry_get_assets['cost_bucket'],
																	'bulan' => $bulan,
																	'tahun' => $tahun,
																);
																$insert_assets = $this->db->insert('tbl_assets', $array_assets_new);
																if($insert_assets){
																	$assets_new = $this->db->get_where('tbl_assets', $array_assets )->row_array();
																}else{
																	$assets_new = $cek_assets;
																}
															}else{
																$assets_new = $cek_assets;
															}
															
															$array_new_efx_assets = array(
																'tbl_exp_id' => $exp_new['id'],
																'tbl_assets_id' => $assets_new['id'],
																'bulan' => $bulan,
																'tahun' => $tahun,
																"create_date" => date('Y-m-d H:i:s'),
																"create_by" => $this->auth['nama_lengkap'],
															);
															$this->db->insert('tbl_efx', $array_new_efx_assets);
														
														}
														
													}
												}
												
												
											}
										}										
										*/
										
										
									}else{
										$array_update = array(
											"tbl_loc_id"=>(isset($get_loc['id']) ? $get_loc['id'] : 0),
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"account"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"descript"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"amount"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"budget_1"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"budget_2"=>($worksheet->getCell("F".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("F".$i)->getCalculatedValue() ),
											"exp_level"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"tbl_rdm_id"=>$rdm_value,
											"rd_tot_qty"=>$rdm_qty,
											"budgettype"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"budgetchg"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"bulan"=>(int)$worksheet->getCell("J".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("K".$i)->getCalculatedValue(),
										);
										
										$array_where = array(
											'account'=>$worksheet->getCell("B".$i)->getCalculatedValue(), 
											'tbl_model_id'=>$this->modeling['id'], 
											'bulan'=>(int)$worksheet->getCell("L".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("M".$i)->getCalculatedValue(),
										);
										$this->db->update('tbl_exp', $array_update, $array_where);
										
									}
								}
							}	
						break;
						case "tbl_assets":
							for($i=2; $i <= $worksheet->getHighestRow(); $i++){
								if($worksheet->getCell("B".$i)->getCalculatedValue() != "" || $worksheet->getCell("B".$i)->getCalculatedValue() != null){								
									$arraynya = array(
										'assets_id'=>$worksheet->getCell("B".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'], 
										'bulan'=>(int)$worksheet->getCell("K".$i)->getCalculatedValue(),
										"tahun"=>(int)$worksheet->getCell("L".$i)->getCalculatedValue(),
									);
									$cek_data = $this->db->get_where('tbl_assets', $arraynya )->row_array();
									$get_loc = $this->db->get_where('tbl_loc', array('costcenter'=>$worksheet->getCell("A".$i)->getCalculatedValue(), 'tbl_model_id'=>$this->modeling['id']) )->row_array();
									
									$arrayrdm = array(
										'assets_id'=>$worksheet->getCell("B".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'], 
										'bulan'=>((int)$worksheet->getCell("K".$i)->getCalculatedValue() - 1),
										"tahun"=>(int)$worksheet->getCell("L".$i)->getCalculatedValue(),
									);
									$rdm = $this->db->get_where('tbl_assets', $arrayrdm )->row_array();
									if(isset($rdm)){
										if(!empty($rdm['tbl_rdm_id']) ){
											$rdm_value = $rdm['tbl_rdm_id'];
											$rdm_qty = $rdm['rd_tot_qty'];
										}else{
											$rdm_value = null;
											$rdm_qty = null;
										}
										
										if(!empty($rdm['id']) ){
											$ass_id = $rdm['id'];
										}else{
											$ass_id = null;
										}
									}else{
										$sqlrdm = "
											SELECT tbl_rdm_id, rd_tot_qty, id, bulan, tahun
												FROM tbl_assets
											WHERE assets_id = '".$worksheet->getCell("B".$i)->getCalculatedValue()."' AND tbl_model_id = '".$this->modeling['id']."' 
											AND bulan = (
												SELECT max(bulan) as bulannya from tbl_assets where tbl_model_id = '".$this->modeling['id']."' AND tahun = (SELECT MAX(tahun) from tbl_assets WHERE tbl_model_id = '".$this->modeling['id']."')
											) 
											AND tahun = (
												SELECT max(tahun) as tahunnya from tbl_assets where tbl_model_id = '".$this->modeling['id']."'
											)
										";
										$qryrdm = $this->db->query($sqlrdm)->row_array();
										
										if(isset($qryrdm)){
											if(!empty($qryrdm['tbl_rdm_id']) ){
												$rdm_value = $qryrdm['tbl_rdm_id'];
												$rdm_qty = $qryrdm['rd_tot_qty'];
											}else{
												$rdm_value = null;
												$rdm_qty = null;
											}
											
											if(!empty($qryrdm['tbl_rdm_id']) ){
												$ass_id = $qryrdm['id'];
											}else{
												$ass_id = null;
											}
										}else{
											$rdm_value = null;
											$ass_id = null;
										}
									}
									
									if(empty($cek_data)){
										$array_insert = array(
											"tbl_loc_id"=>(isset($get_loc['id']) ? $get_loc['id'] : 0),
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"assets_id"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"assets_name"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"assets_description"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"cost"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"amount"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"budget_1"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"budget_2"=>($worksheet->getCell("H".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("H".$i)->getCalculatedValue() ),
											"cost_type"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"cost_bucket"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"tbl_rdm_id"=>$rdm_value,
											"rd_tot_qty"=>$rdm_qty,
											"bulan"=>(int)$worksheet->getCell("K".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("L".$i)->getCalculatedValue(),
											"create_by"=>$this->auth['nama_lengkap'],
											"create_date"=>date('Y-m-d H:i:s'),
										);
										array_push($array_batch_insert, $array_insert);	
										
										/*
										$assets_id = (int)$worksheet->getCell("B".$i)->getCalculatedValue();
										$bulan = (int)$worksheet->getCell("K".$i)->getCalculatedValue();
										$tahun = (int)$worksheet->getCell("L".$i)->getCalculatedValue();
										$insert = $this->db->insert('tbl_assets', $array_insert);
										
										if($insert){
											if($ass_id != null){
												$array_ass_new = array(
													'assets_id' => $assets_id,
													'bulan' => $bulan,
													'tahun' => $tahun,
													'tbl_model_id' => $this->modeling['id']
												);
												$ass_new = $this->db->get_where('tbl_assets', $array_ass_new )->row_array();
												
												$sql_are = "
													SELECT tbl_acm_id, tbl_rdm_id
													FROM tbl_are
													WHERE tbl_assets_id = '".$ass_id."'
												";
												$qryare = $this->db->query($sql_are)->result_array();
												if($qryare){
													foreach($qryare as $q => $r){
														$sql_get_acm = "
															SELECT tbl_model_id, activity_code, descript
															FROM tbl_acm
															WHERE id = '".$r['tbl_acm_id']."' AND tbl_model_id = '".$this->modeling['id']."'
														";
														$qry_get_acm = $this->db->query($sql_get_acm)->row_array();
														$array_acm = array(
															'tbl_model_id' => $qry_get_acm['tbl_model_id'],
															'activity_code' => $qry_get_acm['activity_code'],
															'descript' => $qry_get_acm['descript'],
															'bulan' => $bulan,
															'tahun' => $tahun,
														);
														$cek_acm = $this->db->get_where('tbl_acm', $array_acm)->row_array();
														if(!$cek_acm){
															$insert_acm = $this->db->insert('tbl_acm', $array_acm);
															if($insert_acm){
																$array_acm_new = array(
																	'activity_code' => $qry_get_acm['activity_code'],
																	'bulan' => $bulan,
																	'tahun' => $tahun,
																);
																$acm_new = $this->db->get_where('tbl_acm', $array_acm_new )->row_array();
															}else{
																$acm_new = $cek_acm;
															}
														}else{
															$acm_new = $cek_acm;
														}
														
														$array_new_are = array(
															'tbl_acm_id' => $acm_new['id'],
															'tbl_assets_id' => $ass_new['id'],
															'bulan' => $bulan,
															'tahun' => $tahun,
															"create_date" => date('Y-m-d H:i:s'),
															"create_by" => $this->auth['nama_lengkap'],
														);
														$this->db->insert('tbl_are', $array_new_are);	
													
													}
												}
												
												$sql_efx = "
													SELECT tbl_exp_id
													FROM tbl_efx
													WHERE tbl_assets_id = '".$ass_id."'
												";
												$qryefx = $this->db->query($sql_efx)->result_array();
												if($qryefx){
													foreach($qryefx as $w => $h){
														
														if($h['tbl_exp_id'] != null || $h['tbl_exp_id'] != 0){
															$sql_get_exp = "
																SELECT *
																FROM tbl_exp
																WHERE id = '".$h['tbl_exp_id']."' AND tbl_model_id = '".$this->modeling['id']."'
															";
															
															$qry_get_exp = $this->db->query($sql_get_exp)->row_array();
															$array_exp = array(
																'tbl_model_id' => $qry_get_exp['tbl_model_id'],
																'account' => $qry_get_exp['account'],
																'descript' => $qry_get_exp['descript'],
																'bulan' => $bulan,
																'tahun' => $tahun,
															);
															$cek_exp = $this->db->get_where('tbl_exp', $array_exp)->row_array();
															if(!$cek_exp){
																$array_exp_new = array(
																	'tbl_model_id' => $qry_get_exp['tbl_model_id'],
																	'tbl_loc_id' => $qry_get_exp['tbl_loc_id'],
																	'account' => $qry_get_exp['account'],
																	'descript' => $qry_get_exp['descript'],
																	'amount' => $qry_get_exp['amount'],
																	'budget_1' => $qry_get_exp['budget_1'],
																	'budget_2' => $qry_get_exp['budget_2'],
																	'exp_level' => $qry_get_exp['exp_level'],
																	'tbl_rdm_id' => $qry_get_exp['tbl_rdm_id'],
																	'rd_tot_qty' => $qry_get_exp['rd_tot_qty'],
																	'bulan' => $bulan,
																	'tahun' => $tahun,
																);
																$insert_exp = $this->db->insert('tbl_exp', $array_exp_new);
																if($insert_exp){
																	$exp_new = $this->db->get_where('tbl_exp', $array_exp )->row_array();
																}else{
																	$exp_new = $cek_exp;
																}
															}else{
																$exp_new = $cek_exp;
															}
															
															$array_new_efx_exp = array(
																'tbl_assets_id' => $ass_new['id'],
																'tbl_exp_id' => $exp_new['id'],
																'bulan' => $bulan,
																'tahun' => $tahun,
																"create_date" => date('Y-m-d H:i:s'),
																"create_by" => $this->auth['nama_lengkap'],
															);
															$this->db->insert('tbl_efx', $array_new_efx_exp);
														}
													
													}
												}
												
												
											}
										}
										*/
										
									}else{
										$array_update = array(
											"tbl_loc_id"=>(isset($get_loc['id']) ? $get_loc['id'] : 0),
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"assets_id"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"assets_name"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"assets_description"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"cost"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"amount"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"budget_1"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"budget_2"=>($worksheet->getCell("H".$i)->getCalculatedValue()=='' ? 0 : $worksheet->getCell("H".$i)->getCalculatedValue() ),
											"cost_type"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"cost_bucket"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"tbl_rdm_id"=>$rdm_value,
											"rd_tot_qty"=>$rdm_qty,
											"bulan"=>(int)$worksheet->getCell("K".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("L".$i)->getCalculatedValue(),
											"create_by"=>$this->auth['nama_lengkap'],
											"create_date"=>date('Y-m-d H:i:s'),
										);
										
										$array_where = array(
											'assets_id'=>$worksheet->getCell("B".$i)->getCalculatedValue(), 
											'tbl_model_id'=>$this->modeling['id'], 
											'bulan'=>(int)$worksheet->getCell("L".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("M".$i)->getCalculatedValue(),
										);
										$this->db->update('tbl_assets', $array_update, $array_where);
										
									}
								}
							}
						break;
						case "tbl_loc":
							$fieldnya = 'costcenter';
							for($i=2; $i <= $worksheet->getHighestRow(); $i++){
								if($worksheet->getCell("B".$i)->getCalculatedValue() != "" || $worksheet->getCell("B".$i)->getCalculatedValue() != null){								
								
									$arraynya = array(
										'costcenter'=>$worksheet->getCell("B".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'],
										"bulan"=>(int)$worksheet->getCell("D".$i)->getCalculatedValue(),
										"tahun"=>(int)$worksheet->getCell("E".$i)->getCalculatedValue(),
									);
									$cek_data = $this->db->get_where('tbl_loc', $arraynya)->row_array();					
									if(empty($cek_data)){
										$array_insert = array(
											"location"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"costcenter"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"loc_name"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"bulan"=>(int)$worksheet->getCell("D".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("E".$i)->getCalculatedValue(),
										);
										array_push($array_batch_insert, $array_insert);
									}else{
										$array_update = array(
											"location"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"costcenter"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"loc_name"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"bulan"=>(int)$worksheet->getCell("D".$i)->getCalculatedValue(),
											"tahun"=>(int)$worksheet->getCell("E".$i)->getCalculatedValue(),

										);
										//array_push($array_batch_update, $array_update);
										
										$this->db->update('tbl_loc', $array_update, $arraynya);
									}
								
								}
							}
							/*
							if($array_batch_update){
								$this->db->update_batch($type_import, $array_batch_update, $fieldnya);
							}
							*/							
						break;						
						case "tbl_rdm":
							for($i=2; $i <= $worksheet->getHighestRow(); $i++){
								if($worksheet->getCell("A".$i)->getCalculatedValue() != "" || $worksheet->getCell("A".$i)->getCalculatedValue() != null){																
									$arraynya = array(
										'resource'=>$worksheet->getCell("A".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'],
										"bulan"=>(int)$worksheet->getCell("R".$i)->getCalculatedValue(),
										"tahun"=>(int)$worksheet->getCell("S".$i)->getCalculatedValue(),
									);
									$cek_data = $this->db->get_where('tbl_rdm', $arraynya)->row_array();					
									if(empty($cek_data)){
										$array_insert = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"resource"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"descript"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"rdm_qty"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"budtypeupe"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"costnbrupe"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"coeffupe"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"budtypeupx"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"costnbrupx"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"coeffupx"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"bydtypeupa"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"costnbrupa"=>$worksheet->getCell("K".$i)->getCalculatedValue(),
											"coeffupa"=>$worksheet->getCell("L".$i)->getCalculatedValue(),
											"actorpro"=>$worksheet->getCell("M".$i)->getCalculatedValue(),
											"batch"=>$worksheet->getCell("N".$i)->getCalculatedValue(),
											"note"=>$worksheet->getCell("O".$i)->getCalculatedValue(),
											"constant"=>$worksheet->getCell("P".$i)->getCalculatedValue(),
											"coefficient"=>$worksheet->getCell("Q".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("R".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("S".$i)->getCalculatedValue(),
										);
										array_push($array_batch_insert, $array_insert);	
									}else{
										$array_update = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"resource"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"descript"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"rdm_qty"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"budtypeupe"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"costnbrupe"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"coeffupe"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"budtypeupx"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"costnbrupx"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"coeffupx"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"bydtypeupa"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"costnbrupa"=>$worksheet->getCell("K".$i)->getCalculatedValue(),
											"coeffupa"=>$worksheet->getCell("L".$i)->getCalculatedValue(),
											"actorpro"=>$worksheet->getCell("M".$i)->getCalculatedValue(),
											"batch"=>$worksheet->getCell("N".$i)->getCalculatedValue(),
											"note"=>$worksheet->getCell("O".$i)->getCalculatedValue(),
											"constant"=>$worksheet->getCell("P".$i)->getCalculatedValue(),
											"coefficient"=>$worksheet->getCell("Q".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("R".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("S".$i)->getCalculatedValue(),
										);
										$this->db->update('tbl_rdm', $array_update, $arraynya);
									}	
								}	
							}	
						break;
						case "tbl_cdm":
							for($i=2; $i <= $worksheet->getHighestRow(); $i++){
								if($worksheet->getCell("A".$i)->getCalculatedValue() != "" || $worksheet->getCell("A".$i)->getCalculatedValue() != null){																
									$arraynya = array(
										'cost_driver'=>$worksheet->getCell("A".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'],
										"bulan"=>(int)$worksheet->getCell("R".$i)->getCalculatedValue(),
										"tahun"=>(int)$worksheet->getCell("S".$i)->getCalculatedValue(),
									);
									$cek_data = $this->db->get_where('tbl_cdm', $arraynya)->row_array();					
									if(empty($cek_data)){
										$array_insert = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"cost_driver"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"descript"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"roundit"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"sudn_cd"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"mudn_cd"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"mudn_uom"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"sweight"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"mweight"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"budgettype"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"coefficient"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"constant"=>$worksheet->getCell("K".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("L".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("M".$i)->getCalculatedValue(),
										);
										array_push($array_batch_insert, $array_insert);	
									}else{
										$array_update = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"cost_driver"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"descript"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"roundit"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"sudn_cd"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"mudn_cd"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"mudn_uom"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"sweight"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"mweight"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"budgettype"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"coefficient"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"constant"=>$worksheet->getCell("K".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("L".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("M".$i)->getCalculatedValue(),
										);
										$this->db->update('tbl_cdm', $array_update, $arraynya);
									}
								}	
							}	
						break;
						case "tbl_prm":
							for($i=2; $i <= $worksheet->getHighestRow(); $i++){
								if($worksheet->getCell("A".$i)->getCalculatedValue() != "" || $worksheet->getCell("A".$i)->getCalculatedValue() != null){																
									$arraynya = array(
										'prod_id'=>$worksheet->getCell("A".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'],
										"bulan"=>(int)$worksheet->getCell("L".$i)->getCalculatedValue(),
										"tahun"=>(int)$worksheet->getCell("M".$i)->getCalculatedValue(),
									);
									$cek_data = $this->db->get_where('tbl_prm', $arraynya)->row_array();
									$net_revenue = ( (int)$worksheet->getCell("D".$i)->getCalculatedValue() - (int)$worksheet->getCell("E".$i)->getCalculatedValue() );
									if(empty($cek_data)){
										$array_insert = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"prod_id"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"descript"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"level"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"revenue"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"reduction"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"net_revenue"=>$net_revenue,
											"direct_cost"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"segment_id"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"service_group_id"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"prod_qty"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"uom"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"target_qty"=>$worksheet->getCell("K".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("L".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("M".$i)->getCalculatedValue(),
											"rlu_date"=>date('d-m-Y'),
											"create_by"=>$this->auth['nama_lengkap'],
										);
										array_push($array_batch_insert, $array_insert);	
									}else{
										$array_update = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"prod_id"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"descript"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"level"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"revenue"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"reduction"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
											"net_revenue"=>$net_revenue,
											"direct_cost"=>$worksheet->getCell("F".$i)->getCalculatedValue(),
											"segment_id"=>$worksheet->getCell("G".$i)->getCalculatedValue(),
											"service_group_id"=>$worksheet->getCell("H".$i)->getCalculatedValue(),
											"prod_qty"=>$worksheet->getCell("I".$i)->getCalculatedValue(),
											"uom"=>$worksheet->getCell("J".$i)->getCalculatedValue(),
											"target_qty"=>$worksheet->getCell("K".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("L".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("M".$i)->getCalculatedValue(),
											"rlu_date"=>date('d-m-Y'),
											"create_by"=>$this->auth['nama_lengkap'],
										);
										$this->db->update('tbl_prm', $array_update, $arraynya);
									}
								}
							}	
						break;
						case "tbl_cust":
							for($i=2; $i <= $worksheet->getHighestRow(); $i++){
								if($worksheet->getCell("A".$i)->getCalculatedValue() != "" || $worksheet->getCell("A".$i)->getCalculatedValue() != null){																
									$arraynya = array(
										'customer_id'=>$worksheet->getCell("A".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'],
										"bulan"=>(int)$worksheet->getCell("D".$i)->getCalculatedValue(),
										"tahun"=>(int)$worksheet->getCell("E".$i)->getCalculatedValue(),
									);
									$cek_data = $this->db->get_where('tbl_cust', $arraynya)->row_array();
									if(empty($cek_data)){
										$array_insert = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"customer_id"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"customer_name"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"revenue"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
										);
										array_push($array_batch_insert, $array_insert);
									}else{
										$array_update = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"customer_id"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"customer_name"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"revenue"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
										);
										$this->db->update('tbl_cust', $array_update, $arraynya);
									}
								}								
							}	
						break;
						case "tbl_location":
							for($i=2; $i <= $worksheet->getHighestRow(); $i++){
								if($worksheet->getCell("A".$i)->getCalculatedValue() != "" || $worksheet->getCell("A".$i)->getCalculatedValue() != null){																
									$arraynya = array(
										'location_id'=>$worksheet->getCell("A".$i)->getCalculatedValue(), 
										'tbl_model_id'=>$this->modeling['id'],
										"bulan"=>(int)$worksheet->getCell("D".$i)->getCalculatedValue(),
										"tahun"=>(int)$worksheet->getCell("E".$i)->getCalculatedValue(),
									);
									$cek_data = $this->db->get_where('tbl_location', $arraynya)->row_array();
									if(empty($cek_data)){
										$array_insert = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"location_id"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"location_name"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"revenue"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
										);
										array_push($array_batch_insert, $array_insert);	
									}else{
										$array_update = array(
											"tbl_model_id"=> (isset($this->modeling['id']) ? $this->modeling['id'] : 0),
											"location_id"=>$worksheet->getCell("A".$i)->getCalculatedValue(),
											"location_name"=>$worksheet->getCell("B".$i)->getCalculatedValue(),
											"revenue"=>$worksheet->getCell("C".$i)->getCalculatedValue(),
											"bulan"=>$worksheet->getCell("D".$i)->getCalculatedValue(),
											"tahun"=>$worksheet->getCell("E".$i)->getCalculatedValue(),
										);
										$this->db->update('tbl_location', $array_update, $arraynya);
									}
								}
							}	
						break;
					}
					
					if($array_batch_insert){
						$this->db->insert_batch($type_import, $array_batch_insert);
					}					
				}
			break;
			
			// Resource
			// Tab employee
			case "list_activity_employee":
				$table = "tbl_are";
				$count = count($data['datanya'])-1;
				//echo $count;exit;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_emp_id" => $data['tbl_emp_id'],
						"tbl_acm_id" => $data['datanya'][$i]['id'],
						"tbl_rdm_id" => $data['datanya'][$i]['tbl_rdm_id'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}					
			break;
			case "tbl_are_assignment": // tabs employee = toActivity , tabs expense = toActivity, tabs assets = toActivity
				$table = "tbl_are";
				$tot_qty = $data['rd_tot_qty'];
				$gaji = $data['gaji'];
				$bulan = $data['bulan'];
				$tahun = $data['tahun'];
				$tot_cost = true;
				
				if($data['tbl_emp_id'] == 0){
					unset($data['tbl_emp_id']);
				}
				if($data['tbl_exp_id'] == 0){
					unset($data['tbl_exp_id']);
				}
				if($data['tbl_assets_id'] == 0){
					unset($data['tbl_assets_id']);
				}
				
				unset($data['activity_name']);
				unset($data['resource_name']);
				unset($data['rd_tot_qty']);
				unset($data['gaji']);
				unset($data['bulan']);
				unset($data['tahun']);
				unset($data['editing']);
				unset($data['tbl_loc_id']);
				unset($data['tbl_acm_child_id']);
				unset($data['tbl_model_id']);
				unset($data['cost_nbr']);
				unset($data['total_cost']);
				
				if($tot_qty == null){
					$amount = ( $data['percent'] / 100 ) * $gaji;
					$data['rd_qty'] = null;
					$data['tbl_rdm_id'] = null;
				}elseif($tot_qty == 0){
					$amount = ( $data['percent'] / 100 ) * $gaji;
					$data['rd_qty'] = null;
					$data['tbl_rdm_id'] = null;
				}else{
					$amount = ($data['rd_qty'] * $gaji) / $tot_qty;
					$percent = ($amount/$gaji) * 100;
					$data['percent'] = number_format($percent,2);
				}				
				$data['cost'] = $amount;
				//*/
			break;
			case "list_expense_employee":
				$table = "tbl_efx";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_emp_id" => $data['tbl_emp_id'],
						"tbl_exp_id" => $data['datanya'][$i]['id'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}					
			break;
			case "tbl_efx": // tabs employee -> expense Source, Tabs expense -> To Employee
				if($sts_crud != 'delete'){
					$tot_qty = $data['rd_tot_qty'];
					$amounts = $data['amount'];
					$bulan = $data['bulan'];
					$tahun = $data['tahun'];
					
					if($data['tbl_emp_id'] == 0){
						unset($data['tbl_emp_id']);
					}
					if($data['tbl_exp_id'] == 0){
						unset($data['tbl_exp_id']);
					}
					
					unset($data['expense_name']);
					unset($data['employee_name']);
					unset($data['resource_name']);
					unset($data['editing']);
					unset($data['tbl_rdm_id']);
					unset($data['rd_tot_qty']);
					unset($data['amount']);
					unset($data['bulan']);
					unset($data['tahun']);
					unset($data['tbl_assets_id']);
					
					if($tot_qty == null){
						$amount = ( $data['percent'] / 100 ) * $amounts;
						$data['rd_qty'] = null;
						//$data['tbl_rdm_id'] = null;
					}elseif($tot_qty == 0){
						$amount = ( $data['percent'] / 100 ) * $amounts;
						$data['rd_qty'] = null;
						//$data['tbl_rdm_id'] = null;
					}else{
						/*
						if($data['rd_qty'] >= $tot_qty){
							exit;
						}
						*/
						
						$amount = ($data['rd_qty'] * $amounts) / $tot_qty;
						$percent = ($amount/$amounts) * 100;
						$data['percent'] = number_format($percent,2);
					}					
					$data['cost'] = $amount;
				}
			break;
			
			//tabs expense
			case "list_activity_expense":
				$table = "tbl_are";
				$count = count($data['datanya'])-1;
				//echo $count;exit;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_exp_id" => $data['tbl_exp_id'],
						"tbl_acm_id" => $data['datanya'][$i]['id'],
						"tbl_rdm_id" => $data['datanya'][$i]['tbl_rdm_id'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;
			
			case "list_employee_expense":
				$table = "tbl_efx";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_exp_id" => $data['tbl_exp_id'],
						"tbl_emp_id" => $data['datanya'][$i]['id'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}			
			break;
				
			
			case "tbl_efx_assets": // Tabs expense => to Asset, Tabs Assets => Expense Source
			case "tbl_efx_to_assets":
				$table = "tbl_efx";
				$tot_qty = $data['rd_tot_qty'];
				$amounts = $data['amount'];
				$bulan = $data['bulan'];
				$tahun = $data['tahun'];
				
				unset($data['assets_name']);
				unset($data['expense_name']);
				unset($data['resource_name']);
				unset($data['editing']);
				unset($data['tbl_rdm_id']);
				unset($data['rd_tot_qty']);
				unset($data['amount']);
				unset($data['bulan']);
				unset($data['tahun']);
				unset($data['tbl_emp_id']);
				
				if($tot_qty == null){
					$amount = ( $data['percent'] / 100 ) * $amounts;
					$data['rd_qty'] = 0;
				}elseif($tot_qty == 0){
					$amount = ( $data['percent'] / 100 ) * $amounts;
					$data['rd_qty'] = 0;
				}else{
					/*
					if($data['rd_qty'] >= $tot_qty){
						exit;
					}
					*/
					$amount = ($amounts * $data['rd_qty']) / $tot_qty;
					$percent = ($amount/$amounts) * 100;
					$data['percent'] = number_format($percent,2);
				}
				
				$data['cost'] = $amount;
				
			break;
			case "list_assets_expense":
				$table = "tbl_efx";
				$count = count($data['datanya'])-1;
				//echo $count;exit;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_exp_id" => $data['tbl_exp_id'],
						"tbl_assets_id" => $data['datanya'][$i]['id'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;
			
			
			//tabs assets
			case "list_activity_assets":
				$table = "tbl_are";
				$count = count($data['datanya'])-1;
				//echo $count;exit;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_assets_id" => $data['tbl_assets_id'],
						"tbl_acm_id" => $data['datanya'][$i]['id'],
						"tbl_rdm_id" => $data['datanya'][$i]['tbl_rdm_id'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;
			
			case "list_expense_assets":
				$table = "tbl_efx";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_assets_id" => $data['tbl_assets_id'],
						"tbl_exp_id" => $data['datanya'][$i]['id'],
						"create_date" => date('Y-m-d H:i:s'),
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
						"create_by" => $this->auth['nama_lengkap'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;
			//End Assignment
			
			// Modul Cost Object
			case "list_activity_costobject":
				$table = "tbl_prd";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_prm_id" => $data['tbl_prm_id'],
						"tbl_acm_id" => $data['datanya'][$i]['id'],
						"tbl_cdm_id" => $data['datanya'][$i]['tbl_cdm_id'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;
			
			case "tbl_prd_costobject":
				$table = "tbl_prd";
				$cost_rate = $data['cost_rate2'];
				$data['cost_rate'] = $cost_rate;
				
				unset($data['cost_rate2']);
				unset($data['activity_name']);
				unset($data['cost_driver_name']);
				unset($data['editing']);
				
				$cost = ($data['cost_rate'] * $data['quantity']);
				$data['cost'] = $cost;
			break;
			
			case "list_customer_costobject":
				$table = "tbl_ptp";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_prm_id" => $data['tbl_prm_id'],
						"tbl_cust_id" => $data['datanya'][$i]['id'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;
			
			case "tbl_cust_costobject":
			case "tbl_loc_costobject":
				$table = "tbl_ptp";
				$cost_rate = $data['cost_rate'];
				
				if($data['tbl_cust_id'] == 0){
					unset($data['tbl_cust_id']);
				}
				if($data['tbl_location_id'] == 0){
					unset($data['tbl_location_id']);
				}
				
				unset($data['customer_name']);
				unset($data['location_name']);
				unset($data['cost_rate']);
				unset($data['profit']);
				unset($data['editing']);
				
				$cost = ($cost_rate * $data['quantity']);
				$data['cost'] = $cost;
			break;
			
			case "list_location_costobject":
				$table = "tbl_ptp";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_prm_id" => $data['tbl_prm_id'],
						"tbl_location_id" => $data['datanya'][$i]['id'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;

			case "list_costobject_customer":
				$table = "tbl_ptp";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_cust_id" => $data['tbl_cust_id'],
						"tbl_prm_id" => $data['datanya'][$i]['id'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;
			case "tbl_customer_costobject":
				$table = "tbl_ptp";
				unset($data['cost_object_name']);
				unset($data['location_name']);
				unset($data['editing']);
				
				$cost = ($data['sell_price'] * $data['quantity']);
				$data['cost'] = $cost;
			break;
			
			case "list_location_customer":
				$table = "tbl_ptp";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_cust_id" => $data['tbl_cust_id'],
						"tbl_location_id" => $data['datanya'][$i]['id'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;
			case "tbl_customer_location":
				$table = "tbl_ptp";
				unset($data['cost_object_name']);
				unset($data['location_name']);
				unset($data['editing']);
				unset($data['tbl_prm_id']);
				
				$cost = ($data['sell_price'] * $data['quantity']);
				$data['cost'] = $cost;
			break;
			
			case "list_costobject_location":
				$table = "tbl_ptp";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_location_id" => $data['tbl_location_id'],
						"tbl_prm_id" => $data['datanya'][$i]['id'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}				
			break;
			case "tbl_location_costobject":
				$table = "tbl_ptp";
				unset($data['cost_object_name']);
				unset($data['customer_name']);
				unset($data['editing']);
				
				$cost = ($data['sell_price'] * $data['quantity']);
				$data['cost'] = $cost;
			break;

			case "list_customer_location":
				$table = "tbl_ptp";
				$count = count($data['datanya'])-1;
				
				$array_batch_insert = array();
				for($i = 0; $i <= $count; $i++){
					$array_insert = array(
						"tbl_location_id" => $data['tbl_location_id'],
						"tbl_cust_id" => $data['datanya'][$i]['id'],
						"create_date" => date('Y-m-d H:i:s'),
						"create_by" => $this->auth['nama_lengkap'],
						"bulan" => $data['bulan'],
						"tahun" => $data['tahun'],
					);	
					array_push($array_batch_insert, $array_insert);						
				}
				
				if($array_batch_insert){
					$this->db->insert_batch($table, $array_batch_insert);
				}		
			break;
			case "tbl_location_customer":
				$table = "tbl_ptp";
				unset($data['cost_object_name']);
				unset($data['customer_name']);
				unset($data['editing']);
				unset($data['tbl_prm_id']);
				
				$cost = ($data['sell_price'] * $data['quantity']);
				$data['cost'] = $cost;
			break;
			// End Modul Cost Object
			
			//Duplicate Costing
			case "duplicate_costing":
				$costing = $data['costing'];
				$array = array(
					'tbl_model_id' => $this->modeling['id'],
					'bulan' => $data['bulan'],
					'tahun' => $data['tahun'],
				);
				if(count($costing)>0){
					foreach($costing as $v){
						switch($v){
							case "act_emp":
								$get_emp = $this->db->get_where('tbl_emp', $array)->result_array();
								foreach($get_emp as $k => $h){
									$sqlact = "
										SELECT A.*, B.activity_code, B.descript
										FROM tbl_are A
										LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id
										WHERE A.tbl_emp_id = '".$h['id']."'
									";
									$qryact = $this->db->query($sqlact)->result_array();
									if($qryact){
										foreach($qryact as $t => $u){
											$array_det_act = array(
												'tbl_model_id' => $this->modeling['id'],
												'activity_code' => $u['activity_code'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_act = $this->db->get_where('tbl_acm', $array_det_act)->row_array();
											if($data_det_act){
												$array_emp_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'employee_id' => $h['employee_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_emp_skg = $this->db->get_where('tbl_emp', $array_emp_bulan_skg)->row_array();
												if($data_emp_skg){
													$array_insert_are = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_acm_id' => $data_det_act['id'],
														'tbl_emp_id' => $data_emp_skg['id'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'rd_qty' => $u['rd_qty'],
														'tbl_rdm_id' => $u['tbl_rdm_id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_are = array(
														'tbl_acm_id' => $data_det_act['id'],
														'tbl_emp_id' => $data_emp_skg['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													
													$cek_are = $this->db->get_where('tbl_are', $array_cek_are)->row_array();
													if(!$cek_are){
														$this->db->insert('tbl_are', $array_insert_are);
													}
												}
											}
										}
									}
								}
							break;
							case "exp_emp":
								$get_emp = $this->db->get_where('tbl_emp', $array)->result_array();
								foreach($get_emp as $k => $h){
									$sqlefx = "
										SELECT A.*, B.account, B.descript
										FROM tbl_efx A
										LEFT JOIN tbl_exp B ON B.id = A.tbl_exp_id
										WHERE A.tbl_emp_id = '".$h['id']."'
									";
									$qryefx = $this->db->query($sqlefx)->result_array();
									if($qryefx){
										foreach($qryefx as $t => $u){
											$array_det_exp = array(
												'tbl_model_id' => $this->modeling['id'],
												'account' => $u['account'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_exp = $this->db->get_where('tbl_exp', $array_det_exp)->row_array();
											if($data_det_exp){
												$array_emp_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'employee_id' => $h['employee_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_emp_skg = $this->db->get_where('tbl_emp', $array_emp_bulan_skg)->row_array();
												if($data_emp_skg){
													$array_insert_efx = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_exp_id' => $data_det_exp['id'],
														'tbl_emp_id' => $data_emp_skg['id'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'rd_qty' => $u['rd_qty'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_efx = array(
														'tbl_exp_id' => $data_det_exp['id'],
														'tbl_emp_id' => $data_emp_skg['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_efx = $this->db->get_where('tbl_efx', $array_cek_efx)->row_array();
													if(!$cek_efx){
														$this->db->insert('tbl_efx', $array_insert_efx);
													}
												}
												
											}
										}
									}
								}
							break;
							
							case "act_exp":
								$get_exp = $this->db->get_where('tbl_exp', $array)->result_array();
								foreach($get_exp as $k => $h){
									$sqlact = "
										SELECT A.*, B.activity_code, B.descript
										FROM tbl_are A
										LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id
										WHERE A.tbl_exp_id = '".$h['id']."'
									";
									$qryact = $this->db->query($sqlact)->result_array();
									if($qryact){
										foreach($qryact as $t => $u){
											$array_det_act = array(
												'tbl_model_id' => $this->modeling['id'],
												'activity_code' => $u['activity_code'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_act = $this->db->get_where('tbl_acm', $array_det_act)->row_array();
											if($data_det_act){
												$array_exp_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'account' => $h['account'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_exp_skg = $this->db->get_where('tbl_exp', $array_exp_bulan_skg)->row_array();
												if($data_exp_skg){
													$array_insert_are = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_acm_id' => $data_det_act['id'],
														'tbl_exp_id' => $data_exp_skg['id'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'rd_qty' => $u['rd_qty'],
														'tbl_rdm_id' => $u['tbl_rdm_id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_are = array(
														'tbl_acm_id' => $data_det_act['id'],
														'tbl_exp_id' => $data_exp_skg['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_are = $this->db->get_where('tbl_are', $array_cek_are)->row_array();
													if(!$cek_are){
														$this->db->insert('tbl_are', $array_insert_are);
													}
												}
											}
										}
									}
								}
							break;
							case "emp_exp":
								$get_exp = $this->db->get_where('tbl_exp', $array)->result_array();
								foreach($get_exp as $k => $h){
									$sqlefx = "
										SELECT A.*, B.employee_id, B.last
										FROM tbl_efx A
										LEFT JOIN tbl_emp B ON B.id = A.tbl_emp_id
										WHERE A.tbl_exp_id = '".$h['id']."' 
										AND A.tbl_emp_id IS NOT NULL  
									";
									$qryefx = $this->db->query($sqlefx)->result_array();
									if($qryefx){
										foreach($qryefx as $t => $u){
											$array_det_emp = array(
												'tbl_model_id' => $this->modeling['id'],
												'employee_id' => $u['employee_id'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_emp = $this->db->get_where('tbl_emp', $array_det_emp)->row_array();
											if($data_det_emp){
												$array_exp_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'account' => $h['account'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_exp_skg = $this->db->get_where('tbl_exp', $array_exp_bulan_skg)->row_array();
												if($data_exp_skg){
													$array_insert_efx = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_emp_id' => $data_det_emp['id'],
														'tbl_exp_id' => $data_exp_skg['id'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'rd_qty' => $u['rd_qty'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_efx = array(
														'tbl_emp_id' => $data_det_emp['id'],
														'tbl_exp_id' => $data_exp_skg['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_efx = $this->db->get_where('tbl_efx', $array_cek_efx)->row_array();
													if(!$cek_efx){
														$this->db->insert('tbl_efx', $array_insert_efx);
													}
												}
												
											}
										}
									}
								}
							break;
							case "ass_exp":
								$get_exp = $this->db->get_where('tbl_exp', $array)->result_array();
								foreach($get_exp as $k => $h){
									$sqlefx = "
										SELECT A.*, B.assets_id, B.assets_name
										FROM tbl_efx A
										LEFT JOIN tbl_assets B ON B.id = A.tbl_assets_id
										WHERE A.tbl_exp_id = '".$h['id']."' 
										AND A.tbl_assets_id IS NOT NULL
									";
									$qryefx = $this->db->query($sqlefx)->result_array();
									if($qryefx){
										foreach($qryefx as $t => $u){
											$array_det_ass = array(
												'tbl_model_id' => $this->modeling['id'],
												'assets_id' => $u['assets_id'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_ass = $this->db->get_where('tbl_assets', $array_det_ass)->row_array();
											if($data_det_ass){
												$array_exp_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'account' => $h['account'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_exp_skg = $this->db->get_where('tbl_exp', $array_exp_bulan_skg)->row_array();
												if($data_exp_skg){
													$array_insert_efx = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_assets_id' => $data_det_ass['id'],
														'tbl_exp_id' => $data_exp_skg['id'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'rd_qty' => $u['rd_qty'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_efx = array(
														'tbl_assets_id' => $data_det_ass['id'],
														'tbl_exp_id' => $data_exp_skg['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_efx = $this->db->get_where('tbl_efx', $array_cek_efx)->row_array();
													if(!$cek_efx){
														$this->db->insert('tbl_efx', $array_insert_efx);
													}
												}
												
											}
										}
									}
								}
							break;
							
							case "act_ass":
								$get_ass = $this->db->get_where('tbl_assets', $array)->result_array();
								foreach($get_ass as $k => $h){
									$sqlact = "
										SELECT A.*, B.activity_code, B.descript
										FROM tbl_are A
										LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id
										WHERE A.tbl_assets_id = '".$h['id']."'
									";
									$qryact = $this->db->query($sqlact)->result_array();
									if($qryact){
										foreach($qryact as $t => $u){
											$array_det_act = array(
												'tbl_model_id' => $this->modeling['id'],
												'activity_code' => $u['activity_code'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_act = $this->db->get_where('tbl_acm', $array_det_act)->row_array();
											if($data_det_act){
												$array_ass_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'assets_id' => $h['assets_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_ass_skg = $this->db->get_where('tbl_assets', $array_ass_bulan_skg)->row_array();
												if($data_ass_skg){
													$array_insert_are = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_acm_id' => $data_det_act['id'],
														'tbl_assets_id' => $data_ass_skg['id'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'rd_qty' => $u['rd_qty'],
														'tbl_rdm_id' => $u['tbl_rdm_id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_are = array(
														'tbl_acm_id' => $data_det_act['id'],
														'tbl_assets_id' => $data_ass_skg['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_are = $this->db->get_where('tbl_are', $array_cek_are)->row_array();
													if(!$cek_are){
														$this->db->insert('tbl_are', $array_insert_are);
													}
												}
											}
										}
									}
								}
							break;
							case "exp_ass":
								$get_ass = $this->db->get_where('tbl_assets', $array)->result_array();
								foreach($get_ass as $k => $h){
									$sqlefx = "
										SELECT A.*, B.account, B.descript
										FROM tbl_efx A
										LEFT JOIN tbl_exp B ON B.id = A.tbl_exp_id
										WHERE A.tbl_assets_id = '".$h['id']."'
									";
									$qryefx = $this->db->query($sqlefx)->result_array();
									if($qryefx){
										foreach($qryefx as $t => $u){
											$array_det_exp = array(
												'tbl_model_id' => $this->modeling['id'],
												'account' => $u['account'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_exp = $this->db->get_where('tbl_exp', $array_det_exp)->row_array();
											if($data_det_exp){
												$array_ass_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'assets_id' => $h['assets_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_ass_skg = $this->db->get_where('tbl_assets', $array_ass_bulan_skg)->row_array();
												if($data_ass_skg){
													$array_insert_efx = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_assets_id' => $data_ass_skg['id'],
														'tbl_exp_id' => $data_det_exp['id'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'rd_qty' => $u['rd_qty'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_efx = array(
														'tbl_assets_id' => $data_ass_skg['id'],
														'tbl_exp_id' => $data_det_exp['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_efx = $this->db->get_where('tbl_efx', $array_cek_efx)->row_array();
													if(!$cek_efx){
														$this->db->insert('tbl_efx', $array_insert_efx);
													}
												}
												
											}
										}
									}
								}
							break;
							
							case "act_cobj":
								$get_prm = $this->db->get_where('tbl_prm', $array)->result_array();
								foreach($get_prm as $k => $h){
									$sqlact = "
										SELECT A.*, B.activity_code, B.descript
										FROM tbl_prd A
										LEFT JOIN tbl_acm B ON B.id = A.tbl_acm_id
										WHERE A.tbl_prm_id = '".$h['id']."'
									";
									$qryact = $this->db->query($sqlact)->result_array();
									if($qryact){
										foreach($qryact as $t => $u){
											$array_det_act = array(
												'tbl_model_id' => $this->modeling['id'],
												'activity_code' => $u['activity_code'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_act = $this->db->get_where('tbl_acm', $array_det_act)->row_array();
											if($data_det_act){
												$array_prm_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'prod_id' => $h['prod_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_prm_skg = $this->db->get_where('tbl_prm', $array_prm_bulan_skg)->row_array();
												if($data_prm_skg){
													$array_insert_prd = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_acm_id' => $data_det_act['id'],
														'tbl_prm_id' => $data_prm_skg['id'],
														'tbl_cdm_id' => $u['tbl_cdm_id'],
														'quantity' => $u['quantity'],
														'cost_rate' => $u['cost_rate'],
														'cost' => $u['cost'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_prd = array(
														'tbl_acm_id' => $data_det_act['id'],
														'tbl_prm_id' => $data_prm_skg['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_prd = $this->db->get_where('tbl_prd', $array_cek_prd)->row_array();
													if(!$cek_prd){
														$this->db->insert('tbl_prd', $array_insert_prd);
													}
												}
											}
										}
									}
								}
							break;
							case "cust_cobj":
								$get_prm = $this->db->get_where('tbl_prm', $array)->result_array();
								foreach($get_prm as $k => $h){
									$sqlptp = "
										SELECT A.*, B.customer_id, B.customer_name
										FROM tbl_ptp A
										LEFT JOIN tbl_cust B ON B.id = A.tbl_cust_id
										WHERE A.tbl_prm_id = '".$h['id']."'
										AND (A.tbl_cust_id IS NOT NULL AND A.tbl_cust_id <> 0)
									";
									$qryptp = $this->db->query($sqlptp)->result_array();
									if($qryptp){
										foreach($qryptp as $t => $u){
											$array_det_cust = array(
												'tbl_model_id' => $this->modeling['id'],
												'customer_id' => $u['customer_id'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_cust = $this->db->get_where('tbl_cust', $array_det_cust)->row_array();
											if($data_det_cust){
												$array_prm_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'prod_id' => $h['prod_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_prm_skg = $this->db->get_where('tbl_prm', $array_prm_bulan_skg)->row_array();
												if($data_prm_skg){
													$array_insert_ptp = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_prm_id' => $data_prm_skg['id'],
														'tbl_cust_id' => $data_det_cust['id'],
														'quantity' => $u['quantity'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'revenue' => $u['revenue'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_ptp = array(
														'tbl_prm_id' => $data_prm_skg['id'],
														'tbl_cust_id' => $data_det_cust['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_ptp = $this->db->get_where('tbl_ptp', $array_cek_ptp)->row_array();
													if(!$cek_ptp){
														$this->db->insert('tbl_ptp', $array_insert_ptp);
													}
												}
												
											}
										}
									}
								}
							break;
							case "loc_cobj":
								$get_prm = $this->db->get_where('tbl_prm', $array)->result_array();
								foreach($get_prm as $k => $h){
									$sqlptp = "
										SELECT A.*, B.location_id, B.location_name
										FROM tbl_ptp A
										LEFT JOIN tbl_location B ON B.id = A.tbl_location_id
										WHERE A.tbl_prm_id = '".$h['id']."'
										AND (A.tbl_location_id IS NOT NULL AND A.tbl_location_id <> 0)
									";
									$qryptp = $this->db->query($sqlptp)->result_array();
									if($qryptp){
										foreach($qryptp as $t => $u){
											$array_det_loc = array(
												'tbl_model_id' => $this->modeling['id'],
												'location_id' => $u['location_id'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_loc = $this->db->get_where('tbl_location', $array_det_loc)->row_array();
											if($data_det_loc){
												$array_prm_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'prod_id' => $h['prod_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_prm_skg = $this->db->get_where('tbl_prm', $array_prm_bulan_skg)->row_array();
												if($data_prm_skg){
													$array_insert_ptp = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_prm_id' => $data_prm_skg['id'],
														'tbl_location_id' => $data_det_loc['id'],
														'quantity' => $u['quantity'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'revenue' => $u['revenue'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_ptp = array(
														'tbl_prm_id' => $data_prm_skg['id'],
														'tbl_location_id' => $data_det_loc['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_ptp = $this->db->get_where('tbl_ptp', $array_cek_ptp)->row_array();
													if(!$cek_ptp){
														$this->db->insert('tbl_ptp', $array_insert_ptp);
													}
												}
												
											}
										}
									}
								}
							break;
							
							case "cobj_cust":
								$get_cust = $this->db->get_where('tbl_cust', $array)->result_array();
								foreach($get_cust as $k => $h){
									$sqlptp = "
										SELECT A.*, B.prod_id, B.descript
										FROM tbl_ptp A
										LEFT JOIN tbl_prm B ON B.id = A.tbl_prm_id
										WHERE A.tbl_cust_id = '".$h['id']."'
										AND (A.tbl_prm_id IS NOT NULL AND A.tbl_prm_id <> 0)
									";
									$qryptp = $this->db->query($sqlptp)->result_array();
									if($qryptp){
										foreach($qryptp as $t => $u){
											$array_det_prm = array(
												'tbl_model_id' => $this->modeling['id'],
												'prod_id' => $u['prod_id'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_prm = $this->db->get_where('tbl_prm', $array_det_prm)->row_array();
											if($data_det_prm){
												$array_cust_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'customer_id' => $h['customer_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_cust_skg = $this->db->get_where('tbl_cust', $array_cust_bulan_skg)->row_array();
												if($data_cust_skg){
													$array_insert_ptp = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_cust_id' => $data_cust_skg['id'],
														'tbl_prm_id' => $data_det_prm['id'],
														'quantity' => $u['quantity'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'revenue' => $u['revenue'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_ptp = array(
														'tbl_cust_id' => $data_cust_skg['id'],
														'tbl_prm_id' => $data_det_prm['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_ptp = $this->db->get_where('tbl_ptp', $array_cek_ptp)->row_array();
													if(!$cek_ptp){
														$this->db->insert('tbl_ptp', $array_insert_ptp);
													}
												}
												
											}
										}
									}
								}
							break;
							case "loc_cust":
								$get_cust = $this->db->get_where('tbl_cust', $array)->result_array();
								foreach($get_cust as $k => $h){
									$sqlptp = "
										SELECT A.*, B.location_id, B.location_name
										FROM tbl_ptp A
										LEFT JOIN tbl_location B ON B.id = A.tbl_location_id
										WHERE A.tbl_cust_id = '".$h['id']."'
										AND (A.tbl_location_id IS NOT NULL AND A.tbl_location_id <> 0)
									";
									$qryptp = $this->db->query($sqlptp)->result_array();
									if($qryptp){
										foreach($qryptp as $t => $u){
											$array_det_loc = array(
												'tbl_model_id' => $this->modeling['id'],
												'location_id' => $u['location_id'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_loc = $this->db->get_where('tbl_location', $array_det_loc)->row_array();
											if($data_det_loc){
												$array_cust_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'customer_id' => $h['customer_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_cust_skg = $this->db->get_where('tbl_cust', $array_cust_bulan_skg)->row_array();
												if($data_cust_skg){
													$array_insert_ptp = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_cust_id' => $data_cust_skg['id'],
														'tbl_location_id' => $data_det_loc['id'],
														'quantity' => $u['quantity'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'revenue' => $u['revenue'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_ptp = array(
														'tbl_cust_id' => $data_cust_skg['id'],
														'tbl_location_id' => $data_det_loc['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_ptp = $this->db->get_where('tbl_ptp', $array_cek_ptp)->row_array();
													if(!$cek_ptp){
														$this->db->insert('tbl_ptp', $array_insert_ptp);
													}
												}
												
											}
										}
									}
								}
							break;
							
							case "cobj_loc":
								$get_location = $this->db->get_where('tbl_location', $array)->result_array();
								foreach($get_location as $k => $h){
									$sqlptp = "
										SELECT A.*, B.prod_id, B.descript
										FROM tbl_ptp A
										LEFT JOIN tbl_prm B ON B.id = A.tbl_prm_id
										WHERE A.tbl_location_id = '".$h['id']."'
										AND (A.tbl_prm_id IS NOT NULL AND A.tbl_prm_id <> 0)
									";
									$qryptp = $this->db->query($sqlptp)->result_array();
									if($qryptp){
										foreach($qryptp as $t => $u){
											$array_det_prm = array(
												'tbl_model_id' => $this->modeling['id'],
												'prod_id' => $u['prod_id'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_prm = $this->db->get_where('tbl_prm', $array_det_prm)->row_array();
											if($data_det_prm){
												$array_loc_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'location_id' => $h['location_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_loc_skg = $this->db->get_where('tbl_location', $array_loc_bulan_skg)->row_array();
												if($data_loc_skg){
													$array_insert_ptp = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_location_id' => $data_loc_skg['id'],
														'tbl_prm_id' => $data_det_prm['id'],
														'quantity' => $u['quantity'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'revenue' => $u['revenue'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_ptp = array(
														'tbl_location_id' => $data_loc_skg['id'],
														'tbl_prm_id' => $data_det_prm['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_ptp = $this->db->get_where('tbl_ptp', $array_cek_ptp)->row_array();
													if(!$cek_ptp){
														$this->db->insert('tbl_ptp', $array_insert_ptp);
													}
												}
												
											}
										}
									}
								}
							break;
							case "cust_loc":
								$get_location = $this->db->get_where('tbl_location', $array)->result_array();
								foreach($get_location as $k => $h){
									$sqlptp = "
										SELECT A.*, B.customer_id, B.customer_name
										FROM tbl_ptp A
										LEFT JOIN tbl_cust B ON B.id = A.tbl_cust_id
										WHERE A.tbl_location_id = '".$h['id']."'
										AND (A.tbl_cust_id IS NOT NULL AND A.tbl_cust_id <> 0)
									";
									$qryptp = $this->db->query($sqlptp)->result_array();
									if($qryptp){
										foreach($qryptp as $t => $u){
											$array_det_cust = array(
												'tbl_model_id' => $this->modeling['id'],
												'customer_id' => $u['customer_id'],
												'bulan' => $data['bulan_sekarang'],
												'tahun' => $data['tahun_sekarang'],
											);
											$data_det_cust = $this->db->get_where('tbl_cust', $array_det_cust)->row_array();
											if($data_det_cust){
												$array_loc_bulan_skg = array(
													'tbl_model_id' => $this->modeling['id'],
													'location_id' => $h['location_id'],
													'bulan' => $data['bulan_sekarang'],
													'tahun' => $data['tahun_sekarang'],
												);
												$data_loc_skg = $this->db->get_where('tbl_location', $array_loc_bulan_skg)->row_array();
												if($data_loc_skg){
													$array_insert_ptp = array(
														//'tbl_model_id' =>  $this->modeling['id'],
														'tbl_location_id' => $data_loc_skg['id'],
														'tbl_cust_id' => $data_det_cust['id'],
														'quantity' => $u['quantity'],
														'percent' => $u['percent'],
														'cost' => $u['cost'],
														'revenue' => $u['revenue'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
														"create_date" => date('Y-m-d H:i:s'),
														"create_by" => $this->auth['nama_lengkap'],
													);
													$array_cek_ptp = array(
														'tbl_location_id' => $data_loc_skg['id'],
														'tbl_cust_id' => $data_det_cust['id'],
														'bulan' => $data['bulan_sekarang'],
														'tahun' => $data['tahun_sekarang'],
													);
													$cek_ptp = $this->db->get_where('tbl_ptp', $array_cek_ptp)->row_array();
													if(!$cek_ptp){
														$this->db->insert('tbl_ptp', $array_insert_ptp);
													}
												}
												
											}
										}
									}
								}
							break;
						}
					}
				}
			break;
			//End Duplicate Costing
			
		}
		
		switch ($sts_crud){
			case "add":
				$this->db->insert($table,$data);
			break;
			case "edit":
				$this->db->update($table, $data, array($field_id => $id) );
			break;
			case "delete":
				$this->db->where('id', $this->input->post('id'));
				$this->db->delete($table,$data);
			break;
			case "map_rdm":
				$tabel = $data['tabel'];
				$array_update = array(
					'tbl_rdm_id' => $data['tbl_rdm_id'],
					'rd_tot_qty' => $data['rd_tot_qty'],
				);
				
				if($tabel == 'tbl_emp'){
					$array_update['cost_nbr'] = $data['cost_nbr'];
				}elseif($tabel == 'tbl_exp'){
					
				}
				$this->db->update($tabel, $array_update, array('id'=>$id) );
			break;
		}
		
		if($tot_cost == true){
			$sql_tot_cost = "
				SELECT SUM(cost) as total_cost
				FROM tbl_are
				WHERE tbl_acm_id = ".$data['tbl_acm_id']."
				AND bulan = ".$bulan." AND tahun = ".$tahun."
				GROUP BY tbl_acm_id
			";
			$qrytotcost = $this->db->query($sql_tot_cost)->row_array();
			$cek_data = $this->db->get_where('tbl_acm_total_cost', array('tbl_acm_id'=>$data['tbl_acm_id'], 'bulan'=>$bulan, 'tahun'=>$tahun ) )->row_array();
			if($cek_data){
				$this->db->update('tbl_acm_total_cost', array('total_cost'=>$qrytotcost['total_cost']), array('tbl_acm_id'=>$data['tbl_acm_id'], 'bulan'=>$bulan, 'tahun'=>$tahun ) );
			}else{
				$arrayinsert = array(
					'tbl_acm_id' => $data['tbl_acm_id'],
					'bulan' => $bulan,
					'tahun' => $tahun,
					'total_cost' => $qrytotcost['total_cost'],
				);
				$this->db->insert('tbl_acm_total_cost', $arrayinsert);
			}
		}
		
		if($this->db->trans_status() == false){
			return $this->db->trans_rollback();
			//return 0;
		} else{
			return $this->db->trans_commit();
		}
	
	}
	
}
