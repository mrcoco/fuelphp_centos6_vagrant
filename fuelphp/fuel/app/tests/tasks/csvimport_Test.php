<?php

// Autoload できなかったので直接 require した
require_once(DOCROOT.'/fuel/app/tasks/csvimport.php');

/**
 * Class csvimport_Test
 *
 * @group Tasks
 */

class csvimport_Test extends Test_Dbtestcase
{
    protected $tables = array(
        array(
            'table' => 'events'
        ),
        array(
            'table' => 'performers'
        ),
        array(
            'table' => 'events_performer'
        ),
    );

    /**
     * csv import のタスクで、csv のデータ数と実際に db から読めるデータの数を比較する
     */
    public function test_csv_import()
    {
        $csvimport = new \Fuel\Tasks\Csvimport();
        $csvimport->run();

        foreach ($csvimport->csv_info as $c)
        {
            $expected = count($c['csv']);

            $is_relation = $c['config']['is_relation'];
            if ($is_relation !== '')
            {
                // 全てのモデルに紐付いているデータを足し合わせて中間テーブルの数と比較する
                $from_model_name = '\Model_' . ucfirst($c['config']['from']);
                $to_property_name = $c['config']['to'] . 's'; // TODO consider non-regular plurals
                $entries = $from_model_name::find('all');

                $actual = 0;
                foreach ($entries as $entry)
                {
                    $actual += count($entry->{$to_property_name});
                }

                $this->assertEquals($expected, $actual);
            }
            else
            {
                // csv file のデータの数と model から読み出せるデータの数が同じかをテストする
                $model_name = $c['config']['model_name'];
                $entry = $model_name::find('all');
                $this->assertEquals($expected, count($entry));
            }
        }


    }
}
