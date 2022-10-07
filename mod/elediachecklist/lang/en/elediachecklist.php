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
 * Strings for component elediachecklist, language 'en'
 *
 * @package   mod_elediachecklist
 * @copyright 2021 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['unterschrift_eklausurteam'] = 'Unterschrift E-Klausurteam';
$string['unterschrift_verantwortlicher'] = 'Unterschrift Verantwortliche:r des Fachgebietes.';
$string['Es_wird_vom_Fachgebiet'] = 'Es wird vom Fachgebiet bestätigt, dass der Funktionstest durchgeführt und die rechtlichen Vorgaben zur Durchführung dieser E-Klausur beachtet wurden';
$string['Place_pdf'] = 'Kassel';

$string['tablehead_examname'] = 'Exam name';
$string['tablehead_checked'] = 'Checked';
$string['tablehead_topic'] = 'Bezeichnung';
$string['tablehead_topicdate'] = 'Datum';
$string['tablehead_id'] = 'ID';
$string['tablehead_examid'] = 'Exam ID';

$string['Dozent'] = 'Dozent';
$string['Klausur'] = 'Klausur';
$string['Datum'] = 'Datum';

$string['report_button'] = 'Report';

$string['start_date'] = 'Start date';
$string['end_date'] = 'End date';

$string['checkliste_mail_subject'] = 'Checkliste';

$string['KVB_mail_subject'] = 'E-Klausur: Erinnerung Klausurvorbereitung';
$string['KVB_mail_text'] = 'Sehr geehrte Damen und Herren,<br/><br/>

bitte denken Sie daran, für Ihre E-Klausur am {Datum}, {BEZEICHNUNG} , demnächst folgende Punkte zu erledigen.<br/><br/>

{ITEMS}

<br/>
Mit besten Grüßen<br/>
Ihr E-Klausurteam';

$string['KNB_mail_subject'] = 'E-Klausur: Erinnerung Klausurnachbereitung';
$string['KNB_mail_text'] = 'Sehr geehrte Damen und Herren,<br/><br/>

wir möchten Sie daran erinnern, dass Sie uns für Ihre E-Klausur am {Datum}, {BEZEICHNUNG} , zu folgenden Punkten Ihrer Termincheckliste noch keine Informationen gegeben haben. Wir bitten Sie, dies nachzuholen.<br/><br/>

{ITEMS}

<br/>
Mit besten Grüßen<br/>
Ihr E-Klausurteam';

$string['databaselink'] = '../data/edit.php?d=5';
$string['checklist_id'] = '82';

$string['addcomments'] = 'Add comments';

$string['additem'] = 'Add';
$string['additemalt'] = 'Add a new item to the list';
$string['additemhere'] = 'Insert new item after this one';
$string['addownitems'] = 'Add your own items';
$string['addownitems-stop'] = 'Stop adding your own items';

$string['allowmodulelinks'] = 'Allow module links';

$string['anygrade'] = 'Any';
$string['anygrouping'] = 'Any grouping';
$string['autopopulate'] = 'Show course modules in checklist';
$string['autopopulate_help'] = 'This will automatically add a list of all the resources and activities in the current course into the checklist.<br />
This list will be updated with any changes in the course, whenever you visit the \'Edit\' page for the checklist.<br />
Items can be hidden from the list, by clicking on the \'hide\' icon beside them.<br />
To remove the automatic items from the list, change this option back to \'No\', then click on \'Remove course module items\' on the \'Edit\' page.';
$string['autoupdate'] = 'Check-off when modules complete';
$string['autoupdate2'] = 'Check-off when courses or modules complete';
$string['autoupdate_help'] = 'This will automatically check-off items in your checklist when you complete the relevant activity in the course.<br />
If completion tracking is switched on for a particular activity, that will be used to tick-off the item in the list.<br>
Otherwise, \'completing\' an activity varies from one activity to another - \'view\' a resource, \'submit\' a quiz or assignment, \'post\' to a forum or join in with a chat, etc. (for details of exactly what causes an activity to be marked as \'complete\', ask your site administrator to look in the file \'mod/elediachecklist/classes/local/autoupdate.php\')<br>';
$string['autoupdate2_help'] = 'This will automatically check-off items in your checklist when you complete the relevant activity in the course.<br />
If completion tracking is switched on for a particular activity, that will be used to tick-off the item in the list.<br>
Otherwise, \'completing\' an activity varies from one activity to another - \'view\' a resource, \'submit\' a quiz or assignment, \'post\' to a forum or join in with a chat, etc. (for details of exactly what causes an activity to be marked as \'complete\', ask your site administrator to look in the file \'mod/elediachecklist/classes/local/autoupdate.php\')<br>
If an item is linked to a course and that course has completion enabled for it, then the item will be updated when that course is marked as complete.';
$string['autoupdatenote'] = 'It is the \'student\' mark that is automatically updated - no updates will be displayed for \'Teacher only\' checklists';

