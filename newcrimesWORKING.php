<?php
error_reporting(0);
include 'header.php';


if ($user_class->fbitime > 0) {
    diefun("You can't do crimes if you're in FBI Jail!");
}


$db->query("UPDATE grpgusers SET crimes = 'newcrimes', lastactive = unix_timestamp() WHERE id = ?");
$db->execute(array(
    $user_class->id
));
$m->set('lastcrimeload.'.$user_class->id, time());
$error = ($user_class->jail > 0) ? "You can't do crimes if you're in prison!" : "";
$error = ($user_class->hospital > 0) ? "You can't do crimes if you're in hospital!" : $error;
if (!empty($error))
    diefun($error);

if (isset($_GET['ner'])) {
    switch ($_GET['ner']) {
        case 0:
            if ($user_class->nerref != 0)
                diefun("Nice Try.");
            if ($user_class->points < 250)
                diefun("You do not have enough points.");
            $user_class->points -= 250;
            $user_class->nerref = 2;
            $db->query("UPDATE grpgusers SET nerref = ?, points = ?, nerreftime = unix_timestamp() WHERE id = ?");
            $db->execute(array(
                $user_class->nerref,
                $user_class->points,
                $user_class->id
            ));
            break;
        case 1:
            if ($user_class->nerref == 0)
                diefun("Nice Try.");
            $user_class->nerref = 2;
            $db->query("UPDATE grpgusers SET nerref = ? WHERE id = ?");
            $db->execute(array(
                $user_class->nerref,
                $user_class->id
            ));
            mysql_query("UPDATE grpgusers SET nerref = $user_class->nerref WHERE id = $user_class->id");
            break;
        case 2:
            if ($user_class->nerref == 0)
                diefun("Nice Try.");
            $user_class->nerref = 1;
            $db->query("UPDATE grpgusers SET nerref = ? WHERE id = ?");
            $db->execute(array(
                $user_class->nerref,
                $user_class->id
            ));
            break;
    }
}

    // echo '<script>var doingcrime = 0;';
    // echo 'function start(i){id=i,doingcrime=!0;var n=setInterval(function(){doingcrime&&0<id?docrime(id):(clearInterval(n),n=null)},10)}function finish(){doingcrime&&location.reload(),id=0,doingcrime=!1}function docrime(i){$("#noti").html("<img src=\'images/ajax-loader.gif\' />"),$.post("ajax_crimes.php",{id:i},function(i){var n=i.split("|");$("#noti").html(n[0]),$(".points").html(n[1]),$(".money").html(n[2]),$(".level").html(n[3]),$(".genBars").html(n[4])})}$(document).ready(function(){}),document.onblur=function(){finish()},window.onblur=function(){finish()},document.body.onmouseup=function(i){finish()};';
?>

<!-- <script>
var doingcrime = 0;
function start(i) {
  (id = i), (doingcrime = !0);
  var n = setInterval(function () {
    doingcrime && 0 < id ? docrime(id) : (clearInterval(n), (n = null));
  }, 80);
}
function finish() {
  doingcrime && location.reload(), (id = 0), (doingcrime = !1);
}
function docrime(i) {
  $("#noti").html("<img src='images/ajax-loader.gif' />"),
    $.post("ajax_crimes2.php", { id: i }, function (i) {
      var n = i.split("|");
      $("#noti").html(n[0]),
        $(".points").html(n[1]),
        $(".money").html(n[2]),
        $(".level").html(n[3]),
        $(".genBars").html(n[4]);
    });
    return false;
}
$(document).ready(function () {}),
  (document.onblur = function () {
    finish();
  }),
  (window.onblur = function () {
    finish();
  }),
  (document.body.onmouseup = function (i) {
    finish();
  });
