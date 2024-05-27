<!doctype html>
<html>

<head>
    <script>
        function mostraTesto(tipo) {
            var quesitoChiuso = document.getElementById("quesitoChiuso");
            var sketchCodice = document.getElementById("sketchCodice");

            if (tipo === "chiuso") {
                quesitoChiuso.style.display = "block";
                sketchCodice.style.display = "none";
            } else if (tipo === "sketch") {
                quesitoChiuso.style.display = "none";
                sketchCodice.style.display = "block";
            }
        }

        function validateForm() {
            // Ottieni i valori dei campi del modulo
            var descQuesito = document.forms["quesito"]["descQuesito"].value;
            var nt = document.forms["quesito"]["nt"].value;
            var tipo = document.querySelector('input[name="tipo"]:checked');
            var op1 = document.forms["quesito"]["op1"].value;
            var op2 = document.forms["quesito"]["op2"].value;
            var op3 = document.forms["quesito"]["op3"].value;
            var testosketch = document.forms["quesito"]["testosketch"].value;

            // Controlla se tutti i campi richiesti sono stati compilati o selezionati
            if (descQuesito == "" || nt == "" || !tipo || (tipo.value == "chiuso" && (op1 == "" || op2 == "" || op3 == "")) || (tipo.value == "sketch" && testosketch == "")) {
                alert("Compila tutti i campi richiesti!");
                return false; // Impedisci l'invio del modulo
            }
            return true; // Consenti l'invio del modulo
        }
    </script>
</head>

