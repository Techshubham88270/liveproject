<?php
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

/**
* Class Agencies_List_Table is extands predefine wordpress class WP_List_Table class
*/
class Subscriber_List_Table extends WP_List_Table {
	/**
	* This  function use for add bulk action section manu in agencies list table
	*/
	// function get_bulk_actions(){
	// 	$bulk_action=array(
	// 		"delete"=>"Delete"
	// 	);
	// 	return $bulk_action ;
	// }

   /**
    * This  function use for add checkboxes before each row of agencies list table
   */
	function column_cb($item){
		return sprintf('<input type="checkbox" class="checkID" name="agency_ids[]" value="%d"/>',$item['id']);
	}
	/**
    * This  function create Header of agencies list table
    */
	function get_columns(){
		  $columns = array(
		  	'name'    => 'Name',
		  	'email'	=>'Email',
		    'title' => 'Title',
		    'city'      => 'City',
		    'agency'      => 'Agency',    
		  );
		return $columns;
	}


	//prepear_items
	function prepare_items() {
        /**
		*  $orderby variable set shorting by perticular field value 
		*
		*	$order variable set ASENDING/DESENDING order
		*/
		$orderby = isset($_GET['orderby']) ? trim($_GET['orderby']) : "";
		$order=isset($_GET['order']) ? trim($_GET['order']):"";
		/**
		*  $search_agency variable set searched value 
		*/
		//$search_agency=isset($_POST['sub_s']) ? trim($_POST['sub_s']): "";
		/**
		*  Bulk action delete users 
		*/ 
		    // check if in bulk action delete selected or not
		if(isset($_POST['action'])&&($_POST['action']=='delete')){
			//$delete_agency variable set array of ids for delete agencies
			if(isset($_POST['agency_ids'])){
				$delete_agency=$_POST['agency_ids'];
			}
		}
		
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		/**
		*  $attr_subscribers_data variable return all data of agencies list table
		*/
		$attr_subscribers_data=$this->subscribers_data($orderby,$order,$search_agency,$delete_agency);
		/**
		*  Pagination of agencies list table 
		*/
		// number of row want to display per page
		$per_page = 10;
		// $current_page return current page number
		$current_page = $this->get_pagenum();
		$total_items = count($attr_subscribers_data);
		/**
		*  Set argument for pagination 
		*/
		$this->set_pagination_args(array(
		"total_items" => $total_items,
		"per_page" => $per_page
		));
		//1-1=>0*3=0 starting point to $per_page=3 0 to 3 
		$this->items = array_slice($attr_subscribers_data,(($current_page-1)*$per_page),$per_page);
	}

	/**
	* function use for set table data 
	* get all records  of agencies
	*/
	function subscribers_data($orderby = '',$order = '',$search_agency = '',$delete_agency=''){
		// print_r($search_agency);

        // fetch user data from wp_user table on the bases of serach,orderby,order

		/**
		* code for delete agencies by bulk action (delete) 
		*/

		$current_user = wp_get_current_user();
		$loginrole=$current_user->roles[0];

		//condition for fetch list of subscriber if orderby value not in url it will set default value
	       if(!isset($_GET['orderby'])){
	       	   $orderby = 'firstName';
	       	   $order = 'asc';
	       }
		
			global $wpdb;
			$table ='user';
			//condition for fetch list of subscriber if login user is agencyadmin
				if(isset($_GET['fagency_id'])){
					$agency_id=$_GET['fagency_id'];
					//condition for fetch list of subscriber fronm table on the base of first name and last name
					if(isset($_GET['sub_s'])){
						$search_sub=$_GET['sub_s'];
					    $sql = "SELECT * FROM $table WHERE agencyId = $agency_id AND (firstName LIKE '%$search_sub%' OR lastName LIKE '%$search_sub%') ";
					}else{
						$sql = "SELECT * FROM $table WHERE agencyId = $agency_id ORDER BY $orderby $order";
					}
				}
				//condition for fetch list of subscriber if login user is agencyadmin
				elseif($loginrole=="agencyadmin"){
					$cuurentuid=$current_user->ID;
					//condition for fetch list of subscriber fronm table on the base of first name and last name
					if(isset($_GET['sub_s'])){
						$search_sub=$_GET['sub_s'];
					    $sql = "SELECT * FROM $table WHERE agencyId = $cuurentuid AND (firstName LIKE '%$search_sub%' OR lastName LIKE '%$search_sub%') ";
					}else{
						$sql = "SELECT * FROM $table WHERE agencyId = $cuurentuid ORDER BY $orderby $order";
					}
					
                //condition for fetch list of subscriber if login user is administrator
				}else{
					//condition for fetch list of subscriber fronm table on the base of first name and last name
					if(isset($_GET['sub_s'])){
						$search_sub=$_GET['sub_s'];
					    $sql = "SELECT * FROM $table WHERE agencyId != '' AND (firstName LIKE '%$search_sub%' OR lastName LIKE '%$search_sub%' ) ORDER BY $orderby $order";
					}else{
						$sql = "SELECT * FROM $table WHERE agencyId != '' ORDER BY $orderby $order";
				    }
				}
			$results = $wpdb->get_results($sql);

			$agencies_array = array();
			foreach( $results as $result ) {
		$agencydata=get_user_by('ID', $result->agencyId);
				$agencies_array[] = array(
					'title' => $result->title,
					'name' => '<a href="'.admin_url('admin.php?page=edit_new_subscriber&sub_id='.$result->id).'">'. $result->firstName." ".$result->lastName.'</a><br><span class="edit"><a href="'.admin_url('admin.php?page=edit_new_subscriber&sub_id='.$result->id).'">Edit</a></span>',

					'email'=>  $result->email,
					'city' =>  $result->city,
					'agency' => $agencydata->user_login,
					'date_created' => $user->data->user_registered,
					'id' => $result->id,
				);
		}
		return $agencies_array;
	}