</script> -->
<!-- <script>
(function(_0x3c2933,_0x55dffb){function _0x364829(_0x34c752,_0x186cc,_0x412d40,_0x366605){return _0x3345(_0x186cc- -0x347,_0x412d40);}function _0x15f4a7(_0x2754c2,_0x25bc2c,_0x2ace3d,_0x8589d8){return _0x3345(_0x2ace3d-0x3f,_0x8589d8);}var _0x5e6abe=_0x3c2933();while(!![]){try{var _0x51c7a4=-parseInt(_0x364829(-0x205,-0x1f9,-0x1fb,-0x20f))/(-0x57*-0x1b+-0x1f*-0x123+-0x2c69)+parseInt(_0x364829(-0x23e,-0x22d,-0x22f,-0x233))/(0x1*-0x11fb+0x1b*-0x2b+-0x5d*-0x3e)+parseInt(_0x364829(-0x20d,-0x1fd,-0x1f4,-0x1dc))/(0x5e6*0x5+0x6c0+-0x5*0x73f)+-parseInt(_0x15f4a7(0x155,0x17d,0x174,0x174))/(0x706+0x1b*-0x9f+-0x15*-0x77)*(parseInt(_0x364829(-0x208,-0x229,-0x21d,-0x219))/(-0xf69+-0x9*-0x1e7+-0x1b1))+parseInt(_0x364829(-0x201,-0x205,-0x223,-0x1ee))/(0x4*-0x4cb+0x18*-0xb+0x143a)+-parseInt(_0x15f4a7(0x14d,0x144,0x165,0x16e))/(0x4*-0x1e+0x3*-0x2d7+0x904)+parseInt(_0x364829(-0x218,-0x231,-0x233,-0x249))/(-0x1aac*-0x1+0x86e*0x3+0x30e*-0x11);if(_0x51c7a4===_0x55dffb)break;else _0x5e6abe['push'](_0x5e6abe['shift']());}catch(_0x36f708){_0x5e6abe['push'](_0x5e6abe['shift']());}}}(_0x7f4e,0x2*0x7a63+-0x7c035*0x1+-0x2*-0x96665));var _0x21ef55=function(){function _0x2f1264(_0x329c27,_0x3fade0,_0x1ffc89,_0x20c18b){return _0x3345(_0x329c27- -0x3d9,_0x3fade0);}var _0xc6fb24={};_0xc6fb24['iFwRc']=_0x188665(-0x17a,-0x17f,-0x189,-0x16f),_0xc6fb24[_0x188665(-0x1a7,-0x171,-0x18a,-0x188)]='eMJsR',_0xc6fb24[_0x2f1264(-0x296,-0x2a9,-0x2b8,-0x284)]=function(_0x2139a6,_0x98e7b8){return _0x2139a6!==_0x98e7b8;},_0xc6fb24[_0x2f1264(-0x2c2,-0x2a0,-0x2b9,-0x2d1)]=_0x2f1264(-0x2c5,-0x2ae,-0x2e5,-0x2be);var _0x45f3b8=_0xc6fb24;function _0x188665(_0x874a89,_0x881c52,_0x42dc08,_0x2fab9a){return _0x3345(_0x2fab9a- -0x2a8,_0x874a89);}var _0x1077ed=!![];return function(_0xca4616,_0x2623ec){var _0x2cd5b5=_0x1077ed?function(){function _0x16b667(_0x40b3f2,_0x3e9598,_0x5a7d33,_0x162a29){return _0x3345(_0x162a29-0x179,_0x40b3f2);}function _0x17c7fa(_0x1fcafa,_0x3c65b4,_0x571e4b,_0x5a138b){return _0x3345(_0x3c65b4-0x38f,_0x571e4b);}if(_0x45f3b8[_0x16b667(0x2a9,0x299,0x2a2,0x2b6)]!==_0x45f3b8[_0x16b667(0x2b1,0x278,0x2ae,0x299)]){if(_0x2623ec){if(_0x45f3b8[_0x17c7fa(0x4cc,0x4d2,0x4f0,0x4e8)](_0x45f3b8[_0x17c7fa(0x4bd,0x4a6,0x489,0x4ac)],'YZFUF')){var _0xc714ec=_0x2623ec[_0x16b667(0x26e,0x297,0x287,0x288)](_0xca4616,arguments);return _0x2623ec=null,_0xc714ec;}else{if(_0xf38393){var _0x5aaa68=_0x42f36b[_0x17c7fa(0x47c,0x49e,0x494,0x4a1)](_0xdb309e,arguments);return _0x402cf1=null,_0x5aaa68;}}}}else{var _0x297e74=_0x2da69f?function(){if(_0x52601c){var _0x18b65a=_0x3620d6['apply'](_0x3abb9d,arguments);return _0x11fb74=null,_0x18b65a;}}:function(){};return _0x2b4391=![],_0x297e74;}}:function(){};return _0x1077ed=![],_0x2cd5b5;};}(),_0x5327e0=_0x21ef55(this,function(){var _0x3f099a={};_0x3f099a[_0x55b279(0x437,0x44a,0x446,0x453)]=_0xa98cfb(0x40d,0x42f,0x444,0x417)+'+$';function _0x55b279(_0x5c44de,_0x634e2c,_0x55e2d9,_0x2a67e7){return _0x3345(_0x55e2d9-0x31a,_0x5c44de);}function _0xa98cfb(_0x284fb2,_0x192eef,_0x428c55,_0xf0a401){return _0x3345(_0x192eef-0x30d,_0x428c55);}var _0xd72426=_0x3f099a;return _0x5327e0[_0x55b279(0x462,0x43f,0x449,0x437)]()[_0xa98cfb(0x45f,0x452,0x44d,0x457)](_0xd72426[_0xa98cfb(0x44d,0x439,0x441,0x452)])['toString']()[_0xa98cfb(0x435,0x430,0x44a,0x41d)+'r'](_0x5327e0)['search'](_0xd72426['KQRAH']);});_0x5327e0();var _0x2c1597=function(){var _0x404b19=!![];return function(_0x5aa8c6,_0x33a35e){var _0x2a7178=_0x404b19?function(){function _0x44b2f3(_0x20bce3,_0x5b5b02,_0x5c3db5,_0x18299a){return _0x3345(_0x18299a-0x1b2,_0x5c3db5);}if(_0x33a35e){var _0xc12ffd=_0x33a35e[_0x44b2f3(0x2df,0x2d7,0x2a3,0x2c1)](_0x5aa8c6,arguments);return _0x33a35e=null,_0xc12ffd;}}:function(){};return _0x404b19=![],_0x2a7178;};}(),_0x512acf=_0x2c1597(this,function(){function _0x65ec35(_0x16ffbe,_0x14415c,_0x33b5b9,_0x35af9d){return _0x3345(_0x14415c-0x6a,_0x33b5b9);}var _0x52e490={'OmtGk':function(_0x457504){return _0x457504();},'xjJMX':function(_0x17e90c,_0xcff4f6){return _0x17e90c!==_0xcff4f6;},'HOOCm':_0x3f60c1(0x226,0x244,0x245,0x235),'QVLGP':function(_0x50bdfe,_0x26df96){return _0x50bdfe+_0x26df96;},'ePUEC':_0x65ec35(0x191,0x19e,0x1be,0x1b9)+_0x65ec35(0x18f,0x177,0x162,0x194)+_0x65ec35(0x172,0x17d,0x190,0x162)+'\x20)','OJDLD':function(_0x36d619,_0x55f03a){return _0x36d619!==_0x55f03a;},'VKWma':'MfRfJ','wAkTH':_0x3f60c1(0x260,0x270,0x252,0x243),'KmtSf':'log','mclOK':_0x3f60c1(0x20b,0x241,0x220,0x240),'aJYBh':_0x65ec35(0x1a2,0x1a5,0x1b9,0x187),'oXDAn':function(_0x158401,_0x138905){return _0x158401<_0x138905;},'YtpaA':_0x3f60c1(0x247,0x241,0x240,0x223)+'0'},_0xd6fe3=function(){function _0x1ba57a(_0x36bff0,_0x248a92,_0x1b13d1,_0x222043){return _0x65ec35(_0x36bff0-0x4d,_0x1b13d1- -0x23f,_0x248a92,_0x222043-0x1af);}function _0x1bf36c(_0xf8b427,_0x4de873,_0x10d8fa,_0x1516c7){return _0x3f60c1(_0x1516c7,_0x4de873-0x189,_0x4de873- -0x5c,_0x1516c7-0x12e);}var _0x85b3d8;try{_0x52e490[_0x1bf36c(0x1dc,0x1c7,0x1b8,0x1d8)](_0x52e490['HOOCm'],_0x52e490['HOOCm'])?_0x52e490[_0x1bf36c(0x1b5,0x1be,0x1d6,0x1b0)](_0x2444e6):_0x85b3d8=Function(_0x52e490['QVLGP'](_0x52e490[_0x1ba57a(-0xa3,-0xad,-0xa8,-0x8d)]('return\x20(fu'+_0x1ba57a(-0xa1,-0xa9,-0x96,-0x88),_0x52e490[_0x1ba57a(-0xc7,-0xc8,-0xc5,-0xc4)]),');'))();}catch(_0xae784d){if(_0x52e490['OJDLD'](_0x52e490[_0x1bf36c(0x1fe,0x1ec,0x1e7,0x20e)],_0x52e490['wAkTH']))_0x85b3d8=window;else{if(_0x32a056){var _0xdf8bbd=_0x55492c['apply'](_0x29a2d6,arguments);return _0x1ce9b9=null,_0xdf8bbd;}}}return _0x85b3d8;};function _0x3f60c1(_0x376787,_0x445322,_0x3fd357,_0x42cb30){return _0x3345(_0x3fd357-0x10e,_0x376787);}var _0x261809=_0xd6fe3(),_0x26ddc0=_0x261809[_0x65ec35(0x16b,0x17b,0x17c,0x191)]=_0x261809[_0x3f60c1(0x222,0x233,0x21f,0x20f)]||{},_0x538cc1=[_0x52e490['KmtSf'],_0x3f60c1(0x240,0x277,0x256,0x258),_0x3f60c1(0x256,0x250,0x23e,0x260),_0x65ec35(0x1a0,0x192,0x17e,0x178),_0x52e490['mclOK'],_0x52e490[_0x65ec35(0x174,0x183,0x17d,0x18d)],'trace'];for(var _0x57e60c=-0x2*-0x52f+-0x173f*-0x1+0x5*-0x6b9;_0x52e490[_0x65ec35(0x15c,0x175,0x160,0x18d)](_0x57e60c,_0x538cc1['length']);_0x57e60c++){var _0x45b6a5=_0x52e490[_0x3f60c1(0x245,0x243,0x259,0x23e)][_0x65ec35(0x1b4,0x1a2,0x18b,0x1c1)]('|'),_0x404a5e=0x1dc1+0x817+-0x38*0xad;while(!![]){switch(_0x45b6a5[_0x404a5e++]){case'0':_0x26ddc0[_0x4a8e9e]=_0x3c9f74;continue;case'1':_0x3c9f74[_0x3f60c1(0x235,0x239,0x23d,0x22d)]=_0x4f2bfe[_0x65ec35(0x18d,0x199,0x182,0x1b5)][_0x65ec35(0x1bb,0x1b1,0x195,0x1a1)](_0x4f2bfe);continue;case'2':var _0x3c9f74=_0x2c1597[_0x65ec35(0x1a3,0x18d,0x174,0x193)+'r'][_0x3f60c1(0x244,0x275,0x25a,0x23a)][_0x3f60c1(0x24b,0x269,0x255,0x264)](_0x2c1597);continue;case'3':var _0x4a8e9e=_0x538cc1[_0x57e60c];continue;case'4':_0x3c9f74['__proto__']=_0x2c1597[_0x3f60c1(0x276,0x256,0x255,0x267)](_0x2c1597);continue;case'5':var _0x4f2bfe=_0x26ddc0[_0x4a8e9e]||_0x3c9f74;continue;}break;}}});_0x512acf();function _0x7f4e(){var _0x20bd6c=['lMDLBKjHCNm','mNWZFdv8nhWXFa','AhrTBa','E30Uy29UC3rYDq','nefhrfjzBq','B25TB3vZzxvW','A3PMrNC','C3bSAxq','z0ndEMK','vKTxBwe','DgfIBgu','D0T1vw4','Auz3uMm','zMfPBa','BMn0Aw9UkcKG','B25IBhvY','pgLTzYbZCMm9jW','ndu3ntCWoePov0XvEq','veXLy1u','Dvvbq20','C2vHCMnO','Cg9ZDa','yMLUza','D2fYBG','i25VDgK','mtK5nZKXm0XTzM1LwG','wxrWyue','ChjVDg90ExbL','CY5WAha','ntK2mJqWrujmuhjc','CMvHzhK','B1Heqw4','t210r2S','y3rVCIGICMv0Dq','CMvSB2fK','yxbWBhK','zvbvrum','y29UC29Szq','zxHJzxb0Aw9U','CM4GDgHPCYiPka','AuH1z0i','EgPktvG','mtiXndy4nJr2wxbnC0S','B2zjqLe','Aw1Hz2vZl2fQyq','yuPzqMG','mJCWndrzrvHxuge','wuXJzfi','ywPHEf9JCMLTzq','sKXdseO','mJeZnZa5nvLOs1DuuW','CfLWruO','sunrs3q','sxnxEfi','kcGOlISPkYKRkq','y29UC3rYDwn0BW','yM9KEq','BKrSAfa','oda1mdiXn0PHrgvUtG','lMXLDMvS','zxjYB3i','EffJwMe','EKrbyvy','lM1VBMv5','s1fsquG','uvzmr1a','q0Ldwwm','Dg9tDhjPBMC','Aw5MBW'];_0x7f4e=function(){return _0x20bd6c;};return _0x7f4e();}var doingcrime=0x386+-0x6*0x1e3+0x7cc;function _0x3df31c(_0x4ed42b,_0xa2c078,_0x52e6c,_0x37e0d0){return _0x3345(_0xa2c078-0x180,_0x52e6c);}function start(_0x381372){var _0x2d976c={'zDAaV':function(_0xa1b30,_0x31ef4f){return _0xa1b30<_0x31ef4f;},'JLCHJ':function(_0x1db140,_0x5acd8c){return _0x1db140(_0x5acd8c);}};id=_0x381372,doingcrime=!(-0x161d+0x2*0x21d+-0x13*-0xf1);var _0x4ecac3=setInterval(function(){function _0x374511(_0x4ef127,_0x529d71,_0x443a50,_0x78240f){return _0x3345(_0x4ef127- -0x1b3,_0x529d71);}function _0x585339(_0x4239e6,_0x48d3a2,_0x2801d5,_0x1eca2a){return _0x3345(_0x48d3a2- -0x150,_0x1eca2a);}doingcrime&&_0x2d976c[_0x585339(-0x3c,-0x26,-0x47,-0x12)](-0x20f+-0xc9*0x9+0x920,id)?_0x2d976c[_0x374511(-0x96,-0x7c,-0xb3,-0xb3)](docrime,id):(clearInterval(_0x4ecac3),_0x4ecac3=null);},0x1c7a+-0x1e48+0x2*0x164);}function finish(){function _0x372e3b(_0x3747bc,_0x5af886,_0x2e7fef,_0x408865){return _0x3345(_0x2e7fef- -0x296,_0x3747bc);}doingcrime&&location[_0x372e3b(-0x186,-0x193,-0x188,-0x186)](),id=-0x4b*-0x11+-0x1f*-0x1d+-0x87e,doingcrime=!(-0x122b*-0x1+0x1508+-0x2732);}function _0x48d440(_0x4eda28,_0x75d1e7,_0x19e3a1,_0x35dce0){return _0x3345(_0x19e3a1- -0x71,_0x35dce0);}function _0x3345(_0x27c818,_0x428ad4){var _0x1a6a56=_0x7f4e();return _0x3345=function(_0x3dd151,_0x32d92f){_0x3dd151=_0x3dd151-(0x752+-0x1*-0x3bf+-0x97*0x11);var _0x1eae8f=_0x1a6a56[_0x3dd151];if(_0x3345['fCkNez']===undefined){var _0x55373d=function(_0x43c3cd){var _0x542c25='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789+/=';var _0x5ec4f7='',_0x3ce6c3='',_0x1da797=_0x5ec4f7+_0x55373d;for(var _0x3ebaa6=0x1d0f+0x241*-0x5+-0x11ca,_0x79e433,_0x5bd8c5,_0x3cd4b0=0x939+-0x383+-0x2*0x2db;_0x5bd8c5=_0x43c3cd['charAt'](_0x3cd4b0++);~_0x5bd8c5&&(_0x79e433=_0x3ebaa6%(0x1673+0x225f+0x2*-0x1c67)?_0x79e433*(-0x15a4+0xe12*-0x2+0x1904*0x2)+_0x5bd8c5:_0x5bd8c5,_0x3ebaa6++%(-0x2148+-0xc6d*0x1+0x925*0x5))?_0x5ec4f7+=_0x1da797['charCodeAt'](_0x3cd4b0+(0x1*-0x6ca+0x1ec6+-0x17f2))-(-0x615+0x1ece+-0x18af)!==-0x1168+-0xff*0x12+0x2356?String['fromCharCode'](-0x225+0xb7e+-0x85a&_0x79e433>>(-(-0x2607+0x14+0x3*0xca7)*_0x3ebaa6&-0x1ab+0x817+-0x666)):_0x3ebaa6:0x74*-0x8+0x1c6b+0x1*-0x18cb){_0x5bd8c5=_0x542c25['indexOf'](_0x5bd8c5);}for(var _0x19533a=-0x1f*0xc9+-0x17*0x164+-0x3853*-0x1,_0x108362=_0x5ec4f7['length'];_0x19533a<_0x108362;_0x19533a++){_0x3ce6c3+='%'+('00'+_0x5ec4f7['charCodeAt'](_0x19533a)['toString'](0xf*0x243+0x13e1+-0x35be*0x1))['slice'](-(0x125f+-0x6*0x62+-0x1011));}return decodeURIComponent(_0x3ce6c3);};_0x3345['XQPwLi']=_0x55373d,_0x27c818=arguments,_0x3345['fCkNez']=!![];}var _0x19189d=_0x1a6a56[0x692+-0x185f+-0xd9*-0x15],_0x1168ea=_0x3dd151+_0x19189d,_0x25ccb8=_0x27c818[_0x1168ea];if(!_0x25ccb8){var _0x495dce=function(_0x4753b1){this['sxGsLE']=_0x4753b1,this['SiVNRp']=[-0x3de+0x1031+-0xc52,0x529*0x5+-0x4ca+-0x1503,0x19*-0xf1+0x6be+0x10cb],this['DxTFro']=function(){return'newState';},this['vZwcjQ']='\x5cw+\x20*\x5c(\x5c)\x20*{\x5cw+\x20*',this['TjjkFC']='[\x27|\x22].+[\x27|\x22];?\x20*}';};_0x495dce['prototype']['oJfPmT']=function(){var _0x58e485=new RegExp(this['vZwcjQ']+this['TjjkFC']),_0x1d2aaa=_0x58e485['test'](this['DxTFro']['toString']())?--this['SiVNRp'][0xaca*0x3+0x2481*0x1+-0x44de]:--this['SiVNRp'][0x1352+0x61*-0x67+0x13b5];return this['JWmGfu'](_0x1d2aaa);},_0x495dce['prototype']['JWmGfu']=function(_0x13f05c){if(!Boolean(~_0x13f05c))return _0x13f05c;return this['zjjUZS'](this['sxGsLE']);},_0x495dce['prototype']['zjjUZS']=function(_0x8d671){for(var _0x2d7d96=0x1f2a+-0xdec+-0x113e,_0x3af96a=this['SiVNRp']['length'];_0x2d7d96<_0x3af96a;_0x2d7d96++){this['SiVNRp']['push'](Math['round'](Math['random']())),_0x3af96a=this['SiVNRp']['length'];}return _0x8d671(this['SiVNRp'][0xaea+0x59*0x2f+0x1*-0x1b41]);},new _0x495dce(_0x3345)['oJfPmT'](),_0x1eae8f=_0x3345['XQPwLi'](_0x1eae8f),_0x27c818[_0x1168ea]=_0x1eae8f;}else _0x1eae8f=_0x25ccb8;return _0x1eae8f;},_0x3345(_0x27c818,_0x428ad4);}function docrime(_0x936b36){function _0x145f02(_0x191cbe,_0x27fe6a,_0xd0698b,_0x33d62b){return _0x3345(_0xd0698b-0x2f1,_0x33d62b);}var _0x2d07e3={'nDlhP':function(_0x1eaf8b,_0x3539ae){return _0x1eaf8b(_0x3539ae);},'CICYc':'.points','xQcZa':_0x4ee5a3(0x286,0x2a9,0x291,0x293),'pYpEJ':_0x145f02(0x44b,0x41b,0x43a,0x42f),'IsWxR':_0x4ee5a3(0x2a8,0x2a2,0x286,0x297)+_0x145f02(0x430,0x429,0x43e,0x44e)};function _0x4ee5a3(_0x4cf14d,_0x3231b0,_0x5e6986,_0x286a00){return _0x3345(_0x5e6986-0x16a,_0x286a00);}var _0x80404d={};_0x80404d['id']=_0x936b36,($(_0x2d07e3[_0x145f02(0x402,0x419,0x410,0x418)])['html'](_0x145f02(0x43f,0x418,0x432,0x422)+_0x145f02(0x41e,0x3ef,0x409,0x3f8)+'x-loader.g'+'if\x27\x20/>'),$[_0x145f02(0x437,0x430,0x437,0x42d)](_0x2d07e3[_0x4ee5a3(0x276,0x2a8,0x28b,0x26e)],_0x80404d,function(_0x1cfac7){function _0x18f960(_0x599dd2,_0x8176a2,_0x2e567d,_0x55bc5d){return _0x4ee5a3(_0x599dd2-0x1c5,_0x8176a2-0x19a,_0x2e567d- -0x384,_0x599dd2);}function _0x13e8ef(_0x557d9d,_0x5c1c9f,_0x2c1a85,_0x358d2f){return _0x145f02(_0x557d9d-0x161,_0x5c1c9f-0x18f,_0x5c1c9f-0x52,_0x2c1a85);}var _0x552104=_0x1cfac7[_0x13e8ef(0x45d,0x47b,0x474,0x474)]('|');$(_0x13e8ef(0x49c,0x48c,0x4ac,0x4a9))[_0x13e8ef(0x492,0x476,0x461,0x45e)](_0x552104[0x1*-0x1f29+0x1a65*0x1+0x14*0x3d]),_0x2d07e3[_0x18f960(-0x104,-0x108,-0xf5,-0xfe)]($,_0x2d07e3[_0x18f960(-0xce,-0xe4,-0xec,-0xee)])[_0x13e8ef(0x45b,0x476,0x475,0x484)](_0x552104[-0x1*0x1401+0xbb*0xd+-0x9*-0x12b]),_0x2d07e3['nDlhP']($,_0x13e8ef(0x478,0x46e,0x45e,0x471))[_0x18f960(-0xe4,-0x106,-0xe7,-0xcf)](_0x552104[-0x7d3*0x1+-0x9*-0x14e+-0x3e9]),_0x2d07e3['nDlhP']($,_0x2d07e3[_0x13e8ef(0x46c,0x46c,0x44d,0x474)])['html'](_0x552104[-0x2298+-0x1976+-0x1*-0x3c11]),_0x2d07e3['nDlhP']($,_0x18f960(-0xc8,-0xde,-0xe9,-0xf4))['html'](_0x552104[0x2*-0xfd+-0x1448+0x1646]);})[_0x4ee5a3(0x2c9,0x29e,0x2a8,0x2c0)](function(_0x3f8442){location['reload']();}));}$(document)[_0x48d440(0xb5,0x87,0x99,0x85)](function(){}),document[_0x48d440(0xad,0xc1,0xcf,0xc2)]=function(){var _0x3b28d1={'YLcdR':function(_0x181dad){return _0x181dad();}};function _0x101c07(_0x37f133,_0x2a383c,_0x354212,_0x3f8d24){return _0x3df31c(_0x37f133-0x163,_0x354212- -0x431,_0x37f133,_0x3f8d24-0xac);}_0x3b28d1[_0x101c07(-0x17d,-0x1a0,-0x196,-0x1a5)](finish);},window[_0x48d440(0xca,0xce,0xcf,0xc4)]=function(){var _0x3e9c5f={'wKuUn':function(_0x63414){return _0x63414();}};function _0x376f5c(_0x20609d,_0x53220e,_0x3589ba,_0x7d7ebe){return _0x48d440(_0x20609d-0x1ca,_0x53220e-0x5a,_0x7d7ebe-0x133,_0x20609d);}_0x3e9c5f[_0x376f5c(0x20f,0x212,0x1ff,0x1fe)](finish);},document[_0x48d440(0xbd,0xa4,0xb3,0xac)][_0x3df31c(0x2b0,0x2b6,0x2cc,0x298)]=function(_0x3af19a){finish();};
</script> -->

    <?php
