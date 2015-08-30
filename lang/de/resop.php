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
'Fügt man eine Resop-Aktivität hinzu kann man die Liste erweitern'; //admin setting
$string['listofresources_help'] = 'Resourcen, die in dieser Aktivität verwendet werden können.'; //module settings

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



