<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/tcpdf/tcpdf.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
global $CFG, $DB;

class myPDF extends TCPDF {

    //Page header
    public function Header() {
        //$this->SetFont('helvetica', 'B', 11);
        // Move to the right
        //$this->Cell(60);
        // Title
        //$type = optional_param('type', '', PARAM_TEXT);
        //$this->Cell(60, 10, iconv('UTF-8', 'windows-1252', 'Termincheckliste'), 1, 0, 'C');
        // Line break
        //$this->Ln(20);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 11);
        // Page number
        $txt = 'Seite '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
        $this->Cell(0, 10, $txt, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}

/*
2022-08-26, ng
                                                    SIEHE: install.php
PDF (ist, 17 Punkte)                                {elediachecklist_item}.id
--------------------                                -------------------------
A Ersteller: innenzugriff vorhanden                  1
B Bereitstellung der Termincheckliste                2
C Prüfungskonfiguration beschrieben                  3
D Prüfungsimage vorbereitet                          4
E Funktionstest der Klausur durchgeführt             5
F Qualitätskontrolle durchgeführt                    6
G Endabnahme                                         7
H HIS-Liste an E-Klausur-Team                        8
I Gruppeneinteilung abgeschlossen                    9
J Namen der Aufsichtspersonen                       10
  Importlisten vorbereitet                          11 // ??? Wird NICHT auf der Weboberflaeche ausgegeben!
K TN-Import und Etikettenerstellungexam date        12
L Problemfälle bearbeitet                           13
M Mitteilung über abgeschlossene Klausureinsicht    15 // !!! ID 14 fehlt!?
N Zweitarchivierung und –signierung bei Änderungen  16
O Notenschlüssel bereitgestellt                     17
P Klausur abgeschlossen                             18

PDF (soll, 9 Punkte)
--------------------
01 = (C ->  3) Beschreibung der Prüfungskonfiguration
02 = (E ->  5) Fertigstellung und Funktionstest der Klausur
03 = (F ->  6) Bereitstellung der Klausur für Anpassungen und Qualitätskontrolle
04 = (G ->  7) Endabnahme E-Klausur
05 = (H ->  8) HIS-Liste an E-Klausurteam senden
06 = (I ->  9) Gruppeneinteilung mit Namen und Matrikelnummer
07 = (J -> 10) Namen und Anzahl der Aufsichtspersonen, Studierendeninformationen
08 = (M -> 15) Mitteilung an E-Klausurteam: Klausureinsicht und Korrekturen abgeschlossen
09 = (O -> 17) Notenschlüssel bereitgestellt

*/

/**
 * @param int $elediachecklist_item_id
 * @return string
 */
function text_of_id($elediachecklist_item_id) {

    switch($elediachecklist_item_id) {

        // 01 = (C ->  3) Beschreibung der Prüfungskonfiguration
        case 3:
            $txt = get_string('text_pdf_01', 'elediachecklist');
            break;

        // 02 = (E ->  5) Fertigstellung und Funktionstest der Klausur
        case 5:
            $txt = get_string('text_pdf_02', 'elediachecklist');
            break;

        // 03 = (F ->  6) Bereitstellung der Klausur für Anpassungen und Qualitätskontrolle
        case 6:
            $txt = get_string('text_pdf_03', 'elediachecklist');
            break;

        // 04 = (G ->  7) Endabnahme E-Klausur
        case 7:
            $txt = get_string('text_pdf_04', 'elediachecklist');
            break;

        // 05 = (H ->  8) HIS-Liste an E-Klausurteam senden
        case 8:
            $txt = get_string('text_pdf_05', 'elediachecklist');
            break;

        // 06 = (I ->  9) Gruppeneinteilung mit Namen und Matrikelnummer
        case 9:
            $txt = get_string('text_pdf_06', 'elediachecklist');
            break;

        // 07 = (J -> 10) Namen und Anzahl der Aufsichtspersonen, Studierendeninformationen
        case 10:
            $txt = get_string('text_pdf_07', 'elediachecklist');
            break;

        // 08 = (M -> 15) Mitteilung an E-Klausurteam: Klausureinsicht und Korrekturen abgeschlossen
        case 15:
            $txt = get_string('text_pdf_08', 'elediachecklist');
            break;

        // 09 = (O -> 17) Notenschlüssel bereitgestellt
        case 17:
            $txt = get_string('text_pdf_09', 'elediachecklist');
            break;

        default:
            $txt = '';
            break;
    }

    return $txt;
}


$examid = optional_param('examid', 0, PARAM_INT);

$examTopics = $DB->get_records("elediachecklist_item");
$checkedTopics = $DB->get_records("elediachecklist_check", ['teacherid' => $examid]);
//echo '<pre>'.print_r($examTopics, true).'</pre>'; die();
//echo '<pre>'.print_r($checkedTopics, true).'</pre>'; //die();

// Startwerte
$properties = array(
    'examiner'       => '',      // Dozent                      //.
    'examiners'      => array(), // Dozenten                    //.
    'examname'       => '',      // Bezeichnung Klausur         //.
    'examtimestart'  => '',      // Klausurtermin (d.m.Y)       //.
    'numberstudents' => '',      // Erwartete Anzahl Prueflinge //.
    'scl_name'       => '',      // Name SCL Betreuer           //.
    'duration'       => '',      // Zeitraum der Raumbuchung    //.
);

$sql = "SELECT * from {eledia_adminexamdates} exam where id =" . $examid;
$result = $DB->get_records_sql($sql);
$examStart = 0;
foreach($result as $one) {

    $examStart = $one->examtimestart;

    $examiners = array();
    $examiner = '';
    // Dozenten
    if(!is_numeric($one->examiner)) {
        $sql = "SELECT id, firstname, lastname FROM {user} WHERE id IN (".$one->examiner.")";
        $res = $DB->get_records_sql($sql);
        foreach($res as $r) {
            $examiners[] = trim($r->firstname . ' ' . $r->lastname);
        }
        if(count($examiners) > 0) {
            $examiner = $examiners[0];
        }
    }
    // Dozent
    else {
        $sql = "SELECT id, firstname, lastname FROM {user} WHERE id = " . $one->examiner;
        $res = $DB->get_records_sql($sql);
        if (isset($res) && is_array($res) && count($res) > 0) {
            $val = array_shift($res);
            $examiner = trim($val->firstname . ' ' . $val->lastname);
            $examiners = array(trim($val->firstname . ' ' . $val->lastname));
        }
    }

    // SCL-Verantwortlicher
    $sclname = '';
    $sql = "SELECT id, firstname, lastname FROM {user} WHERE id = ".$one->responsibleperson;
    $res = $DB->get_records_sql($sql);
    if(isset($res)  &&  is_array($res)  &&  count($res) > 0) {
        $val = array_shift($res);
        $sclname = trim($val->firstname.' '.$val->lastname);
    }

    $properties['examiner']       = $examiner;
    $properties['examiners']      = $examiners;
    $properties['examname']       = $one->examname;
    $properties['examtimestart']  = date('d.m.Y', $one->examtimestart);
    $properties['numberstudents'] = $one->numberstudents;
    $properties['scl_name']       = $sclname;
    $properties['duration']       = date('H:i', $one->examtimestart).' - '.date('H:i', ($one->examtimestart + ($one->examduration*60)));
}

//echo '<pre>'.print_r($properties, true).'</pre>'; die();
//echo '<pre>'.print_r($result, true).'</pre>'; //die();

//============================================================================

// create new PDF document
$pdf = new myPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
//$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set color for background // white
$pdf->SetFillColor(255, 255, 255);
// set color for text // black
$pdf->SetTextColor(0, 0, 0);

// add a page
$pdf->AddPage();


$pdf->SetFont('helvetica', 'B', 18);

$html  = get_string('ueberschrift_01', 'elediachecklist');
$html .= '<br />';
$pdf->writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=true, $align='');


$pdf->SetFont('helvetica', '', 11);

$html  = '';
$html.= '<table border="1" cellpadding="0" cellspacing="0"><tr><td>';
$html .= '<table border="0" cellpadding="4" cellspacing="0" width="100%">';
$html .= '<tr>';
$html .= '<td align="left" width="100%">'.get_string('dozent', 'elediachecklist').': '.implode(', ', $properties['examiners']).'</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="left" width="100%">'.get_string('bezeichnung_klausur', 'elediachecklist').': '.$properties['examname'].'</td>';
$html .= '</tr>';
$html .= '</table>';
$html .= '</td></tr></table>';
$html .= '<br />';
$pdf->writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=true, $align='');


