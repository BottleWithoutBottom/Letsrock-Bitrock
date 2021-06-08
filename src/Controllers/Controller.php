<?php

namespace Bitrock\Controllers;
use Bitrock\View\View;
use Bitrock\Models\Vendor\JSONModel;
use Bitrock\Models\Vendor\Response;

abstract class Controller
{
    protected $view;
    protected $response;

    public function __construct()
    {
        $this->view = new View();
    }

    /**
     * @param string $viewName - название view
     * @param array $params - массив значений, которые будут преобразованы в переменные
     */
    public function render($viewName, $params = [])
    {
        return $this->view->render($viewName, $params);
    }

    public function refreshResponse()
    {
        $this->response = new JSONModel();
        return true;
    }

    public function getResponse(): Response
    {
        if (empty($this->response)) $this->refreshResponse();
        return $this->response;
    }

    public function sendResponse()
    {
        http_response_code(200);
        die($this->getResponse()->getAsString());
    }
}