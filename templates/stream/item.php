<?php $this->layout('basic', ['title' => $title]); ?>
<h2><?php echo $this->e($item['title']); ?></h2>
<?php echo $this->e($item['body']); ?>
<div>
    <a href="/edit/<?php echo $item['rowid'];?>" class="btn btn-success btn-xs">Edit</a>
</div>
