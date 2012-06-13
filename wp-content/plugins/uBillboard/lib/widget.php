<?php

class UDS_Billboard_Widget extends WP_Widget {
	private $billboards;
	
	function __construct()
	{
		parent::WP_Widget('uds-billboard-widget', 'uBillboard', array('description' => __('uBillboard Widget', uds_billboard_textdomain)));
		$this->billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	}
	
	function form($instance)
	{
		if($instance) {
			$ubb = esc_attr($instance['ubb_name']);
		} else {
			$ubb = '';
		}
	
		echo '<p>';
		echo '<label for="'.$this->get_field_id('ubb_name').'">'.__('Name', uds_billboard_textdomain). '</label>';
		echo '<select class="widefat" id="'.$this->get_field_id('ubb_name').'" name="'.$this->get_field_name('ubb_name').'">';
		echo '<option value="">-</option>';
		foreach($this->billboards as $name => $billboard) {
			if($name == '_uds_temp_billboard') continue;
			
			echo '<option value="'.$name.'" '.selected($name, $ubb).'>'.$name.'</option>';
		}
		echo '</select>';
		echo '</p>';
	}
	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['ubb_name'] = $new_instance['ubb_name'];
		return $instance;
	}
	
	function widget($args, $instance)
	{
		extract($instance);
		the_uds_billboard($ubb_name);
	}

}

add_action('widgets_init', create_function('', 'register_widget("UDS_Billboard_Widget");'));