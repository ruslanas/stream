<?php $this->layout('basic');?>

<uib-alert close="error = ''" type="danger" ng-show="error ? true : false">{{error}}</uib-alert>
<uib-alert close="message = ''" type="info" ng-show="message ? true : false">{{message}}</uib-alert>

<!-- <div class="alert alert-danger" ng-show="error ? true : false">{{error}}</div>
<div class="alert alert-info" ng-show="message ? true : false">{{message}}</div> -->

<div class="view-animate-container">

    <ng-view class="view-animate"></ng-view>

</div>
