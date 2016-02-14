<?php

class MainController extends Controller {

    private $model;
    private $title = 'Stream';

    public function __construct() {
        parent::__construct();
        $this->model = new Stream();
    }

    public function home() {
        $data = $this->model->getList();
        echo $this->templates->render('home', [
            'title' => $this->title.'__RECENT__',
            'data' => $data
        ]);
    }

    public function edit($id = null) {
        $item = $this->model->getById($id);
        echo $this->templates->render('edit', [
            'title' => $this->title.'__EDIT__',
            'item' => $item
        ]);
    }

    public function displayForm() {
        echo $this->templates->render('new', [
            'title' => $this->title.'__NEW__'
        ]);
    }

    public function save($id = NULL, $data = [], $ajax = FALSE) {
        $this->model->save($id, $data);
        if(!$ajax) {
            $this->redirect('/');
        } else {
            echo json_encode($this->model->getById($id));
        }
    }

    public function view($id = null) {
        $item = $this->model->getById($id);
        echo $this->templates->render('item', [
            'title' => $this->title.'__VIEW__',
            'item' => $item
        ]);
    }
}
