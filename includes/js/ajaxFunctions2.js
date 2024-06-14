function ajaxCheckResult(campos, username, id, single) {
	single = single || false;
    var ajaxRequest=getAjaxRequest();
	if (!ajaxRequest) alert("Your browser doesn't support this chat.");
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
            var ajaxDisplay = document.getElementById("chatTextContainer");
            if (ajaxRequest.responseText=='0') {
                ajaxDisplay.innerHTML='Error...';
            } else {
                ajaxDisplay.innerHTML = ajaxRequest.responseText;
            }
		}
	};
    var ajaxRightNow = new Date();
    ajaxRequest.open("GET", "AjaxPHP.php?code="+campos+"&username="+username+"&user="+id+"&ajaxRandomTimestamp=" + ajaxRightNow.getTime(), true);
	ajaxRequest.send(null);
	if (!single) {
		setTimeout("ajaxCheckResult('"+campos+"', '"+username+"','"+id+"')",2500);
	}
}

      
function ajaxCheckModOnline(campos) {
	var ajaxRequest2=getAjaxRequest();
	if (!ajaxRequest2)
		alert("Your browser doesn't support this chat.");
	// Create a function that will receive data sent from the server
	
	ajaxRequest2.onreadystatechange = function(){
		if(ajaxRequest2.readyState == 4){
            var ajaxDisplay = document.getElementById("onlineContainer");
            if (ajaxRequest2.responseText=='0') {
                ajaxDisplay.innerHTML='Error...';
            } else {
            	if (ajaxRequest2.responseText.indexOf("my_code", 0) != -1) {
            		window.location.href='index.php';
            	} 
            	else 
            	{
					ajaxDisplay.innerHTML=ajaxRequest2.responseText;
				}
            }
		}
	}
	
    var ajaxRightNow2 = new Date();
    ajaxRequest2.open("GET", "AjaxPHP2.php?userid="+campos+"&ajaxRandomTimestamp=" + ajaxRightNow2.getTime()+"&hide=1", true);
	ajaxRequest2.send(null);
	setTimeout("ajaxCheckModOnline('"+campos+"')",15000);
}
function ajaxCheckOnline(campos) {
	var ajaxRequest2=getAjaxRequest();
	if (!ajaxRequest2)
		alert("Your browser doesn't support this chat.");
	// Create a function that will receive data sent from the server
	
	ajaxRequest2.onreadystatechange = function(){
		if(ajaxRequest2.readyState == 4){
            var ajaxDisplay = document.getElementById("onlineContainer");
            if (ajaxRequest2.responseText=='0') {
                ajaxDisplay.innerHTML='Error...';
            } else {
            	if (ajaxRequest2.responseText.indexOf("my_code", 0) != -1) {
            		window.location.href='index.php';
            	} 
            	else 
            	{
					ajaxDisplay.innerHTML=ajaxRequest2.responseText;
				}
            }
		}
	}
	
    var ajaxRightNow2 = new Date();
    ajaxRequest2.open("GET", "AjaxPHP2.php?userid="+campos+"&ajaxRandomTimestamp=" + ajaxRightNow2.getTime(), true);
	ajaxRequest2.send(null);
	setTimeout("ajaxCheckOnline('"+campos+"')",15000);
}

function ajaxCheckUser(userid,buttonid,buttonvalue) {
	var ajaxRequest3=getAjaxRequest();
	if (!ajaxRequest3) alert("Your browser doesn't support this chat.");
	// Create a function that will receive data sent from the server
	ajaxRequest3.onreadystatechange = function(){
		if(ajaxRequest3.readyState == 4){
			var ajaxDisplay = document.getElementById("usernameContainer");
			var submitButton = document.getElementById(buttonid);

			if (ajaxRequest3.responseText=='0') {
				ajaxDisplay.innerHTML='Error...';
				
			} else {
				ajaxDisplay.innerHTML=ajaxRequest3.responseText;
							
				if(ajaxRequest3.responseText != "<font color='red'>User not found!</font>")
				{
					submitButton.disabled = false;
					submitButton.value = buttonvalue;
				} 
				else if(ajaxRequest3.responseText != "<font color='red'>You cannot target yourself.</font>")
				{
					submitButton.disabled = false;
					submitButton.value = buttonvalue;
				} 
				else {
					submitButton.value = "Invalid user id!";
				}
				
			}
		} else if(ajaxRequest3.readyState == 1){
			var ajaxDisplay = document.getElementById("usernameContainer");
			var submitButton = document.getElementById(buttonid);
			ajaxDisplay.innerHTML="Waiting for name...";
			submitButton.disabled = true;
			submitButton.value = "Waiting...";
		}
	}
	var ajaxRightNow3 = new Date();
	ajaxRequest3.open("GET", "AjaxPHP3.php?userid="+userid+"&ajaxRandomTimestamp=" + ajaxRightNow3.getTime(), true);
	ajaxRequest3.send(null);
}


