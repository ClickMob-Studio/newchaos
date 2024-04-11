</div>
            </main>
        </div>
    </div>
    </div>

<footer class="text-center">
    &copy; ChaosCity
</footer>
</body>
</html>
<script>
function calcEXP(){
	$.post("ajax_expcalc.php", {level : $("#levelcalc").val()}, function(d){
		$("#levelrtn").html(d);
	});
}
</script>

<script type="text/javascript">
    $(document).ready(function() {
        let requestInProcess = false;
        $('.ajax-link').click(function(e) {
            e.preventDefault();

            let clicked = $(this);

            $(".ajax-alert-div").remove();
            $(this).hide();
            $(this).after('<img id="spinner" class="temp-spinner" src="images/ajax-loader.gif"/>');

            if (requestInProcess) {
                console.log('**** IN PROCESS');
                return false;
            }

            requestInProcess = true;

            var request = $.ajax({
                url: $(this).attr('href') + '&alv=yes',
                method: "GET",
                dataType: "json"
            });
            request.done(function (res) {

                if (res.success == false || res.success == 'false') {
                    var resMes = "<div class='alert alert-danger ajax-alert-div'><p>" + res.error + "</p></div>";
                } else {
                    var resMes = "<div class='alert alert-info ajax-alert-div'><p>" + res.message + "</p></div>";
                }

                $(".ajax-message-holder").html(resMes);
                $(".ajax-message-holder").show();
                $(".temp-spinner").remove();
                clicked.show();

                // $('html, body').animate({
                //     scrollTop: $(".ajax-message-holder").offset().top
                // }, 2000);


                requestInProcess = false;
            });
        });
    });
</script>

<?php if ($user_class->id == 2 && $user_class->box_hunt_count <= 20): ?>
    <?php $boxHuntChance = mt_rand(1,100); ?>
    <?php if ($boxHuntChance <= 5): ?>
        <script type="text/javascript">
            $(document).ready(function() {
                var xBoxPosi = Math.floor(Math.random()*1000);
                var yBoxPosi = Math.floor(Math.random()*1000);

                $(".box_middle").append('<img alt="Click Me!" src="/css/images/NewGameImages/mysterybox.png" style="position:absolute; top:'+xBoxPosi+'px; left:'+yBoxPosi+'px;" />');
            });
        </script>
    <?php endif; ?>
<?php endif; ?>
