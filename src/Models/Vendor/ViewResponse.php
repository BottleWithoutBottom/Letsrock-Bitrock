<?php
namespace Bitrock\Models\Vendor;

use Bitrock\View\View;

class ViewResponse extends Response
{
    private $view;
    protected $viewName;
    protected $params = [];

    public function __construct()
    {
        $this->view = View::getInstance();
    }

    public function send()
    {
        parent::send();
        $this->render($this->viewName, $this->params);
    }

    public function render($viewName, $params = [])
    {
        if (empty($viewName)) return false;

        echo $this->view->render($viewName, $params);
        die();
    }

    public function setViewName($viewName)
    {
        $this->viewName = (string)$viewName;
        return $this;
    }

    public function setParams($params = [])
    {
        $this->params = $params;
        return $this;
    }
}