function sendModAjax(msg, code) {
	msg=msg.replace(/%/g, "perc.");
	msg=encodeURIComponent(msg);
	var ajaxRequest1=getAjaxRequest();
	if (!ajaxRequest1) alert("Your browser doesn't support this chat.");
	// Create a function that will receive data sent from the server
	ajaxRequest1.onreadystatechange = function(){
		if(ajaxRequest1.readyState == 4){
		}
	}
    var ajaxRightNow1 = new Date();
    ajaxRequest1.open("GET", "AjaxPHP1.php?msg="+msg+"&ModTeam=1&code="+code+"&ajaxRandomTimestamp=" + ajaxRightNow1.getTime(), true);
	ajaxRequest1.send(null);
}


function sendAjax(msg, code) {
	msg=msg.replace(/%/g, "perc.");
    var ajaxRightNow1 = new Date();
	var lang = $("#chatLang").val();
	$.getJSON(
		"AjaxPHP1.php?lang="+lang+"&msg="+msg+"&code="+code+"&ajaxRandomTimestamp=" + ajaxRightNow1.getTime(),
		function (result) {
			if (!result.success && result.message) {
				$.alert(result.message, 'Error');
			} else if (!result.success) {
				$.alert('An unknown error happened in chat, please contact an admin.', 'Error');
			} else {
				ajaxCheckResult(chatCode, chatUsername, chatId, true);
			}
		}
	);
}

function getAjaxRequest() {
    var ajaxRequest;
    try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Browser doesn't support AJAX
				return false;
			}
		}
	}
    return ajaxRequest;
}


function ajaxCheckGang(gangid,buttonid,buttonvalue) {
	var ajaxRequest3=getAjaxRequest();
	if (!ajaxRequest3) alert("Your browser doesn't support this chat.");
	// Create a function that will receive data sent from the server
	ajaxRequest3.onreadystatechange = function(){
		if(ajaxRequest3.readyState == 4){
			var ajaxDisplay = document.getElementById("gangnameContainer");
			var submitButton = document.getElementById(buttonid);

			if (ajaxRequest3.responseText=='0') {
				ajaxDisplay.innerHTML='Error...';
				
			} else {
				ajaxDisplay.innerHTML=ajaxRequest3.responseText;
							
				if(ajaxRequest3.responseText.indexOf('_ERROR|') != -1)
				{
					ajaxDisplay.innerHTML=ajaxRequest3.responseText.replace('_ERROR|','');
				} 
				else
				{
					ajaxDisplay.innerHTML=ajaxRequest3.responseText;
				} 
				submitButton.disabled = false;
				submitButton.value = buttonvalue;
			}
		} else if(ajaxRequest3.readyState == 1){
			var ajaxDisplay = document.getElementById("gangnameContainer");
			var submitButton = document.getElementById(buttonid);
			ajaxDisplay.innerHTML=CONST_WAITING_FOR_NAME;
			submitButton.disabled = true;
			submitButton.value = CONST_WAITING;
		}
	}
	var ajaxRightNow3 = new Date();
	ajaxRequest3.open("GET", "AjaxCheckGang.php?gangid="+gangid+"&ajaxRandomTimestamp=" + ajaxRightNow3.getTime(), true);
	ajaxRequest3.send(null);
}