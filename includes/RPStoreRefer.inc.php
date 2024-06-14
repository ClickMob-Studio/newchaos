<?php

/**
 * class used to manage rp store items.
 *

 * @author Harish<harish@gmail.com>

 * @copyright http://www.prisionstruggle.com
 */
class RPStoreRefer extends RPStore
{
    public static $lables = [
        'rmdays' => DPRPSTORE_RP_DAYS,

        'bank' => DPRPSTORE_MONEY,

        'points' => DPRPSTORE_POINTS,
    ];
    private $store = [];

    /**
     * prepare rp store.
     */
    public function __construct()
    {
        parent::__construct();

        $item = new RPItem(500);

        $item->setCode('8RM');

        $item->setName('8 ' . DPRPSTORE_DAY_RP_PACK);

        $item->AddToAttribute('rmdays', 8);

        $item->AddPrice(1);

        $this->addItem($item);

        $item = new RPItem(501);

        $item->setCode('18RM');

        $item->setName('18 ' . DPRPSTORE_DAY_RP_PACK);

        $item->AddToAttribute('rmdays', 18);

        $item->AddPrice(2);

        $this->addItem($item);

        $item = new RPItem(502);

        $item->setCode('1Pills');

        $item->setName('1 ' . DPRPSTORE_AWAKE_PILLS);

        $item->AddItems(14, 1);

        $item->AddPrice(1);

        $this->addItem($item);

        $item = new RPItem(503);

        $item->setCode('3Pills');

        $item->setName('3 ' . DPRPSTORE_AWAKE_PILLS);

        $item->AddItems(14, 3);

        $item->AddPrice(2);

        $this->addItem($item);

        $item = new RPItem(504);

        $item->setCode('50Points');

        $item->setName('50 ' . DPRPSTORE_POINTS);

        $item->AddToAttribute('points', 50);

        $item->AddPrice(1);

        $this->addItem($item);

        $item = new RPItem(505);

        $item->setCode('135Points');

        $item->setName('135 ' . DPRPSTORE_POINTS);

        $item->AddToAttribute('points', 135);

        $item->AddPrice(2);

        $this->addItem($item);

        $item = new RPItem(506);

        $item->setCode('1Prot');

        $item->setName('1 ' . DPRPSTORE_GUARD_PROTECTION);

        $item->AddItems(75, 1);

        $item->AddPrice(1);

        $this->addItem($item);

        $item = new RPItem(507);

        $item->setCode('3Prot');

        $item->setName('3 ' . DPRPSTORE_GUARD_PROTECTION);

        $item->AddItems(75, 3);

        $item->AddPrice(2);

        $this->addItem($item);

        $item = new RPItem(508);

        $item->setCode('1ACRES');

        $item->setName('1 ' . REFER_ACRES_PACK);

        $item->AddLand(1, 1);

        $item->AddPrice(2);

        $this->addItem($item);
    }
}
