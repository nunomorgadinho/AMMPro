/**
 * jquery.freshline.Boxer - jQuery Plugin for LightBox Functions (freshline)
 * @version: 1.1 (12.09.2011)
 * @requires jQuery v1.2.2 or later 
 * @author Krisztian Horvath
 * All Rights Reserved, use only in freshline Templates or when Plugin bought at Envato ! 
**/




(function($,undefined){	
	
	
	
	////////////////////////////
	// THE PLUGIN STARTS HERE //
	////////////////////////////
	
	$.fn.extend({
	
		
		// OUR PLUGIN HERE :)
		fhboxer: function(options) {
	
		
			
		////////////////////////////////
		// SET DEFAULT VALUES OF ITEM //
		////////////////////////////////
		var defaults = {					
			hover_round:"false"		// true- will generate Circle Hovers, false will generate square hovers.
		};
		
		options = $.extend({}, $.fn.fhboxer.defaults, options);
		
		
		return this.each(function() {
					
			//PUT THE BANNER HOLDER IN A VARIABLE			
			var box = $(this);		
			
			// WRAP THE IMAGE
			box.wrap('<div class="item_lightbox_container"></div>');
			var ilc=box.parent();
			box.css({'cursor':'pointer'});
			
			
			// RECORD THE OPTIONS
			var opt=options;	
			
			
			// CHECK IF THE URL HAS THE PARAMETER PIC ALREADY...
			if (getUrlVars()["pic"] != null) {
                var firstitem=$('<div class="item_lightbox_container"><img src="'+getUrlVars()["pic"]+'"></img></div>');
				
				if ($('body').data('img-opened')!=1) {				
					firstitem.each(createLightBox);
					$('body').data('img-opened',1);		

				}

            }
			
			
		
			ilc.hover(
				function() {
					var $this=$(this).find('img');					
					var tp = $this.position().top + parseInt($this.css('padding-top')) + parseInt($this.css('margin-top')) + parseInt($this.css('border-top-width'));
					var lp = $this.position().left+ parseInt($this.css('padding-left')) + parseInt($this.css('margin-left')) + parseInt($this.css('border-left-width'));
					
					
					var overlay=$('<div class="draw_layer_over_item"></div>');	
					
					if (opt.hover_round=="true") 
						overlay.css({'-webkit-border-radius': $this.width()/2+'px',	
									 '-moz-border-radius': $this.width()/2+'px',
									'border-radius': $this.width()/2+'px'});
									
					var plus=$('<div class="layer_over_item_plus"></div>');
					overlay.data('overopacity',overlay.css('opacity'));
					 
					$this.parent().append(overlay);
					$this.parent().append(plus);

					var newW = $this.width();
					var newH = $this.height();
					if ($this.width() > $this.parent().width()) newW=$this.parent().width();
					if ($this.height() > $this.parent().height()) newH=$this.parent().height();
					
					overlay.css({'width':newW+"px",
								 'height':newH+"px",
								 'top':tp+"px",
								 'left':lp+"px",
								 'display':'block',
								 'opacity':'0.0'
								 });
								 
					plus.css({ 'left':(lp+(newW/2) -17) + "px",
								 'top':(tp+(newH/2) -17)+ "px",								 
								 'opacity':'0.0'
								 });
								 
								 
					
										
					plus.animate({'opacity': '1'},{duration:300,queue:false});
					overlay.animate({'opacity': '0.8'},{duration:300,queue:false});
					
				}, 
				function() {
					var $this=$(this);		
					$this.parent().find('.draw_layer_over_item').remove();
					$this.parent().find('.layer_over_item_plus').remove();
				});
			

			// OPEN THE LIGHTBOX IN CASE THE IMG BOX HAS BEEN CLICKED
			ilc.click(createLightBox);
			
			
		})
	}
})
		

		///////////////////////////////
		//  --  LOCALE FUNCTIONS -- //
		///////////////////////////////
		
		
				///////////////////////////
				// GET THE URL PARAMETER //
				///////////////////////////
				function getUrlVars()
						{
							var vars = [], hash;
							var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
							for(var i = 0; i < hashes.length; i++)
							{
								hash = hashes[i].split('=');
								vars.push(hash[0]);
								vars[hash[0]] = hash[1];
							}
							return vars;
						}
						
						
					function checkiPhone() {
						var iPhone=((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)));
						return iPhone;
					}

					function checkiPad() {
						var iPad=navigator.userAgent.match(/iPad/i);
						return iPad;
					}
					
					
			
					/////////////////////////
					// CREATE LIGHTBOX NOW //
					////////////////////////
					function createLightBox() {
						
						
						var $this=$(this);
						
						
						// ADD A BIG OVERLAY ON THE SCREEN
					    var overlay=$('<div id="fh_lboxoverlay" class="fh_lightbox_overlay"></div>');
						$('body').append(overlay);	
						var targetOpacity = overlay.css('opacity');
						
						// LIGHTBOX PROBLEM FOR iPAD && iPhone
						var ts=0;
						if (checkiPhone() || checkiPad()) ts=jQuery(window).scrollTop();
						
						overlay.css({	'width':$(window).width()+'px',
										'height':($(window).height()+150)+'px',
										'opacity':'0.0',
										'top':ts+'px'
										});						
											
						overlay.animate({'opacity':targetOpacity},{duration:500,queue:false});
						
						
						
						
						
						// CREATE THE LIGHTBOX ITSELF
						var lb=$('<div id="fh_lbox" class="fh_lightbox_holder"></div>');						
						lb.css({'opacity':'0.0'});						
						lb.css( {'top': ts + Math.round( ($( window ).height() - lb.outerHeight() ) / 2 ) + 'px', 'left': Math.round( ($( window ).width() - lb.outerWidth() ) / 2 ) + 'px', 'margin-top': 0, 'margin-left': 0} )												
						lb.animate({'opacity':'1.0'},{duration:900,queue:false});
						
						// ADD A MAINLOADER
						var mainloader=$('<div id="fh_lightbox-main-loader"></div>');
						mainloader.css({'opacity':'0.0'});						
						mainloader.animate({'opacity':'1.0'},{duration:900,queue:false});
						lb.append(mainloader);
						
						// ADD THE CLOSE BUTTON
						var cb=$('<div id="fh_lightbox_holder_closebutton" class="fh_lightbox_holder_closebutton"></div>')						
						cb.css({'opacity':'0.0'});						
						cb.animate({'opacity':'1.0'},{duration:900,queue:false});
						cb.css( { 'top':  ts + (0-cb.height()/2 + Math.round( ($( window ).height() - lb.outerHeight() ) / 2 )) + 'px', 
								  'left': (0-cb.width()/2 + lb.width()+Math.round( ($( window ).width() - lb.outerWidth() ) / 2 )) + 'px', 'margin-top': 0, 'margin-left': 0} )																		
						
						
						
						// ADD A INFO BOX NEXT TO THE LIGHTBOX
						var infobox=$('<div id="fh_lightbox_info_box" class="fh_lightbox_info_box"></div>');
						infobox.css({'opacity':'0.0'});						
						
						
						// SET THE INFOBOX START POSITION
						infobox.css( { 'top':  ts + (Math.round( ($( window ).height() - lb.outerHeight() ) / 2 )) + 'px', 
								  'left': (Math.round( ($( window ).width()) / 2 )) + 'px'} )																		
						
						
						// ADD THE TEXT (TITLE AND DECLARATION VIA THE ALT AND TITLE DECODED WITH URI
						if (decodeURI($this.find('img:first').attr('title'))!=undefined) var infotitle=$('<div class="title">'+decodeURI($this.find('img:first').attr('title'))+'</div>');
						if (decodeURI($this.find('img:first').attr('title'))!=undefined) var infocontent=$('<div class="content">'+decodeURI($this.find('img:first').attr('alt'))+'</div>')
						
						// REMOVE UNUSED IMG TAGS
						infotitle.find('img').remove();
						infocontent.find('img').remove();
						
						
						// FIND OUT WHERE THE ALTERNATIVE SOURCE IS. (ref, a href or src itself)
						var src=$this.find('img:first').attr('ref');
						if (src==undefined) src=$this.parent().find('a').attr('href');
						if (src==undefined) src=$this.find('img:first').attr('src');
						
						
						
						// ADD THE SOCIAL ICONS
						var twit=$('<div class="twitter"><div class="social_tab"><a href="http://twitter.com/share" class="twitter-share-button" data-url="'+self.location.href+"?pic="+src+'" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div></div>');											
						var face=$('<div class="facebook"><div class="social_tab"><iframe src="http://www.facebook.com/plugins/like.html?app_id=209611125764047&amp;href='+src+'&amp;send=false&amp;layout=button_count&amp;width=80&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=verdana&amp;height=21" style="width:80px; height:21px;"></iframe></div></div>'); 
						var gplus=$('<div class="googleplus"><!-- +1 Button from plus.google.com --><div class="social_tab"><script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script><g:plusone size="medium" href="'+src+'"></g:plusone></div></div>');
						
						
						
						
						var socials=$('<div id="socials"></div>');
						
						infobox.append(socials);	
						socials.append(twit).append(face).append(gplus);
						
						
						// SOCIAL FUNCTIONS
						socials.find('div').hover(
								function(){
									var $social_tab = $(this).find('.social_tab');
									// move tab into sight while fading
									$social_tab.show();
									$social_tab.stop(true,true);									
									$social_tab.animate({top:"-36px",opacity:1},{duration:200,queue:false});
								},function() {
									var $social_tab = $(this).find('.social_tab');
									// hide tab while fading out
									$social_tab.stop(true,true);
									//$social_tab.hide();
									$social_tab.animate({top:"-60px",opacity:"0.0"},{duration:0,queue:false});
								});
								
						infobox.append('<div style="clear:both" class="divider"></div>');
						
						if (infotitle.html()!="undefined") infobox.append(infotitle);
						if (infocontent.html()!="undefined") infobox.append(infocontent);
						
						
						
						
						
						// ADD THE IMAGE INSIDE THE LAYERDIV
						var bigImg = $('<img style="position:absolute;top:0px;left:0px;" src="'+src+'">');
						lb.append(bigImg);
						
						// READ OUT THE INFORMATION FROM TITLE AND ALT
						
						
						
						
						// APPEND ALL TO THE BODY
						$('body').append(lb);
						$('body').append(cb);
						$('body').append(infobox);
						
						
						lb.hover(
							function() {
								var $this=$(this);
								var lb=$('body').find('#fh_lbox');								
								var infobox=$('body').find('#fh_lightbox_info_box');
								
								if (lb.width()+lb.position().left>infobox.position().left) {
									infobox.stop();
									infobox.animate({'opacity':'0.0'},{duration:500,queue:false});
								}
							},
							function() {
								var $this=$(this);
								var infobox=$('body').find('#fh_lightbox_info_box');
								infobox.stop();
								infobox.animate({'opacity':'1.0'},{duration:300,queue:false});
							});
							
						
						// KILL ALL LIGHTBOX ITEMS HERE
						overlay.click(function() {
							$('body').unbind();
							$('body').find('#fh_lboxoverlay').remove();
							$('body').find('#fh_lbox').remove();
							$('body').find('#fh_lightbox_holder_closebutton').remove();
							$('body').find('#fh_lightbox_info_box').remove();
						});
						
						// KILL ALL LIGHTBOX ITEMS HERE
						cb.click(function() {
							$('body').unbind();
							$('body').find('#fh_lboxoverlay').remove();
							$('body').find('#fh_lbox').remove();
							$('body').find('#fh_lightbox_holder_closebutton').remove();
							$('body').find('#fh_lightbox_info_box').remove();
						});
																																							
						
						
						
						
						
						/////////////////////////////////////////////////////////////////////////////////////
						// DEPENDING ON THE SCROLL OR RESIZING EFFECT, WE SHOULD REPOSITION THE LIGHTBOX  //
						/////////////////////////////////////////////////////////////////////////////////////
						$(window).bind('resize scroll', resizeMeNow);
						
						preloadba_images(src);
						resizeMeNow(true);
					}
					
					
					/////////////////////////////////////////////////////
					// RESIZE THE WINDOW, AND OPEN THE MAIN IMAGE HERE //
					/////////////////////////////////////////////////////
					function resizeMeNow(dontshowinfo) {
							
							var $this=$(window);							
							var overlay=$('body').find('#fh_lboxoverlay');
							var lb=$('body').find('#fh_lbox');
							var infobox=$('body').find('#fh_lightbox_info_box');
							var cb=$('body').find('#fh_lightbox_holder_closebutton');
							
							// LIGHTBOX PROBLEM FOR iPAD && iPhone
							var ts=0;
							if (checkiPhone() || checkiPad()) ts=jQuery(window).scrollTop();
							
							overlay.css({'width':$this.width()+'px',
										'height':($this.height()+150)+'px',
										'top':ts+'px'
										});
							
							lb.stop();
							var newW=lb.find('img:first').width();
							var newH=lb.find('img:first').height();
							if (newW==0 || newW==null || newW==NaN) newW=100;
							if (newH==0 || newH==null || newH==NaN) newH=100;
							var minL=newW;
							
							if (newW+infobox.width() < $(window).width()) 
							{
								minL = newW+infobox.width();
								var infoW = (newW+Math.round( ($( window ).width() - minL) / 2 ));
							}  else {
								
								infoW = (newW+Math.round( ($( window ).width() - newW) / 2 )) - infobox.outerWidth();
							}
							
							
							lb.animate( {'top':  ts+Math.round( ($( window ).height() - newH) / 2 ) + 'px', 
										 'left': Math.round( ($( window ).width() - minL) / 2 ) + 'px' ,
										 'width':newW+'px',
										 'opacity':'1.0',
										 'height':newH+'px'},
										{duration:300,queue:false})

							cb.stop();
							cb.animate( {'top':  ts+(0-cb.height()/2 + Math.round( ($( window ).height() - newH ) / 2 )) + 'px', 
								         'left': (0-cb.width()/2+Math.round( ($( window ).width() - minL) / 2 ) + newW)+ 'px',
										 'opacity':'1.0'										 
										 },
										{duration:300,queue:false})																												
							
							infobox.stop();
							
							infobox.animate( {'top':  ts+(Math.round( ($( window ).height() - newH ) / 2 )) + 'px', 
								         'left': infoW + 'px',
										 'opacity':'1.0'										 
										 },
										{duration:300,queue:false})																												
						}
					
					//////////////////////////////////////
					// GET THE PAGESCROLL SETTINGS HERE //
					//////////////////////////////////////
					function getPageScroll() {
						var  yScroll;
						if (self.pageYOffset) {
						  yScroll = self.pageYOffset;
						  
						} else if (document.documentElement && document.documentElement.scrollTop) {
						  yScroll = document.documentElement.scrollTop;
						  
						} else if (document.body) {// all other Explorers
						  yScroll = document.body.scrollTop;
						  
						}
						return yScroll;
					}
					
					
					/////////////////////////////////////////////////////////////////////////////////////////	
					//REKURSIVE PRELOADING ALL THE ba_images, AND CALL THE CALLBACK FUNCTION AT THE END   //
					////////////////////////////////////////////////////////////////////////////////////////
					function preloadba_images(src){	
										
										
										var img = new Image();	// TEMPORARY HOLDER FOR IMAGE TO LOAD				
										$(img).css("display","none");
										$(img).attr('src',src);	// SET THE SOURCE OF THE TEMP IMAGE																				
										$(img).load(function(){						// IF NOT CACHED YET, LETS LOAD THE IMAGE
										var lb=$('body').find('#fh_lbox');
												// REMOVE LOADER IF IT IS STILL THERE....
												lb.find('#fh_lightbox-main-loader').animate({'opacity':'0.3'},{duration:500, complete:function() {
													var $this=$(this);
													$this.remove();
												}});
												resizeMeNow();						// CAN CALLBACK FUNCION BE CALLED		
											});										
								}; 
		
					
})(jQuery);			

				
			

			   