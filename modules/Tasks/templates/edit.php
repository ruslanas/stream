<?php $this->layout('basic'); ?>

<form action="/tasks/save" method="POST">
    <div class="form-group">
        <label>Title</label>
        <input name="title" class="form-control"/>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="5"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
</form>