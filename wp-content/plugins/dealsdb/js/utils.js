function validate_integer(input,msg)
{
	t = document.getElementById("err_msg");
	t.innerHTML = "";
	if (!isInteger(input.value) && input.value != "") 
	{
		t.innerHTML = msg;
	} 
}

function isInteger(s) 
{
	if(s == "")
		return true;
		
	return (s.toString().search(/^-?[0-9]+$/) == 0);
}

function validate_range(input,msg) {
	result = true;
	t = document.getElementById("err_msg");
	t.innerHTML = "";
	
	min=document.getElementById("price_min");
		
		if (parseInt(min.value) > input.value) {
			t.innerHTML = msg;
		}
	
}