$html  = '';
$html .= '<table border="0" cellpadding="2" cellspacing="0" width="100%">';
$html .= '<tr>';
$html .= '<td align="left" width="25%">'.get_string('klausurtermin', 'elediachecklist').':</td>';
$html .= '<td align="left" width="30%">'.$properties['examtimestart'].'</td>';
$html .= '<td align="left" width="30%">'.get_string('erwartetet_anzahl_prueflinge', 'elediachecklist').':</td>';
$html .= '<td align="left" width="15%">'.$properties['numberstudents'].'</td>';
//$html .= '<td align="left" width="10%">&nbsp;</td>';
$html .= '</tr>';
$html .= '<tr>';
$html .= '<td align="left" width="25%">'.get_string('name_scl_betreuer', 'elediachecklist').':</td>';
$html .= '<td align="left" width="30%">'.$properties['scl_name'].'</td>';
$html .= '<td align="left" width="30%">'.get_string('zeitraum_der_raumbuchung', 'elediachecklist').':</td>';
$html .= '<td align="left" width="15%">'.$properties['duration'].'</td>';
//$html .= '<td align="left" width="10%">&nbsp;</td>';
$html .= '</tr>';
$html .= '</table>';
$html .= '<br /><br /><br />';
$pdf->writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=true, $align='');


