# API Dokumentation

- Die Backend-Komponente stellt eine Remote Procedure Call (RPC) API dar, die je nach Anfrage die jeweiligen Daten
  verarbeitet.
- Alle Requests werden als POST-Requests an `backend/requestHandler.php` gesendet.
- Die aufzurufende Methode wird über den POST-Parameter `method` bestimmt.

### Arten von Responses

**Status 400 Bad Request**

Es wird eine Fehlermeldung in Plaintext zurückgegeben.

Tritt auf, wenn entweder die POST-Parameter und/oder die übergebenen Werte fehlerhaft sind.

**Status 200 OK**

Der Request hat funktioniert und wurde ordnungsgemäß durchgeführt.

Der Response wird JSON-encoded gesendet, jene sind in der Dokumentation angeführt.

### Testen in Postman

Die Parameter müssen über den Tab "Body", als `form-data` übergeben werden

# User-Requests

## Login

### Request

```json
method: "login"
user: string
password: string
```

### Response

```json
{
  "success": boolean
}
```

**Weitere Infos:**

Bei korrekten Daten wird automatisch die session gesetzt und der User ist angemeldet. Im JavaScript muss dann auf die
home Seite weitergeleitet werden.

## Logout

Die Session wird beendet und der angemeldete User dadurch ausgeloggt.

### Request

```
method: "logout"
```

### Response

```json
{
  "success": true
}
```

## Login-Status abrufen

Der Login-Status wird über das Session-Cookie geprüft.

### Request

```
method: "getLoginStatus"
```

### Response

```json
{
  "isLoggedIn": boolean,
  "username": string,
  "userId": int,
  "userType": int
}
```

## Prüfen, ob ein Username verfügbar ist

Könnte zum Beispiel während der Eingabe des Usernamen aufgerufen werden, um bereits vor absenden des Formulars checken
zu können, ob der gewünschte Username verfügbar ist. Der Username wird aber bei der Registrierung dann noch einmal extra
überprüft.

### Request

```json
method: "checkUserNameAvailable"
user: string
```

### Response

```json
{
  "success": boolean,
  "userNameAvailable": boolean
}
```

## Lehrer-Account anlegen

Erstellt einen neuen Lehrer-Account.

### Request

```json
method: "registerTeacher"
first_name: string
last_name: string
user: string
password: string
```

### Response

```json
{
  "success": true,
  "msg": "User successfully created!"
}
```

**Weitere Infos:**

Die Formvalidierung sollte auch client-seitig in Javascript durchgeführt werden, hier wird nur noch einmal zusätzlich
eine Backend-Formvalidierung durchgeführt, um zu verhindern, dass jemand manuell ungültige Daten an den requestHandler
schickt.

Folgende Formvalidierung wird vorgenommen:

- user: Muss zwischen 6 und 50 Zeichen sein
- first_name: Nur Buchstaben, Leerzeichen und Apostrophe, max 50 Zeichen
- last_name: Nur Buchstaben, Leerzeichen und Apostrophe, max 50 Zeichen
- password: mindestens 6 Zeichen

Die Passwörter werden in der Datenbank nur gehashed gespeichert.

## Schüler-Accounts anlegen

### Request

```json
method: "registerStudents"
students: [{first_name, last_name}, ...]
group_id: int
```

Der Nutzername hat stets die Form <nachname.vorname>; wenn der Nachname länger als 12 Zeichen lang ist: <nachname.v>

Wenn ein doppelter Nutzername vorkommen sollte, wird eine zufällige dreistellige Zahl an den Nutzernamen angehängt.

**Validierung:**

Folgende Formvalidierung wird vorgenommen:

- user: Muss zwischen 6 und 50 Zeichen sein
- first_name: Nur Buchstaben, Leerzeichen und Apostrophe, max. 50 Zeichen
- last_name: Nur Buchstaben, Leerzeichen und Apostrophe, max. 50 Zeichen

### Response

```json
[
  {
    "username": string,
    "password": string,
    "first_name": string,
    "last_name": string,
    "user_id": int
  }
]
```

Das Passwort wird am Server generiert und einmal am Client angezeigt.

### Permissions

- Angemeldeter Nutzer muss eine Lehrperson sein.
- Angemeldeter Nutzer muss in der angegebenen Gruppe sein.

## Nutzerdaten aktualisieren

Ändert die Nutzerdaten des gerade eingeloggten Nutzers.

