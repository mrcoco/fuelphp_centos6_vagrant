<?php

class Util_File
{
    /**
     * その日の log ファイルの内容を 1 行が 1 データとなるように array で取得する
     * @return array|string
     */
    public static function get_log_as_array()
    {
        $current_year = Date::forge()->format("%Y");
        $current_month = Date::forge()->format("%m");
        $current_date = Date::forge()->format("%d");

        $log_file_path = DOCROOT . '../fuel/app/logs/' . $current_year . '/' . $current_month . '/' . $current_date . '.php';
        $result = array();
        if (\Fuel\Core\File::exists($log_file_path))
        {
            $file = fopen($log_file_path, "r");
            if($file){
                while ($line = fgets($file)) {
                    if (strpos($line, "ERROR") !== FALSE || strpos($line, 'WARNING') !== FALSE)
                    {
                        $result[] = $line;
                    }
                }
            }
            fclose($file);
        }
        return $result;
    }
}