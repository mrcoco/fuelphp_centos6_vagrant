<?php
/**
 * Created by PhpStorm.
 * User: tsuzukitomoaki
 * Date: 15/05/27
 * Time: 16:30
 */

class Inputfilter_Myinputfilters
{
    /**
     * 文字エンコーディング検証のフィルタ
     *
     * @param string|array $value
     * @param string|array
     * @throws HttpInvalidInputException
     * @return string|array $value
     */
    public static function check_encoding($value)
    { if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($value), true)) !== __AM_CONTINUE__) return $__am_res; 
        // 配列の場合は再帰的に処理する
        if (is_array($value))
        {
            array_map(array('Myinputfilters', 'check_encoding'), $value);
            return $value;
        }

        // 文字エンコーディングを検証
        if (mb_check_encoding($value, Fuel::$encoding))
        {
            return $value;
        }
        else
        {
            // エラーの場合はログに記録
            static::log_error('Invalid character encoding', $value);

            // エラーを表示して終了する
            throw new Exception_HttpInvalidInputException('Invalid input data');
        }
    }

    /**
     * 改行コードとタブを覗く制御文字が含まれないかの検証フィルタ
     *
     * @param string|array $value
     * @return string|array
     * @throws HttpInvalidInputException
     */
    public static function check_control($value)
    { if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($value), true)) !== __AM_CONTINUE__) return $__am_res; 
        // 配列の場合は再帰的に処理
        if (is_array($value))
        {
            array_map(array('MyInputFilters', 'check_control'), $value);
            return $value;
        }

        // 改行コードとタブを覗く制御文字が含まれないか
        if (preg_match('/\A[\r\n\t[:^cntrl:]]*\z/u', $value) === 1)
        {
            return $value;
        }
        else
        {
            // エラーの場合はログに記録
            static::log_error('Invalid conrtrol characters', $value);

            // エラーを表示して終了する
            throw new Exception_HttpInvalidInputException('Invalid input data');
        }
    }

    // エラーをログに記録
    public static function log_error($msg, $value)
    { if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($msg, $value), true)) !== __AM_CONTINUE__) return $__am_res; 
        Log::error(
            $msg . Input::uri() . ' ' .
            rawurldecode($value) . ' ' .
            Input::ip() . ' " ' . Input::user_agent() . '"'
        );
    }
}