$html  = get_string('text_pdf_intro', 'elediachecklist');
$html .= '<br />';
$pdf->writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=true, $align='');



$html  = '';
$html .= '<table border="1" cellpadding="6" cellspacing="0" width="100%">';
$html .= '<tr>';
$html .= '<td align="center" width="5%">&nbsp;</td>';
$html .= '<td align="left" width="81%">Bezeichnung</td>';
$html .= '<td align="center" width="14%">Datum</td>';
$html .= '</tr>';
foreach ($examTopics as &$topic) {

    $txt_of_id = text_of_id($topic->id);
    if(trim($txt_of_id) == '') {
        continue;
    }

    $topicDate = date('r', $examStart);
    $r = strtotime($examStart );
    $s =  strtotime('-1 day', strtotime($examStart ));
    $f= date('d.m.Y', strtotime('-1 day', strtotime($examStart )));

    $isChecked = "-";
    foreach ($checkedTopics as &$checked) {
        if ($checked->item == $topic->id)
            $isChecked = "X";
    }

    $second = $txt_of_id;
    //$second = iconv('UTF-8', 'windows-1252', $second);

    $third = '-';
    if($examid > 0) {
        if ($topic->displaytext == "Endabnahme") {
            $eaDate = $DB->get_record("elediachecklist_item_date", ['examid' => $examid]);
            if (isset($eaDate->checkdate)) {
                $date = date("d.m.Y", $eaDate->checkdate);
                // Holiday to this day? //.
                $isholiday = false;

                // ERLEDIGT & AUSKOMMENTIERT
                //$mixed = elediachecklist_is_holiday($date);
                //if(is_array($mixed)) {
                //    $isholiday = true;
                //    // Next workday //.
                //    $date = elediachecklist_get_next_workday_after_holiday($date);
                //    $date = '<i>'.$date.'</i>';
                //}

                $third = $date;
            }
        } else {
            if (isset($topic->duetime)) {
                $date = date('d.m.Y', strtotime($topic->duetime . ' day', strtotime($topicDate)));
                // Holiday to this day? //.
                $isholiday = false;

                // ERLEDIGT & AUSKOMMENTIERT
                //$mixed = elediachecklist_is_holiday($date);
                //if(is_array($mixed)) {
                //    $isholiday = true;
                //    // Next workday //.
                //    $date = elediachecklist_get_next_workday_after_holiday($date);
                //    $date = '<i>'.$date.'</i>';
                //}

                $third = $date;
            }
        }
    }

    $html .= '<tr>';
    $html .= '<td align="center" width="5%">'.$isChecked.'</td>';
    $html .= '<td align="left" width="81%">'.$second.'</td>';
    $html .= '<td align="center" width="14%">'.$third.'</td>';
    $html .= '</tr>';
}
$html .= '</table>';
$pdf->writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=true, $align='');

//Close and output PDF document
$pdf->Output('Termincheckliste_' . date("Ymd") . '.pdf', 'I');
