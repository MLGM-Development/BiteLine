# Progetto BiteLine

## Informazioni sui pacchetti

PHP File manager: composer 

packets: phpdotenv, google/apiclients

Comando per installare su composer: composer require <nome pacchetto\>

NodeJS File Manager: npm

packets: express mysql2 bcrypt cookie-parser jsonwebtoken cors

Comando per installare su npm: npm install <nome pacchetto\>

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

## Inizializzazione del server

Una volta fatto ciò basterà andare sulla cartella nodeServer ed effettuare il comando

    npm init -y

Dopodiché 

    node server.js

Dovrete poi avviare mysqli e apache da XAMPP ed entrare all'URL

http://localhost/BiteLine