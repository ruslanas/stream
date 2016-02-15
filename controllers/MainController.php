<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */
class MainController extends Controller {

    private $model;

    public function __construct() {
        parent::__construct();
        $this->templates->addFolder('stream', 'templates/stream');
        $this->model = new Stream();
    }

    public function home() {
        $data = $this->model->getList();
        echo $this->templates->render('stream::home', [
            'title' => $this->app->title.'__RECENT__',
            'data' => $data
        ]);
    }

    public function edit($id = null) {
        $item = $this->model->getById($id);
        echo $this->templates->render('stream::edit', [
            'title' => $this->app->title.'__EDIT__',
            'item' => $item
        ]);
    }

    public function displayForm() {
        echo $this->templates->render('stream::new', [
            'title' => $this->app->title.'__NEW__'
        ]);
    }

    public function save($id = NULL, $data = []) {
        $this->model->save($id, $data);
        $this->redirect('/');
    }

    public function view($id = null) {
        $item = $this->model->getById($id);
        echo $this->templates->render('stream::item', [
            'title' => $this->app->title.'__VIEW__',
            'item' => $item
        ]);
    }
}
