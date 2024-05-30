<!doctype html>
<html>

<head>
<link type="text/css" rel="stylesheet" href="grafica.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
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
    include_once 'ConnessioneMongoDB.php';
    ?>
    <div class="intesta">
    <a href="hpDocente.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-arrow-left-circle-fill">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </a>
        <h1>CREA QUESITO</h1>
    </div>
    <div class="principale">
        <form name="quesito" method="GET" action="CreaQuesito.php" onsubmit="return validateForm()">
            <label>Descrizione quesito</label><br><br>
            <input type="text" name="descQuesito" value="" class="textfield"><br><br>
            <label>Difficoltà quesito:&emsp;</label><input type=radio name=difficolta value=basso> Basso &nbsp &nbsp
            <input type=radio name=difficolta value=medio> Medio &nbsp &nbsp
            <input type=radio name=difficolta value=alto> Alto <br><br>
            <label>Tabella di riferimento:&emsp;</label>
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
            <label>Tipo di risposta:&emsp;</label>
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
            <input type="submit" name="Creaquesito" value="Crea" class='button'>
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

                $query="insert into rif_tabella_quesito(ProgressivoQuesito, TitoloTest, NomeTabella, MailDocente) values ('$progQuesito', '$test', '$tabella', '$mail')";
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
    </div>
</body>

</html>
