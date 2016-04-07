<?php $this->layout('basic');?>

<div class="alert alert-info" ng-show="error ? true : false">{{error}}</div>

<div class="view-animate-container">

    <ng-view class="view-animate"></ng-view>

</div>
