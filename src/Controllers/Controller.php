<?php

namespace Bitrock\Controllers;
use Bitrock\Models\Vendor\JSONResponse;
use Bitrock\Models\Vendor\ViewResponse;
use Bitrock\Models\Vendor\Response;
use Symfony\Component\HttpFoundation\Request;

abstract class Controller
{
    public CONST EMPTY_DATA_MESSAGE = 'Массив с данными пуст';
    protected $request;
    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

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
        $jsonResponse->send();
    }

    protected function checkEmptyData(array $data = [], string $emptyMessage = ''): bool
    {
        $dataIsEmpty = true;
        foreach ($data as $field) {
            if (!empty($field)) {
                $dataIsEmpty = false;
            }
        }

        if (!$dataIsEmpty) {
            return false;
        } else {
            if (empty($emptyMessage)) $emptyMessage = static::EMPTY_DATA_MESSAGE;

            $this->getResponse()->setData(['text' => $emptyMessage]);
            return true;
        }
    }
}