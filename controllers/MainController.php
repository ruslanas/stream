<?php

/**
 * @author Ruslanas Balciunas <ruslanas.com@gmail.com>
 */
class MainController extends Controller {

    private $model;

    public function __construct() {
        parent::__construct();
        $this->templates->addFolder('stream', 'templates/stream');
        $this->app->connect();
        $this->model = new Stream($this->app->pdo);
    }

    public function home() {
        $data = $this->model->getList();
        echo $this->templates->render('stream::home', [
            'data' => $data
        ]);
    }

    public function edit($id = null) {
        $item = $this->model->getById($id);
        echo $this->templates->render('stream::edit', [
            'item' => $item
        ]);
    }

    public function displayForm() {
        echo $this->templates->render('stream::new', [
        ]);
    }

    public function save($id = NULL, $data = []) {
        $this->model->save($id, $data);
        $this->redirect('/');
    }

    public function view($id = null) {
        $item = $this->model->getById($id);
        echo $this->templates->render('stream::item', [
            'item' => $item
        ]);
    }
}
