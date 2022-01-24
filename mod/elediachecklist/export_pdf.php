<?php

//include_once("connection.php");
include_once('libs/fpdf.php');
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
global $CFG;

class export_pdf extends FPDF {

   // Page header
    function Header()
    {
        // Logo
        //$this->Image('pix/logo.png',10,-1,70);
        $this->SetFont('Arial','B',9);
        // Move to the right
        $this->Cell(80);
        // Title
        $type = optional_param('type', '', PARAM_TEXT);
        if ($type == "qm") {
            $this->Cell(80, 10, iconv('UTF-8', 'windows-1252', 'Qualitätsmanagement'), 1, 0, 'C');
        } else {
            $this->Cell(80, 10, iconv('UTF-8', 'windows-1252', 'Endabnahmeprotokoll E-Klausur'), 1, 0, 'C');
        }
        // Line break
        $this->Ln(20);
    }

// Page footer
  /*  function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }*/

    function Footer()
    {

        $type = optional_param('type', '', PARAM_TEXT);
        if ($type == "qm") {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','I',7);
            // Page number
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        } else {
            // Arial italic 8
            //$this->SetFont('Arial', 'I', 8);
            $this->SetFont('Arial','',9);

            if ($this->PageNo() == 1) {
                $this->SetY(-40);
                $this->Cell(0, 10, '__________________________________                                           ____________________________________', 0, 0, 'C');
                $this->SetY(-35);
                $this->SetX(75);
                $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', get_string("unterschrift_eklausurteam", "elediachecklist")), 0, 0, 'L');
                $this->SetX(163);
                $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', get_string("unterschrift_verantwortlicher", "elediachecklist")), 0, 0, 'L');


                $this->SetFont('Arial', 'B', 10);
                $this->SetY(120);
                //$this->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Bemerkungen:'), 0, 0, 'L');
                //$this->Cell(0, 10, iconv('UTF-8', 'windows-1252', get_string("Es_wird_vom_Fachgebiet", "elediachecklist")), 0, 0, 'L');

                $this->SetFont('Arial', '', 9);
                //$this->SetY(125);
                //$this->Cell(0, 10, iconv('UTF-8', 'windows-1252', get_string("Es_wird_vom_Fachgebiet", "elediachecklist")), 0, 0, 'L');

                $this->SetFont('Arial', 'B', 10);
                $this->SetY(130);
                $this->Cell(0, 10, "Bemerkungen:", 0, 0, 'L');

                $this->SetFont('Arial', '', 9);
                $this->SetY(137);
                $this->MultiCell(0, 5, iconv('UTF-8', 'windows-1252',base64_decode($_COOKIE["commentsEA"])), 0, 'L', false);

                $this->SetY(160);
                $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', get_string("Place_pdf", "elediachecklist") . ", " . date('d.m.Y', time())), 0, 0, 'L');
            }
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            // Page number
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    //Cell with horizontal scaling if text is too wide
    function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
    {
        //Get string width
        $str_width=$this->GetStringWidth($txt);

        //Calculate ratio to fit cell
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $ratio = ($w-$this->cMargin*2)/$str_width;

        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit)
        {
            if ($scale)
            {
                //Calculate horizontal scaling
                $horiz_scale=$ratio*100.0;
                //Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
            }
            else
            {
                //Calculate character spacing in points
                $char_space=($w-$this->cMargin*2-$str_width)/max(strlen($txt)-1,1)*$this->k;
                //Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET',$char_space));
            }
            //Override user alignment (since text will fill up cell)
            $align='';
        }

        //Pass on to Cell method
        $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);

        //Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
    }

    //Cell with horizontal scaling only if necessary
    function CellFitScale($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,false);
    }

    //Cell with horizontal scaling always
    function CellFitScaleForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,true);
    }

    //Cell with character spacing only if necessary
    function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
    }

    //Cell with character spacing always
    function CellFitSpaceForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        //Same as calling CellFit directly
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,true);
    }
}

