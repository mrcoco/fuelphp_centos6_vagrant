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
    { if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table), true)) !== __AM_CONTINUE__) return $__am_res; 
        // テーブルのデータを削除
        static::empty_table($table);
    }

    protected static function empty_table($table)
    { if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($table), true)) !== __AM_CONTINUE__) return $__am_res; 
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