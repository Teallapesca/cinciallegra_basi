<?php

function logEvent($message) {
    try {
        // Connessione a MongoDB
        $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

        // Creazione del documento da inserire
        $bulk = new MongoDB\Driver\BulkWrite;
        $datetime = new DateTime('now', new DateTimeZone('Europe/Rome'));
        
        // Converti l'orario in UTC
        $datetime->setTimezone(new DateTimeZone('UTC'));
        
        // Creazione del documento da inserire con il timestamp in UTC
        $document = [
            'message' => $message,
            'timestamp' => new MongoDB\BSON\UTCDateTime($datetime->getTimestamp() * 1000) // MongoDB\BSON\UTCDateTime richiede millisecondi
        ];
        $bulk->insert($document);

        // Inserimento nella collezione 'logs' del database 'mydatabase'
        $manager->executeBulkWrite('EventiCinciallegra.eventi', $bulk);
    } catch (MongoDB\Driver\Exception\Exception $e) {
        echo "Errore durante la registrazione dell'evento: ", $e->getMessage(), "\n";
    }
}
?>