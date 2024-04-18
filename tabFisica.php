<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include 'connessione.php';
    mysqli_begin_transaction($conn);

    //select per scegliere la tabella da cui prendere la chiave primaria
    $mail = $_SESSION['mailDocente'];
    if (isset($_GET['fine'])) {
        echo"buuuuuu";

    }

    if (!mysqli_commit($conn)) {
        mysqli_rollback($conn);
        echo "Errore durante il commit della transazione.";
    }

    // chiusura della connessione
    mysqli_close($conn);

?>
