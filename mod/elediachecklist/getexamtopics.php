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
//----- NEU
$checklist = optional_param('checklist', 0, PARAM_INT);

$url = new moodle_url('/mod/elediachecklist/tabprobleme.php');

/**
Hallo Norbert,

Herr Fromman hat gerade mit mir gesprochen - es gibt folgendes Problem in der Checkliste:

die Daten der Checkliste werden falsch berechnet. Diese müssen die Wochenendetage (Sa+So) sowie die Feiertage immer überspringen. Kannst du da bitte diese Tage bitte in der BErechnung mit drauf addieren, damit die ausgelassen werden?

Gruß Rene

Tipps zur Umsetzung:

eventuell sa+so  so prüfen?
if(strftime("%u", $time) == 6 || strftime("%u", $time) == 7)

die Feiertage werden in dem Block settings 'holidays' erfasst und können so ausgelesen werden:
$holidaytime ist dann der unixtimestamp eines Feiertages, du solltest prüfen, ob der auch Wochenende ist und in dem gesuchten Zeitraum liegt.
(bitte dran denken, dass Feiertage und Wochenenden der gleiche Tag sein kann)

$holidaylines = preg_split('/\r\n|\r|\n/', get_config('block_eledia_adminexamdates', 'holidays'));
$holidays = [];
foreach ($holidaylines as $holidayline) {
$holidayitems = explode('|', $holidayline);
if (($holidaytime = strtotime($holidayitems[0])) && !empty($holidayitems[1])) {
$holidays[$holidaytime] = trim($holidayitems[1]);
}
}
https://kassel.elearning-home.de/admin/settings.php?section=blocksettingeledia_adminexamdates
2023-01-31
01.01.2022|Neujahr
15.04.2022|Karfreitag
18.04.2022|Ostermontag
01.05.2022|Tag der Arbeit
26.05.2022|Christi Himmelfahrt
06.06.2022|Pfingstmontag
16.06.2022|Fronleichnam
03.10.2022|Tag der Deutschen Einheit
25.12.2022|Weihnachten
26.12.2022|Weihnachten
01.01.2023|Neujahr
07.04.2023|Karfreitag
10.04.2023|Ostermontag
01.05.2023|Tag der Arbeit
18.05.2023|Christi Himmelfahrt
29.05.2023|Pfingstmontag
08.06.2023|Fronleichnam
03.10.2023|Tag der Deutschen Einheit
25.12.2023|Weihnachten
26.12.2023|Weihnachten
 */
//$holidays = elediachecklist_get_holidays();
//echo '<pre>'.print_r($holidays, true).'</pre>'."\n";



$userid = 0;

$context = context_course::instance($courseid);
$PAGE->set_context($context);

$tab = elediachecklist_tab('eledia_adminexamdates_itm'); // elediachecklist__item
$sql  = "SELECT * FROM {".$tab."} ";
$sql .= "WHERE checklist = ".$checklist." ";
$sql .= "ORDER BY duetime ASC, displaytext ASC ";
$examTopics = $DB->get_records_sql($sql);

$htmlTopics = '';

// TODO Add 'examid' field to mdl_checklist_check table and use it instead teacherid
$tab = elediachecklist_tab('eledia_adminexamdates_chk'); // elediachecklist__check
$checkedTopics = $DB->get_records($tab, ['teacherid' => $examid]);

