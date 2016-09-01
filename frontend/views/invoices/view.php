<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
?>

<div class="invoices container">
    
    <?=Html::a('<< Back to invoices', 'index')?>
    

    <?php
    if (!empty($invoiceDetailProvider)) {
        
        if(!empty($invoiceHeader->invoiceTrailer->id_002)){
            $delivery = number_format($invoiceHeader->invoiceTrailer->id_002, 2);
        } else {
            $delivery = '0.00';
        }

        $creditPo = 'FEE-' . $invoiceHeader->ih_023 . '-';
        $credit = \common\models\InvoiceHeader::find()->where(['LIKE', 'ih_023', $creditPo])->one();
        $creditApplied = '';
        if($credit){
            $creditApplied = ' | Credit Applied.';
        }
    ?>
    
        <h2 class="header-bg">Invoice No: <b>#<?=$invoiceHeader->ih_invoice_number?></b> | <?=$invoiceHeader->ih_019?> <?=$creditApplied?></h2>
        <br>

    <?php

        $detailViewCostValues = new \common\models\InvoiceDetailCostsViewModel();
        $detailViewCostValues->delivery = '£'.$delivery;
        $detailViewCostValues->totalExVat = '£'.number_format($invoiceHeader->invoiceTrailer->it_004, 2);
        $detailViewCostValues->vatCode = $invoiceHeader->invoiceTrailer->it_005;
        $detailViewCostValues->vat = '£'.number_format($invoiceHeader->invoiceTrailer->it_011, 2);
        $detailViewCostValues->totalIncVat = '£'.number_format($invoiceHeader->invoiceTrailer->it_012, 2);
        
        $invoiceHeader->invoiceTrailer->it_004 = (float) $invoiceHeader->invoiceTrailer->it_004;
        $invoiceHeader->invoiceTrailer->it_006 = (int) $invoiceHeader->invoiceTrailer->it_006;
        $invoiceHeader->invoiceTrailer->it_008 = (float) $invoiceHeader->invoiceTrailer->it_008;
        $invoiceHeader->invoiceTrailer->it_012 = (float) $invoiceHeader->invoiceTrailer->it_012;
        
                
        
        echo '<div class="row details">';
            
        
                echo '<div class="col-xs-12 col-md-6 col-lg-6">'
                        . '<div class="detail-box-header"><b>Invoice Details</b></div>'
                        . '<div class="detail-box">'
                        . '<div>Invoice Number <span class="pull-right"><b>' . $invoiceHeader->ih_invoice_number . '</b></span></div>'
                        . '<div>Customer PO <span class="pull-right"><b>' . $invoiceHeader->ih_023 . '</b></span></div>'
                        . '<div>S.O.P. <span class="pull-right"><b>' . $invoiceHeader->ih_026 . '</b></span></div>'
                        . '</div>'
                        . '</div>';
                
                echo '<div class="col-xs-12 col-md-6 col-lg-6">'
                        . '<div class="detail-box-header"><b>Invoice Address</b></div>'
                        . '<div class="detail-box">'
                        . '<div>Name: '.$invoiceHeader->ih_004.'</div>'
                        . '<div>Address</div>'
                        .  '<div class="text-center">'.$invoiceHeader->ih_008.'</div>'
                        .  '<div class="text-center">'.$invoiceHeader->ih_009.'</div>'
                        .  '<div class="text-center">'.$invoiceHeader->ih_010.'</div>'
                        .  '<div class="text-center">'.$invoiceHeader->ih_011.'</div>'
                        . '</div>'
                        . '</div>';
                
                echo '<div class="col-xs-12 col-md-6 col-lg-6">'
                        . '<div class="detail-box-header"><b>VAT Details</b></div>'
                        . '<div class="detail-box">'
                        . '<div>VAT No. <span class="pull-right"><b>' . $invoiceHeader->invoiceTrailer->it_010 . '</b></span></div>'
                        . '<div>VAT Code <span class="pull-right"><b>' . $invoiceHeader->invoiceTrailer->it_005 . '</b></span></div>'
                        . '<div>VAT Rate <span class="pull-right"><b>' . $invoiceHeader->invoiceTrailer->it_006 . '</b></span></div>'
                        . '<div>Goods Value <span class="pull-right"><b>£' . number_format($invoiceHeader->invoiceTrailer->it_004, 2) . '</b></span></div>'
                        . '<div>VAT Value <span class="pull-right"><b>£' . number_format($invoiceHeader->invoiceTrailer->it_008, 2) . '</b></span></div>'
                        . '</div>'
                        . '</div>';
                
                echo '<div class="col-xs-12 col-md-6 col-lg-6">'
                        . '<div class="detail-box-header"><b>Billing Details</b></div>'
                        . '<div class="detail-box">'
                        . '<div>Total [ex. VAT] <span class="pull-right"><b>£' . number_format($invoiceHeader->invoiceTrailer->it_004, 2) . '</b></span></div>'
                        . '<div>VAT Total <span class="pull-right"><b>£' . number_format($invoiceHeader->invoiceTrailer->it_008, 2) . '</b></span></div>'
                        . '<div>Invoice Total <span class="pull-right"><b>£' . number_format($invoiceHeader->invoiceTrailer->it_012, 2) . '</b></span></div>'
                        . '</div>'
                        . '</div>';
                
                

                if($credit){
                
                    echo '<div class="col-xs-12 col-md-6 col-lg-6">'
                        . '<div class="detail-box-header"><b>Credit Note</b></div>'
                        . '<div class="detail-box">'
                        . '<div>Invoice Number <span class="pull-right"><b>' . $credit->ih_invoice_number . '</b></span></div>'
                        . '<div>Credit <span class="pull-right"><b>£' . number_format($credit->invoiceTrailer->it_012, 2) . '</b></span></div>'
                        . '</div>'
                        . '</div>';
                }

        echo '</div><br><br>';
        
        
        $columns = [
            ['class' => 'yii\grid\SerialColumn'],
                [
                    'label' => 'Part Code',
                    'attribute' => 'id_004',
                ],
                [
                    'label' => 'Units Supplied',
                    'attribute' => 'id_001',
                    'format' => 'raw',
                    'value' => function($data){
                        return $data->id_001;
                    }
                ],
                [
                    'label' => 'Unit Price',
                    'attribute' => 'id_008',
                    'format' => 'raw',
                    'value' => function($data){
                        return "£".number_format($data->id_008,2);
                    }
                ],
                [
                    'label' => 'Total',
                    'attribute' => 'id_009',
                    'format' => 'raw',
                    'value' => function($data){
                        return "£".number_format($data->id_009,2);
                    }
                ],
                [
                    'label' => 'VAT Code',
                    'attribute' => 'id_010',
                ],
        ];
        
        echo GridView::widget([
            'dataProvider' => $invoiceDetailProvider,
            'filterModel' => $searchModel,
            'columns' => $columns,
            'pjax'              => true,
            'responsive'        => true,
            'hover'             => true,
            'condensed'         => true,
            'floatHeader'       => true,
            'floatHeaderOptions' => ['scrollingTop'           => 0,
                                     'useAbsolutePositioning' => true,
                                     'floatTableClass'        => 'kv-table-float slevel-float hidden-xs hidden-sm',
//                                        'autoReflow' => true          // Doesn't help scrolling problem

            ],


            'panel'             => [
                'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i>&nbsp;Products</h3>',
                'type'    => 'default',
            ],
        ]); 
                    
    } else {
        echo "Incorrect Details / Invoice Could Not Be Found";
    }
    ?>


</div><!-- invoices -->
