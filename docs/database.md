# Datenbank Dokumentation

## Relationenmodell
![Datenbank Diagramm](https://cdn.discordapp.com/attachments/946785963701575800/955874812776030278/Datenbankentwurf_v2.png)

## Standard-Datensätze

Die einzig vordefinierten Datensätze sind die Nutzertypen (``user_type``):
- ``1 : teacher``
- ``2 : student``

Diese Werte dürfen NICHT verändert werden!

## Delete Constraints

- Wenn ein user gelöscht wird, werden alle Messages, Uploads, Assignments und Gruppenzuordnungen (in user_group) gelöscht
- Wenn das Assignment gelöscht wird, werden auch die zugehörigen Uploads automatisch gelöscht
- Wenn eine Gruppe gelöscht wird, werden auch alle Assignments für die Gruppe gelöscht
- Wenn eine Gruppe gelöscht wird, werden auch alle user Zuordnungen (in user_group) für die Gruppe gelöscht
- Wenn ein Chat gelöscht wird, werden auch alle Messages im Chat gelöscht

Wichtig: Ein Gruppenchat wird nicht automatisch gelöscht, wenn eine Gruppe gelöscht wird!

Die deletes (wie oben beschrieben) sind so in der Datenbank angelegt:

### student_upload

```
fk_assignment_id -> ON DELETE CASCADE
fk_user_id -> ON DELETE CASCADE
```

### assignment

```
fk_group_id -> ON DELETE CASCADE
fk_user_id -> ON DELETE CASCADE
```

### user_group

```
fk_group_id -> ON DELETE CASCADE
fk_user_id -> ON DELETE CASCADE
```

### message

```
fk_chat_id -> ON DELETE CASCADE
fk_user_id -> ON DELETE CASCADE
```
