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
    { if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($value), true)) !== __AM_CONTINUE__) return $__am_res; 
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
}