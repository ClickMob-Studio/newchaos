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

function str_pad( input, pad_length, pad_string, pad_type ) {
    // Returns input string padded on the left or right to specified length with pad_string  
    // 
    // version: 905.2617
    // discuss at: http://phpjs.org/functions/str_pad
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // + namespaced by: Michael White (http://getsprink.com)
    // *     example 1: str_pad('Kevin van Zonneveld', 30, '-=', 'STR_PAD_LEFT');
    // *     returns 1: '-=-=-=-=-=-Kevin van Zonneveld'
    // *     example 2: str_pad('Kevin van Zonneveld', 30, '-', 'STR_PAD_BOTH');
    // *     returns 2: '------Kevin van Zonneveld-----'
    var half = '', pad_to_go;

    var str_pad_repeater = function(s, len) {
        var collect = '', i;

        while(collect.length < len) {collect += s;}
        collect = collect.substr(0,len);

        return collect;
    };

    input += '';

    if (pad_type != 'STR_PAD_LEFT' && pad_type != 'STR_PAD_RIGHT' && pad_type != 'STR_PAD_BOTH') { pad_type = 'STR_PAD_RIGHT'; }
    if ((pad_to_go = pad_length - input.length) > 0) {
        if (pad_type == 'STR_PAD_LEFT') { input = str_pad_repeater(pad_string, pad_to_go) + input; }
        else if (pad_type == 'STR_PAD_RIGHT') { input = input + str_pad_repeater(pad_string, pad_to_go); }
        else if (pad_type == 'STR_PAD_BOTH') {
            half = str_pad_repeater(pad_string, Math.ceil(pad_to_go/2));
            input = half + input + half;
            input = input.substr(0, pad_length);
        }
    }

    return input;
}


jQuery.fn.shake = function(intShakes , intDistance, intDuration ) {
    this.each(function() {
        var jqNode = $(this);
        jqNode.css({position: 'relative'});
        for (var x=1; x<=intShakes; x++) {
            jqNode.animate({ left: (intDistance * -1) },(((intDuration / intShakes) / 4)))
            .animate({ left: intDistance },((intDuration/intShakes)/2))
            .animate({ left: 0 },(((intDuration/intShakes)/4)));
        }
    });
    return this;
}

function countdown(secs, div, format, onZeroAction) {
    if (secs > 0) {
        DisplayStr = format.replace(/%%D%%/g, Math.floor((secs / 86400) % 100000));
        DisplayStr = DisplayStr.replace(/%%H%%/g, Math.floor((secs / 3600) % 24));
        DisplayStr = DisplayStr.replace(/%%M%%/g, str_pad(Math.floor((secs / 60) % 60), 2, '0', 'STR_PAD_LEFT'));
        // If we aren't including seconds we only need to update once per minute
        var refreshTime = 60000;
        if (format.indexOf('%%S%%') !== -1) {
            refreshTime = 1000;
            DisplayStr = DisplayStr.replace(/%%S%%/g, str_pad(Math.floor(secs % 60), 2, '0', 'STR_PAD_LEFT'));
        }
        document.getElementById(div).innerHTML = DisplayStr;
        setTimeout("countdown("+(secs - 1)+", '"+div+"', '"+format+"', '"+onZeroAction+"')", refreshTime);
    } else {
        DisplayStr = format.replace(/%%D%%/g, '0');
        DisplayStr = DisplayStr.replace(/%%H%%/g, '00');
        DisplayStr = DisplayStr.replace(/%%M%%/g, '00');
        DisplayStr = DisplayStr.replace(/%%S%%/g, '00');
        document.getElementById(div).innerHTML = DisplayStr;
        if (onZeroAction === 'refresh') {
            setTimeout(function () {
                window.location.reload();
            }, 1000);
        }
    }
}

jQuery.Validation = {
    is_empty: function(text) {
        return ( jQuery.trim(text).length == 0 || text == null || text == undefined) ;
    },
    is_email: function(email) {
        return ( email.match(/^[A-Za-z0-9\._\-+]+@[A-Za-z0-9_\-+]+(\.[A-Za-z0-9_\-+]+)+$/)) ;
    },
    is_number: function(num) {
        return ( num.match(/^[0-9]+$/)) ;
    },
    is_double: function(num) {
        return ( num.match(/^[0-9]*\.[0-9]*$/)) ;
    }
};

function incrementItem(element) {
    var input = $(element).parent().find('input[name="qty"]');
    input.val(parseInt(input.val()) + 1);
    return false;
}

function decrementItem(element) {
    var input = $(element).parent().find('input[name="qty"]');
    if (input.val() > 1) {
        input.val(parseInt(input.val()) - 1);
    }
    return false;
}

function confetti(element) {
    function random(max){
        return Math.random() * (max - 0) + 0;
    }

    $(element).css('position', 'relative');

    var c = document.createDocumentFragment();
    for (var i=0; i<100; i++) {
        var styles = 'transform: translate3d(' + (random(700) - 350) + 'px, ' + (random(500) - 225) + 'px, 0) rotate(' + random(180) + 'deg);\
                  background: hsla('+random(360)+',100%,50%,1);\
                  animation: bang 850ms ease-out forwards;\
                  opacity: 0';

        var e = document.createElement("i");
        e.classList.add('confetti');
        e.style.cssText = styles.toString();
        c.appendChild(e);
    }

    $(element).append(c);
}