<?php $this->layout('basic', ['title' => $title]); ?>
<h1><?= $title ?></h1>
<form action="/tasks/add" method="POST">
    <div class="form-group">
        <label>Title</label>
        <input name="title" class="form-control" type="text" value="" />
    </div>
    <div class="form-group">
        <label>Body</label>
        <textarea rows="7" name="body" class="form-control"></textarea>
    </div>
    <button class="btn btn-success">Save</button>
</form>
