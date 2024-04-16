<!doctype html>
<html>
<head>
    <link type="text/css" rel="stylesheet" href="stile.css">
</head>
<body>
    <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        include 'connessione.php';
        mysqli_begin_transaction($conn);
        //$_SESSION['quesiti']=[];  // da ovviamente problema il fatto che essendo un session ed essendocene uno unico per tutti i test, 
        //salva tutti i quesiti di tutti i test che si aprono, dovrei farne uno personalizzato per ogni test
    ?>
    <div class="intesta">
        <h1>SVOLGI IL TEST</h1>
    </div>
    <div class="principale">
        <form name="test" method="GET" action="SvolgiTest.php?titolo=<?php $_SESSION['titoloTest'] ?>">
            <?php
            ///////---- mettere sia tabelle per ogni quesito sia il bottone check dopo i codici
                $test=$_SESSION['titoloTest'];
                $mail=$_SESSION['mailStudente']; 
                $_SESSION["quesiti$test"]=[]; ///****credo dia comunque problema, ricontrollare */
                if (isset($_GET['titolo'])) {
                   $_SESSION['titoloTest']=$_GET['titolo'];
                    $titoloTest = $_GET['titolo'];
                    echo "<h1>".$_SESSION['titoloTest']."</h1>";

                    //---inserimento in svolgimento 
                    $data=date('Y-m-d H:i:s');
                    $stato="";
                    $mail=$_SESSION['mailStudente'];
                    //controlla se il test selezionato è in svolgimento
                    $entrata="SELECT * FROM svolgimento WHERE MailStudente='$mail' AND TitoloTest='$titoloTest';";
                    $ris_entr = mysqli_query($conn, $entrata);
                    if (!$ris_entr) {
                        echo "Errore nella query: " . mysqli_error($conn);
                    } else {
                        if(mysqli_num_rows($ris_entr) == 0){//se non c'è lo inserisce con stato aperto (che è di default)
                            //$stato="Aperto";
                            $inserimento="INSERT INTO svolgimento(MailStudente, TitoloTest, DataInizio) VALUES ('$mail', '$titoloTest', '$data');";
                            $ris_ins = mysqli_query($conn, $inserimento);
                            if (!$ris_ins) {
                                echo "Errore nell'inserimento in svolgimento: " . mysqli_error($conn);
                            }
                        }
                    }

                    //---visualizzazione quesiti
                    $num=0;
                    //seleziono i quesiti del test aperto
                    $query = "SELECT TitoloTest, Progressivo, Difficolta, Descrizione 
                    FROM QUESITO
                    WHERE TitoloTest = '$titoloTest';";  //ci sarebbe la procedura che lo fa ma non va :(
                    $risultato = mysqli_query($conn, $query);
                    if (!$risultato) {
                        echo "Errore nella query che seleziona i quesiti: " . mysqli_error($conn);
                    } else {
                        //vado a controllare per ogni riga se è un quesito chiuso oppure no
                        $_SESSION['numQuesiti']=mysqli_num_rows($risultato);
                        while ($row = mysqli_fetch_array($risultato, MYSQLI_ASSOC)) { 
                            $descrizione = $row['Descrizione'];
                            $progressivo = $row['Progressivo'];
                            if(!isset($_SESSION["risposta$progressivo"])){
                                $_SESSION["risposta$progressivo"]="";
                            }
                            //composizione dei quesiti
                            $num=$num+1;
                            echo "<br> ---------------------------------------- <br>";
                            echo "<br>".$num . "<br>";
                            echo "<h3>$descrizione</h3><br> Inserisci la tua risposta:<br>";

                            //query per vedere se è un quesito chiuso
                            $chiuso_query = "SELECT * FROM quesito_chiuso WHERE Progressivo='$progressivo' AND TitoloTest='$titoloTest'";
                            $ris_chiuso = mysqli_query($conn, $chiuso_query);
                            if (!$ris_chiuso) {
                                echo "Errore nella query 2: " . mysqli_error($conn);
                            } else {
                                if(mysqli_num_rows($ris_chiuso) == 0){
                                    // Se non ci sono quesiti chiusi, mostra il campo di testo per la risposta perchè è un codice
                                    $tipo="codice";
                                    if (isset($_SESSION["risposta$progressivo"])&&($_SESSION["risposta$progressivo"]!="")) {
                                        //se la variabile di risposta di questo quesito non è vuoto, vado già ad inserire il suo valore nel value del textbox
                                        $risp=$_SESSION["risposta$progressivo"];
                                        echo "<input type='text' name='risposta$progressivo' value='$risp'><br><br>";
                                    }else{
                                        //altrimenti faccio un textbox vuoto
                                        echo "<input type=text name='risposta$progressivo' value=''><br><br>";
                                        
                                    }
                                } else {
                                    // Altrimenti, se ci sono quesiti chiusi, mostra le opzioni
                                    //prendo le opzioni del quesito corrente del test selezionato
                                    $opzioni = "SELECT * 
                                                FROM opzione
                                                WHERE TitoloTest = '$titoloTest' AND ProgressivoChiuso='$progressivo';";
                                    $ris_opzioni = mysqli_query($conn, $opzioni);
                                    if (!$ris_opzioni) {
                                        echo "Errore nella query delle opzioni: " . mysqli_error($conn);
                                    } else {
                                        $tipo="chiuso";
                                        while($rowOP = mysqli_fetch_array($ris_opzioni, MYSQLI_ASSOC)){
                                            $testoOp = $rowOP['Testo'];
                                            $numOP=$rowOP['Numerazione'];
                                            //se la variabile risposta è piena 
                                            if (($_SESSION["risposta$progressivo"]=="$numOP$progressivo")) {
                                                echo "<input type=radio name='risposta$progressivo' value='$numOP$progressivo' checked>$testoOp<br>";
                                            }
                                            else{
                                                echo "<input type=radio name='risposta$progressivo' value='$numOP$progressivo'>$testoOp<br>";

                                            }
                                        }
                                    }
                                }
                            }
                            //salvo nell'array i quesiti
                            $quesito=$_SESSION["quesiti$test"][] = Array("progressivo" => $progressivo,
                            "tipo" => $tipo);
                        }
                    }
                } else {
                    echo "Nessun titolo test specificato nella query string.";
                }
            ?>
            <br> ---------------------------------------- <br>
            <br>
            <input type="hidden" name="titolo" value="<?php echo $_SESSION['titoloTest']; ?>">
            <input type="submit" name="Fine" value="Fine">
            <br><br>
            <input type="submit" name="indietro" value="indietro">
        </form>

        <?php
    //controllo se le risposte sono giuste, salvataggio delle risposte anche se non è stato completato il test e inserimento in risposte
        $titoloTest = $_SESSION['titoloTest'];
        $mail=$_SESSION['mailStudente']; 

        if(isset($_GET['indietro'])){

            foreach ($_SESSION["quesiti$titoloTest"] as $quesito) {

                $progressivo=$quesito["progressivo"];

                if(isset($_SESSION["risposta$progressivo"])||($_SESSION["risposta$progressivo"]!="")){
                    $testo=$_GET["risposta$progressivo"]; //potrei creare una procedura da mysql che faccia controllo ed inserimento da solo
                    $_SESSION["risposta$progressivo"]=$testo;

                    //cerco se sono già state date risposte a questo quesito
                    $selezionato="SELECT * FROM risposta WHERE MailStudente='$mail' AND TitoloTest='$titoloTest' AND Progressivo='$progressivo';";
                    $rissel = mysqli_query($conn, $selezionato);
                    if (!$rissel) {
                        echo "Errore nell'inserimento dela risposta: " . mysqli_error($conn);
                    }else{
                        if(mysqli_num_rows($rissel) != 0){
                            //aggiorno con la nuova risposta del quesito
                            $inserimento="UPDATE risposta SET Testo='$testo' WHERE MailStudente='$mail' AND TitoloTest='$titoloTest' AND Progressivo='$progressivo';";
                            $ris_ins = mysqli_query($conn, $inserimento);
                            if (!$ris_ins) {
                                echo "Errore nell'inserimento in svolgimento2: " . mysqli_error($conn);
                            }
                        }
                        else{ //in questo caso io sto inserendo la nuova risposta (quesito o codice)
                            if(isset($_GET["risposta$progressivo"])){/***dovrei fare il controllo che se questa risposta è già stata inserita fa l'update***/
                                //se è stato premuto un radio inserisco la risposta
                                $risposta="INSERT INTO risposta(Progressivo, TitoloTest, MailStudente, Testo) VALUES ('$progressivo','$titoloTest', '$mail', '$testo' );";
                                $risris = mysqli_query($conn, $risposta);
                                if (!$risris) {
                                    echo "Errore nell'inserimento dela risposta: " . mysqli_error($conn);
                                }    
                            }
                        }
                    }
                }
            }

            //faccio il controllo se ci sono risposte inserite, se si faccio l'update
            /*$controllo="SELECT * FROM risposta WHERE TitoloTest='$titoloTest';";
            $ris_controllo = mysqli_query($conn, $controllo);
            if (!$ris_controllo) {
                echo "Errore nell'inserimento in svolgimento1: " . mysqli_error($conn);
            }
            else{
                if(mysqli_num_rows($ris_controllo) != 0){
                    //update di svolgimento in "incompletamento"
                    $stato="InCompletamento";
                    $inserimento="UPDATE svolgimento SET stato='$stato' WHERE MailStudente='$mail' AND TitoloTest='$titoloTest';";
                    $ris_ins = mysqli_query($conn, $inserimento);
                    if (!$ris_ins) {
                        echo "Errore nell'inserimento in svolgimento2: " . mysqli_error($conn);
                    }
                }
                
            }*/

            
            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                echo "Errore durante il commit della transazione.";
            }

            // chiusura della connessione
            mysqli_close($conn);
            //torno alla pagina precedente
            header("Location: hpStudente.php");
            exit();
                
        }
        else if(isset($_GET['Fine'])){

            foreach ($_SESSION["quesiti$titoloTest"] as $quesito) {

                $progressivo=$quesito["progressivo"];

                if(isset($_SESSION["risposta$progressivo"])||($_SESSION["risposta$progressivo"]!="")){
                    $testo=$_GET["risposta$progressivo"]; //potrei creare una procedura da mysql che faccia controllo ed inserimento da solo
                    $_SESSION["risposta$progressivo"]=$testo;

                    //cerco se sono già state date risposte a questo quesito
                    $selezionato="SELECT * FROM risposta WHERE MailStudente='$mail' AND TitoloTest='$titoloTest' AND Progressivo='$progressivo';";
                    $rissel = mysqli_query($conn, $selezionato);
                    if (!$rissel) {
                        echo "Errore nell'inserimento dela risposta: " . mysqli_error($conn);
                    }else{
                        if(mysqli_num_rows($rissel) != 0){
                            //aggiorno con la nuova risposta del quesito
                            $inserimento="UPDATE risposta SET Testo='$testo' WHERE MailStudente='$mail' AND TitoloTest='$titoloTest' AND Progressivo='$progressivo';";
                            $ris_ins = mysqli_query($conn, $inserimento);
                            if (!$ris_ins) {
                                echo "Errore nell'inserimento in svolgimento2: " . mysqli_error($conn);
                            }
                        }
                        else{ //in questo caso io sto inserendo la nuova risposta (quesito o codice)
                            if(isset($_GET["risposta$progressivo"])){/***dovrei fare il controllo che se questa risposta è già stata inserita fa l'update***/
                                    //se è stato premuto un radio inserisco la risposta
                                    $risposta="INSERT INTO risposta(Progressivo, TitoloTest, MailStudente, Testo) VALUES ('$progressivo','$titoloTest', '$mail', '$testo' );";
                                    $risris = mysqli_query($conn, $risposta);
                                    if (!$risris) {
                                        echo "Errore nell'inserimento dela risposta: " . mysqli_error($conn);
                                    }
                                    
                                }

                        }
                    }

                }
            }

            $stato="Concluso";
            $inserimento="UPDATE svolgimento SET stato='$stato' WHERE MailStudente='$mail' AND TitoloTest='$titoloTest';";
            $ris_ins = mysqli_query($conn, $inserimento);
            if (!$ris_ins) {
                echo "Errore nell'inserimento in svolgimento2: " . mysqli_error($conn);
            }

            

            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                echo "Errore durante il commit della transazione.";
            }
    
                // chiusura della connessione
                mysqli_close($conn);
                unset($_SESSION["risposta$progressivo"]);
                header("Location: hpStudente.php");
                exit();
                
        }
        else{
            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                echo "Errore durante il commit della transazione.";
            }
                // chiusura della connessione
                mysqli_close($conn);
        }
    ?>
    </div>
</body>
</html>
