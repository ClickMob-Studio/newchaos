function checkMassEmailFields(curForm)
{
	if(curForm.subj.value=="")
	{
		alert("Please Enter Subject");
		return false;
	}
	if(curForm.body.value=="")
	{
		alert("Please Enter Message");
		return false;
	}
	return true;
}