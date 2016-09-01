<?php
use yii\helpers\Html;
use kartik\grid\GridView;


/* @var $this yii\web\View */
$this->title                   = 'Invoices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-invoices container">
  
    <h1><?=$this->title?></h1>
    
    <?php
        if(Yii::$app->user->can('add_customer_user')){
        
            echo GridView::widget([
            'pjax'              => true,
            'pjaxSettings'      => [
                'replace' => false
            ],
            'dataProvider'      => $invoiceHeaderProvider,
            'filterModel'       => $searchModel,


            'toggleDataOptions' => [
                'all' => [
                    'icon'  => '',
                    'label' => ''
                ],
            ],
//        'export'    => false,             // Supposed to hide the export button, but causes javascript error


            'pager'             => [
            ],
            'columns'           => [
                'ih_invoice_number',
                'ih_023',
                [
                    'attribute' => 'invoiceTrailer.it_012',
                    'format' => 'raw',
                    'value' => function($data){
                        $data->invoiceTrailer->it_012 = str_replace('-', '', $data->invoiceTrailer->it_012);
                        return '£'.number_format($data->invoiceTrailer->it_012, 2);
                    }
                ],
                [
                    'label' => 'Credit',
                    'format' => 'raw',
                    'value' => function($data){
                        $creditPo = 'FEE-' . $data->ih_023 . '-';
                        $credit = \common\models\InvoiceHeader::find()->where(['LIKE', 'ih_023', $creditPo])->one();
                    
                        if($credit){
                            $result = (float) $credit->invoiceTrailer->it_012;
                        } else {
                            $result = 0.00;
                        }
                        
                        return '£'.number_format($result, 2);
                    }
                ],
                [
                    'label' => 'Credit Note',
                    'format' => 'raw',
                    'value' => function($data){
                        $creditPo = 'FEE-' . $data->ih_023 . '-';
                        $credit = \common\models\InvoiceHeader::find()->where(['LIKE', 'ih_023', $creditPo])->one();
                    
                        if($credit){
                            $result = $credit->ih_invoice_number;
                        } else {
                            $result = null;
                        }
                        
                        return $result;
                    }
                ],
                [
                    'label' => '',
                    'format' => 'raw',
                    'value' => function($data){
                        $viewBtn = Html::a('View Invoice', ['/invoices/view', 'id'=>$data->ih_header_row_id], ['class' => 'btn btn-primary', 'data-pjax'=>'0']);
                        
                        return $viewBtn;
                    }
                ],
            ],
            'responsive'        => true,
            'hover'             => true,
            'condensed'         => true,
            'floatHeader'       => true,
            'floatHeaderOptions' => ['scrollingTop'           => 0,
                                     'useAbsolutePositioning' => true,
                                     'floatTableClass'        => 'kv-table-float slevel-float hidden-xs hidden-sm',
//                                        'autoReflow' => true          // Doesn't help scrolling problem

            ],
        ]);
            
            
        }
    ?>
    
</div>
