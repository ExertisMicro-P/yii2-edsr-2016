<?php

use kartik\tabs\TabsX;
use common\components\DigitalPurchaser;
use yii\helpers\Html;

//echo '<pre>';
//print_r($model);return ;
?>

<?= Html::csrfMetaTags(); ?>

<div id="det-<?= $model->id ?>" class="row row-centered">

    <h3>Key Delivery Ref: <?= substr($model->order_number, 1) ?>
        <small></small>
    </h3>


    <div class="col-sm-10 col-centered">
        <table class="pdetails table table-bordered table-condensed table-hover small">
            <tbody>
            <tr class="success">
                <th colspan="4" class="text-center text-success">
                    <div class="row">
                        <div class="col-xs-12">
                            Created at <?= $model->created_at ?><br class="visible-xs-inline-block"/> and emailed
                            to <?= $model->email ?>
                        </div>
                    </div>
                </th>
                <th class="hidden-xs hidden-sm"></th>
            </tr>
            <tr class="active">
                <th></th>
                <th class="text-right">Product Code</th>
                <th class="hidden-xs hidden-sm">Description</th>
                <th class="hidden-xs hidden-sm">Purchase Order</th>
                <th></th>
            </tr>

            <?php
            $total = 0;


            foreach ($model->emailedItems as $item) {
                $total++;
                $digitalProduct = $item->stockItem->digitalProduct;

                $urlParams = [
                    'euser'     => $model->id,
                    'eitem'     => $item->id,
                    'onumber'   => substr($model->order_number, 1),
                    'stockroom' => $item->stockItem->stockroom_id,
                    'itemId'    => $item->stock_item_id
                ];

                ?>
                <tr>
                    <td class="text-center"><?= $digitalProduct->getMainImageThumbnailTag() ?></td>
                    <td class="hidden-xs hidden-sm">
                        <?= $digitalProduct->partcode ?></td>
                    <td><?= $digitalProduct->description ?>
                        <div class="key text-center">
                            Key:&nbsp;<?= DigitalPurchaser::getProductInstallKey($item->stockItem); ?>
                        </div>
                    </td>

                    <td><?= $item->stockItem->orderdetails->rawpo ?></td>

                    <td class="text-center">
                        <div class="btn-group-horizontal btn-group-SM" role="group"
                             aria-label="krajee-book-detail-buttons">
                            <button type="button" class="keyact btn btn-default"
                                    rel="<?= Yii::$app->urlManager->createUrl(array_merge(['yiicomp/stockroom/viewkeys'], $urlParams)) ?>"
                                    data-name=""
                                    data-email=""
                                    title="Resend the key"
                                    onclick="resendEmails(event) "
                                    data-toggle="tooltip" data-original-title="View the keys">
                                <span class="glyphicon glyphicon-envelope""></span>
                            </button>


                            <a href="<?= Yii::$app->urlManager->createUrl(array_merge(['printkeys/reprint'], ['pdfkeys' => $item->stock_item_id])) ?>"
                               target="_blank"
                               class="keyact btn btn-default"
                               title="Re-print the key"
                               data-pjax="0"
                               data-toggle="tooltip" data-original-title="Re-print the key">
                                <span class="glyphicon glyphicon-print""></span>
                            </a>

                        </div>
                    </td>
                </tr>
            <?php } ?>
            <tr class="warning" data-bind="slideVisible: xxx==1">
                <th class="hidden-xs hidden-sm"></th>
                <th class="text-right" colspan="3">Total Keys</th>
                <th class="text-center"><?= $total ?></th>
            </tr>
            </tbody>
        </table>
    </div>

    <div style="clear:both"></div>
</div>

<script type="text/html" id="resendEmails">
    <div class="row row-centered" style="xdisplay:none"
         data-bind="slideVisible : showForm, deleteOnClose: 'tr:eq(0)', css: {'kv-grid-loading': resending}">
        <div class="col-xs-12 col-centered" id="emailKeys">

            <button type="button" class="close"
                    data-bind="click: closeForm"
                    xdata-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
            </button>

            <h3>You are about to re-send the key for your selected product</h3>
        </div>

        <form class="form-inline row-centered" role="form" action="#" data-bind="submit: emailToRecipient">
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>"/>

            <div class="col-xs-12">Message Body</div>
            <div class="col-xs-2"></div>
            <div class="col-xs-8 center-block text-center">
                <textarea class="form-control" cols="80" rows="4" name="message" style="width: 100%"
                          data-bind="value: message"></textarea>
            </div>
            <div class="col-xs-2"></div>

            <div class="col-xs-12">
                <hr/>
            </div>
            <div class="col-xs-6">
                <div class="form-group required">
                    <label for="rname">Recipient's Name:</label>
                    <input type="text" class="form-control" data-bind="value: recipient, css: {err: badrname}"
                           name="rname" id="rname"/>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group required">
                    <label for="remail">Email address:</label>
                    <input type="email" class="form-control"
                           data-bind="value: email, css: {err: !emailOk()}, valueUpdate: 'afterkeydown'" name="email"
                           id="remail"/>
                </div>
                <div class="indic" data-bind="css: {ok: emailOk, bad : !emailOk()}"></div>
            </div>

            <div class="col-xs-12 text-center alert">
                <button type="submit" class="btn btn-default"
                        data-bind="attr:{disabled: invalid}, visible: !errormsg(),"><span
                        class="glyphicon glyphicon-envelope"></span> Send
                </button>
                <div class="alert alert-info fade in"
                     data-bind="text: errormsg, visible: errormsg, css: msgClass"
                ">
            </div>
    </div>

    </form>

    <div class="col-xs-12 row-centered form-group required">
        <br/>Fields marked <label></label> are required
    </div>
    <div class="col-xs-12 row-centered note">
        <!--                NOTE: The keys will be logged in a 'cupboard' identified by the email address.<br />
                                If this address has already been used, they will be added to an exising cupboard -->
    </div>
    </div>
</script>

<script type="application/javascript">
    function resendEmails(event) {
        var tkn     = $('meta[name="csrf-token"]').attr("content");
        var element = $(event.target);
        if (element[0].tagName != 'BUTTON') {
            element = element.parents('button:eq(0)');
        }

        // -------------------------------------------------------------------
        // Jquery uniqueId not working....
        // -------------------------------------------------------------------
        var id = element.attr('id');
        if (!id) {
            id = new Date().getTime();
            element.attr('id', id);
        }

        ko.postbox.publish('resend.emails', id);
    }
</script>
