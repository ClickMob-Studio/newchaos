function numbersonly(myfield, e, dec) {
    var key;
    var keychar;
    if (window.event)
        key = window.event.keyCode;
    else if (e)
        key = e.which;
    else
        return true;
    keychar = String.fromCharCode(key);
    if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27))
        return true;
    else if ((("0123456789").indexOf(keychar) > -1))
        return true;
    else if (dec && (keychar == ".")) {
        myfield.form.elements[dec].focus();
        return false;
    } else
        return false;
}
function actConfirm(num, type, apts, redirect) {
    var ask = window.confirm('Are you sure you want to buy ' + num + ' ' + type + ' for ' + apts + ' Activity Points?');
    if (ask) {
        window.location = redirect;
    }
}
function ptsConfirm(num, type, apts, redirect) {
    var ask = window.confirm('Are you sure you want to buy ' + num + ' ' + type + ' for $' + apts);
    if (ask) {
        window.location = redirect;
    }
}
function ganghitlistEdit(id) {
    if ($(".noedit" + id).length) {
        $(".edit" + id).css("display", "block");
        $(".noedit" + id).css("display", "none");
    }
}
function ganghitlistEditSubmit(id) {
    if ($('#edit' + id).val() != '') {
        $.post("ajax_hitlistedit.php", { 'hlid': id, 'edittext': $('#edit' + id).val() }, function (d) {
            $(".noedit" + id).html(d);
            $(".noedit" + id).css("display", "block");
            $(".edit" + id).css("display", "none");
        });
    }
    return false;
}
function showhousing(uid) {
    $.post('ajax_port.php', { 'uid': uid }, function (d) {
        $("#housingport").html(d);
    });
}
function insertAtCursor(text, num) {
    var field = document.getElementById("reply");
    field.focus();
    var val = field.value;
    var selStart = field.selectionStart;
    var caretPos = selStart + text.length;
    field.value = val.slice(0, selStart) + text + val.slice(field.selectionEnd);
    field.setSelectionRange(caretPos, caretPos - num);
}
function hideemojis() {
    $("#emojis").css("display", "none");
    $("#hemojis").css("display", "none");
    $("#semojis").css("display", "block");
    $.post("ajax_emojis.php?hide");
    return false;
}
function showemojis() {
    $("#emojis").css("display", "block");
    $("#hemojis").css("display", "block");
    $("#semojis").css("display", "none");
    $.post("ajax_emojis.php?show");
    return false;
}
function sendGmail() {
    if ($('#reply').val() != '') {
        var ts = new Date().getTime();
        $.post("ajax_gc.php?ts=" + ts, { 'msg': $('#reply').val() }, function (d) {
            if (d) {
                var myArr = d.split('|-|-|');

                console.log(myArr[0]);
                console.log(myArr[1]);

                $('#chat_block').prepend(myArr[1]);
                $('#chat_block div#t' + ts).slideDown(500);
                $('#reply').val('');
                $('#reply').focus();
                lastGmailID = myArr[0];
            }
        });
    }
    return false;
}
function syncGmail() {
    var ts = new Date().getTime();
    $.get("ajax_gc.php?ts=" + ts + "&lastID=" + lastGmailID, function (d) {
        var myArr = d.split('|-|-|');
        if (myArr[2]) {
            $('#chat_block').prepend('<div id="t' + ts + '" style="display:none">' + myArr[2] + '</div>');
            $('#chat_block div#t' + ts).slideDown(500);
            lastGmailID = myArr[1];
        }
        if (myArr[0]) {
            $('#gccontainer').html(myArr[0]);
        }
        setTimeout("syncGmail()", 1000);
    });
    $('.rating-btn').on('click', function () {
        var post_id = $(this).data('id');
        var btn = $(this);
        action = $(this).attr('data-action');
        $.ajax({
            url: 'ajax_gc.php',
            type: 'post',
            data: {
                'action': action,
                'post_id': post_id
            },
            success: function (data) {
                res = JSON.parse(data);
                switch (action) {
                    case 'like':
                        a = 'unlike'
                        b = 'dislike'
                        break;
                    case 'dislike':
                        b = 'undislike'
                        a = 'like'
                        break;
                    case 'unlike':
                        a = 'like'
                        b = 'dislike'
                        break;
                    case 'undislike':
                        b = 'dislike'
                        a = 'like'
                }
                $('.like').attr('data-action', a)
                $('.dislike').attr('data-action', b)
                if (action == 'like' || action == 'dislike') {
                    btn.removeClass('far');
                    btn.addClass('fas');
                    btn.siblings('i.fas').removeClass('fas').addClass('far');
                } else if (action == 'unlike' || action == 'undislike') {
                    btn.removeClass('fas');
                    btn.addClass('far');
                    btn.siblings('i.fas').removeClass('fas').addClass('far');
                }
                btn.siblings('span.likes').text(res.likes);
                btn.siblings('span.dislikes').text(res.dislikes);
            }
        });
    });
}
function typing() {
    if ($('#reply').val() == "")
        $.get("ajax_globaltyping.php?is=0");
    else
        $.get("ajax_globaltyping.php?is=1");
}
function tooltips() {
    $('[rel="tipsy"]').tipsy({ html: 'true' });
    $('[rel="tipsy-w"]').tipsy({ gravity: 'w' });
    $('[rel="tipsy-e"]').tipsy({ gravity: 'e' });
    $('[rel="tipsy-s"]').tipsy({ gravity: 's', html: 'true' });
}
function notepad() {
    $.post("ajax_notepad.php", { bbcode: $('#reply').val() }, function (d) {
        $("#rtn").html(d);
    });
    return false;
}
function changeContactNote(id) {
    $.post("ajax_contactlist.php", { note: $('#' + id + 'notes').val(), id: id }, function (d) {
        $("#rtn").html(d);
    });
    setTimeout(emptyRtn, 3000);
}
function emptyRtn() {
    $("#rtn").html('<br /><br />');
}
function addmission() {
    $.post("ajax_managemissions.php", { addmissionform: 'mmhhmm' }, function (d) {
        $("#rtn").html(d);
    });
}
function addobjective() {
    $.post("ajax_managemissions.php", { addobjective: 'mmhhmm', oo: $('#oo').val(), howmanytodo: $('#howmanytodo').val(), po: $('#po').val(), missionpayout: $('#missionpayout').val() }, function (d) {
        $("#dbinput").append(d);
        $('#missionpayout').val('');
        $('#howmanytodo').val('');
    });
}
function addmissionfinal() {
    $.post("ajax_managemissions.php", { addmissionfinal: 'mmhhmm', name: $('#missionname').val(), type: $('#missiontype').val(), objective: $('#dbinput').html() }, function (d) {
        var results = d.split('|');
        if (results[0] == 'success') {
            $("#errorsuccess").html(results[1]);
            $('#dbinput').html('');
        } else {
            $("#errorsuccess").html(results[1]);
        }
    });
}
function suggest() {
    $.post("ajax_managemissions.php", { suggest: 'mmhhmm', whatcur: $('#po').val(), obj: $('#oo').val(), howmany: $('#howmanytodo').val() }, function (d) {
        $('#missionpayout').val(d);
    });
}
function calcEXP() {
    $.post("ajax_expcalc.php", { level: $("#levelcalc").val() }, function (d) {
        $("#levelrtn").html(d);
    });
}

