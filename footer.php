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
    $('.ajax-link').click(function(e) {
        e.preventDefault();

        console.log($(this).attr('href'));

        $.get($(this).attr('href'), {}, (response) => {
            console.log(response);
            // $('.jail-cell-row').remove();

            //if (jailers != false) {
            //    jailers.forEach((data, index) => {
            //
            //        $('#jail-table tr:last').after('' +
            //            '<tr class="jail-cell-row">' +
            //            '<td>' + data.username + '</td>' +
            //            '<td>' + data.time + '</td>' +
            //            '<td><a class="jail-break-link" href="?jailbreak=' + data.id + '&token=<?php //echo $token ?>//" data-user-id="' + data.id + '" class="break-out-link">Break Out</a></td>' +
            //            '</tr>'
            //        );
            //
            //        $('.jail-break-link').click(function() {
            //            $('.jail-break-link').remove();
            //        });
            //    })
            //}
        }, "json");
    })
</script>