<?php

abstract class BokuPacks
{
    public $id;
    public $price;
    public $service;
    public $desc;
    public $param;

    public function __construct($id, $price, $service, $desc, $userid, $destiny)
    {
        $this->id = $id;
        $this->price = $price;
        $this->service = $service;
        $this->userid = $userid;
        $this->desc = $desc;
        $this->destiny = $destiny;
        $this->param = 'www.generalforces.com|' . $this->id . '|' . $this->userid . '|' . $this->destiny;
    }

    public function item_padrao()
    {
        $item = new RPItem($this->id);
        $item->setCode(str_replace(' ', '', $this->desc));
        $item->setName($this->desc);

        return $item;
    }

    abstract public function transformAtRPItem();
}

class B30RM extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(0, 400, '69e867f40decdc7bd00cb1b0', '30 RM Days', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddToAttribute('rmdays', 30);

        return $item;
    }
}
class B20RM extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(1, 300, '6a07edf20decdc7b1a6523d1', '20RM Days', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddToAttribute('rmdays', 20);

        return $item;
    }
}
class B10RM extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(2, 200, '6a0f994e0decdc7b1421a6fe', '10RM Days', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddToAttribute('rmdays', 10);

        return $item;
    }
}
class B5AP extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(3, 500, '6a1c38380decdc7b9f1c816f', '5 Awake Pills', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddItems(14, 5);

        return $item;
    }
}
class B2AP extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(4, 300, '6a38f15f0decdc7b41b4ce31', '2 Awake Pills', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddItems(14, 2);

        return $item;
    }
}
class B5BP extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(5, 490, '6a410d600decdc7b1a1b8b3e', '5 Body Guard Protect', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddItems(75, 5);

        return $item;
    }
}
class B2BP extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(6, 270, '6a461ff60decdc7bcb26de14', '2 Body Guard Protect', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddItems(75, 2);

        return $item;
    }
}

class BLAND extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(7, 270, '6a4b73430decdc7b25194971', '1 Acre of Land', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddLand(1, 1);

        return $item;
    }
}
class B350P extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(8, 600, '7356f6ac0decdc7be86d1078', '350 Points', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddToAttribute('points', 350);

        return $item;
    }
}
class B150P extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(9, 400, '735bd92e0decdc7b3fd89700', '150 Points', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddToAttribute('points', 150);

        return $item;
    }
}
class B50P extends BokuPacks
{
    public function __construct($userid, $destiny)
    {
        parent::__construct(10, 200, '7360ef880decdc7b1046c440', '50 Points', $userid, $destiny);
    }

    public function transformAtRPItem()
    {
        $item = $this->item_padrao();
        $item->AddToAttribute('points', 50);

        return $item;
    }
}

class Boku
{
    public $buttonCode;
    public $pack;
    public $packs = [];
    public $userid;
    public $destiny;

    public function Boku($userid, $destiny = null, $pack = null)
    {
        if ($pack != null) {
            $this->pack = $pack;
        }
        if ($destiny == null) {
            $destiny = $userid;
        }

        $this->userid = $userid;
        $this->destiny = $destiny;
    }

    public function makePacks()
    {
        $userid = $this->userid;
        $destiny = $this->destiny;

        $unorder = [new B30RM($userid, $destiny),new B20RM($userid, $destiny),new B10RM($userid, $destiny), new B5AP($userid, $destiny),
            new B2AP($userid, $destiny),new B5BP($userid, $destiny),new B2BP($userid, $destiny),new BLAND($userid, $destiny),new B350P($userid, $destiny),
            new B150P($userid, $destiny),new B50P($userid, $destiny),
            ];
        $this->packs = [];
        foreach ($unorder as $value) {
            $this->packs[$value->id] = $value;
        }
    }

