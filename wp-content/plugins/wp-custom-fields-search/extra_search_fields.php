<?php
/*
 * Copyright 2009 Don Benjamin
 * Licensed under the Apache License, Version 2.0 (the "License"); 
 * you may not use this file except in compliance with the License. 
 * You may obtain a copy of the License at 
 *
 * 	http://www.apache.org/licenses/LICENSE-2.0 
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, 
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
 * See the License for the specific language governing permissions and 
 * limitations under the License.
 */

/*
 *  This library is required for the configurable search plugin.
 *
 *  It could also be used to make static unconfigurable search plugins.
 *
 * Author: Don Benjamin
 * Author URI: http://don-benjamin.co.uk
 * */

$debugMode =false;

if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( !defined('WP_CONTENT_DIR') )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );

class ParameterisedObject {
	var $params=array();
	function ParameterisedObject($params=array()){
		$this->__construct($params);
	}
	function __construct($params=array()){
		$this->setParams($params);
		if(!is_array($this->params)){
			foreach(debug_backtrace() as $trace){
				extract($trace);
				echo "<li>$file:$line $class.$function</li>";
			}
			die("<h1>".get_class($this)."</h1>");
		}
	}

	function setParams($params){
		$this->params=$params;
	}

	function param($key,$default=null){
		if(!array_key_exists($key,$this->params)) return $default;
		return $this->params[$key];
	}
}
class DB_WP_Widget extends ParameterisedObject {
	function DB_WP_Widget($name,$params=array()){
		DB_WP_Widget::__construct($name,$params);
	}
	function __construct($name,$params=array()){
		parent::__construct($params);
		$this->name = $name;
		$this->id = strtolower(get_class($this));
		$options = get_option($this->id);


//		register_sidebar_widget($this->name,array(&$this,'renderWidget'));
		$doesOwnConfig = $this->param('doesOwnConfig',false);
		$desc = $this->param('description',$this->name);
		$widget_ops = array('classname' => $this->id, 'description' => __($desc));
		$control_ops = array('width' => 400, 'height' => 350, 'id_base' => $this->id);
		$name = $this->name;
	
		$id = false;
		do {
			if($options)
			foreach ( array_keys($options) as $o ) {
				// Old widgets can have null values for some reason
				if ( !isset($options[$o]['exists']) )
					continue;
				$id = "$this->id-".abs($o); // Never never never translate an id
				wp_register_sidebar_widget($id, $name, array(&$this,'renderWidget'), $widget_ops, array( 'number' => $o ));
				wp_register_widget_control($id, $name, array(&$this,'configForm'), $control_ops, array( 'number' => $o ));
			}
			$options = array( -1=>array('exists'=>1));
		} while(!$id);
	}

	function setParams($params){
		parent::setParams($this->overrideParams($params));
	}

	function getDefaults(){
		return array('doesOwnConfig'=>false);
	}
	function overrideParams($params){
		foreach($this->getDefaults() as $k=>$v){
			if(!array_key_exists($k,$params)) $params[$k] = $v;
		}
		return $params;
	}

	function renderWidget(){
		echo "<h1>Unconfigured Widget</h1>";
	}

	function defaultWidgetConfig(){
		return array('exists'=>'1');
	}
	function getConfig($id=null,$key=null){
		$options = get_option($this->id);
		if(is_null($id)) return $options;
		if(!@array_key_exists($id,$options))
			$id = preg_replace('/^.*-(\d+)$/','\\1',$id);
		if(is_null($key))
			return $options[$id];
		else 
			return $options[$id][$key];
	}
	function configForm($args,$force=false){
		static $first;
		global $wp_registered_widgets;

		if ( !is_array($args) )
			$args = array( 'number' => $args );

		$args = wp_parse_args($args,array('number'=>-1));
		static $updated = array();

		$options = get_option($this->id);

		if(!$updated[$this->id] && ($_POST['sidebar'] || $force)){
			$updated[$this->id]=true;
			$sidebar = (string) $_POST['sidebar'];
			$default_options=$this->defaultWidgetConfig();

			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset($sidebars_widgets[$sidebar]) )
				$this_sidebar = $sidebars_widgets[$sidebar];
			else
				$this_sidebar = array();

			foreach ( $this_sidebar as $_widget_id ) {
				$callback = $wp_registered_widgets[$_widget_id]['callback'];
			       if(is_array($callback) && get_class($callback[0])==get_class($this) && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {{
				       $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
			       }
				if ( !in_array( "$this->id-$widget_number", $_POST['widget-id'] ) ) 
					unset($options[$widget_number]);
				}
			}
			foreach ((array)$_POST[$this->id] as $widget_number => $posted) {
				if(!isset($posted['exists']) && isset($options[$widget_number]))continue;

				$widgetOptions = $this->form_processPost($posted,$options[$widget_number]);
				$options[$widget_number] = $widgetOptions;
			}
			update_option($this->id,$options);
		}
		global $mycount;
		if(-1==$args['number']){
			$args['number']='%i%';
			$values = $default_options;
		} else {
			$values = $options[$args['number']];
		}
		$this->form_outputForm($values,$this->id.'['.$args['number'].']');
	}
	function form_processPost($post,$old){
		return array('exists'=>1);
	}
	function form_outputForm($old,$pref){
		$this->form_existsInput($pref);
	}
	function form_existsInput($pref){
		echo "<input type='hidden' name='".$pref."[exists]' value='1'/>";
	}