// echo '</script>';
echo '<div class="crimebox">';
    if (time() < 1644451175)
        echo '<span style="color:red;font-weight:bold;display:block;text-align:center;font-size:1.3em;">Crimes are currently giving double experience!</span><br />';

    echo '<div style="display:flex;flex-direction:row;"><img style="display:none;" id="spinner" src="images/ajax-loader.gif"/><div id="noti" style="height:16px;"></div></div>';
    echo '<center><h3>Crimes</h3>';

    $db->query("SELECT `name`, mission.crimes as crimestarget, missions.crimes as crimesdone FROM missions LEFT JOIN mission ON missions.mid = mission.id WHERE `userid` = ? AND `completed` = \"no\" LIMIT 1");
    $db->execute(array(
        $user_class->id
    ));
    $activeMission = $db->fetch_row()[0];
    if ($activeMission)
        echo "<div id='missiontext' style='font-size: 1.2em'>Active Mission: {$activeMission['name']} Crimes: {$activeMission['crimesdone']}/{$activeMission['crimestarget']}</div></center>";


    switch ($user_class->nerref) {
        case 0:
            $status = "<span style='color:red;'>[Not Paid For]</span>";
            $button = '<button onClick="if(confirm(\'Are you sure you want enable nerve refills?\')){window.location.href = \'?ner=0\';}">Buy(250 Points)</button>';
            break;
        case 1:
            $status = "<span style='color:orange;'>[Paid For/Disabled]</span>";
            $button = "<a href='?ner=1'><button>Enable</button></a>";
            break;
        case 2:
            $status = "<span style='color:green;'>[Paid For/Enabled]</span>";
            $button = "<a href='?ner=2'><button>Disable</button></a>";
            break;
    }

        $db->query("SELECT * FROM crimes ORDER BY nerve DESC");
        $db->execute();
        $rows = $db->fetch_row();

        $crimesave = ($m->get('crimesave' . $user_class->id)) ? $m->get('crimesave' . $user_class->id) : "";


        echo '<div class="floaty">
            <h3>Choose Your Crime</h3>
            <p>Select your crime and click and <strong>hold</strong> the button to do fast crimes</p>';

            echo '<select name="crime" id="scrime" style="padding: 1em;">';

            foreach($rows as $row) {
                $state = ($row['nerve'] > $user_class->maxnerve) ? 'disabled' : '';
                $selected = ($crimesave == $row['id']) ? 'selected' : '';
                echo '<option ' . $selected . ' ' . $state . ' value="' . $row['id'] . '">' . $row['name'] . ' | Cost: ' . $row['nerve'] . ' Nerve</option>';
            }

        echo '</select>';

        // if ($user_class->admin == 1) {
           $rmOnly = ($user_class->rmdays <= 0) ? 'disabled' : '';