### Request

```json
method: "updateUserData"
type: "username" | "firstName" | "lastName"
data: string
```

**Validierung:**

Folgende Formvalidierung wird vorgenommen:

- username: Muss zwischen 6 und 50 Zeichen sein
- first_name: Nur Buchstaben, Leerzeichen und Apostrophe, max. 50 Zeichen
- last_name: Nur Buchstaben, Leerzeichen und Apostrophe, max. 50 Zeichen

### Response

```json
{
  "success": true
}
```

### Permissions

- Nutzer muss eingeloggt sein.

## Passwort ändern

### Request

```json
method: "updateUserPassword"
old_password: string
new_password string
```

### Response

```json
{
  "success": boolean
}
```

## Neues Schüler-Passwort generieren

Methode, um ein neues 10-stelliges Passwort zu generieren, dass an den Client gesendet wird.

Wird in Kombination mit dem Request “setNewStudentPassword” verwendet.

### Request

```
method: “generateNewStudentPassword”
```

### Response:

```json
{
  "success": true,
  "generatedPassword": 0123456789
}
```

## Neues Schüler-Passwort setzen

Setzt das Passwort eines Schülers neu.

### Request

```
method: setNewStudentPassword
new_password: string
user_id: string
```

### Response:

```json
{
  "success": true
}
```

# Group-Requests

## Gruppe anlegen

### Request

```json
method: "createGroup"
group_name: string
```

### Response

```json
{
  "success": boolean,
  "newGroupID": int
}
```

### Permissions

- Aufrufender Nutzer muss ein Lehrer sein.

## Gruppen eines Nutzers abrufen

Ruft die Gruppen des aktuell eingeloggten Nutzers ab und gibt Namen und IDs zurück.

### Request

```json
method: "getUserGroups"
```

### Response

```json
{
  "success": true,
  "noGroups": boolean,
  "groups": [
    {
      "groupName": string,
      "groupId": int
    },
    ...
  ]
}
```

## Chat ID einer Gruppe abrufen

### Request

```json
method: "getGroupChatId"
group_id: int
```

### Response

```json
{
  "success": boolean,
  "groupChatId": int
}
```

### Permissions

- Der angemeldete Nutzer muss Teil der angefragten Gruppe sein.
- Wenn der User dann Nachrichten aus dem entsprechenden Chat abrufen oder neue Nachrichten schreiben möchte, wird
  nochmals überprüft, ob der User die nötige Berechtigung hat.

## Gruppenname abrufen

### Request

```json
method: "getGroupName"
group_id: int
```

### Response

```json
{
  "success": boolean,
  "groupName": string
}
```

### Permissions

- Der Gruppenname kann nur dann abgerufen werden, wenn der User Teil der Gruppe ist.

## Lehrer einer Gruppe abrufen

### Request

```json
method: "getGroupTeacher"
group_id: int
```

### Response

```json
{
  "success": boolean,
  "teacherFirstName": string,
  "teacherLastName": string
}
```

### Permissions

- Nutzer muss Teil der angefragten Gruppe sein.

## Mitglieder einer Gruppe abrufen

### Request

```json
method: "getGroupMembers"
group_id: int
```

### Response

```json
{
  "success": true,
  "groupMembers": [
    {
      "user_id": int,
      "user_type": int,
      "username": string,
      "first_name": string,
      "last_name": string
    },
    ...
  ]
}
```

### Permissions

- Nutzer muss in der entsprechenden Gruppe sein.

# Assignment-Requests

## Assignment nach ID abrufen

### Request

```json
method: "getAssignmentById"
assignment_id: int
```

### Response

```json
{
  "assignment_id": int,
  "creator_id": int,
  "creator_name": string,
  "group_id": int,
  "time": unix_timestamp,
  "due_time": unix_timestamp,
  "title": string,
  "text": string,
  "file_path": string,
  "isExpired": boolean
}
```

### Permissions

- Abrufender Nutzer muss eingeloggt und in der entsprechenden Gruppe, in der das Assignment erstellt wurde, sein.

## Assignments einer Gruppe abrufen

### Request

```json
method: "getGroupAssignments"
group_id: int
```

### Response

```json
{
  "success": true,
  "groupAssignments": [
    {
      "assignmentId": int,
      "creator_id": int,
      "group_id": int,
      "creation_time": string
      (timestamp),
      "due_time": string
      (timestamp),
      "title": string,
      "text": string,
      "file_path": string,
      "isExpired": boolean
    },
    ...
  ]
}
```

