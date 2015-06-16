<?php
/**
 * Created by PhpStorm.
 * User: tsuzukitomoaki
 * Date: 15/05/27
 * Time: 23:18
 */

abstract class Test_Dbtestcase extends TestCase
{
    // テーブル定義
    protected $tables = array(
    );

    protected function setUp()
    {
        parent::setUp();

        if (!empty($this->tables))
        {
            $this->dbfixt($this->tables);
        }
    }

    protected function dbfixt($tables)
    {
        foreach ($tables as $table)
        {
            Test_Dbfixture::load($table['table']);
        }
    }
}