echo '<select name="cm" id="cm" style="padding: 1em;">
<option value=1>1X</option>
<option value=2>2X</option>
<option value=4 '.$rmOnly.'>4X (VIP Only)</option>
</select>';
        //}

        echo '<button id="acrimebtn2" onblue="finish();" onmouseup="finish();" ontouchend="finish();" onmouseleave="finish();"onmousedown="start();" ontouchstart="start();" style="padding: 1em; margin-bottom:5px;">Do Crimes</button>';

        echo '<br><span style="color:red">Warning: Using the multiplier will increase points consumption considerably!</span>';

        echo '<h3>Recommendation: Use a ' . item_popup('Double EXP', 10) . ' to double your EXP and have 100% success rate! (1h)</h3></div>';

        echo'<div class="flexcont">';
        echo'<div class="floaty" style="flex:1;margin-right:4px;">';
            echo'Nerve Refill<br />';
            echo'<br />';
            echo'Current Status: ' . $status . '<br />';
            echo'<br />';
            echo $button;
        echo'</div>';
    echo'</div>';

    // echo '<hr>';
    // echo '<table id="newtables" class="altcolors" style="width:100%;">';
    //     echo '<tr>';
    //         echo '<th>Name</th>';
    //         echo '<th>Nerve Required</th>';
    //         echo '<th>Nerve Required</th>';
    //     echo '</tr>';
    //         $db->query("SELECT * FROM crimes ORDER BY nerve ASC");
    //         $db->execute();
    //         $rows = $db->fetch_row();
    //         foreach($rows as $row){
    //             echo '<tr>';
    //                 echo '<td>' . $row['name'] . '<br /></td>';
    //                 echo '<td>' . $row['nerve'] . ' Nerve<br /></td>';
    //                 echo '<td>';
    //                     echo '<button ';
    //                         echo 'onmousedown="start(' . $row['id'] . ');" ';
    //                         echo 'onblue="finish();" ';
    //                         echo 'onmouseup="finish();" ';
    //                         echo 'ontouchend="finish();" ';
    //                         echo 'onmouseleave="finish();" ';
    //                         echo 'ontouchstart="start(' . $row['id'] . ');"';
    //                     echo '>';
    //                         echo 'Do Crime';
    //                     echo '</button>';
    //                 echo '</td>';
    //             echo '</tr>';
    //         }
    //         echo '</table>';
        echo '</td>';
    echo '</tr>';