	function nameAsId(){
		return strtolower(str_replace(" ","_",$this->name));
	}
}

class DB_Search_Widget extends DB_WP_Widget {
	var $inputs = array();

	function DB_Search_Widget($name){
		DB_Search_Widget::__construct($name);
	}
	function __construct($name='Custom',$params=array()){
		$this->loadTranslations();
		parent::__construct(sprintf(__('%1$s Search','wp-custom-fields-search'),$name),$params);
		add_filter('posts_join',array(&$this,'join_meta'));
		add_filter('posts_where',array(&$this,'sql_restrict'));
		add_filter('posts_groupby', array(&$this,'sql_group'));
	//	add_filter('posts_orderby', array(&$this,'sql_order'));
		add_filter('home_template',array(&$this,'rewriteHome'));
		add_filter('page_template',array(&$this,'rewriteHome'));
		add_filter( 'get_search_query', array(&$this,'getSearchDescription'));
		add_action('wp_head', array(&$this,'outputStylesheets'), 1);
	}
	function loadTranslations(){
		static $loaded;
		if ( !$loaded && function_exists('load_plugin_textdomain') ) {
			$loaded=true;
			if ( !defined('WP_PLUGIN_DIR') ) {
				load_plugin_textdomain('wp-custom-fields-search', str_replace( ABSPATH, '', dirname(__FILE__) ) );
			} else {
				load_plugin_textdomain('wp-custom-fields-search', false, dirname( plugin_basename(__FILE__) ) );
			}
		}
	}
	function addInput($input){
		$this->inputs[] = $input;
	}
	function outputStylesheets(){
		$dir = WP_CONTENT_URL .'/plugins/' .  dirname(plugin_basename(__FILE__) ) . '/';
		echo "\n".'<style type="text/css" media="screen">@import "'. $dir .'templates/searchforms.css";</style>'."\n";
	}

	function getInputs($params){
		return $this->inputs;
	}

	function getTitle(){
		return $this->param('description',$this->name);
	}

	function renderWidget($params=array(),$p2 = array()){
		$title = $this->getTitle($params);
		$inputs = $this->getInputs($params);
		$hidden = "<input type='hidden' name='search_class' value='".$this->getPostIdentifier()."'/><input type='hidden' name='widget_number' value='".$p2['number']."'/>";
		$formCssClass = 'custom_search widget custom_search_'.$this->nameAsId();
		$formAction = get_option('home');
		if(function_exists('locate_template'))
			$formTemplate = locate_template(array('wp-custom-fields-search-form.php'));
		if(!$formTemplate) $formTemplate = dirname(__FILE__).'/templates/wp-custom-fields-search-form.php';

		foreach($inputs as $k=>$v){
			if($v->isHidden()){
				$hidden.=$v->getInput(false);
				unset($inputs[$k]);
			}
		}
		include($formTemplate);
	}

	function isPosted(){
		return $_GET['search_class'] == $this->getPostIdentifier();
	}
	function getPostIdentifier(){
		return get_class($this).'-'.$this->id;
	}
	function isHome($isHome){
		return $isHome && !$this->isPosted();
	}
	function rewriteHome($homeTemplate){
		if($this->isPosted()) return get_query_template('search');
		return $homeTemplate;
	}

