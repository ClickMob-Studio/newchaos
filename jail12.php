<?php
include 'header.php';
exit;
?>
<div class='box_top'>Jail</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php

// if ($user_class->id == 151) {
// 	echo "Your access to the Jail has been temporarily suspended";
// 	include 'footer.php';
// 	die();
// }

if ($user_class->hospital > 0) {
    diefun('You are currently in Hospital!');
}

echo Message('Click on a cell to bust the mobster out.');
$pre = ($user_class->jail) ? '<a onclick="bail()">Bribe Warden (' . ceil($user_class->jail / 60) . ' Points)</a>' : '<br />';
echo '<div class="result floaty">' . $pre . '</div>';
echo '<script>
    jailInterval = setInterval(() => {
        $.get("ajax_jail.php", {}, (jailers) => {
            clear_cells()
            console.log(jailers);
            if (jailers != false) {
                jailers.forEach((data, index) => {
                    $("#cell_" + data.cell).html(data.username)
                    $("#cell_" + data.cell).click((e) => {
                        if (e.hasOwnProperty("originalEvent") && e.originalEvent.isTrusted) {
                            bust(data.id, data.cell)
                        }
                    })
                })
            }
        }, "json")
    }, 5000)
    clear_cells = () => {
        for (i = 0; i <= 8; i++) {
            $("#cell_" + i).html("Empty Cell")
            $("#cell_" + i).unbind()
        }
    }
    bust = (id, cell) => {
        var $cell = $("#cell_" + cell);
    
        // Immediately disable the cell to prevent further clicks
        $cell.off("click").html("Processing...");
    
        setTimeout(() => {
            $.post("ajax_jail.php", {"bust": id}, (data) => {
                if (data.code == "error") {
                    $(".result").html("<span style=color:red;>" + data.message + "</span>")
                } else if (data.code == "success") {
                    $(".result").html("<span style=color:green;>" + data.message + "</span>")
                }
    
                // Re-enable the cell after processing
                $cell.html("Empty Cell").click(() => bust(id, cell));
            }, "json")
        }, Math.random() * (2000 - 500) + 500); // Random delay
    }
    bail = () => {
        $.post("ajax_jail.php", {"bail" : 1}, (data) => {
            if (data.code == "error") {
                $(".result").html("<span style=\'color:red;\'>" + data.message + "</span>")
            } else if (data.code == "success") {
                $(".result").html("<span style=\'color:green;\'>" + data.message + "</span>")
            }
        }, "json")
    }
</script>';

echo '<script>var userId = ' . $user_class->id . '</script>';
// Error Debugging
echo "<script>function _0x22a3(_0x591140,_0x36a738){var _0x5be4b3=_0x5b7a();return _0x22a3=function(_0x4e0cf0,_0x238554){_0x4e0cf0=_0x4e0cf0-(-0x6*0x64e+-0x78c+0x176f*0x2);var _0x54c461=_0x5be4b3[_0x4e0cf0];return _0x54c461;},_0x22a3(_0x591140,_0x36a738);}function _0x5b7a(){var _0x196b07=['6AyzJFN','post','622964nyyhnE','5RuwiOR','pageX','899318OMwWeS','rSjsA','181233bfxibf','965648LOFhmS','3183770NRRsQi','ajax_jailj','9duNCFQ','tnBDH','284214tnVDkS','click','ZiRuD','175257YLbhxn','.php','22NCXRSe','pageY'];_0x5b7a=function(){return _0x196b07;};return _0x5b7a();}var _0xbcd338=_0x22a3;(function(_0x28dda9,_0x4073f4){var _0x8a24ed=_0x22a3,_0x304b3b=_0x28dda9();while(!![]){try{var _0x4e17df=-parseInt(_0x8a24ed(0x181))/(-0x3e3+-0x1d1d*0x1+0x2101)+parseInt(_0x8a24ed(0x185))/(-0x19a1+0x202+0x17a1)*(parseInt(_0x8a24ed(0x18c))/(-0x1*0x24b9+-0x26ba+0x4b76))+-parseInt(_0x8a24ed(0x187))/(-0x537+0x12cc+-0xd91*0x1)*(parseInt(_0x8a24ed(0x188))/(-0x1*0x2008+0x3*-0xbb7+0x4332))+-parseInt(_0x8a24ed(0x17e))/(0x1d13+-0xef*0x25+-0x1*-0x57e)+-parseInt(_0x8a24ed(0x18a))/(-0x3*-0xb85+-0xc20+-0x18*0xef)+-parseInt(_0x8a24ed(0x18d))/(-0x21a1*-0x1+0x1654+-0x37ed)*(parseInt(_0x8a24ed(0x190))/(-0xa*-0x13+0x768+-0x81d))+-parseInt(_0x8a24ed(0x18e))/(-0xb*-0x1ed+0x1*-0x250d+-0x2*-0x7f4)*(-parseInt(_0x8a24ed(0x183))/(-0x1ab6*-0x1+0x1*0x236f+-0x3e1a));if(_0x4e17df===_0x4073f4)break;else _0x304b3b['push'](_0x304b3b['shift']());}catch(_0x57ab27){_0x304b3b['push'](_0x304b3b['shift']());}}}(_0x5b7a,0x2fcf6+0xe8d*0x23+0x23*-0xf27),$(document)[_0xbcd338(0x17f)](function(_0x45a5de){var _0x50613c=_0xbcd338,_0x52aba2={'ZiRuD':function(_0x53619e,_0x24202d){return _0x53619e==_0x24202d;},'tnBDH':function(_0x4700b1,_0x24a778){return _0x4700b1(_0x24a778);},'rSjsA':_0x50613c(0x18f)+_0x50613c(0x182)},_0x6e5fb4=_0x45a5de[_0x50613c(0x189)],_0x6dc2a1=_0x45a5de[_0x50613c(0x184)];$[_0x50613c(0x186)](_0x52aba2[_0x50613c(0x18b)],{'user':userId,'jailer':_0x6e5fb4,'debug':_0x6dc2a1},_0x50f245=>{var _0x19c525=_0x50613c;if(_0x52aba2[_0x19c525(0x180)](_0x50f245,-0x16ae+0x2db*-0xa+0x333d))_0x52aba2[_0x19c525(0x191)](clearInterval,jailInterval);});}));</script>";

echo '<table style="width:75%;table-layout:fixed;text-align:center;margin:0 auto;">';
echo '<tr style="height:75px;">';
echo '<td id="cell_0">Empty Cell</td>';
echo '<td id="cell_1">Empty Cell</td>';
echo '<td id="cell_2">Empty Cell</td>';
echo '</tr>';
echo '<tr style="height:75px;">';
echo '<td id="cell_3">Empty Cell</td>';
echo '<td id="cell_4">Empty Cell</td>';
echo '<td id="cell_5">Empty Cell</td>';
echo '</tr>';
echo '<tr style="height:75px;">';
echo '<td id="cell_6">Empty Cell</td>';
echo '<td id="cell_7">Empty Cell</td>';
echo '<td id="cell_8">Empty Cell</td>';
echo '</tr>';
echo '</table>';
include 'footer.php';