<?php

// define general options for billboard
$uds_billboard_general_options = array(
	'name' => array(
		'type' => 'text',
		'label' => __('Billboard Name', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Enter a name for this Billboard. You will use this name to create the Billboard on your site.', uds_billboard_textdomain),
		'default' => 'billboard'
	),
	'width' => array(
		'type' => 'text',
		'label' => __('Width', uds_billboard_textdomain),
		'unit' => 'px',
		'tooltip' => __('Billboard Width in pixels', uds_billboard_textdomain),
		'default' => 940
	),
	'height' => array(
		'type' => 'text',
		'label' => __('Height', uds_billboard_textdomain),
		'unit' => 'px',
		'tooltip' => __('Billboard Height in pixels', uds_billboard_textdomain),
		'default' => 380
	),
	'autoplay' => array(
		'type' => 'checkbox',
		'label' => __('Autoplay', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Automatically start playing slides, makes sense to turn this off only if Show Controls is enabled.', uds_billboard_textdomain),
		'default' => 'on'
	),
	'pause-on-video' => array(
		'type' => 'checkbox',
		'label' => __('Pause on video', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Pause playback if slide with embedded video is encountered.', uds_billboard_textdomain),
		'default' => 'on'
	),
	'randomize' => array(
		'type' => 'checkbox',
		'label' => __('Shuffle Slides', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Shuffle slides each time the slider is loaded', uds_billboard_textdomain),
		'default' => ''
	),
	'square-size' => array(
		'type' => 'text',
		'label' => __('Square Size', uds_billboard_textdomain),
		'unit' => 'pixels',
		'tooltip' => __('Square dimension, applies only to transitions based on squares', uds_billboard_textdomain),
		'default' => 100
	),
	'style' => array(
		'type' => 'select',
		'label' => __('Style', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => '',
		'options' => array(
			'dark' => __('Dark', uds_billboard_textdomain),
			'bright' => __('Bright', uds_billboard_textdomain)
		),
		'default' => ''
	),
	'show-timer' => array(
		'type' => 'checkbox',
		'label' => __('Show Timer', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Shows countdown until the next slide transition when playing', uds_billboard_textdomain),
		'default' => 'on'
	),
	'controls-skin' => array(
		'type' => 'select',
		'label' => __('Skin', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('How the controls should look', uds_billboard_textdomain),
		'options' => array(
			'mini' 				=> __('Minimal Style Controls', uds_billboard_textdomain),
			'oldskool' 			=> __('Old School uBillboard', uds_billboard_textdomain),
			'oldskool-bright' 	=> __('Bright Old School uBillboard', uds_billboard_textdomain),
			'utube' 			=> __('uTube', uds_billboard_textdomain),
			'silver' 			=> __('Silver', uds_billboard_textdomain),
			'modern'			=> __('Modern', uds_billboard_textdomain),
			'ubb-v2'			=> __('uBillboard v2', uds_billboard_textdomain)
		),
		'default' => 'oldskool'
	),
	'controls-position' => array(
		'type' => 'select',
		'label' => __('Position', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => '',
		'options' => array(
			'inside' 	=> __('Inside', uds_billboard_textdomain),
			'outside' 	=> __('Outside', uds_billboard_textdomain)
		),
		'default' => 'inside'
	),
	'show-controls' => array(
		'type' => 'select',
		'label' => __('Show Controls', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Controls enable you to switch between slides', uds_billboard_textdomain),
		'options' => array(
			'no' 	=> __("Don't show controls", uds_billboard_textdomain),
			'hover' => __('Show on Mouse Hover', uds_billboard_textdomain),
			'yes' 	=> __('Show at all times', uds_billboard_textdomain)
		),
		'default' => 'yes'
	),
	'show-pause' => array(
		'type' => 'select',
		'label' => __('Show Play/Pause', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Display show pause button', uds_billboard_textdomain),
		'options' => array(
			'no' 	=> __("Don't show Play/Pause", uds_billboard_textdomain),
			'hover' => __('Show on Mouse Hover', uds_billboard_textdomain),
			'yes' 	=> __('Show at all times', uds_billboard_textdomain)
		),
		'default' => 'yes'
	),
	'show-paginator' => array(
		'type' => 'select',
		'label' => __('Show Paginator', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Display pagination control', uds_billboard_textdomain),
		'options' => array(
			'no' 	=> __("Don't show Paginator", uds_billboard_textdomain),
			'hover' => __('Show on Mouse Hover', uds_billboard_textdomain),
			'yes' 	=> __('Show at all times', uds_billboard_textdomain)
		),
		'default' => 'yes'
	),
	'show-thumbnails' => array(
		'type' => 'select',
		'label' => __('Show Thumbnails', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Small preview images for all slides', uds_billboard_textdomain),
		'options' => array(
			'no' 	=> __("Don't show Thumbnails", uds_billboard_textdomain),
			'hover' => __('Show on Mouse Hover', uds_billboard_textdomain),
			'yes' 	=> __('Show at all times', uds_billboard_textdomain)
		),
		'default' => 'yes'
	),
	'thumbnails-position' => array(
		'type' => 'select',
		'label' => __('Thumbnail Position', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Where do you want thumbs to show', uds_billboard_textdomain),
		'options' => array(
			'top' 		=> __('Top', uds_billboard_textdomain),
			'bottom' 	=> __('Bottom', uds_billboard_textdomain),
			'right' 	=> __('Right', uds_billboard_textdomain),
			'left' 		=> __('Left', uds_billboard_textdomain)
		),
		'default' => 'bottom'
	),
	'thumbnails-inside' => array(
		'type' => 'checkbox',
		'label' => __('Inside', uds_billboard_textdomain),
		'unit' => '',
		'tooltip' => __('Where do you want thumbs to show', uds_billboard_textdomain),
		'default' => ''
	),
	'thumbnails-width' => array(
		'type' => 'text',
		'label' => __('Thumbnail Width', uds_billboard_textdomain),
		'unit' => 'px',
		'tooltip' => __('Width of the thumbnail images', uds_billboard_textdomain),
		'default' => '80'
	),
	'thumbnails-height' => array(
		'type' => 'text',
		'label' => __('Thumbnail Height', uds_billboard_textdomain),
		'unit' => 'px',
		'tooltip' => __('Height of the thumbnail images', uds_billboard_textdomain),
		'default' => '60'
	),
	'thumbnails-hover-color' => array(
		'type' => 'color',
		'unit' => '',
		'label' => __('Thumbnail border hover color', uds_billboard_textdomain),
		'tooltip' => __('Border color of thumbnail on mouse over', uds_billboard_textdomain),
		'default' => 'red'
	)
);


class uBillboard {
	/**
	 *	@var array of slides in this slider
	 */
	private $slides;
	
	/**
	 *	@var array 	of slides in this slider that have already been rendered
	 *				This is used to render slide JS in the correct order
	 */
	private $slidesRendered;
	
	/**
	 *	Helper static function to transform an oldschool
	 *	uBillboard v2 style billboards array into v3 format
	 *
	 *	@param array $billboards v2 style billboards array
	 *	
	 *	@return array of v3 billboards
	 */
	public static function upgradeFromV2($billboards)
	{
		$v2 = array();
		foreach($billboards as $name => $bbv2) {
			$billboard = new uBillboard(array(
				'name' => $name,
				'width' => $bbv2['width'],
				'height' => $bbv2['height'],
				'autoplay' => $bbv2['autoplay'],
				'randomize' => isset($bbv2['randomize']) ? $bbv2['randomize'] : '',
				'square-size' => $bbv2['square-size'],
				'style'	=> 'dark',
				'show-timer' => '',
				'controls-skin' => 'oldskool',
				'controls-position' => 'inside',
				'show-paginator' => $bbv2['show-paginator'] == 'on' ? 'yes' : 'no',
				'show-controls' => $bbv2['show-controls'] == 'on' ? 'yes' : 'no',
				'show-pause' => $bbv2['show-pause'] == 'on' ? 'yes' : 'no',
				'show-thumbnails' => 'no'
			));
			
			foreach($bbv2['slides'] as $slide) {
				$v3slide = uBillboardSlide::upgradeFromV2($slide, $bbv2, $billboard);
				$billboard->addSlide($v3slide);	
			}
			
			$v2[$name] = $billboard;
		}
		
		$v3 = maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array()));
		
		$v3 = array_merge($v3, $v2);
		
		return $v3;
	}
	
	/**
	 *	Constructor, will parse $options to create uBillboard
	 *	
	 *	@param array|bool $options 
	 *	@return uBillboard
	 */
	public function __construct($options = false)
	{
		global $uds_billboard_general_options;

		foreach($uds_billboard_general_options as $key => $option) {
			$camelized = $this->camelize($key);
			if(isset($options[$key])) {
				$this->{$camelized} = $this->sanitizeOption($key, $options[$key]);
				unset($options[$key]);
			} else {
				$this->{$camelized} = $option['default'];
			}
		}
		
		$this->slides = array();
		$this->slidesRendered = array();
	}
	
	/**
	 *	Generalized getter 
	 *	
	 *	@return mixed
	 */
	public function __get($key)
	{
		$key = $this->camelize($key);
		return $this->{$key};
	}
	
	/**
	 *	unserialize uBillboard, reset all attributes that
	 *	are not present to their default values
	 *	
	 *	@return void
	 */
	public function __wakeup()
	{
		global $uds_billboard_general_options;
		
		foreach($uds_billboard_general_options as $key => $option) {
			$camelized = $this->camelize($key);
			if(!isset($this->{$camelized})) {
				$this->{$camelized} = $option['default'];
			}
		}
		
		foreach($this->slides as $id => $slide) {
			$slide->setId($id);
			$slide->setSlider($this);
		}
	}
	
	/**
	 *	Importer, set up current instance based on SimpleXMLElement
	 *	XML data from an import file
	 *	
	 *	@return void
	 */
	public function importFromXML($billboard)
	{
		global $uds_billboard_general_options;
		
		foreach($billboard->properties[0] as $property){
			foreach($uds_billboard_general_options as $key => $option) {
				if($property->key == $key) {
					$camelized = $this->camelize($key);
					$this->{$camelized} = (string)$property->value;
				}
			}
		}
		
		foreach($billboard->slides->slide as $slide) {
			$s = new uBillboardSlide(false, $this);
			$s->importFromXML($slide);
			$this->slides[] = $s;
		}
	}
	
	/**
	 *	Helper, make sure that this uBillboard has an unique name among other uBillboards
	 *	in this installation
	 *	
	 *	@return void
	 */
	public function setUniqueName()
	{
		$billboards = array_keys(maybe_unserialize(get_option(UDS_BILLBOARD_OPTION, array())));
	
		$guess = $root = 'billboard';
	
		$i = 1;
		while(in_array($guess, $billboards)) {
			$guess = $root . '-' . $i;
			$i++;
		}
		
		$this->name = $guess;
	}
	
	/**
	 *	Function, adds a new slide to the end of the slide array
	 *	
	 *	@return bool success
	 */
	public function addSlide($slide)
	{
		if(is_a($slide, 'uBillboardSlide')) {
			$this->slides[] = $slide;
			return true;
		}
		return false;
	}
	
	/**
	 *	Update current uBillboard with data from the $options array
	 *
	 *	@param array $options
	 *	
	 *	@return void
	 */
	public function update($options)
	{
		global $uds_billboard_general_options;

		foreach($uds_billboard_general_options as $key => $option) {
			$camelized = $this->camelize($key);
			if(isset($options[$key])) {
				$this->{$camelized} = $this->sanitizeOption($key, $options[$key]);
				unset($options[$key]);
			} else {
				$this->{$camelized} = '';
			}
		}
		
		$this->slides = uBillboardSlide::getSlides($options, $this);

		if(!empty($this->slides)) {
			foreach($this->slides as $key => $slide) {
				$slide->setId($key);
				if(!$slide->isValid()) {
					unset($this->slides[$key]);
				}
			}
		}
	}
	
	/**
	 *	Helper function, performs internal checks to determine if uBillboard
	 *	is valid
	 *	
	 *	@return bool
	 */
	public function isValid()
	{
		return !empty($this->slides);
	}
	
	/**
	 *	Add empty default slide to the end of slide array
	 *	
	 *	@return void
	 */
	public function addEmptySlide()
	{
		$this->slides[] = new uBillboardSlide(false, $this);
	}
	
	/**
	 *	Exporter, exports self in the XML format that can be read by the importer
	 *	
	 *	@return void
	 */
	public function export()
	{
		global $uds_billboard_general_options;
		
		$out = '  <udsBillboard>' . "\n";
		
		$out .= '     <properties>'. "\n";
		
		foreach($uds_billboard_general_options as $key => $option) {
			$camelKey = $this->camelize($key);
			$out .= '     <property>' . "\n";
			$out .= '      <key>' . $key . '</key>' . "\n";
			$out .= '      <value><![CDATA[' . $this->{$key} . ']]></value>' . "\n";
			$out .= '     </property>' . "\n";
		}
		
		$out .= '   </properties>'. "\n";
		
		$out .= '   <slides>' . "\n";
		
		foreach($this->slides as $slide) {
			$out .= $slide->export();
		}
		
		$out .= '   </slides>' . "\n";
		
		$out .= '  </udsBillboard>' . "\n";
		
		return $out;
	}
	
	public function createThumbs()
	{
		$ok = true;
		foreach($this->slides as $slide) {
			$result = $slide->resizeImages(true);
			if(is_wp_error($result)) {
				$ok = false;
			}
		}
		
		return $ok;
	}
	
	/**
	 *	The main frontend renderer
	 *	
	 *	@param int $id ID of the current uBillboard on the currently rendered page
	 *	@return string rendered html markup
	 */
	public function render($id = 0)
	{
		global $uds_bb_params; // parameters of the currently rendered billboard
		
		// store width and height into the params
		$uds_bb_params = array(
			'width' => $this->width,
			'height' => $this->height
		);
		
		$slides = $this->renderSlides();
		$controls = $this->renderControls();
		
		$out = "
		<!-- uBillboard ".UDS_BILLBOARD_VERSION." ID:$id -->
		<div class='uds-bb uds-{$this->style}' id='uds-bb-$id' style='width:{$this->width}px;height:{$this->height}px'>
			$controls
			$slides
		</div>
		<!-- END uBillboard ID:$id -->";
		
		return $out;
	}
	
	/**
	 *	Render JS calls for the uBillboard
	 *	
	 *	@return string rendered JS
	 */
	public function renderJS($id = 0)
	{
		$autoplay = $this->autoplay === 'on' ? 'true' : 'false';
		$pauseOnVideo = $this->pauseOnVideo === 'on' ? 'true' : 'false';
		$showTimer = $this->showTimer === 'on' ? 'true' : 'false';
		
		$showControls = 'false';
		if($this->showControls == 'hover') 	$showControls = "'hover'";
		if($this->showControls == 'yes') 	$showControls = 'true';

		$showPause = 'false';
		if($this->showPause == 'hover') $showPause = "'hover'";
		if($this->showPause == 'yes') 	$showPause = 'true';

		$showPaginator = 'false';
		if($this->showPaginator == 'hover') $showPaginator = "'hover'";
		if($this->showPaginator == 'yes') 	$showPaginator = 'true';
		
		$showThumbnails = 'false';
		if($this->showThumbnails == 'hover')	$showThumbnails = "'hover'";
		if($this->showThumbnails == 'yes')		$showThumbnails = 'true';

		$thumbnailsHoverColor = $this->thumbnailsHoverColor;
		
		// Render slides' JS
		$slides = array();
		
		foreach($this->slidesRendered as $key => $slide) {
			$slides[] = $key . ': ' . $slide->renderJS();
		}
		
		$slides = implode(",\n\t\t\t\t\t", $slides);
		
		$scripts = "
			$('#uds-bb-$id').show().uBillboard({
				width: '{$this->width}px',
				height: '{$this->height}px',
				squareSize: '{$this->squareSize}px',
				autoplay: $autoplay,
				pauseOnVideo: $pauseOnVideo,
				showControls: $showControls,
				showPause: $showPause,
				showPaginator: $showPaginator,
				showThumbnails: $showThumbnails,
				showTimer: $showTimer,
				thumbnailHoverColor: \"#$thumbnailsHoverColor\",
				slides: {
					$slides
				}
			});
		";
		
		return $scripts;
	}
	
	/**
	 *	Admin renderer function for every field
	 *	
	 *	@return
	 */
	public function renderAdminOption($option)
	{
		global $uds_billboard_general_options;
		$field = $uds_billboard_general_options[$option];
		$camelized = $this->camelize($option);
		$value = $this->{$camelized};
		
		switch($uds_billboard_general_options[$option]['type']) {
			case 'text':
				$this->renderAdminOptionText($option, $field, $value);
				break;
			case 'checkbox':
				$this->renderAdminOptionCheckbox($option, $field, $value);
				break;
			case 'color':
				$this->renderAdminColorpicker($option, $field, $value);
				break;
			case 'select':
				$this->renderAdminOptionSelect($option, $field, $value);
				break;
		}
	}
	
	// Functions to render single fields in general options for each billboard
	public function renderAdminOptionText($option, $field, $value)
	{
		?>
		<div class="uds-billboard-<?php echo $option ?> option-container">
			<label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
			<input type="text" id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" value="<?php echo empty($value) ? $field['default'] : $value ?>" class="text" />
			<span class="unit"><?php echo $field['unit'] ?></span>
			<div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
			<div class="clear"></div>
		</div>
		<?php
	}
	
	public function renderAdminOptionCheckbox($option, $field, $value)
	{
		$checked = ( $value === null ? $field['default'] : $value ) == 'on' ? 'checked="checked"' : '';
		?>
		<div class="uds-billboard-<?php echo $option ?> option-container">
			<label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
			<input type="checkbox" id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" <?php echo $checked ?> class="checkbox" />
			<span class="unit"><?php echo $field['unit'] ?></span>
			<div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
			<div class="clear"></div>
		</div>
		<?php
	}
	
	function renderAdminOptionSelect($option, $field, $value)
	{
		$checked = ( $value === null ? $field['default'] : $value ) == 'on' ? 'checked="checked"' : '';
		?>
		<div class="uds-billboard-<?php echo $option ?> option-container select">
			<label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
			<select id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" class="select">
				<option value="" disabled="disabled"><?php echo $field['label'] ?></option>
				<?php foreach($field['options'] as $key => $label): ?>
					<option value="<?php echo $key ?>" <?php echo $key == $value ? 'selected="selected"' : '' ?>><?php echo $label ?></option>
				<?php endforeach; ?>
			</select>
			<span class="unit"><?php echo $field['unit'] ?></span>
			<div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
			<div class="clear"></div>
		</div>
		<?php
	}
	

	function renderAdminColorpicker($option, $field, $value)
	{
		?>
        <div class="uds-billboard-<?php echo $option ?> option-container color">
            <label for="uds-billboard-<?php echo $option ?>"><?php echo $field['label'] ?></label>
            #<input type="text" id="uds-billboard-<?php echo $option ?>" name="uds_billboard[<?php echo $option ?>]" value="<?php echo $value ?>" class="color" />
            <div class="tooltip-content"><?php echo $field['tooltip'] ?></div>
            <div class="clear"></div>
        </div>
        <?php
	}
	
	private function renderSlides()
	{
		$out = "<div class='uds-bb-slides'>\n";
		
		$slides = $this->slides;
		
		if($this->randomize === "on") {
			shuffle($slides);
		}
		
		$this->slides = $slides;
		
		foreach($slides as $slide) {
			$out .= $slide->render();
			$this->slidesRendered[] = $slide;
		}
		
		$out .= "\t\t\t</div>";
		
		return $out;
	}
	
	private function renderControls()
	{
		$out = "<div class='uds-bb-controls'>";
		
		switch($this->controlsSkin){
			case 'mini':
				$out .= $this->paginatorMini();
				break;
			case 'oldskool':
				$out .= $this->paginatorOldskool();
				break;
			case 'oldskool-bright':
				$out .= $this->paginatorOldskoolBright();
				break;
			case 'utube':
				$out .= $this->paginatoruTube();
				break;
			case 'silver':
				$out .= $this->paginatorSilver();
				break;
			case 'modern':
				$out .= $this->paginatorModern();	
				break;
			case 'ubb-v2':
				$out .= $this->paginatorUbbv2();	
		}

		if($this->showThumbnails !== 'no') {
			$out .= $this->thumbnails($this->slides);
		}
			
		$out .= "\t\t\t</div>\n";
		
		return $out;
	}
	
	
	/**
	 *	Mini paginator renderer
	 *	
	 *	@return string rendered paginator
	 */
	private function paginatorMini()
	{
		$out = "
				<div class='uds-bb-paginator mini {$this->controlsPosition}'>
					<div class='uds-bb-button uds-bb-playpause'>".__('Play', uds_billboard_textdomain)."</div>
					<div class='uds-bb-button uds-bb-prev'>".__('Prev', uds_billboard_textdomain)."</div>
					<div class='uds-bb-button uds-bb-next'>".__('Next', uds_billboard_textdomain)."</div>
					<div class='uds-bb-position-indicator'></div>
				</div>
		";
		return $out;
	}
	
	/**
	 *	Oldskool paginator renderer
	 *	
	 *	@return string rendered paginator
	 */
	private function paginatorOldskool()
	{
		$out = "
				<div class='uds-bb-paginator oldskool {$this->controlsPosition}'>
					<div class='uds-bb-button uds-bb-playpause uds-center'><span>".__('Play', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-button uds-bb-prev uds-center-vertical'><span>".__('Prev', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-button uds-bb-next uds-center-vertical'><span>".__('Next', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-position-indicator-bullets'></div>
				</div>
		";
		return $out;
	}
	
		/**
	 *	Oldskool paginator renderer
	 *	
	 *	@return string rendered paginator
	 */
	private function paginatorUbbv2()
	{
		$out = "
				<div class='uds-bb-paginator ubbv2 {$this->controlsPosition}'>
					<div class='uds-bb-button uds-bb-playpause uds-center'><span>".__('Play', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-button uds-bb-prev uds-center-vertical'><span>".__('Prev', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-button uds-bb-next uds-center-vertical'><span>".__('Next', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-position-indicator-bullets'></div>
				</div>
		";
		return $out;
	}
	
	/**
	 *	Oldskool bright paginator renderer
	 *	
	 *	@return string rendered paginator
	 */
	private function paginatorOldskoolBright()
	{
		$out = "
				<div class='uds-bb-paginator oldskool-bright {$this->controlsPosition}'>
					<div class='uds-bb-button uds-bb-playpause uds-center'><span>".__('Play', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-button uds-bb-prev uds-center-vertical'><span>".__('Prev', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-button uds-bb-next uds-center-vertical'><span>".__('Next', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-position-indicator-bullets'></div>
				</div>
		";
		return $out;
	}
	
	/**
	 *	Silver paginator renderer
	 *	
	 *	@return string rendered paginator
	 */
	
	private function paginatorSilver()
	{
		$out = "
				<div class='uds-bb-paginator silver {$this->controlsPosition}'>
					<div class='uds-bb-button uds-bb-prev uds-center-vertical'><span>".__('Prev', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-button uds-bb-next uds-center-vertical'><span>".__('Next', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-position-indicator-bullets-container uds-center-horizontal'>
						<div class='uds-bb-position-indicator-bullets'></div>
						<div class='uds-bb-button uds-bb-playpause'><span>".__('Play', uds_billboard_textdomain)."</span></div>
					</div>
				</div>
		";
		return $out;
	}
	
	/**
	 *	uTube paginator renderer
	 *	
	 *	@return string rendered paginator
	 */
	private function paginatoruTube()
	{
		$out = "
				<div class='uds-bb-paginator uTube {$this->controlsPosition}'>
					<div class='uds-bb-button uds-bb-prev'><span>".__('Prev', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-button uds-bb-playpause uds-left'><span>".__('Play', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-button uds-bb-next'><span>".__('Next', uds_billboard_textdomain)."</span></div>
					<div class='uds-bb-position-indicator-bullets'></div>
				</div>
		";
		return $out;
	}
	
	/**
	 *	Modern paginator renderer
	 *	
	 *	@return string rendered paginator
	 */
	private function paginatorModern()
	{
		$out = "
				<div class='uds-bb-paginator modern {$this->controlsPosition}'>
					<div class='uds-bb-paginator moderncontainer'>
						<div class='uds-bb-button uds-bb-prev'><span>".__('Prev', uds_billboard_textdomain)."</span></div>
						<div class='uds-bb-button uds-bb-playpause uds-left'><span>".__('Play', uds_billboard_textdomain)."</span></div>
						<div class='uds-bb-button uds-bb-next'><span>".__('Next', uds_billboard_textdomain)."</span></div>
						<div class='uds-bb-position-indicator-bullets'></div>
					</div>
				</div>
		";
		return $out;
	}
	
	/**
	 *	thumbnails renderer
	 *	
	 *	@return string thumbnails renderer
	 */
	private function thumbnails($slides)
	{
		$position = $this->thumbnailsPosition;
		$inside = $this->thumbnailsInside === 'on' ? 'inside' : '';
		$skin = $this->controlsSkin;
		
		$out = "
				<div class='uds-bb-thumbnails $skin $position $inside'>
					<div class='uds-bb-thumbnail-container'>\n";
		foreach($slides as $slide) {
			$out .= $slide->renderThumb();
		}
		$out .= "\t\t\t\t\t</div>
				</div>\n";
		return $out;
	}
	
	/**
	 *	Make sure the option conforms to the possible value types
	 *	and definition in attribute array
	 *	
	 *	@return NULL or option
	 */
	private function sanitizeOption($key, $option)
	{
		global $uds_billboard_general_options;
		
		if(!array_key_exists($key, $uds_billboard_general_options)){
			return NULL;
		}
		
		if($key == 'name') {
			return sanitize_title($option);
		}
		
		if(in_array($key, array_keys($uds_billboard_general_options))) {
			if($uds_billboard_general_options[$key]['type'] == 'select') {
				if(in_array($option, array_keys($uds_billboard_general_options[$key]['options']))) {
					return $option;
				} else {
					return $uds_billboard_general_options[$key]['default'];
				}
			}
			
			return $option;
		}
		
		return $uds_billboard_general_options[$key]['default'];
	}
	
	/**
	 *	Helper function to transform attributes from dash to cammelCase
	 *	
	 *	@return string camelized string
	 */
	private function camelize($string) 
	{
		$string = str_replace(array('-', '_'), ' ', $string); 
		$string = ucwords($string); 
		$string = str_replace(' ', '', $string);  
		
		$string = lcfirst($string);
		
		return $string;
	}
}

?>