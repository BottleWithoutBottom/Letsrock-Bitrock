<?php
namespace Bitrock\Models\Vendor;

abstract class Response
{
    public CONST SUCCESS_RESPONSE_CODE = 200;
    public CONST NOT_FOUND_RESPONSE_CODE = 404;
    public CONST FORBIDDEN_RESPONSE_CODE = 403;

    protected $httpStatus;

    public function send()
    {
        $this->sendHTTPstatus();
    }

    public function sendHTTPstatus()
    {
        if (empty($this->httpStatus)) $this->httpStatus = static::SUCCESS_RESPONSE_CODE;

        http_response_code($this->httpStatus);
    }

    public function setHTTPStatus($status)
    {
        if (empty($status)) return false;

        $this->httpStatus = $status;
        return true;
    }

    public function getHTTPStatus()
    {
        return $this->httpStatus;
    }
}