function validate(){
	var errors = [];
	if(checkUsername(0) === false)
		errors.push("Your username has to be between 1 and 20 characters.");
	if(checkExistingUsername() === false)
		errors.push("This username already exists.");
	if(checkPassword() === false)
		errors.push("Your password has to be at least 6 characters.");
	if(checkConfPassword() === false)
		errors.push("Your password and confirm password do not match.");
	if(checkEmail() === false)
		errors.push("Double check your email, it either exists on this game or it is not properly formatted.");
	if(errors.length === 0)
		return true;
	else{
		document.getElementById("errors").innerHTML = errors.join("<br />");
		shakeMe();
		return false;
	}
}
function checkUsername(which){
	var username = document.getElementById("username");
	var check = username.value.trim();
	var length = check.length;
	if(length < 1){
		username.classList.add("fail");
		username.classList.remove("pass");
		return false;
	}
	if(length > 20){
		username.classList.add("fail");
		username.classList.remove("pass");
		return false;
	}
	if(which == 1)
		if(checkExistingUsername() === false){
			username.classList.add("fail");
			username.classList.remove("pass");
			return false;
		}
	username.classList.add("pass");
	username.classList.remove("fail");	
	return true;
}
function checkExistingUsername(){
	var username = document.getElementById("username").value.trim();
	var response = makeRequest("regCheck.php?username=" + username);
	if(response == "pass")
		return true;
	else
		return false;
}
function checkPassword(){
	var pass = document.getElementById("pass");
	var check = pass.value.trim();
	var length = check.length;
	if(length < 6){
		pass.classList.add("fail");
		pass.classList.remove("pass");
		return false;
	}
	pass.classList.add("pass");
	pass.classList.remove("fail");	
	return true;
}
function checkConfPassword(){
	var pass = document.getElementById("pass");
	var p1 = pass.value.trim();
	var conf = document.getElementById("conpass");
	var p2 = conf.value.trim();
	if(p1 != p2){
		pass.classList.add("fail");
		pass.classList.remove("pass");
		conf.classList.add("fail");
		conf.classList.remove("pass");
		return false;
	}
	conf.classList.add("pass");
	conf.classList.remove("fail");	
	return true;
}
function checkEmail(){
	var email = document.getElementById("email");
	var check = email.value.trim();
	var response = makeRequest("regCheck.php?email=" + check);
	if(response == "pass"){
		email.classList.add("pass");
		email.classList.remove("fail");
		return true;
	} else {
		email.classList.add("fail");
		email.classList.remove("pass");
		return false;
	}
}
function makeRequest(url){
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open( "GET", url, false );
	xmlHttp.send( null );
	return xmlHttp.responseText;
}
function shakeMe() {
	var move = 5;
	var shaker = document.getElementById("regForm");
	shaker.classList.add("shake");
	setTimeout(function() {
		shaker.classList.remove("shake");
	}, 300);
}