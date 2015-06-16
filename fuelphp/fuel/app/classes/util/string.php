<?php

class Util_String {

    /**
     * $arg1 と $arg2 で囲まれている部分を抽出してリストにして返す
     * @param $str
     * @param $arg1
     * @param $arg2
     * @return array
     */
    public static function explode_contents_inside($str, $arg1, $arg2) {
        $result = array();

        while(true) {

            $first_index = strpos($str, $arg1);

            // finish break here
            if (!$first_index) {
                break;
            }

            $second_index = strpos($str, $arg2) + strlen($arg2);
            $text = substr($str, $first_index, $second_index - $first_index);
            array_push($result, $text);
            $str = substr_replace($str, "", $first_index, $second_index - $first_index);
        }
        return $result;
    }

    /**
     * $arg1 と $arg2 の間にある文字列を返す
     * @param $str
     * @param $arg1
     * @param $arg2
     * @return string
     */
    public static function get_content_inside($str, $arg1, $arg2) {
        $first_index = strpos($str, $arg1);
        if (!$first_index) {
            return '';
        }

        $first_index += strlen($arg1);
        $second_index = strpos($str, $arg2, $first_index);
        $text = substr($str, $first_index, $second_index - $first_index);

        return $text;
    }

    /**
     * $arg1 と $arg2 で囲まれている部分を削除する
     * $arg1 と $arg2 も削除
     * @param $str
     * @param $arg1
     * @param $arg2
     * @return mixed
     */
    public static function remove_between($str, $arg1, $arg2) {
        while(true) {
            $first_index = strpos($str, $arg1);

            // finish break here
            if (!$first_index) {
                break;
            }

            $second_index = strpos($str, $arg2) + strlen($arg2);

            if ($first_index >= $second_index) {
                break;
            }

            $str = substr_replace($str, "", $first_index, $second_index - $first_index);
        }
        return $str;
    }

    /**
     * コメントに記載されている処理を行う
     * @param $str
     * @return mixed|string
     */
    public static function simplify($str) {
        // 全角を半角に
        $result = mb_convert_kana($str,"a", 'UTF-8');

        // スペースの削除
        $result = trim($result);
        $result = str_replace('　', '', $result);
        $result = str_replace(' ', '', $result);

        // \t の削除
        $result = str_replace("\t", '', $result);

        // () の中身削除
        $result = static::remove_between($result, "(", ")");

        // [] の中身削除
        $result = static::remove_between($result, "[", "]");

        // 半角記号を取り除く
        $result = preg_replace('/[][}{)(!"#$%&\'~|\*+,\/@.\^<>`;:?_=\\\\-]/i','', $result);

        return $result;
    }

