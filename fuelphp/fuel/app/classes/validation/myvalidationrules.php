<?php
/**
 * Created by PhpStorm.
 * User: tsuzukitomoaki
 * Date: 15/05/27
 * Time: 17:14
 */

class Validation_Myvalidationrules
{
    /**
     * 改行コードやタブが含まれていないかの検証ルール
     *
     * @param string $value
     * @return boolean
     */
    public static function _validation_no_tab_and_newline($value)
    {
        // 改行コードやタブが含まれていないか
        if (preg_match('/\A[^\r\n\t]*\z/u', $value) === 1)
        {
            // 含まれていない
           return true;
        }
        else
        {
            // 含まれている
            return false;
        }
    }

    /**
     * Model でユニークの validation を行う
     * @param $val
     * @param $options
     * @return bool
     */
    public static function _validation_unique($val, $options)
    {
        list($table, $field) = explode('.', $options);

        $result = DB::select($field)
            ->where($field, '=', Str::lower($val))
            ->from($table)->execute();

        Validation::active()->set_message('unique', '「:label 」はユニークです。「:value」は既に登録されています。');

        return ! ($result->count() > 0);
    }
}