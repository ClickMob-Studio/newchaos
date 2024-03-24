<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    $('#nameSearch').on('input', function() {
        $.getJSON('searchAjax.php?query=' + $(this).val(), function(data) {
            // Clear the search results
            $('#searchResults').empty();
            
            // Add each player's name to the search results
            $.each(data, function(i, name) {
                $('#searchResults').append('<p>' + name + '</p>');
            });
        });
    });
});
</script>