$string['autoupdatewarning_both'] = 'There are items on this list that will be automatically updated (as students complete the related activity). However, as this is a \'student and teacher\' checklist the progress bars will not update until a teacher agrees the marks given.';
$string['autoupdatewarning_student'] = 'There are items on this list that will be automatically updated (as students complete the related activity).';
$string['autoupdatewarning_teacher'] = 'There are items on this list that will be automatically updated (as students complete the related activity).';

$string['canceledititem'] = 'Cancel';

$string['calendardescription'] = 'This event was added by the checklist: {$a}';

$string['changetextcolour'] = 'Next text colour';

$string['checkeditemsdeleted'] = 'Checked items deleted';

$string['checklist'] = 'checklist';
$string['pluginadministration'] = 'Checklist administration';

$string['checklist:addinstance'] = 'Add a new checklist';
$string['checklist:edit'] = 'Create and edit checklists';
$string['checklist:emailoncomplete'] = 'Receive completion emails';
$string['checklist:preview'] = 'Preview a checklist';
$string['checklist:updatelocked'] = 'Update locked checklist marks';
$string['checklist:updateother'] = 'Update students\' checklist marks';
$string['checklist:updateown'] = 'Update your checklist marks';
$string['checklist:viewmenteereports'] = 'View mentee progress (only)';
$string['checklist:viewreports'] = 'View students\' progress';
$string['checklistautoupdate'] = 'Allow checklists to automatically update';

$string['checklistfor'] = 'Checklist for';

$string['checklistintro'] = 'Introduction';
$string['checklistsettings'] = 'Settings';

$string['checks'] = 'Check marks';
$string['choosecourse'] = 'Choose course...';
$string['comments'] = 'Comments';

$string['completiondetail:percent'] = 'Check-off items: {$a}%';
$string['completiondetail:items'] = 'Check-off items: {$a}';
$string['completionpercentgroup'] = 'Require checked-off';
$string['completionpercentgroup_help'] = 'If \'percent of items\' is selected, then users must check-off at least the specified percentage of the checklist items to be considered \'complete\'. If \'items\' is selected, then the user must check-off at least that many individual items in the list. Note if you specify a number of items here that is greater than the number of items in the checklist, then it will never be marked as complete.';
$string['completionpercent'] = 'Amount of items that should be checked-off:';

$string['configchecklistautoupdate'] = 'Before allowing this you must make a few changes to the core Moodle code, please see mod/elediachecklist/README.txt for details';
$string['configshowupdateablemymoodle'] = 'If this is checked then only updatable Checklists will be shown from the \'My Moodle\' page';
$string['configshowcompletemymoodle'] = 'If this is unchecked then completed Checklists will be hidden from the \'My Moodle\' page';
$string['configshowmymoodle'] = 'If this is unchecked then Checklist activities (with progress bars) will no longer appear on the \'My Moodle\' page';

$string['confirmdeleteitem'] = 'Are you sure you want to permanently delete this checklist item?';

$string['deleteitem'] = 'Delete this item';

$string['duedatesoncalendar'] = 'Add due dates to calendar';

$string['edit'] = 'Edit checklist';
$string['editchecks'] = 'Edit checks';
$string['editdatesstart'] = 'Edit dates';
$string['editdatesstop'] = 'Stop editing dates';
$string['edititem'] = 'Edit this item';

