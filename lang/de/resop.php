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
 * German strings for resop
 * is located in lang/de of module - but nobody uses this structure
 * all strings are located in a separate lang dir of moodle data 
 * right way - don't know
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_resop
 * @copyright  2015 Karsten Roemke
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'Ressourcen Planer';
$string['modulenameplural'] = 'Ressource Planer';
$string['modulename_help'] = 'Modul um Resourcen zu planen, gedacht zunächst für Klassenarbeiten';
$string['resopfieldset'] = 'Feineinstellungen, wichtig';
$string['resopname'] = 'Name';
$string['resopname_help'] = 'Name des Planers (nach Belieben)';
$string['resop'] = 'resop';
$string['pluginadministration'] = 'resop administration';
$string['pluginname'] = 'Resourcen Planer';

//fuer die Settings admin / einzelne resop aktivität
$string['listofresources'] = 'Liste der Ressourcen'; //beides
$string['listofresources_default_help'] = 'Vorgabe für die Liste der ressourcen welche verfügbar sein sollen, '.
'Jede Zeile enthält einer Ressource.<br>'.
'Will man Klassenarbeiten verwalten, bietet es sich an, hier die Klassen aufzuführen.<br>'.
'Dies sind die vorgegebenen Einträge, diese können später für jedes Modul erweitert oder reduziert werden.'; 
//admin setting
$string['maxnumberexams']='Maximale Anzahl von Klassenarbeiten';
$string['maxnumberexams_help'] = 'Für den Typ Klassenarbeiten: Die Maximalzahl von Arbeiten, die pro Woche eingetragen werden können.';
$string['exportsalt']='Export salt ';
$string['exportsalt_help']='Dieser Zufallstext (hash salt) wird benutzt, um die Sicherheit der Authentifikationstoken für '. 
	'den Kalenderexport zu erhöhen. Bitte beachten Sie, dass alle aktuellen Token ungültig werden, ' . 
	'wenn Sie diesen Text ändern. Wenn Sie das Feld leer lassen, wird ein 60 Zeichen Zufallswert erstellt.';
$string['listofresources_help'] = 'Resourcen, die in dieser Aktivität verwendet werden können.<br>'.
								  'Sie können Elemente löschen und / oder hinzufügen. '; //module settings

$string['listofusers']="Nutzer";
$string['listofusers_help']='Nutzer, die eine Ressource buchen können, Multiselect sinnvoll.';

$string['listofdepartements']="Abteilungen";
$string['listofdepartements_help']='Abteilungen, vergleiche Resourcen';
$string['resoptypestring'] = 'Art';
$string['departement']='Abteilung';

//---------------------------------------
$string['display'] = 'Ressourcen Planer erstellen';
$string['contentheader'] = 'Inhalt';
$string['typeall'] = 'Beliebige Ressource wählbar (funktioniert noch nicht)';
$string['typeexam'] = 'Klassenarbeiten';
$string['abteilungen']="Abteilung wählen.";
$string['abtheader']="Abteilung";
//--------------- links in view.php / view.php  --------------
$string['showall']='Zeige alle Einträge';
$string['resExam']='Klassen';
$string['resFree']='Ressourcen';
$string['insert'] = 'Eintrag hinzufügen';
$string['entries'] = 'Einträge';
$string['nodata']='keine Einträge gefunden';
$string['class']='Klasse';
$string['bookedby'] = 'gebucht für ';
$string['bookedby_header']='gebucht für (über alle Instanzen)';
$string['termin'] = 'Termin';
$string['edit'] = 'bearbeiten';
$string['iCal'] = 'iCal Export';
$string['iCal_help'] = 'Ein iCal-File wird generiert. Sie können die Datei in Ihren Kalender importieren oder den Link kopieren '. 
      '(rechte Maustaste) und in einen anderen Kalender, der eine URL importieren kann, einfügen. ' . 
      ' Im Google-Kalender z.B. über Weitere Kalender - über URL hinzufügen.' ;
//--------------------------- Insert / Update Form
$string['insertClass']='Klasse';
$string['choose']='Bitte wählen!';
$string['chooseHelp']='Sie müssen eine Auswahl treffen!';
$string['insertUser']='gebucht für';
$string['kind']='Fach / Art / Raum';
$string['kind_help']='Bitte angeben, um welches Fach und welche Art z.B. KA oder Test oder Abgabe es sich handelt.';
$string['termin']="Start-Datum/Zeit";
$string['duration']="Dauer";
$string['saveback']="Speichern, zur Liste";
$string['savenext']="Speichern und nächster";
$string['update']='Aktualisieren';
$string['dateError'] = 'Datum in der Vergangenheit!';
$string['error3KA'] = 'Maximalzahl der Klassenarbeiten überschritten';	   
$string['errorOverlap'] = 'Terminüberschneidung';
$string['confirmdelete'] = 'Den Eintrag löschen?';