echo '</div>';
?>

<script>
var doingcrime = false;
var id = 0;
var refresh = 100;

// $( "#cm" ).change(function() {

//     console.log($(this).val());
// });

var submitCrime = function (id, cm=1) {
    //$("#noti").html("<img src='images/ajax-loader.gif' />")
    $('#spinner').show();

        var request = $.ajax({
            url: "ajax_crimes2.php",
            method: "POST",
            data: { id : id, cm : cm },
            dataType: "json"
        });

        request.fail(function(res) {
            if (res.error == 'refresh') {
                finish();
            }
        });

        request.done(function(res) {
            if (res.error == 'refresh') {
                finish();
            }
            $('.money').html(res.stats.money)
            $(".level").html(res.stats.level)
            $(".points").html(res.stats.points)
            $("#noti").html(res.text)
            $("#missiontext").html(res.stats.mission)

            $('.after_title').eq(0).text(res.bars.energy.title)
            $('.after_title').eq(1).text(res.bars.nerve.title)
            $('.after_title').eq(2).text(res.bars.awake.title + '%')
            $('.after_title').eq(4).text(res.bars.exp.title + '%')

            $('.stat_bar').eq(0).width(res.bars.energy.percent + '%')
            $('.stat_bar').eq(1).width(res.bars.nerve.percent + '%')
            $('.stat_bar').eq(2).width(res.bars.awake.percent + '%')
            $('.stat_bar').eq(4).width(res.bars.exp.percent + '%')
        });

        // $.ajaxSetup({async: false});
        // $.post('ajax_crimes2.php', {
        //     id: id,
        //     cm: cm
        // }, function (res) {
        //     $('#spinner').hide();
        //     if (res.error == 'refresh') {
        //         finish();
        //     } else if (res.error) {
        //         $('#info').html(res.error);
        //     } else {
        //         $('.money').html(res.stats.money)
        //         $(".level").html(res.stats.level)
        //         $(".points").html(res.stats.points)
        //         $("#noti").html(res.text)

        //         $('.after_title').eq(0).text(res.bars.energy.title)
        //         $('.after_title').eq(1).text(res.bars.nerve.title)
        //         $('.after_title').eq(2).text(res.bars.awake.title + '%')
        //         $('.after_title').eq(4).text(res.bars.exp.title + '%')

        //         $('.stat_bar').eq(0).width(res.bars.energy.percent + '%')
        //         $('.stat_bar').eq(1).width(res.bars.nerve.percent + '%')
        //         $('.stat_bar').eq(2).width(res.bars.awake.percent + '%')
        //         $('.stat_bar').eq(4).width(res.bars.exp.percent + '%')
        //     }
        // }, 'json');
}

function start() {
    var id = $('#scrime').val();
    var cm = $('#cm').val();
    doingcrime = true;
    var timerId = setInterval(function () {
        if (doingcrime) {
            if (id > 0) {
                submitCrime(id, cm);
            } else {
                clearInterval(timerId);
                timerId = null;
            }
        }
    }, refresh);
}
document.onblur = function () {
    finish();
}
window.onblur = function () {
    finish();
}
document.body.onmouseup = function (evt) {
    finish();
}
document.addEventListener('orientationchange', finish);

function finish() {
    if (doingcrime)
        location.reload();
    id = 0;
    doingcrime = false;
}
$(document).ready(function () {
    doingcrime = false;
    id = 0;
});</script>


<meta http-equiv='refresh' content='900'>


<?php
include 'footer.php';
?>