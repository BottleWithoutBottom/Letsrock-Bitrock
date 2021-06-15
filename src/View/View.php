<?php

namespace Bitrock\View;
use Bitrock\LetsCore;
use Bitrock\Models\Singleton;

class View extends Singleton
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
        require(LetsCore::getEnv(LetsCore::VIEWS_DIR) . $viewName);
        return ob_get_clean();
    }

    public static function preHook()
    {
        return !empty(LetsCore::getEnv(LetsCore::VIEWS_DIR)) && is_dir(LetsCore::getEnv(LetsCore::VIEWS_DIR));
    }
}