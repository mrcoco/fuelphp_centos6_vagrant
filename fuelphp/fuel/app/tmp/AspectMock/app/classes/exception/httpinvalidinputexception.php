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
    { if (($__am_res = __amock_before($this, __CLASS__, __FUNCTION__, array(), false)) !== __AM_CONTINUE__) return $__am_res; 
        $response = Request::forge('error/invalid')->execute(array($this->getMessage()))->response();
        return $response;
    }
}