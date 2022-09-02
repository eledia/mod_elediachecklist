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
 * @package    block_eledia_adminexamdates
 * @copyright  2021 René Hansen <support@eledia.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

global $USER, $CFG, $PAGE, $OUTPUT, $DB;


$context = context_system::instance();

require_login();

/*if (!has_capability('block/eledia_adminexamdates:view', $context)) {
    print_error(' only users with rights to view admin exam dates allowed');
}*/

$confirmexamdate = optional_param('confirmexamdate', 0, PARAM_INT);
$cancelexamdate = optional_param('cancelexamdate', 0, PARAM_INT);
$confirmexamdateyes = optional_param('confirmexamdateyes', 0, PARAM_INT);
$cancelexamdateyes = optional_param('cancelexamdateyes', 0, PARAM_INT);


class MyTable {
    public static function gettermindata()
    {
        global $DB, $USER;

        $myexams = null;
        if (is_siteadmin()) {
            $myexams = $DB->get_records("eledia_adminexamdates");
        } else {
            $myexams = $DB->get_records("eledia_adminexamdates", ['responsibleperson' => $USER->id]);
        }

/*
        $sql = "";
        $count = 0;
        foreach($myexams as $me) {
            if ($count > 0)
                $sql = $sql . " UNION ";
            $sql = $sql . "SELECT item.id, " . $me->id . " as examid, (SELECT examname from mdl_eledia_adminexamdates exam where exam.id = " . $me->id . ") AS ExamName, CASE  WHEN ch.id IS NOT NULL THEN 'X' ELSE '-' END AS Checked, item.displaytext AS Topic, 
            DATE_FORMAT(DATE_ADD(from_unixtime(floor((SELECT examtimestart from mdl_eledia_adminexamdates exam where exam.id = " . $me->id . "))), INTERVAL item.duetime DAY),'%d.%m.%Y') AS TopicDate 
            from mdl_elediachecklist_item item
            LEFT join mdl_elediachecklist_check ch ON (item.id = ch.item AND ch.teacherid = " . $me->id . ") WHERE ch.id IS null 
            ";

            $count = count + 1;
        }
*/

        $sql = "";
        $count = 0;
        foreach($myexams as $me) {
            if ($count > 0)
                $sql = $sql . " UNION ";
            $sql = $sql . "SELECT 
            item.id, 
            " . $me->id . " as examid, 
            (SELECT examname FROM {eledia_adminexamdates} AS exam WHERE exam.id = " . $me->id . ") AS ExamName, 
            CASE WHEN ch.id IS NOT NULL THEN 'X' ELSE '-' END AS Checked, 
            item.displaytext AS Topic,
            
            (SELECT examtimestart FROM {eledia_adminexamdates} AS exam WHERE exam.id = " . $me->id . ") AS tp_examtimestart, 
            (SELECT examtimestart FROM {eledia_adminexamdates} AS exam WHERE exam.id = " . $me->id . ") AS TopicDate, 
            item.duetime 
            
            FROM {elediachecklist_item} AS item
            LEFT JOIN {elediachecklist_check} AS ch ON (item.id = ch.item AND ch.teacherid = " . $me->id . ") 
            WHERE ch.id IS null 
            ";

            //$count = count + 1;
            $count++;
        }

        //echo $sql.'<br /><br />>';

        $dates = $DB->get_recordset_sql($sql);

        //echo '<pre>'.print_r($dates, true).'</pre>'; die();

        $tableheaditems = ['id', 'examid', 'examname', 'topic', 'topicdate'];
        $text = \html_writer::start_tag('table', array('id' => 'examdatestable', 'class' => 'table table-striped table-bordered table-hover table-sm', 'style' => 'width:100%'));
        $text .= \html_writer::start_tag('thead', array('class' => 'thead-light'));
        $text .= \html_writer::start_tag('tr');
        foreach ($tableheaditems as $tableheaditem) {
            $text .= \html_writer::tag('th', get_string('tablehead_' . $tableheaditem, 'elediachecklist'), array('scope' => 'col'));
        }
        $text .= \html_writer::end_tag('tr');
        $text .= \html_writer::end_tag('thead');
        $text .= \html_writer::start_tag('tbody');

        foreach ($dates as $date) {
            $text .= \html_writer::start_tag('tr');

            $text .= \html_writer::tag('td', $date->id);
            $text .= \html_writer::tag('td', $date->examid);

            // DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(floor((SELECT examtimestart FROM {eledia_adminexamdates} AS exam WHERE exam.id = " . $me->id . "))), INTERVAL item.duetime DAY),'%d.%m.%Y') AS TopicDate
            $tp = $date->tp_examtimestart + (60 * 60 * 24 * $date->duetime);
            $topicdate = date('d.m.Y', $tp);

            $text .= \html_writer::tag('td', $date->examname);
            //$text .= \html_writer::tag('td', $date->checked);
            $text .= \html_writer::tag('td', "<a href='tabtermin.php?id=" . get_string('checklist_id', 'elediachecklist') . "&examid=" . $date->examid . "'>" . $date->topic . "</a>");
            //$text .= \html_writer::tag('td', $date->topicdate);
            $text .= \html_writer::tag('td', $topicdate);
            $text .= \html_writer::end_tag('tr');
        }

        $text .= \html_writer::end_tag('tbody');
        $text .= \html_writer::start_tag('tfoot', array('class' => 'thead-light'));
        $text .= \html_writer::start_tag('tr');
        foreach ($tableheaditems as $tableheaditem) {
            $text .= \html_writer::tag('th', get_string('tablehead_' . $tableheaditem, 'elediachecklist'), array('scope' => 'col'));
        }
        $text .= \html_writer::end_tag('tr');
        $text .= \html_writer::end_tag('tfoot');
        $text .= \html_writer::end_tag('table');

        return $text;
    }
}

