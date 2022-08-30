<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/lib/tcpdf/tcpdf.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
global $CFG, $DB;

class myPDF extends TCPDF {

    //Page header
    public function Header() {
        $this->SetFont('helvetica', 'B', 11);
        // Move to the right
        $this->Cell(60);
        // Title
        //$type = optional_param('type', '', PARAM_TEXT);
        $this->Cell(60, 10, iconv('UTF-8', 'windows-1252', 'Termincheckliste'), 1, 0, 'C');
        // Line break
        $this->Ln(20);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', '', 11);
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
            $txt  = '';
            $txt .= '<b>Beschreibung der Prüfungskonfiguration</b><br /><br />';
            $txt .= 'Teilen Sie uns bitte die gewünschte Prüfungskonfiguration mit. Z.B.';
            $txt .= '<ul style="">';
            $txt .= '<li>Sie benötigen einen PDF-Reader</li>';
            $txt .= '<li>Sie möchten den Studierenden am Bildschirm einen kleinen Taschenrechner zur Verfügung stellen.</li>';
            $txt .= '<li>Sie möchten, dass Ihre Studierenden während der Klausur eine weitere Anwendung nutzen.</li>';
            $txt .= '</ul>';
            $txt .= 'Hinweis! Je früher Sie uns Ihren Konfigurationswunsch mitteilen, desto größer ist die Chance, dass wir diesen umsetzen können.';
            break;

        // 02 = (E ->  5) Fertigstellung und Funktionstest der Klausur
        case 5:
            $txt  = '';
            $txt .= '<b>Fertigstellung und Funktionstest der Klausur</b><br /><br />';
            $txt .= 'Prüfen Sie Ihre Fragen auf folgende Eigenschaften:';
            $txt .= '<ul style="">';
            $txt .= '<li>Die Summe der Punkte bei einer Aufgabe darf kein negativer Wert möglich sein. Dies ist prüfungsrechtlich nicht zulässig.</li>';
            $txt .= '<li>In Word erstellte Textbausteine wurden vor dem Einfügen in eine Frage oder ein Assessment von dem in Word automatisch vorhandenen Quellcode befreit. Der Quellcode könnte die Funktion der Fragen unerwartet beeinflussen.</li>';
            $txt .= '<li>Alle Fragen wurden auf eine korrekte Punkteberechnung hin geprüft.</li>';
            $txt .= '</ul>';
            $txt .= 'Bevor Sie Ihre Klausur zur Qualitätskontrolle dem E-Klausur-Team bereitstellen, ist ein interner Funktionstest obligatorisch, um Fehler oder fehlende Funktionalitäten frühzeitig zu erkennen.';
            $txt .= '<ul style="">';
            $txt .= '<li>Generieren Sie ein "richtiges Prüfungsergebnis". Eine Anleitung finden Sie in den Schulungsunterlagen auf dem Prüfungssystem.</li>';
            $txt .= '<li>Bei <b>Zufallsauswahl</b> von Fragen ist zusätzlich eine Testklausur mit allen Fragen zu erstellen und zu erproben.</li>';
            $txt .= '<li>Wenn Sie beim Funktionstest Unterstützung wünschen, wenden Sie sich bitte an das E-Klausur-Team.</li>';
            $txt .= '</ul>';
            $txt .= 'Bei der Endabnahme bestätigen Sie, dass Sie die o. g. Punkte geprüft haben.';
            break;

        // 03 = (F ->  6) Bereitstellung der Klausur für Anpassungen und Qualitätskontrolle
        case 6:
            $txt  = '';
            $txt .= '<b>Bereitstellung der Klausur für Anpassungen und Qualitätskontrolle</b><br /><br />';
            $txt .= 'An jeder Klausur nehmen wir einige manuelle Anpassungen vor:<br />';
            $txt .= 'Aktivierung einer manipulationssicheren Prüfungsumgebung, Sicherheitseinstellung für den Fall einer Prüfungsunterbrechung u.a.m.<br />';
            $txt .= 'Außerdem überprüfen wir Ihre Klausur hinsichtlich der Funktionalität. ';
            $txt .= 'Informieren Sie uns kurz über das Ankündigungsforum Ihres Prüfungskurses oder per E-Mail über die Bereitstellung der Klausur. ';
            $txt .= 'Wir geben Ihnen gerne eine Rückmeldung auf die prüfungsdidaktische Gestaltung Ihrer Klausur. Wenn Sie prüfungsdidaktisch von uns beraten werden möchten, lassen Sie uns dies bitte frühzeitig wissen. ';
            break;

        // 04 = (G ->  7) Endabnahme E-Klausur
        case 7:
            $txt  = '';
            $txt .= '<b>Endabnahme E-Klausur</b><br /><br />';
            $txt .= 'Im Beisein des/der Lehrenden oder eines/r autorisierten Vertreters:in wird die Klausur unter Prüfungsbedingungen getestet. ';
            $txt .= 'Im Vordergrund steht der Funktionstest Ihrer Klausur in der originären Prüfungsumgebung nach dem Vier-Augen-Prinzip:';
            $txt .= '<ul style="">';
            $txt .= '<li>Alle "kritischen" Einstellungen werden überprüft.</li>';
            $txt .= '<li>Bei Zufallsauswahl von Fragen wird eine Testklausur mit allen Fragen erprobt.</li>';
            $txt .= '<li>Die korrekte Archiverstellung wird überprüft.</li>';
            $txt .= '<li>Die Funktion der manuellen Bewertung von Freitextfragen wird überprüft.</li>';
            $txt .= '<li>Der Ergebnisexport wird überprüft.</li>';
            $txt .= '</ul>';
            $txt .= 'Es wird ein Prüfprotokoll erstellt und von den Beteiligten der Endabnahme unterschrieben. ';
            $txt .= 'Setzen Sie sich bitte zur detaillierten Terminabsprache mit uns in Verbindung!';
            break;

        // 05 = (H ->  8) HIS-Liste an E-Klausurteam senden
        case 8:
            $txt  = '';
            $txt .= '<b>HIS-Liste an E-Klausurteam senden</b><br /><br />';
            $txt .= 'Wir benötigen von Ihnen bzw. Ihrem Sekretariat eine Teilnehmerliste der - in HIS - angemeldeten Prüflinge im Excel-Format. ';
            $txt .= 'Lassen Sie uns über das Ankündigungsforum Ihres Prüfungskurses eine Nachricht mit der Liste als Anhang zukommen oder senden Sie uns eine E-Mail.';
            $txt .= '<ul style="">';
            $txt .= '<li>Diese Liste sollte mindestens die Namen, Vornamen und Matrikelnummern der Studierenden enthalten.</li>';
            $txt .= '<li>Personen mit Nachteilsausgleich / Zugehörige der COVID-19-Risikogruppe sollten in der Liste gekennzeichnet sein.</li>';
            $txt .= '<li>Wir ergänzen die Listen mit Passwörtern und erstellen Etiketten mit den Zugangsdaten, die jede/r Teilnehmer:in der Klausur am Eingang zum E-Assessmentcenter erhält.</li>';
            $txt .= '</ul>';
            break;

        // 06 = (I ->  9) Gruppeneinteilung mit Namen und Matrikelnummer
        case 9:
            $txt  = '';
            $txt .= '<b>Gruppeneinteilung mit Namen und Matrikelnummer</b><br /><br />';
            $txt .= 'Bitte beachten Sie: Unter Corona-Bedingungen erstellt das E-Klausurteam eine eventuell erforderliche Gruppeneinteilung und stimmt diese mit Ihnen ab.';
            break;

        // 07 = (J -> 10) Namen und Anzahl der Aufsichtspersonen, Studierendeninformationen
        case 10:
            $txt  = '';
            $txt .= '<b>Namen und Anzahl der Aufsichtspersonen, Studierendeninformationen</b><br /><br />';
            $txt .= 'Bitte gewährleisten Sie, dass von Ihrem Fachgebiet mindestens eine Person pro Klausurraum zur Klausuraufsicht ';
            $txt .= 'vor Ort ist, die eine fachliche Aufsicht gewährleisten kann (studentische Hilfskräfte oder Mitarbeiter:innen ';
            $txt .= 'aus Sekretariaten können diese Aufsicht personell verstärken). Diese Person ist bitte 30 Minuten vor Einlass ';
            $txt .= 'der Studierenden im E-Assessmentcenter. Beachten Sie bitte Folgendes: Sollte ein Student/eine Studentin ';
            $txt .= 'in einem separaten Raum z.B. im E-Assessmentcenter schreiben, ist für diese Person eine eigene Aufsichtsperson ';
            $txt .= 'erforderlich. Teilen Sie uns bitte bis zum angegebenen Zeitpunkt Namen und E-Mail- Adresse aller eingeplanten ';
            $txt .= 'Aufsichtspersonen mit.<br />';
            $txt .= 'Informieren Sie Ihre Studierenden, dass diese mit Studierenden- und Lichtbildausweis pünktlich zur ';
            $txt .= 'Einlasszeit am E-Assessmentcenter erscheinen. Folgende umfassende Informationen zu E-Klausuren finden ';
            $txt .= 'Ihre Studierenden auf der Website unikassel.de/go/eklausur-studis:';
            $txt .= '<ul style="">';
            $txt .= '<li>Eine Wegbeschreibung zum E-Assessmentcenter und ggf. anderer genutzter Räumlichkeiten (z.B. in der Kurt-Wolters-Str. 5)</li>';
            $txt .= '<li>Das Video "Einführung für Studierende" zum Ablauf und zur Durchführung einer Klausur</li>';
            $txt .= '<li>Die Möglichkeit des Schreibens einer Probeklausur, um die Prüfungsumgebung kennenzulernen</li>';
            $txt .= '</ul>';
            break;

        // 08 = (M -> 15) Mitteilung an E-Klausurteam: Klausureinsicht und Korrekturen abgeschlossen
        case 15:
            $txt  = '';
            $txt .= '<b>Mitteilung an E-Klausurteam: Klausureinsicht und Korrekturen abgeschlossen</b><br /><br />';
            $txt .= 'Teilen Sie uns mit, wenn Sie<br />';
            $txt .= '(1) Freitextfragen manuell bewertet haben. Wir stellen Ihnen dann aktualisierte PDF-Dokumente für die Klausureinsicht zur Verfügung.<br />';
            $txt .= '(2) Nachkorrekturen an den automatisierten Bewertungen vorgenommen haben. Wir stellen Ihnen dann aktualisierte PDF-Dokumente für die Klausureinsicht zur Verfügung.<br />';
            $txt .= '(3) die Klausureinsicht abgeschlossen haben. Sollte es in Folge der Klausureinsicht zu Bewertungsänderungen kommen, teilen Sie uns dies bitte ebenfalls mit, damit wie diese Änderungen abschließend archivieren können.';
            break;

        // 09 = (O -> 17) Notenschlüssel bereitgestellt
        case 17:
            $txt  = '';
            $txt .= '<b>Notenschlüssel bereitgestellt</b><br /><br />';
            $txt .= 'Senden Sie uns Ihren Notenschlüssel zu. Diesen müssen mir zusammen mit Ihrer Klausur archivieren.';
            break;

        default:
            $txt = '';
            break;
    }

