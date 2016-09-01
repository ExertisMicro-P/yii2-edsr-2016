<?php

use yii\helpers\Url;
use yii\helpers\Html;

/**
 * CLASS EXCEL EXPORTER
 * ====================
 */

namespace frontend\components ;
//namespace console\components\OrderFeedFile;


class ExcelExporter
{
    private $wBook ;
    private $sheet ;
    private $rowNumber = 1 ;                // Rows start at 1, but cols at 0


    /**
     * NEW SALES EMAIL
     * ===============
     *
     * @param $salesData
     */
    public function newSalesEmail($salesData) {
//echo '<pre>'; print_r($salesData); exit ;
        $cols = [
// ---------------------------------------------------------------------------
// This inserts the image, but it floats instead of being in the cell, so
// not using it.
// ---------------------------------------------------------------------------
//            [
//                'content'   => [
//                    'data'  => function ($colIndex, $rowNumber, $dataItem) {
//                        $stockItem = $dataItem['item'];
//                        $img = $stockItem->getImageUrl() ;
//
//                        if ($img->image_url) {
//                            $col = chr(ord('A') + $colIndex) ;
//
//                            $gdImage = imagecreatefromjpeg($img->image_url);
//
//                            $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
//                            $objDrawing->setImageResource($gdImage);
//                            $objDrawing->setRenderingFunction(\PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
//                            $objDrawing->setMimeType(\PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT);
//                            $objDrawing->setHeight(150);
//
//                            $objDrawing->setOffsetX(0);
//                            $objDrawing->setOffsetY(0);
//
//                            $objDrawing->setCoordinates($col . $rowNumber);
//                            $objDrawing->setWorksheet($this->wBook->getActiveSheet()) ;
//
////                        $objDrawing->setName('Logo');
////                        $objDrawing->setDescription('Logo');
////                            $objDrawing->setPath($img->image_url);
////                            $objDrawing->setHeight(36);
//                        }
//                    }
//                ]
//            ],
            'Purchase Order',

            [
                'header'   => 'Order',
                'content'  => [
                    'data'  => function ($colIndex, $rowNumber, $dataItem) {

                        if (!$dataItem ||
                            !array_key_exists('item', $dataItem) ||
                            !array_key_exists('orderdetails', $dataItem)) {
                            return '' ;
                        }
                        $stockItem = $dataItem['item'];

                        return $dataItem['orderdetails']->sop . '-' .
                        $stockItem->id . '-' . $stockItem->eztorm_order_id ;
                    }
                ]
            ],


            [
                'header'   => 'Partcode',
                'content'  => [
                    'data'  => function ($colIndex, $rowNumber, $dataItem) {

                        if (!$dataItem ||
                            !array_key_exists('item', $dataItem) ||
                            !array_key_exists('orderdetails', $dataItem)) {
                            return '' ;
                        }
                        $stockItem = $dataItem['item'];
                        return $stockItem->productcode ;
                    }
                ]
            ],

            [
                'header'    => 'Description',
                'content'   => [
                    'data'  => function ($colIndex, $rowNumber, $dataItem) {
                        if ($dataItem) {
                            return $dataItem['product']->description ;
                        }
                    }
                ]
            ],

            [
                'header'    => 'Name',
                'content'   => [
                    'data'  => function ($colIndex, $rowNumber, $dataItem) {
                        if ($dataItem) {
                            return $dataItem['orderdetails']->name ;
                        }
                    }
                ]
            ],


            [
                'header'    => 'Install Key',
                'width'     => '200',            // What dimensions?
                'headerstyle' => [
                    'fill'    =>   [
                        'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                        'startcolor' => ['argb' => 'FFFFC'
                        ]
                    ]
                ],
                'content'   => [
                    'data'  => function ($colIndex, $rowNumber, $dataItem) {
                        if ($dataItem) {
                            $stockItem = $dataItem['item'];
                            return \common\components\DigitalPurchaser::getProductInstallKey($stockItem);
                        }
                    }
                ]
            ]
        ];

        $fname = realpath(\Yii::getAlias('@runtime')) . '/keys.xlsx' ;

        $this->createDoc($fname) ;
        $this->writeHeaderRow($cols) ;

        foreach($salesData['pos'] as $key => $purchaseOrder) {
            foreach ($purchaseOrder as $dataItem) {
                $this->writeDataRow($cols, array_merge([$key], $dataItem)) ;
            }
        }

        $this->saveXlsx($fname) ;

