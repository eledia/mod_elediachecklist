<?php

//include_once("connection.php");
include_once('libs/fpdf.php');
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
global $CFG, $DB;

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

        // ALT
        $this->Ln(20);
        // NEU
        //$this->Ln(14);
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
        //----- Qualitaetsmanagement //.
        if ($type == "qm") {
            // Position at 1.5 cm from bottom
            $this->SetY(-15);
            // Arial italic 8
            $this->SetFont('Arial','I',7);
            // Page number
            $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
        }
        //----- Endabnahme //.
        else {
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

                // Bemerkungen, String //.
                $this->SetFont('Arial', 'B', 10);
                // ALT
                //$this->SetY(130);
                // NEU
                $this->SetY(136);
                $this->Cell(0, 10, "Bemerkungen:", 0, 0, 'L');

                // Bemerkungen, Inhalt //.
                $this->SetFont('Arial', '', 9);
                // ALT
                //$this->SetY(137);
                // NEU
                $this->SetY(143);
                $inp = '';
                if(isset($_REQUEST['commentsEA'])) {
                    //$inp = base64_decode($_REQUEST["commentsEA"]);
                    $inp = $_REQUEST["commentsEA"];
                }
                else if(isset($_COOKIE['commentsEA'])) {
                    //$inp = base64_decode($_COOKIE["commentsEA"]);
                    $inp = $_COOKIE["commentsEA"];
                }
                $this->MultiCell(0, 5, iconv('UTF-8', 'windows-1252', $inp), 0, 'L', false);

                // Datum
                // ALT
                //$this->SetY(160);
                // NEU
                $this->SetY(171);
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


if (isset($_REQUEST['commentsEA']) ) {
    $cookie_name = "commentsEA";
    $cookie_value = $_REQUEST["commentsEA"];
    //setcookie($cookie_name, base64_encode($cookie_value), time() + (86400 * 30), "/"); // 86400 = 1 day
}


    $examid = optional_param('examid', 0, PARAM_INT);
    $type = optional_param('type', '', PARAM_TEXT);

    // ACHTUNG: {elediachecklist_my_check} hat aus irgendwelchen Gruenden(!) manchmal dieselben Eintraege,
    // so dass der 'LEFT JOIN nicht immer funktioniert'.
    // Moodle beschwert sich:
    // Did you remember to make the first column something unique in your call to get_records? Duplicate value '49' found in column 'id'.
    // Erste Massnahme: Alle Checkboxen deaktivieren/aktivieren, 2022-09-23, ngeiges
    $sql  = "SELECT ";
    $sql .= "myitem.id, ";
    $sql .= "CASE  WHEN mycheck.id_item IS NOT NULL THEN 'OK' ELSE '' END AS ischecked, ";
    $sql .= "myitem.displaytext AS displaytext, ";
    $sql .= "myitem.is_checkbox AS is_checkbox ";
    $sql .= "FROM mdl_elediachecklist_my_item AS myitem ";
    $sql .= "LEFT JOIN mdl_elediachecklist_my_check AS mycheck ON (mycheck.id_item = myitem.id AND mycheck.id_exam=" . $examid .") ";
    $sql .= "WHERE myitem.type='" . $type . "' ";
    $sql .= "ORDER BY myitem.id ";
    $result = $DB->get_records_sql($sql);
    //echo '<pre>'.print_r($result, true).'</pre>'; die();
    //echo $sql.'<br /><br />>';
    //echo '<pre>'.print_r($result, true).'</pre>';
    //echo 'SQL 1'; die();

    $buf = array();
    foreach($result as $one) {
        $new = new stdClass();
        $new->ischecked = $one->ischecked;
        $new->displaytext = $one->displaytext;
        $new->is_checkbox = $one->is_checkbox;
        $buf[] = $new;
    }
    $result = $buf;

    $pdf = new export_pdf('L');
    //$pdf->setPageOrientation('l');
    //header
    $pdf->AddPage();
    //foter page
    $pdf->AliasNbPages();

    // $myrow
    $sql  = "SELECT ";
    $sql .= "exam.id, exam.examiner, ";
    //$sql .= "dozent.firstname, dozent.lastname, ";
    $sql .= "exam.examtimestart ";
    $sql .= "FROM {eledia_adminexamdates} AS exam ";//, {user} AS dozent ";
    $sql .= "WHERE exam.id = ".$examid." ";
    //$sql .=   "AND dozent.id = exam.examiner ";
    $exams = $DB->get_records_sql($sql);
    //echo '<pre>'.print_r($exams, true).'</pre>';
    // Start
    $myrow = array(
            0 => '', // firstname
            1 => '', // lastname
            2 => 0,  // tp, examtimestart
    );
    foreach($exams as $exam) {
        $sql = "SELECT id, firstname, lastname FROM {user} WHERE id IN (".$exam->examiner.")";
        $res = $DB->get_records_sql($sql);
        if(count($res) > 0) {
            $dozent = array_shift($res);
            $myrow = array(
                0 => $dozent->firstname,
                1 => $dozent->lastname,
                2 => $exam->examtimestart,
            );
            break;
        }
    }
    //echo '<pre>'.print_r($myrow, true).'</pre>';

    $arrdozent = array();
    if(isset($exam->examiner)  &&  trim($exam->examiner) != '') {
        $sql = "SELECT id, firstname, lastname FROM {user} WHERE id IN (" . $exam->examiner . ")";
        $res = $DB->get_records_sql($sql);
        foreach ($res as $one) {
            $name = trim($one->firstname . ' ' . $one->lastname);
            $arrdozent[] = $name;
        }
        sort($arrdozent, SORT_NATURAL);
    }
    //echo '<pre>'.print_r($arrdozent, true).'</pre>';

    //Klausur date
    $examdate = $myrow[2];
    $examdate = date('r', $myrow[2]);
    //Get Topic Klausur
    // $klausurrow -> $klausurrow[0]
    $sql = "SELECT id, duetime from mdl_elediachecklist_item where id = 18";
    $res = $DB->get_records_sql($sql);
    $klausurrow = array(0 => '');
    if(isset($res)  &&  count($res) == 1) {
        $one = array_shift($res);
        $klausurrow = array(0 => $one->duetime);
    }
    //echo '<pre>'.print_r($klausurrow, true).'</pre>';

    //Get Exam name
    // $examName -> $examName[0]
    $sql = "SELECT id, examname from {eledia_adminexamdates} where id = " . $examid;
    $res = $DB->get_records_sql($sql);
    $examName = array(0 => '');
    if(isset($res)  &&  count($res) == 1) {
        $one = array_shift($res);
        $examName = array(0 => $one->examname);
    }
    //echo '<pre>'.print_r($examName, true).'</pre>'; die();

    $pdf->SetFont('Arial', '', 9);

    // Klausur //.
    $txt = iconv('UTF-8', 'windows-1252', 'Klausur:') . ':  ' .iconv('UTF-8', 'windows-1252', $examName[0]);
    $pdf->Cell(0, 10, $txt, 0, 0, 'L');
    $pdf->Ln(5);

    // Klausurdatum //.
    $pdf->SetX(10);
    $inp = iconv('UTF-8', 'windows-1252', 'Klausurdatum:') . ': ';
    if (isset($myrow[2]) && $myrow[2] != 0) {
        //$inp .= date('d.m.Y', strtotime($klausurrow[0] . ' day', strtotime($examdate)));
        $inp .= date('d.m.Y', $myrow[2]);
    }
    $pdf->Cell(0, 10, $inp, 0, 0, 'L');
    $pdf->Ln(5);

    // Dozent //.
    $inp = implode(', ', $arrdozent);
    $pdf->SetX(10);
    $pdf->Cell(0, 10, iconv('UTF-8', 'windows-1252', 'Dozent') . ': ' . iconv('UTF-8', 'windows-1252', $inp), 0, 0, 'L');

    // Line break
    //$pdf->Ln(20);

    $pdf->SetFont('Arial', 'B', 10);


    $mywidth = 10;
    $rownum = 1;

    foreach ($result as $row) {

        $pdf->Ln();
        $mycell = 0;

        if ($row->is_checkbox == "0") {
            $pdf->SetFont('Arial', 'B', 10);
        } else {
            $pdf->SetFont('Arial', '', 9);
        }

        //if ($row["is_checkbox"] == "0") {
        if ($row->is_checkbox == "0") {
            if ($mycell <= 1) $pdf->Cell(260, 6, iconv('UTF-8', 'windows-1252', $row->displaytext), 1);
        } else {
            foreach ($row as $column) {
                if ($mycell == 0) $mywidth = 10;
                if ($mycell == 1) $mywidth = 250;
                if ($mycell <= 1) $pdf->Cell($mywidth, 6, iconv('UTF-8', 'windows-1252', $column), 1, 0, '', false, '', 2);
                $mycell = $mycell + 1;
            }
        }

        $rownum = $rownum + 1;
    }


    if ($type == "qm") {
        $pdf->Output('Qualitatsmanagement_' . date("Ymd") . '.pdf', 'I');
    } else {
        $pdf->Output('Endabnahme_' . date("Ymd") . '.pdf', 'I');
    }
