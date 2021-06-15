<?php

namespace Bitrock\Controllers;
use Bitrock\Models\Vendor\JSONResponse;
use Bitrock\Models\Vendor\ViewResponse;
use Bitrock\Models\Vendor\Response;

abstract class Controller
{
    /**
     * @param string $viewName - название view
     * @param array $params - массив значений, которые будут преобразованы в переменные
     * @param int $httpStatus
     */
    public function render(
        $viewName,
        $params = [],
        $httpStatus = Response::SUCCESS_RESPONSE_CODE
    ) {
        if (empty($viewName)) return false;

        $viewResponse = new ViewResponse();
        $viewResponse->setHTTPStatus($httpStatus);
        $viewResponse->setViewName($viewName)
            ->setParams($params)
            ->send();

        return true;
    }

    public function json(
        $data = [],
        $message = '',
        $status = true,
        $httpStatus = Response::SUCCESS_RESPONSE_CODE
    ) {
        $jsonResponse = new JSONResponse();
        $jsonResponse->setHTTPStatus($httpStatus);
        $jsonResponse->setData($data);
        $jsonResponse->setMessage($message);
        $jsonResponse->setStatus($status);
        $this->refreshJSONResponse();
    }
}