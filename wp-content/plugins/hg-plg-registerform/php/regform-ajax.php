<?php

if (!function_exists('add_action')){
	require_once("../../../../wp-config.php");
}

if($_POST){  
	
	//echo $_POST['email'].'-'.$_POST['name'].'-'.$_POST['cc-number'].'-'.$_POST['cc-exp-month'].'-'.$_POST['cc-exp-year'].'-'.$_POST['zipcode'].'-'.$_POST['address'];
	
	//We shall SQL escape all inputs
	/*
	$username = $wpdb->escape($_REQUEST['username']);
	if(emptyempty($username)) {
		echo "User name should not be empty.";
		exit();
	}
	*/
	$email = $wpdb->escape($_REQUEST['email']);
	if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/", $email)) {
		echo "1|Please enter a valid email.";
		exit();
	}

	$random_password = wp_generate_password( 12, false );
	$status = wp_create_user( $email, $random_password, $email );
	
	
	if ( is_wp_error($status) ){
		echo "1|Username already exists. Please try another one.";
	}
	else {
		$from = get_option('admin_email');
		$headers = 'From: '.$from . "\r\n";
		$subject = "Registration successful";
		$msg = "Registration successful.\nYour login details\nUsername: $email\nPassword: $random_password";
		wp_mail( $email, $subject, $msg, $headers );
		echo "0|Please check your email for login details.";
	}
}  

exit();