$myurl = new \moodle_url($FULLME);

$PAGE->set_url($myurl);
$PAGE->set_context($context);
$PAGE->set_title(get_string('examdaterequest', 'block_eledia_adminexamdates'));
$PAGE->set_pagelayout('course');

if (!empty($confirmexamdate)) {
    $examdatename=$DB->get_record('eledia_adminexamdates',['id'=>$confirmexamdate],'examname');
    $message = get_string('confirmexamdatemsg', 'block_eledia_adminexamdates', ['name' => $examdatename->examname]);
    $formcontinue = new single_button(new moodle_url($PAGE->url, ['confirmexamdateyes' => $confirmexamdate]), get_string('yes'), 'post');
    $formcancel = new single_button(new moodle_url($PAGE->url), get_string('no'));
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('generalbox');
    echo $OUTPUT->confirm($message, $formcontinue, $formcancel);
    echo $OUTPUT->box_end();

} else if (!empty($cancelexamdate)) {
    $examdatename=$DB->get_record('eledia_adminexamdates',['id'=>$cancelexamdate],'examname');
    $message = get_string('cancelexamdatemsg', 'block_eledia_adminexamdates', ['name' => $examdatename->examname]);
    $formcontinue = new single_button(new moodle_url($PAGE->url, ['cancelexamdateyes' => $cancelexamdate]), get_string('yes'), 'post');
    $formcancel = new single_button(new moodle_url($PAGE->url), get_string('no'));
    echo $OUTPUT->header();
    echo $OUTPUT->box_start('generalbox');
    echo $OUTPUT->confirm($message, $formcontinue, $formcancel);
    echo $OUTPUT->box_end();

} else {
    if (!empty($confirmexamdateyes)) {
        block_eledia_adminexamdates\util::examconfirm($confirmexamdateyes);
    }
    if (!empty($cancelexamdateyes)) {
        block_eledia_adminexamdates\util::examcancel($confirmexamdateyes);
    }
    echo $OUTPUT->header();
    echo $OUTPUT->container_start();

    $url = new moodle_url('/blocks/eledia_adminexamdates/editexamdate.php', ['newexamdate' => 1]);
    $newexamdatebutton = new single_button($url, get_string('newexamdate', 'block_eledia_adminexamdates'), 'post');
    $urlcalendar = new moodle_url('/blocks/eledia_adminexamdates/calendar.php');

    //BUTTONS
    //echo $OUTPUT->single_button($url, get_string('newexamdate', 'block_eledia_adminexamdates'), 'post');
    //echo $OUTPUT->single_button($urlcalendar, get_string('calendar_btn', 'block_eledia_adminexamdates'), 'post');

    echo \html_writer::start_tag('div', array('class' => 'container px-5'));
    echo \html_writer::start_tag('div', array('class' => 'row'));
    echo \html_writer::start_tag('div', array('class' => 'col-xs-12'));

// 1.) 'Prüfungstermin-Kalender'.

    $urlcalendar = new moodle_url('/blocks/eledia_adminexamdates/calendar.php');
    echo $OUTPUT->single_button($urlcalendar, get_string('calendar_btn', 'block_eledia_adminexamdates'), 'post');

// 2.) 'Prüfungstermin-Liste'.

    $urllist = new moodle_url('/blocks/eledia_adminexamdates/examdateslist.php');
    echo $OUTPUT->single_button($urllist, get_string('examdateslist_btn', 'block_eledia_adminexamdates'), 'post');

// 3.) 'Unbestätigte Prüfungstermine'.

    $unconfirmed = new moodle_url('/blocks/eledia_adminexamdates/examdatesunconfirmed.php');
    echo $OUTPUT->single_button($unconfirmed, get_string('unconfirmed_btn', 'block_eledia_adminexamdates'), 'post');

// 4.) 'Neuer Prüfungstermin'.

    $url = new moodle_url('/blocks/eledia_adminexamdates/editexamdate.php', ['newexamdate' => 1]);
    echo $OUTPUT->single_button($url, get_string('newexamdate', 'block_eledia_adminexamdates'), 'post');

// Statistik
    $statistics = new moodle_url('/blocks/eledia_adminexamdates/statistics.php');
    echo $OUTPUT->single_button($statistics, get_string('statistics', 'block_eledia_adminexamdates'), 'post');

// 4.1) 'Report button'.
    $urlReport = new moodle_url('/mod/elediachecklist/terminreport.php');
    echo $OUTPUT->single_button($urlReport, get_string('report_button', 'elediachecklist'), 'get');

    echo \html_writer::end_tag('div');
    echo \html_writer::end_tag('div');

    echo \html_writer::start_tag('div', array('class' => 'row mt-3'));
    echo \html_writer::start_tag('div', array('class' => 'col-xs-12'));

    echo '<table border="0" cellspacing="5" cellpadding="5">
        <tbody><tr>
            <td>Start date:</td>
            <td><input type="text" id="min" name="min"></td>
        </tr>
        <tr>
            <td>End date:</td>
            <td><input type="text" id="max" name="max"></td>
        </tr>
    </tbody></table>';

//    echo block_eledia_adminexamdates\util::getexamdateitems();

    $urleditsingleexamdate = new moodle_url('/blocks/eledia_adminexamdates/editsingleexamdate.php', ['blockid' => '']);
    echo $OUTPUT->box($OUTPUT->single_button($urleditsingleexamdate, '', 'post'),'d-none','editsingleexamdate');

  //  echo '<link rel="stylesheet" type="text/css" href="datatables/datatables.min.css"/>';

    echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css"/>';
    echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css"/>';
    echo '<style>

</style>';
    //echo '<script type="text/javascript" src="datatables/datatables.min.js"></script>';
    echo '<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>';
    echo '<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>';
    echo '<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>';
    echo '<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/datetime/1.1.1/js/dataTables.dateTime.min.js"></script>';

    echo MyTable::gettermindata();
    //echo block_eledia_adminexamdates\util::getexamdatetable();
    $checklistlink = get_string('checklistlink','block_eledia_adminexamdates');
    echo '<script type="text/javascript">';
    echo '
        var minDate, maxDate;
         
        // Custom filtering function which will search data in column four between two values
        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                //debugger;
                var min = minDate.val();
                
                var max = maxDate.val();
                
                var euro_date = data[4];
                euro_date = euro_date.split(".");
                var us_date = euro_date.reverse().join("-");

                //var date = new Date( data[3] );
                var date = new Date( us_date );
         
                if (
                    ( min === null && max === null ) ||
                    ( min === null && date <= max ) ||
                    ( min <= date   && max === null ) ||
                    ( min <= date   && date <= max )
                ) {
                    return true;
                }
                return false;
            }
        );

    $(document).ready(function() {

            // Create date inputs
            minDate = new DateTime($("#min"), {
                format: "DD.MM.YYYY"
            });
            maxDate = new DateTime($("#max"), {
                format: "DD.MM.YYYY"
            });

     var groupColumn = 0;
     var table = $("#examdatestable").DataTable( {
         "buttons": [
        "copy", "excel", "pdf"
    ],
    "order": [[ 1, "asc" ], [ 0, "asc" ]],
          "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 1 ],
                "visible": false,
                "searchable": false
            } ],           
        "stateSave": false,
        
        "displayLength": 50,

        "language": {
            "lengthMenu": "'.get_string('dt_lenghtmenu','block_eledia_adminexamdates').'",
            "zeroRecords": "'.get_string('dt_zerorecords','block_eledia_adminexamdates').'",
            "info": "'.get_string('dt_info','block_eledia_adminexamdates').'",
            "infoEmpty": "'.get_string('dt_infoempty','block_eledia_adminexamdates').'",
            "infoFiltered": "'.get_string('dt_infofiltered','block_eledia_adminexamdates').'",
                        "emptyTable": "'.get_string('dt_emptytable','block_eledia_adminexamdates').'",
            "infoPostFix": "'.get_string('dt_infopostfix','block_eledia_adminexamdates').'",
            "thousands": "'.get_string('dt_thousands','block_eledia_adminexamdates').'",
            "loadingRecords": "'.get_string('dt_loadingrecords','block_eledia_adminexamdates').'",
            "processing": "'.get_string('dt_processing','block_eledia_adminexamdates').'",
                        "search": "'.get_string('dt_search','block_eledia_adminexamdates').'",
                        "paginate": {
            "first": "'.get_string('dt_first','block_eledia_adminexamdates').'",
            "last": "'.get_string('dt_last','block_eledia_adminexamdates').'",
            "next": "'.get_string('dt_next','block_eledia_adminexamdates').'",
            "previous": "'.get_string('dt_previous','block_eledia_adminexamdates').'",
                        },
                         "aria": {
                         "sortAscending": "'.get_string('dt_sortascending','block_eledia_adminexamdates').'",
            "sortDescending": "'.get_string('dt_sortdescending','block_eledia_adminexamdates').'",
            }
        }
    } );
    
    // Refilter the table
    $("#min, #max").on("change", function () {
        table.draw();
    });
    
    $("#examdatestable").removeClass("dataTable");
  
 
} );
    </script>';
    echo $OUTPUT->container_end();
}


echo $OUTPUT->footer();


//$("#examdatestable tbody").on( "click", "tr.group", function () {
//    var currentOrder = table.order()[0];
//    if ( currentOrder[0] === groupColumn && currentOrder[1] === "asc" ) {
//        table.order( [ groupColumn, "desc" ] ).draw();
//    }
//    else {
//        table.order( [ groupColumn, "asc" ] ).draw();
//    }
//} );

