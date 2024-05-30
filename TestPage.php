<!DOCTYPE html>
<html>

<head>
    <title>Creazione Test</title>
    <link type="text/css" rel="stylesheet" href="grafica.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
     </style>
            
</head>

<body>
    <div class="intesta">
            <a href="hpDocente.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-arrow-left-circle-fill">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </a>
            <h1 class=title>CREAZIONE DI UN NUOVO TEST</h1>
    </div>
    <div class="principale">
    <form action="TestPage.php" method="POST" enctype="multipart/form-data">
        <label>Titolo del Test:</label><br><br>
        <input type="text" name="test_title" class="textfield">
        <br><br>
        <label for="img">Scegli immagine: (facoltativo) &emsp;</label><br><br>
        <input type="file" id="testImg" name="testImg"><br><br>
        <button type="submit" name="crea_test" class="button">Crea Test</button><br><br><br>
        </form>
        <h2>Inserisci i quesiti</h2>        
        <br>
        <a href="CreaQuesito.php"><button type="button" name=quesito class="button">Aggiungi Quesito</button></a>
    </div>
</body>

</html>

<?php
// Connessione al database
    ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include 'connessione.php';
    include 'ConnessioneMongoDB.php';
	mysqli_begin_transaction($conn);
    
    
	if(isset($_POST["crea_test"])) {
        $mail = $_SESSION['mailDocente'];
        $_SESSION['test_title']=$_POST['test_title'];
        $test_title=$_SESSION['test_title'];
        $visualizza=0;
        if(isset($_POST['visualizza'])){
            $visualizza=1;
        }
        
        $fileName = "";
        if (isset($_FILES["testImg"])) {
            $fileName = ($_FILES["testImg"]["name"]);
        }
        
        // Controlla se il test esiste già nel database
        $query = "SELECT * FROM test WHERE Titolo = '$test_title'";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) > 0) {
            // Test già presente nel database
            echo "Test già presente nel database.";
        }else {
            // Esecuzione della query per inserire il test nel database
            $sql = "CALL CreaTest('$test_title', '$fileName', '$visualizza', '$mail')";
            
            $risultato=mysqli_query($conn, $sql);
            if ($risultato === false) {
                // Errore durante la creazione del test
                echo "Errore durante la creazione del test: " . mysqli_error($conn);
                
            } else {
                //logEvent("Nuovo test $test_title inserito");
                echo "Test creato.";
            }
        }
    }

    if (!mysqli_commit($conn)) {
        mysqli_rollback($conn);
        echo "Errore durante il commit della transazione.";
    }

    mysqli_close($conn);
?>