	function join_meta($join){
		if($this->isPosted()){
			$desc = array();
			foreach($this->getInputs($_REQUEST['widget_number']) as $input){
				$join = $input->join_meta($join);
				$desc = $input->describeSearch($desc);
			}
			if($desc){
				$desc = join(__(" and ",'wp-custom-fields-search'),$desc);
			} else {
				$desc = __("All Entries",'wp-custom-fields-search');
			}
			$this->desc=$desc;
		}
		return $join;
	}

	function getSearchDescription($desc){
		if($this->isPosted()) return $this->desc;
		return $desc;
	}
	function sql_restrict($where){
		if($this->isPosted()){
			global $wpdb;
			/** This could possibly be considered invasive, need to think this bit through
			 * properly.
			 */
			$where = preg_replace("_AND\s*\(ID\s*=\s*'\d+'\)_","",$where);
			$where = preg_replace("/AND $wpdb->posts.post_type = '(post|page|dealentry|businessentry)'/","",$where);
			if ($_GET['widget_number'] == 'preset-1')
				$where.= " AND ($wpdb->posts.post_type='businessentry') ";
			else
				$where.= " AND ($wpdb->posts.post_type='dealentry') ";
			foreach($this->getInputs($_REQUEST['widget_number']) as $input){
				$where = $input->sql_restrict($where);
			}
		}
		return $where;
	}
	function sql_group($group){
		if($this->isPosted()){
			global $wpdb;
			$group = "$wpdb->posts.ID";
		}
		return $group;
	}
	/*
function sql_order($order){
		if($this->isPosted()){
			global $wpdb;
			//$order = "TEST";
			$order = "meta0.meta_value AND (meta0.meta_key='price_sold') DESC";
		}
		return $order;
	}*/

	function toSearchString(){
	}
}


class SearchFieldBase {
	function SearchFieldBase(){
		SearchFieldBase::__construct();
	}
	function __construct(){
		add_filter('search_params',array(&$this,'form_inputs'));
		static $index;
		$this->index = ++$index;
	}
	function form_inputs($form){
		die("Unimplemented function ".__CLASS__.".".__FUNCTION__);
	}
	function sql_restrict($where){
		die("Unimplemented function ".__CLASS__.".".__FUNCTION__);
	}
}

class Field extends ParameterisedObject {
	function getValue($name){
		$v =  $_REQUEST[$this->getHTMLName($name)];
		if(get_magic_quotes_gpc()) $v= stripslashes($v);
		return $v;
	}

	function getHTMLName($name){
		return 'cs-'.str_replace(" ","_",$name);
	}

	function getInput($name){
		$htmlName = $this->getHTMLName($name);
		$value = $this->getValue($name);
		return "<input name='$htmlName' value='$value'/>";
	}
	function getCSSClass(){
		return get_class($this);
	}
}
class TextField extends Field {
}
class TextInput extends TextField{}
class DropDownField extends Field {
	function DropDownField($params=array()){
		$this->__construct($params);
	}
	function __construct($params = array()){
		parent::__construct($params);
		if($optionString = $this->param('dropdownoptions',false)){
			$options=array();
			$optionPairs = explode(',',$optionString);
			foreach($optionPairs as $option){
				list($k,$v) = explode(':',$option);
				if(!$v) $v=$k;
				$options[$k]=$v;
			}
		} else {
			$options = $this->param('options',array());
		}
		$this->options = $options;
	}

	function getOptions($joiner,$name){
		if($this->param('fromDb',!$this->options)){
			$options = array(''=>__('ANY','wp-custom-fields-search'));
			$auto = $joiner->getAllOptions($name);
			asort($auto);
			$options +=$auto;
			return $options;
		} else {
			return $this->options;
		}
	}
	function getInput($name,$joiner,$fieldName=null){
		if(!$fieldName) $fieldName=$name;
		$v = $this->getValue($name);
		$id = $this->getHTMLName($name);

		$options = '';
		foreach($this->getOptions($joiner,$fieldName) as $option=>$label){
			$checked = ($option==$v)?" selected='true'":"";
			$option = htmlspecialchars($option,ENT_QUOTES);
			$label = htmlspecialchars($label,ENT_QUOTES);
			$options.="<option value='$option'$checked>$label</option>";
		}
		$atts = '';
		if($this->params['onChange']) $atts = ' onChange="'.htmlspecialchars($this->params['onChange']).'"';
		if($this->params['id']) $atts .= ' id="'.htmlspecialchars($this->params['id']).'"';
		if($this->params['css_class']) $atts .= ' class="'.htmlspecialchars($this->params['css_class']).'"';
		return "<select name='$id'$atts>$options</select>";
	}
	function getConfigForm($id,$values){
		return "<label for='$id-dropdown-options'>".__('Drop Down Options','wp-custom-fields-search')."</label><input id='$id-dropdown-options' name='$id"."[dropdownoptions]' value='$values[dropdownoptions]'/>";
	}
}
class HiddenField extends Field {
	function HiddenField(){
		$func_args = func_get_args();
		call_user_func_array(array($this,'__construct'),$func_args);
	}
	function __construct($params = array()){
		$params['hidden']=true;
		parent::__construct($params);
	}
	function getValue(){
		return $this->param('constant-value',null);
	}

