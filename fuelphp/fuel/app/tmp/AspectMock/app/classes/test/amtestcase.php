<?php
/**
 * Created by PhpStorm.
 * User: tsuzukitomoaki
 * Date: 15/05/28
 * Time: 1:03
 */

// AspectMock\Test クラスを test としてインポート
use AspectMock\Test as test;

abstract class Test_Amtestcase extends TestCase
{
    protected function tearDown()
    { if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
        test::clean(); // 登録したモックを全て削除
    }
}