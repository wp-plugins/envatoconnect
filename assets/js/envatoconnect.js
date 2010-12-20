
// JQUERY ANIMATION EXTENSIONS
// ======================================================================
jQuery.each({
	slideFadeIn: { opacity: 'show', height: 'show' },
	slideFadeOut: { opacity: 'hide', height: 'hide' },
	slideFadeToggle: { opacity: 'toggle', height: 'toggle' }
}, function( name, props ) {
	jQuery.fn[ name ] = function( speed, easing, callback ) {
		return this.animate( props, speed, easing, callback );
	};
});


// ENVATO CONNECT
// ======================================================================
jQuery(document).ready(function($) {

	/* FANCYBOX (OVERLAY)
	-------------------------------------- */
	$('.envatoconnect_button').fancybox({
		'padding': 20,
		'overlayColor': '#fff',
		'overlayOpacity': 0.9,
		'scrolling': 'no'
	});
	
	
	/* BINDS FUNCTION TO AJAXED CONTENT
	-------------------------------------- */
	$('#fancybox-content > div').livequery(function() {	
	
		/* LOGIN FORM
		-------------------------------------- */
		$('#envatoconnect_form', this).submit(function() {
			var error;
			
			error = false
			
			$('.required', this).removeClass('error')
			$('.envatoconnect_form_error').slideFadeOut(function() {
				$(this).remove();	
			});
			
			$('.required', this).each(function() {	
				var fieldName = $(this).attr('name'),
					fieldLabel = $(this).prev().text();
					
				if( $.trim($(this).val()) == '' ) {
					$(this).addClass('error');
					error = true;
				} else if( $(this).hasClass('email') ) {	
					if( !$.trim( $(this).val() ).match(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/) ) {
						$(this).addClass('error');
						error = true;
					}
				}
			});
			
			if(!error){
				var fieldsVal = $(this).serialize();
				
				$('.envatoconnect_form_wrap *', this).fadeTo(300, 0.6);
				$('.envatoconnect_form_wrap', this).addClass('loading');
				
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: fieldsVal,
					success: function(result) {					
						if(result === 'ERROR1') {
							$('<div class="envatoconnect_form_error">Invalid username or API key.</div>').prependTo('#envatoconnect_form').hide().slideFadeIn(200);	
						}
						else {
							$('#envatoconnect_form').before('<div class="envatoconnect_form_success">You have connected successfully. <em class="desc">Please wait for few seconds to redirected you </em></div>').hide().slideFadeIn(200);	
							$('#envatoconnect_form').slideFadeOut(500, function() {
								
								$(this).remove();
								
								interval = window.setInterval(function(){
									var text = $('em.desc').text();
									if (text.length < 49){
										$('em.desc').text(text + '.');					
									} else {
										$('em.desc').text('Please wait for few seconds to redirected you ');				
									}
								}, 300);
								
								setTimeout(function() {
									window.clearInterval(interval);
									window.location.href = result;
								}, 3000);
							});
						}
						
						$('.envatoconnect_form_wrap *').fadeTo(200, 1);
						$('.envatoconnect_form_wrap').removeClass('loading');
					}
				});
			}
			
			return false;
			
		});
		
		
		/* SIGNUP FORM
		-------------------------------------- */
		$('#envatoconnect_signup', this).submit(function() {
			var marketplace = $('#envatoconnect_marketplace').val();
			window.location = marketplace;
			return false;
		});
	});
});