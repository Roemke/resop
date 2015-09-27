Resop - Version 0.1
========================
English Readme - see below.

Ein Moodle Modul um Klassenarbeiten (vielleicht irgenwann mal andere
Ressourcen) zu verwalten.

Das Modul ist für Moodle 2.9 entwickelt worden, Tests für ältere Versionen 
wurden nicht durchgeführt.

Fehler sind höchstwahrscheinlich noch vorhanden, das Modul ist nur kurz
getestet und ist meine erste Entwicklung eines Moduls. 

Hinzufügen des Moduls
-----------------------------------
Beim Hinzufügen des Moduls ist eine Liste von Abteilungen anzugeben. Aus
diesen kann später für eine Instanz des Moduls jeweils eine ausgewählt
werden. 

Die Liste der Ressourcen ist lediglich eine Vorgabe für später wählbare 
Ressourcen und enstpricht den Klassen. Jede Klasse wird als eine Ressource
angesehen. 
Später, beim Hinzufügen einer Instanz kann die Liste erweitert oder
reduziert werden.

Die Liste der Nutzer ist fest, d.h. hier müssen alle Nutzer aus allen
Abteilungen eingetragen werden. Beim Hinzufügen einer Instanz können die
Nutzer ausgewählt werden, die innerhalb der Instanz verwendet werden. Das
spätere Hinzufügen von Nutzern ist nicht möglich, dazu muss ein
Administrator die Einstellungen anpassen. 

Erstellen einer Instanz des Moduls
---------------------------------
Die üblichen Angaben müssen gesetzt werden Als Name bietet sich z.B.
Klassenarbeiten Abteilungsname an. 

Die Abteilung muss gewählt werden und die Ressourcen/Klassen sowie die Nutzer müssen
eingetragen werden. Bei den Ressourcen kann man die Liste frei erweitern.

Verwendung
------------------
Lehrer/Teacher können Termine ansehen und bearbeiten, Schüler/Teilnehmer können Termine
ansehen und haben auch dabei geringere Möglichkeiten. Sie können weder alle
Termine sehen noch die Termine eines Lehrers. Bei der Auswahl aus den
Klassen / Ressourcen sind sie allerdings frei, da keine Zuordnung von
Schülern zu Klassen existiert. 

Zu jeder Anzeige existiert ein iCal-Export, der Button verweist direkt auf
einen Download. Der Link kann auch kopiert und in ein Kalenderprogramm
eingefügt werden. 

 
Resop - Version 0.1 - english description
========================

A small Moodle Modul to plan written exams (maybe later some other
resources).

The modul is developed for Moodle 2.9. I don't now if earlier versions will
work. 

Surely there are some bugs in this modul. It is only slightly tested and
it's my first try to develop a moodle module. 

Add the Modul
-----------------------------------
When adding the modul you have to add a list of departments. From this
departements the trainer who add later an instance of the modul can choose
one departement.

The list of resources is only the set of resources which is preset later. 
Each school-class you should see as one resource. Later, when adding the
instance, the list of resources / classes can be extended or reduced. 

The list of users is fixed, that means here you should all users from all
departments. When adding the instance the teacher can choose which user
should be added to the list of bookers. Later adding of users is not
supported, but the administrator could adapt the list in the settings of the
modul. 


Creating an instance 
---------------------------------
You have to set the usual values for the modul. As name you should 
chouse something like written exams of departement. 

The departement have to be choosen and the resources/classes 
and the users must be set. The list of resources can be modified 
completely free - obviously you need at least one resource/class. 

Using
------------------
Teacher can add dates and insert/modify/delete them. 
Subscribers of a course  can only view the dates and have lesser rights. 
They can't see all dates and can't see the dates of a teacher /user.
But they can see the dates of each class, because there is no relation from 
pupils to classes realized in the database.  

Each view has an own link  to an ical-export. This link can also be copied
and inserted as url into a calendar program. 

 
