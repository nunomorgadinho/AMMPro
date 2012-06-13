
function textCounter(field,cntfield,maxlimit) { 
	if (field.value.length > maxlimit) 
	field.value = field.value.substring(0, maxlimit); 
	else 
	cntfield.value = maxlimit - field.value.length; 
}