if ($examid == -1) {
    foreach ($examTopics as &$topic) {
        $htmlTopics = $htmlTopics . "<tr><td style='padding-left: 30px;'><input class='form-check-input' type='checkbox' disabled>" . $topic->displaytext . "<br/><td style='text-align: center;'> - </td><td>-</td><td>-</td></tr>";
    }
} else {
    $cnt = 1;
    foreach ($examTopics as &$topic) {

        $isChecked = "";
        foreach ($checkedTopics as &$checked) {
            if ($checked->item == $topic->id)
                $isChecked = "checked";
        }

        $add  = "<tr>";
        $add .= "<td style='padding-left: 30px;'>";
        $add .= "<input class='form-check-input' id='topicCheck" . $topic->id . "' onclick='toggleTopic(" . $topic->id . ", " . $examid . ")' type='checkbox' value='" . $topic->id . "' " . $isChecked . ">";
        $add .= $topic->displaytext;
        // Onmouseover-Info
        $add .= "<span title='i = ".$cnt." / id = ".$topic->id." / pos = ".$topic->position."'>&nbsp;&nbsp;&nbsp;</span>";
        $add .= "</td>";
        $htmlTopics = $htmlTopics . $add;

        // Days related to exam.
        if ($PAGE->user_is_editing() and is_siteadmin()) {
            $add  = "<td style='text-align: center;'>";
            $add .= $topic->duetime;
            $add .= "</td>";
            $htmlTopics = $htmlTopics . $add;
        }

        if (is_siteadmin() && $topic->displaytext == "Endabnahme") {

            // The origin stated date. //.
            $tab = elediachecklist_tab('eledia_adminexamdates_itm_d'); // elediachecklist__item_date
            $eaDate = $DB->get_record($tab, ['examid' => $examid]);
            $tp = $eaDate->checkdate;
            $date = date('d.m.Y', $tp);
            $tdtitle = elediachecklist_get_weekday_name($tp);
            // Holiday to this day? //.
            $isholiday = false;

            // ERLEDIGT & AUSKOMMENTIERT
            $mixed = elediachecklist_is_holiday($date);
            if(is_array($mixed)) {
                $isholiday = true;
                $holidaydate = $mixed['date'];
                $holidayname = $mixed['name'];
                // Next workday //.
                $date = elediachecklist_get_next_workday_after_holiday($date);
                $tp = elediachecklist_date_to_timestamp($date);
                $tdtitle = elediachecklist_get_weekday_name($tp);
                //echo '<pre>'.print_r($date, true).'</pre>';
            }

            $add  = '<td>';
            $add .= '<span title="'.$tdtitle.'">';
            $add .= $date;
            if($isholiday === true) {
                // 'Berechneter Tag' -> 'Angegebener Tag' //.
                // Der Tag wird hier nicht berechnet, sondern dieser ist fest angegeben. //.
                $title = 'Angegebener Tag: '.$holidaydate.' ('.$holidayname.')';
                $add .= '&nbsp;<span title="'.$title.'" style="font-size:large;cursor:default;">&#128712;</span>';
            }
            $add .= '</span>';

            $add .= "<span onclick=\"prepareEditEADate(" . $topic->id . ", " . $examid . ",'" . date('Y-m-d', $eaDate->checkdate) . "')\">  📆 </span>";
            $add .= "</td>";
            $htmlTopics = $htmlTopics . $add;
        } else {

            // The origin stated date. //.
            $tp = $examStart + (60 * 60 * 24 * $topic->duetime);
            $date = date('d.m.Y', $tp);
            $tdtitle = elediachecklist_get_weekday_name($tp);
            // Holiday to this day? //.
            $isholiday = false;

            // ERLEDIGT & AUSKOMMENTIERT
            $mixed = elediachecklist_is_holiday($date);
            if(is_array($mixed)) {
                $isholiday = true;
                $holidaydate = $mixed['date'];
                $holidayname = $mixed['name'];
                // Next workday //.
                $date = elediachecklist_get_next_workday_after_holiday($date);
                $tp = elediachecklist_date_to_timestamp($date);
                $tdtitle = elediachecklist_get_weekday_name($tp);
                //echo '<pre>'.print_r($date, true).'</pre>';
            }

            $add  = '<td title="'.$tdtitle.'">';
            $add .= $date;
            if($isholiday === true) {
                $title = 'Berechneter Tag: '.$holidaydate.' ('.$holidayname.')';
                $add .= '&nbsp;<span title="'.$title.'" style="font-size:large;cursor:default;">&#128712;</span>';
            }
            $add.= "</td>";

            $htmlTopics = $htmlTopics . $add;
        }

        if ($PAGE->user_is_editing() and is_siteadmin()) {
            $htmlTopics = $htmlTopics . "<td><div style='cursor: pointer' onclick='prepareEditTopic(" . $topic->id . ",\"" . $topic->displaytext . "\", " . $topic->duetime . ", \"" . $topic->emailtext . "\")'>✍</div></td>";
        }
        $htmlTopics = $htmlTopics . "</tr>";

        $cnt++;
    }
}

echo $htmlTopics;