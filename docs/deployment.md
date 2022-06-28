# Deployment Anleitung

### Tech-Stack:

- **Apache** 2.4.x+
- **PHP** 7.3+
- **MariaDB** 10.4+ 

### Libraries
Alle Libraries werden standardmäßig per Content-Delivery-Network (CDN) eingebunden.
Wenn lokale Versionen der Libraries existieren, müssen lediglich die jeweiligen Pfade in der ``index.html``
angepasst werden.

- **jQuery** 3.6+ 
- **Bootstrap** 5+
- **Notyf** 3+

### 1. Klonen des Repositorys
Das Repository in das Verzeichnis des Web-Servers klonen.
```
$ git clone https://github.com/Oscavian/ss22-itp-g02.git 
```

### 2. Datenbank-Setup
- neue Datenbank mit beliebigem Namen anlegen
- das zur Verfügung gestellte SQL-Script `database.sql` importieren
- die Konstanten aus ``backend/db/dbaccess-template.php`` in ``backend/db/dbaccess.php`` kopieren und entsprechend definieren

#### Optional: Demo-Daten importieren

1. Importieren des SQL-Scripts ``docs/demo/demodata.sql``, ggf. die Fremdschlüsselüberprüfung deaktivieren.
2. Dateien aus ``docs/demo/attachments.zip`` in `uploads/assignments/attachments/` verschieben
3. Dateien aus ``docs/demo/submissions.zip`` in `uploads/assignments/submissions/` verschieben
4. Anmeldedaten aus ``docs/demo/README.md`` verwenden


### 3. Root-Path setzen
Standardmäßig ist der Pfad `http://localhost/ss22-itp-g02` als App-Root Verzeichnis eingestellt.
Wenn das Projekt an einem anderen Ort liegt, müssen folgende Variablen angepasst werden:

- Unter ``client/js/main.js`` muss die Konstante `rootPath` den vollständigen Pfad zur App beinhalten. 
Standard: `http://localhost/ss22-itp-g02` 
- Unter ``backend/logic/fileHandler.php`` muss die Variable `$upload_dir` dem Sub-Folder entsprechend angepasst werden, um das Upload-Verzeichnis festzulegen.
Standard: ``$_SERVER["DOCUMENT_ROOT"] . "/ss22-itp-g02/uploads/" . $target_dir;``

### 4. Fertig!
Die Anwendung kann nun genutzt werden, indem entweder ein neuer Teacher-Account angelegt wird oder Zugangsdaten aus den vorher importierten Demodaten verwendet werden.