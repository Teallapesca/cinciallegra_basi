<?php

function logEvent($message) {
    try {
        // Connessione a MongoDB
        $manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');

        // Creazione del documento da inserire
        $bulk = new MongoDB\Driver\BulkWrite;
        $document = [
            'message' => $message,
            'timestamp' => new MongoDB\BSON\UTCDateTime()
        ];
        $bulk->insert($document);

        // Inserimento nella collezione 'logs' del database 'mydatabase'
        $manager->executeBulkWrite('EventiCinciallegra.eventi', $bulk);
        
        echo "Evento registrato con successo!";
    } catch (MongoDB\Driver\Exception\Exception $e) {
        echo "Errore durante la registrazione dell'evento: ", $e->getMessage(), "\n";
    }
}

logEvent('Nuovo bar registrato');
logEvent('Nuovo loc creato');
logEvent('Nuovo dfg aggiunto');
?>