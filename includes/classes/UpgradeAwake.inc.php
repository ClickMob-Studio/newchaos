<?php

class UpgradeAwake
{
    public static $awake = [
        5 => [
            'sku' => 'AWAKE-5',
            'price' => 5,
            'credit' => 5,
        ],
        12 => [
            'sku' => 'AWAKE-12',
            'price' => 8.99,
            'credit' => 12,
        ],
        25 => [
            'sku' => 'AWAKE-25',
            'price' => 15,
            'credit' => 25,
        ],
        65 => [
            'sku' => 'AWAKE-65',
            'price' => 27,
            'credit' => 65,
        ],
    ];

    /**
     * Retrieve the information regarding the amount of awake pills they wish to purchase.
     *
     * @throws Exception
     *
     * @return int[]
     */
    public static function get(int $amount)
    {
        if (!isset(self::$awake[$amount])) {
            throw new \SoftException('Unable to purchase that amount of awake pills.');
        }

        return self::$awake[$amount];
    }

    /**
     * Retrieve all.
     *
     * @return array
     */
    public static function getAll()
    {
        return self::$awake;
    }
}
