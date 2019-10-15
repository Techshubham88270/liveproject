<?php
require_once('../../../../../wp-config.php');
/* 
* Api For get all agencies list 
*/
// required headers
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header('Content-Type: application/json');
//get registration number by url
if(isset($_GET['reg_code'])){
	$reg_code=$_GET['reg_code'];
}
	$all_subs_qry = new WP_User_Query( array( 'role__in' => array('agencyadmin'), 'meta_query' => array(
				array(
				    'key'     => 'reg_code',
				    'value'   => $reg_code,
				    'compare' => '='
				),		        
			), 'count_total' => true));
	$results=$all_subs_qry->results;
	//fetch agency id by wp_user_query
	$agencyID=$results[0]->ID;

	if($agencyID){
		// get agency id if reg_code is exist in any of agency
      $agency=$results[0]->ID;
	}else{
		// get agency id if reg_code is not exist in any of agency
      $agency=-1;
	}
	//result of api giving agency ID on the base of registration no .
	$api_results = array('agencyID' => $agency);
	echo json_encode($api_results);