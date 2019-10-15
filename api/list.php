<?php
require_once('../../../../../wp-config.php');
/* 
* Api For get all agencies list 
* 
*/
if(isset($_GET['agency']) && $_GET['agency']=="all" ){
	//it will get all user data array whoes role is agencyadmin with his meta fields data
	$all_users_qry = new WP_User_Query( array( 'role__in' => array('agencyadmin'), 'fields' => 'all_with_meta' ) );
	$user_array = [];
	if ( ! empty( $all_users_qry->get_results() ) ) {
		foreach($all_users_qry->get_results() as $user ){
			
			$single_data_arr = [];
			// get user id 
			$single_data_arr["ID"] = $user->ID;
			// get Email 
			$single_data_arr['email'] = $user->user_email;
			// get Name from user meta  
			$single_data_arr['name'] = $user->display_name;
			// get registration code from user meta  
			$single_data_arr['reg_code'] = $user->get('reg_code');
			// get count of agency users  
			$all_subs_qry = new WP_User_Query( array( 'role__in' => array('Subscriber'), 'meta_query' => array(
				array(
		            'key'     => 'agencyid',
		            'value'   => $user->ID,
		            'compare' => '='
		        ),		        
			), 'count_total' => true));
			$single_data_arr['total_users'] = $all_subs_qry->get_total();
			array_push($user_array,$single_data_arr);
		}
	}
	$api_results = array('response' => true, 'data' => $user_array);
	echo json_encode($api_results);
}
/* 
* Api For get Agency data by agency ID 
* 
*/
elseif((isset($_GET['agency'])) && $_GET['agency']!="all")
{
	$agency_id=$_GET['agency'];
	//it will get all user data array whoes role is agencyadmin with his meta fields data
	// $all_users_qry = new WP_User_Query( 
	// 	array(
	// 	'search'         => $agency_id,
	// 	'search_columns' => array( 'ID'),
	// 	'role__in' => array('agencyadmin'),
	// 	'fields' => 'all_with_meta'
	// 	),

	// );
	// print_r($all_users_qry);
	// $user_array = [];
	// if ( ! empty( $all_users_qry->get_results() ) ) {
	// 	foreach($all_users_qry->get_results() as $user ){
			
	// 		$single_data_arr = [];
	// 		// get user id 
	// 		$single_data_arr["ID"] = $user->ID;
	// 		// get Email 
	// 		$single_data_arr['email'] = $user->user_email;
	// 		// get Name from user meta  
	// 		$single_data_arr['name'] = $user->display_name;
	// 		// get registration code from user meta  
	// 		$single_data_arr['reg_code'] = $user->get('reg_code');
	// 		// get count of agency users  
	// 		$all_subs_qry = new WP_User_Query( array( 'role__in' => array('Subscriber'), 'meta_query' => array(
	// 			array(
	// 	            'key'     => 'agencyid',
	// 	            'value'   => $user->ID,
	// 	            'compare' => '='
	// 	        ),		        
	// 		), 'count_total' => true));
	// 		$single_data_arr['total_users'] = $all_subs_qry->get_total();
	// 		array_push($user_array,$single_data_arr);
	// 	}
	// }

	// $api_results = array('response' => true, 'data' => $user_array);
	// if(!empty($api_results['data'])){
 //       	echo json_encode($api_results);
	// }else{
	// 		echo "No agency found";
	// }
}



