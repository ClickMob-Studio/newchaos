<?php

class UpgradePoints
{
    public static $points = [
        15 => [
            'sku' => 'POINTS-15',
            'price' => 10,
            'credit' => 15,
        ],
        30 => [
            'sku' => 'POINTS-30',
            'price' => 20,
            'credit' => 30,
        ],
        45 => [
            'sku' => 'POINTS-45',
            'price' => 30,
            'credit' => 45,
        ],
        75 => [
            'sku' => 'POINTS-75',
            'price' => 50,
            'credit' => 75,
        ],
        135 => [
            'sku' => 'POINTS-135',
            'price' => 90,
            'credit' => 135,
        ],
        185 => [
            'sku' => 'POINTS-185',
            'price' => 115,
            'credit' => 185,
        ],
        210 => [
            'sku' => 'POINTS-210',
            'price' => 130,
            'credit' => 210,
        ],
        245 => [
            'sku' => 'POINTS-245',
            'price' => 150,
            'credit' => 245,
        ],

        345 => [
            'sku' => 'POINTS-345',
            'price' => 200,
            'credit' => 345,
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
        if (!isset(self::$points[$amount])) {
            throw new \SoftException('Unable to purchase that amount of points.');
        }

        return self::$points[$amount];
    }

    /**
     * Retrieve all.
     *
     * @return array
     */
    public static function getAll()
    {
        return self::$points;
    }
}