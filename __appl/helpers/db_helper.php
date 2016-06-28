<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
function dump($arg) {
	echo "<br /><pre>";
	if(is_string($arg)) echo $arg;
	else print_r($arg);
	echo "</pre>";
	die();
}
function rst2Array($sql, $ret="all", $mode="std") {
	$output = null;
	$CI 	=& get_instance();
	$query	= $CI->db->query($sql);
	$fields = $query->list_fields();
	$meta	= $query->field_data();
	$nrows	= $query->num_rows();
	$data	= $query->result_array();
	$query->free_result();

	if($nrows>0) {
		switch ($ret) {
			case "cel":
				$keys	= array_keys($data[0]);
				$output	= $data[0][$keys[0]];
				break;
			case "row":
				$output = $data[0];
				break;
			case "all":
				$output = $data;
				break;
		}
		unset($data);
	}
	
	switch ($mode) {
		case "std":
			return $output;
			break;
		case "all":
			return array('rows'=>$output, 'nrows'=>$nrows, 'fields'=>$fields, 'meta'=>$meta);
			break;
		case 'sql':
			dump($sql);
			break;
		case "rst":
		case "out":
			dump($output);
			break;
	}
}
function rst2Object($sql, $mode="std") {
	$CI 	=& get_instance();
	$query	= $CI->db->query($sql);
	$fields = $query->list_fields();
	$meta	= $query->field_data();
	$nrows	= $query->num_rows();
	$output	= $query->result();
	$query->free_result();

	switch ($mode) {
		case "std":
			return $output;
			break;
		case "all":
			return array('rows'=>$output, 'nrows'=>$nrows, 'fields'=>$fields, 'meta'=>$meta);
			break;
		case 'sql':
			dump($sql);
			break;
		case "rst":
		case "out":
			dump($output);
			break;
	}
}
function rst2ID($a) {
	$o = array();
	foreach($a as $k=>$v) $o[$v['id']] = $v;
	return $o;
}
function rst2Tree($array, $delimiter = '_', $baseval = false) {
	if(!is_array($array)) return false;
	$splitRE   = '/' . preg_quote($delimiter, '/') . '/';
	$returnArr = array();
	foreach ($array as $key => $val) {
		// Get parent parts and the current leaf
		$parts  	= preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
		$leafPart	= array_pop($parts);

		// Build parent structure
		// Might be slow for really deep and large structures
		$parentArr = &$returnArr;
		foreach ($parts as $part) {
			if (!isset($parentArr[$part])) {
				$parentArr[$part] = array();
			}
			elseif (!is_array($parentArr[$part])) {
				if ($baseval) {
					$parentArr[$part] = array('__base_val' => $parentArr[$part]);
				}
				else {
					$parentArr[$part] = array();
				}
			}
			$parentArr = &$parentArr[$part];
		}

		// Add the final part to the structure
		if (empty($parentArr[$leafPart])) {
			$parentArr[$leafPart] = $val;
		}
		elseif ($baseval && is_array($parentArr[$leafPart])) {
			$parentArr[$leafPart]['__base_val'] = $val;
		}
	}
	return $returnArr;
}
function explodeTree($array, $delimiter = '_', $baseval = false) {
	if(!is_array($array)) return false;
	$splitRE   = '/' . preg_quote($delimiter, '/') . '/';
	$returnArr = array();
	foreach ($array as $key => $val) {
		// Get parent parts and the current leaf
		$parts  	= preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
		$leafPart	= array_pop($parts);

		// Build parent structure
		// Might be slow for really deep and large structures
		$parentArr = &$returnArr;
		foreach ($parts as $part) {
			if (!isset($parentArr[$part])) {
				$parentArr[$part] = array();
			}
			elseif (!is_array($parentArr[$part])) {
				if ($baseval) {
					$parentArr[$part] = array('__base_val' => $parentArr[$part]);
				}
				else {
					$parentArr[$part] = array();
				}
			}
			$parentArr = &$parentArr[$part];
		}

		// Add the final part to the structure
		if (empty($parentArr[$leafPart])) {
			$parentArr[$leafPart] = $val;
		}
		elseif ($baseval && is_array($parentArr[$leafPart])) {
			$parentArr[$leafPart]['__base_val'] = $val;
		}
	}
	return $returnArr;
}
function plotTree($arr, $indent=0, $mother_run=true){
    if($mother_run){
        // the beginning of plotTree. We're at rootlevel
        echo "startn";
    }
 
    foreach($arr as $k=>$v){
        // skip the baseval thingy. Not a real node.
        if($k == "__base_val") continue;
        // determine the real value of this node.
        $show_val = ( is_array($v) ? $v["__base_val"] : $v );
 
        // show the indents
        echo str_repeat("  ", $indent);
        if($indent == 0){
            // this is a root node. no parents
            echo "O ";
        } elseif(is_array($v)){
            // this is a normal node. parents and children
            echo "+ ";
        } else{
            // this is a leaf node. no children
            echo "- ";
        }
 
        // show the actual node
        echo $k . " (".$show_val.")"."n";
 
        if(is_array($v)){
            // this is what makes it recursive, rerun for childs
            plotTree($v, ($indent+1), false);
        }
    }
 
    if($mother_run){
        echo "endn";
    }
}
function is_xhr() {
	$flag = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' : false;
	//return ($_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
	return $flag;
}
function autherz($flag, $redirect=false) {
	if(!$flag) {
		if(is_xhr()) echo "expired";
		else {
			$goto = $redirect ? $redirect : $_SERVER['HTTP_HOST'];
			header("Location: http://$redirect");
		}
		exit();
	}
}

