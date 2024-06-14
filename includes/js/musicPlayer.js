/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
function pad2(number) {
   return (number < 10) ? '0' + number : number;
}

function resize()
{
     if (navigator.appName.indexOf("Microsoft")!=-1) {
	   document.body.offsetWidth=450;
  	   document.body.offsetHeight=document.getElementById("all").offsetParent.offsetHeight+20;
	 }
	else
	{
 	 window.innerWidth=450
	 window.innerHeight=document.getElementById("all").offsetParent.offsetHeight+20;
	 }

}


 var volume=100;
 var playing;
 var global_slider;
 var actual_music;
var music;
var st=0;
function musicPlayer(resizew,autostart)
{
      st=autostart;
      soundManager.allowPolling = true;   // enable flash status updates. Required for whileloading/whileplaying.
      soundManager.consoleOnly = false;   // if console is being used, do not create/write to #soundmanager-debug
      soundManager.debugMode = false;      // enable debugging output (div#soundmanager-debug, OR console..)
      soundManager.flashLoadTimeout = 2000;// ms to wait for flash movie to load before failing (0 = infinity)
      soundManager.flashVersion = 9;      // version of flash to require, either 8 or 9. Some features require 9.
      soundManager.nullURL = 'null.mp3';  // (Flash 8 only): URL of silent/blank MP3 for unloading/stopping request.
      soundManager.url = 'sm/swf/'; // path (directory) where SM2 .SWF files will be found.
      soundManager.useConsole = true;     // use firebug/safari console.log()-type debug console if available
      soundManager.useHighPerformance = false;// position:fixed flash movie for faster JS/flash callbacks
      soundManager.wmode = 'transparent';     // null, transparent, opaque (last two allow HTML on top of flash)
      soundManager.usePeakData=true;
      soundManager.waitForWindowLoad = true;
      soundManager.onload = function() {
	 this.play=play;
         this.previous=previous;
         createPlaylist( musics);
          if(resizew)
               resize();
	  
        }

	soundManager.onerror = function() {
		alert("Your flash version is not updated");
	 }
         
}
					
function play()
{
        if( actual_music==-1) return;
         music= musics[ actual_music];
        soundManager.stopAll();
        playing='mySound'+music[0];
        soundManager.play('mySound'+music[0]);
                volume_slider.f_setValue(volume);

}
function previous()
{
        if(musics[ actual_music-1])
                actual_music--;
        play();
}
function next()
{
        if(musics[ actual_music+1 ])
                actual_music++;
        play();
}
function pause()
{

        soundManager.pauseAll();
}
function stop()
{
        soundManager.stopAll();
}
function eq_lvl(lvl,camp)
{


        var div=document.getElementById(camp);
        var cor= new Array('green','yellow','orange','red');
        div.style.backgroundColor=cor[Math.floor(lvl/8)];
        div.style.height=lvl+"px";
        div.style.width="5px";


}
function volumeUpdate(i)
{

        if( playing)
                soundManager.setVolume( playing,i);
                volume=i;
}
function TimeScroll(i)
{
        if(playing)
                soundManager.getSoundById(playing).setPosition(i);
}
function createPlaylist( musics)
{

        if(musics.length==0)
                 actual_music=-1;
        else
                 actual_music=0;



        for (i=0;i<musics.length;i++)
        {

                soundManager.createSound({
                        id: 'mySound' + musics[i][0],
                        url: musics[i][1]+"",
                        onfinish: function(){
                                next();
                        },usePeakData: true,

                        whileplaying: function() {

                        var seconds=Math.round(this.durationEstimate/1000);
                        var minutes=Math.floor(seconds/60);
                        seconds=Math.round(seconds-minutes*60);
                        document.getElementById('stime').innerHTML="Total:"+pad2(minutes)+":"+pad2(seconds);
                        var seconds=Math.round(this.position/1000);
                        var minutes=Math.floor(seconds/60);
                        seconds=Math.round(seconds-minutes*60);
                        document.getElementById('elapsed').innerHTML=pad2(minutes)+":"+pad2(seconds);

                        if (!global_slider.onmove)
                        {
                                global_slider.n_maxValue = this.durationEstimate;
                                global_slider.n_pix2value = global_slider.n_pathLength / (global_slider.n_maxValue - global_slider.n_minValue);
                                global_slider.f_setValue(this.position,"don't callback");
                        }
                        eq_lvl(Math.floor(this.peakData.left*30),'equalizer');
                        eq_lvl(Math.floor(this.peakData.right*30),'equalizer1');


                        },
                onid3: function(){

                        document.getElementById('title').innerHTML = this.id3['TIT2'];
                        document.getElementById('artist').innerHTML = this.id3['TALB'];
                },
                 onstop: function()
                 {},
                 onpause: function()
                 {},
                 onresume: function()
                 {}
                });
                createEntry(musics[i][0],musics[i][2],i);


        }
        if(st==1)
              play();
}

function createEntry(id, name,nmbr)
{
    if(document.getElementById("playlist"))
        document.getElementById("playlist").innerHTML+='<table width="100%" class="playlist_item'+(nmbr%2)+'"><tr><td width="80%"><a class="link" onclick="choose('+id+')" onmouseover="this.parentNode.parentNode.className='+"'"+'playlist_mouseover'+(nmbr%2)+"'"+'" onmouseout="this.parentNode.parentNode.className=\'playlist_item'+(nmbr%2)+'\'">'+name+'</a></td><td><a onclick="delete_file('+id+');" onmouseover="this.parentNode.parentNode.className=\'playlist_mouseover'+(nmbr%2)+'\'" onmouseout="this.parentNode.parentNode.className=\'playlist_item'+(nmbr%2)+'\'" href="#"><img src="images/buttons/delete.png"></a></td></tr></table></div>';

}
function choose(id)
{
        for(i=0;i<musics.length;i++)
                if(musics[i][0]==id)
                {
                        actual_music=i;
                        play();
                        return;
                }
}
function delete_file(target){


        if (confirm('Do your really wish to remove this music?'))
                location = "?remove_audio=" + target;
}