$string['emailoncomplete'] = 'Email when checklist is complete:';
$string['emailoncomplete_help'] = 'When a checklist is complete, a notification email can be sent: to the student who completed it, to all the teachers on the course or to both.<br />
An administrator can control who receives this email using the capability \'mod:checklist/emailoncomplete\' - by default all teachers and non-editing teachers have this capability.';
$string['emailoncompletesubject'] = 'User {$a->user} has completed checklist \'{$a->checklist}\'';
$string['emailoncompletesubjectown'] = 'You have completed checklist \'{$a->checklist}\'';
$string['emailoncompletebody'] = 'User {$a->user} has completed checklist \'{$a->checklist}\' in the course \'{$a->coursename}\'
View the checklist here:';
$string['emailoncompletebodyown'] = 'You have completed checklist \'{$a->checklist}\' in the course \'{$a->coursename}\'
View the checklist here:';
$string['enterurl'] = 'Enter url ...';
$string['eventchecklistcomplete'] = 'Checklist complete';
$string['eventeditpageviewed'] = 'Edit page viewed';
$string['eventreportviewed'] = 'Report viewed';
$string['eventstudentchecksupdated'] = 'Student checks updated';
$string['eventteacherchecksupdated'] = 'Teacher checks updated';

$string['export'] = 'Export items';

$string['forceupdate'] = 'Update checks for all automatic items';

$string['gradetocomplete'] = 'Grade to complete:';
$string['grouping'] = 'Visible to grouping';
$string['guestsno'] = 'You do not have permission to view this checklist';

$string['headingitem'] = 'This item is a heading - it will not have a checkbox beside it';

$string['import'] = 'Import items';
$string['importfile'] = 'Choose file to import';
$string['importfromsection'] = 'Current section';
$string['importfromcourse'] = 'Whole course';
$string['indentitem'] = 'Indent item';
$string['itemcomplete'] = 'Completed';
$string['items'] = 'Checklist items';
$string['itemstype'] = 'Items';

$string['linkcourses'] = 'Allow linking items to courses';
$string['linkcourses_desc'] = 'When enabled, checklist items can be linked to courses within Moodle - being marked as complete when the associated course is completed. Enabling this may have some performance implications when editing checkist items on sites with a large number of courses.';
$string['linkto'] = 'Link to';
$string['linktocourse'] = 'Course associated with this item';
$string['linktomodule'] = 'Activity associated with this item';
$string['linktourl'] = 'Link associated with this item';

$string['lockteachermarks'] = 'Lock teacher marks';
$string['lockteachermarks_help'] = 'When this setting is enabled, once a teacher has saved a \'Yes\' mark, they will be unable to change it. Users with the capability \'mod/elediachecklist:updatelocked\' will still be able to change the mark.';
$string['lockteachermarkswarning'] = 'Note: Once you have saved these marks, you will be unable to change any \'Yes\' marks';

$string['modulename'] = 'eLeDia Checklist';
$string['modulename_help'] = 'The checklist module allows a teacher to create a checklist / todo list / task list for their students to work through.';
$string['modulename_link'] = 'mod/elediachecklist/view';
$string['modulenameplural'] = 'Checklists';

$string['moveitemdown'] = 'Move item down';
$string['moveitemup'] = 'Move item up';

$string['noitems'] = 'No items in the checklist';

$string['onlyenrolled'] = 'Only active users';
$string['onlyenrolleddesc'] = 'When selected, only users with active enrolments are shown in checklists (deselect to return to the old behaviour)';
$string['openlinkinnewwindow'] = 'Open in new window?';
$string['optionalitem'] = 'This item is optional';
$string['optionalhide'] = 'Hide optional items';
$string['optionalshow'] = 'Show optional items';
$string['or'] = 'OR';

$string['percent'] = 'Percent of items';
$string['percentcomplete'] = 'Required items';
$string['percentcompleteall'] = 'All items';
$string['pluginname'] = 'eLeDia Checklist';
$string['preview'] = 'Preview';

