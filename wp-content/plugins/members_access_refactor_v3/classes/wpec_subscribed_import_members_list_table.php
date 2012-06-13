<?php
/*************************** LOAD THE BASE CLASS *******************************
 *******************************************************************************
 * The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}



class Import_Members_List_Table extends WP_List_Table {
    
  
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'import_user',     //singular name of the listed records
            'plural'    => 'importer_users',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
/*
    as the bulk option on this page is not implemented intot his class yet the 
    function that handles the validation returns if validation failed need to 
    check for pre selected checkboxes from this failed validation
*/
    function column_cb($item){
    	$checked = '';
    	if (isset($_POST['import_user'])){
    		if ( in_array($item['ID'], $_POST['import_user']) )
    		$checked = 'checked';
    		else
    		$checked = '';
    	}
    	
    	return '<input type="checkbox" name="import_user[]" '.$checked .' value="'. $item['ID'] .'" />';
      
    }
    
    
 function column_default($item, $column_name){
	    $array_data = $item[$column_name];
		
		    $return = '';
		    foreach ( $array_data as $data )
		    	$return .= '<p>'.$data . '</p>';
		    
		    return $return;
    
    }
    
    /*
function column_subscription($item){
    	return $item['subscription'];
    }
*/
    
    function column_name($item){
  		
		return $item['name'];
		
    }   
    
    function column_username($item){
    	return $item['username'].get_avatar( $item['ID'], 32 );
    }

    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'username'     => 'Username',
            'name'    => 'Name',
            'subscription'  => 'Current Subscriptions',
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'username'     => array('user_login',true)     //true means its already sorted
        );
        return $sortable_columns;
    }
    
     
function prepare_items() {
        
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = 40;
        
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        
       
        $this->_column_headers = array($columns, $hidden, $sortable);
        $current_page = $this->get_pagenum();
        
        /* work out the query limits */
        $end_limit = $per_page;
        $start_limit = ( ($current_page - 1) * $per_page);
        

		/* set a default order currently we can only sort by username */
         $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'user_login'; //If no sort, default to title
         $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
       		
		global $wpdb;

        $query = "SELECT `ID`, `user_login` FROM " . $wpdb->users . " ORDER BY ".$orderby ." ". $order. " LIMIT ".$start_limit.",".$end_limit;
        
        $users = $wpdb->get_results($query);
        
        /* build a nice array of userdata */
        $data = array();
        $i = 0;
        
        foreach( $users as $user ){
        
	        $user_id = $user->ID;
	        $fname =  $user_last = get_user_meta( $user_id, 'first_name', true ); 
	        $lname =  $user_last = get_user_meta( $user_id, 'last_name', true ); 
	        
	        if ( $fname == '' && $lname == '')
	        	$name = 'Not provided';
	        else $name = $fname . " " . $lname;
	        	
	        
	        
	        $subscription_ends = get_user_meta( $user_id, '_subscription_ends', true ); 
			$subscriptions = '';
			if ( !empty($subscription_ends) )
	      		$subscriptions = array_keys($subscription_ends);
	       else
	       		$subscriptions[] = 'No Current Subscriptions';
	       
	       	$data[$i]['ID'] = $user_id;
	       	$data[$i]['username'] = $user->user_login;
	       	$data[$i]['name'] = $name;
	       	$data[$i]['subscription'] = $subscriptions;	
	        $i++;
        } 
        
        $query = "SELECT COUNT(`ID`) FROM " . $wpdb->users;
		$total_items = $wpdb->get_var($query);
        
       /* set the sorted data items for the table display */
        $this->items = $data;
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}