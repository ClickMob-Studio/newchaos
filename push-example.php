<script src="js/autobahn.min.js?123"></script>
<!-- <script>
    var conn = new ab.Session('wss://themafialife.com/socket/',
        function() {
            conn.subscribe('kittensCategory', function(topic, data) {
                console.log('New article published to category "' + topic + '" : ' + data.title);
            });
        },
        function() {
            console.warn('WebSocket connection closed');
        },
        {'skipSubprotocolCheck': true}
    );
</script> -->

<script>
        function connectToService() {

var conn; // The websocket connection variable

var onOpenWebsocketCallback = function () {
    // Subscribe for 'newsTopic' topic. We pass a callback function
    // to be executed when a message is broadcasted to this websocket
    conn.subscribe('kittensCategory', function (topic, data) {
        // Check the message content to see what this message is about
        console.log(data);
        if (data.about == 'subscribers') {
            console.log('Subscribers: ' + data.subscribers);
        } else if (data.about == 'publishing') {
            console.log('Published message: ' + data.message);
        } else {
            console.log('New post for "' + topic + '" (' + data.when + '): ' + data.subscribers);
        }
    });

    // When the user submits the form, we will send a message to the 'newsTopic' topic
    // document.querySelector("#messageForm").addEventListener("submit", function (e) {
    //     e.preventDefault();

    //     var message = document.messageForm.message.value;

    //     conn.publish('newsTopic', message);
    // });
};

var onCloseWebsocketCallback = function () {
    console.warn('WebSocket connection closed');
};

// We open a websocket to service's <IP>:<port> and when the connection
// is established we subscribe through the callback function
var conn = new ab.Session('wss://themafialife.com/socket/',
    onOpenWebsocketCallback,
    onCloseWebsocketCallback,
    {'skipSubprotocolCheck': true, 'verify_peer': false}
);
}
</script>