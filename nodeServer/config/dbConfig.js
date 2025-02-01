const mysqli = require('mysql2/promise') //Richiamo la libreria per mysql
require('dotenv').config() //Richiamo la libreria per usare i file .env

//credenziali del database
const dbConfig = {
    host: process.env.DATABASE_HOSTNAME,
    user: process.env.DATABASE_USERNAME,
    password: process.env.DATABASE_PASSWORD,
    database: process.env.DATABASE_NAME,

    /*host: 'localhost',
    user: 'root',
    password: 'root',
    database: 'biteline_db',*/
};

//funzione di connessione al database
const connectDatabase = async () => {
    return await mysqli.createConnection(dbConfig);
}

//esportazione del modulo di connessione per poterlo richiamare
module.exports = connectDatabase()