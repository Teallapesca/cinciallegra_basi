<!doctype html>
<html>
<head>
    <link type="text/css" rel="stylesheet" href="grafica.css">
    <meta charset="UTF-8">
    <title>Svolgimento test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <!--<style>
    .fixed-left {
      position: fixed;
      top: 20%;
      bottom: 0;
      left: 0;
      width: 400px; /* Larghezza del div sinistro */
      height:80%;
      background-color: #ffffff; /* Colore di sfondo del div sinistro */
      overflow: bottom;
    }
    .scroll-right {
      position: relative;
      margin-left: 200px; /* Larghezza del div sinistro */
      margin-right: -20px; /* Margine sinistro negativo */
      /* padding-bottom: 100px; */ /* Imposta lo spazio sotto il div destro */
    }
    </style>-->
            
    <script>
        function leggiMessaggi() {
            var messaggiContent = document.getElementById("messaggiContent");
            if (messaggiContent.style.display === "block") {
                messaggiContent.style.display = "none";
            } else {
                messaggiContent.style.display = "block";
                messaggiForm.style.display = "none";
            }
        }

        function scriviMessaggi() {
            var messaggiForm = document.getElementById("messaggiForm");
            if (messaggiForm.style.display === "block") {
                messaggiForm.style.display = "none";
            } else {
                messaggiForm.style.display = "block";
                messaggiContent.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        include 'connessione.php';
        mysqli_begin_transaction($conn);
        include_once 'ConnessioneMongoDB.php';
        include 'controlli.php';
    ?>
    <div class="intesta">
        <h1>SVOLGI IL TEST</h1>
    </div>
    <div class="messaggi-dropdown">  
        <button class="messaggi-icon" onclick="leggiMessaggi()"><u>Messaggi ricevuti</u></button>
        <div class="messaggi-content" id="messaggiContent">
            <!-- Contenuto dei messaggi qui -->
            <?php
              $titoloTest = $_SESSION['titoloTest'];            
              echo "<h4>".$_SESSION['titoloTest']."</h4>";        
              $query1 = "SELECT * FROM MESSAGGIODOCENTE WHERE TitoloTest = '$titoloTest'";
              $result1 = $conn->query($query1);
      
              if ($result1->num_rows > 0) {
                  echo "<ul>";
                  while ($row = $result1->fetch_assoc()) {
                      echo "<li class='messaggi'><b>{$row['MailDocente']} - {$row['DataInserimento']}</b><br>  {$row['TitoloMess']}<br> {$row['Testo']}</li>";
                  }
                  echo "</ul>";
              } else {
                  echo "<p class='messaggi'>Nessun messaggio trovato per questo test.</p>";
              }

            ?>
        </div>
    </div>

    <div class="messaggi-scrivi">
        
        <button class="messaggi-icon" onclick="scriviMessaggi()"><u>Inserisci messaggio</u></button>
        <div class="messaggi-form" id="messaggiForm">
        <form name="invio-messaggio" method="GET" action="SvolgiTest.php?titoloTest=<?php $_SESSION['titoloTest']?>">
                        <label class='messaggi'>Oggetto del messaggio:<label>
                        <input type='text' name='titoloMess' value='' class="textfield"><br><br>
                        <label class='messaggi'>Testo del messaggio:<label>
                        <input type='text' name='testoMess' value='' style='height: 100px' class="textfield"><br><br>
                        <input type='submit' name='invio' value='Invia messaggio' class="button">
                    </form>
            
            <!-- Contenuto dei messaggi qui -->
            <?php
               if (isset($_GET['invio'])) {
                    $titoloMess = $_GET['titoloMess'];
                    $testoMess = $_GET['testoMess'];
                    $titoloTest = $_SESSION['titoloTest'];
                    $mailDocente = $_SESSION['mailDocente'];
                    $mailStudente = $_SESSION['mailStudente'];
            
                    $query2 = 'CALL InserimentoMessaggioStudente("'.$titoloMess.'", "'.$testoMess.'", "'.$titoloTest.'", "'.$mailStudente.'", "'.$mailDocente.'")';
                    $result2 = mysqli_query($conn, $query2);
            
                    if (!$result2) {
                        echo "errore nella ricerca" . die (mysqli_error($conn));
                    }
                    else {
                        echo "<label class='sobrio'>Messaggio inviato</label>";
                    }

                    if (!mysqli_commit($conn)) {
                        mysqli_rollback($conn);
                        echo "Errore durante il commit della transazione.";
                    }
                }

            ?>
            
        </div>
    </div>

    <div class="principale">
            <?php

                $test=$_SESSION['titoloTest'];//non dovrebbe servire, ma per ora teniamolo
                $mail=$_SESSION['mailStudente']; 
                $_SESSION["quesiti$test"]=[];//creo l'array che conterrà i miei quesiti
                               
                if (isset($_GET['titolo'])) {//eseguirò tutto questo codice se non c'è stato alcun probelma di settaggio del test scelto
                    $_SESSION['titoloTest']=$_GET['titolo'];
                    $titoloTest = $_GET['titolo'];
                    echo "<h1>$titoloTest</h1>";
                    $sql="SELECT Foto FROM Test WHERE Titolo = '$titoloTest'";
                    $result = $conn->query($sql);

                if ($result === false) {
                    // Stampa l'errore della query
                    echo "Errore nella query SQL: " . $conn->error;
                    exit();
                }
                
                $fotoTest = '';
                if ($row = $result->fetch_assoc()) {
                    $fotoTest = $row['Foto'];
                }

                if($fotoTest != ''){
                    $relativePath = '/img/' . $fotoTest;
                    echo "<img src='" . htmlspecialchars($relativePath, ENT_QUOTES, 'UTF-8') . "' style = 'width:30%'>";
                }
                   
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
                        $stato="Aperto";
                        $inserimento="INSERT INTO svolgimento(MailStudente, TitoloTest, DataInizio) VALUES ('$mail', '$titoloTest', '$data');";
                        $ris_ins = mysqli_query($conn, $inserimento);
                        if (!$ris_ins) {
                            echo "Errore nell'inserimento in svolgimento: " . mysqli_error($conn);
                        }else{
                            //logEvent("Nuovo test $titoloTest inserito in svolgimento");
                        }
                        if (!mysqli_commit($conn)) {
                            mysqli_rollback($conn);
                            echo "Errore durante il commit della transazione. boo";
                        }
                    }
                }
            ?>
        <div class="principale">
            <?php
                    //---visualizza tabelle
                    $tabelle = [];
                    // Esegui una singola query per ottenere tutti i dati necessari
                    $query = "CALL VisualizzaTabella(?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $titoloTest);
                    $stmt->execute();
                    $risultato = $stmt->get_result();
                    //$risultato = mysqli_query($conn, $query);
                    if (!$risultato) {
                        echo "Errore nella visualizzazione delle tabelle e degli attributi: " . mysqli_error($conn);
                    } else {
                        // Memorizza gli attributi per ciascuna tabella in un array associativo
                        while ($row = mysqli_fetch_array($risultato, MYSQLI_ASSOC)) { 
                            $tabella = $row['NomeTabella'];
                            $attributo = $row['NomeAttributo'];
                            $tabelle[$tabella][] = $attributo;
                    }
                    $stmt->close();
                    // Itera sulle tabelle e sugli attributi di ciascuna tabella
                    foreach ($tabelle as $tabella => $attributi) {
                        echo "<h4 class='text-xxl-start'>".$tabella."</h4>";
                        echo "<table  class='table table-bordered table-sm w-25 p-3'>";
                        echo "<tr>";
                        // Stampa gli attributi come intestazioni della tabella
                        foreach ($attributi as $attributo) {
                            echo "<th scope='col' class='table-primary' >".$attributo."</th>";
                        }
                        echo "</tr>";

                        // Popola la tabella con i dati
                        $popolazione_query = "SELECT * FROM $tabella;";
                        $popolazione_risultato = mysqli_query($conn, $popolazione_query);
                        if (!$popolazione_risultato) {
                            echo "Errore nella visualizzazione della popolazione: " . mysqli_error($conn);
                        } else {
                                while ($pop = mysqli_fetch_array($popolazione_risultato, MYSQLI_ASSOC)) { 
                                    echo "<tr>";
                                    // Stampa i valori degli attributi per ciascuna riga
                                    foreach ($attributi as $attributo) {
                                        echo "<td class='table-secondary'>".$pop[$attributo]."</td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                            echo "</table>";
                        }
                    }
            ?>
        </div>
        <div class="principale">
        <form name="test" method="GET" action="SvolgiTest.php?titolo=<?php $_SESSION['titoloTest'] ?>">
            <?php
                    //---visualizzazione quesiti
                    $num=0;
                    //seleziono i quesiti del test aperto
                    //$query =  "SELECT TitoloTest, Progressivo, Difficolta, Descrizione FROM QUESITO WHERE TitoloTest = '$titoloTest';"; 
                    
                    $query="CALL VisualizzazioneQuesiti(?);";
                    //$risultato = mysqli_query($conn, $query);

                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $titoloTest);
                    $stmt->execute();
                    $risultato = $stmt->get_result();

                    if (!$risultato) {
                        echo "Errore nella query che seleziona i quesiti: " . mysqli_error($conn);
                    } else {
                        $_SESSION['numQuesiti']=mysqli_num_rows($risultato);
                        while ($row = mysqli_fetch_array($risultato, MYSQLI_ASSOC)) { 
                            $descrizione = $row['Descrizione'];
                            $progressivo = $row['Progressivo'];
                            if(!(isset($_SESSION["risposta$progressivo"]))){
                                $_SESSION["risposta$progressivo"]="";
                            }
                            //salvo nell'array i quesiti
                            $quesito=$_SESSION["quesiti$titoloTest"][] = Array("progressivo" => $progressivo,
                            "descrizione" => $descrizione);
                        }
                    }

                    $stmt->close();
                    foreach($_SESSION["quesiti$titoloTest"] as $key => $quesito){

                        $progressivo=$quesito["progressivo"];
                        $descrizione=$quesito["descrizione"];
                        riempimento($titoloTest, $mail, $conn, $progressivo);
                        $tipo="";
                        //composizione dei quesiti
                        $num=$num+1;
                        echo "<br> ---------------------------------------- <br>";
                        //echo "<hr class='border border-danger border-2 opacity-50'>";
                        echo "<br> <span class='badge text-bg-info-emphasis'>".$num . "</span><br>";
                        echo "<h3> ". $descrizione." </h3><br> Inserisci la tua risposta:<br>";
                        //vado a controllare per ogni riga se è un quesito chiuso oppure no
                        //query per vedere se è un quesito chiuso e le sue opzioni
                        $chiuso_query = "SELECT * FROM quesito_chiuso as c, opzione as o WHERE c.Progressivo=o.ProgressivoChiuso AND c.Progressivo='$progressivo' AND c.TitoloTest='$titoloTest'";
                        $ris_chiuso = mysqli_query($conn, $chiuso_query);
                        if (!$ris_chiuso) {
                            echo "Errore nella query 2: " . mysqli_error($conn);
                        }else {
                            mysqli_num_rows($ris_chiuso);
                            $giusto=controlloRisposta($mail, $conn, $progressivo);
                            if(mysqli_num_rows($ris_chiuso)==0){
                                $tipo="codice";
                               if($giusto==false){
                                    if (($_SESSION["risposta$progressivo"]!="")) {
                                        //se la variabile di risposta di questo quesito non è vuoto, vado già ad inserire il suo valore nel value del textbox
                                        $risp=$_SESSION["risposta$progressivo"];
                                        echo "<input type='text' name='risposta$progressivo' value='$risp'><br><br>";
                                        
                                    }else{
                                        //altrimenti faccio un textbox vuoto
                                        echo "<input type=text name='risposta$progressivo' value=''><br><br>";
                                    }
                                }else{
                                    if (($_SESSION["risposta$progressivo"]!="")) {
                                        //se la variabile di risposta di questo quesito non è vuoto, vado già ad inserire il suo valore nel value del textbox
                                        $risp=$_SESSION["risposta$progressivo"];
                                        echo "<input type='text' name='risposta$progressivo' value='$risp' readonly><br><br>";
                                    }
                                    echo "<br><span style='color: #198754;'>Risposta corretta</span><br>";
                                }
                            }
                            else{
                                $tipo="chiuso";
                                
                                if($giusto==true){
                                    while($rowOP = mysqli_fetch_array($ris_chiuso, MYSQLI_ASSOC)){
                                        $testoOp = $rowOP['Testo'];
                                        $numOP=$rowOP['Numerazione'];
                                        //se la variabile risposta è piena 
                                        if (($_SESSION["risposta$progressivo"]=="$numOP")) {//se la session è uguale al valore dell'opzione la restituisco già checkata
                                            echo "<input type=radio name='risposta$progressivo' value='$numOP' checked >$testoOp<br>";
                                        }
                                        else{
                                            echo "<input type=radio name='risposta$progressivo' value='$numOP' disabled>$testoOp<br>";
        
                                        }
                                    }
                                    echo "<span style='color: #198754;'>Risposta corretta</span><br>";
                                }else{
                                    while($rowOP = mysqli_fetch_array($ris_chiuso, MYSQLI_ASSOC)){
                                        $testoOp = $rowOP['Testo'];
                                        $numOP=$rowOP['Numerazione'];
                                        //se la variabile risposta è piena 
                                        if (($_SESSION["risposta$progressivo"]=="$numOP")) {//se la session è uguale al valore dell'opzione la restituisco già checkata
                                            echo "<input type=radio name='risposta$progressivo' value='$numOP' checked>$testoOp<br>";
                                        }
                                        else{
                                            echo "<input type=radio name='risposta$progressivo' value='$numOP'>$testoOp<br>";
        
                                        }
                                    }
                                }
                                
                            }
                            
                            mysqli_free_result($ris_chiuso);
                        }
                        $_SESSION["quesiti$titoloTest"][$key]["tipo"]=$tipo;
                    }
                    //var_dump($_SESSION["quesiti$titoloTest"]);
                } else {
                    echo "Nessun titolo test specificato nella query string.";
                }
            ?>
            <br> ---------------------------------------- <br>
            <br>
            <input type="hidden" name="titolo" value="<?php echo $_SESSION['titoloTest']; ?>">            
            <input type="submit" name="esci" value="Esci" Class="button">
        </form>
        </div>
            </div>
        <?php
    //controllo se le risposte sono giuste, salvataggio delle risposte anche se non è stato completato il test e inserimento in risposte
        $titoloTest = $_SESSION['titoloTest'];
        $mail=$_SESSION['mailStudente']; 
      
        if(isset($_GET["esci"])){

            foreach ($_SESSION["quesiti$titoloTest"] as $quesito) {

                $progressivo=$quesito["progressivo"];
                $tipo=$quesito["tipo"];

                $testo=$_GET["risposta$progressivo"];
                $_SESSION["risposta$progressivo"]=$testo;

                if($tipo=="codice"){

                    // controllo l'esito
                    $esitorisp=0;
                    $esito=controlloCodice($conn, $progressivo, $testo);
                    $query_esito="SELECT * FROM sketch_codice WHERE Progressivo=$progressivo AND Soluzione='$testo';";
                    $risultesito = mysqli_query($conn, $query_esito);
                    if (!$risultesito) {
                        echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
                    }else{
                        if( mysqli_num_rows($risultesito) != 0 || $esito==1){
                            $esitorisp=1;
                            echo "inserito esito corretto";
                        }
                    }

                    if($_SESSION["risposta$progressivo"]!=""){
                        risposta($conn, $mail, $titoloTest, $progressivo, $testo, $esitorisp);
                    }
                    
                }elseif($tipo=="chiuso"){

                    // controllo l'esito
                    $esitorisp=0;
                    $query_esito="SELECT * FROM quesito_chiuso WHERE Progressivo=$progressivo AND OpzioneGiusta=$testo;";
                    $risultesito = mysqli_query($conn, $query_esito);
                    if (!$risultesito) {
                        echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
                    }else{
                        if( mysqli_num_rows($risultesito) != 0){
                            $esitorisp=1;
                        }
                    }

                    if(isset($_SESSION["risposta$progressivo"])&&($_SESSION["risposta$progressivo"]!="")){
                        risposta($conn, $mail, $titoloTest, $progressivo, $testo, $esitorisp);
                    }
                }
                
            }

            // chiusura della connessione
            mysqli_close($conn);
            unset($_SESSION["quesiti$titoloTest"]);
            //torno alla pagina precedente
            header("Location: hpStudente.php");
            exit();
                
        }
        //se tutti i quesiti hanno ricevuto una risposta e questa è corretta si può concludere il test
        
        /*$num_righe=controlloTotale($titoloTest, $mail, $conn);
        if($_SESSION['numQuesiti']==$num_righe){
            


        }*/
    
        /*else if(isset($_GET['Fine'])){

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
                            $ris_ins3 = mysqli_query($conn, $inserimento);
                            if (!$ris_ins3) {
                                echo "Errore nell'inserimento in svolgimento2: " . mysqli_error($conn);
                            }
                        }
                        else{ //in questo caso io sto inserendo la nuova risposta (quesito o codice)
                            if(isset($_GET["risposta$progressivo"])){
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
            }//fare update anche della data di fine

            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                echo "Errore durante il commit della transazione. boo";
            }
    
            // chiusura della connessione
            mysqli_close($conn);
            foreach ($_SESSION["quesiti$titoloTest"] as $quesito) {
                $progressivo=$quesito["progressivo"];
                unset($_SESSION["risposta$progressivo"]);
            }
            unset($_SESSION['titoloTest']);
            header("Location: hpStudente.php");
            exit();
                
        }*/
       
    ?>
    </div>
    <script src="path/to/bootstrap/js/bootstrap.min.js"></script>

</body>
</html>
