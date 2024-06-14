function checkall(obj,objName,myform)
{
    for(i=0;i<document.forms[myform].elements.length;i++)
    {
        if(document.forms[myform].elements[i].name==objName)
            if(obj.checked)
                document.forms[myform].elements[i].checked=true;
            else
                document.forms[myform].elements[i].checked=false;
    }
}

/**
* function used to post variables through javascript
*/

function postForm(url, vars)
{
    var form = document.createElement('form');
    form.method = 'post';
    form.action = url;

    for(var fieldname in vars)
    {
        var input = document.createElement('input');
        input.type='hidden';
        input.name=fieldname;
        input.value=vars[fieldname];
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    
    form.submit();
}