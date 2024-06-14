/**
 * @author Admin
 */
function AJAX(param, div){
	this.httpObject = null;
	this.getHTTPObject = getHTTPObject;
	this.setOutput = setOutput;
	this.Search = Search;
	this.param = param;
	this.div = div;
	this.OnReady = null;
	this.Form=Form;
	

	function Form(form)
	{
	      strSend="";
	     for(i=0;i<document.getElementById(form).elements.length;i++)
		 {
		  	switch(document.getElementById(form).elements[i].type)
			{
				case "checkbox":
					strSend = strSend + "&" + document.getElementById(form).elements[i].name + "=" + document.getElementById(form).elements[i].checked;
				default:
					strSend = strSend + "&" + document.getElementById(form).elements[i].name + "=" + document.getElementById(form).elements[i].value;
			}
	      }
		  return strSend;
		  
	}
	
	function getHTTPObject(){
		if (window.ActiveXObject) 
			return new ActiveXObject("Microsoft.XMLHTTP");
		else 
			if (window.XMLHttpRequest) 
				return new XMLHttpRequest();
			
			else {
				alert("Your browser does not support AJAX.");
				return null;
			}
	}
	
	
	function setOutput(httpObject,div,callback){
		if (httpObject.readyState == 4) {
			document.getElementById(div).innerHTML = httpObject.responseText;
			if (document.getElementById('run_'+div)) 
				eval(document.getElementById('run_'+div).innerHTML);
			if(callback!=null)
				callback();
		}
	}
	function Search(){
	
		
		this.httpObject = this.getHTTPObject();
		if (this.httpObject != null) {
			this.httpObject.open("GET", this.param, true);
			this.httpObject.send(null);
			div=this.div;
			httpObject=this.httpObject;
			callback=this.OnReady;
			this.httpObject.onreadystatechange = function(){setOutput(httpObject,div,callback)};
		}
	}
}	