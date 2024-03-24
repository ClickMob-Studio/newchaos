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
		var nowServer = new Date("<?= date('Y/M/d H:i:s'); ?>");

		var hServer = nowServer.getHours();   //  1
		var mServer = nowServer.getMinutes(); // 20
		var sServer = nowServer.getSeconds(); // 30  

		var nowLoad = new Date();
		var hLoad = nowLoad.getHours();   //  1
		var mLoad = nowLoad.getMinutes(); // 20
		var sLoad = nowLoad.getSeconds(); // 30

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
			var time = new Date();
			document.getElementById('servertime').innerHTML =
				time.toLocaleString('en-us', {
					timeZone: 'UTC',
					weekday: 'short',
					month: 'short',
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