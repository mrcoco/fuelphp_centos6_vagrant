<?php

class Model_Base extends \Orm\Model
{
    /**
     * 特定カラム値の種類を array で返す
     * @param $column
     * @return array
     */
    public static function get_kinds($column)
    {
        $kinds = DB::select($column)->from(static::table())->distinct()->execute()->as_array();
        $result = array();
        foreach ($kinds as $kind)
        {
            $result[] = $kind[$column];
        }
        return $result;
    }

    /**
     * 指定したカラムをフリーワード検索する
     * @param $search
     * @param $column_name
     * @param $limit
     * @return \Orm\Model|\Orm\Model[]
     */
    public static function search($search, $column_name, $limit)
    {
        // 検索文字列の作成
        if (!empty($search))
        {
            $search = '%' . $search . '%';
        }
        else
        {

            $search = '%%';
        }

        // 検索する
        $conditions['where'] = array();
        $conditions['where'][] = array($column_name, 'LIKE', $search);
        $conditions['limit'] = $limit;
        return static::find('all', $conditions);
    }

    /**
     * 指定したカラムと value が一致するデータを返す
     * @param $conditions
     * @param int $limit
     * @return \Orm\Model|\Orm\Model[]
     */
    public static function find_by($conditions, $limit=1)
    {
        // 検索する
        $conditions['where'] = $conditions;
        $conditions['limit'] = $limit;
        return static::find('all', $conditions);
    }

}
