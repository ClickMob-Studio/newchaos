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

            if (requestInProcess) {
                console.log('**** IN PROCESS');
                return false;
            }

            requestInProcess = true;

            var request = $.ajax({
                url: $(this).attr('href'),
                method: "GET",
                dataType: "json"
            });
            request.done(function (res) {
                if (res.success) {
                    var resMes = "<div class='alert alert-info'><p>" + res.message + "</p></div>";
                } else {
                    var resMes = "<div class='alert alert-danger'><p>" + res.error + "</p></div>";
                }

                $(".ajax-message-holder").html(resMes);

                requestInProcess = false;
            });
        });ß
    });
</script>