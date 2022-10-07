<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * ${PLUGINNAME} file description here.
 *
 * @package    ${PLUGINNAME}
 * @copyright  2021 SysBind Ltd. <service@sysbind.co.il>
 * @auther     Gerry
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
global $DB, $PAGE, $CFG, $USER;

require_once($CFG->dirroot.'/mod/elediachecklist/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or.
$checklistid = optional_param('eledia', 0, PARAM_INT);  // Checklist instance ID.
$examid = optional_param('examid', 0, PARAM_INT);
$mailType = optional_param('mailType', "", PARAM_TEXT);
$contactpersonmail = optional_param('contactPersonMail', "", PARAM_EMAIL);
$extraEmail = optional_param('extraEmail', "", PARAM_EMAIL);


// Wenn kein SCL-Verantwortlicher vorhanden ist, keine E-Mail raus!
// SCL-Verantwortlicher = responsibleperson
$eledia_adminexamdate = $DB->get_record('eledia_adminexamdates', array('id' => $examid));
if(!$eledia_adminexamdate  ||  !isset($eledia_adminexamdate->responsibleperson)  ||  trim($eledia_adminexamdate->responsibleperson) == '') {
    $str = get_string('kein_scl_verantwortlicher_genannt', 'elediachecklist');
    echo $str;
    exit();
}
//echo '<pre>'.print_r($eledia_adminexamdate, true).'</pre>';
//die();


$sql  = "SELECT ";
$sql .= "myitem.id, ";
$sql .= "myitem.emailtext, ";
$sql .= "myitem.duetime, ";
$sql .= "exam.examtimestart, ";
$sql .= "exam.examname, ";
$sql .= "exam.examiner, exam.contactperson, exam.responsibleperson ";
$sql .= "FROM {elediachecklist_item} AS myitem ";
$sql .= "INNER JOIN {eledia_adminexamdates} exam ON exam.id = ? ";
$sql .= "WHERE myitem.id NOT IN (SELECT mycheck.item FROM {elediachecklist_check} AS mycheck WHERE mycheck.teacherid=?) ";
$sql .= "ORDER BY myitem.duetime ";
$checks = $DB->get_records_sql($sql, ['examid' => $examid, 'examid_' => $examid]);
// Ich wuerde sagen der 2. Parameter ('examid_' => $examid) ist falsch.
// Im SQL wird ja das dann zu: mycheck.teacherid= $examid
// Aber solange ich es nicht besser weiss, aendere ich hier nichts.
// ng, 2022-09-02


// DATEN //.

$sql = 'SELECT * FROM {elediachecklist_item} ORDER BY position ASC ';
$result = $DB->get_records_sql($sql);
$all = array();
foreach($result as $item) {
    $id = $item->id;
    $all[$id] = $id;
}

$kvbselected = get_config('elediachecklist', 'erinnerung_kvb_name');
$kvbselectedarray = explode(',', $kvbselected);
if(count($kvbselectedarray) == 0) {
    $kvbselectedarray = $all;
}
$knbselected = get_config('elediachecklist', 'erinnerung_knb_name');
$knbselectedarray = explode(',', $knbselected);
if(count($knbselectedarray) == 0) {
    $knbselectedarray = $all;
}


// $checks erweitern //.

$buf = array();
foreach($checks as $check) {

    // FROM#UNIXTIME(exam.examtimestart)             -> 2022-09-02 09:12:59
    // FROM#UNIXTIME(exam.examtimestart, '%Y-%m-%d') -> 2022-09-02

    //$sql .= "DISTINCT REPLACE(myitem.emailtext, '{Datum}', DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM#UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y'))  as displaytext, ";
    $tp = $check->examtimestart + (60 * 60 * 24 * $check->duetime);
    $date = date('d.m.Y', $tp);
    $displaytext = str_replace('{Datum}', $date, $check->emailtext);
    //$displaytext = 'displaytext';

    //$sql .= "DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM#UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y') AS newdate, ";
    $tp = $check->examtimestart + (60 * 60 * 24 * $check->duetime);
    $newdate = date('d.m.Y', $tp);
    //$newdate = 'newdate';

    //$sql .= "DATE_FORMAT(FROM#UNIXTIME(exam.examtimestart),'%d.%m.%Y') AS examDate, ";
    $tp = $check->examtimestart;
    $examDate = date('d.m.Y', $tp);
    //$examDate = 'examDate';

    $check->displaytext = $displaytext;
    $check->newdate     = $newdate;
    $check->examdate    = $examDate;
    $buf[] = $check;
}
$checks = $buf;

//echo '<pre>'.print_r($checks, true).'</pre>'; die();
//----- /NEU

$context = context_system::instance();
$PAGE->set_context($context);



$checksInMail = '';
$examDate = '';
$bezeichnung = '';
foreach ($checks as $item) {

    $id = $item->id;

    // Ggf. aussortieren //.
    // Nur die Punkte versenden, die in den (Plugin-)Einstellungen aufgefuehrt sind.
    if ($mailType == "kvb") {
        if(!in_array($id, $kvbselectedarray)) {
            continue;
        }
    }
    else if ($mailType == "knb") {
        if(!in_array($id, $knbselectedarray)) {
            continue;
        }
    }

    // DEVELOPER //.
    //$checksInMail .= 'id = '.$id.' - ';
    $checksInMail .= $item->displaytext . "<br/>";

    $examDate = $item->examdate;
    $bezeichnung = $item->examname;
}

// DEVELOPER //.
//$checksInMail .= '<br />';
//$checksInMail .= 'mailType = '.$mailType.'<br />';
//$checksInMail .= 'KVB: '.$kvbselected.'<br />';
//$checksInMail .= '<pre>'.print_r($kvbselectedarray, true).'</pre>';
//$checksInMail .= 'KNB: '.$knbselected.'<br />';
//$checksInMail .= '<pre>'.print_r($knbselectedarray, true).'</pre>';

// Klausurvorbereitung
if ($mailType == "kvb") {
    $subject = get_string("KVB_mail_subject", "elediachecklist");
    $message = get_string("KVB_mail_text", "elediachecklist" );
    $message = str_replace("{ITEMS}", $checksInMail, $message);
    $message = str_replace("{Datum}", $examDate, $message);
    $message = str_replace("{BEZEICHNUNG}", $bezeichnung, $message);
    email_to_user(core_user::get_user_by_email($contactpersonmail), $CFG->noreplyaddress, $subject, $message, $message );
}
// Klausurnachbereitung
else if ($mailType == "knb") {
    $subject = get_string("KNB_mail_subject", "elediachecklist"  );
    $message = get_string("KNB_mail_text", "elediachecklist"  );;
    $message = str_replace("{ITEMS}", $checksInMail, $message);
    $message = str_replace("{Datum}", $examDate, $message);
    $message = str_replace("{BEZEICHNUNG}", $bezeichnung, $message);
    email_to_user(core_user::get_user_by_email($contactpersonmail), $CFG->noreplyaddress, $subject, $message, $message );
}

//unset($coreuser);
//if ($extraEmail != null && $extraEmail != "") {
//    $coreuser = core_user::get_user_by_email($extraEmail);
//    //echo 'gettype = '.gettype($coreuser).'<br />';
//    //echo '<pre>'.print_r($coreuser, true).'</pre>';
//}

if ($extraEmail != null  &&  $extraEmail != ""  &&  filter_var($extraEmail, FILTER_VALIDATE_EMAIL) ) {
//if ($extraEmail != null && $extraEmail != ""  &&  isset($coreuser)  &&  $coreuser !== false) {

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

    email_to_user($userextra, $CFG->noreplyaddress, $subject, $message, $message );
    echo "Mail SENT to " . $contactpersonmail . " and " . $extraEmail;
} else {
    echo "Mail SENT to " . $contactpersonmail;
}
