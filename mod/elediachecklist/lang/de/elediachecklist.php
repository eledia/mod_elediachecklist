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
 * Strings for component elediachecklist, language 'de'
 *
 * @package   mod_elediachecklist
 * @copyright 2021 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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

$string['erinnerung_klausurvorbereitung'] = 'E-Mail, Erinnerung Klausurvorbereitung';
$string['erinnerung_klausurvorbereitung_beschreibung'] = 'Wählen Sie die Punkte aus der Termincheckliste aus, '
.'die in der E-Mail aufgeführt werden sollen.';

$string['erinnerung_klausurnachbereitung'] = 'E-Mail, Erinnerung Klausurnachbereitung';
$string['erinnerung_klausurnachbereitung_beschreibung'] = 'Wählen Sie die Punkte aus der Termincheckliste aus, '
.'die in der E-Mail aufgeführt werden sollen.';