$string['privacy:metadata:checklist_check'] = 'Information about which checklist items have been checked off by the user (or checked off by a teacher for the user) on a given checklist';
$string['privacy:metadata:checklist_check:item'] = 'The ID of the checklist item that has been checked-off';
$string['privacy:metadata:checklist_check:teacherid'] = 'The ID of the teacher who last updated the status of this item';
$string['privacy:metadata:checklist_check:teachermark'] = 'The status allocated to this item, for the user, by a teacher';
$string['privacy:metadata:checklist_check:teachertimestamp'] = 'The time when the teacher last allocated a status to this item';
$string['privacy:metadata:checklist_check:userid'] = 'The ID of the user who has checked-off the item (or, if marked by a teacher, the user it was checked-off for)';
$string['privacy:metadata:checklist_check:usertimestamp'] = 'The time when the user last checked-off the item (0 if the item is currently unchecked)';
$string['privacy:metadata:checklist_comment'] = 'Information about comments that have been made by a teacher about the user\'s progress on a given checklist';
$string['privacy:metadata:checklist_comment:commentby'] = 'The ID of the user (teacher) that made this comment';
$string['privacy:metadata:checklist_comment:itemid'] = 'The ID of the checklist item that the comment relates to';
$string['privacy:metadata:checklist_comment:text'] = 'The text of the comment';
$string['privacy:metadata:checklist_comment:userid'] = 'The ID of the user (student) that this comment relates to';
$string['privacy:metadata:checklist_item'] = 'Information about private items the user has added to the given checklist';
$string['privacy:metadata:checklist_item:checklist'] = 'The ID of the checklist this private item was added to';
$string['privacy:metadata:checklist_item:displaytext'] = 'The text of the private checklist item';
$string['privacy:metadata:checklist_item:userid'] = 'The ID of the user who created this private item (0 for items that are created by a teacher and shown to all users)';

$string['progress'] = 'Progress';

$string['removeauto'] = 'Remove course module items';

$string['report'] = 'View progress';
$string['reporttablesummary'] = 'Table showing the items on the checklist that each student has completed';

$string['requireditem'] = 'This item is required - it must be completed';

$string['resetchecklistprogress'] = 'Reset checklist progress and user items';

$string['savechecks'] = 'Save';

$string['showcompletemymoodle'] = 'Show completed Checklists on \'My Moodle\' page';
$string['showfulldetails'] = 'Show full details';
$string['showhidechecked'] = 'Show/hide selected items';
$string['showupdateablemymoodle'] = 'Show only updatable Checklists on \'My Moodle\' page';
$string['showmymoodle'] = 'Show Checklists on \'My Moodle\' page';
$string['showprogressbars'] = 'Show progress bars';
$string['showcolorchooser'] = 'Display color chooser';
$string['showcolorchooserdesc'] = 'Displays the color chooser of the list elements in edit.php';

$string['teachercomments'] = 'Teachers can add comments';
$string['teacherdate'] = 'Date a teacher last updated this item';

$string['teacheredit'] = 'Updates by';
$string['teacherid'] = 'The teacher who last updated this mark';

$string['teachermarkundecided'] = 'Teacher has not yet marked this';
$string['teachermarkyes'] = 'Teacher states that you have completed this';
$string['teachermarkno'] = 'Teacher states that you have NOT completed this';

$string['teachernoteditcheck'] = 'Student only';
$string['teacheroverwritecheck'] = 'Teacher only';
$string['teacheralongsidecheck'] = 'Student and teacher';

$string['togglecolumn'] = 'Toggle Column';
$string['toggledates'] = 'Toggle names & dates';
$string['togglerow'] = 'Toggle Row';
$string['theme'] = 'Checklist display theme';

$string['updatecompletescore'] = 'Save completion grades';
$string['unindentitem'] = 'Unindent item';
$string['updateitem'] = 'Update';
$string['userdate'] = 'Date the user last updated this item';
$string['useritemsallowed'] = 'User can add their own items';
$string['useritemsdeleted'] = 'User items deleted';

$string['view'] = 'View checklist';
$string['viewall'] = 'View all students';
$string['viewallcancel'] = 'Cancel';
$string['viewallsave'] = 'Save';

$string['viewsinglereport'] = 'View progress for this user';
$string['viewsingleupdate'] = 'Update progress for this user';

$string['yesnooverride'] = 'Yes, cannot override';
$string['yesoverride'] = 'Yes, can override';



