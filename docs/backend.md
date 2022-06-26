# Backend Architektur

Die Backend-Komponente von Schoala ist in PHP geschrieben und verwendet das Modell einer Remote Procedure Call API, die ausschließlich über **POST-Requests** arbeitet. Ausschlaggebend ist der POST-Parameter `method` über welchen die aufzurufende Methode bestimmt wird.

**Ordnerstruktur**
- `backend/db` - Datenbank Interface und Zugangsdaten
- `backend/logic` - Klassen, die Requests verarbeiten
- `backend/models` - Klassen, die Entitäten repräsentieren und Datenbank-Operationen durchführen.
- `backend/permissions` - Klasse zum Verwalten und prüfen von Berechtigungen

## Entry Point
Die Datei `backend/requestHandler.php` stellt den Entry-Point für jegliche API-Requests dar. Dort wird lediglich eine neue Instanz der Klasse `RequestHandler` erstellt und deren Methode `process()` aufgerufen.

## Verteilung der Requests
Die Singleton-Klasse `MainLogic` ist für die Verteilung der Requests über den POST-Parameter `method` zuständig. Je nach dessen Inhalt, wird eine entsprechende Methode der Business-Logik aufgerufen, die den Request letztendlich verarbeitet.

Außerdem wird zuvor auf alle POST-Parameter die Funktionen `trim()` und `stripslashes()` angewandt.

## Hub
Die Singleton-Klasse Hub stellt Methoden zur Verfügung, die es ermöglichen, effizient auf die Methoden der Logik-Klassen zuzugreifen, sowie einheitlich neue Instanzen von Model-Klassen zu erstellen.

Es ist lediglich in der Datei `backend/logic/mainLogic.php` das Statement `require "hub.php";` erforderlich. Es werden in den Logik und Model-Klassen dadurch keine weiteren `include`- oder `require`-Statements nötig.

Beispiel:
```php
Hub::Users()->login(); //ruft die Methode login() aus der Logik-Klasse "Users" auf

$newAssignment = Hub::Assignment() //returnt eine neue Instanz der Klasse "Assignment"
```

## Logik Klassen
Die Logik-Klassen `Users`, `Groups`, `Chats` und `Assignments` sind im Prinzip statisch, d.h. ohne Membervariablen, beinhalten also lediglich Methoden, die Parameter aus dem `$_POST`-Array verarbeiten.

Eine Methode jener Klassen ist typischerweise folgendermaßen aufgebaut:
 
1. Es wird überprüft, ob die für den Request benötigten Parameter richtig übergeben worden sind und ob sie bestimmten Datentypen (z.B. Array) entsprechen.

2. Es werden die Berechtigungen geprüft, ob ein Client die gewünschte Methode aufrufen darf.
Beispiel:
`Permissions::checkIsTeacher();`
Sollte die Überprüfung fehlschlagen, wird der Request sofort abgebrochen und eine entsprechende Fehlermeldung an den Client gesendet.

2. Es werden je nach Requests Instanzen von Model-Klassen erstellt, dessen Methoden aufgerufen, um Daten abzurufen oder abzuspeichern.

3. Es wird eine Response-Payload als assioziatives Array erstellt. Jene stellt den Rückgabewert dar, der dann letztendlich im JSON-Format an den Client gesendet wird.


## Error Handling
Die API verwendet lediglich zwei HTTP-Response-Codes: `200 OK` für einen erfolgreichen Request, und `400 BadRequest` für einen fehlgeschlagenen Request.

### 200 OK

Die Request Parameter waren korrekt und der Request konnte verarbeitet werden.

Bei beinahe allen Response-Objekten wird außerdem der Boolean-Wert `success` zurückgegeben, der vom Request kontextabhängig mitteilt, ob eine Operation wie gewünscht durchgeführt wurde. Meist ist dieser jedoch `true`.

### 400 BadRequest
Wenn ein Fehler in der Verarbeitung eines Requests auftritt, wird in der ensprechenden Methode eine Exception geworfen, die der RequestHandler dann als Error-Nachricht im Plaintext an den Client sendet.

Typischerweise treten Exceptions in folgenden Fällen auf:

- Die Request-Parameter sind falsch.
- Die Inhalte eines Parameters sind inkorrekt.
- Der Client besitzt keine Berechtigungen, eine Anfrage mit bestimmten Parametern durchzuführen.
- Eine Datenbankoperation schlug fehl. 

## Model Klassen

## Datenbank Interface

Das Backend verwendet `msqli`, um Datenbankoperationen durchzuführen. Sämtliche Datenbankabfragen werden über die Klassen `Database` durchgeführt. Für je eine der CRUD-Operationen existiert eine Methode.

Queries werden mit Hilfe von den Methoden `prepare()` und `bind_param()` vor SQL Injections geschützt.

### SELECT

Signatur:
```php
public static function select(string $query, array $params = null, string $param_types = null, bool $singleRow = null)
```

- Der Parameter `$query` beinhaltet eine valide SQL-Query und muss die Form `(select|SELECT) .+ (from|FROM) .+` haben. Parameter müssen als `?` angegeben werden.
- Das Array `$params` enthält die den `?` entsprechenden Werte der Parameter in derselben (!) Reihenfolge! Am besten werden sie beim Aufruf über ein implizites Array übergeben, z.B: `[$foo, $bar]`


### Datenbankzugang

Die zu verwendende Datenbank wird in der Datei `backend/dbaccess.php` durch vier Konstanten spezifiziert.

Aus Sicherheitsgründen existiert lediglich eine Datei `backend/dbaccess-template.php`, die lokal in `backend/dbaccess.php` kopiert werden muss. Letztere Datei wird von Git ignoriert, um unerwünschte Leaks von Datenbankzugangsdaten zu vermeiden.


## Businesslogik