	function getInput($name){
		$v=$this->getValue($name);
		$id = $this->getHTMLName($name);
		return "<input type='hidden' name='".htmlspecialchars($name)."' value='".htmlspecialchars($v)."'/>";
	}
	function getConfigForm($id,$values){
		return "<label for='$id-constant-value'>".__('Constant Value','wp-custom-fields-search')."</label><input id='$id-constant-value' name='$id"."[constant-value]' value='{$values['constant-value']}'/>";
	}
}

/* TODO: Add Caching */
class CustomFieldReader {

}

class DropDownFromValues extends DropDownField {
	function DropDownFromValues($params=array()){
		$this->__construct($params);
	}

	function __construct($params=array()){
		$params['fromDb'] = true;
		parent::__construct(array(),$params);
	}

	function getConfigForm($id,$values){
		return "";
	}
}
class RadioButtonField extends Field {
	function RadioButtonField($options=array(),$params=array()){
		RadioButtonField::__construct($options,$params);
	}
	function __construct($params=array()){
		parent::__construct($params);
		if($params['radiobuttonoptions']){
			$options=array();
			$optionPairs = explode(',',$params['radiobuttonoptions']);
			foreach($optionPairs as $option){
				list($k,$v) = explode(':',$option);
				if(!$v) $v=$k;
				$options[$k]=$v;
			}
		}
		$this->options = $options;
	}
	function getOptions($joiner,$name){
		if($this->param('fromDb',!$this->options)){
			return $joiner->getAllOptions($name);
		} else {
			return $this->options;
		}
	}
	function getInput($name,$joiner,$fieldName=null){
		if(!$fieldName) $fieldName=$name;
		$v = $this->getValue($name);
		$id = $this->getHTMLName($name);

		$options = '';
		foreach($this->getOptions($joiner,$fieldName) as $option=>$label){
			$option = htmlspecialchars($option,ENT_QUOTES);
			$label = htmlspecialchars($label,ENT_QUOTES);
			$checked = ($option==$v)?" checked='true'":"";
			$htmlId = "$id-$option";

			$options.="<div class='radio-button-wrapper'><input type='radio' name='$id' id='$htmlId' value='$option'$checked> <label for='$htmlId'>$label</label></div>";
		}
		return $options;
	}
	function getCSSClass(){
		return "RadioButton";
	}
	function getConfigForm($id,$values){
		return "<label for='$id-radiobutton-options'>Radio Button Options</label><input id='$id-radiobutton-options' name='$id"."[radiobuttonoptions]' value='$values[radiobuttonoptions]'/>";
	}
}
class RadioButtonFromValues extends RadioButtonField {
	function RadioButtonFromValues($fieldName=null){
		RadioButtonFromValues::__construct($fieldName);
	}

	function __construct($fieldName=null,$params){
		$params['fromDb'] = true;
		parent::__construct($options,$params);
	}
	function getConfigForm($id,$values){
		return "";
	}
}

class Comparison {
	function addSQLWhere($field,$value){
		die("Unimplemented function ".__CLASS__.".".__FUNCTION__);
	}
	function describeSearch($value){
		die("Unimplemented function ".__CLASS__.".".__FUNCTION__);
	}
}
class EqualComparison extends Comparison {
	function addSQLWhere($field,$value){
		return "$field = '$value'";
	}
	function describeSearch($value){
		return sprintf(__(' is "%1$s"','wp-custom-fields-search'),$value);
	}
}
class LikeComparison extends Comparison{
	function addSQLWhere($field,$value){
		return $this->getLikeString($field,$value);
	}
	function getLikeString($field,$value){
		return "$field LIKE '%$value%'";
	}
	function describeSearch($value){
		return sprintf(__(' contains "%1$s"','wp-custom-fields-search'),$value);
	}
}

