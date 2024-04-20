<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include 'connessione.php';
    mysqli_begin_transaction($conn);

    //select per scegliere la tabella da cui prendere la chiave primaria
    $mail = $_SESSION['mailDocente'];
    $tabelle = $_SESSION["tabelle"];
    if (isset($_GET['fine'])) {
        echo "buuuuuu";
        var_dump($tabelle);
        if (isset($_SESSION["tabelle"])) {
            var_dump($_SESSION["tabelle"]);
            echo "ok noh";
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
                            if ($tipo == 'VARCHAR') {
                                $tipo = "VARCHAR(50)"; // Esempio di lunghezza massima di 255 caratteri
                            }
                            $chiave=$row['PossibileChiavePrimaria'];
                            echo "attributo: ".$nome. "<br>";
                            $add="ALTER TABLE `$tabella`
                                        ADD `$nome` $tipo;";
                            $ris_add = mysqli_query($conn, $add);
                            if (!$ris_add) {
                                echo "creazione colonna fallita: " . mysqli_error($conn);
                            }else{
                                if($chiave==1){
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
                        /* $sql2="ALTER TABLE `$tabella`
                                DROP COLUMN id;";
                        $eliminazione = mysqli_query($conn, $sql2);
                        if (!$eliminazione) {
                            echo "creazione tabella fallita: " . mysqli_error($conn);
                        }//else{}*/
                        echo " e tutto ok gente!";
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
    unset( $_SESSION["tabelle"]);
    // chiusura della connessione
    mysqli_close($conn);

?>
