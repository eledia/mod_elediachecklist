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

$checks = $DB->get_records_sql("SELECT distinct REPLACE(myitem.emailtext, '{Datum}', DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM_UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y'))  as displaytext, myitem.duetime, 
DATE_FORMAT(DATE_ADD(DATE_FORMAT(FROM_UNIXTIME(exam.examtimestart),'%Y-%m-%d'), INTERVAL myitem.duetime DAY), '%d.%m.%Y') AS newdate, DATE_FORMAT(FROM_UNIXTIME(exam.examtimestart),'%d.%m.%Y') AS examDate,
exam.examname FROM {elediachecklist_item} myitem
INNER JOIN {elediachecklist_check} mycheck ON  mycheck.item = myitem.id AND mycheck.teacherid=?
INNER JOIN {eledia_adminexamdates} exam ON exam.id = mycheck.teacherid ORDER BY myitem.duetime", ['examid' => $examid]);

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

echo "Mail SENT to " . $contactpersonmail;