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

## Session Handling

//TODO

## Hub
Die Singleton-Klasse Hub stellt Methoden zur Verfügung, die es ermöglichen, effizient auf die Methoden der Logik-Klassen zuzugreifen, sowie einheitlich neue Instanzen von Model-Klassen zu erstellen.

Es ist lediglich in der Datei `backend/logic/mainLogic.php` das Statement `require "hub.php";` erforderlich. Es werden in den Logik- und Model-Klassen dadurch keine weiteren `include`- oder `require`-Statements nötig.

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

3. Es wird eine Response-Payload als assoziatives Array erstellt. Jene stellt den Rückgabewert dar, der dann letztendlich im JSON-Format an den Client gesendet wird.


## Model Klassen
Die Model-Klassen `Assignment`, `Chat`, `Group`, `Message`, `Submission` und `Assignment` stellen Entitäten der Datenmodelle dar, ähnlich wie sie im Datenbankmodell abgebildet sind.
Nur diese Komponenten interagieren mit dem Datenbank-Interface.

Dem Konstruktor kann eine ID übergeben werden, die die Instanz mit der entsprechenden ID aus der Datenbank initialisiert.
Wenn keine ID übergeben wird, oder die ID ungültig ist, ist die Instanz "leer".

Es existieren für alle Attribute der Entität entsprechende Getter, die das gewünschte Feld aus der Datenbank laden, im Objekt speichern und zurückgeben.
Jede Klasse enthält außerdem eine Methode, um einen neuen Datensatz in der Datenbank anzulegen sowie einige spezifische Methoden.


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

## Permissions

Die statische Klasse ``Permissions`` beinhaltet verschiedene Methoden, um zentral auf bestimmte Berechtigungen zu prüfen.
Wenn eine Prüfung fehlschlägt, wird eine Exception geworfen, ansonsten passiert nichts.

- `checkIsLoggedIn()` - überprüft, ob die `$_SESSION`-Variable `userId` gesetzt ist
- `checkIsTeacher()` - überprüft, ob der Usertyp des eingeloggten Users gleich 1 (= Lehrer*in) ist. Überprüft auch den Loginstatus.
- `checkIsStudent()` - überprüft, ob der Usertyp des eingeloggten Users gleich 2 (= Schüler*in) ist. Überprüft auch den Loginstatus.
- `checkIsInGroup(Group $group)` - überprüft, ob der eingeloggte User in der übergebenen Gruppe ist. Überprüft auch den Loginstatus.
- `checkCanAccessAssignment(Assignment $assignment)` - überprüft, ob der eingeloggte User in der Gruppe des übergebenen Assignments ist. Überprüft auch den Loginstatus.
- `checkCanAssignUserToGroup(User $otherUser, Group $group)` - überprüft, ob der übergebene User in die angegebene Gruppe hinzugefügt werden kann; funktioniert nur, wenn der eingeloggte User auch in der übergebenen Gruppe ist. Kann nur vom Lehrer*innen aufgerufen werden.
- `checkIsInGroupWith(User $user, User $otherUser)` - überprüft, ob `$user` und `$otherUser` in derselben Gruppe sind.

## Datenbank Interface

Das Backend verwendet `msqli`, um Datenbankoperationen durchzuführen. Sämtliche Datenbankabfragen werden über die Klassen `Database` durchgeführt. Für je eine der CRUD-Operationen existiert eine Methode.

