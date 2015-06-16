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
    { if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
        parent::setUp();

        if (!empty($this->tables))
        {
            $this->dbfixt($this->tables);
        }
    }

    protected function dbfixt($tables)
    { if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array($tables), false)) !== __AM_CONTINUE__) return $__am_res; 
        foreach ($tables as $table)
        {
            Test_Dbfixture::load($table['table']);
        }
    }
}