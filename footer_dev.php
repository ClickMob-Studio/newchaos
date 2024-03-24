<?php
$stats = new User_Stats("1");
				echo '<br />';
				echo '<br />';
				echo '</div>';
			echo '</div>';
			echo'<div class="bottom row"></div>';
			echo'<div id="footer" class="wrap"></div>';
		echo'</center>';
	echo'</body>';
?>
	<script>
		var nowServer = new Date("<?= date('Y/m/d H:i:s'); ?>");

		var hServer = nowServer.getHours();   //  1
		var mServer = nowServer.getMinutes(); // 20
		var sServer = nowServer.getSeconds(); // 30  

		var nowLoad = new Date();
		var hLoad = nowLoad.getHours();   //  1
		var mLoad = nowLoad.getMinutes(); // 20
		var sLoad = nowLoad.getSeconds(); // 30

		var nowNowGlobal = new Date();

		function lastSunday(month, year) {
			var d = new Date();
			var lastDayOfMonth = new Date(Date.UTC(year || d.getFullYear(), month+1, 0));
			var day = lastDayOfMonth.getDay();
			return new Date(Date.UTC(lastDayOfMonth.getFullYear(), lastDayOfMonth.getMonth(), lastDayOfMonth.getDate() - day));
		}

		function isBST(date) {
			var d = date || new Date();
			var starts = lastSunday(2, d.getFullYear());
			starts.setHours(1);
			var ends = lastSunday(9, d.getFullYear());
			starts.setHours(1);
			return d.getTime() >= starts.getTime() && d.getTime() < ends.getTime();
		}

		function setTime() {			
			var nowNow = new Date();
			var hDiff = nowNow.getHours() - hLoad;   //  1 -  1 = 0
			var mDiff = nowNow.getMinutes() - mLoad; // 21 - 20 = 1
			var sDiff = nowNow.getSeconds() - sLoad; // 35 - 30 = 5

			var time = new Date(nowServer);
			
			time.setHours(time.getHours() + hDiff);
			time.setMinutes(time.getMinutes() + mDiff);
			time.setSeconds(time.getSeconds() + sDiff);
			
			if (isBST(time)) {
				time.setTime(time.getTime() + (60*60*1000));
			}

			document.getElementById('servertime').innerHTML =
				time.toLocaleString('en-us', {
					timeZone: 'UTC',
					weekday: 'short',
					month: 'long',
					day: '2-digit',
					hour: '2-digit',
					minute: '2-digit',
					second: 'numeric'
				}).replace(" AM", "am").replace(" PM","pm");
			
			var t = setTimeout(setTime, 500);
		}

		$(document).ready(function () {
			setTime();
		});
	</script>
<?php
echo'</html>';