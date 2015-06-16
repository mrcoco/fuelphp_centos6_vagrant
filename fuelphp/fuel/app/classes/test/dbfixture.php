<?php
/**
 * Created by PhpStorm.
 * User: tsuzukitomoaki
 * Date: 15/05/27
 * Time: 23:23
 */

class Test_Dbfixture
{
    public static function load($table)
    {
        // テーブルのデータを削除
        static::empty_table($table);
    }

    protected static function empty_table($table)
    {
        if (DBUtil::table_exists($table))
        {
            DBUtil::truncate_table($table);
        }
        else
        {
            exit ('No such table: ' . $table . PHP_EOL);
        }
    }
}