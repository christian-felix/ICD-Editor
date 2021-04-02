<?php

require('C:\xampp\htdocs\workspace2\Triage\fpdf\fpdf.php'); //

require(dirname(__FILE__).'\DB.php');

/**
 * Class GeneratePDF
 */
class GeneratePDF extends FPDF
{
    /**
     *
     *
     * @param $xPos     X-Position
     * @param $yPos     Y-Position
     * @param $width    Länge
     * @param $height   Höhe
     */
    public function generateMainImage($xPos, $yPos, $width, $height){

        $leftSpace = 40;
        $this->Image('images\V_Gesamt.png',$leftSpace , 0, 90,240);
        $this->Rect($leftSpace + $xPos, $yPos, $width, $height);  // Betroffener Bereich

        //90 * 240 ( stet im Verhältnis 1:3  hinsichtlich des generiereten PDF - Dokuments) => TODO
        $this->Rect($leftSpace, 0, 90,240); //Img Box [wird zur Übertragung auf die Ziel-Koordinate benötigt)
    }

    /**
     *
     * @param $xPos     x-Position
     * @param $yPos     y-Position
     * @param $width    Länge
     * @param $height   Höhe
     */
    public function generateDetailImage($xPos, $yPos, $width, $height, $detailImg){

        $leftSpace = 40;
        $this->Image('Images\\'.$detailImg, $leftSpace, 10,100);
        $this->Rect($leftSpace + $xPos, $yPos + 10, $width, $height); //Betroffener Bereich

        //Verhältnis 1:4
        $this->Rect($leftSpace, 10,100, 100);
    }

    /**
     * @throws Exception
     * @return array $icds
     */
    public function getICDs($icd){
        if(!class_exists('DB')){throw new Exception("class DB not available!");}
        $result = DB::getInstance()->getData('SELECT icd, icd_description_1, icd_description_2 FROM sysm_icd_dimdi WHERE icd like "%'.$icd.'%"',array('icd'));
        return json_encode($result);
    }

    /**
     * @param $xPos         X-Position  (Startposition)
     * @param $yPos         Y-Position
     * @param $width        Breite/Länge
     * @param $height       Höhe
     * @param $icd          icd Code
     * @param $type         (detail / gesamt)
     * @param $image        Detail-Image
     */
    public function saveRectCoordinate($xPos, $yPos, $width, $height, $icd, $type = 'totalImage', $image=null){

        //TODO: Use same data coordinates as in pdf document => Test Routine

        switch($type){
            case 'totalImage':
                $sCoordinate = floor($xPos/3) . "," . floor($yPos/3) . "," . floor($width/3) . "," . floor($height/3);
                DB::getInstance()->query("UPDATE sysm_icd_data SET bild_gesamt_pos = '$sCoordinate', bild_gesamt_typ = 'rect' WHERE ICD = '$icd' ");
                break;
            case 'detailImage':
                $sCoordinate = floor($xPos/4) . "," . floor($yPos/4) . "," . floor($width/4) . "," . floor($height/4);
                $img = str_replace(".png","",strtolower($image));
                DB::getInstance()->query("UPDATE sysm_icd_data SET bild_detail_pos = '$sCoordinate', bild_detail_typ = 'rect', bild_detail = '$img' WHERE ICD = '$icd' ");
                break;
        }
    }
}


?>