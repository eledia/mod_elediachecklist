<?php

//include_once('libs/fpdf.php');

global $DB, $PAGE, $CFG, $USER;

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/tcpdf/tcpdf.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot.'/mod/elediachecklist/lib.php');

// Button: Sende Checkliste //.

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or.
$checklistid = optional_param('eledia', 0, PARAM_INT);  // Checklist instance ID.
$examid = optional_param('examid', 0, PARAM_INT);
$mailType = optional_param('mailType', "", PARAM_TEXT);
$contactpersonmail = optional_param('contactPersonMail', "", PARAM_EMAIL);
$extraEmail = optional_param('extraEmail', "", PARAM_EMAIL);

class PDF extends TCPDF {

    function Header()
    {
    }


    function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 11);
        // Page number
        $txt = 'Seite '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
        $this->Cell(0, 10, $txt, 0, false, 'C', 0, '', 0, false, 'T', 'M');
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


// DATEN //.

$tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
$sql = "SELECT * FROM {".$tab."} ORDER BY duetime ASC, displaytext ASC";
$examTopics = $DB->get_records_sql($sql);


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

$tab = elediachecklist_tab('eledia_adminexamdates_chk'); // elediachecklist__check
$checkedTopics = $DB->get_records($tab, ['teacherid' => $examid]);


