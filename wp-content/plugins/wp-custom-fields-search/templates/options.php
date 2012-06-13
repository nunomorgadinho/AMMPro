<div class='presets-selector'><ul>
<h4><?php _e('Select Preset','wp-custom-fields-search')?></h4>
<?php	foreach($presets as $key=>$name){ ?>
<li><a href='<?php echo $linkBase?>&selected-preset=<?php echo $key?>'><?php echo $name?></a></li>
<?php	}	?>
</ul></div>
<div class='presets-example-code'> 
<h4><?php _e('Template Code','wp-custom-fields-search')?></h4>
<?php _e('To use this preset in your templates copy this code to the appropriate place in your template file:','wp-custom-fields-search')?><br/>
	<?php if($preset=='preset-default') { ?> 
		<pre><code><?php echo htmlspecialchars("<?php if(function_exists('wp_custom_fields_search')) 
	wp_custom_fields_search(); ?>")?></code></pre>
	<?php } else { ?>
		<pre><code><?php echo htmlspecialchars("<?php if(function_exists('wp_custom_fields_search')) 
	wp_custom_fields_search('$preset'); ?>")?></code></pre>
	<?php } ?>
	<h4><?php _e('Tag For Posts','wp-custom-fields-search')?></h4>
<?php _e('To use this preset in your posts/pages copy this code to the appropriate place in your post/page:','wp-custom-fields-search')?><br/>
	<?php if($preset=='preset-default') { ?> 
		<pre><code><?php echo htmlspecialchars("[wp-custom-fields-search]");?></pre></code>
	<?php } else { 
		$presetLabel = substr($preset,7);
	?>
		<pre><code><?php echo htmlspecialchars("[wp-custom-fields-search preset=\"$presetLabel\" ]");?></pre></code>
	<?php } ?>
</div>

<form method='post'><div class='searchforms-config-form'>
<?php echo $hidden?>
		<h4>Edit Preset "<?php echo $presets[$preset]?>"</h4>
		<?php $plugin->configForm($preset,$shouldSave) ?>
		<div class='options-controls'>
			<div class='options-button'>
			<input type='submit' value='<?php _e('Save Changes','wp-custom-fields-search')?>'/>
			</div>
			<div class='options-button'>
			<input type='submit' name='delete' value='<?php _e('Delete','wp-custom-fields-search')?>' onClick='return confirm("<?php _e('Are you sure you want to delete this preset?','wp-custom-fields-search')?>")'/>
			</div>
		</div>
</div></form>
