<?php
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

/**
* Class Agencies_List_Table is extands predefine wordpress class WP_List_Table class
*/
class Agencies_List_Table extends WP_List_Table {
	/**
	* This  function use for add bulk action section manu in agencies list table
	*/
	function get_bulk_actions(){
		$bulk_action=array(
			"delete"=>"Delete"
		);
		return $bulk_action ;
	}

	

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
		  	'cb' => '<input type="checkbox"/>',
		    'title' => 'Title',
		    'users'    => 'Users',
		    'show_users'      => 'Show Users',
		    'reg_code'      => 'Reg Code',
		    'date_created'      => 'Date Created',	    
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
		$search_agency=isset($_POST['s']) ? trim($_POST['s']): "";
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
		*  $attr_agencies_data variable return all data of agencies list table
		*/
		$attr_agencies_data=$this->agencies_data($orderby,$order,$search_agency,$delete_agency);
		/**
		*  Pagination of agencies list table 
		*/
		// number of row want to display per page
		$per_page = 10;
		// $current_page return current page number
		$current_page = $this->get_pagenum();
		$total_items = count($attr_agencies_data);
		/**
		*  Set argument for pagination 
		*/
		$this->set_pagination_args(array(
		"total_items" => $total_items,
		"per_page" => $per_page
		));
		//1-1=>0*3=0 starting point to $per_page=3 0 to 3 
		$this->items = array_slice($attr_agencies_data,(($current_page-1)*$per_page),$per_page);
	}

	/**
	* function use for set table data 
	* get all records  of agencies
	*/
	function agencies_data($orderby = '',$order = '',$search_agency = '',$delete_agency=''){
        // fetch user data from wp_user table on the bases of serach,orderby,order

		/**
		* code for delete agencies by bulk action (delete) 
		*/
		if($delete_agency){
		    foreach($delete_agency as $del_agency){

				wp_delete_user($del_agency);

				/**
				* code for get user id of agency users which is 
				*/
				$all_del_u_qry = new WP_User_Query( array( 'role__in' => array('Subscriber'), 'meta_query' => array(
							array(
							'key'     => 'agencyid',
							'value'   => $del_agency,
							'compare' => '='
							)	        
							)
						)
				);
				$agency_users=$all_del_u_qry->results;
				/**
				* code for delete agency users  if any agency deleted by bulk action (delete) 
				*/
				foreach($agency_users as $agency_users){
				$agency_userid=$agency_users->ID;
				wp_delete_user($agency_userid);
				}
			}			    
		}
		$blogusers = get_users( array( 'search' => $search_agency,'role'=>'agencyadmin','orderby'=>$orderby,'order'=>$order ) );
		// Array of WP_User objects.
		$agencies_array = array();
		foreach ( $blogusers as $user ) {
		    //get user counts of perticular agency
			// $all_subs_qry = new WP_User_Query( array( 'role__in' => array('Subscriber'), 'meta_query' => array(
			// 	array(
			// 	    'key'     => 'agencyid',
			// 	    'value'   => $user->data->ID,
			// 	    'compare' => '='
			// 	),		        
			// ), 'count_total' => true));
			// $agency_user_count = $all_subs_qry->get_total();

			$agen_id=$user->data->ID;
			global $wpdb;

			$table ='user';

			$sql = "SELECT COUNT(*) FROM $table WHERE agencyId = $agen_id";
            //count of subscriber of agency
			$sub_count = $wpdb->get_var($sql);



				
				//fetch registration number of user by usermeta
				$reg_code=get_user_meta( $user->data->ID, 'reg_code' );
				//create array of data for agencies list table
				$agencies_array[] = array(
					'title' => '<a href="'.admin_url('admin.php?page=add_new_agency&agency_id='.$user->data->ID).'">'.$user->data->user_login.'</a><br><span class="edit"><a href="'.admin_url('admin.php?page=add_new_agency&agency_id='.$user->data->ID).'">Edit</a></span>',
					'users' => $sub_count, 
					'show_users' => '<a href="'.admin_url('admin.php?page=subscribers&fagency_id='.$user->data->ID).'">Show Users</a>',
					'reg_code' => $reg_code[0],
					'date_created' => $user->data->user_registered,
					'id' => $user->data->ID,
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
			case 'users':
			case 'show_users':
			case 'reg_code':
			case 'date_created':
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
			'title'  => array('title',false),
			'Users' => 'users',
			'Show Users'   => 'show_users',
			'Reg Code'   => 'reg_code',
			'date_created'   => array('date_created',false)
		);
	return $sortable_columns;
	}
}

/**
* function use for display list table on wp-admin in all agencies section  
* 
*/
function my_render_list_page(){
  $AgenciesListTable = new Agencies_List_Table();
  echo '<div class="wrap agency_table"><h1 class="wp-heading-inline">
		Agencies</h1><a href="'.admin_url('admin.php?page=add_new_agency').'" class="page-title-action">Add New</a>'; 
  $AgenciesListTable->prepare_items();
  echo"<form  method='POST' name='form_search_agency' action='".$_SERVER['PHP_SELF']."?page=agencies'>";
  //function use for display serch box on  list table
  $AgenciesListTable->search_box("Serach Agencies","search_agency_id"); 
  echo"</form>";
  //function use for display list table
  echo"<form  method='POST' name='form_search_agency' action='".$_SERVER['PHP_SELF']."?page=agencies'>";
  $AgenciesListTable->display(); 
  echo '</form></div>'; 
}
my_render_list_page();
