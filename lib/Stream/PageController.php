<?php

/**
 * @author Ruslanas Balčiūnas <ruslanas.com@gmail.com>
 */

namespace Stream;

class PageController extends Controller {

    protected $templates;

    public function __construct() {
        parent::__construct();
        $this->setupTemplate();
    }

    public function setupTemplate()
    {
        $this->templates = new \League\Plates\Engine($this->app->template_path);
        $this->templates->addData([
            'authorized' => !empty($_SESSION['uid']),
            'title' => $this->app->title
        ]);
    }
}