<body>
    <?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include 'connessione.php';
    mysqli_begin_transaction($conn);
    include 'ConnessioneMongoDB.php';
    ?>
    <div class="titolo">
        <h1>CREA QUESITO</h1>
    </div>
    <div class="principale">
        <form name="quesito" method="GET" action="CreaQuesito.php" onsubmit="return validateForm()">
            Descrizione quesito<br><br>
            <input type="text" name="descQuesito" value=""><br><br>
            Difficoltà quesito: <input type=radio name=difficolta value=basso> Basso &nbsp &nbsp
            <input type=radio name=difficolta value=medio> Medio &nbsp &nbsp
            <input type=radio name=difficolta value=alto> Alto <br><br>
            <select name="nt">
                <option> Seleziona La tabella </option>
                <?php
                $mail = $_SESSION['mailDocente'];
                $query = "SELECT Nome FROM tabella_esercizio WHERE MailDocente='$mail' ;";

                $ris = mysqli_query($conn, $query);

                if (!$ris) {
                    echo "ricerca fallita: " . die(mysqli_error($conn));
                }
                if (mysqli_num_rows($ris) == 0) {
                    echo "non ci sono righe" . die();
                }
                while ($row = mysqli_fetch_array($ris)) {
                    echo "<option value=" . $row['Nome'] . ">" . $row['Nome'] . "</option>";
                }

                if (!mysqli_commit($conn)) {
                    mysqli_rollback($conn);
                    echo "Errore durante il commit della transazione.";
                }

                ?>
            </select> <br><br>
            Tipo di risposta:
            <input type=radio name=tipo value=chiuso onChange="mostraTesto('chiuso')"> Quesito chiuso &nbsp &nbsp
            <input type=radio name=tipo value=sketch onChange="mostraTesto('sketch')"> Sketch di codice <br><br>

            <div id="quesitoChiuso" style="display:none;">
                    Opzione 1: &nbsp&nbsp <input type=text name=op1> &nbsp&nbsp <input type=radio name=giusta value=opr1> opzione giusta<br><br>
                    Opzione 2: &nbsp&nbsp <input type=text name=op2> &nbsp&nbsp <input type=radio name=giusta value=opr2> opzione giusta<br><br>
                    Opzione 3: &nbsp&nbsp <input type=text name=op3> &nbsp&nbsp <input type=radio name=giusta value=opr3> opzione giusta<br><br>
                    <!--<input type=submit name=opzione value=crea>-->
            </div>

            <div id="sketchCodice" style="display:none;">
                inserisci la soluzione: &nbsp&nbsp <input type=text name=testosketch>
            </div>
            <br>
            <input type="submit" name="Creaquesito" value="Crea">
        </form>

        <?php
            if (isset($_GET["Creaquesito"])) {
                //tabella di riferimento
                $_SESSION['tabella']=$_GET['nt'];
                $tabella=$_SESSION['tabella'];

                //numerazione del quesito
                $query = "SELECT * FROM quesito";
                $ris = mysqli_query($conn, $query);
                if (!$ris) {
                    echo "ricerca fallita: " . die(mysqli_error($conn));
                }
                $_SESSION['progQuesito'] = mysqli_num_rows($ris) + 1;
                $progQuesito = $_SESSION['progQuesito'];

                //difficolta
                $difficolta="";
                if(isset($_GET['difficolta'])){
                    if($_GET['difficolta']=="basso"){
                        $difficolta="Basso";
                    }else if($_GET['difficolta']=="medio"){
                        $difficolta="Medio";
                    }else if($_GET['difficolta']=="alto"){
                        $difficolta="Alto";
                    }
                }else{echo "seleziona una difficoltà" .die();}

                //descrizione
                $descrizione=$_GET['descQuesito'];
                $test = $_SESSION['test_title'];

                if ($_GET["tipo"]=="chiuso") {
                    $opzione1 = $_GET['op1'];
                    $opzione2 = $_GET['op2'];
                    $opzione3 = $_GET['op3'];
                    

                    //inserimento quesito chiuso
                    $query="CALL NewQuesitoChiuso('$progQuesito', '$test', '$difficolta', '$descrizione')";
                    $ris = mysqli_query($conn, $query);
                    if (!$ris) {
                        echo "inserimento quesito fallito: " . die(mysqli_error($conn));
                    }
                    else{
                        logEvent("Nuovo quesito $progQuesito inserito");
                    }

                    //numerazione della prima opzione
                    $numerazione = 1;

                    //inserimento delle opzioni nella tabella opzione
                    $giusta = "";
                    if(isset($_GET["giusta"])){

                        if ($_GET["giusta"]=="opr1") {
                            $giusta = 1;
                        }
                        $query1 = 'insert into opzione(Numerazione, ProgressivoChiuso, TitoloTest, Testo) values ("' . $numerazione . '"," ' . $progQuesito . '","' . $test . '","' . $opzione1 . '");';
                        $risult1 = mysqli_query($conn, $query1);
                        if (!$risult1) {
                            echo "ricerca fallita: " . die(mysqli_error($conn));
                        }
                        
    
                        //----op2
                        $numerazione = $numerazione + 1;
                        
                        if ($_GET["giusta"]=="opr2") {
                            $giusta = 2;
                        }
                        $query2 = 'insert into opzione(Numerazione, ProgressivoChiuso, TitoloTest, Testo) values ("' . $numerazione . '"," ' . $progQuesito . '","' . $test . '","' . $opzione2 . '");';
                        $risult2 = mysqli_query($conn, $query2);
                        if (!$risult2) {
                            echo "ricerca fallita: " . die(mysqli_error($conn));
                        }
    
                        //----op3
                        
                        if ($_GET["giusta"]=="opr3") {
                            $giusta = 3;
                        }
                        $numerazione = $numerazione + 1;
                        $query3 = 'insert into opzione(Numerazione, ProgressivoChiuso, TitoloTest, Testo) values ("' . $numerazione . '"," ' . $progQuesito . '","' . $test . '","' . $opzione3 . '");';
                        $risult3 = mysqli_query($conn, $query3);
                        $queryA = "UPDATE QUESITO_CHIUSO SET OpzioneGiusta = '$giusta' WHERE Progressivo = '$progQuesito';";
                        $risultA = mysqli_query($conn, $queryA);
                        if (!$risultA) {
                            echo "ricerca fallita: " . die(mysqli_error($conn));
                        }
                        logEvent("Nuova opzione $opzione3 inserita");

                    }
                    else{
                        echo"Scegli quale opzione è corretta";
                    }
                    
                
                }
                if ($_GET["tipo"]=="sketch") {
                    $soluzione=$_GET['testosketch'];

                    $query="CALL NewSketchCodice('$progQuesito', '$test', '$difficolta', '$descrizione', '$soluzione')";
                    $ris = mysqli_query($conn, $query);
                    if (!$ris) {
                        echo "inserimento quesito fallito: " . die(mysqli_error($conn));
                    }else{
                        logEvent("Nuovo quesito $progQuesito inserito");
                    }

                }

                //inserimento in rif_tabella_quesito

                $_SESSION['tabella']=$_GET['nt'];
                $tabella=$_SESSION['tabella'];

                $query="insert into rif_tabella_quesito(ProgressivoQuesito, TitoloTest, NomeTabella) values ('$progQuesito', '$test', '$tabella')";
                $risultato = mysqli_query($conn, $query);
                if (!$risultato) {
                    echo "inserimento fallito: " . die(mysqli_error($conn));
                }else{echo "<br> inserimento del quesito effettuato";}
                
            }
            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                echo "Errore durante il commit della transazione.";
            }
        
            mysqli_close($conn);
        ?>
        <br> <br> <a href=TestPage.php> <- </a>
    </div>
</body>

</html>
