
<div id="det-<?= $salesRepOrder->id ?>" class="row row-centered">

    <h3><?= $salesRepOrder->po ?><small></small></h3>

    <div class="col-sm-10 col-centered">
            <table class="pdetails table table-bordered table-condensed table-hover small">
                <tbody>
                <tr class="success">
                    <th colspan="3" class="text-center text-success">The order details</th>
                </tr>
                <tr class="active">
                    <th></th>
                    <th class="hidden-xs hidden-sm">Product Code</th>
                    <th>Description</th>
                </tr>

                <?php
                $total = 0 ;


                foreach ($orderdItems as $item) {
                    $digitalProduct = $item->stockitem->digitalProduct ;
                    $total++ ;
?>
                    <tr>
                        <td class="text-center"><?= $digitalProduct->getMainImageThumbnailTag() ?></td>
                        <td class="hidden-xs hidden-sm"><?= $digitalProduct->partcode ?></td>
                        <td><?= $digitalProduct->description ?></td>
                    </tr>

                <?php } ?>
                <tr class="warning">
                    <th></th><th>Total Items</th><th class="text-right"><?= $total ?></th>
                </tr>
                </tbody>
            </table>
    </div>
    <div style="clear:both"></div>
</div>

