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
    {
        test::clean(); // 登録したモックを全て削除
    }
}