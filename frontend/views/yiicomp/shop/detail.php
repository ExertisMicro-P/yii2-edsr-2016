<?php
    use yii\helpers\Html ;
    use yii\bootstrap\Carousel;

?>

<div class="panel panel-default">
  <div class="panel-heading">
      &nbsp;<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  </div>
  <div class="panel-body">

      <div class="row"> <!-- box shots left, spec right -->
          <div class="lead truncate details-heading col-md-11"><?= $product->getProductName() ?></span></div>
          <div class="col-md-6" id="details-gallery">
              <div class="row text-center">
                     <?= Html::a(Html::img($product->getBoxShotUrl(), ['class'=>'details-main-image']), $product->getBoxShotUrl(), ['target'=>'_blank']); ?>
                       </div> <!-- row -->

                        <div class="row">
                            <?php
                            /*
                             * RCH 20160330
                             * TEMP REMOVE UNTIL WE CAN GRAB SCREENSHOTS
                             * 
                      foreach ($product->getScreenshots() as $sshot) {

                        echo '<div class="col-xs-3">';
                        echo Html::a(Html::img($sshot, ['class'=>'details-gallery-image']), $sshot, ['target'=>'_blank']);
                        echo '</div>';
                          

                      }
                             * 
                             */
                      ?>
 </div> <!-- row -->

                      
                     

                    

          </div>
          <div class="col-md-5 col-md-offset-1"> <!-- specs -->
              <ul class="list-group">
                <li class="list-group-item"><?= $product->partcode ; ?></li>
                <li class="list-group-item">Category: <?= $product->getCategory() ; ?></li>
                <li class="list-group-item">Format: <?= $product->getFormat() ; ?></li>
                <li class="list-group-item">Publisher: <?= $product->getPublisher() ; ?></li>
                <li class="list-group-item">Rating: <?= $product->getPegi() ; ?></li>
                

              </ul>
              <?= '<span class="badge">'.implode('</span><span class="badge">',$product->getGenres()).'</span>' ; ?>
          </div>
      </div>

      <div class="row">
        <div class="details-info col-md-12">
            <h2>Description</h2>
        <?= $product->getInformation() ; ?>
        </div>

      </div>

      <div class="row">
        <div class="details-info col-md-12">
            <h2>Requirements</h2>
       <?= $product->getRequirements() ; ?>
        </div>

      </div>
  </div>
  <div class="panel-footer">&nbsp;<button type="button" class="btn btn-success btn-xs pull-right" data-dismiss="modal">Back to Shop</button></div>
</div>    
<script>
    //$('#gallery').photobox('a', { thumbs:true, loop:false });
</script>
