<?php $this->layout('basic'); ?>
<form method="POST" action="/user/add">
    <div class="form-group">
        <label>Email:</label>
        <input name="email" type="email" class="form-control"/>
    </div>
    <div class="form-group">
        <label>Password:</label>
        <input name="password" type="password" class="form-control"/>
    </div>
    <div class="form-group">
        <label>Repeat password:</label>
        <input name="password2" type="password" class="form-control"/>
    </div>
    <button type="submit" class="btn btn-success">Register</button>
</form>
