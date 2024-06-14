<?php

/**
 * class used to manage rp store items.
 *
 * @author Harish<harish@gmail.com>
 * @copyright http://www.prisionstruggle.com
 */
class RPStore
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
        $item = new RPItem(1);
        $item->setCode('30RM');
        $item->setName('30 ' . DPRPSTORE_DAY_RP_PACK);
        $item->AddToAttribute('rmdays', 30);
        $item->AddToAttribute('bank', 10000);
        $item->AddToAttribute('points', 50);
        $item->AddPrice(3, 'moneybooker');
        $item->AddPrice(3);
        $item->AddPrice(3, 'wallie');
        $item->AddPrice(4, 'phone');
        $item->AddPrice(5, 'pbc');
        $this->addItem($item);

        $item = new RPItem(2);
        $item->setCode('60RM');
        $item->setName('60 ' . DPRPSTORE_DAY_RP_PACK);
        $item->AddToAttribute('rmdays', 60);
        $item->AddToAttribute('bank', 25000);
        $item->AddToAttribute('points', 125);
        $item->AddPrice(6, 'moneybooker');
        $item->AddPrice(6);
        $item->AddPrice(6, 'wallie');
        $item->AddPrice(8, 'phone');
        $item->AddPrice(8, 'pbc');
        $this->addItem($item);

        $item = new RPItem(3);
        $item->setCode('90RM');
        $item->setName('90 ' . DPRPSTORE_DAY_RP_PACK);
        $item->AddToAttribute('rmdays', 90);
        $item->AddToAttribute('bank', 75000);
        $item->AddToAttribute('points', 200);
        $item->AddPrice(9, 'moneybooker');
        $item->AddPrice(9);
        $item->AddPrice(9, 'wallie');
        $item->AddPrice(11, 'pbc');
        $item->AddPrice(12, 'phone');
        $this->addItem($item);

        $item = new RPItem(4);
        $item->setCode('5Pills');
        $item->setName('5 ' . DPRPSTORE_AWAKE_PILLS);
        $item->AddItems(14, 5);
        $item->AddPrice(3, 'moneybooker');
        $item->AddPrice(3);
        $item->AddPrice(3, 'wallie');
        $item->AddPrice(4, 'phone');
        $item->AddPrice(5, 'pbc');
        $this->addItem($item);

        $item = new RPItem(7);
        $item->setCode('30Pills');
        $item->setName('30 ' . DPRPSTORE_AWAKE_PILLS);
        $item->AddItems(14, 30);
        $item->AddPrice(10, 'moneybooker');
        $item->AddPrice(10);
        $item->AddPrice(10, 'wallie');
        $item->AddPrice(12, 'pbc');
        $item->AddPrice(13.50, 'phone');
        $this->addItem($item);

        $item = new RPItem(8);
        $item->setCode('60Pills');
        $item->setName('60 ' . DPRPSTORE_AWAKE_PILLS);
        $item->AddItems(14, 60);
        $item->AddPrice(17, 'moneybooker');
        $item->AddPrice(17);
        $item->AddPrice(17, 'wallie');
        $item->AddPrice(19, 'pbc');
        $this->addItem($item);

        $item = new RPItem(9);
        $item->setCode('250P');
        $item->setName('250 ' . DPRPSTORE_POINTS);
        $item->AddToAttribute('points', 250);
        $item->AddPrice(3, 'moneybooker');
        $item->AddPrice(3);
        $item->AddPrice(3, 'wallie');
        $item->AddPrice(4, 'phone');
        $item->AddPrice(5, 'pbc');
        $this->addItem($item);

        $item = new RPItem(10);
        $item->setCode('1000P');
        $item->setName('1,000 ' . DPRPSTORE_POINTS);
        $item->AddToAttribute('points', 1000);
        $item->AddPrice(9, 'moneybooker');
        $item->AddPrice(9);
        $item->AddPrice(9, 'wallie');
        $item->AddPrice(11, 'pbc');
        $item->AddPrice(12, 'phone');
        $this->addItem($item);

        $item = new RPItem(11);
        $item->setCode('5000P');
        $item->setName('5,000 ' . DPRPSTORE_POINTS);
        $item->AddToAttribute('points', 5000);
        $item->AddPrice(29, 'moneybooker');
        $item->AddPrice(29);
        $item->AddPrice(29, 'wallie');
        $item->AddPrice(31, 'pbc');
        $this->addItem($item);

        $item = new RPItem(12);
        $item->setCode('10000P');
        $item->setName('10,000 ' . DPRPSTORE_POINTS);
        $item->AddToAttribute('points', 10000);
        $item->AddPrice(45, 'moneybooker');
        $item->AddPrice(45);
        $item->AddPrice(45, 'wallie');
        $item->AddPrice(47, 'pbc');
        $this->addItem($item);

        $item = new RPItem(23);
        $item->setCode('5Prot');
        $item->setName('5 ' . DPRPSTORE_GUARD_PROTECTION);
        $item->AddItems(75, 5);
        $item->AddPrice(3, 'moneybooker');
        $item->AddPrice(3);
        $item->AddPrice(3, 'wallie');
        $item->AddPrice(4, 'phone');
        $item->AddPrice(5, 'pbc');
        $this->addItem($item);

        $item = new RPItem(24);
        $item->setCode('30Prot');
        $item->setName('30 ' . DPRPSTORE_GUARD_PROTECTION);
        $item->AddItems(75, 30);
        $item->AddPrice(10, 'moneybooker');
        $item->AddPrice(10);
        $item->AddPrice(10, 'wallie');
        $item->AddPrice(12, 'pbc');
        $item->AddPrice(13.50, 'phone');
        $this->addItem($item);

        $item = new RPItem(25);
        $item->setCode('60Prot');
        $item->setName('60 ' . DPRPSTORE_GUARD_PROTECTION);
        $item->AddItems(75, 60);
        $item->AddPrice(17, 'moneybooker');
        $item->AddPrice(17);
        $item->AddPrice(17, 'wallie');
        $item->AddPrice(19, 'pbc');
        $this->addItem($item);

        $item = new RPItem(17);
        $item->setCode('Killer');
        $item->setName(REFER_KILLER_PACK);
        $item->AddItems(45, 1);
        $item->AddItems(50, 1);
        $item->AddItems(14, 20);
        $item->AddToAttribute('points', 2000);
        $item->AddPrice(40, 'moneybooker');
        $item->AddPrice(40);
        $item->AddPrice(40, 'wallie');
        $item->AddPrice(42, 'pbc');
        $this->addItem($item);

        $item = new RPItem(5);
        $item->setCode('9mm');
        $item->setName(DPRPSTORE_9MM_PISTOL);
        $item->AddItems(38, 1);
        $item->AddToAttribute('points', 250);
        $item->AddPrice(15, 'moneybooker');
        $item->AddPrice(15);
        $item->AddPrice(15, 'wallie');
        $item->AddPrice(17, 'pbc');
        $item->AddPrice(20, 'phone');
        $this->addItem($item);

        $item = new RPItem(14);
        $item->setCode('2ACRES');
        $item->setName('2 ' . REFER_ACRES_PACK);
        $item->AddLand(1, 2);
        $item->AddPrice(3, 'moneybooker');
        $item->AddPrice(3);
        $item->AddPrice(3, 'wallie');
        $item->AddPrice(4, 'phone');
        $item->AddPrice(5, 'pbc');
        $this->addItem($item);

        $item = new RPItem(15);
        $item->setCode('10ACRES');
        $item->setName('10 ' . REFER_ACRES_PACK);
        $item->AddLand(1, 10);
        $item->AddPrice(12, 'moneybooker');
        $item->AddPrice(12);
        $item->AddPrice(12, 'wallie');
        $item->AddPrice(14, 'pbc');
        $item->AddPrice(16, 'phone');
        $this->addItem($item);

        $item = new RPItem(16);
        $item->setCode('20ACRES');
        $item->setName('20 ' . REFER_ACRES_PACK);
        $item->AddLand(1, 20);
        $item->AddPrice(20, 'moneybooker');
        $item->AddPrice(20);
        $item->AddPrice(20, 'wallie');
        $item->AddPrice(22, 'pbc');
        $this->addItem($item);

        $item = new RPItem(6);
        $item->setCode('BSP');
        $item->setName('1 ' . DPRPSTORE_SURVIVAL_PACK);
        $item->AddItems(36, 1);
        $item->AddItems(53, 1);
        $item->AddItems(14, 5);
        $item->AddToAttribute('bank', 25000);
        $item->AddToAttribute('points', 25);
        $item->AddPrice(9, 'moneybooker');
        $item->AddPrice(9);
        $item->AddPrice(9, 'wallie');
        $item->AddPrice(11, 'pbc');
        $item->AddPrice(12, 'phone');
        $this->addItem($item);
        /*
        $item = new RPItem(26);
        $item->setCode('UPack');
        $item->setName(REFER_ULTIMATE_PACK);
        $item->AddItems ( 52, 1 );
        $item->AddItems ( 53, 1 );
        $item->AddToAttribute ( 'points', 250 );
        $item->AddPrice(55);
                $item->AddPrice(55, 'moneybooker');
        $item->AddPrice(55, 'wallie');
        $item->AddPrice(57, 'pbc');
        $this->addItem($item);
        */
        $item = new RPItem(13);
        $item->setCode('InPack');
        $item->setName(REFER_INSANE_TRAINING_PACK);
        $item->AddToAttribute('rmdays', 30);
        $item->AddToAttribute('bank', 100000);
        $item->AddItems(14, 100);
        $item->AddToAttribute('points', 40000);
        $item->AddPrice(199, 'moneybooker');
        $item->AddPrice(199);
        $item->AddPrice(201, 'pbc');
        $this->addItem($item);

        /*$upgradePacks = (int)Variable::GetValue('upgradePacks');
        $item = new RPItem(27);
        $item->setCode('UpgradePack');
        $item->setName(UPGRADE_PACK);
        $item->AddItems ( 114, 1 );
        $item->AddItems ( 115, 1 );
        $item->AddItems ( 116, 1 );
        /*$item->AddQuantity($upgradePacks);
        if($upgradePacks <= 0)
            $item->Disable();*/
        /* $item->AddPrice(20, 'moneybooker');
         $item->AddPrice(20);
         $item->AddPrice(20, 'wallie');
         $item->AddPrice(20, 'pbc');
         //$item->setVariable('upgradePacks');
         $this->addItem($item);
                */
        $halloweenPacks = Variable::GetValue('halloweenPacks');

        /*$item = new RPItem(666);
        $item->setCode('HaPack');
        $item->setName(HA_PACK);
        $item->AddItems ( 350, 1 );
        $item->AddItems ( 351, 1 );
                $item->AddItems ( 14, 60 );
                $item->AddToAttribute ( 'points', 1000 );
        $item->AddQuantity($halloweenPacks);
        if($halloweenPacks <= 0)
            $item->Disable();
        $item->AddPrice(65);
                $item->AddPrice(65, 'moneybooker');
        $item->AddPrice(65, 'pbc');
        $this->addItem($item);*/

        $item = new RPItem(666);
        $item->setCode('HaPack');
        $item->setName('Halloween Pack');
        $item->AddItems(350, 1);
        $item->AddItems(351, 1);
        $item->AddItems(14, 60);
        $item->AddToAttribute('points', 2000);
        $item->AddQuantity($halloweenPacks);
        if ($halloweenPacks <= 0) {
            $item->Disable();
        }
        $item->AddPrice(65);
        $item->AddPrice(65, 'moneybooker');
        $item->AddPrice(67, 'pbc');
        $this->addItem($item);

        $item = new RPItem(999);
        $item->setCode('XmPack');
        $item->setName('Christmas Pack');
        $item->AddItems(907, 1);
        $item->AddItems(908, 1);
        $item->AddItems(909, 1);
        $item->AddToAttribute('points', 1000);
        //$item->AddQuantity($halloweenPacks);
        //if($halloweenPacks <= 0)
        //$item->Disable();
        $item->AddPrice(50);
        $item->AddPrice(50, 'moneybooker');
        $item->AddPrice(52, 'pbc');
        $this->addItem($item);

        /*$item = new RPItem(667);
        $item->setCode('HaPack2');
        $item->setName('Halloween Pack');
        $item->AddItems ( 350, 1 );
        $item->AddItems ( 351, 1 );
        $item->AddItems ( 14, 60 );
        $item->AddToAttribute ( 'points', 2000 );
        $item->AddQuantity($halloweenPacks);
        if($halloweenPacks <= 0)
            $item->Disable();
        $item->AddPrice(50);
        $item->AddPrice(50, 'moneybooker');
        $item->AddPrice(52, 'pbc');
        $item->AddGameMoney(250000000);
        $this->addItem($item);*/

        /*$item = new RPItem(300);
        $item->setCode('Special Pack');
        $item->setName('60 ' . DPRPSTORE_AWAKE_PILLS);*/

        $item = new RPItem(250);
        $item->setCode('HTML Name');
        $item->setName(RPNAMES_HTML_USERNAME);
        $item->AddPrice(35, 'moneybooker');
        $item->AddPrice(35);
        $item->AddPrice(35, 'wallie');
        $item->AddPrice(37, 'pbc');
        $this->addItem($item, 'rpnames');

        $item = new RPItem(260);
        $item->setCode('Simple Javascript Name');
        $item->setName(RPNAMES_SIMPLE_JS_USERNAME);
        $item->AddPrice(50, 'moneybooker');
        $item->AddPrice(50);
        $item->AddPrice(50, 'wallie');
        $item->AddPrice(52, 'pbc');
        $this->addItem($item, 'rpnames');

        $item = new RPItem(270);
        $item->setCode('Advanced Javascript Name');
        $item->setName(RPNAMES_ADVANCE_JS_USERNAME);
        $item->AddPrice(90, 'moneybooker');
        $item->AddPrice(90);
        $item->AddPrice(90, 'wallie');
        $item->AddPrice(92, 'pbc');
        $this->addItem($item, 'rpnames');

        $item = new RPItem(280);
        $item->setCode('Image Name');
        $item->setName(RPNAMES_CUSTOM_IMAGE_USERNAME);
        $item->AddPrice(75, 'moneybooker');
        $item->AddPrice(75);
        $item->AddPrice(75, 'wallie');
        $item->AddPrice(77, 'pbc');
        $this->addItem($item, 'rpnames');
    }

    /**
     * Function used to add RP item.
     *
     * @param string $cat
     */
    public function addItem(RPItem $item, $cat = 'store')
    {
        $item->setCategory($cat);
        $this->store[$item->GetId()] = $item;
    }

    /**
     * Function used to get rp store items array.
     *
     * @param string $cat
     *
     * @return array
     */
    public function getItems($cat = 'store')
    {
        if (empty($this->store)) {
            return [];
        }

        if (strtolower($cat) == 'all') {
            return $this->store;
        }

        $return = [];

        foreach ($this->store as $id => $item) {
            if ($item->GetCategory() == $cat) {
                $return[$id] = $item;
            }
        }

        return $return;
    }

    /**
     * Get item on the bases of id.
     *
     * @param Number $id
     *
     * @return array
     */
    public function getItem($id)
    {
        return $this->store[$id];
    }

    public function getItemByCode($code)
    {
        $items = $this->getItems('all');

        foreach ($this->store as $id => $item) {
            if ($item->GetCode() == $code) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Function returns the pack name.
     *
     * @param Number $id
     *
     * @return unknown
     */
    public function getItemName($id)
    {
        return $this->store[$id]->item['name'];
    }

    /**
     * Returns the attribute lables.
     *
     * @param string $attr
     *
     * @return string
     */
    public function getLable($attr)
    {
        return 	self::$lables[$attr];
    }
}
