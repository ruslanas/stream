<?php $this->layout('basic'); ?>

<form method="POST" action="/user/login">
    <div class="form-group">
        <label>Email:</label>
        <input name="email" type="email" value="<?= $this->e($data['email']);?>" class="form-control"/>
    </div>
    <div class="form-group">
        <label>Password:</label>
        <input name="password" type="password" class="form-control"/>
    </div>
    <button type="submit" class="btn btn-default">Sign In</button>
</form>
