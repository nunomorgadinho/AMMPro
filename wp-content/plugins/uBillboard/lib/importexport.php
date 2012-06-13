<?php

////////////////////////////////////////////////////////////////////////////////
//
//	Importer and Exporter
//
////////////////////////////////////////////////////////////////////////////////

/**
 *	Function, process file at location $file, import billboards from it
 *	Function might redirect using wp_redirect()
 *
 *	@param string $file Path to the import file
 *	
 *	@return void
 */
function uds_billboard_import($file)
{
	global $uds_billboard_errors, $uds_billboard_attributes;
	$import = @file_get_contents($file);

	if(empty($import)) {
		$uds_billboard_errors[] = __('Import file is empty', uds_billboard_textdomain);
		return;
	}
	
	try {
		libxml_use_internal_errors(true);
		$import = new SimpleXMLElement($import);
	} catch(Exception $e) {
		$uds_billboard_errors[] = sprintf(__('An error has occurred during XML Parsing: %s', uds_billboard_textdomain), $e->getMessage());
		return;
	}

	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));
	
	foreach($import->udsBillboards->udsBillboard as $udsBillboard) {
		$billboard = new uBillboard();
		$billboard->importFromXML($udsBillboard);
		$billboards[$billboard->name] = $billboard;
	}
	
	if(!$billboard->isValid()) {
		$uds_billboard_errors[] = __('Import file is corrupted', uds_billboard_textdomain);
		return;
	}

	if(isset($_POST['import-attachments']) && $_POST['import-attachments'] == 'on') {
		foreach($billboards as $bbname => $billboard) {
			foreach($billboard->slides as $slide) {
				$urlinfo = parse_url($slide->image);
				$localurl = parse_url(get_option('siteurl'));
				//if($urlinfo['hostname'] == $localurl['hostname']) continue;
				
				//echo "Downloading attachment";
				$image = @file_get_contents($slide->image);
				if(!empty($image)) {
					$uploads = wp_upload_dir();
					if(false === $uploads['error']) {
						$filename = pathinfo($urlinfo['path']);
						$path = trailingslashit($uploads['path']) . wp_unique_filename($uploads['path'], $filename['basename']);
						if(! (false === @file_put_contents($path, $image)) ) {
							$filename = pathinfo($path);
							$slide->image = $uploads['url'] . '/' . $filename['basename'];
							
							$wp_filetype = wp_check_filetype(basename($path), null );
							$attachment = array(
								'post_mime_type' => $wp_filetype['type'],
								'post_title' => preg_replace('/\.[^.]+$/', '', basename($path)),
								'post_content' => '',
								'post_status' => 'inherit'
							);
							$attach_id = wp_insert_attachment( $attachment, $path );
							// you must first include the image.php file
							// for the function wp_generate_attachment_metadata() to work
							require_once(ABSPATH . "wp-admin" . '/includes/image.php');
							$attach_data = wp_generate_attachment_metadata( $attach_id, $path );
							wp_update_attachment_metadata( $attach_id,  $attach_data );
							//echo "Attachment saved in ".$billboards[$bbname]['slides'][$key]->image;
						} else {
							$uds_billboard_errors[] = sprintf(__("Failed to save image to %s", uds_billboard_textdomain), $path);
							break;
						}
					} else {
						$uds_billboard_errors[] = __("Uploads dir is not writable", uds_billboard_textdomain);
						break;
					}
				} else {
					$uds_billboard_errors[] = __("Failed to download image", uds_billboard_textdomain);
					break;
				}
			}
			
			if(!empty($uds_billboards_errors)) break;
		}
	}
	
	update_option(UDS_BILLBOARD_OPTION, maybe_serialize($billboards));
	
	if(empty($uds_billboards_errors))
		wp_redirect('admin.php?page=uds_billboard_admin');
}

/**
 *	Function, export a billboard or all billboards. Directly echoes the content with
 *	appropriate headers
 *
 *	Parameter can be:
 *		bool false -> will export all billboards
 *		string -> will export single uBillboard by name
 *		array -> array('billboard', 'billboard2') will export all billboards by names
 *	
 *	@param bool|string|array
 *	@return void
 */
function uds_billboard_export($what = false)
{
	$billboards = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION));
	
	$export = '<?xml version="1.0"?>' . "\n";
	$export .= '<udsBillboardExport>' . "\n";
	$export .= ' <version>'.UDS_BILLBOARD_VERSION.'</version>' . "\n";
	$export .= ' <udsBillboards>' . "\n";
	
	if(is_array($what)) {
		foreach($what as $name) {
			$billboard = $billboards[$name];
			$export .= $billboard->export() . "\n";
		}
	} elseif(is_string($what)) {
		$billboard = $billboards[$name];
		$export .= $billboard->export() . "\n";
	} else {
		foreach($billboards as $name => $billboard) {
			$export .= $billboard->export() . "\n";
		}
	}
	
	$export .= ' </udsBillboards>' . "\n";
	$export .= '</udsBillboardExport>' . "\n";
	
	header('Content-type: text/xml');
	header('Content-Disposition: attachment; filename="uBillboard.xml"');
	die($export);
}

function uds_billboard_get_v2()
{
	return maybe_unserialize(get_option('uds-billboard', array()));
}

function uds_billboard_can_import_from_v2()
{
	$v2 = uds_billboard_get_v2();
	
	return !empty($v2);
}

function uds_billboard_list_v2()
{
	$v2 = uds_billboard_get_v2();
	
	if(!empty($v2)) {
		echo '<ol class="uds-billboard-list-v2">';
		foreach($v2 as $name => $billboard) {
			echo '<li>' . $name . '</li>';
		}
		echo '</ol>';
	}
}

function uds_billboard_import_v2()
{
	$v2 = uds_billboard_get_v2();
	
	$billboards = uBillboard::upgradeFromV2($v2);
	
	update_option(UDS_BILLBOARD_OPTION, maybe_serialize($billboards));
	
	$message = 'uds-message='.urlencode(__('Billboards imported successfully', uds_billboard_textdomain)).'&uds-class='.urlencode('updated');
	
	wp_redirect('admin.php?page=uds_billboard_admin&'.$message);
}

?>