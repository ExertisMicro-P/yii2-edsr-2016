<?php
namespace frontend\controllers;

use common\models\StockActivity;
use common\models\StockItem;
use frontend\controllers\yiicomp\StockroomController;
use Yii;
use Url;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\CreditLevel;

use frontend\models\RegisterForm;
use common\components\DigitalPurchaser;
use kartik\mpdf\Pdf;
use common\models\EmailedItem;
use common\models\EmailedUser;

//use Knp\Snappy\Pdf;

/**
 * Site controller
 */
class PrintkeysController extends yiicomp\StockroomController
{


    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'reprint'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
        ];
    }


    /**
     * BEFORE ACTION
     * =============
     * This is used to skip CSRF checks on the post for the print request
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws BadRequestHttpException
     */

    public function beforeAction($action)
    {
        if ($action->id == 'index') {
            Yii::$app->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }


    /**
     * INDEX
     * =====
     *
     * @return \yii\web\Response
     */
    public function actionIndex()
    {
        $keys = explode(',', Yii::$app->request->post('pdfkeys'));

        return $this->printKeys($keys, true);
    }

    public function actionReprint()
    {
        $keys = explode(',', Yii::$app->request->get('pdfkeys'));

        return $this->printKeys($keys, false);
    }


    /**
     * PRINT KEYS
     * ==========
     *
     * @param $stockitem_ids
     * @param $unpurchasedOnly
     *
     * @return string|void|\yii\web\Response
     * @throws
     * @throws \Exception
     */
    private function printKeys($stockitem_ids, $unpurchasedOnly)
    {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/user/index');
        }

        if (($user = $this->getUserDetails()) === false) {
            return $this->redirect('/user/index');

        } else {
            $this->layout = false;

            $stockitem_ids = $this->validateStockKeys($user, $stockitem_ids, $unpurchasedOnly);

            StockActivity::log('Printing keys for ' . count($stockitem_ids) . ' products', $this->stockroomId,
                null, null,
                EmailedItem::tableName(), null
            );

            $recipientDetails = [
                'email'       => null,
                'recipient'   => null,
                'orderNumber' => 'PrintedKeys' . date('d-m-Y H:i:s'),
                'errors'      => []
            ];

            // ---------------------------------------------------------------
            // If we're re-printing, we don't need to record the details
            // ---------------------------------------------------------------
            if ($unpurchasedOnly) {
                list($result, $selectedDetails) = $this->saveEmailedOrderDetails($recipientDetails, $stockitem_ids);
            } else {
                $result = true;
            }

            if ($result === true) {
                $products = $this->findAllProducts($stockitem_ids);
                $html     = $this->printAllProducts($products);

                return $this->convertHtmlToPdf($html);

            } else {
                $status = 404;
                print_r($result);

                return 'Invalid request';

            }
        }
    }

    /**
     * FIND ALL PRODUCTS
     * =================
     * The printout is intend to use a view named after the partcode, so we
     * read that. In doing do, we also verify that the purchase belongs to
     * the currently logged in account and silently ignore any that don't.
     *
     * @param $keys
     * @param $sroomIds
     *
     * @return array
     */
    private function findAllProducts($keys)
    {
        $stockItems = StockItem::find()
            ->select('stock_item.id, productcode, stockroom_id, eztorm_order_id, eztorm_product_id') // RCH 20160215 Added stockroom_id and eztorm_order_id to stop Reprint keys failing when we call DigitalPurchaser
            ->where(['stock_item.id' => $keys])
            ->joinWith('stockroom')
            ->joinWith('digitalProduct')
            ->joinWith('digitalProduct.productLeafletInfo')
            ->andWhere(['account_id' => $this->user->account_id])
            ->all();

        $products = [];

        foreach ($stockItems as $sitem) {

            $products[] = [
                'stockId'     => $sitem->id,
                'partcode'    => ($partcode = $sitem->productcode),
                'dpId'        => $sitem->digitalProduct->id,
                'description' => $sitem->digitalProduct->description,
                'image'       => $sitem->digitalProduct->image_url,
                'thumb'       => $sitem->digitalProduct->getMainImageThumbnailTag(false),
                'key'         => DigitalPurchaser::getProductInstallKey($sitem),
                'leaflet'     => [
//                    'image'      => Yii::getAlias('@webroot') . '/' . Yii::$app->params['uploadPath'] . 'product_leaflets/' . $partcode . '/' . $sitem->digitalProduct->productLeafletInfo->image,
                    'image_type'      => $sitem->digitalProduct->productLeafletInfo->image_type,
                    'image'           => $sitem->digitalProduct->productLeafletInfo->getLeafletImageFilename(),

                    'key_xcoord'      => $sitem->digitalProduct->productLeafletInfo->key_xcoord,
                    'key_ycoord'      => $sitem->digitalProduct->productLeafletInfo->key_ycoord,
                    'key_box_width'   => $sitem->digitalProduct->productLeafletInfo->key_box_width,
                    'key_box_height'  => $sitem->digitalProduct->productLeafletInfo->key_box_height,

                    'logo_xcoord'     => $sitem->digitalProduct->productLeafletInfo->logo_xcoord,
                    'logo_ycoord'     => $sitem->digitalProduct->productLeafletInfo->logo_ycoord,
                    'logo_box_width'  => $sitem->digitalProduct->productLeafletInfo->logo_box_width,
                    'logo_box_height' => $sitem->digitalProduct->productLeafletInfo->logo_box_height,

                    'name_xcoord'     => $sitem->digitalProduct->productLeafletInfo->name_xcoord,
                    'name_ycoord'     => $sitem->digitalProduct->productLeafletInfo->name_ycoord,
                    'name_box_width'  => $sitem->digitalProduct->productLeafletInfo->name_box_width,
                    'name_box_height' => $sitem->digitalProduct->productLeafletInfo->name_box_height
                ]
            ];
        }

//echo '<pre>';print_r($products);exit;
        return $products;
    }

    /**
     * PRINT ALL PRODUCTS
     * ==================
     * This builds an html string with the formatted html for all the requested
     * keys.
     *
     * @param $products
     *
     * @return string
     */
    private function printAllProducts($products)
    {
        $html = '';
        foreach ($products as $product) {
            $view = $this->findViewfileForProduct($product);

            $html .= $this->processProduct($product, $view);
//echo ($html);exit;
//break ;
        }

        return $html;
    }

    /**
     * FIND VIEW FILE FOR PRODUCT
     * ==========================
     * Each product can have its own customised view by creating a directory
     * named after it's partcode and including a view file called main.php.
     *
     * If that doesn't exist, we drop back to using default.php
     *
     * For convenience, any shared partials should be placed in the common directory
     *
     * @param $product
     *
     * @return string
     */
    private function findViewfileForProduct($product)
    {
        $baseViewPath  = $this->viewPath . '/';
        $localViewPath = 'mindypdftemplates/';

        $productPath = $localViewPath . $product['partcode'] . '/';
        $productView = $productPath . 'main.php';

        $fullPath = $baseViewPath . $productView;

        if (!is_file($fullPath) || !is_readable($fullPath)) {
            $productView = $localViewPath . 'default.php';
        } else {
            //die (__METHOD__.': $fullPath=' . $fullPath);
        }

        return $productView;
    }

    /**
     * PROCESS PRODUCT
     * ===============
     * This is responsible for collecting the html corresponding to a single
     * product and it's key, as defined by the passed product and view file.
     *
     * @param $product
     * @param $view
     *
     * @return string
     */
    private function processProduct($product, $view)
    {

        // -----------------------------------------------------------------------
        // Creating the image using the raw data is intensive and needs a lot of ram
        // -----------------------------------------------------------------------
        ini_set("memory_limit", "256M");

        $leafletImage = $this->getLeafletGdImage($product);
        if ($leafletImage) {
            $this->addKeyToLeaflet($leafletImage, $product);
            $this->addNameToLeaflet($leafletImage, $product);

            $this->addAccountLogoToLeaflet($leafletImage, $product);

            // -----------------------------------------------------------------------
            // Allocate the temporary file and write the image to it
            // -----------------------------------------------------------------------
            $workImg = tempnam(Yii::getAlias('@frontend') . '/runtime/tmp', 'LEF') . '.jpg';
            imagejpeg($leafletImage, $workImg);

            return $this->renderPartial($view, [
                'workImg' => $workImg
            ]);
        }
    }

    /**
     * CHECK FONTS
     * ===========
     * Find the ttf font to use for the text output. The same font should ideally
     * be used in the editor at the back end
     */
    private function checkFonts()
    {
        if (!defined('TTF_DIR')) {
            if (!array_key_exists('fontLocation', Yii::$app->params)) {
                die('You must define params[\'fontLocation\']');

            } else {
                DEFINE("TTF_DIR", Yii::$app->params['fontLocation']);
            }

            if (!array_key_exists('fontFile', Yii::$app->params)) {
                die('You must define params[\'fontFile\'');

            } else {
                DEFINE("TTF_FONTFILE", Yii::$app->params['fontFile']);
            }
        }
    }

    private function getLeafletGdImage($product)
    {
        return $this->getImageObject($product['leaflet']['image_type'], $product['leaflet']['image']);
    }

    private function addKeyToLeaflet($leafletImage, $product)
    {
        $this->checkFonts();

        //$text = 'YF3CN-3WXXY-7XXM2-YXX86-2XXMF'; // Debug / Demo
        $text = $product['key'];

        // -----------------------------------------------------------------------
        // Create a text box with the desired font and size and output the key
        // -----------------------------------------------------------------------
        $fontFile = TTF_DIR . TTF_FONTFILE;
        $fontSize = 55;
        $angle    = 0;

        $dimensions = imagettfbbox($fontSize, $angle, $fontFile, $text);

        // -----------------------------------------------------------------------
        // Using the output from the above, we can calculate the actual width and
        // height of the text in pixels.
        // -----------------------------------------------------------------------
        $dimensionsOfText         = new \stdClass();
        $dimensionsOfText->width  = abs($dimensions[2] - $dimensions[0]);
        $dimensionsOfText->height = abs($dimensions[1] - $dimensions[7]);

        // -----------------------------------------------------------------------
        // Now do the same for the area allocated to the key, and the difference
        // gives the offsets to output the text so that it is both vertically and
        // horizontally centered in the defined area.
        // -----------------------------------------------------------------------
        $dimensionsOfSpace         = new \stdClass();
        $dimensionsOfSpace->width  = $product['leaflet']['key_box_width'];
        $dimensionsOfSpace->height = $product['leaflet']['key_box_height'];

        $xoffset = abs($dimensionsOfSpace->width - $dimensionsOfText->width) / 2;
        $yoffset = abs($dimensionsOfSpace->height - $dimensionsOfText->height) / 2;

        // -----------------------------------------------------------------------
        // Can now write the text on to the image, including the set x and y coords
        // -----------------------------------------------------------------------
        $black = ImageColorAllocate($leafletImage, 0, 0, 0);
        $xpos  = $product['leaflet']['key_xcoord'];
        $ypos  = $product['leaflet']['key_ycoord'] - $dimensions[5];

        imagettftext($leafletImage, $fontSize, $angle, $xpos + $xoffset, $ypos + $yoffset, $black, $fontFile, $text);
    }

    private function addNameToLeaflet($leafletImage, $product)
    {
        $this->checkFonts();

        //var_dump($product); die();
        
        //$text = 'YF3CN-3WXXY-7XXM2-YXX86-2XXMF'; // Debug / Demo
        $text = $product['description'];

        // -----------------------------------------------------------------------
        // Create a text box with the desired font and size and output the key
        // -----------------------------------------------------------------------
        $fontFile = TTF_DIR . TTF_FONTFILE;
        $fontSize = 40;
        $angle    = 0;

        $dimensions = imagettfbbox($fontSize, $angle, $fontFile, $text);

        // -----------------------------------------------------------------------
        // Using the output from the above, we can calculate the actual width and
        // height of the text in pixels.
        // -----------------------------------------------------------------------
        $dimensionsOfText         = new \stdClass();
        $dimensionsOfText->width  = abs($dimensions[2] - $dimensions[0]);
        $dimensionsOfText->height = abs($dimensions[1] - $dimensions[7]);

        // -----------------------------------------------------------------------
        // Now do the same for the area allocated to the key, and the difference
        // gives the offsets to output the text so that it is both vertically and
        // horizontally centered in the defined area.
        // -----------------------------------------------------------------------
        $dimensionsOfSpace         = new \stdClass();
        $dimensionsOfSpace->width  = $product['leaflet']['name_box_width'];
        $dimensionsOfSpace->height = $product['leaflet']['name_box_height'];

        $xoffset = abs($dimensionsOfSpace->width - $dimensionsOfText->width) / 2;
        $yoffset = abs($dimensionsOfSpace->height - $dimensionsOfText->height) / 2;

        // -----------------------------------------------------------------------
        // Can now write the text on to the image, including the set x and y coords
        // -----------------------------------------------------------------------
        $black = ImageColorAllocate($leafletImage, 0, 0, 0);
        $xpos  = $product['leaflet']['name_xcoord'];
        $ypos  = $product['leaflet']['name_ycoord'] - $dimensions[5];

        imagettftext($leafletImage, $fontSize, $angle, $xpos + $xoffset, $ypos + $yoffset, $black, $fontFile, $text);
    }

    private function addAccountLogoToLeaflet($leafletImage, $product)
    {
        if ($product['leaflet']['logo_box_width'] && $product['leaflet']['logo_box_height']) {
            $accountLogo = $this->user->account->getAccountLogo();
            if ($accountLogo) {

                $accountLogo = Yii::getAlias('@webroot') . $accountLogo;

                list($width, $height) = getimagesize($accountLogo);

                $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $accountLogo);
                finfo_close($finfo);

                $logoGdImage = $this->getImageObject($mimeType, $accountLogo);

                $left     = $product['leaflet']['logo_xcoord'];
                $top      = $product['leaflet']['logo_ycoord'];
                $lbWidth  = $product['leaflet']['logo_box_width'];
                $lbHeight = $product['leaflet']['logo_box_height'];

                if ($width > $lbWidth || $height > $lbHeight) {
                    $scale   = $scaleX = $scaleY = 0;
                    $owidth  = $width;
                    $oheight = $height;

                    if ($width > $lbWidth) {
                        $scaleX = $lbWidth / $width;
                    }
                    if ($height > $lbHeight) {
                        $scaleY = $lbHeight / $height;
                    }

                    if ($scaleX <> 0 && $scaleY <> 0) {
                        $scale = min($scaleX, $scaleY);

                    } elseif ($scaleX > 0) {
                        $scale = $scaleX;

                    } elseif ($scaleY > 0) {
                        $scale = $scaleY;
                    }

                    if ($scale) {
                        $owidth *= $scale;
                        $oheight *= $scale;
                    }

                    $xoffset = $left + abs($lbWidth - $width) / 2;
                    $yoffset = $top + abs($lbHeight - $height) / 2;

                    imagecopyresized($leafletImage, $logoGdImage,
                        0, 0, 0, 0,
                        $xoffset, $yoffset,
                        $width, $height, $owidth, $oheight);

                } else {
                    imagecopy($leafletImage, $logoGdImage, $left, $top, 0, 0, $width, $height);
                }
            }
        }

    }


    private function getImageObject($imageType, $imageFile)
    {
        switch ($imageType) {
            case 'image/jpeg':
                return imagecreatefromjpeg($imageFile);
                break;
            case 'image/png':
                return imagecreatefrompng($imageFile);
                break;
            case 'image/gif':
                return imagecreatefromgif($imageFile);
                break;
        }

        return false;
    }


    /**
     * CONVERT HTML TO PDF
     * ===================
     * This processes the passed html string to convert it into a pdf file,
     * which it then echos to the browser as a downloadable file called key.pdf
     *
     * @param $html
     */
    private function convertHtmlToPdf($html)
    {

        $pdf = new Pdf([
            'mode'        => Pdf::MODE_CORE,
            'format'      => 'A4',  //  Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,

            'methods'     => [
                'SetHeader' => ['Krajee Report Header'],
                'SetFooter' => ['{PAGENO}'],
            ],

            'content'     => $html,
//            'filename'    => $workPDF ,
            'destination' => Pdf::DEST_BROWSER,

            'cssFile'     => 'css/mindy.css',

        ]);

        echo $pdf->render();

        return;

        // -------------------------------------------------------------------
        // Produce a single pdf with all pages at A4 size
        // -------------------------------------------------------------------
//        $workPDF = tempnam(Yii::getAlias('@frontend') . '/runtime/tmp', 'FOO');
//
//
//        $options = ['user-style-sheet' => Yii::getAlias('@webroot') . '/css/mindy.css'];
//
//        $snappy = new Pdf('/usr/local/bin/wkhtmltopdf');
//        $snappy->generateFromHtml($html, $workPDF, $options, true);
//
//        header('Content-Type: application/pdf');
//        header('Content-Disposition: attachment; filename="key.pdf"');
//        echo file_get_contents($workPDF);

    }

}

