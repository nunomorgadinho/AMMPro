/**
 * jquery.freshline.MiniSlide - The Mini Touchable 8in1 Item
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
		minislides: function(options) {
	
		
			
		////////////////////////////////
		// SET DEFAULT VALUES OF ITEM //
		////////////////////////////////
		var defaults = {	
			width: 876, // width of Slider
			height: 300, // height of Slider						
			slides:4,	// amount of the small slides
			padding:20,	// Padding between the Slides
			shadow:0,
			ease:'easeOutQuad',
			speed:300,
			hidetoolbar:0,
			animtype:1,			//  1: slide left-right , 2: drop down the outsliding item
			mousewheel:'off',
			timer:0
		};
		
		options = $.extend({}, $.fn.minislides.defaults, options);
		
		
		return this.each(function() {
					
			//PUT THE BANNER HOLDER IN A VARIABLE			
			var slides = $(this);					
	        var opt=options;	
			prepareSlides(slides,opt);
			if (opt.maxslides > opt.slides)
				prepareController(slides,opt);
			else
				slides.find('.controller').css({'display':'none'});
				
			// TOUCH ENABLED SCROLL
			slides.swipe( {data:slides, swipeLeft:function() { slides.find(".leftbutton").click(); }, swipeRight:function() { slides.find(".rightbutton").click(); }, allowPageScroll:"auto"} );
			
			slides.hover(function() {
				var $this=$(this);
				$this.addClass('nowIsOver');
			}, 
			function() {
				var $this=$(this);
				$this.removeClass('nowIsOver');
			});
			if (opt.timer>0)
				setInterval(function() {
										
											if (!slides.hasClass("nowIsOver")) 
												slides.find(".leftbutton").click();
										},(opt.timer));
			
		})
	}
})
		

		///////////////////////////////
		//  --  LOCALE FUNCTIONS -- //
		///////////////////////////////
		
		
		
		
		///////////////////////////////
		//  --  CREATE CONTROLLS -- //
		///////////////////////////////
		function prepareController(slides,opt) {
			var control=slides.find('.controller');
			control.wrap('<div style="position:relative;"></div>');
			var bg=$('<div class="background"></div>');
			var lb=$('<div class="leftbutton"></div>');
			var rb=$('<div class="rightbutton"></div>');
			control.append(bg).append(lb).append(rb);
			
			if (opt.mousewheel=="on") {
				slides.bind('mousewheel', function(event, delta) {
					if (delta<0) leftClick(bg,lb,rb,slides,opt);
					if (delta>0) rightClick(bg,lb,rb,slides,opt);
					return false;
				});
			}
			
			// IF LEFT PRESSED, LET ROTATE THE SLIDES
			lb.click(function() {leftClick(bg,lb,rb,slides,opt)});			
			rb.click(function() {rightClick(bg,lb,rb,slides,opt)});
			

			
			
			
			// IF RIGHT PRESSED, LET ROTATE THE SLIDES
		
			
			
			// HIDE TOOLBAR IF IT IS NOT NEEDED
			slides.hover(function() {
								var $this=$(this);
								$this.data('mouseOver','0');
								$this.data('hideT',0);
								$this.find('.controller').stop(true,true);
								if ($.browser.msie && $.browser.version >= 7 && $.browser.version < 9 ) {
									$this.find('.controller').css({'display':'block'});
								} else {
									$this.find('.controller').animate({'opacity':'1.0'},{duration:100});
								}																								
							},
							function() {
								var $this=$(this);
								$this.data('mouseOver','1');
								$this.data('hideT',0);								
							});
							
			
			slides.data('hideT',0);
			
			if (opt.hidetoolbar!=0) {
					setInterval(function() {					 
							if (slides.data('mouseOver')!='0') slides.data('hideT',(slides.data('hideT')+100));
							if (slides.data('hideT')>=opt.hidetoolbar) {												
								if ($.browser.msie && $.browser.version >= 7 && $.browser.version < 9 ) {
									slides.find('.controller').css({'display':'none'});
								} else {
									slides.find('.controller').animate({'opacity':'0.0'},{duration:400});
								}
							}					
					},100);
			}
		}
		
		
		
		//////////////////////////////
		//	 - LEFT CLICK HANDLER - //
		//////////////////////////////
		function leftClick(bg,lb,rb,slides,opt) {
		 
		 if (!opt.animation) {
				
				
				slides.find('shorty_is_on_action').find('.shorty_hide').click();
				slides.find('.slide').each(function() {
				  var $this=$(this);
				    
				  if ($this.position().left<0) {
					$this.css({'left':opt.maxsize+'px'});														  
					$this.data('actLeft',$this.position().left)
				  }
				  	
				   if ($this.hasClass('shorty_is_not_on_action')) {						
						$this.stop();						
						if ($.browser.msie && $.browser.version<9)
								$this.css({'visibility':'visible'});
							else							
								$this.css({'opacity':1});												
						
						$this.find('.shorty_video').html("");
						$this.removeClass('shorty_is_not_on_action');
					  }
					if ($this.hasClass('shorty_is_on_action')) {							
							$this.find('.shorty_full_content').hide();														
							$this.css({'width':opt.slideWidth+'px'});					
							$this.removeClass('shorty_is_on_action');
							$this.find('.shorty_video').html("");
							$this.find('.shorty_more').show();
							$this.find('.shorty_more').css({'visibility':'visible'});
					   }
				  
				});
				slides.find('.slide').each(function() {
					var $this=$(this);
					$this.stop();
					opt.animation=true;
					
					
					// ANIMATION TYPES
					if (opt.animtype==1) {
							slideToLeft($this,opt);
					 } else	{
							
							if (opt.animtype==2) { 

								  if (Math.floor($this.position().left)==0) {		
									slideToDownbyLeft($this,opt);
									
								  } else {
									slideToLeft($this,opt);
								  }
							}
					}
				});
			}
		}
		
		//////////////////////////////
		//	 - LEFT CLICK HANDLER - //
		//////////////////////////////
		function rightClick(bg,lb,rb,slides,opt) {
			 if (!opt.animation) {
				
					slides.find('shorty_is_on_action').find('.shorty_hide').click();
					
					slides.find('.slide').each(function(i) {
					  var $this=$(this);
					  
					  if (Math.floor($this.position().left)>=Math.floor(opt.maxsize)) {						
						$this.css({'left':(0-opt.slideWidth - opt.padding)+'px'});												  
						$this.data('actLeft',$this.position().left)
					  }
					if ($this.hasClass('shorty_is_not_on_action')) {						
						$this.stop();						
						if ($.browser.msie && $.browser.version<9)
								$this.css({'visibility':'visible'});
							else							
								$this.css({'opacity':1});												
						$this.find('.shorty_video').html("");
						
						$this.removeClass('shorty_is_not_on_action');
					  }
					if ($this.hasClass('shorty_is_on_action')) {							
							$this.find('.shorty_full_content').hide();														
							$this.css({'width':opt.slideWidth+'px'});					
							$this.removeClass('shorty_is_on_action');
							$this.find('.shorty_video').html("");
							$this.find('.shorty_more').show();
							$this.find('.shorty_more').css({'visibility':'visible'});
					   }
					});
					
					slides.find('.slide').each(function(i) {
						var $this=$(this);
						$this.stop();					
						opt.animation=true;
						// ANIMATION TYPES
						if (opt.animtype==1) {
								slideToRight($this,opt);
						 } else	{
								
								if (opt.animtype==2) { 
									
									  if (Math.abs(((opt.slideWidth*(opt.slides-1)) + (opt.padding*(opt.slides-1)))-Math.floor($this.position().left))<30) {			
										slideToDownbyRight($this,opt);
										
									  } else {
										slideToRight($this,opt);
									  }
								}
						}
					});
				}
			}
		
		/////////////////////////////////////
		//  --  SLIDE ANIMATION TO LEFT -- //
		/////////////////////////////////////
		function slideToLeft($this,opt) {
			del=0;
			$this.delay(del).animate({'left':($this.data('actLeft') - opt.slideWidth - opt.padding)+'px'},{duration:opt.speed, easing:opt.ease,
									complete:function() {
												opt.animation=false;
												var $this=$(this); 
												if ($this.position().left<0) {
												  $this.css({'left':opt.maxsize+'px','top':'0px'});												  
												}
												$this.data('actLeft',$this.position().left)
											}});
											
							
											
			}
			
		//////////////////////////////////////
		//  --  SLIDE ANIMATION TO RIGHT -- //
		/////////////////////////////////////
		function slideToRight($this,opt) {
			del=0;
			
			$this.delay(del).animate({'left':($this.data('actLeft') + opt.slideWidth + opt.padding)+'px'},{duration:opt.speed, easing:opt.ease,
										complete:function() {
													opt.animation=false;
													var $this=$(this); 
													if ($this.position().left>opt.maxsize) {
													  $this.css({'left':(0-opt.slideWidth)+'px'});												  
													}
													$this.data('actLeft',$this.position().left)
												}});
												
			}
		
		/////////////////////////////////////
		//  --  SLIDE ANIMATION TO LEFT -- //
		/////////////////////////////////////
		function slideToDownbyLeft($this,opt) {
				$this.animate({'top':$this.height()/3,'opacity':'0.0'},{duration:opt.speed, easing:opt.ease,
									complete:function() {
												opt.animation=false;
												var $this=$(this); 												
												$this.css({'opacity':'1.0','left':opt.maxsize+'px','top':'0px'});												  												
												$this.data('actLeft',$this.position().left)
											}});
			}
			
		
		/////////////////////////////////////
		//  --  SLIDE ANIMATION TO LEFT -- //
		/////////////////////////////////////
		function slideToDownbyRight($this,opt) {
				$this.animate({'top':$this.height()/3,'opacity':'0.0'},{duration:opt.speed, easing:opt.ease,
									complete:function() {
													opt.animation=false;
													var $this=$(this); 
													$this.css({'left':($this.data('actLeft') + opt.slideWidth + opt.padding)+'px'});	
													if ($this.position().left>opt.maxsize) 
													  $this.css({'left':(0-opt.slideWidth)+'px'});														  
													$this.css({'top':'0px','opacity':'1.0'});													
													$this.data('actLeft',$this.position().left)
												}});
			}
			
			
			
			
			
			
			
			
			
		///////////////////////////
		//  --  CREATE SLIDES -- //
		//////////////////////////
		function prepareSlides(slides,opt) {
			slides.find('ul:first').wrap('<div class="slidesholder"></div>');
			
			
			slides.find('.slidesholder').css({
						'width':3+opt.width+'px',
						'height':3+opt.height+'px',
						'overflow':'hidden',	
						'position':'relative'
						});

			opt.slideWidth = Math.floor((opt.width-((opt.slides-1)*opt.padding)) / opt.slides);					
			
			opt.maxsize = 0;
			
			opt.maxslides =0;
			
			
			// PREPARE THE SLIDES ONE BY ONE
			slides.find("ul >li").each(function(i){				
				opt.maxslides=opt.maxslides+1;
				var $this=$(this);				
				
				$this.css({'list-type':'none'});
				
				// ADD A NEW DIV TO WRAP THE SLIDE ITSELD
				$this.wrapInner('<div class="slide"></div>');
				
				var slide=$this.find('.slide');
				slide.data('iidd',i);
				
				// ADD SHADOW
				if (opt.shadow==1) slide.addClass('shadow1') 
				   else 
					if (opt.shadow==2) slide.addClass('shadow2') 
						else
						if (opt.shadow==3) slide.addClass('shadow3') 
						
				slide.data('actLeft',(i*(opt.slideWidth)+(i)*opt.padding));
				opt.maxsize = (i*(opt.slideWidth)+(i)*opt.padding);
				
				//SET THE SLIDE SETTINGS
				slide.css({'width':opt.slideWidth+'px',
										  'height':opt.height+'px',
										  'left':i*(opt.slideWidth)+(i)*opt.padding+'px',
										  'top':'0px',
										  'position':'absolute',
										  'overflow':'hidden'
										  });
								
				slide.data('radiustr',slide.css("-moz-border-radius-topright"));
				slide.data('radiusbr',slide.css("-moz-border-radius-bottomright"));
				
				slide.data('svideo',slide.find('.shorty_video').html());
				slide.find('.shorty_video').html('');
				
				// APPEND THE CONTENT TO THE TOP LEFT POSITION
				var scontent=$this.find('.hover_content');					
				scontent.css({'opacity':'0.0','position':'absolute','top':'50px','left':'0px'});				
				if ($.browser.msie && $.browser.version < 9 ) scontent.css({'display':'none'});
								
				
				// APPEND A HOVER PNG
				var hoverpng=$('<div class="hovercover" style="opacity:0;position:absolute;top:-100px;left:0px;width:'+opt.width+'px;height:'+scontent.height()+'px"></div>');			
				hoverpng.hide();
				
				
				// ADD INNES SHADOWS
				slide.append('<div class="insideshadow" style="width:'+scontent.width()+'px;height:'+scontent.height()+'px;position:absolute;top:0px;left:0px;"></div>');
				
				// ADD INNER REFLECTION
				slide.append('<div class="frame_custom_reflection" style="width:'+scontent.width()+'px;height:'+scontent.height()+'px;position:absolute;top:0px;left:0px;"></div>');				
				
				
				// IF WE HAVE THE PORTFOLIO THEME, THAN WE NEED A SPECIAL HOVER ACTION HERE
				if (slides.hasClass('minislider_portfolio') || slides.hasClass('minislider_vintage') || slides.hasClass('minislider_fabric')) {
						// ADD THE TWO NEW  DIVS TO THE SLIDE ITSELF
						slide.append(hoverpng);												
						slide.append(scontent);																
						slide.hover(
							function() {
								var $this=$(this);
								var cover=$this.find('.hovercover');						
								var cont=$this.find('.hover_content');											
								cover.stop();
								cover.animate({'opacity':'1.0','top':'0px'},{duration:400,queue:false});
								cover.show();
								cont.stop();
								cont.css({'display':'block'});
								cont.animate({'opacity':'1.0','top':'0px'},{duration:400,queue:false});
							},
							function() {
								var $this=$(this);
								var cover=$this.find('.hovercover');
								var cont=$this.find('.hover_content');
								cover.stop();
								cover.animate({'opacity':'0.0','top':'-100px'},{duration:1,queue:false});
								cont.stop();
								cont.delay(200).animate({'opacity':'0.0','top':'50px'},{duration:1,queue:false});
								if ($.browser.msie && $.browser.version < 9 ) {
									setTimeout(function(){cont.css({'display':'none'})},0);
								}
							});
					}
				
				
				
				///////////////////
				// SHORTY EXTRAS //
				//////////////////
				slide.find('.shorty_more').click(function() {
					var $this=$(this);
					
					var li_item=$(this).closest('.slidetop');
					var slide=li_item.find('.slide');					
					var ul_item=li_item.closest('ul');
					
					// ADD A CLASS TO FIND THIS ITEM LATER :)
					slide.addClass('shorty_is_on_action');
					slide.find('.shorty_video').html(slide.data('svideo'));
					
					// HIDE ALL NOT USED ITEMS HERE FIRST
					ul_item.find('.slide').each(function() {
						var $this=$(this);
						$this.css({'z-index':'100'});						
						$this.addClass('shorty_is_not_on_action');
						
						// IF THIS IS NOT OUR ITEM, WE CAN HIDE IT 
						if (!$this.hasClass('shorty_is_on_action')) {
							if ($.browser.msie && $.browser.version<9)
								$this.css({'visibility':'hidden'});
							else
								$this.animate({'opacity':'0.0'},{duration:400,queue:false});
						} 
					
					});
					
					
					// PUT THIS ITEM ON THE TOP
					slide.css({'z-index':'101'});
					
					// OPEN THE MORE CONTENT PART
				
					
					
					slide.css({
								'-webkit-border-top-right-radius':'0px',
								'-moz-border-radius-topright': '0px',
								'border-top-right-radius': '0px',	
								'-webkit-border-bottom-right-radius':'0px',
								'-moz-border-radius-bottomright': '0px',
								'border-bottom-right-radius': '0px'	
					});
					
					
					
					slide.animate({'left':'0px','width':opt.width+'px'},{duration:400,queue:false,complete:function() {
										var $this=$(this);
										var rad = $this.data('radiustr');
										var rad2 = $this.data('radiusbr');
					
										slide.css({
													'-webkit-border-top-right-radius':rad,
													'-moz-border-radius-topright': rad,
													'border-top-right-radius': rad,	
													'-webkit-border-bottom-right-radius':rad2,
													'-moz-border-radius-bottomright': rad2,
													'border-bottom-right-radius': rad2
										});
					}});					
					
					// SET THE DEFAULT SIZE 
					slide.find('.shorty_short_content').css({'width':opt.slideWidth+'px'});
					
					//CALCULATE THE PADDS HERE
					var padds=parseInt(slide.find('.shorty_full_content').css('padding-left'),0) + parseInt(slide.find('.shorty_full_content').css('padding-right'),0);
					
					// SET FULL CONTENT SIZE
					slide.find('.shorty_full_content').css({'position':'absolute','top':'0px','left':opt.slideWidth+'px','height':opt.height+'px','width':(opt.width-opt.slideWidth-padds)+'px'});
					
					// SHOW THE FULL CONTENT
					slide.find('.shorty_full_content').show();
					
					// HIDE THE "READ MORE BUTTON"
					$this.css({'visibility':'hidden'});
					return false;
				});
				
				
				
				// HIDE THE MORE CONTENT HERE
				slide.find('.shorty_hide').click(function() {
					var $this=$(this);
					
					var li_item=$(this).closest('.slidetop');
					var slide=li_item.find('.slide');					
					var ul_item=li_item.closest('ul');
					
					// ADD A CLASS TO FIND THIS ITEM LATER :)
					
					
					
					
					// HIDE ALL NOT USED ITEMS HERE FIRST
					ul_item.find('.slide').each(function() {
						var $this=$(this);
						$this.css({'z-index':'100'});						
						$this.removeClass('shorty_is_not_on_action');
						
						// IF THIS IS NOT OUR ITEM, WE CAN HIDE IT 
						if (!$this.hasClass('shorty_is_on_action')) {
							$this.find('.shorty_video').html("");
							if ($.browser.msie && $.browser.version<9)
								$this.css({'visibility':'visible'});
							else							
								$this.animate({'opacity':'1.0'},{duration:400,queue:false});
						}
					
					});
					
					slide.removeClass('shorty_is_on_action');
					
					// PUT THIS ITEM ON THE TOP
					slide.css({'z-index':'101'});
					
					
					slide.css({
								'-webkit-border-top-right-radius':'0px',
								'-moz-border-radius-topright': '0px',
								'border-top-right-radius': '0px',	
								'-webkit-border-bottom-right-radius':'0px',
								'-moz-border-radius-bottomright': '0px',
								'border-bottom-right-radius': '0px'	
					});
					
					
					// OPEN THE MORE CONTENT PART
					slide.animate({'left':slide.data('actLeft')+'px','width':opt.slideWidth+'px'},{duration:400,queue:false,complete:function() {
										var $this=$(this);
										var rad = $this.data('radiustr');
										var rad2 = $this.data('radiusbr');
					
										slide.css({
													'-webkit-border-top-right-radius':rad,
													'-moz-border-radius-topright': rad,
													'border-top-right-radius': rad,	
													'-webkit-border-bottom-right-radius':rad2,
													'-moz-border-radius-bottomright': rad2,
													'border-bottom-right-radius': rad2
										});
					}});			
					
					// SHOW THE FULL CONTENT
					slide.find('.shorty_full_content').hide();
					li_item.find('.shorty_more').css({'visibility':'visible'});
					//li_item.find('.shorty_more').show();
					
					return false;
				});
				
				
				
				// LIFT ME TRICK :)
				
			
				slide.find('.liftme').each(function()
				 {
				 	var $this =$(this);
					$this.hover(
						function() {
								var $this =$(this);								
								$this.stop();
								$this.animate({'top':-13+'px'},{duration:350,queue:false,easing:'easeInOutQuad'});						
								if ($.browser.version<9 && $.browser.msie)  {
								
								} else {
									//$this.canvas.style.marginTop=10+"px";
								}
								
						},
						function() {
								var $this =$(this);									
								
								$this.stop();
								$this.animate({'top':0+'px'},{duration:350,queue:false,easing:'easeOutBack'});							
								
						});					
				});
			});
		}
					
})(jQuery);			

				
			

			   