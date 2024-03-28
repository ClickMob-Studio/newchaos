</div>
						</div>
						<div class="box_bottom"></div>
					</div>
				</div>
				<div class="spacer"></div>
			</div>
			<div class="bottom_content row"></div>
			
			
		</div>
		<div id="footer" class="row">
			<span>Chaos City RPG</span><br />
			&copy; COPYRIGHT 2024+ . All Rights Reserved.<br />

		</div>
	</div>
</div>
</div>
<script>
function calcEXP(){
	$.post("ajax_expcalc.php", {level : $("#levelcalc").val()}, function(d){
		$("#levelrtn").html(d);
	});
}
</script>