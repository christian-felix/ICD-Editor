<?php
/**
 * Step 1: Require the Slim Framework
 *
 * If you are not using Composer, you need to require the
 * Slim Framework and register its PSR-0 autoloader.
 *
 * If you are using Composer, you can skip this step.
 */

require 'Slim/Slim.php';


require 'src/generatePDF.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->config('templates.path','template');

/**
 *  Main Image
 */
$app->get('/editor/preview', function () use ($app){
    $app->render('preview.phtml');
});

/**
 *  Detail Image
 */
$app->get('/editor/detailPreview', function () use ($app){
    $app->render('detailPreview.phtml');
});

/**
 *  Set related coordinates
 */
$app->get('/editor/add', function () use ($app) {

    $images = "";
    //read all available images in Directory "images"
    $dirHandle = opendir("images");
    while( false !==  ($img = readdir($dirHandle))){
        if(!is_dir($img)) {
            $images[] = $img;
        }
    }
    closedir($dirHandle);
    $app->render('add.phtml',array('images' => $images));
});

/**
 *  get icds
 */
$app->get('/editor/icds/:icd', function($icd) use ($app){

    $pdf = new GeneratePDF();
     $icds = $pdf->getICDs($icd);
    echo $icds;
});

/**
 *  Edit ICD
 */
$app->get('/editor/edit/:id', function() use ($app){
    //TODO:
});

/**
 *  Save ICD and related coordinates
 */
$app->get('/editor/save', function () use ($app) {

    $icd = $app->request()->get('icd');

    //Gesamt Image Koordinaten
    $xTotal       = $app->request()->get('xTotal');
    $yTotal       = $app->request()->get('yTotal');
    $widthTotal   = $app->request()->get('widthTotal');
    $heightTotal  = $app->request()->get('heightTotal');

    //Detail Image Koordinaten
    if($app->request()->get('image')) {
        $image = $app->request()->get('image');
        $xDetail = $app->request()->get('xDetail');
        $yDetail = $app->request()->get('yDetail');
        $widthDetail = $app->request()->get('widthDetail');
        $heightDetail = $app->request()->get('heightDetail');
    }

    $pdf = new GeneratePDF();

    //Gesamt Image Position
    if(isset($xTotal) && isset($yTotal) && isset($widthTotal) && isset($heightTotal)) {
        $pdf->saveRectCoordinate($xTotal, $yTotal, $widthTotal, $heightTotal, $icd, 'totalImage');
    }

    //Detail Image Position
    if(isset($image) && isset($xDetail) && isset($yDetail)){
        $detailCoordinate = floor($xDetail/4) .",".floor($yDetail/4) .",". floor($widthDetail /4) .",". floor($heightDetail /4);  //TODO
        //$dbImage = str_replace(".png","",strtolower($image));
        //$sql = "UPDATE sysm_icd_data SET bild_detail = '$dbImage', bild_detail_pos = '$detailCoordinate' WHERE ICD = '$icd' ";
        //mysql_query($sql, $db);

        $pdf->saveRectCoordinate($xDetail, $yDetail, $widthDetail, $heightDetail, $icd, 'detailImage', $image);
    }

    //$pdf = new GeneratePDF();
    $pdf->setAutoPageBreak(true);
    $pdf->addPage();
    $pdf->generateMainImage($xTotal, $yTotal+ 15 , $widthTotal, $heightTotal);

    //Gesamt Image
    $filename = "coordinate.pdf";
    $pdf->Output($filename,'D');
    $pdf->Output($filename, 'F');

    
    unset($pdf);
    $pdf = new GeneratePDF();
    $pdf->setAutoPageBreak(true);
    $pdf->addPage();

    //Detail Image
    if(isset($image)) {
        $pdf->generateDetailImage($xDetail, $yDetail, $widthDetail, $heightDetail, $image);
        $filename = "detailCoordinate.pdf";
        //$pdf->Output($filename, 'D'); //
        $pdf->Output($filename, 'F');
    }

    //$app->view();
    //$app->render('save.phtml');
});

$app->run();