    public function MakeRequest()
    {
        $this->pack = new $this->pack($this->userid, $this->destiny);
        $ch = curl_init();
        $tmp = sprintf('action=prepare&desc=%s&key=%s&merchant-id=tcardoso&param=%s&service-id=%s&timestamp=%s', urlencode($this->pack->desc), urlencode($this->pack->price), urlencode($this->pack->param), urlencode($this->pack->service), time());
        $param = sprintf('action=prepare&desc=%s&key=%s&merchant-id=tcardoso&param=%s&service-id=%s&timestamp=%s', $this->pack->desc, $this->pack->price, $this->pack->param, $this->pack->service, time());

        $param .= '4UZ8as7cTwbLTLCFKOwtPx5KLfI6qZYqTVw5FYvlDqwwfXUTBa0zv0XltADvMQcyBUJ5TOUFptNOReVCT9n5eI915yML5kw5zO3g';
        $tmp .= '&sig=' . self::CreateSig($param);

        curl_setopt($ch, CURLOPT_URL, 'https://api2.boku.com/billing/request?' . $tmp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $text = curl_exec($ch);
        curl_close($ch);

        return $this->ParseRequest($text);
    }

    public function check_avaiblity()
    {
        $this->pack = new $this->pack($this->userid, $this->destiny);
        $ch = curl_init();
        $country = 'us';
        $ip = $_SERVER['REMOTE_ADDR'];
        $tmp = sprintf('action=price&country=%s&desc=%s&ip-address=%s&key=%s&merchant-id=tcardoso&param=%s&service-id=%s&timestamp=%s', $country, urlencode($this->pack->desc), $ip, urlencode($this->pack->price), urlencode($this->pack->param), urlencode($this->pack->service), time());
        $param = sprintf('action=price&country=%s&desc=%s&ip-address=%s&key=%s&merchant-id=tcardoso&param=%s&service-id=%s&timestamp=%s', $country, $this->pack->desc, $ip, $this->pack->price, $this->pack->param, $this->pack->service, time());

        $param .= '4UZ8as7cTwbLTLCFKOwtPx5KLfI6qZYqTVw5FYvlDqwwfXUTBa0zv0XltADvMQcyBUJ5TOUFptNOReVCT9n5eI915yML5kw5zO3g';
        $tmp .= '&sig=' . self::CreateSig($param);

        curl_setopt($ch, CURLOPT_URL, 'https://api2.boku.com/billing/request?' . $tmp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $text = curl_exec($ch);
        curl_close($ch);

        $xml = new SimpleXMLElement($text);

        $country = $xml->{'geo-countrycode'};
        $tmp = sprintf('action=service-prices&merchant-id=tcardoso&service-id=%s&timestamp=%s', urlencode($this->pack->service), time());
        $param = sprintf('action=service-prices&merchant-id=tcardoso&service-id=%s&timestamp=%s', $this->pack->service, time());

        $param .= '4UZ8as7cTwbLTLCFKOwtPx5KLfI6qZYqTVw5FYvlDqwwfXUTBa0zv0XltADvMQcyBUJ5TOUFptNOReVCT9n5eI915yML5kw5zO3g';
        $tmp .= '&sig=' . self::CreateSig($param);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api2.boku.com/billing/request?' . $tmp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $text = curl_exec($ch);
        curl_close($ch);

        $xml = new SimpleXMLElement($text);

        foreach ($xml->{'service'}[0]->{'key-value'}[0]->{'pricing'} as $payments) {
            if (strtolower($payments['country']) == strtolower($country)) {
                return true;
            }
        }

        return false;
    }

    public static function check_payment($txi, $merchant_id)
    {
        $tmp = sprintf('action=verify-trx-id&merchant-id=%s&timestamp=%s&trx-id=%s', urlencode($merchant_id), time(), urlencode($txi));
        $param = sprintf('action=verify-trx-id&merchant-id=%s&timestamp=%s&trx-id=%s', $merchant_id, time(), $txi);

        $param .= '4UZ8as7cTwbLTLCFKOwtPx5KLfI6qZYqTVw5FYvlDqwwfXUTBa0zv0XltADvMQcyBUJ5TOUFptNOReVCT9n5eI915yML5kw5zO3g';
        $tmp .= '&sig=' . self::CreateSig($param);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api2.boku.com/billing/request?' . $tmp);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $text = curl_exec($ch);
        curl_close($ch);

        $xml = new SimpleXMLElement($text);

        if ($xml->{'result-code'} == 0) {
            return true;
        }

        return false;
    }

    public function ParseRequest($text)
    {
        $xml = new SimpleXMLElement($text);

        return $xml->{'buy-url'};
    }

    public static function CreateSig($enc)
    {
        return md5(utf8_encode(str_replace(['=','&'], '', $enc)));
    }
}