class WordsLikeComparison extends LikeComparison {
	function addSQLWhere($field,$value){
		$words = explode(" ",$value);
		$like = array(1);
		foreach($words as $word){
			$like[] = $this->getLikeString($field,$word);
		}
		return "(".join(" AND ",$like).")";
	}
	function describeSearch($value){
		return sprintf(__(' contains "%1$s"','wp-custom-fields-search'),join('"'.__(" and ",'wp-custom-fields-search').'"',explode(" ",$value)));
	}
}
class LessThanComparison extends Comparison{
	function addSQLWhere($field,$value){
		return "$field < '$value'";
	}
	function describeSearch($value){
		return sprintf(__(' less than "%1$s"','wp-custom-fields-search'),$value);
	}
}
class AtMostComparison extends Comparison{
	function addSQLWhere($field,$value){
		return "$field <= '$value'";
	}
	function describeSearch($value){
		return sprintf(__(' at most "%1$s"','wp-custom-fields-search'),$value);
	}
}
class AtLeastComparison extends Comparison{
	function addSQLWhere($field,$value){
		return "$field >= '$value'";
	}
	function describeSearch($value){
		return sprintf(__(' at least "%1$s"','wp-custom-fields-search'),$value);
	}
}
class MoreThanComparison extends Comparison{
	function addSQLWhere($field,$value){
		return "$field > '$value'";
	}
	function describeSearch($value){
		return sprintf(__(' more than "%1$s"','wp-custom-fields-search'),$value);
	}
}
class RangeComparison extends Comparison{
	function addSQLWhere($field,$value){
		list($min,$max) = explode("-",$value);
		$where=1;
		if(strlen($min)>0) $where.=" AND $field >= $min";
		if(strlen($max)>0) $where.=" AND $field <= $max";
		return $where;
	}
	function describeSearch($value){
		list($min,$max) = explode("-",$value);
		if(strlen($min)==0) return sprintf(__(' less than "%1$s"','wp-custom-fields-search'),$max);
		if(strlen($max)==0) return sprintf(__(' more than "%1$s"','wp-custom-fields-search'),$min);
		return sprintf(__(' between "%1$s" and "%2$s"','wp-custom-fields-search'),$min,$max);
	}
}
class NotEqualComparison extends Comparison {
	function addSQLWhere($field,$value){
		return "$field != '$value'";
	}
	function describeSearch($value){
		return sprintf(__(' is not "%1$s"','wp-custom-fields-search'),$value);
	}
}