Queries werden mithilfe von den Methoden `prepare()` und [`bind_param()`](https://www.php.net/manual/en/mysqli-stmt.bind-param) vor SQL Injections geschützt.

### SELECT

Ruft Datensätze aus der Datenbank ab.

#### Signatur

```php
public static function select(string $query, array $params = null, string $param_types = null, bool $singleRow = null)
```
#### Parameter

- Der Parameter `$query` beinhaltet eine valide SQL-Query und muss die Form `(select|SELECT) .+ (from|FROM) .+` haben. Parameter müssen als `?` angegeben werden.
- Das (optionale) Array `$params` enthält die den `?` entsprechenden Werte der Parameter in derselben (!) Reihenfolge! Am besten werden sie beim Aufruf über ein implizites Array übergeben, z.B: `[$foo, $bar]`
- Der (optionale) String ``$param_types`` enthält die Datentypen der im vorherigen Array angegebenen Parameter in richtiger (!) Reihenfolge. `i` für Integer, `s` für String.
- Die (optionale) Flag ``singleRow`` soll auf ``true`` gesetzt werden, wenn explizit ein einziger Datensatz erwartet wird. Dann wird lediglich der erste Datensatz als assoziatives Array zurückgegeben. Dadurch ist es nicht mehr nötig, explizit auf das Element 0 zuzugreifen.

#### Rückgabewerte

- ``false``, wenn die Datenbankabfrage aus irgendeinem Grund fehlschlägt.
- Assoziatives Array, wenn ``singleRow == true``
- leeres Array, wenn keine Datensätze gefunden wurden.
- Array aus Objekten, die je einen Datensatz beinhalten


### INSERT

Erstellt einen neuen Datensatz in der Datenbank.

#### Signatur
````php
public static function insert(string $query, array $params, string $param_types): ?int
````

#### Parameter

Der Parameter `$query` beinhaltet eine valide SQL-Query und muss die Form `(insert|INSERT) (INTO|into) .+ (VALUES|values) \(.*\)` haben. Parameter müssen als `?` angegeben werden.
- Das Array `$params` enthält die den `?` entsprechenden Werte der Parameter in derselben (!) Reihenfolge! Am besten werden sie beim Aufruf über ein implizites Array übergeben, z.B: `[$foo, $bar]`
- Der String ``$param_types`` enthält die Datentypen der im vorherigen Array angegebenen Parameter in richtiger (!) Reihenfolge. `i` für Integer, `s` für String.

#### Rückgabewerte

- Die ID des neu erzeugten Datensatzes als ``int``
- ``null``, wenn das Statement fehlschlug.

### UPDATE

Ändert Daten in bestehenden Datensätzen.

#### Signatur

````php
public static function update(string $query, array $params = null, string $param_types = null): bool
````

#### Parameter

- Der Parameter `$query` beinhaltet eine valide SQL-Query und muss die Form `(update|UPDATE) .+ (SET|set) .+` haben. Parameter müssen als `?` angegeben werden.
- Das Array `$params` enthält die den `?` entsprechenden Werte der Parameter in derselben (!) Reihenfolge! Am besten werden sie beim Aufruf über ein implizites Array übergeben, z.B: `[$foo, $bar]`
- Der String ``$param_types`` enthält die Datentypen der im vorherigen Array angegebenen Parameter in richtiger (!) Reihenfolge. `i` für Integer, `s` für String.

#### Rückgabewerte

`true` bei Erfolg, `false` bei einem Fehler. 

### DELETE

Entfernt Datensätze aus der Datenbank.

#### Signatur

````php
public static function delete(string $query, array $params = null, string $param_types = null): bool
````

#### Parameter

- Der Parameter `$query` beinhaltet eine valide SQL-Query und muss die Form `(delete|DELETE) (FROM|from) .+` haben. Parameter müssen als `?` angegeben werden.
- Das Array `$params` enthält die den `?` entsprechenden Werte der Parameter in derselben (!) Reihenfolge! Am besten werden sie beim Aufruf über ein implizites Array übergeben, z.B: `[$foo, $bar]`
- Der String ``$param_types`` enthält die Datentypen der im vorherigen Array angegebenen Parameter in richtiger (!) Reihenfolge. `i` für Integer, `s` für String.

#### Rückgabewerte

`true` bei Erfolg, `false` bei einem Fehler.

### Datenbankzugang

Die zu verwendende Datenbank wird in der Datei `backend/dbaccess.php` durch vier Konstanten spezifiziert.

Aus Sicherheitsgründen existiert lediglich eine Datei `backend/dbaccess-template.php`, die lokal in `backend/dbaccess.php` kopiert werden muss. Letztere Datei wird von Git ignoriert, um unerwünschte Leaks von Datenbankzugangsdaten zu vermeiden.

## File Handler

Statische Klasse, die eine Methode zum Dateiupload bereitstellt.

Standardmäßig werden alle Dateien in das Verzeichnis `ROOT_DIR . /uploads/` hochgeladen.

Die maximale Dateigröße beträgt standardmäßig 10MB.

### Signatur
````php
public static function uploadFile(string $param, string $target_dir, array $file_types = null): string
````

### Parameter

- `$param` - String, der dem entsprechenden Index im `$_FILES`-Array entspricht.
- `$target_dir` - String, der das Upload-Verzeichnis beinhaltet, z.B. `assignments/submissions/`; muss dem Format `\A([a-zA-Z0-9]+\/)+\z` entsprechen. Wenn das Verzeichnis noch nicht existiert, wird es erstellt.
- `$file_types` - Array aus Strings, um die erlaubten Dateierweiterungen festzulegen (optional)

### Rückgabewert

Gibt den relativen Pfad zur hochgeladenen Datei als String zurück.

z.B. ``uploads/assignements/img.jpg``