$string['ueberschrift_01'] = 'Ihre E-Klausur Termincheckliste';
$string['dozent'] = 'Dozent_in';
$string['bezeichnung_klausur'] = 'Bezeichnung Klausur';
$string['klausurtermin'] = 'Klausurtermin';
$string['name_scl_betreuer'] = 'Name SCL Betreuer_in';
$string['erwartetet_anzahl_prueflinge'] = 'Erwartete Anzahl Prüflinge';
$string['zeitraum_der_raumbuchung'] = 'Zeitraum der Raumbuchung';

$string['text_pdf_intro'] = 'Sehr geehrte Damen und Herren,<br /><br />'
        .'wir möchten gewährleisten, dass Ihre E-Klausur organisatorisch und technisch reibungslos abläuft. '
        .'Zu diesem Zweck haben wir für Sie die folgende Checkliste mit verbindlichen Aufgaben erstellt. '
        .'Bitte stellen Sie sicher, dass die benannten Aufgaben von Ihrer Seite zu den angegebenen Terminen erfüllt werden.';

//-----

$string['text_pdf_01'] = '<b>Beschreibung der Prüfungskonfiguration</b><br /><br />'
.'Teilen Sie uns bitte die gewünschte Prüfungskonfiguration mit. Z.B.'
.'<ul style="">'
.'<li>Sie benötigen einen PDF-Reader</li>'
.'<li>Sie möchten den Studierenden am Bildschirm einen kleinen Taschenrechner zur Verfügung stellen.</li>'
.'<li>Sie möchten, dass Ihre Studierenden während der Klausur eine weitere Anwendung nutzen.</li>'
.'</ul>'
.'Hinweis! Je früher Sie uns Ihren Konfigurationswunsch mitteilen, desto größer ist die Chance, dass wir diesen umsetzen können.';

$string['text_pdf_02'] = '<b>Fertigstellung und Funktionstest der Klausur</b><br /><br />'
.'Prüfen Sie Ihre Fragen auf folgende Eigenschaften:'
.'<ul style="">'
.'<li>Die Summe der Punkte bei einer Aufgabe darf kein negativer Wert möglich sein. Dies ist prüfungsrechtlich nicht zulässig.</li>'
.'<li>In Word erstellte Textbausteine wurden vor dem Einfügen in eine Frage oder ein Assessment von dem in Word automatisch vorhandenen Quellcode befreit. Der Quellcode könnte die Funktion der Fragen unerwartet beeinflussen.</li>'
.'<li>Alle Fragen wurden auf eine korrekte Punkteberechnung hin geprüft.</li>'
.'</ul>'
.'Bevor Sie Ihre Klausur zur Qualitätskontrolle dem E-Klausur-Team bereitstellen, ist ein interner Funktionstest obligatorisch, um Fehler oder fehlende Funktionalitäten frühzeitig zu erkennen.'
.'<ul style="">'
.'<li>Generieren Sie ein "richtiges Prüfungsergebnis". Eine Anleitung finden Sie in den Schulungsunterlagen auf dem Prüfungssystem.</li>'
.'<li>Bei <b>Zufallsauswahl</b> von Fragen ist zusätzlich eine Testklausur mit allen Fragen zu erstellen und zu erproben.</li>'
.'<li>Wenn Sie beim Funktionstest Unterstützung wünschen, wenden Sie sich bitte an das E-Klausur-Team.</li>'
.'</ul>'
.'Bei der Endabnahme bestätigen Sie, dass Sie die o. g. Punkte geprüft haben.';

$string['text_pdf_03'] = '<b>Bereitstellung der Klausur für Anpassungen und Qualitätskontrolle</b><br /><br />'
.'An jeder Klausur nehmen wir einige manuelle Anpassungen vor:<br />'
.'Aktivierung einer manipulationssicheren Prüfungsumgebung, Sicherheitseinstellung für den Fall einer Prüfungsunterbrechung u.a.m.<br />'
.'Außerdem überprüfen wir Ihre Klausur hinsichtlich der Funktionalität. '
.'Informieren Sie uns kurz über das Ankündigungsforum Ihres Prüfungskurses oder per E-Mail über die Bereitstellung der Klausur. '
.'Wir geben Ihnen gerne eine Rückmeldung auf die prüfungsdidaktische Gestaltung Ihrer Klausur. Wenn Sie prüfungsdidaktisch von uns beraten werden möchten, lassen Sie uns dies bitte frühzeitig wissen. ';