    /**
     * 特定の文字列で開始しているかどうか
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function starts_with($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    /**
     * 特定の文字列で終了しているかどうか
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public static function ends_with($haystack, $needle) {
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }

    /**
     * 日付を表すかどうか
     * @param $date
     * @return bool
     */
    public static function is_date($date) {
        $dates = explode('～', $date);
        foreach ($dates as $d) {
            $dt = DateTime::createFromFormat("Y/m/d", $d);
            $is_date = $dt !== false && !array_sum($dt->getLastErrors());
            if ($is_date === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 日付を開始日と終了日にして返す
     * 1 日のものは開始日と終了日が同じになる
     * @param $date
     * @return array
     */
    public static function explode_datetime($date) {
        $result = array();
        $dates = explode('～', $date);
        foreach ($dates as $d) {
            $dt = DateTime::createFromFormat("Y/m/d", $d);
            $str = $dt->format('Y/m/d');
            $result[] = $str;
        }
        if (count($result) == 1) {
            $result[] = $result[0];
        }
        return $result;
    }

    /**
     * 日付をフォーマットする
     * @param $date
     * @return string
     */
    public static function format_datetime($date) {
        $result = '';
        $dates = explode('～', $date);
        foreach ($dates as $d) {
            $dt = DateTime::createFromFormat("Y/m/d", $d);
            $str = $dt->format('Y/m/d');
            $result = $result.$str.'～';
        }
        $result = rtrim($result, '～');
        return $result;
    }

    /**
     * utf-8 かどうかを検証する
     * @param $input
     * @return bool
     */
    public static function validate_utf8($input) {
        return (bool)preg_match('//u', $input);
    }

    /**
     * 時間かどうかを検証する
     * @param $time
     * @return bool
     */
    public static function is_time($time) {
        $dt = DateTime::createFromFormat("H:i", $time);
        return $dt !== false && !array_sum($dt->getLastErrors());
    }

    /**
     * null か空文字か検証する
     * @param $str
     * @return bool
     */
    public static function is_null_or_empty($str) {
        if (strlen($str) == 0){
            return true;
        }
        return false;
    }

    /**
     * カタカナをひらがなにする
     * @param $katakana
     * @return string
     */
    public static function convert_katakana_to_hiragana($katakana)
    {
        $table = array(
            'ア' => 'あ',
            'イ' => 'い',
            'ウ' => 'う',
            'エ' => 'え',
            'オ' => 'お',
            'カ' => 'か',
            'キ' => 'き',
            'ク' => 'く',
            'ケ' => 'け',
            'コ' => 'こ',
            'サ' => 'さ',
            'シ' => 'し',
            'ス' => 'す',
            'セ' => 'せ',
            'ソ' => 'そ',
            'タ' => 'た',
            'チ' => 'ち',
            'ツ' => 'つ',
            'テ' => 'て',
            'ト' => 'と',
            'ナ' => 'な',
            'ニ' => 'に',
            'ヌ' => 'ぬ',
            'ネ' => 'ね',
            'ノ' => 'の',
            'ハ' => 'は',
            'ヒ' => 'ひ',
            'フ' => 'ふ',
            'ヘ' => 'へ',
            'ホ' => 'ほ',
            'マ' => 'ま',
            'ミ' => 'み',
            'ム' => 'む',
            'メ' => 'め',
            'モ' => 'も',
            'ヤ' => 'や',
            'ユ' => 'ゆ',
            'ヨ' => 'よ',
            'ラ' => 'ら',
            'リ' => 'り',
            'ル' => 'る',
            'レ' => 'れ',
            'ロ' => 'ろ',
            'ワ' => 'わ',
            'ヲ' => 'を',
            'ン' => 'ん',
            'ガ' => 'が',
            'ギ' => 'ぎ',
            'グ' => 'ぐ',
            'ゲ' => 'げ',
            'ゴ' => 'ご',
            'ザ' => 'ざ',
            'ジ' => 'じ',
            'ズ' => 'ず',
            'ゼ' => 'ぜ',
            'ゾ' => 'ぞ',
            'ダ' => 'だ',
            'ヂ' => 'ぢ',
            'ヅ' => 'づ',
            'デ' => 'で',
            'ド' => 'ど',
            'バ' => 'ば',
            'ビ' => 'び',
            'ブ' => 'ぶ',
            'ベ' => 'べ',
            'ボ' => 'ぼ',
            'パ' => 'ぱ',
            'ピ' => 'ぴ',
            'プ' => 'ぷ',
            'ペ' => 'ぺ',
            'ポ' => 'ぽ',
            'ッ' => 'っ',
            'ァ' => 'ぁ',
            'ィ' => 'ぃ',
            'ゥ' => 'ぅ',
            'ェ' => 'ぇ',
            'ォ' => 'ぉ',
            'ャ' => 'ゃ',
            'ュ' => 'ゅ',
            'ョ' => 'ょ',
            'ヴ' => 'ゔ',
            'ー' => 'ー',
        );

        $result = '';
        $str_array = preg_split("//u", $katakana, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($str_array as $char) {
            if (array_key_exists($char, $table)) {
                $result = $result . $table[$char];
            }
        }
        return $result;
    }
}