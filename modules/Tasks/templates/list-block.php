<?php foreach($data as $item): ?>
<div class="panel panel-info">
    <div class="panel-heading">
        <a href="/tasks/edit/<?php echo $this->e($item->id);?>"><?php echo $this->e($item->title); ?></a>
    </div>
    <div class="panel-body"><?php echo $this->e($item->description); ?></div>
</div>
<?php endforeach; ?>
