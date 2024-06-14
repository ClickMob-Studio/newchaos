<?php

class UpgradeMoney
{
    public static $money = [
        500 => [
            'sku' => 'MONEY-500',
            'price' => 1.01,
            'credit' => 500,
        ],
        1100 => [
            'sku' => 'MONEY-1100',
            'price' => 18,
            'credit' => 1100,
        ],
        5750 => [
            'sku' => 'MONEY-5750',
            'price' => 30,
            'credit' => 5750,
        ],
        12000 => [
            'sku' => 'MONEY-12000',
            'price' => 50,
            'credit' => 12000,
        ],
    ];

    /**
     * Retrieve the information regarding the amount of points they wish to purchase.
     *
     * @throws Exception
     *
     * @return int[]
     */
    public static function get(int $amount)
    {
        if (!isset(self::$money[$amount])) {
            throw new \SoftException('Unable to purchase that amount of points.');
        }

        return self::$money[$amount];
    }

    /**
     * Retrieve all.
     *
     * @return array
     */
    public static function getAll()
    {
        return self::$money;
    }
}
