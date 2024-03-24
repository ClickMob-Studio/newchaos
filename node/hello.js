var http = require('http');

http.createServer(function (req, res) {
    res.writeHead(200, {
        'Content-Type': 'text/html'
    });
    res.end('Hello World!');
}).listen(3000);

var mysql = require('mysql');

var con = mysql.createConnection({
    host: "localhost",
    user: "aa_user",
    password: "ovShOg&iLtat",
    database: "aa"
});

con.connect(function (err) {
    if (err) throw err;
    console.log("Connected!");
    con.query("SELECT * FROM grpgusers WHERE id = 174", function (err, result, fields) {
        if (err) throw err;
        console.log(result);
        console.log(result[0].username);
    });
});