        return $fname ;
    }

    /**
     * CREATE DOC
     * ==========
     * @param $fileName
     */
    public function createDoc($fileName) {
        $this->wBook = new \PHPExcel() ;
        $this->wBook->getProperties()->setDescription('Your Keys') ;
        $this->wBook->setActiveSheetIndex(0) ;

        $this->wBook->getSecurity()->setLockWindows(true);
        $this->wBook->getSecurity()->setLockStructure(true);
        $this->wBook->getSecurity()->setWorkbookPassword('secret');

        $this->sheet = $this->wBook->getActiveSheet() ;
    }

    /**
     * SAVE XLSX
     * =========
     * Writes out the final spreadsheet file as an Excel 2007 format
     *
     * @param $fileName
     *
     * @throws \PHPExcel_Writer_Exception
     */
    public function saveXlsx($fileName) {
        $writer = new \PHPExcel_Writer_Excel2007($this->wBook);
        $writer->save($fileName) ;
    }


    /**
     * WRITE HEADER ROW
     * ================
     * Outputs a simple header row to the current spreadsheet object. The
     * column contents need to be provided in the call parameter. This can
     * be either a simple string or an array with additional details.
     *
     * If an array, it can have one or moe of the following offsets, each
     * of which may be an array
     *
     *      header      the text to display
     *      width       the column width
     *      style       an array with phpexcel specific formatting codes, eg
     *          font        [name => 'Arial', 'size' => 8]
     *          borders     [left => [style => [], right => []]
     *          fill        [type => solid, startcolor => [argb => FFFFC]]
     *          vertical    [style => PHPExcel_Style_Border::BORDER_THIN]]
     *
     * @param array $columns
     */
    public function writeHeaderRow(Array $columns = null)
    {
        if(!$columns) {
            $columns = $this->cols ;
        }

        $colIndex = 0 ;
        foreach ($columns as $column) {
            $value = null ;

            if (is_array($column)) {
                if (array_key_exists('header', $column)) {
                    $value = $column['header'] ;
                }
                if (array_key_exists('style', $column)) {
                    $this->sheet->getStyleByColumnAndRow($colIndex, $this->rowNumber)
                         ->applyFromArray($column['style']);
                }
                if (array_key_exists('width', $column)) {
                    $this->sheet->getColumnDimensionByColumn($colIndex)
                         ->setWidth($column['width']);
                }

            } else {
                $value = $column ;
            }

            $this->sheet->setCellValueByColumnAndRow($colIndex, $this->rowNumber, $value);
            $colIndex++ ;
        }
        $this->rowNumber++ ;
    }

    /**
     * WRITE DATA ROW
     * ==============
     * This outputs a single data row to the spreadsheet using any formatting
     * information provided in the columns array.
     *
     * Columns should have an entry for each column, but will only be processed
     * if the entry is an array with a 'content' offset. That should itself be an
     * array with one or more of the following offsets.
     *
     *      content
     *          width       the column width
     *          colspan     used for merged cells.If absent, 0 or 1 a single cell is used
     *          data       if provided, the data source. One or other of
     *              attribute   if $data is an object, the attribute to use
     *              a closure   a method to call along with the column and row numbers
     *          style       an array with phpexcel specific formatting codes, eg
     *              font        [name => 'Arial', 'size' => 8]
     *              borders     [left => [style => [], right => []]
     *              fill        [type => solid, startcolor => [argb => FFFFC]]
     *              vertical    [style => PHPExcel_Style_Border::BORDER_THIN]]
     * This is configured so that the same array can be used for the header
     * and data rows.
     *
     * @param array $columns
     * @param array $data
     */
    public function writeDataRow(Array $columns, $data) {
        $colIndex = 0 ;
        foreach ($columns as $ind => $column) {
            $options = [] ;

            // ---------------------------------------------------------------
            // Start with the complex option
            // ---------------------------------------------------------------
            if (is_array($column)) {
                $value = $data;
                if (array_key_exists('content', $column)) {
                    $options = $column['content'];

                    if (array_key_exists('width', $options)) {
                        $this->sheet->getColumnDimensionByColumn($colIndex)
                            ->setWidth($options['width']);
                    }
                    if (array_key_exists('style', $column)) {
                        $this->sheet->getStyleByColumnAndRow($colIndex, $this->rowNumber)
                            ->applyFromArray($column['style']);
                    }
                    if (array_key_exists('data', $options)) {
                        $attribute = $options['data'];

                        if ($attribute instanceof \Closure) {
                            $value = call_user_func($attribute, $colIndex, $this->rowNumber, $value);

                        } elseif (is_array($data)) {
                            $value = $data[$attribute];

                        } elseif (is_object($data)) {
                            $value = $data->$attribute;
                        }
                    }
                }

            // ---------------------------------------------------------------
            // Shouldn't happen - all fields the same value
            // ---------------------------------------------------------------
            } elseif (!is_array($data)) {
                $value = $data;

            // ---------------------------------------------------------------
            // No value provided for the current index
            // ---------------------------------------------------------------
            } elseif (!array_key_exists($colIndex, $data)) {
                $value = null ;

            } else {
                $value = $data[$colIndex];
            }

            $this->sheet->setCellValueByColumnAndRow($colIndex, $this->rowNumber, $value);

            // ---------------------------------------------------------------
            // Adjust the column index
            // ---------------------------------------------------------------
            $colIndex++ ;
            if (array_key_exists('colspan', $options)) {
                $colspan = intvalue($options['colspan']) ;
                if ($colspan > 1) {
                    $colIndex += $colspan-1 ;
                }
            }
        }
        $this->rowNumber++ ;
    }
}
