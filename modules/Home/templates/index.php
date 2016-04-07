<?php $this->layout('basic');?>

<uib-alert ng-repeat="err in sys.errors"
    close="sys.dismiss($index)"
    type="{{err.type}}">{{err.msg}}</uib-alert>

<div class="view-animate-container">

    <ng-view class="view-animate"></ng-view>

</div>
