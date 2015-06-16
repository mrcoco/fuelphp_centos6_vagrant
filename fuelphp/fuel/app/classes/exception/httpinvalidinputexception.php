<?php
/**
 * Created by PhpStorm.
 * User: tsuzukitomoaki
 * Date: 15/05/27
 * Time: 2:38
 */

class Exception_HttpInvalidInputException extends HttpException
{
    public function response()
    {
        $response = Request::forge('error/invalid')->execute(array($this->getMessage()))->response();
        return $response;
    }
}