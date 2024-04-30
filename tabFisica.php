<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include 'connessione.php';
    mysqli_begin_transaction($conn);

    //select per scegliere la tabella da cui prendere la chiave primaria
    $mail = $_SESSION['mailDocente'];
    $tabelle = $_SESSION["tabelle"];
    if (isset($_GET['fine'])) {
       // var_dump($tabelle);
        if (isset($_SESSION["tabelle"])) {
           // var_dump($_SESSION["tabelle"]);
            //creazione fisica delle tabelle
            foreach($_SESSION["tabelle"] as $tabella){
                echo "tabella: " . $tabella ."<br>";
                //per ogni tabella nuova creata (quindi presente nell'array), seleziono i suoi attributi
                $query="SELECT * FROM attributo WHERE NomeTabella='$tabella';";
                $ris = mysqli_query($conn, $query);
                if (!$ris) {
                    echo "ricerca fallita: " . mysqli_error($conn);
                }
                else{
                    //creo la tabella nuova vuota
                    $sql="CREATE TABLE IF NOT EXISTS `$tabella`(id INT AUTO_INCREMENT PRIMARY KEY);";
                    $creazione = mysqli_query($conn, $sql);
                    if (!$creazione) {
                        echo "creazione tabella fallita: " . mysqli_error($conn);
                    }else{
                        echo "tabella creata <br>";
                        //per ogni attributo faccio l'aggiornamento della tabella con la nuova colonna
                        while ($row = mysqli_fetch_array($ris)) {
                            $nome=$row['Nome'];
                            $tipo=$row['Tipo'];
                            if ($tipo == 'VARCHAR') {//se è un varchar gli specifico una lunghezza di default
                                $tipo = "VARCHAR(50)";
                            }
                            $chiave=$row['PossibileChiavePrimaria'];
                            echo "attributo: ".$nome. "<br>";
                            //aggiungo la colonna dell'attributo
                            $add="ALTER TABLE `$tabella`
                                        ADD `$nome` $tipo;";
                            $ris_add = mysqli_query($conn, $add);
                            if (!$ris_add) {
                                echo "creazione colonna fallita: " . mysqli_error($conn);
                            }else{
                                if($chiave==1){//se ho un'altra chiave tolgo id e aggiungo la nuova chiave
                                    //**********non vanno le chiavi multiple */
                                    $drop="ALTER TABLE `$tabella` 
                                            DROP `id`;";
                                    $ris_drop = mysqli_query($conn, $drop);
                                    if (!$ris_drop) {
                                        echo "eliminazione fallita: " . mysqli_error($conn);
                                    }
                                    $key="ALTER TABLE `$tabella` 
                                            ADD PRIMARY KEY (`$nome`);";
                                    $ris_key = mysqli_query($conn, $key);
                                    if (!$ris_key) {
                                        echo "chiave primaria fallita: " . mysqli_error($conn);
                                    }
                                }
                            }
                            
        
                        }
                    }
                }
            }

            //vincoli fisici
            foreach($_SESSION["tabelle"] as $tabella){
                $vincoli="SELECT * FROM vincolo WHERE NomeTabellaFK = '$tabella';";
                $ris_vinc= mysqli_query($conn, $vincoli);
                if (!$ris_vinc) {
                    echo "chiave esterna fallita: " . mysqli_error($conn);
                }else{
                    while ($row = mysqli_fetch_array($ris_vinc)) {
                        $pk=$row['NomeAttributoPK'];
                        $fk=$row['NomeAttributoFK'];
                        $tab2=$row['NomeTabellaPK'];
                        $key="ALTER TABLE `$tabella` 
                        ADD FOREIGN KEY (`$fk`) REFERENCES `$tab2`(`$pk`);";
                        $foreign = mysqli_query($conn, $key);
                        if (!$foreign) {
                            echo "chiave primaria fallita: " . mysqli_error($conn);
                        }
                    }
                }
            }

        }
        else{
            echo "non ci sono righe nell'array tabelle";
        }
    }

    if (!mysqli_commit($conn)) {
        mysqli_rollback($conn);
        echo "Errore durante il commit della transazione.";
    }
    unset( $_SESSION["tabelle"]);//elimino l'array così che possa essere ricreato da zero con le nuove tabelle
    // chiusura della connessione
    mysqli_close($conn);
    echo "<br> <br> <a href=hpDocente.php> <- </a>";
?>
