<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include 'connessione.php';
    mysqli_begin_transaction($conn);

    //select per scegliere la tabella da cui prendere la chiave primaria
    $mail = $_SESSION['mailDocente'];
    if (isset($_GET['fine'])) {
        echo"buuuuuu";
        foreach($_SESSION["tabelle"] as $tabella){
            //per ogni tabella nuova creata (quindi presente nell'array), seleziono i suoi attributi
            $query="SELECT * FROM attributo WHERE NomeTabella='$tabella';";
            $ris = mysqli_query($conn, $query);
            if (!$ris) {
                echo "ricerca fallita: " . die(mysqli_error($conn));
            }
            else{
                $sql="CALL crea_tabella_dinamica('$tabella');";
                $tabdin = mysqli_query($conn, $sql);
                if (!$tabdin) {
                    echo "ricerca fallita: " . die(mysqli_error($conn));
                }
            }
        }
    }

    if (!mysqli_commit($conn)) {
        mysqli_rollback($conn);
        echo "Errore durante il commit della transazione.";
    }
    unset( $_SESSION["tabelle"]);
    // chiusura della connessione
    mysqli_close($conn);

?>
