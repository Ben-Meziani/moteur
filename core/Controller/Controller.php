<?php

namespace Core\Controller;


class Controller
{

    protected $viewPath;
    protected $template;

    protected function render($view, $variables = [])
    {
        ob_start();
        extract($variables);
        require($view = $this->viewPath . str_replace('.', '/', $view) . '.php');
        $content = ob_get_clean();
        if(isset($variables['debrief']) && $variables['debrief'] === true) {
            require($this->viewPath . 'templates/' . $this->debriefTemplate . '.php');
        } else {
        require($this->viewPath . 'templates/' . $this->template . '.php');
        }
    }

}
