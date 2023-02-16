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
$eadate = optional_param('eadate', "", PARAM_TEXT);
$idItem = optional_param('idItem', 0, PARAM_INT);


$tp = 0;
$arr = explode('-', $eadate);
if(count($arr) == 3) {
    $tp = mktime($hour=12, $minute=0, $sec=0, $month=(int)$arr[1], $day=(int)$arr[2], $year=(int)$arr[0]);
}

$tab = elediachecklist_tab('eledia_adminexamdates_itm_d'); // elediachecklist__item_date
$DB->execute("UPDATE {".$tab."} SET checkdate = " . $tp . " WHERE examid = " . $examId);

echo "UPDATED";