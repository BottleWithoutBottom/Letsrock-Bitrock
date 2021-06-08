<?php

namespace Bitrock\View;
use Bitrock\LetsCore;

class View
{
    /**
     * @param string $viewName - название view
     * @param array $params - массив значений, которые будут преобразованы в переменные
     */
    public function render(string $viewName, $params = []): string
    {
        if (empty($viewName)) return false;
        if (!empty($params)) extract($params);
        ob_start();
        require(LetsCore::getEnv(LetsCore::VIEWS_PATH) . $viewName);
        return ob_get_clean();
    }
}