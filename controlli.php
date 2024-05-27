<?php
   include_once 'ConnessioneMongoDB.php'; 
   //funzione per controllare se tutto il test è corretto
    function controlloTotale($titoloTest, $mail, $conn){
        $num_righe=0;
        if(!empty($_SESSION["quesiti$titoloTest"])){
            foreach ($_SESSION["quesiti$titoloTest"] as $quesito) {
    
                $progressivo=$quesito["progressivo"];
                //$tipo=$quesito["tipo"];
    
                //$testo=$_SESSION["risposta$progressivo"];
                //$_SESSION["risposta$progressivo"]=$testo;
    
                $query="SELECT * FROM risposta WHERE ProgressivoQuesito=$progressivo AND MailStudente='$mail' AND Esito=1;";
                $risult = mysqli_query($conn, $query);
                if (!$risult) {
                    echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
                }else{
                    if( mysqli_num_rows($risult)!= 0){
                        $num_righe=$num_righe+1;
                    }
                }
    
            }
        }
        return $num_righe;
    }

    //controllo se la risposta al quesito chiuso è corretta
    function controlloRisposta($mail, $conn, $progressivo){
        $query="SELECT * FROM risposta WHERE ProgressivoQuesito=$progressivo AND MailStudente='$mail' AND Esito=1;";
        $risult = mysqli_query($conn, $query);
        if (!$risult) {
            echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
        }else{
            if(mysqli_num_rows($risult)!= 0){
                //echo "<br><span style='color: #198754;'>Risposta corretta</span><br>";
                return true;
            }
        }
        return false;
    }
    
    //funzione per salvare le risposte date nel test quando si esce
    function riempimento($titoloTest, $mail, $conn, $progressivo){
        $query =  "SELECT * FROM risposta WHERE TitoloTest = '$titoloTest' AND MailStudente='$mail';"; 
        $ris_codice = mysqli_query($conn, $query);
        if (!$ris_codice) {
            echo "Errore nel riempimento: " . mysqli_error($conn);
        }else {
            while($row = mysqli_fetch_array($ris_codice, MYSQLI_ASSOC)) { 
                $testo = $row['Testo'];
                $progr = $row['ProgressivoQuesito'];
                if($progr==$progressivo){
                    $_SESSION["risposta$progressivo"]=$testo;
                }
                
            }
        }
    }

    //funzione per controllare se la risposta del quesito aperto è un codice o no
    function ControlloQuery($prima_parola){
        if($prima_parola=="SELECT"||$prima_parola=="UPDATE"||$prima_parola=="CREATE"||$prima_parola=="DELETE" ||$prima_parola=="INSERT"){
            return true;
        }
        else{
            return false;
        }
    }

    //funzione per controllare se il codice scritto dallo studente è corretto
    function controlloCodice($conn, $progressivo, $risposta){

        $soluzione="";
        $esito=0;
        $query="SELECT Soluzione FROM sketch_codice WHERE Progressivo=$progressivo;"; //prendo il testo della soluzione del prof
        $risult = mysqli_query($conn, $query);
        if (!$risult) {
            echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
        }else{
            $row = mysqli_fetch_assoc($risult);

            foreach ($row as $elemento) {
                $prima_parola = strstr($elemento, ' ', true);
            }
            $controlloQuery=ControlloQuery($prima_parola); 
            if($controlloQuery==true){
                $query_sol="$row[Soluzione];"; //eseguo la query impostata come soluzione
                $risultato = mysqli_query($conn, $query_sol);
                if (!$risultato) {
                    echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
                }else{
                    $soluzione=mysqli_fetch_assoc($risultato);
                }
            }
        }

        //eseguo la query data in risposta dallo studente
        $risp="";
        $parole=explode(' ', $risposta);
        $prima_parola = $parole[0];
        $controlloQuery=ControlloQuery($prima_parola); 
        if($controlloQuery==true){
            $query_risp="$risposta";
            $risult = mysqli_query($conn, $query_risp);
            if (!$risult) {
                echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
            }else{
                $risp = mysqli_fetch_assoc($risult);
                
            }
        }

        if(($risp==$soluzione)&&($risp!="")&&($soluzione!="")){
            $esito=1;
        }

        return $esito;
    }

    //funzione per inserire o aggiornare la risposta dello studente
    function risposta($conn, $mail, $titoloTest, $progressivo, $testo, $esitorisp) {
                 
        //cerco se sono già state date risposte a questo quesito
        $selezionato="SELECT * FROM risposta WHERE MailStudente='$mail' AND TitoloTest='$titoloTest' AND ProgressivoQuesito=$progressivo;";
        $rissel = mysqli_query($conn, $selezionato);
        if (!$rissel) {
            echo "Errore nell'inserimento della risposta: " . mysqli_error($conn);
        }else{
            if(mysqli_num_rows($rissel) != 0){
                //aggiorno con la nuova risposta del quesito
                //$inserimento="UPDATE risposta SET Testo='$testo', Esito=$esitorisp WHERE MailStudente='$mail' AND TitoloTest='$titoloTest' AND Progressivo='$progressivo';";
                $inserimento="CALL AggiornaRisposta('$testo',$esitorisp,'$titoloTest','$mail',$progressivo);";
                $ris_ins2 = mysqli_query($conn, $inserimento);
                if (!$ris_ins2) {
                    echo "Errore nell'aggiornamento della risposta: " . mysqli_error($conn);
                }
            }
            else{ //in questo caso io sto inserendo la nuova risposta (quesito o codice)
                if(isset($_GET["risposta$progressivo"])){
                    //se è stato premuto un radio inserisco la risposta
                   // $risposta="INSERT INTO risposta(Progressivo, TitoloTest, MailStudente, Esito, Testo) VALUES ('$progressivo','$titoloTest', '$mail', '$esitorisp', '$testo' );";
                    $risposta="CALL InserimentoRisposta('$progressivo','$titoloTest', '$mail', '$esitorisp', '$testo' );";

                    $risris = mysqli_query($conn, $risposta);
                    if (!$risris) {
                        echo "Errore nell'inserimento della risposta2: " . mysqli_error($conn);
                    }else{
                        logEvent("Nuova risposta a $titoloTest inserita ");
                    }
                }
            }
            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                echo "Errore durante il commit della transazione. quii??";
            }
        }
    }


?>