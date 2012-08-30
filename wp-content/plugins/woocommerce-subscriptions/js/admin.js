jQuery(document).ready(function($){

	$.extend({
		getParameterByName: function(name) {
			name = name.replace(/[\[]/, '\\\[').replace(/[\]]/, '\\\]');
			var regexS = '[\\?&]' + name + '=([^&#]*)';
			var regex = new RegExp(regexS);
			var results = regex.exec(window.location.search);
			if(results == null) {
				return '';
			} else {
				return decodeURIComponent(results[1].replace(/\+/g, ' '));
			}
		},
		showHideSubscriptionMeta: function(){
			if ($('select#product-type').val()==WCSubscriptions.productType) {
				$('.show_if_simple').show();
				$('.show_if_subscription').show();
				$('.grouping_options').hide();
				$('.options_group.pricing').hide();
			} else {
				$('.show_if_subscription').hide();
			}
		},
		setSubscriptionLengths: function(){
			var selectedLength = $('#_subscription_length').val();
			$('#_subscription_length').empty();
			$.each(WCSubscriptions.subscriptionLengths[$('#_subscription_period').val()], function(length,description) {
				if(parseInt(length) == 0 || 0 == (parseInt(length) % parseInt($('#_subscription_period_interval').val())))
					$('#_subscription_length').append($('<option></option>').attr('value',length).text(description));
			});
			$('#_subscription_length').val(selectedLength);
		},
		setTrialPeriods: function(){
			var selectedTrialLength = $('#_subscription_trial_length').val();
			$('#_subscription_trial_length').empty();
			$.each(WCSubscriptions.trialLengths[$('#_subscription_period').val()], function(length,description) {
				if(parseInt(length) == 0 || 0 == (parseInt(length) % parseInt($('#_subscription_period_interval').val())))
					$('#_subscription_trial_length').append($('<option></option>').attr('value',length).text(description));
			});
			$('#_subscription_trial_length').val(selectedTrialLength);
		}
	});

	if($('.options_group.pricing').length > 0) {
		$.showHideSubscriptionMeta();
		$.setSubscriptionLengths();
		$.setTrialPeriods();
	}

	// Move the subscription pricing section to the same location as the normal pricing section
	$('.options_group.subscription_pricing').insertBefore($('.options_group.pricing'));

	// Update subscription ranges when subscription period or interval is changed
	$('#_subscription_period, #_subscription_period_interval').change(function(){
		$.setSubscriptionLengths();
		$.setTrialPeriods();
	});

	$('body').bind('woocommerce-product-type-change',function(){
		$.showHideSubscriptionMeta();
	});

	if($.getParameterByName('select_subscription')=='true'){
		$('select#product-type option[value="'+WCSubscriptions.productType+'"]').attr('selected', 'selected');
		$('select#product-type').trigger('woocommerce-product-type-change');
		$('select#product-type').select();
	}

	$('#posts-filter').submit(function(){
		if($('[name="post_type"]').val()=='shop_order' && $('[name="action"]').val()=='trash'){
			var containsSubscription = false;
			$('[name="post[]"]:checked').each(function(){
				if($('[name="contains_subscription"]',$('#post-'+$(this).val())).val()=='true'){
					containsSubscription = true;
					return false;
				}
			});
			if(containsSubscription)
				return confirm(WCSubscriptions.bulkTrashWarning);
		}
	});

	$('.order_actions .submitdelete').click(function(){
		if($('[name="contains_subscription"]').val()=='true')
			return confirm(WCSubscriptions.bulkTrashWarning);
	});

	$(window).load(function(){
		if($('[name="contains_subscription"]').length > 0 && $('[name="contains_subscription"]').val()=='true'){
			$('#woocommerce-order-items #add_item_id').hide();
			$('#woocommerce-order-items #add_item_id_chzn').hide();
			$('#woocommerce-order-items .add_shop_order_item').hide();
		}
	});
});