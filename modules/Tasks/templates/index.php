<?php $this->layout('basic'); ?>

<form class="form-inline jumbotron" action="/tasks/save" method="POST">
    <div class="form-group">
        <input name="title" size="40" class="form-control" type="text" placeholder="Task title"/>
        <button type="submit" class="btn btn-primary">Save</button>
    </div>
</form>

<?php $this->insert('task::list-block', ['data'=>$data]); ?>
