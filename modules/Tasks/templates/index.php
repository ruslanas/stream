<?php $this->layout('basic'); ?>

<form class="form-inline" action="/tasks/save" method="POST">
    <div class="form-group">
        <input name="title" class="form-control" type="text" placeholder="Task title"/>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>
