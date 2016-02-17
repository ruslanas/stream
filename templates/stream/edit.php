<?php $this->layout('basic'); ?>

<form action="/edit/<?php echo $this->e($item['id']);?>" method="POST">
    <div class="form-group">
        <label>Title</label>
        <input name="title" class="form-control" type="text" value="<?php echo $this->e($item['title']); ?>" />
    </div>
    <div class="form-group">
        <label>Body</label>
        <textarea rows="7" name="body" class="form-control"><?php echo $this->e($item['body']); ?></textarea>
    </div>
    <button class="btn btn-success">Save</button>
</form>
