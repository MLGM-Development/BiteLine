# Progetto BiteLine

## Informazioni sui pacchetti

PHP File manager: composer 

packets: phpdotenv, google/apiclients

Comando per installare su composer: composer require <nome pacchetto\>

NodeJS File Manager: npm

packets: express mysql2 bcrypt cookie-parser jsonwebtoken cors

Comando per installare su npm: npm install <nome pacchetto\>

## Clonazione del repository

Clonare il repository nella cartella htdocs di XAMPP

    git clone https://github.com/MLGM-Development/DatabaseBiteLine.git

Il percorso dovrebbe assomigliare a questo:

    C:\xampp\htdocs\BiteLine

È importante che il percorso sia questo in quanto il server node.js è configurato per leggere i file da questa cartella, 
il nome deve essere obbligatoriamente `BiteLine`

## Adattamento e correzione di APACHE

Adattamento di apache per farlo funzionare con Xampp:

    <VirtualHost *:80>
    DocumentRoot "<PERCORSO DELLA TUA CARTELLA DI PROGETTO DENTRO HTDOCS>"
    ServerName localhost

    <Directory "PERCORSO DELLA TUA CARTELLA HTDOCS">
        Options Indexes FollowSymLinks Includes ExecCGI
        AllowOverride All
        Require all granted
    </Directory>

    # Instrada richieste a /node-api verso il server Node.js
    ProxyPass /node-api http://localhost:3000/node-api
    ProxyPassReverse /node-api http://localhost:3000/node-api

    # Evita di instradare altre richieste PHP a Node.js
    ProxyPassMatch ^/(.*\.php)$ !
    </VirtualHost>

Poi occorrerà togliere dai commenti queste linee

    LoadModule proxy_module modules/mod_proxy.so
    LoadModule proxy_http_module modules/mod_proxy_http.so

## Configurazione del database

Per configurare il database occorrerà creare un file .env nella cartella nodeServer con il seguente contenuto

    JWT_SECRET=
    DATABASE_HOSTNAME=localhost
    DATABASE_USERNAME=root
    DATABASE_PASSWORD=
    DATABASE_NAME=biteline_db

Andrà craeto anche un file .env nella cartella `Backend/Database/` con il seguente contenuto

    DB_HOST=localhost
    DB_USER=root
    DB_PASS=
    DB_NAME=bite_line

Dopodiché occorrerà creare il database `biteline_db` e importare il file `biteline_db.sql`

https://github.com/MLGM-Development/DatabaseBiteLine.git

## Avvio di xampp

Avviare XAMPP e avviare i servizi Apache e MySQL

<hr>

## Inizializzazione del server

Una volta fatto ciò basterà andare sulla cartella nodeServer

    cd nodeServer

Effettuare il comando

    npm init -y

Nel caso in cui il precedente comando non funzioni, occorrerà installare npm con il comando

    npm install

Dopodiché 

    node server.js

Dovrete poi avviare mysqli e apache da XAMPP ed entrare all'URL

http://localhost/BiteLine