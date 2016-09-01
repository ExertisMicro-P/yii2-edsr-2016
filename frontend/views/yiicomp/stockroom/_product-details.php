<?php

use kartik\tabs\TabsX;

?>

<div id="det-<?= $model->id ?>" class="row">

    <h3><?= $model->description ?><small></small></h3>
    <div>


        <div class="col-sm-4 photo">
            <div class="img-thumbnail img-rounded text-center">
                <?= $model->digitalProduct->getMainImageTag() ?>
            </div>
        </div>


<?php
        $statusLookup = [
            \common\models\StockItem::STATUS_PURCHASED  => 'Available',
            \common\models\StockItem::STATUS_NOT_PURCHASED  => 'Pending'
        ] ;

        $itemsCounts = $model->totalOfThisProductByStatus(array_keys($statusLookup)) ;

?>

        <div class="col-sm-6">
            <table class="pdetails table table-bordered table-condensed table-hover small">
                <tbody><tr class="success">
                    <th colspan="3" class="text-center text-success">Your product counts and status</th>
                </tr>
                <tr class="active">
                    <th>Status</th>
                    <th class="text-right">Count</th>
                    <th>Action</th>
                </tr>

<?php
    $total = 0 ;


    foreach ($itemsCounts as $item) {
        $total += $item->num ;

        $urlParams = [
            'pid'       => $model->digitalProduct->id,
            'status'    => $model->status,
            'stockroom' => $model->stockroom_id
        ] ;
?>
                <tr>
                    <td><?= $statusLookup[$item['status']] ?></td>
                    <td class="text-right"><?= $item->num ?></td>
                    <td>
                        <div class="btn-group-horizontal btn-group-SM" role="group" aria-label="krajee-book-detail-buttons">
                            <button type="button" class="keyact btn btn-default"
                                    rel="<?= Yii::$app->urlManager->createUrl(array_merge(['yiicomp/stockroom/viewkeys'],$urlParams))?>"
                                    title="" data-toggle="tooltip" data-original-title="View the keys">
                                <span class="glyphicon glyphicon-zoom-in""></span></button>
<!--
                            <button type="button" class="keyact btn btn-default" title=""
                                    rel="<?= Yii::$app->urlManager->createUrl(['stockroom/deliverKeys','id' => $model->digitalProduct->id])?>"
                                    data-toggle="tooltip" data-original-title="Deliver to a cupboard">
                                <span class="glyphicon glyphicon-download-alt"></span></button>

                            <button type="button" class="keyact btn btn-default"
                                    rel="<?= Yii::$app->urlManager->createUrl(['yiicomp/stockroom/emailKeys','id' => $model->digitalProduct->id])?>"
                                    title="" data-toggle="tooltip" data-original-title="Email the keys"><span class="glyphicon glyphicon-envelope"></span></button>
-->
                            <button type="button" class="keyact btn btn-default"
                                    rel="<?= Yii::$app->urlManager->createUrl(array_merge(['yiicomp/stockroom/revieworders'],$urlParams))?>"
                                    title="" data-toggle="tooltip" data-original-title="Review previous orders"><span class="glyphicon glyphicon-tasks"></span></button>

                        </div>
                    </td>
                </tr>
<?php } ?>
                <tr class="warning">
                    <th>Total</th><th class="text-right"><?= $total ?></th><th></th>
                </tr>
                </tbody></table>
        </div>
    </div>
    <div style="clear:both"></div>
</div>
