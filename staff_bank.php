<?php
include 'header.php';
if($user_class->admin < 1){
    exit;
}
?>
<div class='box_top'>Bank</div>
						<div class='box_middle'>
							<div class='pad'>
<style>
    
.upgrade-package {
    flex: 0 1 calc(50% - 20px); /* Keep as is, ensures two items per row */
    padding: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    margin-bottom: 20px; /* Ensure there's space at the bottom */
    border-radius: 10px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column; /* Keeps children stacked vertically */
}

.div-form-wrapper {
    display: flex;
    justify-content: space-between; /* Adjusted for better spacing */
    gap: 15px; /* This creates space between the flex items */
}

    /* Default styles for the bank containers */
.bank-container {
    width: 47%; /* Default width for desktop */
    float: left;
    margin: 0;
}

/* Media query for mobile devices */
@media only screen and (max-width: 767px) {
    .bank-container {
        width: 100%; /* Full width on mobile */
        float: none; /* Clear the float */
        margin: 20px 0; /* Add margin to separate sections on mobile */
    }
}

/* General reset for table elements */
table {
    width: 100%;
    text-align: left;
    margin: auto;
    border-collapse: collapse;
}

table td, table th {
    border: 1px solid #444;
    padding: 10px;
}

/* Style the horizontal rule */
hr {
    border: 0;
    border-bottom: thin solid #333;
}

/* Style the forms */
.bank-form {
    display: flex;
    justify-content: center; /* Center the form content */
    align-items: center;
    margin-top: 20px;
}

input[type="text"]{
    padding: 10px;
    margin: 5px;
    border: 1px solid #444;
    color: #FFF;
    border-radius: 5px;
    text-align: center; /* Center text inside inputs */
}

input[type="submit"] {
    cursor: pointer;
    background-color: #333; /* Dark grey background */
    color: #ccc; /* Light grey text */
}


/* Responsive design */
@media (max-width: 600px) {
    .bank-form {
        flex-direction: column;
    }
}

</style>
<?php
$rel_user = new User($user_class->relplayer);


echo "
<div>
<form method='GET'>
<label>Input a userid to view transaction</label>
<input type='number'name='user' id='name' required>
<button type='submit'> Submit</button>
</form>
</div>
";
if(isset($_GET['user'])){
    $_GET['user'] = intval($_GET['user']);
    echo "
<div id='banklog'>
    " . staff_banklog($_GET['user']) . "
</div>";
}

print <<<TEXT
<script>
    function updateBankLog(){
        // Retrieve input values
        let limit = $("#limit").val();
        let format = $("#format").val();
        let show = $("#show").val();

        // Check if limit is empty or not a number, default to '0'
        limit = limit === '' || isNaN(limit) ? '0' : limit;

        // Perform the post request with the potentially modified limit value
        $.post("ajax_banklog.php", {'limit': limit, 'format': format, 'show': show}, function (callback){
            $("#banklog").html(callback);
        });
    }
</script>
<br>
TEXT;
include 'footer.php';