$(document).ready(function () {
    $('.poll').on('submit', function (event) {
        event.preventDefault();
        var payload = $(this).serializeArray();
        var form = $(this);
        $(this).find('.radiobuttons').hide();
        $(this).find('button').hide();
        $.ajax({
            url: "ajax_vote.php",
            type: "POST",
            data: payload,
            success: function (result) {
                $(form).next('.results').html(result);
                form.remove();
            }
        });
    });

    $('#acrimebtn').on('mousedown', function (e) {
        e.preventDefault()
        var crime = $('#scrime').val();
        start(crime);
    });

    $('#acrimebtn').on('ontouchstart', function (e) {
        e.preventDefault()
        var crime = $('#scrime').val();
        start(crime);
    });

    $('#addchoice').on('click', function (e) {
        e.preventDefault();
        $('.choices').append('<input class="ic" type="text" name="poll_choice[]"/>');
    });

    $('#addpoll').on('click', function (e) {
        e.preventDefault();
        $(this).hide();
        $('#poll').css('display', 'inline-grid');
        $('#poll_title').prop('required', true);
        $('.ic').prop('required', true);
        $('#poll_finish').prop('required', true);
    });

    // $('#autocomplete').on('keyup', function(e) {
    //     unvalidInputKeys=[8,9,13,16,17,18,19,20,27,33,34,35,36,37,38,39,40,45,46,91,92,93,112,113,114,115,116,117,118,119,120,121,122,123,144,145];
    //     $.ajax({
    //         url: "ajax_search.php",
    //         type: "POST",
    //         dataType:"json",
    //         data: {
    //             "term" : $(this).val()
    //         },
    //         success: function (result) {

    //             $.fn.selectRange = function(start, end) {
    //                 var f = document.getElementById($(this).attr('id'))
    //                 if (!f) return;
    //                 else if (f.setSelectionRange) { f.focus(); f.setSelectionRange(start, end); }
    //                 else if (f.createTextRange) { var range = f.createTextRange(); range.collapse(true); range.moveEnd('character', end); range.moveStart('character', start); range.select(); }
    //                 else if (f.selectionStart) { f.selectionStart = start; f.selectionEnd = end; }
    //                 return $(f); };

    //             if($('#autocomplete').val().length>0&&!unvalidInputKeys.includes(e.keyCode)) {
    //                 for(i=0;i<result.length;i++) {
    //                     if(result[i].startsWith($('#autocomplete').val())) {
    //                     userTypedLength=$('#autocomplete').val().length;
    //                     $('#autocomplete').val(result[i]);
    //                     $('#autocomplete').selectRange(userTypedLength, result[i].length);
    //                     return;
    //                     }
    //                 }
    //             }

    //         },
    //     });
    // });



    //if(! /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {

    // if(!/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
    // || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4)))
    // {
    // 	$("#sortable").sortable({helper: 'clone'});
    $("#sortable").sortable({
        update: function (event, ui) {
            var sortedIDs = $("#sortable").sortable("toArray");
            var payload = [];
            payload.push(
                {
                    name: 'method',
                    value: 'update'
                },
                {
                    name: 'data',
                    value: sortedIDs
                }
            );

            $.ajax({
                url: "ajax_savemenu.php",
                type: 'POST',
                data: payload,
                success: function (response) {
                }
            });
        }
    });

    // }

    // $("#resetmenuorder").on('click', function(e)
    // {



    //     if($('#resetmenuorder').hasClass('confirm')) {

    //         var payload = [];
    //         payload.push(
    //             {
    //                 name: 'method',
    //                 value: 'reset'
    //             }
    //         );

    //         $.ajax({
    //             url: "ajax_savemenu.php",
    //             type: 'POST',
    //             data: payload,
    //             success: function (response) {
    //                 location.reload();
    //             }
    //         });

    //     } else {
    //         $('#resetmenuorder').toggleClass('confirm');
    //         $(this).text("Confirm Reset");
    //     }

    // });

    $('#pollSubmit').on('click', function (e) {
        e.preventDefault();
    });

    $(".auto").autocomplete({
        source: "ajax_search.php",
        minLength: 1,
        select: function (event, ui) {
            var id = ui.item.id;
            if (id != '#') {
                location.href = 'http://anotherasylum.com/profiles.php?id=' + id;
            }
        },
    });
});

$('#donate-input').on('input', function (e) {
    var amount = $('#donate-input').val();
    var boost = ($("#boost").is(':checked') ? 1 : 0);
    var credits = amount * 10;
    if (boost == 1) {
        credits = credits * 2;
    }
    $('#credits').html(credits + " Credits");
    var user = $('#user').val();
    $('#custom').val(boost + ',' + user);
});

$("#boost").change(function () {
    var user = $('#user').val();
    var boost = ($(this).is(':checked') ? 1 : 0);
    if (boost == 1) {
        var amount = $('#donate-input').val();
        var credits = (amount * 10) * 2;
        $('#credits').html(credits + " Credits");
    } else {
        var amount = $('#donate-input').val();
        var credits = amount * 10;
        $('#credits').html(credits + " Credits");
    }
    $('#custom').val(boost + ',' + user);
});