if ( !empty($_POST) ) {
    $cookie_name = "commentsEA";
    $cookie_value = $_POST["commentsEA"];
    setcookie($cookie_name, base64_encode($cookie_value), time() + (86400 * 30), "/"); // 86400 = 1 day

} else {

    $mysqli = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

//$display_heading = array('ischecked'=>'ID', 'displaytext'=> 'Item', 'is_checkbox'=> '');

    $examid = optional_param('examid', 0, PARAM_INT);
    $type = optional_param('type', '', PARAM_TEXT);

    $result = mysqli_query($mysqli, "SELECT CASE  WHEN mycheck.id_item IS NOT NULL THEN 'OK' ELSE '' END AS ischecked, myitem.displaytext as displaytext, myitem.is_checkbox as is_checkbox FROM mdl_elediachecklist_my_item myitem
LEFT JOIN mdl_elediachecklist_my_check mycheck ON  mycheck.id_item = myitem.id AND mycheck.id_exam=" . $examid .
        " WHERE myitem.type='" . $type . "' order by myitem.id") or die("database error:" . mysqli_error($mysqli));

//$header = mysqli_query($mysqli, "SHOW columns FROM mdl_elediachecklist_my_item");

    $pdf = new export_pdf('L');
//$pdf->setPageOrientation('l');
//header
    $pdf->AddPage();
//foter page
    $pdf->AliasNbPages();


    $mydozent = mysqli_query($mysqli, "SELECT dozent.firstname, dozent.lastname, exam.examtimestart FROM mdl_eledia_adminexamdates exam, mdl_user dozent
WHERE exam.id = " . $examid . " AND dozent.id = exam.examiner") or die("database error:" . mysqli_error($mysqli));
    $myrow = $mydozent->fetch_row();

//Klausur date
    $examdate = $myrow[2];
    $examdate = date('r', $myrow[2]);
//Get Topic Klausur
    $klausuritem = mysqli_query($mysqli, "SELECT duetime from mdl_elediachecklist_item where id = 18") or die("database error:" . mysqli_error($mysqli));
    $klausurrow = $klausuritem->fetch_row();

    //Get Exam name
    $examResult = mysqli_query($mysqli, "SELECT examname from mdl_eledia_adminexamdates where id = " . $examid) or die("database error:" . mysqli_error($mysqli));
    $examName = $examResult->fetch_row();

    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(80);
// Title
    $pdf->SetX(10);
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Klausur:') . ':  ' .$examName[0], 0, 0, 'L');

    $pdf->SetX(110);
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Klausurdatum:') . ': ' . date('d.m.y', strtotime($klausurrow[0] . ' day', strtotime($examdate))), 0, 0, 'L');

    $pdf->SetX(200);
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Dozent') . ': ' . iconv('UTF-8', 'windows-1252', $myrow[0] . " " . $myrow[1]), 0, 0, 'L');

// Line break
//$pdf->Ln(20);

    $pdf->SetFont('Arial', 'B', 10);

    /*foreach($header as $heading) {
        $pdf->Cell(40,12,$display_heading[$heading['Field']],1);
    }*/
//$pdf->Cell(10,12,'',1);
//$pdf->Cell(240,12,'Item',1);

    $mywidth = 10;
    $rownum=1;
    foreach ($result as $row) {
        $pdf->Ln();
        $mycell = 0;
        if ($row["is_checkbox"] == "0") {
            $pdf->SetFont('Arial', 'B', 10);
        } else {
            $pdf->SetFont('Arial', '', 9);
        }
        if ($row["is_checkbox"] == "0") {
            if ($mycell <= 1) $pdf->Cell(260, 6, iconv('UTF-8', 'windows-1252', $row["displaytext"]), 1);
        } else {
            foreach ($row as $column) {
                if ($mycell == 0) $mywidth = 10;
                if ($mycell == 1) $mywidth = 250;
                //if ($mycell == 0) $pdf->Cell($mywidth, 12, iconv('UTF-8', 'windows-1252', '✅'), 1);
                if ($mycell <= 1) $pdf->Cell($mywidth, 6, iconv('UTF-8', 'windows-1252', $column), 1, 0, '', false, '', 2);
                $mycell = $mycell + 1;
            }
        }

        /*if ($rownum == 10) {
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(260, 6, iconv('UTF-8', 'windows-1252', 'Bestätigungen'), 1, 0, '', false, '', 2);
        }*/
        $rownum = $rownum + 1;
    }
    if ($type == "qm") {
        $pdf->Output('Qualitatsmanagement_' . date("Ymd") . '.pdf', 'I');
    } else {
        $pdf->Output('Endabnahme_' . date("Ymd") . '.pdf', 'I');
    }
}