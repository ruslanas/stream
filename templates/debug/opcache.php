<?php $this->layout('basic', ['title' => $title]); ?>
<?php var_dump($data['opcache_statistics']);?>
<table class="table table-condensed table-bordered table-striped">
    <tbody>
        <?php foreach($data['scripts'] as $script => $d): ?>
            <tr>
                <td><?= $this->e(basename($d['full_path'])); ?></td>
                <td><?= $this->e($d['hits']); ?></td>
                <td><?= $this->e($d['memory_consumption']); ?></td>
                <td><?= $this->e($d['last_used']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
var_dump($data);
