<?php
/**
 * Plugin Name: HG Register Form
 * Version: 0.1.0
 * Author: Herberto Graça
 * Description: A plugin that shows a form to register users in a e-shop
 * License: 2012 All rights reserved.
 */
require_once plugin_dir_path(__FILE__).'php/RegisterForm.php';

//shortcode
add_shortcode( 'registerform', array('RegisterForm', 'registerFormDisplay') );

//actions
add_action('widgets_init', create_function('', 'return register_widget("RegisterForm");'));

//Initialize the admin panel
