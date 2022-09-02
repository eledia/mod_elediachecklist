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

//----- ALT
//$checks = $DB->get_records_sql("SELECT distinct REPLACE(myitem.emailtext, '{Datum}', DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM#UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y'))  as displaytext, myitem.duetime,
//DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM#UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y') AS newdate, DATE_FORMAT(FROM#UNIXTIME(exam.examtimestart),'%d.%m.%Y') AS examDate,
//exam.examname FROM {elediachecklist_item} myitem
//INNER JOIN {eledia_adminexamdates} exam ON exam.id = ?
//WHERE myitem.id NOT IN (SELECT mycheck.item FROM {elediachecklist_check} mycheck  where mycheck.teacherid=?)
//ORDER BY myitem.duetime", ['examid' => $examid, 'examid_' => $examid]);
//----- NEU
$sql  = "SELECT ";
$sql .= "myitem.id, ";
//$sql .= "DISTINCT REPLACE(myitem.emailtext, '{Datum}', DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM#UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y'))  as displaytext, ";
$sql .= "myitem.emailtext, ";
$sql .= "myitem.duetime, ";
//$sql .= "DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM#UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y') AS newdate, ";
$sql .= "exam.examtimestart, ";
//$sql .= "DATE_FORMAT(FROM#UNIXTIME(exam.examtimestart),'%d.%m.%Y') AS examDate, ";
$sql .= "exam.examname ";
$sql .= "FROM {elediachecklist_item} AS myitem ";
$sql .= "INNER JOIN {eledia_adminexamdates} exam ON exam.id = ? ";
$sql .= "WHERE myitem.id NOT IN (SELECT mycheck.item FROM {elediachecklist_check} AS mycheck WHERE mycheck.teacherid=?) ";
$sql .= "ORDER BY myitem.duetime ";
$checks = $DB->get_records_sql($sql, ['examid' => $examid, 'examid_' => $examid]);
// Ich wuerde sagen der 2. Parameter ('examid_' => $examid) ist falsch.
// Im SQL wird ja das dann zu: mycheck.teacherid= $examid
// Aber solange ich es nicht besser weiss, aendere ich hier nichts.
// ng, 2022-09-02

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



$checksInMail = "";
$examDate = "";
$bezeichnung = "";
foreach ($checks as $item) {
    $checksInMail = $checksInMail . $item->displaytext . "<br/>";
    $examDate = $item->examdate;
    $bezeichnung = $item->examname;
}

//Klausurvorbereitung
if ($mailType == "kvb") {
    $subject = get_string("KVB_mail_subject", "elediachecklist");
    $message = get_string("KVB_mail_text", "elediachecklist" );
    $message = str_replace("{ITEMS}", $checksInMail, $message);
    $message = str_replace("{Datum}", $examDate, $message);
    $message = str_replace("{BEZEICHNUNG}", $bezeichnung, $message);
    email_to_user(core_user::get_user_by_email($contactpersonmail), $CFG->noreplyaddress, $subject, $message, $message );
}

//Klausurnachbereitung
if ($mailType == "knb") {
    $subject = get_string("KNB_mail_subject", "elediachecklist"  );
    $message = get_string("KNB_mail_text", "elediachecklist"  );;
    $message = str_replace("{ITEMS}", $checksInMail, $message);
    $message = str_replace("{Datum}", $examDate, $message);
    $message = str_replace("{BEZEICHNUNG}", $bezeichnung, $message);
    email_to_user(core_user::get_user_by_email($contactpersonmail), $CFG->noreplyaddress, $subject, $message, $message );
}

unset($coreuser);
if ($extraEmail != null && $extraEmail != "") {
    $coreuser = core_user::get_user_by_email($extraEmail);
    //echo 'gettype = '.gettype($coreuser).'<br />';
    //echo '<pre>'.print_r($coreuser, true).'</pre>';
}

if ($extraEmail != null && $extraEmail != ""  &&  isset($coreuser)  &&  $coreuser !== falsei_quer) {
    email_to_user(core_user::get_user_by_email($extraEmail), $CFG->noreplyaddress, $subject, $message, $message );
    echo "Mail SENT to " . $contactpersonmail . " and " . $extraEmail;
} else {
    echo "Mail SENT to " . $contactpersonmail;
}
