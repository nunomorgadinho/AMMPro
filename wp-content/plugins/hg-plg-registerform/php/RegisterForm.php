<?php
/**
 * 
 * This is a class that creates a portfolio from posts in a category.
 * Designed for WordPress plugin "Category Portfolio" (hg-plg-cat-portfolio). 
 * @author Herberto Graça - Freelance Web Developer (herberto.graca@gmail.com)
 * @version 1.0.0
 * @copyright 2011 All rights reserved.
 */

class RegisterForm  extends WP_Widget {
	
	const NAME = 'Register Form';
	//const SLUG = 'registerform';	
	const INSTALL_FOLDER_NAME = 'hg-plg-registerform';
	const TMPL_FILE_NAME = 'registerform-view.php';

	//private $params = false;	//shortcode parameters as an array
	
	//============================================================
	//===== Constructors
	//============================================================
		
	/**
	 * Gets the shortcode parameters and initializes internal vars
	 * @author Herberto Graça
	 * @param array $atts The atributes in the shortcode
	 * @param string $content
	 */
	
	public function __construct($atts=false) {
		parent::WP_Widget( false, self::NAME );
		//if ($atts && !$this->params) $this->init($atts);	
	}
	

	/**
	 * @author Herberto Graça
	 */
	/*
	public function RegisterForm() {
		parent::WP_Widget( false, self::NAME );	
	}
	*/
	
	/** 
	 * Gets the shortcode parameters, initializes internal vars and displays data
	 * @author Herberto Graça
	 * @param array $atts The atributes in the shortcode
	 * @param string $content
	 */
	/*
	static public function registerFormDisplay($atts=false) {
		$regForm = new RegisterForm($atts);
		return $regForm->display();
	}
	*/
	/**
	 * Initializes the internal vars with the values from shortcode tags.  
	 * @author Herberto Graça
	 * @param array $atts The atributes in the shortcode
	 */	
	/*
	private function init($atts){
		//$atts = shortcode_atts(array( 'tag' => -1, 'numposts' => 5 ), $atts);
		$this->params=$atts;		
	}
	*/
	
	//============================================================
	//===== Main methods
	//============================================================
	
	/**
	 * Outputs the list of posts
	 * @author Herberto Graça
	 */
	/*
	public function display(){	
		
		//$args='tag='.$this->params['tag'];
		
		// The Query
		//$query = new WP_Query( $args );
		
		// The Loop
		ob_start();
		include( ABSPATH.'wp-content/plugins/'.self::INSTALL_FOLDER_NAME.'/views/'.self::TMPL_FILE_NAME );
		$out = ob_get_contents();
		ob_end_clean();
		
		// Reset Post Data
		//wp_reset_postdata();
		
		return $out;
	}
	*/
	/**
	 * Outputs the content of the widget.
	 *
	 * @args			The array of form elements
	 * @instance
	 */
	function widget( $args, $instance ) {
	
		require_once(ABSPATH . WPINC . '/registration.php');  
	    global $wpdb, $user_ID;  
		extract( $args, EXTR_SKIP );
	    
	    if (!$user_ID) {
	    	
			// Before widget (defined by themes). 
			echo $args['before_widget'];
			
		    // Display the widget
			include( ABSPATH.'wp-content/plugins/'.self::INSTALL_FOLDER_NAME.'/views/'.self::TMPL_FILE_NAME );

			// After widget (defined by themes).
			echo $args['after_widget'];
				
	    }
	    else {
	    	wp_redirect( home_url() ); exit;
	    }
	}

	function install () {
		global $wpdb;

		$table_name = $wpdb->prefix . "users_ecomerce_data";

		global $wpdb;
		$sql = "CREATE TABLE $table_name (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  name tinytext NOT NULL,
		  text text NOT NULL,
		  url VARCHAR(55) DEFAULT '' NOT NULL,
		  UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}



















































