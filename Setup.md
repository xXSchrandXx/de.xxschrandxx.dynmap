# Setting up Minecraft-Sync
[German](#German) | [English](#English)

## English
1. Stop the Minecraft server.
2. Download the Dynmap plugin.
3. Upload the downloaded file "Dynmap-.jar" to the folder `server/plugins/` or `server/mods/`.
4. Start the Minecraft server.
5. A new folder has been created in the folder `plugins/dynmap` or `dynmap` next to the JAR file. In this folder, a file named `configuration.txt` is created. This file contains almost everything you need to configure all aspects of the plugin, but it can be very overwhelming and is a comprehensive document, of which most users will only change one or two lines.
6. Change the storage area in the `configuration.txt`.
    <details>
    <summary>Before</summary>

    ``` yaml
    storage:
    # Filetree storage (standard tree of image files for maps)
    type: filetree
    # SQLite db for map storage (uses dbfile as storage location)
    #type: sqlite
    #dbfile: dynmap.db
    # MySQL DB for map storage (at 'hostname':'port' with flags "flags" in database 'database' using user 'userid' password 'password' and table prefix 'prefix')
    #type: mysql
    #hostname: localhost
    #port: 3306
    #database: dynmap
    #userid: dynmap
    #password: dynmap
    #prefix: ""
    #flags: "?allowReconnect=true&autoReconnect=true"
    ```
    </details>
    <details>
    <summary>After</summary>

    ``` yaml
    storage:
    # Filetree storage (standard tree of image files for maps)
    #type: filetree <- DONT FORGET TO COMMENT THIS OUT
    # SQLite db for map storage (uses dbfile as storage location)
    #type: sqlite
    #dbfile: dynmap.db
    # MySQL DB for map storage (at 'hostname':'port' with flags "flags" in database 'database' using user 'userid' password 'password' and table prefix 'prefix')
    type: mysql
    hostname: <mysql_ip>
    port: <mysql_port>
    database: <mysql_database>
    userid: <dynmap_mysql_user>
    password: <dynmap_mysql_password>
    prefix: "" # Can add prefix for tables if you want
    flags: "?allowReconnect=true&autoReconnect=true"
    ```
    </details>
7. Directly below the storage area, there should be a section named "class: org.dynmap.InternalClientUpdateComponent" which we will comment out.
    <details>
    <summary>Before</summary>

    ``` yaml
    - class: org.dynmap.InternalClientUpdateComponent
        sendhealth: true
        sendposition: true
        allowwebchat: true
        webchat-interval: 5
        hidewebchatip: true
        trustclientname: false
        includehiddenplayers: false
        # (optional) if true, color codes in player display names are used
        use-name-colors: false
        # (optional) if true, player login IDs will be used for web chat when their IPs match
        use-player-login-ip: true
        # (optional) if use-player-login-ip is true, setting this to true will cause chat messages not matching a known player IP to be ignored
        require-player-login-ip: false
        # (optional) block player login IDs that are banned from chatting
        block-banned-player-chat: true
        # Require login for web-to-server chat (requires login-enabled: true)
        webchat-requires-login: false
        # If set to true, users must have dynmap.webchat permission in order to chat
        webchat-permissions: false
        # Limit length of single chat messages
        chatlengthlimit: 256
    #  # Optional - make players hidden when they are inside/underground/in shadows (#=light level: 0=full shadow,15=sky)
    #  hideifshadow: 4
    #  # Optional - make player hidden when they are under cover (#=sky light level,0=underground,15=open to sky)
    #  hideifundercover: 14
    #  # (Optional) if true, players that are crouching/sneaking will be hidden 
        hideifsneaking: false
        # If true, player positions/status is protected (login with ID with dynmap.playermarkers.seeall permission required for info other than self)
        protected-player-info: false
        # If true, hide players with invisibility potion effects active
        hide-if-invisiblity-potion: true
        # If true, player names are not shown on map, chat, list
        hidenames: false
    ```
    </details>
    <details>
    <summary>After</summary>

    ``` yaml
    # - class: org.dynmap.InternalClientUpdateComponent
        #sendhealth: true
        #sendposition: true
        #allowwebchat: true
        #webchat-interval: 5
        #hidewebchatip: false
        #trustclientname: false
        #includehiddenplayers: false
        # (optional) if true, color codes in player display names are used
        #use-name-colors: false
        # (optional) if true, player login IDs will be used for web chat when their IPs match
        #use-player-login-ip: true
        # (optional) if use-player-login-ip is true, setting this to true will cause chat messages not matching a known player IP to be ignored
        #require-player-login-ip: false
        # (optional) block player login IDs that are banned from chatting
        #block-banned-player-chat: true
        # Require login for web-to-server chat (requires login-enabled: true)
        #webchat-requires-login: false
        # If set to true, users must have dynmap.webchat permission in order to chat
        #webchat-permissions: false
        # Limit length of single chat messages
        #chatlengthlimit: 256
    #  # Optional - make players hidden when they are inside/underground/in shadows (#=light level: 0=full shadow,15=sky)
    #  hideifshadow: 4
    #  # Optional - make player hidden when they are under cover (#=sky light level,0=underground,15=open to sky)
    #  hideifundercover: 14
    #  # (Optional) if true, players that are crouching/sneaking will be hidden 
        #hideifsneaking: false
        # If true, player positions/status is protected (login with ID with dynmap.playermarkers.seeall permission required for info other than self)
        #protected-player-info: false
        # If true, hide players with invisibility potion effects active
        #hide-if-invisiblity-potion: true
        # If true, player names are not shown on map, chat, list
        #hidenames: false
    ```
    </details>
8. Then remove the commenting of the section directly below it. Then it looks like this.
    <details>
    <summary>Before</summary>

    ``` yaml
    #- class: org.dynmap.JsonFileClientUpdateComponent
    #  writeinterval: 1
    #  sendhealth: true
    #  sendposition: true
    #  allowwebchat: true
    #  webchat-interval: 5
    #  hidewebchatip: false
    #  includehiddenplayers: false
    #  use-name-colors: false
    #  use-player-login-ip: false
    #  require-player-login-ip: false
    #  block-banned-player-chat: true
    #  hideifshadow: 0
    #  hideifundercover: 0
    #  hideifsneaking: false
    #  # Require login for web-to-server chat (requires login-enabled: true)
    #  webchat-requires-login: false
    #  # If set to true, users must have dynmap.webchat permission in order to chat
    #  webchat-permissions: false
    #  # Limit length of single chat messages
    #  chatlengthlimit: 256
    #  hide-if-invisiblity-potion: true
    #  hidenames: false
    ```
    </details>
    <details>
    <summary>After</summary>

    ``` yaml
    - class: org.dynmap.JsonFileClientUpdateComponent
        writeinterval: 1
        sendhealth: true
        sendposition: true
        allowwebchat: true
        webchat-interval: 5
        hidewebchatip: false
        includehiddenplayers: false
        use-name-colors: false
        use-player-login-ip: false
        require-player-login-ip: false
        block-banned-player-chat: true
        hideifshadow: 0
        hideifundercover: 0
        hideifsneaking: false
    #  # Require login for web-to-server chat (requires login-enabled: true)
        webchat-requires-login: false
    #  # If set to true, users must have dynmap.webchat permission in order to chat
        webchat-permissions: false
    #  # Limit length of single chat messages
        chatlengthlimit: 256
        hide-if-invisiblity-potion: true
        hidenames: false
    ```
    </details>
9. Now we will disable the internal webserver since we no longer need it. Search for "disable-webserver" and change the value from `false` to `true`.
10. (Optional) Uncomment `publicURL` and set it to the desired URL of the website.
11. Restart the Minecraft server. (Additional configuration options at https://github.com/webbukkit/dynmap/wiki)
12. Edit the Minecraft servers in the WSC administration area. Set the Dynmap database-host, -port, -user, -password, and -name.
    (Optional) Add a display image or short description.
13. (Optional) Edit the group permissions in the WSC administration area.

## German
1. Stoppe den Minecraft-Server.
2. Lade das Dynmap-Plugin herunter.
3. Lade die heruntergeladene Datei „Dynmap-.jar“ in deen Ordner `server/plugins/` bzw. `server/mods/`.
4. Starte den Minecraft-Server.
5. Ein neuer Ordner wurde im Ordner `plugins/dynmap` oder `dynmap` neben der JAR-Datei erstellt. In diesem Ordner wird eine Datei namens `configuration.txt` erstellt. Diese Datei enthält so ziemlich alles, was zur Konfiguration aller Aspekte der Plugins benötigt wird, kann jedoch sehr überwältigend sein und ist ein umfangreiches Dokument, von dem die meisten Benutzer nur ein oder zwei Zeilen ändern werden.
6. Änder den Speicherbereich in der `configuration.txt`.
    <details>
    <summary>Vorher</summary>

    ``` yaml
    storage:
    # Filetree storage (standard tree of image files for maps)
    type: filetree
    # SQLite db for map storage (uses dbfile as storage location)
    #type: sqlite
    #dbfile: dynmap.db
    # MySQL DB for map storage (at 'hostname':'port' with flags "flags" in database 'database' using user 'userid' password 'password' and table prefix 'prefix')
    #type: mysql
    #hostname: localhost
    #port: 3306
    #database: dynmap
    #userid: dynmap
    #password: dynmap
    #prefix: ""
    #flags: "?allowReconnect=true&autoReconnect=true"
    ```
    </details>
    <details>
    <summary>Nachher</summary>

    ``` yaml
    storage:
    # Filetree storage (standard tree of image files for maps)
    #type: filetree <- DONT FORGET TO COMMENT THIS OUT
    # SQLite db for map storage (uses dbfile as storage location)
    #type: sqlite
    #dbfile: dynmap.db
    # MySQL DB for map storage (at 'hostname':'port' with flags "flags" in database 'database' using user 'userid' password 'password' and table prefix 'prefix')
    type: mysql
    hostname: <mysql_ip>
    port: <mysql_port>
    database: <mysql_database>
    userid: <dynmap_mysql_user>
    password: <dynmap_mysql_password>
    prefix: "" # Can add prefix for tables if you want
    flags: "?allowReconnect=true&autoReconnect=true"
    ```
    </details>
7. Direkt unter dem Speicherbereich sollte sich ein Abschnitt namens „class: org.dynmap.InternalClientUpdateComponent” befinden, der auskommentiert wird.
    <details>
    <summary>Vorher</summary>

    ``` yaml
    - class: org.dynmap.InternalClientUpdateComponent
        sendhealth: true
        sendposition: true
        allowwebchat: true
        webchat-interval: 5
        hidewebchatip: true
        trustclientname: false
        includehiddenplayers: false
        # (optional) if true, color codes in player display names are used
        use-name-colors: false
        # (optional) if true, player login IDs will be used for web chat when their IPs match
        use-player-login-ip: true
        # (optional) if use-player-login-ip is true, setting this to true will cause chat messages not matching a known player IP to be ignored
        require-player-login-ip: false
        # (optional) block player login IDs that are banned from chatting
        block-banned-player-chat: true
        # Require login for web-to-server chat (requires login-enabled: true)
        webchat-requires-login: false
        # If set to true, users must have dynmap.webchat permission in order to chat
        webchat-permissions: false
        # Limit length of single chat messages
        chatlengthlimit: 256
    #  # Optional - make players hidden when they are inside/underground/in shadows (#=light level: 0=full shadow,15=sky)
    #  hideifshadow: 4
    #  # Optional - make player hidden when they are under cover (#=sky light level,0=underground,15=open to sky)
    #  hideifundercover: 14
    #  # (Optional) if true, players that are crouching/sneaking will be hidden 
        hideifsneaking: false
        # If true, player positions/status is protected (login with ID with dynmap.playermarkers.seeall permission required for info other than self)
        protected-player-info: false
        # If true, hide players with invisibility potion effects active
        hide-if-invisiblity-potion: true
        # If true, player names are not shown on map, chat, list
        hidenames: false
    ```
    </details>
    <details>
    <summary>Nachher</summary>

    ``` yaml
    # - class: org.dynmap.InternalClientUpdateComponent
        #sendhealth: true
        #sendposition: true
        #allowwebchat: true
        #webchat-interval: 5
        #hidewebchatip: false
        #trustclientname: false
        #includehiddenplayers: false
        # (optional) if true, color codes in player display names are used
        #use-name-colors: false
        # (optional) if true, player login IDs will be used for web chat when their IPs match
        #use-player-login-ip: true
        # (optional) if use-player-login-ip is true, setting this to true will cause chat messages not matching a known player IP to be ignored
        #require-player-login-ip: false
        # (optional) block player login IDs that are banned from chatting
        #block-banned-player-chat: true
        # Require login for web-to-server chat (requires login-enabled: true)
        #webchat-requires-login: false
        # If set to true, users must have dynmap.webchat permission in order to chat
        #webchat-permissions: false
        # Limit length of single chat messages
        #chatlengthlimit: 256
    #  # Optional - make players hidden when they are inside/underground/in shadows (#=light level: 0=full shadow,15=sky)
    #  hideifshadow: 4
    #  # Optional - make player hidden when they are under cover (#=sky light level,0=underground,15=open to sky)
    #  hideifundercover: 14
    #  # (Optional) if true, players that are crouching/sneaking will be hidden 
        #hideifsneaking: false
        # If true, player positions/status is protected (login with ID with dynmap.playermarkers.seeall permission required for info other than self)
        #protected-player-info: false
        # If true, hide players with invisibility potion effects active
        #hide-if-invisiblity-potion: true
        # If true, player names are not shown on map, chat, list
        #hidenames: false
    ```
    </details>
8. Entferne danach die Auskommentierung des Abschnitts direkt darunter. Dann sieht es so aus.
    <details>
    <summary>Vorher</summary>

    ``` yaml
    #- class: org.dynmap.JsonFileClientUpdateComponent
    #  writeinterval: 1
    #  sendhealth: true
    #  sendposition: true
    #  allowwebchat: true
    #  webchat-interval: 5
    #  hidewebchatip: false
    #  includehiddenplayers: false
    #  use-name-colors: false
    #  use-player-login-ip: false
    #  require-player-login-ip: false
    #  block-banned-player-chat: true
    #  hideifshadow: 0
    #  hideifundercover: 0
    #  hideifsneaking: false
    #  # Require login for web-to-server chat (requires login-enabled: true)
    #  webchat-requires-login: false
    #  # If set to true, users must have dynmap.webchat permission in order to chat
    #  webchat-permissions: false
    #  # Limit length of single chat messages
    #  chatlengthlimit: 256
    #  hide-if-invisiblity-potion: true
    #  hidenames: false
    ```
    </details>
    <details>
    <summary>Nachher</summary>

    ``` yaml
    - class: org.dynmap.JsonFileClientUpdateComponent
        writeinterval: 1
        sendhealth: true
        sendposition: true
        allowwebchat: true
        webchat-interval: 5
        hidewebchatip: false
        includehiddenplayers: false
        use-name-colors: false
        use-player-login-ip: false
        require-player-login-ip: false
        block-banned-player-chat: true
        hideifshadow: 0
        hideifundercover: 0
        hideifsneaking: false
    #  # Require login for web-to-server chat (requires login-enabled: true)
        webchat-requires-login: false
    #  # If set to true, users must have dynmap.webchat permission in order to chat
        webchat-permissions: false
    #  # Limit length of single chat messages
        chatlengthlimit: 256
        hide-if-invisiblity-potion: true
        hidenames: false
    ```
    </details>
9. Jetzt wird der interne Webserver deaktiviert, da er nicht mehr benötigt wird. Suche „disable-webserver“ und änder den Wert von `false` in `true`.
10. (Optional) Entferne die Auskommentierung von `publicURL` und setze es zu der gewünschten URL der Webseite.
11. Starte den Minecraft-Server neu. (Weitere Einstellungsmöglichkeiten von Dynmap unter https://github.com/webbukkit/dynmap/wiki)
12. Bearbeite im Administationsbereich des WSC die Minecraft-Server. Setze Dynmap Datenbank-Host, -Port, -Benutzer, -Passwort und -Name.
    (Optional) Füge ein Anzeigebild bzw. Kurzbeschreibung hinzu.
13. (Optional) Bearbeite im Administationsbereich des WSC die Gruppenberechtigungen.