class BaseJoiner extends ParameterisedObject {
	function BaseJoiner($name=null,$params=array()){
		$this->__construct($name,$params);
	}
	function __construct($name=null,$params=array()){
		parent::__construct($params);
		$this->name=$name;
	}
	function sql_join($join,$name,$index,$value){
		return $join;
	}
	function process_where($where){
		return $where;
	}
	function needsField(){
		return true;
	}
}
class CustomFieldJoiner extends BaseJoiner{
	function CustomFieldJoiner($name,$params){
		$this->__construct($name,$params);
	}
	function __construct($name,$params){
		$this->params = $params;
	}
	function param($key,$default=null){
		if(array_key_exists($key,$this->params)) return $this->params[$key];
		return $default;
	}
	function sql_restrict($name,$index,$value,$comparison){
		$table = 'meta'.$index;
		$field = "$table.meta_value".($this->param('numeric',false)?'*1':'');
		$comp = " AND ".$comparison->addSQLWhere($field,$value);
		if($name!='all')
			$comp = " AND ( $table.meta_key='$name' ".$comp.") "; 
		return $comp;

	}
	function sql_join($join,$name,$index,$value){
		if(!$value && !$this->param('required',false)) return $join;
		global $wpdb;
		$table = 'meta'.$index;
		return "$join JOIN $wpdb->postmeta $table ON $table.post_id=$wpdb->posts.id";
	}
	function getAllOptions($fieldName){
		global $wpdb;
		$where='';
		if($fieldName!='all')
			$where = " WHERE meta_key='$fieldName'";
		$q = mysql_query($sql = "SELECT DISTINCT meta_value FROM $wpdb->postmeta m JOIN $wpdb->posts p ON m.post_id=p.id AND p.post_status='publish' $where");
		$options = array();
		while($r = mysql_fetch_row($q))
			$options[$r[0]] = $r[0];
		return $options;
	}
	function getSuggestedFields(){
		global $wpdb;
		$q = mysql_query($sql = "SELECT DISTINCT meta_key FROM $wpdb->postmeta WHERE meta_key NOT LIKE '\\_%'");
		$options = array('all'=>'All Fields');
		while($r = mysql_fetch_row($q))
			$options[$r[0]] = $r[0];
		return $options;
	}
}
class CategoryJoiner extends BaseJoiner {
	function sql_restrict($name,$index,$value,$comparison){
		if(!($value || $this->params['required'])) return $join;
		$table = 'meta'.$index;
		return " AND ( ".$comparison->addSQLWhere("$table.name",$value).")";
	}
	function getTaxonomy(){
		if ($_GET['widget_number'] == 'preset-1')
			return $this->param('taxonomy','business_category'); // switch category to gallery - customizing plugin to work for art market monitor
		return $this->param('taxonomy','gallery');
	}
	function getTaxonomyWhere($table){
		return "`$table`.taxonomy='".$this->getTaxonomy()."'";
	}
	function sql_join($join,$name,$index,$value){
		if(!($value || $this->params['required'])) return $join;
		global $wpdb;
		$table = 'meta'.$index;
		$rel = 'rel'.$index;
		$tax = 'tax'.$index;
		return $join." JOIN $wpdb->term_relationships $rel ON $rel.object_id=$wpdb->posts.id JOIN  $wpdb->term_taxonomy $tax ON $tax.term_taxonomy_id=$rel.term_taxonomy_id JOIN $wpdb->terms $table ON $table.term_id=$tax.term_id AND ".$this->getTaxonomyWhere($tax);
	}
	function getAllOptions($fieldName){
		global $wpdb;
		$sql = "SELECT distinct t.name FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id INNER JOIN $wpdb->term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id JOIN $wpdb->posts p ON tr.object_id=p.id AND p.post_status='publish' WHERE ".$this->getTaxonomyWhere('tt');
		$q = mysql_query($sql);
		if($e = mysql_error()) echo "<h1>SQL: $sql</h1>".mysql_error();
		$options = array();
		while($r = mysql_fetch_row($q))
			$options[$r[0]] = $r[0];
		return $options;
	}
	function needsField(){
		return false;
	}
}
class TagJoiner extends CategoryJoiner {
	function getTaxonomy(){
		return $this->param('taxonomy','post_tag');
	}
}

class PostTypeJoiner extends BaseJoiner {
	function process_where($where){
		global $wpdb;
		$where = preg_replace("/AND \($wpdb->posts.post_type *= *'(post|page|dealentry|businessentry)'\)/","",$where);
		return $where;
	}
	function sql_restrict($name,$index,$value,$comparison){
		global $wpdb;
		if(!($value || $this->params['required'])) return $join;
		return " AND ( ".$comparison->addSQLWhere("$wpdb->posts.post_type",$value).")";
	}
	function getAllOptions($fieldName){
		global $wpdb;
		$q = mysql_query($sql = "SELECT distinct post_type FROM $wpdb->posts p WHERE post_status='publish' ");
		$options = array();
		while($r = mysql_fetch_row($q))
			$options[$r[0]] = $r[0];
		return $options;
	}
	function needsField(){
		return false;
	}
}

class PostDataJoiner extends BaseJoiner {
	function sql_restrict($name,$index,$value,$comparison){
		global $wpdb;
		$table = $wpdb->posts;
		if($name=='all'){
			$logic = array();
			foreach($this->getSuggestedFields() as $name=>$desc){
				if($name=='all') continue;
				$logic[] =  "( ".$comparison->addSQLWhere("$table.$name",$value).") ";
			}
			$logic = " AND (".join(" OR ",$logic).")";
			return $logic;
		} else {
			return " AND ( ".$comparison->addSQLWhere("$table.$name",$value).") ";
		}
	}
	function sql_join($join,$name,$index,$value){
		return $join;
	}
	function getAllOptions($fieldName){
		global $wpdb;
		$q = mysql_query($sql = "SELECT $fieldName FROM $wpdb->posts");
		$options = array();
		while($r = mysql_fetch_row($q))
			$options[$r[0]] = $r[0];
		return $options;
	}
	function getSuggestedFields(){
		return array(
			'all'=>__('All Fields','wp-custom-fields-search'),
			'post_content'=>__('Body Text','wp-custom-fields-search'),
			'post_title'=>__('Title','wp-custom-fields-search'),
			'post_author'=>__('Author','wp-custom-fields-search'),
			'post_date'=>__('Date','wp-custom-fields-search'),
		);
	}
}

