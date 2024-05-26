<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include 'connessione.php';
    mysqli_begin_transaction($conn);

    //select per scegliere la tabella da cui prendere la chiave primaria
    $mail = $_SESSION['mailDocente'];
    $tabelle = $_SESSION["tabelle"];
    $chiavi_primarie=array();
    $attributi=array();
    var_dump($tabelle);
    if (isset($tabelle) && !empty($tabelle)) {
        foreach($tabelle as $tabella) {
            echo "Tabella: $tabella <br>";
            $sql =" CALL TabellaFisica('$mail', '$tabella');";
            
            $creazione_tabella = mysqli_query($conn, $sql);
            if (!$creazione_tabella) {
                echo "Creazione tabella fallita: " . mysqli_error($conn);
                continue; // Passa alla prossima tabella
            } else {
                echo "Tabella creata <br>";
            }
    
    
            // Trigger num_righe
            $trigger = "CREATE TRIGGER num_righe$tabella AFTER INSERT ON $tabella
                        FOR EACH ROW
                        BEGIN
                            UPDATE TABELLA_ESERCIZIO
                            SET NumeroRighe = NumeroRighe + 1
                            WHERE Nome = '$tabella' AND MailDocente = '$mail';
                        END;
                        ";
    
            $risTRIGGER = mysqli_query($conn, $trigger);
    
            if (!$risTRIGGER) {
                echo "Trigger fallito: " . mysqli_error($conn);
            }
        }
    
        if (!mysqli_commit($conn)) {
            mysqli_rollback($conn);
            echo "Errore durante il commit della transazione.";
        }
    } else {
        echo "Non ci sono righe nell'array tabelle";
    }

    if (!mysqli_commit($conn)) {
        mysqli_rollback($conn);
        echo "Errore durante il commit della transazione.";
    }
    unset( $_SESSION["tabelle"]);//elimino l'array così che possa essere ricreato da zero con le nuove tabelle

    mysqli_close($conn);
    /*header('Location: CreaTabelle.php');
    exit();*/
    echo "<br> <br> <a href=hpDocente.php> <- </a>";
?>
