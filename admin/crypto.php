<?php
require "header.php";
    $url='https://bitpay.com/api/rates';
$json=json_decode( file_get_contents( $url ) );
$usd = 1;
$btc = 0;
$dollar=$btc=0;

foreach( $json as $obj ){
    if( $obj->code=='USD' ){
    $btc = $obj->rate;
    $store = ceil($btc);
    $fet = DBi::$conn->query("SELECT * FROM crypto WHERE company_name = 'Bitcoin'");
    $f = mysqli_fetch_assoc($fet);
    if($store > $f['cost']){
        $direction = 'up';
    }else if($store < $f['cost']){
        $direction = 'down';    
    }else{
        $direction = 'static';
    }
    DBi::$conn->query("INSERT INTO crypto_history (stock_id, cost) VALUES(".$f['id'].", ".$store.")");
    DBi::$conn->query("UPDATE crypto SET cost = ".$store.", direction = '".$direction."' WHERE company_name ='Bitcoin'");
    }
    if($obj->code == 'ETH'){
        
        $rates =($usd/$obj->rate)*$btc;
        $store = ceil($rates);
        $fet = DBi::$conn->query("SELECT * FROM crypto WHERE company_name = 'Ethereum'");
        $f = mysqli_fetch_assoc($fet);
        if($store > $f['cost']){
            $direction = 'up';
        }else if($store < $f['cost']){
            $direction = 'down';    
        }else{
            $direction = 'static';
        }
        DBi::$conn->query("INSERT INTO crypto_history (stock_id, cost) VALUES(".$f['id'].", ".$store.")");
    
        DBi::$conn->query("UPDATE crypto SET cost = ".$store.", direction = '".$direction."' WHERE company_name ='Ethereum'");
        }
    $rate = ($usd/$obj->rate)*$btc;
    echo $obj->code ."-".$rate."</br>";    
}

echo "1 bitcoin=\$" . $btc . "USD<br />";
$dollar=1 / $btc;
echo "10 dollars = " . round( $dollar * 10,8 )."BTC";