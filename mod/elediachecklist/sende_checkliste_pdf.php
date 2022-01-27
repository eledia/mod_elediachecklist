<?php

//include_once("connection.php");
include_once('libs/fpdf.php');

global $DB, $PAGE, $CFG, $USER;

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once($CFG->dirroot.'/mod/elediachecklist/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or.
$checklistid = optional_param('eledia', 0, PARAM_INT);  // Checklist instance ID.
$examid = optional_param('examid', 0, PARAM_INT);
$mailType = optional_param('mailType', "", PARAM_TEXT);
$contactpersonmail = optional_param('contactPersonMail', "", PARAM_EMAIL);

class PDF extends FPDF {

// Page header
    function Header()
    {
        // Logo
        //$this->Image('pix/logo.png',10,-1,70);
        $this->SetFont('Arial','B',11);
        // Move to the right
        $this->Cell(80);
        // Title
        $type = optional_param('type', '', PARAM_TEXT);
        $this->Cell(80, 10, iconv('UTF-8', 'windows-1252', 'Termincheckliste'), 1, 0, 'C');
        // Line break
        $this->Ln(20);
    }

// Page footer
    function Footer()
    {
    /*    // Arial italic 8
        $this->SetFont('Arial','I',8);

        if ($this->PageNo() == 2) {
            $this->SetY(-40);
            $this->Cell(0, 10, '______________________________________________________________________', 0, 0, 'C');
            $this->SetY(-35);
            $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', get_string("unterschrift_eklausurteam", "elediachecklist")), 0, 0, 'C');

            $this->SetY(-30);
            $this->Cell(0, 10, iconv('UTF-8', 'windows-1252', get_string("Place_pdf", "elediachecklist") . " " . date('d.m.Y', time())) , 0, 0, 'C');


            $this->SetY(-25);
            $this->Cell(0, 10, iconv('UTF-8', 'windows-1252',get_string("Es_wird_vom_Fachgebiet", "elediachecklist")), 0, 0, 'C');

        }
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');*/
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Page number
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
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

$mysqli = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

$examTopics = $DB->get_records("elediachecklist_item");
$checkedTopics = $DB->get_records("elediachecklist_check", ['teacherid' => $examid]);

$result = mysqli_query($mysqli, "SELECT * from mdl_eledia_adminexamdates exam where id =" . $examid) or die("database error:". mysqli_error($mysqli));

$examStart = 0;
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        $examStart = $row["examtimestart"];
    }
}

$pdf = new PDF('L');

//header
$pdf->AddPage();
//foter page
$pdf->AliasNbPages();
$pdf->SetFont('Arial','B',11);

//Column headers
$pdf->Ln();
$pdf->Cell(10, 12, iconv('UTF-8', 'windows-1252', ""), 1, 0, 'C', false, '', 2);
$pdf->Cell(220, 12, iconv('UTF-8', 'windows-1252', "Bezeichnung"), 1, 0, '', false, '', 2);
$pdf->Cell(40, 12, iconv('UTF-8', 'windows-1252',  "Datum"), 1, 0, 'C', false, '', 2);

$pdf->SetFont('Arial','',10);

foreach ($examTopics as &$topic) {

    $pdf->Ln();

    $topicDate = date('r', $examStart);
    $r = strtotime($examStart );
    $s =  strtotime('-1 day', strtotime($examStart ));
    $f= date('d.m.Y', strtotime('-1 day', strtotime($examStart )));

    $isChecked = "-";
    foreach ($checkedTopics as &$checked) {
        if ($checked->item == $topic->id)
            $isChecked = "X";
    }

    $pdf->Cell(10, 12, iconv('UTF-8', 'windows-1252', $isChecked), 1, 0, 'C', false, '', 2);
    $pdf->Cell(220, 12, iconv('UTF-8', 'windows-1252', $topic->displaytext), 1, 0, '', false, '', 2);
    //$pdf->Cell(60, 12, iconv('UTF-8', 'windows-1252', $topic->duetime), 1, 0, 'C', false, '', 2);

    if ($topic->displaytext == "Endabnahme") {
        $eaDate = $DB->get_record("elediachecklist_item_date", ['examid' => $examid]);
        $pdf->Cell(40, 12, iconv('UTF-8', 'windows-1252', date("d.m.Y", strtotime($eaDate->checkdate))), 1, 0, 'C', false, '', 2);
    } else {
        $pdf->Cell(40, 12, iconv('UTF-8', 'windows-1252', date('d.m.Y', strtotime($topic->duetime . ' day', strtotime($topicDate)))), 1, 0, 'C', false, '', 2);
    }
}
$pdfName = 'Termincheckliste_' . date("Ymd") . '.pdf';
$pdf->Output($pdfName, 'F');

$checks = $DB->get_records_sql("SELECT distinct REPLACE(myitem.emailtext, '{Datum}', DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM_UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y'))  as displaytext, myitem.duetime, 
DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM_UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y') AS newdate, DATE_FORMAT(FROM_UNIXTIME(exam.examtimestart),'%d.%m.%Y') AS examDate,
exam.examname FROM {elediachecklist_item} myitem
INNER JOIN {eledia_adminexamdates} exam ON exam.id = ?
WHERE myitem.id NOT IN (SELECT mycheck.item FROM {elediachecklist_check} mycheck  where mycheck.teacherid=?)
ORDER BY myitem.duetime", ['examid' => $examid, 'examid_' => $examid]);

$checksInMail = "";
$examDate = "";
$bezeichnung = "";
foreach ($checks as $item) {
    $checksInMail = $checksInMail . $item->displaytext . "<br/>";
    $examDate = $item->examdate;
    $bezeichnung = $item->examname;
}

$subject = get_string("checkliste_mail_subject", "elediachecklist");
$message = get_string("checkliste_mail_text", "elediachecklist" );
$message = str_replace("{Datum}", $examDate, $message);
$message = str_replace("{BEZEICHNUNG}", $bezeichnung, $message);
email_to_user(core_user::get_user_by_email($contactpersonmail), $CFG->noreplyaddress, $subject, $message, $message, $pdfName, $pdfName );

echo "Checkliste SENT to " . $contactpersonmail;













?>