<?php
    use common\components\DigitalPurchaser ;
?>
<div class=""row">
    <div class="col-sm-12 col-centered">
        <table class="pdetails table table-bordered table-condensed table-hover small">

            <tbody>
                <tr class="success">
                    <th colspan="6" class="text-center text-success"><?= $product->partcode . ' : ' . $product->description ?></th>
                </tr>

                <tr class="active">
                    <th>Stock Id</th>
                    <th>Your Ref</th>
                    <th>Our Ref</th>
                    <th>For (To)</th>
                    <th>Order Date</th>
                    <th>Key</th>
                </tr>

            <?php
            $total = 0 ;


            foreach ($items as $item) {
                $total++ ;
    ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td><?= $item->orderdetails->po ?></td>
                    <td><?= $item->orderdetails->sop.'-'.$item->id.'-'.$item->eztorm_order_id ?></td>
                    <td><?= $item->orderdetails->name.' / '.$item->orderdetails->postcode ?></td>
                    <td><?= $item->timestamp_added ?></td>
                    <td style="white-space: nowrap">
                        <?= DigitalPurchaser::getProductInstallKey($item); ?>
                    </td>
                </tr>
    <?php } ?>
            </tbody>
            <tfoot>
                <tr class="warning">
                    <th class="text-right" colspan="5">Total</th><th class="text-right"><?= $total ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div style="clear:both"></div>