$sql = "SELECT * from {eledia_adminexamdates} exam where id =" . $examid;
$result = $DB->get_records_sql($sql);
$examStart = 0;
//foreach($result as $one) {
//    $examStart = $one->examtimestart;
//}
foreach($result as $one) {

    $examStart = $one->examtimestart;

    $examiners = array();
    $examiner = '';
    // Dozenten //.
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
    // Dozent //.
    else {
        $sql = "SELECT id, firstname, lastname FROM {user} WHERE id = " . $one->examiner;
        $res = $DB->get_records_sql($sql);
        if (isset($res) && is_array($res) && count($res) > 0) {
            $val = array_shift($res);
            $examiner = trim($val->firstname . ' ' . $val->lastname);
            $examiners = array(trim($val->firstname . ' ' . $val->lastname));
        }
    }

    // SCL-Verantwortlicher //.
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

// Wenn kein SCL-Verantwortlicher vorhanden ist, keine E-Mail raus!
if(!isset($properties['scl_name'])  ||  trim($properties['scl_name']) == '') {
    $str = get_string('kein_scl_verantwortlicher_genannt', "elediachecklist");
    echo $str;
    exit();
}

// PDF //.

$pdf = new PDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

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

    $txt_of_id = txt_of_id($topic->id);
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
            $tab = elediachecklist_tab('eledia_adminexamdates_itm_d'); // elediachecklist__item_date
            $eaDate = $DB->get_record($tab, ['examid' => $examid]);
            if (isset($eaDate->checkdate)) {
                $date = date("d.m.Y", $eaDate->checkdate);
                // Holiday to this day? //.
                $isholiday = false;

                // ERLEDIGT & AUSKOMMENTIERT
                $mixed = elediachecklist_is_holiday($date);
                if(is_array($mixed)) {
                    $isholiday = true;
                    // Next workday //.
                    $date = elediachecklist_get_next_workday_after_holiday($date);
                    //$date = '<i>'.$date.'</i>';
                }

                $third = $date;
            }
        } else {
            if (isset($topic->duetime)) {
                $date = date('d.m.Y', strtotime($topic->duetime . ' day', strtotime($topicDate)));
                // Holiday to this day? //.
                $isholiday = false;

                // ERLEDIGT & AUSKOMMENTIERT
                $mixed = elediachecklist_is_holiday($date);
                if(is_array($mixed)) {
                    $isholiday = true;
                    // Next workday //.
                    $date = elediachecklist_get_next_workday_after_holiday($date);
                    //$date = '<i>'.$date.'</i>';
                }

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
//$pdf->Output('Termincheckliste_' . date("Ymd") . '.pdf', 'I');
$pdfName = 'Termincheckliste_' . date("Ymd") . '.pdf';
$pdf->Output($CFG->dataroot . "/" . $pdfName, 'F');



// E-MAIL-VERSAND //.

$tab1 = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
$tab2 = elediachecklist_tab('eledia_adminexamdates_chk'); // elediachecklist__check

$sql  = "SELECT ";
$sql .= "myitem.id, ";
$sql .= "myitem.emailtext, ";
$sql .= "myitem.duetime, ";
$sql .= "exam.examtimestart, ";
$sql .= "exam.examname ";
$sql .= "FROM {".$tab1."} AS myitem ";
$sql .= "INNER JOIN {eledia_adminexamdates} exam ON exam.id = ? ";
$sql .= "WHERE myitem.id NOT IN (SELECT mycheck.item FROM {".$tab2."} AS mycheck WHERE mycheck.teacherid=?) ";
$sql .= "ORDER BY myitem.duetime ";
$checks = $DB->get_records_sql($sql, ['examid' => $examid, 'examid_' => $examid]);
// Ich wuerde sagen der 2. Parameter ('examid_' => $examid) ist falsch.
// Im SQL wird ja das dann zu: mycheck.teacherid= $examid
// Aber solange ich es nicht besser weiss, aendere ich hier nichts.
// ng, 2022-09-02

$buf = array();
foreach($checks as $check) {

    // displaytext //.
    $tp = $check->examtimestart + (60 * 60 * 24 * $check->duetime);
    $date = date('d.m.Y', $tp);
    // Holiday to this day? //.
    $isholiday = false;

    // ERLEDIGT & AUSKOMMENTIERT
    $mixed = elediachecklist_is_holiday($date);
    if(is_array($mixed)) {
        $isholiday = true;
        // Next workday //.
        $date = elediachecklist_get_next_workday_after_holiday($date);
        //$date = $date.'_';
    }

    $displaytext = str_replace('{Datum}', $date, $check->emailtext);

    // newdate //.
    $tp = $check->examtimestart + (60 * 60 * 24 * $check->duetime);
    $newdate = date('d.m.Y', $tp);
    // Holiday to this day? //.
    $isholiday = false;

    // ERLEDIGT & AUSKOMMENTIERT
    $mixed = elediachecklist_is_holiday($newdate);
    if(is_array($mixed)) {
        $isholiday = true;
        // Next workday //.
        $newdate = elediachecklist_get_next_workday_after_holiday($newdate);
        //$newdate = $newdate.'-';
    }

    // examDate //.
    $tp = $check->examtimestart;
    $examDate = date('d.m.Y', $tp);
    // Der Tag kommt aus der Tabelle 'eledia_adminexamdates' //.
    // Hier keine Ueberpruefung auf 'Holiday'                //.

    $check->displaytext = $displaytext;
    $check->newdate     = $newdate;
    $check->examdate    = $examDate;
    $buf[] = $check;
}
$checks = $buf;

//echo '<pre>'.print_r($checks, true).'</pre>'; die();

$context = context_system::instance();
$PAGE->set_context($context);

$checksInMail = "";
$examDate = "";
$bezeichnung = "";
foreach ($checks as $item) {
    $checksInMail = $checksInMail . $item->displaytext . "<br/>";
    $examDate = $item->examdate;
    $bezeichnung = $item->examname;
}

// IMMER:
// An $contactpersonmail -> Der Ansprechpartner (s. module.js)
$subject = get_string("checkliste_mail_subject", "elediachecklist");
$message = get_string("checkliste_mail_text", "elediachecklist" );
$message = str_replace("{Datum}",       $examDate,    $message);
$message = str_replace("{BEZEICHNUNG}", $bezeichnung, $message);
email_to_user(core_user::get_user_by_email($contactpersonmail), $CFG->noreplyaddress, $subject, $message, $message, $CFG->dataroot . "/" . $pdfName, $pdfName );

//unset($coreuser);
//if ($extraEmail != null  &&  $extraEmail != ""  &&  filter_var($extraEmail, FILTER_VALIDATE_EMAIL) ) {
//    $coreuser = core_user::get_user_by_email($extraEmail);
//    //echo 'gettype = '.gettype($coreuser).'<br />';
//    //echo '<pre>'.print_r($coreuser, true).'</pre>';
//}

//if ($extraEmail != null && $extraEmail != ""  &&  isset($coreuser)  &&  $coreuser !== false) {
if ($extraEmail != null  &&  $extraEmail != ""  &&  filter_var($extraEmail, FILTER_VALIDATE_EMAIL) ) {
    // OPTIONAL:An $extraMail -> Muss als Nutzer vorhanden sein

    $userextra = core_user::get_user_by_email($extraEmail);
    if(!$userextra) {
        $userextra = new stdClass();
        $userextra->id = guest_user()->id;
        $userextra->lang = current_language();
        $userextra->firstaccess = time();
        $userextra->mnethostid = $CFG->mnet_localhost_id;
        $userextra->email = $extraEmail;
        $userextra->username = 'Guest';
        $userextra->mailformat = 1;
    }

    $message = get_string("checkliste_mail_text", "elediachecklist" );
    $message = str_replace("{Datum}",       $examDate,    $message);
    $message = str_replace("{BEZEICHNUNG}", $bezeichnung, $message);
    //email_to_user(core_user::get_user_by_email($extraEmail), $CFG->noreplyaddress, $subject, $message, $message, $CFG->dataroot . "/" . $pdfName, $pdfName);
    email_to_user($userextra, $CFG->noreplyaddress, $subject, $message, $message, $CFG->dataroot . "/" . $pdfName, $pdfName);
    echo "Checkliste SENT to " . $contactpersonmail . " and " . $extraEmail;
} else {
    echo "Checkliste SENT to " . $contactpersonmail;
}
