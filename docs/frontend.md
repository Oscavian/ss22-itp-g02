# Frontend Architektur

Das Frontend von Schoala ist in javascript geschrieben. Die Seite ist eine Single Page Application, es wird nie die gesamte Seite neu geladen.

**Ordnerstruktur**
- `client/assets` - css file und Bilder (icons & Logo) 
- `client/html-includes` - Navigationsleiste (footer)
- `client/js` - js files, welche je nach Seite geladen/ausgeführt werden 
- `client/pages` - page content, welcher entsprechend geladen wird

### Libraries
Alle Libraries werden standardmäßig per Content-Delivery-Network (CDN) in `index.html` ingebunden.
Verwendet werden folgende Libraries:

- **jQuery** 3.6+ 
- **Bootstrap** 5+
- **Notyf** 3+

# Aufbau und Routing
## index.html
Zuerst wird `index.html` geladen. Hier werden zuerst die Libraries eingebunden. Im HTML-Body von `index.hmtl` befindet sich kein content, sondern lediglich Platzhalter HTML-DIVs für den Content, der später geladen wird. Schlussendlich wird das script `main.js` geladen.

## main.js
`main.js` übernimmt das Routing und lädt den eigentlichen Content der verschiedenen Seiten mittels AJAX-Request.

Zuerst lädt `main.js` den Footer `footer.hmtl`. Dann wird überprüft, ob in der URL ein Seitenname gespeichert ist. Ist eine Seite angegeben, wird die entsprechende Seite mit `loadPage()` geladen. Benötigt die Seite eine id, wird diese auch aus der URL geladen und an `loadPage()` übergeben. Ist in der URL keine Seite angegeben wird `loadDefaultPage()` aufgerufen.

### loadPage()
Die Funktion für das Laden einer Seite ist `loadPage()`. `loadPage()` wird meist als `onclick=""` event bei verschiedenen Buttons und HTML-Elementen verwendet.

````
loadPage(pageName, id = null, addStateVar = true)
````
`loadPage()` übernimmt den Parameter `pageName`. Hier muss der Name der gewünschten Seite übergeben werden. Manche Seiten (wie z.B. `group-details.html`) benötigen außerdem die id der entsprechenden Gruppe, diese kann auch an `loadPage()` übergeben werden. `addStateVar` ist standardmäßig `true`, dieser Parameter kann auf `false` gesetzt werden, wenn kein state erstellt werden soll (z.B. bei `loadDefaultPage()`).

Um anhand des Seitennamens die richtige Seite laden zu können, gibt es in `main.js` ein Array `pages`, in dem die zugehörigen Pfade zu den Seiten gespeichert sind.

### loadDefaultPage()

`loadDefaultPage()` lädt je nach Login-Status des Users eine andere Seite:
- Ist der User nicht eingeloggt, wird die Startseite `home.html` geladen
- Ist der User als Lehrer\*in eingeloggt, wird die Gruppenübersicht `groups.html` geladen.
- Ist der User als Schüler\*in eingeloggt, ermittelt `loadDefaultPage()` mittels AJAX-request die id der Gruppe des Users und lädt dann die Gruppen-Seite `group-details.hmlt` mit der entsprechenden id.

### Vor/Zurück-Funktionalität
Damit man die Vor- und Zurück-Buttons des Browsers benutzen kann, obwohl es eine Single Page Application ist und nie die ganze Seite neu geladen wird, wird in `main.js` bei jedem Seitenwechsel ein state gesetzt. In der URL des states bleibt dann der Seitenname und gegebenenfalls die id gespeichert. In `main.js` gibt es einen Eventlistener, der auf `window.onpopstate` hört und die entsprechende Seite lädt, wenn der Vor-/Zurück-Button gedrückt wurde.

### Logik der Seiten
Wenn eine neue Seite geladen wird, lädt `main.js` mittels AJAX-request zuerst die entsprechende`.html` Datei. In dieser `.html` Datei wird dann wiederum die entsprechende `.js` Datei mit der Logik für die Seite geladen.