$string['text_pdf_04'] = '<b>Endabnahme E-Klausur</b><br /><br />'
.'Im Beisein des/der Lehrenden oder eines/r autorisierten Vertreters:in wird die Klausur unter Prüfungsbedingungen getestet. '
.'Im Vordergrund steht der Funktionstest Ihrer Klausur in der originären Prüfungsumgebung nach dem Vier-Augen-Prinzip:'
.'<ul style="">'
.'<li>Alle "kritischen" Einstellungen werden überprüft.</li>'
.'<li>Bei Zufallsauswahl von Fragen wird eine Testklausur mit allen Fragen erprobt.</li>'
.'<li>Die korrekte Archiverstellung wird überprüft.</li>'
.'<li>Die Funktion der manuellen Bewertung von Freitextfragen wird überprüft.</li>'
.'<li>Der Ergebnisexport wird überprüft.</li>'
.'</ul>'
.'Es wird ein Prüfprotokoll erstellt und von den Beteiligten der Endabnahme unterschrieben. '
.'Setzen Sie sich bitte zur detaillierten Terminabsprache mit uns in Verbindung!';

$string['text_pdf_05'] = '<b>HIS-Liste an E-Klausurteam senden</b><br /><br />'
.'Wir benötigen von Ihnen bzw. Ihrem Sekretariat eine Teilnehmerliste der - in HIS - angemeldeten Prüflinge im Excel-Format. '
.'Lassen Sie uns über das Ankündigungsforum Ihres Prüfungskurses eine Nachricht mit der Liste als Anhang zukommen oder senden Sie uns eine E-Mail.'
.'<ul style="">'
.'<li>Diese Liste sollte mindestens die Namen, Vornamen und Matrikelnummern der Studierenden enthalten.</li>'
.'<li>Personen mit Nachteilsausgleich / Zugehörige der COVID-19-Risikogruppe sollten in der Liste gekennzeichnet sein.</li>'
.'<li>Wir ergänzen die Listen mit Passwörtern und erstellen Etiketten mit den Zugangsdaten, die jede/r Teilnehmer:in der Klausur am Eingang zum E-Assessmentcenter erhält.</li>'
.'</ul>';

$string['text_pdf_06'] = '<b>Gruppeneinteilung mit Namen und Matrikelnummer</b><br /><br />'
.'Bitte beachten Sie: Unter Corona-Bedingungen erstellt das E-Klausurteam eine eventuell erforderliche Gruppeneinteilung und stimmt diese mit Ihnen ab.';

$string['text_pdf_07'] = '<b>Namen und Anzahl der Aufsichtspersonen, Studierendeninformationen</b><br /><br />'
.'Bitte gewährleisten Sie, dass von Ihrem Fachgebiet mindestens eine Person pro Klausurraum zur Klausuraufsicht '
.'vor Ort ist, die eine fachliche Aufsicht gewährleisten kann (studentische Hilfskräfte oder Mitarbeiter:innen '
.'aus Sekretariaten können diese Aufsicht personell verstärken). Diese Person ist bitte 30 Minuten vor Einlass '
.'der Studierenden im E-Assessmentcenter. Beachten Sie bitte Folgendes: Sollte ein Student/eine Studentin '
.'in einem separaten Raum z.B. im E-Assessmentcenter schreiben, ist für diese Person eine eigene Aufsichtsperson '
.'erforderlich. Teilen Sie uns bitte bis zum angegebenen Zeitpunkt Namen und E-Mail- Adresse aller eingeplanten '
.'Aufsichtspersonen mit.<br />'
.'Informieren Sie Ihre Studierenden, dass diese mit Studierenden- und Lichtbildausweis pünktlich zur '
.'Einlasszeit am E-Assessmentcenter erscheinen. Folgende umfassende Informationen zu E-Klausuren finden '
.'Ihre Studierenden auf der Website unikassel.de/go/eklausur-studis:'
.'<ul style="">'
.'<li>Eine Wegbeschreibung zum E-Assessmentcenter und ggf. anderer genutzter Räumlichkeiten (z.B. in der Kurt-Wolters-Str. 5)</li>'
.'<li>Das Video "Einführung für Studierende" zum Ablauf und zur Durchführung einer Klausur</li>'
.'<li>Die Möglichkeit des Schreibens einer Probeklausur, um die Prüfungsumgebung kennenzulernen</li>'
.'</ul>';

