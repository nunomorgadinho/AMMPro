<?php
/*
Plugin Name: WP Custom Search
Plugin URI: http://www.don-benjamin.co.uk/wordpress-plugins/wp-custom-search
Description: Allows admin to build custom search form.  Allows the site admin to configure multiple html inputs for different fields including custom fields.  Also provides an extensible mechanism for integrating with other plugins data structures.
Version: 0.3.16
Author: Don Benjamin
Author URI: http://www.don-benjamin.co.uk/
Text Domain: wp-custom-fields-search
*/
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
	require_once(dirname(__FILE__).'/extra_search_fields.php');

	//Add Widget for configurable search.
	add_action('plugins_loaded',array('DB_CustomSearch_Widget','init'));


	class DB_CustomSearch_Widget extends DB_Search_Widget {
		function DB_CustomSearch_Widget($params=array()){
			DB_CustomSearch_Widget::__construct($params);
		}
		function __construct($params=array()){
			$this->loadTranslations();
			parent::__construct(__('Custom Fields ','wp-custom-fields-search'),$params);
			add_action('admin_print_scripts', array(&$this,'print_admin_scripts'), 90);
			add_action('admin_menu', array(&$this,'plugin_menu'), 90);
			add_filter('the_content', array(&$this,'process_tag'),9);
			add_shortcode( 'wp-custom-fields-search', array(&$this,'process_shortcode') );
			wp_enqueue_script('jquery');
			if(version_compare("2.7",$GLOBALS['wp_version'])>0) wp_enqueue_script('dimensions');
		}
		function init(){
			global $CustomSearchFieldStatic;
			$CustomSearchFieldStatic['Object'] = new DB_CustomSearch_Widget();
			$CustomSearchFieldStatic['Object']->ensureUpToDate();
		}

		function currentVersion(){
			return "0.3.16";
		}

		function ensureUpToDate(){
			$version = $this->getConfig('version');
			$latest = $this->currentVersion();
			if($version<$latest) $this->upgrade($version,$latest);
		}

		function upgrade($current,$target){
			$options = $this->getConfig();
			if(version_compare($current,"0.3")<0){
				$config = $this->getDefaultConfig();
				$config['name'] = __('Default Preset','wp-custom-fields-search');
				$options['preset-default'] = $config;
			}
			$options['version']=$target;
			update_option($this->id,$options);
		}

		function getInputs($params = false,$visitedPresets=array()){
			if(is_array($params)){
				$id = $params['widget_id'];
			} else {
				$id = $params;
			}
			if($visitedPresets[$id]) return array();
			$visitedPresets[$id]=true;
			
			global $CustomSearchFieldStatic;
			if(!$CustomSearchFieldStatic['Inputs'][$id]){
			
				$config = $this->getConfig($id);
				$inputs = array();
				if($config['preset']) $inputs = $this->getInputs($config['preset'],$visitedPresets);
				$nonFields = $this->getNonInputFields();
				if($config)
				foreach($config as $k=>$v){
					if(in_array($k,$nonFields)) continue;
					if(!(class_exists($v['input']) && class_exists($v['comparison']) && class_exists($v['joiner']))) {
						continue;
					}
					$inputs[] =  new CustomSearchField($v);

				}
				foreach($inputs as $k=>$v){
					$inputs[$k]->setIndex($k);
				}
				$CustomSearchFieldStatic['Inputs'][$id]=$inputs;
			}
			return $CustomSearchFieldStatic['Inputs'][$id];
		}
		function getTitle($params){
			$config = $this->getConfig($params['widget_id']);
			return $config['name'];
		}

		function form_processPost($post,$old){
			unset($post['###TEMPLATE_ID###']);
			if(!$post) $post=array('exists'=>1);
			return $post;
		}
		function getDefaultConfig(){
			return array('name'=>'Site Search', 
				1=>array(
					'label'=>__('Key Words','wp-custom-fields-search'),
					'input'=>'TextField',
					'comparison'=>'WordsLikeComparison',
					'joiner'=>'PostDataJoiner',
					'name'=>'all'
				),
				2=>array(
					'label'=>__('Category','wp-custom-fields-search'),
					'input'=>'DropDownField',
					'comparison'=>'EqualComparison',
					'joiner'=>'CategoryJoiner'
				),
			);
		}
		function form_outputForm($values,$pref){
			$defaults=$this->getDefaultConfig();
			$prefId = preg_replace('/^.*\[([^]]*)\]$/','\\1',$pref);
			$this->form_existsInput($pref);
			$rand = rand();
?>
	<div id='config-template-<?php echo $prefId?>' style='display: none;'>
	<?php 
		$templateDefaults = $defaults[1];
		$templateDefaults['label'] = 'Field ###TEMPLATE_ID###';
		echo  $this->singleFieldHTML($pref,'###TEMPLATE_ID###',$templateDefaults);
	?>
	</div>

<?php
			foreach($this->getClasses('input') as $class=>$desc) {
				if(class_exists($class))
					$form = new $class();
				else $form = false;
				if(compat_method_exists($form,'getConfigForm')){
					if($form = $form->getConfigForm($pref.'[###TEMPLATE_ID###]',array('name'=>'###TEMPLATE_NAME###'))){
?>
	<div id='config-input-templates-<?php echo $class?>-<?php echo $prefId?>' style='display: none;'>
		<?php echo $form?>
	</div>
		
<?php					}
				}
			}
 ?>
	<div id='config-form-<?php echo $prefId?>'>
<?php
			if(!$values) $values = $defaults;
			$maxId=0;
			$presets = $this->getPresets();
			array_unshift($presets,__('NONE','wp-custom-fields-search'));
?>
		<div class='searchform-name-wrapper'><label for='<?php echo $prefId?>[name]'><?php echo __('Search Title','wp-custom-fields-search')?></label><input type='text' class='form-title-input' id='<?php echo $prefId?>[name]' name='<?php echo $pref?>[name]' value='<?php echo $values['name']?>'/></div>
		<div class='searchform-preset-wrapper'><label for='<?php echo $prefId?>[preset]'><?php echo __('Use Preset','wp-custom-fields-search')?></label>
<?php
			$dd = new AdminDropDown($pref."[preset]",$values['preset'],$presets);
			echo $dd->getInput()."</div>";
			$nonFields = $this->getNonInputFields();
			foreach($values as $id => $val){
				$maxId = max($id,$maxId);
				if(in_array($id,$nonFields)) continue;
				echo "<div id='config-form-$prefId-$id'>".$this->singleFieldHTML($pref,$id,$val)."</div>";
			}
?>
	</div>

	<br/><a href='#' onClick="return CustomSearch.get('<?php echo $prefId?>').add();"><?php echo __('Add Field','wp-custom-fields-search')?></a>
	<script type='text/javascript'>
			CustomSearch.create('<?php echo $prefId?>','<?php echo $maxId?>');
<?php
	foreach($this->getClasses('joiner') as $joinerClass=>$desc){
		if(compat_method_exists($joinerClass,'getSuggestedFields')){
			$options = eval("return $joinerClass::getSuggestedFields();");
			$str = '';
			foreach($options as $i=>$v){
				$k=$i;
				if(is_numeric($k)) $k=$v;
				$options[$i] = json_encode(array('id'=>$k,'name'=>$v));
			}
			$str = '['.join(',',$options).']';
			echo "CustomSearch.setOptionsFor('$joinerClass',".$str.");\n";
		}elseif(eval("return $joinerClass::needsField();")){
			echo "CustomSearch.setOptionsFor('$joinerClass',[]);\n";
		}
	}
?>
	</script>
<?php
		}

		function getNonInputFields(){
			return array('exists','name','preset','version');
		}
		function singleFieldHTML($pref,$id,$values){
			$prefId = preg_replace('/^.*\[([^]]*)\]$/','\\1',$pref);
			$pref = $pref."[$id]";
			$htmlId = $pref."[exists]";
			$output = "<input type='hidden' name='$htmlId' value='1'/>";
			$titles="<th>".__('Label','wp-custom-fields-search')."</th>";
			$inputs="<td><input type='text' name='$pref"."[label]' value='$values[label]' class='form-field-title'/></td><td><a href='#' onClick='return CustomSearch.get(\"$prefId\").toggleOptions(\"$id\");'>".__('Show/Hide Config','wp-custom-fields-search')."</a></td>";
			$output.="<table class='form-field-table'><tr>$titles</tr><tr>$inputs</tr></table>";
			$output.="<div id='form-field-advancedoptions-$prefId-$id' style='display: none'>";
			$inputs='';$titles='';
			$titles="<th>".__('Data Field','wp-custom-fields-search')."</th>";
			$inputs="<td><div id='form-field-dbname-$prefId-$id' class='form-field-title-div'><input type='text' name='$pref"."[name]' value='$values[name]' class='form-field-title'/></div></td>";
			$count=1;
			foreach(array('joiner'=>__('Data Type','wp-custom-fields-search'),'comparison'=>__('Compare','wp-custom-fields-search'),'input'=>__('Widget','wp-custom-fields-search')) as $k=>$v){
				$dd = new AdminDropDown($pref."[$k]",$values[$k],$this->getClasses($k),array('onChange'=>'CustomSearch.get("'.$prefId.'").updateOptions("'.$id.'","'.$k.'")','css_class'=>"wpcfs-$k"));
				$titles="<th>".$v."</th>".$titles;
				$inputs="<td>".$dd->getInput()."</td>".$inputs;
				if(++$count==2){
					$output.="<table class='form-field-table form-class-$k'><tr>$titles</tr><tr>$inputs</tr></table>";
					$count=0;
					$inputs = $titles='';
				}
			}
			if($titles){
				$output.="<table class='form-field-table'><tr>$titles</tr><tr>$inputs</tr></table>";
				$inputs = $titles='';
			}
			$titles.="<th>".__('Numeric','wp-custom-fields-search')."</th><th>".__('Widget Config','wp-custom-fields-search')."</th>";
			$inputs.="<td><input type='checkbox' ".($values['numeric']?"checked='true'":"")." name='$pref"."[numeric]'/></td>";

			if(class_exists($widgetClass = $values['input'])){
				$widget = new $widgetClass();
				if(compat_method_exists($widget,'getConfigForm'))
					$widgetConfig=$widget->getConfigForm($pref,$values);
			}


			$inputs.="<td><div id='$this->id"."-$prefId"."-$id"."-widget-config'>$widgetConfig</div></td>";
			$output.="<table class='form-field-table'><tr>$titles</tr><tr>$inputs</tr></table>";
			$output.="</div>";
			$output.="<a href='#' onClick=\"return CustomSearch.get('$prefId').remove('$id');\">Remove Field</a>";
			return "<div class='field-wrapper'>$output</div>";
		}

		function getRootURL(){
			return WP_CONTENT_URL .'/plugins/' .  dirname(plugin_basename(__FILE__) ) . '/';
		}
		function print_admin_scripts($params){
			$jsRoot = $this->getRootURL().'js';
			$cssRoot = $this->getRootURL().'css';
			$scripts = array('Class.js','CustomSearch.js','flexbox/jquery.flexbox.js');
			foreach($scripts as $file){
				echo "<script src='$jsRoot/$file' ></script>";
			}
			echo "<link rel='stylesheet' href='$cssRoot/admin.css' >";
			echo "<link rel='stylesheet' href='$jsRoot/flexbox/jquery.flexbox.css' >";
		}

		function getJoiners(){
			return $this->getClasses('joiner');
		}
		function getComparisons(){
			return $this->getClasses('comparison');
		}
		function getInputTypes(){
			return $this->getClasses('input');
		}
		function getClasses($type){
			global $CustomSearchFieldStatic;
			if(!$CustomSearchFieldStatic['Types']){
				$CustomSearchFieldStatic['Types'] = array(
					"joiner"=>array(
						"PostDataJoiner" =>__( "Post Field",'wp-custom-fields-search'),
						"CustomFieldJoiner" =>__( "Custom Field",'wp-custom-fields-search'),
						"CategoryJoiner" =>__( "Category",'wp-custom-fields-search'),
						"TagJoiner" =>__( "Tag",'wp-custom-fields-search'),
						"PostTypeJoiner" =>__( "Post Type",'wp-custom-fields-search'),
					),
					"input"=>array(
						"TextField" =>__( "Text Input",'wp-custom-fields-search'),
						"DropDownField" =>__( "Drop Down",'wp-custom-fields-search'),
						"RadioButtonField" =>__( "Radio Button",'wp-custom-fields-search'),
						"HiddenField" =>__( "Hidden Constant",'wp-custom-fields-search'),
					),
					"comparison"=>array(
						"EqualComparison" =>__( "Equals",'wp-custom-fields-search'),
						"LikeComparison" =>__( "Phrase In",'wp-custom-fields-search'),
						"WordsLikeComparison" =>__( "Words In",'wp-custom-fields-search'),
						"LessThanComparison" =>__( "Less Than",'wp-custom-fields-search'),
						"MoreThanComparison" =>__( "More Than",'wp-custom-fields-search'),
						"AtMostComparison" =>__( "At Most",'wp-custom-fields-search'),
						"AtLeastComparison" =>__( "At Least",'wp-custom-fields-search'),
						"RangeComparison" =>__( "Range",'wp-custom-fields-search'),
//TODO: Make this work...
//						"NotEqualComparison" =>__( "Not Equal To",'wp-custom-fields-search'),
					)
				);
				$CustomSearchFieldStatic['Types'] = apply_filters('custom_search_get_classes',$CustomSearchFieldStatic['Types']);
			}
			return $CustomSearchFieldStatic['Types'][$type];
		}
		function plugin_menu(){
			add_options_page('Form Presets','WP Custom Fields Search',8,__FILE__,array(&$this,'presets_form'));
		}
		function getPresets(){
			$presets = array();
			foreach(array_keys($config = $this->getConfig()) as $key){
				if(strpos($key,'preset-')===0) {
					$presets[$key] = $key;
					if($name = $config[$key]['name'])
						$presets[$key]=$name;
				}
			}
			return $presets;
		}
		function presets_form(){
			$presets=$this->getPresets();
			if(!$preset = $_REQUEST['selected-preset']){
				$preset = 'preset-default';
			}
			if(!$presets[$preset]){
				$defaults = $this->getDefaultConfig();
				$options = $this->getConfig();
				$options[$preset] = $defaults;
				if($n = $_POST[$this->id][$preset]['name'])
					$options[$preset]['name'] = $n;
				elseif($preset=='preset-default')
					$options[$preset]['name'] = 'Default';
				else{
					list($junk,$id) = explode("-",$preset);
					$options[$preset]['name'] = 'New Preset '.$id;
				}
				update_option($this->id,$options);
				$presets[$preset] = $options[$preset]['name'];
			}
			if($_POST['delete']){
				check_admin_referer($this->id.'-editpreset-'.$preset);
				$options = $this->getConfig();
				unset($options[$preset]);
				unset($presets[$preset]);
				update_option($this->id,$options);
				list($preset,$name) = each($presets);
			}

			$index = 1;
			while($presets["preset-$index"]) $index++;
			$presets["preset-$index"] = __('New Preset','wp-custom-fields-search');

			$linkBase = $_SERVER['REQUEST_URI'];
			$linkBase = preg_replace("/&?selected-preset=[^&]*(&|$)/",'',$linkBase);
			foreach($presets as $key=>$name){
				$config = $this->getConfig($key);
				if($config && $config['name']) $name=$config['name'];
				if(($n = $_POST[$this->id][$key]['name'])&&(!$_POST['delete']))
					$name = $n;
				$presets[$key]=$name;
			}
			$plugin=&$this;
			ob_start();
			wp_nonce_field($this->id.'-editpreset-'.$preset);
			$hidden = ob_get_contents();
			$hidden.="<input type='hidden' name='selected-preset' value='$preset'>";
			$shouldSave = $_POST['selected-preset'] && !$_POST['delete'] && check_admin_referer($this->id.'-editpreset-'.$preset);
			ob_end_clean();
			include(dirname(__FILE__).'/templates/options.php');
		}
		function process_tag($content){
			$regex = '/\[\s*wp-custom-fields-search\s+(?:([^\]=]+(?:\s+.*)?))?\]/';
			return preg_replace_callback($regex, array(&$this, 'generate_from_tag'), $content);
		}
		function process_shortcode($atts,$content){
			return $this->generate_from_tag(array("",$atts['preset']));
		}
		function generate_from_tag($reMatches){
			global $CustomSearchFieldStatic;
			ob_start();

			$preset=$reMatches[1];
			if(!$preset) $preset = 'default';
			wp_custom_fields_search($preset);

			$form = ob_get_contents();
			ob_end_clean();
			return $form;
		}
	}
	global $CustomSearchFieldStatic;
	$CustomSearchFieldStatic['Inputs'] = array();
	$CustomSearchFieldStatic['Types'] = array();

	class AdminDropDown extends DropDownField {
		function AdminDropDown($name,$value,$options,$params=array()){
			AdminDropDown::__construct($name,$value,$options,$params);
		}
		function __construct($name,$value,$options,$params=array()){
			$params['options'] = $options;
			$params['id'] = $params['name'];
			parent::__construct($params);
			$this->name = $name;
			$this->value = $value;
		}
		function getHTMLName(){
			return $this->name;
		}
		function getValue(){
			return $this->value;
		}
		function getInput(){
			return parent::getInput($this->name,null);
		}
	}

if (!function_exists('json_encode'))
{
  function json_encode($a=false)
  {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a))
    {
      if (is_float($a))
      {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a))
      {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
      }
      else
        return $a;
    }
    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a))
    {
      if (key($a) !== $i)
      {
        $isList = false;
        break;
      }
    }
    $result = array();
    if ($isList)
    {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    }
    else
    {
      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }
  }
}
function wp_custom_fields_search($presetName='default'){
	global $CustomSearchFieldStatic;
	if(strpos($presetName,'preset-')!==0) $presetName="preset-$presetName";
	$CustomSearchFieldStatic['Object']->renderWidget(array('widget_id'=>$presetName,'noTitle'=>true),array('number'=>$presetName));
}
function compat_method_exists($class,$method){
	return method_exists($class,$method) || in_array(strtolower($method),get_class_methods($class));
}