class CategorySearch {
}

class CustomSearchField extends SearchFieldBase {
	function CustomSearchField($nameOrParams,$input=false,$comparison=false,$joiner=false){
		CustomSearchField::__construct($nameOrParams,$input,$comparison,$joiner);
	}
	function __construct($nameOrParams,$input=false,$comparison=false,$joiner=false){
		parent::__construct();
		if(!is_array($nameOrParams)){
			$params = array('name'=>$nameOrParams);
		} else {
			$params = $nameOrParams;
		}
		$this->name = $params['name'];
		$this->params = $params;

		$this->joiner = $joiner;
		$this->comparison = $comparison;
		$this->input = $input;

		if(!is_object($this->input)){
			$input = $this->param('input','TextField');
			$this->input = new $input($params);
		}
		if(!is_object($this->comparison)){
			$comparison = $this->param('comparison','LikeComparison');
			$this->comparison = new $comparison();
		}
		if(!is_object($this->joiner)){
			$joiner = $this->param('joiner','CustomFieldJoiner');
			$this->joiner = new $joiner($this->param('name'),$this->params);
		}


	}
	function setIndex($n){
		$this->index=$n;
	}
	function param($key,$default=null){
		if(array_key_exists($key,$this->params)) return $this->params[$key];
		return $default;
	}

	function stripInitialForm($form){
		$pref='<!--cs-form-->';
		if(preg_match("/^$pref/",$form)) return $form;
		else return $pref;
	}

	function form_inputs($form){
		$form = $this->stripInitialForm($form);
		return $form.$this->getInput($this->name,$this->joiner);
	}
	function hasValue(){
		return $this->getValue();
	}
	function sql_restrict($where){
		if($this->hasValue()){
			$value = $this->getValue();
			$value = $GLOBALS['wpdb']->escape($value);
			$where.=$this->joiner->sql_restrict($this->name,$this->index,$value,$this->comparison);
		}
		if(method_exists($this->joiner,'process_where'))
			$where = $this->joiner->process_where($where); 
		return $where;
	}
	function describeSearch($current){
		if($this->hasValue()){
			$current[] = $this->getLabel()." ".$this->comparison->describeSearch($this->getValue());
		}
		return $current;

	}
	function join_meta($join){
		global $wpdb;
		$join=$this->joiner->sql_join($join,$this->name,$this->index,$this->getValue(),$this->comparison);
		return $join;
	}

	function getQualifiedName(){
		return $this->name.'-'.$this->index;
	}
	function getOldValue(){ return $this->getValue(); }
	function getValue(){
		$v = $this->input->getValue($this->getQualifiedName(),$this->name);
		return $v;
	}
	function getLabel(){
		if(!$this->params['label']) $this->params['label'] = ucwords($this->name);
		return $this->params['label'];
	}

	function isHidden(){
		return $this->input->param('hidden',false);
	}
	function getInput($wrap=true){
		$input = $this->input->getInput($this->getQualifiedName(),$this->joiner,$this->name);
		if($wrap){
			$input = "<div class='searchform-param'><label class='searchform-label'>".$this->getLabel()."</label><span class='searchform-input-wrapper'>$input</span></div>";
		}
		return $input;
	}
	function getCSSClass(){
		return method_exists($this->input,'getCSSClass')?$this->input->getCSSClass():get_class($this->input);
	}
}

function wp_custom_search_fields_include_bridges(){
	$dir = opendir($path = dirname(__FILE__).'/bridges');
	while($file = readdir($dir)){
		if(is_file("$path/$file") && preg_match("/^[^.].*\.php$/",$file)){
			require_once("$path/$file");
		}
	}
}
wp_custom_search_fields_include_bridges();

if($debugMode){
	add_filter('posts_request','debug_dump_query');
	function debug_dump_query($query){
		echo "<h1>$query</h1>";
		return $query;
	}
}
?>
