<?php
/**
 * Created by PhpStorm.
 * User: tsuzukitomoaki
 * Date: 15/06/09
 * Time: 16:56
 */

namespace Fuel\Tasks;

use Fuel\Core\Config;
use Fuel\Core\File;
use Fuel\Core\Format;
use Fuel\Core\Inflector;

class Csvimport
{
    // for test
    public $csv_info = array();

    public function run()
    {
        echo 'Start importing csv to mysql' . PHP_EOL;

        // load config
        Config::load('csvimport', true);
        $configs = Config::get('csvimport.configs');

        foreach ($configs as $class_name => $config)
        {
            $this->import_csv_to_db($config);
        }
    }

    /**
     * @param $config config data
     * @throws DomainException
     */
    private function import_csv_to_db($config)
    {
        $file_name = $config['file_name'];
        $model_name = $config['model_name'];
        $is_relation = $config['is_relation'];

        echo 'importing ' . $file_name . PHP_EOL;

        $csv = $this->get_csv($file_name);
        if ($csv === false)
        {
            throw new \DomainException('Could not get csv file. path = ' . DOCROOT.'/csv/'.$file_name);
        }

        if ($is_relation === '')
        {
            $this->save_data($csv, $model_name);
        }
        else
        {
            $this->save_relation($csv, $config);
        }

        // for test
        $this->set_csv_info($config, $csv);
    }

    /**
     * @param $file_name
     * @return array|bool
     * @throws \InvalidPathException
     */
    private function get_csv($file_name)
    {
        $file_path = DOCROOT.'/csv/'.$file_name;
        if (File::exists($file_path)) {
            $file = File::read($file_path, true);
            $csv = Format::forge($file, 'csv')->to_array();
            return $csv;
        }
        return false;
    }

    /**
     * Insert or update csv data to db
     * @param $csv csv data as string
     * @param $model_name model name
     * @throws DomainException
     */
    private function save_data($csv, $model_name)
    {
        foreach ($csv as $line) {

            // check if same id already exists
            $data = $model_name::find($line['id']);
            if ($data == null) {
                $data = $model_name::forge();
            }

            // update or insert data with csv value
            $data->set($line);
            if (!$data->save())
            {
                throw new \DomainException('Data cannot be saved. model ' . $model_name . ' id ' . $line['id']);
            }
        }
    }

    /**
     * Insert or update relation
     * @param $csv csv data as string
     * @param $config config data dfining relation
     * @throws DomainException
     * TODO currently only support has_many relation
     */
    private function save_relation($csv, $config)
    {
        if ($config['is_relation'] !== 'has_many')
        {
            throw new \DomainException('relation is supported ' . $config['is_relation']);
        }

        foreach ($csv as $line)
        {
            $from_model_name = '\Model_' . ucfirst($config['from']);
            $from_id = $config['from'] . '_id';
            $to_model_name = '\Model_' . ucfirst($config['to']);
            $to_id = $config['to'] . '_id';
            $to_property_name = Inflector::pluralize($config['to']);

            $from_data = $from_model_name::find($line[$from_id]);
            $to_data = $to_model_name::find($line[$to_id]);
            $from_data->{$to_property_name}[] = $to_data;

            if (!($from_data and $to_data and $from_data->save()))
            {
                throw new \DomainException('Relation cannot be saved. from id = ' . $from_id . ' to_id = ' . $to_id);
            }
        }
    }

    /**
     * Set csv information to be used in test classes
     * @param $config config file data
     * @param $csv csv to be imported
     */
    private function set_csv_info($config, $csv)
    {
        $this->csv_info[] = array(
            'config' => $config,
            'csv' => $csv,
        );
    }
}