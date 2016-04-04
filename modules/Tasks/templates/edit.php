<?php $this->layout('basic'); ?>

<form action="/tasks/save" method="POST">
    <input type="hidden" name="id" value="<?php echo $this->e($data->id); ?>"/>
    <div class="form-group">
        <label>Title</label>
        <input name="title" value="<?php echo $this->e($data->title); ?>" class="form-control"/>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="5"><?php echo $this->e($data->description); ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
</form>

<?php $this->insert('task::list-block', ['data'=>$list]); ?>
