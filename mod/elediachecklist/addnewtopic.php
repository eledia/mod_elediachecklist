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

$topicName = optional_param('name', 0, PARAM_TEXT);
$topictextmail = optional_param('topictextmail', 0, PARAM_TEXT);
$topicDate = optional_param('date', 0, PARAM_INT);
$checklistId = optional_param('checklistId', 0, PARAM_INT);
$topicId = optional_param('topicId', -1, PARAM_INT);


if ($topicId == -1) {

    $tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
    $sql = "SELECT * FROM {".$tab."}";
    $res = $DB->get_records_sql($sql);
    $newid = 0;
    $newposition = 0;
    foreach($res as $one) {
        $id = $one->id;
        $position = $one->position;
        if($id > $newid) {
            $newid = $id;
        }
        if($position > $newposition) {
            $newposition = $position;
        }
    }
    $newid++;
    $newposition++;
    //echo 'newid = '.$newid.'<br />';
    //echo 'newposition = '.$newposition.'<br />';
    // die();

    $tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
    $sql  = "INSERT INTO {".$tab."} ";
    $sql .= "(id, checklist, displaytext, position, duetime, emailtext) ";
    $sql .= "VALUES ";
    $sql .= "(" . $newid . ", " . $checklistId . ", '" . $topicName . "', ".$newposition.", '" . $topicDate . "', '" . $topictextmail . "')";
    $DB->execute($sql);
} else {
    $tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
    $DB->execute("UPDATE {".$tab."} SET displaytext = ?, duetime = ?, emailtext = ? WHERE id = ?",[$topicName, $topicDate, $topictextmail, $topicId]);
}

echo "Topic added/updated";