### Permissions

- Nutzer muss in der entsprechenden Gruppe sein.

## Assignment erstellen

### Request

```json
method: "createAssignment"
group_id: int
due_time: string (datetime)
title: string
attachment: file
```

"attachment" ist optional.

### Response

```json
{
  "success": boolean,
  "msg": "Assignment erfolgreich erstellt!",
  "assignment_id": int
}
```

Wenn der Datei-Upload fehlschlägt:

```json
{
  success: false,
  error: string
}
```

### Permissions

- Nutzer muss vom Typ Lehrer sein
- Nutzer muss Teil der übergebenen Gruppe sein

## Submissions abrufen

Ruft die Daten der abgegebenen Abgaben eines bestimmten Assignments ab.

### Request

```json
method: "getSubmissions"
assignment_id: int
```

### Response

```json
[
  {
    "submission_id": int,
    "user_id": int,
    "assignment_id": int,
    "file_path": string,
    "creation_time": string
    (timestamp),
    "user_name": string,
    "first_name": string,
    "last_name": string
  }
]
```

### Permissionsie

- Nutzer muss vom Typ Lehrer sein.
- Nutzer muss Teil der Gruppe des Assignments sein.

## Submission hinzufügen

Nimmt eine neue Abgabe inkl. Datei entgegen und erstellt eine neue Abgabe für das enspr. Assignment.

Details zum File-Upload in der Backend-Architektur-Dokumentation.

Erlaubte Dateitypen: alle

### Request

```
method: "addSubmission"
assignment_id: int
attachment: file
```

### Response

```json
{
  "success": true,
  "msg": "Abgabe erfolgreich erstellt!",
  "submission_id": int
}
```

Sollte der Datei-Upload fehlschlagen:

```json
{
  "success": false,
  "error": string
}
```

### Permissions

- Nutzer muss Zugriff auf das entsprechende Assignment haben, bzw. Teil der jeweiligen Gruppe sein.

## Assignment-Anhang herunterladen

Lädt den Anhang des angegebenen Assignments herunter.

### Request

```json
method: "downloadAssignmentFile"
assignment_id: int
```

### Permissions

- Nutzer muss Zugriff auf das Assignment haben, bzw. Teil der jeweiligen Gruppe sein

## Submission-Anhang herunterladen

Lädt des Anhang der gegebenen Submission herunter.

### Request

```json
method: "downloadSubmissionFile"
submission_id: int
```

### Permissions

- Nutzer muss vom Typ Lehrer sein.
- Nutzer muss Zugriff auf das entsprechende Assignment haben

# Chat-Requests

## Nachrichten abrufen

### Request

```json
method: "getMessages"
chat_id: int
group_id: int
offset: int
```

Es wird entweder eine Chat ID oder eine Gruppen ID wird benötigt.

Es werden max. 20 Nachrichten auf einmal abgerufen. Mit dem Parameter "offset", kann bestimmt werden, welche Nachrichten
geladen werden sollen, z.B.:

- Offset 0 => Nachrichten 0 - 19
- Offset 1 => Nachrichten 20 - 39
- usw.

### Response

```json
[
  {
    "message_id": int,
    "user_id": int,
    "chat_id": int,
    "first_name": string,
    "last_name": string,
    "isOwnMessage": boolean,
    "text": string,
    "time": string
  },
  ...
]
```

### Permissions

- Nutzer muss in der entsprechenden Gruppe sein.

## Nachricht senden

Nimmt eine neue Nachricht entgegen und speichert sie in den angegebenen Chat

### Request

```json
method: "sendMessage"
chat_id: int
group_id: int
text: string
```

Entweder chat_id oder group_id wird benötigt.se

### Response

```json
{
  "success": true,
  "msg": "Nachricht gesendet.",
  "message_id": int
}
```

### Permissions

- Nutzer muss eingeloggt sein.
- Nutzer muss in der angegebenen Gruppe sein.

## Nachricht löschen

Löscht eine Nachricht aus einem Chat. Kann nur von Nutzern vom Typ Lehrer verwendet werden.

In der aktuellen Version werden die Nachrichten unwiderruflich aus der Datenbank gelöscht.

### Request

```json
method: "deleteMessage"
message_id: int
```

### Response

```json
{
  "success": true,
  "msg: ": "Message deleted."
}
```



