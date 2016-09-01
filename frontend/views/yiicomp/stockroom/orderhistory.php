<?php
use common\components\DigitalPurchaser ;
?>
<div class=""row">
<div class="col-sm-12 col-centered">
    <h3>Order History</h3>
    <table class="pdetails table table-bordered table-condensed table-hover small">

        <tbody>
        <tr class="success">
            <th colspan="2" class="text-center text-success"><?= $product->partcode . ' : ' . $product->description ?></th>
        </tr>

        <tr class="active">
            <th>Stock Id</th>
            <th class="text-right">Key</th>
        </tr>

        <?php
        $total = 0 ;


        foreach ($items as $item) {
            $total++ ;
            ?>
            <tr>
                <td><?= $item->id ?></td>
                <td>
                    <?= DigitalPurchaser::getProductInstallKey($item); ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot>
        <tr class="warning">
            <th>Total</th><th class="text-right"><?= $total ?></th>
        </tr>
        </tfoot>
    </table>
</div>
</div>
<div style="clear:both"></div>


