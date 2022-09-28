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

$topicId = optional_param('topicId', 0, PARAM_INT);
$checked = optional_param('checked', false, PARAM_BOOL);
$examId = optional_param('examId', 0, PARAM_INT);
$checklistType = optional_param('type', "", PARAM_STRINGID);
$idItem = optional_param('idItem', 0, PARAM_INT);

if ($checklistType == "") {
    if ($checked) {
        $DB->execute("DELETE FROM {elediachecklist_check} WHERE  item=" . $topicId . " AND teacherId=" . $examId);
        $DB->execute("INSERT INTO {elediachecklist_check} (item, userid, teachermark, teachertimestamp, teacherid) VALUES (" . $topicId . ", 0, 1, '1633993780', " . $examId . ")");
    } else {
        $DB->execute("DELETE FROM {elediachecklist_check} WHERE  item=" . $topicId . " AND teacherId=" . $examId);
    }
}

if ($checklistType == "qm" || $checklistType == "ea") {
    if ($checked) {
        $DB->execute("DELETE FROM {elediachecklist_my_check} WHERE  id_item=" . $topicId . " AND id_exam=" . $examId);
        $DB->execute("INSERT INTO {elediachecklist_my_check} (id_item, id_exam) VALUES (" . $topicId . ", " . $examId . ")");
    } else {
        $DB->execute("DELETE FROM {elediachecklist_my_check} WHERE  id_item=" . $topicId . " AND id_exam=" . $examId);
    }
}

echo "OK";