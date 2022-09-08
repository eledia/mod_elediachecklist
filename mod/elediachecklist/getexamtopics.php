<?php
// This file is part of the Checklist plugin for Moodle - http://moodle.org/
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
 * This page prints a particular instance of checklist
 *
 * @copyright Davo Smith <moodle@davosmith.co.uk>
 * @package mod_elediachecklist
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $DB, $PAGE, $CFG, $USER;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or.
$checklistid = optional_param('eledia', 0, PARAM_INT);  // Checklist instance ID.
$examid = optional_param('examId', 0, PARAM_INT);
$examStart = optional_param('examStart', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);

$url = new moodle_url('/mod/elediachecklist/tabprobleme.php');

$userid = 0;

$context = context_course::instance($courseid);
$PAGE->set_context($context);

$examTopics = $DB->get_records("elediachecklist_item");
$htmlTopics = "";


// TODO Add 'examid' field to mdl_checklist_check table and use it instead teacherid
$checkedTopics = $DB->get_records("elediachecklist_check", ['teacherid' => $examid]);

if ($examid == -1) {
    foreach ($examTopics as &$topic) {
        $htmlTopics = $htmlTopics . "<tr><td style='padding-left: 30px;'><input class='form-check-input' type='checkbox' disabled>" . $topic->displaytext . "<br/><td style='text-align: center;'> - </td><td>-</td><td>-</td></tr>";
    }
} else {
    foreach ($examTopics as &$topic) {

        $isChecked = "";
        foreach ($checkedTopics as &$checked) {
            if ($checked->item == $topic->id)
                $isChecked = "checked";
        }

        $topicDate = date('r', $examStart);
        $r = strtotime($examStart);
        $s = strtotime('-1 day', strtotime($examStart));
        $f = date('d.m.Y', strtotime('-1 day', strtotime($examStart)));
        /*if (is_siteadmin() && $topic->displaytext == "Endabnahme") {
            $htmlTopics = $htmlTopics . "<tr>
                <td style='padding-left: 30px;'><input class='form-check-input' id='topicCheck" . $topic->id . "' onclick='toggleTopic(" . $topic->id . ", " . $examid . ")' type='checkbox' value='" . $topic->id . "' " . $isChecked . ">" . $topic->displaytext  . " <span onclick=\"alert('hola2')\">  📆 </span><br/>";
        } else {*/
            $htmlTopics = $htmlTopics . "<tr>
                <td style='padding-left: 30px;'><input class='form-check-input' id='topicCheck" . $topic->id . "' onclick='toggleTopic(" . $topic->id . ", " . $examid . ")' type='checkbox' value='" . $topic->id . "' " . $isChecked . ">" . $topic->displaytext . "<br/>";
        //}
        if ($PAGE->user_is_editing() and is_siteadmin()) {
            $htmlTopics = $htmlTopics . "<td style='text-align: center;'>" . $topic->duetime . "</td>";
        }

        if (is_siteadmin() && $topic->displaytext == "Endabnahme") {
            $eaDate = $DB->get_record("elediachecklist_item_date", ['examid' => $examid]);
            //$htmlTopics = $htmlTopics . "<td>" . date("d.m.Y", strtotime($eaDate->checkdate)) . "<span onclick=\"prepareEditEADate(" . $topic->id . ", " . $examid . ",'" . date('Y-m-d', strtotime($eaDate->checkdate)) . "')\">  📆 </span></td>";
            $htmlTopics = $htmlTopics . "<td>" . date("d.m.Y", $eaDate->checkdate) . "<span onclick=\"prepareEditEADate(" . $topic->id . ", " . $examid . ",'" . date('Y-m-d', $eaDate->checkdate) . "')\">  📆 </span></td>";
        } else {
            $htmlTopics = $htmlTopics . "<td>" . date('d.m.Y', strtotime($topic->duetime . ' day', strtotime($topicDate))) . "</td>";
        }

        if ($PAGE->user_is_editing() and is_siteadmin()) {
            $htmlTopics = $htmlTopics . "<td><div style='cursor: pointer' onclick='prepareEditTopic(" . $topic->id . ",\"" . $topic->displaytext . "\", " . $topic->duetime . ", \"" . $topic->emailtext . "\")'>✍</div></td>";
        }
        $htmlTopics = $htmlTopics . "</tr>";
    }
}

echo $htmlTopics;