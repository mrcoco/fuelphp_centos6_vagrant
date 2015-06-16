<?php

// library への依存。TODO 綺麗に load できるようにしたい。

$path = DOCROOT.'/fuel/app/classes/lib/';
set_include_path(get_include_path().PATH_SEPARATOR.$path);
require_once("HTTP/Request2.php");

class Util_Http {

    public function post($url, $header, $form, $cookies = null) {
        return $this->communicate_without_proxy(HTTP_Request2::METHOD_POST, $url, $header, $form, $cookies);
    }

    public function get($url, $header, $cookies = null) {
        $form = array();
        return $this->communicate_without_proxy(HTTP_Request2::METHOD_GET, $url, $header, $form, $cookies);
    }

    public function get_cookie($url, $header) {
        $form = array();
        return $this->communicate_to_get_cookie(HTTP_Request2::METHOD_GET, $url, $header, $form);
    }

    private function communicate_without_proxy($method_type, $url, $header, $param, $cookies) {
        $result = $this->communicate($method_type, $url, $header, $param, $cookies);

        if ($this->is_error($result)) {
            return $result['error'];
        }

        $response = $result['response'];

        $decoded_body = $response->getBody();
        if ($response->getHeader('content-encoding') == 'gzip') {
            $decoded_body = $response->decodeGzip($response->getBody());
        }
        if ($this->is_ms932($response->getHeader('content-type'))) {
            $decoded_body = mb_convert_encoding($decoded_body, 'utf8', 'sjis-win');
        }

        return $decoded_body;
    }

    private function communicate_to_get_cookie($method_type, $url, $header, $param) {
        $result = $this->communicate($method_type, $url, $header, $param, null);
        if ($this->is_error($result)) {
            return $result['error'];
        }
        return $result['response']->getCookies();
    }

    private function communicate($method_type, $url, $header, $param, $cookies = null) {

        $result = array();

        $error_prefix = '';
        if ($method_type == HTTP_Request2::METHOD_POST) {
            $error_prefix = 'POST ERROR: ';
        } else if ($method_type == HTTP_Request2::METHOD_GET) {
            $error_prefix = 'GET ERROR: ';
        }

        try{
            $request = new HTTP_Request2();
            @$request->setUrl($url);
            $request->setMethod($method_type);

            $request->setHeader($header);
            foreach ($param as $key => $value) {
                $request->addPostParameter($key, $value);
            }

            if ($cookies != null) {
                foreach ($cookies as $c) {
                    $request->addCookie($c["name"], $c["value"]);
                }
            }

            $response = $request->send();
            $result['response'] = $response;
            return $result;

            // HTTP_Request2のエラーを表示
        } catch( HTTP_Request2_Exception $e ){
            $result['error'] = $error_prefix . $e->getMessage();
            return $result;
            // それ以外のエラーを表示
        } catch (Exception $e){
            $result['error'] = $error_prefix . $e->getMessage();
            return $result;
        }
    }

    private function is_ms932($content_type) {
        if (strpos($content_type, 'charset=MS932') !== false) {
            return true;
        }
        return false;
    }

    private function is_error($result) {
        if (array_key_exists('error', $result)) {
            return true;
        }
        return false;
    }

}