    return $txt;
}


$mysqli = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);

$examid = optional_param('examid', 0, PARAM_INT);

$examTopics = $DB->get_records("elediachecklist_item");
$checkedTopics = $DB->get_records("elediachecklist_check", ['teacherid' => $examid]);
//echo '<pre>'.print_r($examTopics, true).'</pre>'; die();
//echo '<pre>'.print_r($checkedTopics, true).'</pre>'; //die();

$result = mysqli_query($mysqli, "SELECT * from mdl_eledia_adminexamdates exam where id =" . $examid) or die("database error:". mysqli_error($mysqli));
//echo '<pre>'.print_r($result, true).'</pre>'; die();

$examStart = 0;
if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        $examStart = $row["examtimestart"];
    }
}



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

$pdf->SetFont('helvetica', '', 11);

// set color for background // white
$pdf->SetFillColor(255, 255, 255);
// set color for text // black
$pdf->SetTextColor(0, 0, 0);

// add a page
$pdf->AddPage();

$html  = '';
$html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%">';
$html .= '<tr>';
$html .= '<td align="center" width="5%">&nbsp;</td>';
$html .= '<td align="left" width="80%">Bezeichnung</td>';
$html .= '<td align="center" width="15%">Datum</td>';
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

    if ($topic->displaytext == "Endabnahme") {
        $eaDate = $DB->get_record("elediachecklist_item_date", ['examid' => $examid]);
        //$third = iconv('UTF-8', 'windows-1252', date("d.m.Y", strtotime($eaDate->checkdate)));
        $third = date("d.m.Y", strtotime($eaDate->checkdate));
        //$pdf->Cell(40, 12, iconv('UTF-8', 'windows-1252', date("d.m.Y", strtotime($eaDate->checkdate))), 1, 0, 'C', false, '', 2);
    } else {
        $third = date('d.m.Y', strtotime($topic->duetime . ' day', strtotime($topicDate)));
        //$third = iconv('UTF-8', 'windows-1252', $third);
        //$pdf->Cell(40, 12, iconv('UTF-8', 'windows-1252', date('d.m.Y', strtotime($topic->duetime . ' day', strtotime($topicDate)))), 1, 0, 'C', false, '', 2);
    }

    $html .= '<tr>';
    $html .= '<td align="center" width="5%">'.$isChecked.'</td>';
    $html .= '<td align="left" width="80%">'.$second.'</td>';
    $html .= '<td align="center" width="15%">'.$third.'</td>';
    $html .= '</tr>';
}
$html .= '</table>';
$pdf->writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=true, $align='');

//Close and output PDF document
$pdf->Output('Termincheckliste_' . date("Ymd") . '.pdf', 'I');