$string['text_pdf_08'] = '<b>Mitteilung an E-Klausurteam: Klausureinsicht und Korrekturen abgeschlossen</b><br /><br />'
.'Teilen Sie uns mit, wenn Sie<br />'
.'(1) Freitextfragen manuell bewertet haben. Wir stellen Ihnen dann aktualisierte PDF-Dokumente für die Klausureinsicht zur Verfügung.<br />'
.'(2) Nachkorrekturen an den automatisierten Bewertungen vorgenommen haben. Wir stellen Ihnen dann aktualisierte PDF-Dokumente für die Klausureinsicht zur Verfügung.<br />'
.'(3) die Klausureinsicht abgeschlossen haben. Sollte es in Folge der Klausureinsicht zu Bewertungsänderungen kommen, teilen Sie uns dies bitte ebenfalls mit, damit wie diese Änderungen abschließend archivieren können.';

$string['text_pdf_09'] = '<b>Notenschlüssel bereitgestellt</b><br /><br />'
.'Senden Sie uns Ihren Notenschlüssel zu. Diesen müssen mir zusammen mit Ihrer Klausur archivieren.';



//$string['checkliste_mail_text_02'] = 'Sehr geehrte Damen und Herren,<br/><br/>'
//.'bitte denken Sie daran, für Ihre Checkliste am {Datum}, {BEZEICHNUNG}.<br/><br/>'
//.'<br/>'
//.'Mit besten Grüßen<br/>'
//.'Ihr E-Klausurteam<br /><br />'
//.'E-Klausuren an der Universität Kassel<br />'
//.'https://www.uni-kassel.de/go/eklausur';

$string['checkliste_mail_text'] = 'Sehr geehrte Damen und Herren,<br/><br/>'
.'wir möchten gewährleisten, dass Ihre E-Klausur organisatorisch und technisch reibungslos abläuft. '
.'Zu diesem Zweck haben wir für Sie eine Checkliste mit verbindlichen Aufgaben erstellt. '
.'Bitte stellen Sie sicher, dass die benannten Aufgaben von Ihrer Seite zu den angegebenen Terminen erfüllt werden. '
.'Sie finden die Checkliste im Anhang.<br /><br />'
.'Mit besten Grüßen<br/>'
.'Ihr E-Klausurteam<br /><br />'
.'E-Klausuren an der Universität Kassel<br />'
.'https://www.uni-kassel.de/go/eklausur';

//$string['checkliste_mail_text_03'] = 'Sehr geehrte Damen und Herren,<br/><br/>'
//.'in der Anlage übersenden wir Ihnen für Ihre E-Klausur "{BEZEICHNUNG} am {Datum} Ihre Termincheckliste zur Klausur.<br /><br />" '
//.'Mit besten Grüßen<br/>'
//.'Ihr E-Klausurteam<br /><br />'
//.'E-Klausuren an der Universität Kassel<br />'
//.'https://www.uni-kassel.de/go/eklausur';

$string['erinnerung_kvb'] = 'E-Mail, Erinnerung Klausurvorbereitung';
$string['erinnerung_kvb_beschreibung'] = 'Wählen Sie die Punkte aus der Termincheckliste aus, '
.'die in der E-Mail aufgeführt werden sollen.';

$string['erinnerung_knb'] = 'E-Mail, Erinnerung Klausurnachbereitung';
$string['erinnerung_knb_beschreibung'] = 'Wählen Sie die Punkte aus der Termincheckliste aus, '
.'die in der E-Mail aufgeführt werden sollen.';

$string['kein_scl_verantwortlicher_genannt'] = 'Es ist kein SCL-Verantwortlicher genannt. Es wurde keine E-Mail versendet.';

