
function suspendmembers(id,capability){
jQuery(document).ready(function(){
	var comm =jQuery("#"+capability+id).attr("checked");

	alert(comm);
	if (comm == true){
		jQuery.post("index.php","admin=true&ajax=true&members_caps=true&suspend=true&value=1&id="+id);
	} else {		
		jQuery.post("index.php","admin=true&ajax=true&members_caps=true&suspend=true&value=2&id="+id);
	}
	//	jQuery.post('index.php?wpsc_admin_action=check_form_options',post_values, function(returned_data){
	return false;
	});
}


jQuery(document).ready(function(){
		
		jQuery("#recurring_options").hide(10);
  		jQuery("#charging_options").hide(10);
  		jQuery("#keep_charging").hide(10);
        
         jQuery("#q_billing input:radio:eq(0)").click(function(){
             jQuery("#recurring_options").show(10);
             jQuery("#keep_charging").show(10);
             jQuery("#charging_options").show(10);
          });
          
          jQuery("#q_billing input:radio:eq(1)").click(function(){
               jQuery("#recurring_options").hide(10);
             jQuery("#keep_charging").hide(10);
             jQuery("#charging_options").hide(10);
          });
          
          jQuery("#keep_charging input:radio:eq(0)").click(function(){
             jQuery("#charging_options").hide(10);
          });

          jQuery("#keep_charging input:radio:eq(1)").click(function(){
             jQuery("#charging_options").show(10);
          });

  });
