<?php
/**
 * Created by PhpStorm.
 * User: tsuzukitomoaki
 * Date: 15/05/27
 * Time: 17:33
 */

class Controller_Public extends Controller_Template
{
    public function before()
    {
        parent::before();
        $this->response = Response::forge();
        $this->response->set_header('X-FRAME-OPTIONS', 'SAMEORIGIN');
    }

    public function after($response)
    {
        $response = $this->response;
        $response->body = $this->template;
        return parent::after($response);
    }
}