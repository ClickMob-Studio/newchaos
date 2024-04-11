<?php

session_start();
include 'header.php';
?>

<div class="box_top"><h1>Back Alley</h1></div>
<div class="box_middle">
    <div class="row">
        <div class="col-md-12">
            <center>
                <p>
                    Welcome to the Back Alley! Here you will battle against different opponents,
                    which But will you take the risk when its 20% energy per attack.
                    If you fail you will find yourself in the hospital
                </p>

                <div id="ba-response-message" style="min-height: 60px; display: none;"></div>

                <a href="#" class="btn btn-primary ba-search-link">Search</a>
                <a href="#" class="btn btn-primary">Med Pack</a>

                <hr />

                <table class="new_table" id="newtables" style="width:100%;">
                    <thead>
                        <tr>
                            <th>Outcomes</th>
                        </tr>
                    </thead>
                    <tbody id="ba-tbody">
                    
                    </tbody>
                </table>
            </center>

        </div>
    </div>
</div>

<?php
include 'footer.php';
?>

<script type="text/javascript">
    $(document).ready(function() {
        let requestInProcess = false;

        $('.ba-search-link').click(function(e) {
            e.preventDefault();

            let clicked = $(this);

            $(".ajax-alert-div").remove();
            $(this).hide();
            $(this).after('<img id="spinner" class="temp-spinner" src="images/ajax-loader.gif"/>');

            if (requestInProcess) {
                return false;
            }

            requestInProcess = true;

            var request = $.ajax({
                url: 'ajax_ba_new.php?alv=yes',
                method: "GET",
                dataType: "json"
            });
            request.done(function (res) {
                if (res.success == false || res.success == 'false') {
                    var resMes = "<div class='alert alert-danger ajax-alert-div'><center><p>" + res.error + "</p></center></div>";

                    $('<tr><td>' + res.error + '</td></tr>').prependTo("#ta-tbody");
                } else {
                    var resMes = "<div class='alert alert-info ajax-alert-div'><center><p>" + res.message + "</p></center></div>";
                    $('<tr><td>' + res.message + '</td></tr>').prependTo("#ta-tbody");
                }

                $("#ba-response-message").html(resMes);
                $("#ba-response-message").show();
                $(".temp-spinner").remove();
                clicked.show();

                requestInProcess = false;
            });
        });
    });
</script>
