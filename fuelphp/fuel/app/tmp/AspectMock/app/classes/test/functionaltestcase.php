<?php
/**
 * Created by PhpStorm.
 * User: tsuzukitomoaki
 * Date: 15/05/27
 * Time: 18:00
 */

use Goutte\Client;

abstract class Test_Functionaltestcase extends TestCase
{
    // vagrant
    const BASE_URL = 'http://192.168.33.10/';

    protected static $client;
    protected static $crawler;
    protected static $post;

    public static function setUpBeforeClass()
    { if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array(), true)) !== __AM_CONTINUE__) return $__am_res; 
        // .htaccess をテスト環境用に変更
        $htaccess = DOCROOT . 'public/.htaccess';
        $htaccess_develop = DOCROOT . 'public/.htaccess_develop';
        $htaccess_test = DOCROOT . 'public/.htaccess_test';

        if (!file_exists($htaccess_develop)
            || filemtime($htaccess) > filemtime($htaccess_develop))
        {
            $ret = rename($htaccess, $htaccess_develop);
            if ($ret === false)
            {
                exit('Error: cant backup .htaccess');
            }
        }

        if (!file_exists(($htaccess_test)))
        {
            exit ('Error: .htaccess_test does not exist');
        }

        $ret = copy($htaccess_test, $htaccess);
        if ($ret === false)
        {
            exit('Error cant copy .htaccess_test');
        }

        // Goutte の Client オブジェクトを生成
        static::$client = new Client();
    }

    public static function tearDownAfterClass()
    { if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array(), true)) !== __AM_CONTINUE__) return $__am_res; 
        static::$client = null;
        static::$crawler = null;
        static::$post = null;

        // .htaccess を開発環境用に戻す
        $htaccess = DOCROOT . 'public/.htaccess';
        copy ($htaccess . '_develop', $htaccess);
        touch ($htaccess, filemtime($htaccess . '_develop'));
    }

    // 絶対 url を返す
    public static function open($url)
    { if (($__am_res = __amock_before(get_called_class(), __CLASS__, __FUNCTION__, array($url), true)) !== __AM_CONTINUE__) return $__am_res; 
        return static::BASE_URL . $url;
    }


}