(function($){ 
    $( document ).ready(function() {
        console.log( "ready!" );

        // Restrict the donation to 3 dollars    
        $( "#donate-btn" ).click(function(e) {
            console.log("hello!")
            if( $("#donate-input").val() < 3 ){
                e.preventDefault();
                alert( "Please enter a minimum of $3" );
            }
            else{
                
            }
        });
    });
})(jQuery);