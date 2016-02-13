<?php $this->layout('basic', ['title' => $title]);?>

<?php /* ?>

<?php foreach($data as $post): ?>
    <h2>
        [<?= $this->e($post['rowid']) ?>]
        <a href="/posts/<?= $this->e($post['rowid']) ?>"><?php echo $this->e($post['title']);?></a>
    </h2>
    <div><?php echo $this->e($post['body']); ?></div>
    <div class="row">
        <div class="col-xs-6">
            <a href="/edit/<?= $this->e($post['rowid']) ?>" class="btn btn-success btn-xs">Edit</a>
        </div>
        <div class="col-xs-6">
        </div>
    </div>
<?php endforeach; ?>

<?php */ ?>