//helper djenonk
function arraydate($type=""){
	switch($type){
		case 'tanggal':
			$data = array(
				'0' => array('id'=>'1','txt'=>'1'),
				'1' => array('id'=>'2','txt'=>'2'),
				'2' => array('id'=>'3','txt'=>'3'),
				'3' => array('id'=>'4','txt'=>'4'),
				'4' => array('id'=>'5','txt'=>'5'),
				'5' => array('id'=>'6','txt'=>'6'),
				'6' => array('id'=>'7','txt'=>'7'),
				'7' => array('id'=>'8','txt'=>'8'),
				'8' => array('id'=>'9','txt'=>'9'),
				'9' => array('id'=>'10','txt'=>'10'),
				'10' => array('id'=>'11','txt'=>'11'),
				'11' => array('id'=>'12','txt'=>'12'),
				'12' => array('id'=>'13','txt'=>'13'),
				'13' => array('id'=>'14','txt'=>'14'),
				'14' => array('id'=>'15','txt'=>'15'),
				'15' => array('id'=>'16','txt'=>'16'),
				'16' => array('id'=>'17','txt'=>'17'),
				'17' => array('id'=>'18','txt'=>'18'),
				'18' => array('id'=>'19','txt'=>'19'),
				'19' => array('id'=>'20','txt'=>'20'),
				'20' => array('id'=>'21','txt'=>'21'),
				'21' => array('id'=>'22','txt'=>'22'),
				'22' => array('id'=>'23','txt'=>'23'),
				'23' => array('id'=>'24','txt'=>'24'),
				'24' => array('id'=>'25','txt'=>'25'),
				'25' => array('id'=>'26','txt'=>'26'),
				'26' => array('id'=>'27','txt'=>'27'),
				'27' => array('id'=>'28','txt'=>'28'),
				'28' => array('id'=>'29','txt'=>'29'),
				'29' => array('id'=>'30','txt'=>'30'),
				'30' => array('id'=>'31','txt'=>'31'),
			);
			
			/*
			$data = array();
			$tgl = 1;
			$no = 0;
			while($tgl <= 31){
				array_push($data, array('id' => $tgl, 'value'=>$tgl));
				$tgl++;
			}
			*/
		break;
		case 'bulan':
			$data = array(
				'0' => array('id'=>'1','txt'=>'Januari'),
				'1' => array('id'=>'2','txt'=>'Februari'),
				'2' => array('id'=>'3','txt'=>'Maret'),
				'3' => array('id'=>'4','txt'=>'April'),
				'4' => array('id'=>'5','txt'=>'Mei'),
				'5' => array('id'=>'6','txt'=>'Juni'),
				'6' => array('id'=>'7','txt'=>'Juli'),
				'7' => array('id'=>'8','txt'=>'Agustus'),
				'8' => array('id'=>'9','txt'=>'September'),
				'9' => array('id'=>'10','txt'=>'Oktober'),
				'10' => array('id'=>'11','txt'=>'November'),
				'11' => array('id'=>'12','txt'=>'Desember'),
			);
		break;
		case 'bulan_singkat':
			$data = array(
				'0' => array('id'=>'1','txt'=>'Jan'),
				'1' => array('id'=>'2','txt'=>'Feb'),
				'2' => array('id'=>'3','txt'=>'Mar'),
				'3' => array('id'=>'4','txt'=>'Apr'),
				'4' => array('id'=>'5','txt'=>'Mei'),
				'5' => array('id'=>'6','txt'=>'Jun'),
				'6' => array('id'=>'7','txt'=>'Jul'),
				'7' => array('id'=>'8','txt'=>'Ags'),
				'8' => array('id'=>'9','txt'=>'Sept'),
				'9' => array('id'=>'10','txt'=>'Okt'),
				'10' => array('id'=>'11','txt'=>'Nov'),
				'11' => array('id'=>'12','txt'=>'Des'),
			);
		break;
		case 'tahun':
			$data = array();
			$year = date('Y');
			$year_kurang = ($year-3);
			$no = 0;
			while($year >= $year_kurang ){
				array_push($data, array('id' => $year, 'txt'=>$year));
				$year--;
			}
		break;
		case 'tahun_lahir':
			$data = array();
			$year = date('Y');
			$no = 0;
			while($year >= 1950){
				array_push($data, array('id' => $year, 'txt'=>$year));
				$year--;
			}
		break;
	}
	
	return $data;
}




// end of file db_helper.php
/* Location: ./application/helper/db_helper.php */