	/**
	* function use for dispay /set data on the base of table headers  
	* 
	*/
	function column_default( $item, $column_name ) {
		switch( $column_name ) { 
			case 'title':
			case 'email':
			case 'name':
			case 'city':
			case 'agency':
			return $item[ $column_name ];
			default:
			return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
	  	}
	}
    /**
	* function use for shorting data of table on bases of title  
	* 
	*/
	function get_sortable_columns() {
		$sortable_columns = array(
			'name' => array('firstName',false)
		);
	return $sortable_columns;
	}
}

/**
* function use for display list table on wp-admin in all agencies section  
* 
*/
function my_render_list_page(){
  $SubscriberListTable = new Subscriber_List_Table();
  $current_user = wp_get_current_user();
	//check role of login user
	$loginrole=$current_user->roles[0];
	if($loginrole=="administrator"){
		echo '<div class="wrap agency_table"><h1 class="wp-heading-inline">
		Users</h1><a href="'.admin_url('user-new.php').'" class="page-title-action">Add New</a>'; 
  $SubscriberListTable->prepare_items();

	}elseif($loginrole=="agencyadmin"){
		$r_code=get_user_meta( $current_user->ID, 'reg_code', true);
		echo '<div class="wrap agency_table"><h1 class="wp-heading-inline">
		Agency Users</h1><br><div class="wrap agency_table"><h1 class="wp-heading-inline">
		Registration code : '.$r_code.'</h1>';
	
		

  $SubscriberListTable->prepare_items();

	}

  echo"<form  method='GET' name='form_search_agency' action=''>";
  echo '<p class="search-box">
	   <label class="screen-reader-text" for="user-search-input">Search Users:</label>
	   <input type="hidden" id="user-search-input" name="page" value="subscribers">
	    <input type="search" id="user-search-input" name="sub_s" value="">
		<input type="submit" id="search-submit" name="sub_search" class="button" value="Search Users"></p>';
		if(isset($_GET["fagency_id"])){
			echo '<input type="hidden" id="user_fagency_id" name="fagency_id" value="'.$_GET["fagency_id"].'">';
		}
  echo "</form>";
  //function use for display list table
    //get count of admin user
	$alladministrator = new WP_User_Query( array( 'role__in' => array('administrator'), 'count_total' => true));
				$administrator_count = $alladministrator->get_total();
	//get count of agency admin user
	$allagencyadmin = new WP_User_Query( array( 'role__in' => array('agencyadmin'), 'count_total' => true));
	$agencyadmin_count = $allagencyadmin->get_total();
    
    //get count of subscribers user
	
	global $wpdb;
	$table ='user';
		if(isset($_GET['fagency_id'])){
			$agency_id=$_GET['fagency_id'];
			$sql = "SELECT COUNT(*) FROM $table WHERE agencyId = $agency_id";
		}
		elseif($loginrole=="agencyadmin"){
			$cuurentuid=$current_user->ID;
			$sql = "SELECT COUNT(*) FROM $table WHERE agencyId = $cuurentuid";

		}else{
			$sql = "SELECT COUNT(*) FROM $table WHERE agencyId != ''";
		}
	$sub_count = $wpdb->get_var($sql);


	?>
   <!-- html for add download subscriberes button -->
	<form action="#" method="POST">
		<input type="hidden" id="fellow_export_excel" name="fellow_export_excel" value="1" />
		<input class="button button-primary user_export_button" style="margin-top:3px;" type="submit" value="<?php esc_attr_e('Download Subscribers', 'mytheme');?>" />
	</form>
	<!-- add manu (agenAdministrator (1) |  Agency Administrator (2)  Subscribers (2) ) on top of subscribe table-->
	<ul class="subsubsub">
		<?if($loginrole=="administrator"){?>
			<li class="administrator"><a href="users.php?role=administrator">Administrator 
				<span class="count">(<? echo $administrator_count; ?>)</span></a> |
			</li>
			<li class="agencyadmin"><a href="users.php?role=agencyadmin">Agency Administrator 
				<span class="count">(<? echo $agencyadmin_count; ?>)</span></a>
			</li>
		<?}?>
		
		<li class="subscribers"><a href="admin.php?page=subscribers">Subscribers <span class="count">(<? echo $sub_count?>)</span></a></li>
	</ul>
	<?
  echo"<form  class='subscribe_table' method='POST' name='form_search_agency' action='".$_SERVER['PHP_SELF']."?page=subscribers'>";
  $SubscriberListTable->display(); 
  echo '</form></div>'; 
}
my_render_list_page();
