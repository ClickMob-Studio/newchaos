<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 class miniplayer extends playerCOM
 {
     public function miniplayer($user_class, $resize = 1, $lvl = '', $autostart = -1)
     {
         parent::__construct($user_class, $resize, $lvl, $autostart);
         $this->files = [0 => Playlist::getFile($this->user_class)];
         if (count($this->files[0]) == 0) {
             throw new Exception();
         }
     }

     public function javascriptCode()
     {
         $this->includes(); ?>

			<script>
		  	<?php
                $add = 'eval(musics=new Array(';
         foreach ($this->files as $i => $file) {
             if ($i != 0) {
                 $add .= ',';
             }
             $add .= "new Array('" . $i . "','" . MEDIA_FILES_PATH . '/' . addslashes($file['path']) . "','" . addslashes($file['name']) . "')";
         }
         echo $add . '));'; ?>

                              musicPlayer(0,<?php echo $this->autostart; ?>);
                              <?php if ($this->autostart) {
             echo 'play();';
         } ?>



			</script> <?php
     }

     public function htmlCode()
     {
         ?>
                            <table class="playersmall" id="all">
				<tr class="pl1">
					<td><table><tr>
					<td class="pl1_botoes">
						<img src ="images/player/botoes.gif" width="194" height="40" border="0"
						alt="Player Control" usemap ="#muellermap">
						<map id ="muellermap" name="muellermap">
							<area shape ="rect" coords ="42,2,76,37" onclick="previous()" class="link"
							alt="<?php echo POPUP_PLAYER_PREV; ?>">
							<area shape ="rect" coords ="119,2,154,37" onclick="play()" class="link"
							alt="<?php echo POPUP_PLAYER_PLAY; ?>">
							<area shape ="rect" coords ="80,2,114,37" onclick="pause()" class="link"
							alt="<?php echo POPUP_PLAYER_PAUSE; ?>">
							<area shape ="rect" coords ="3,2,36,37" onclick="stop()" class="link"
							alt="<?php echo POPUP_PLAYER_STOP; ?>">
							<area shape ="rect" coords ="158,2,192,37" onclick="next()" class="link"
							 alt="<?php echo POPUP_PLAYER_NEXT; ?>">
						</map>
						<table>
							<tr>
								<td><img src="images/volume.png" width="20" height="20"></td><td>
						<form>
						<input name="sliderValue" id="sliderValue" type="hidden" size="3">
						<script src="includes/js/slider_tei.js"></script>

						<script>

							var A_TPL = {
								'b_vertical' : false,
								'b_watch': true,
								'n_controlWidth': 120,
								'n_controlHeight': 10,
								'n_sliderWidth': 25,
								'n_sliderHeight': 15,
								'n_pathLeft' : 1,
								'n_pathTop' : -2,
								'n_pathLength' : 103,
								's_imgControl': 'images/bluev_bg.gif',
								's_imgSlider': 'images/bluev_sl.gif',
								'n_zIndex': 1
							}
							var A_INIT = {
								's_form' : 0,
								's_name': 'sliderValue',
								'n_minValue' : 0,
								'n_maxValue' : 100,
								'n_value' : 5,
								'n_step' : 1,
								'setFunc': volumeUpdate,
								'def':100
							}

							var volume_slider=new slider(A_INIT, A_TPL);
						</script>
						</td></tr>
						</table>
						</form>

					</td>
					<td class="pl1_visualizador">
						<table>
							<tr><td class="pl1_visualizador_cord_upper"></td></tr>


							<tr ><td class="pl1_visualizador_back">
								<table class="visualizador_text">
								<tr><td><div id="title"></div></td><td rowspan="2"><table><tr><td><div id="equalizer"></div></td><td><div id="equalizer1"></div></td></tr></table></td></tr>
								<tr><td><div id="artist"></td></div>
								</tr>

							</table>

							</td></tr>
							<tr><td>
						<table>
							<tr>
								<td><div class="time_color" id="elapsed">00:00</div></td><td>
						<form>
						<input name="sliderValue1" id="sliderValue1" type="hidden" size="3">

						<script>

							var A_TPL = {
								'b_vertical' : false,
								'b_watch': true,
								'n_controlWidth': 120,
								'n_controlHeight': 10,
								'n_sliderWidth': 25,
								'n_sliderHeight': 15,
								'n_pathLeft' : 1,
								'n_pathTop' : -2,
								'n_pathLength' : 103,
								's_imgControl': 'images/bluev_bg.gif',
								's_imgSlider': 'images/bluev_sl.gif',
								'n_zIndex': 1
							}
							var A_INIT = {
								's_form' : 0,
								's_name': 'sliderValue1',
								'n_minValue' : 0,
								'n_maxValue' : 100,
								'n_value' : 20,
								'n_step' : 1,
								 'setFunc': TimeScroll,
								 'def':0
							}

							var global_slider=new slider(A_INIT, A_TPL);
						</script>
						</td><td><div id="stime" class="time_color">Total:00:00<div></td></tr>
						</table>
						</form></td></tr>
						</table>
					</td>
				</tr></table></td></tr>
				</table>
			<?php
     }
 }
?>
