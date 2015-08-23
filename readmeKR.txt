2015-07-12
nach Anleitung im github teil fuer neue module vorgegangen
dann die zusaetzlichen tabellen mit mysql-workbench erzeugt 
nach moodle geschoben und mdl_ als prefix dazu, dann 
tabelle aus mysql erzeugen, source jeweils die tabelle ausgewählt
dest leer gelassen
version.php regelmaessig erhoeht
am nächsten Tag war's weg - mist
2015-07-13
neu - dabei zwischen durch mal cache leeren
hatte primary keys vergessen

2015-07-14
erstellen der tabellen aus vorhandener funktioniert nicht 
verhalten unklar, versuche nochmal neu zu starten nenne modul resop
erst danach ist die Tabelle, die automatischangelegt wird wieder komplett

datetime soll als unix timestamp also int(10) gespeichert werden

beim aktualisieren der db noch ein fehler, in resource_user geht autosequence
nur, wenn primaer-schluessel -> im xml file geaendert

schiebe es mal auf github

2015-07-15 
experimentiere mit den lang-files, sie sollten nach resop/lang/de - andere
module haben sie unter moodleused/... ?

Caching abschalten: https://docs.moodle.org/dev/Developer_Mode

2015-08-09
erweiterung, fuege eine spalte in db hinzu, nein doch nicht noetig,
der user ist derjenige, für den eingetragen wird, also den Besitzer
der resource
hmm hatte in mod_form schon geaendert, dass man zwischen KA und "normaler
Ressource" wählen kann
(aenderungen an der sprachdatei funktionieren nicht - wahrscheinlich muss
man cache leeren, dachte waere aus) - ja ist so 

2015-08-22
viel gespielt, jetzt entschieden die aktuelle id eines resop-eintrags mit
in die Tabellen aufzunehmen und die konfiguration nicht global sondern 
pro Objekt zu machen, damit faellt Settings aus. ..
lasse die Datei mal drin damit ich mich daran erinnere
in den tabellen wird dann jeweils die id des aktuellen eintrags mit
uebernommen, so dass loeschen ohne weiteres funktioniert.
