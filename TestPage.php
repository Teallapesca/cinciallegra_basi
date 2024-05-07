<!DOCTYPE html>
<html>

<head>
    <title>Creazione Test</title>
    <link type="text/css" rel="stylesheet" href="stile.css">
    <style>
        form {
            margin-bottom: 20px; /* Aggiunge spazio sotto il modulo */
        }
    </style>
    <style>
                .messaggi-dropdown {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                }
                .messaggi-icon {
                    background: none;
                    border: none;
                    cursor: pointer;
                }
                .messaggi-content {
                    display: none;
                    position: absolute;
                    top: 30px;
                    right: 0;
                    width: 200px;
                    background-color: #f9f9f9;
                    border: 1px solid #ddd;
                    padding: 10px;
                    z-index: 1;
                }
                .messaggi-scrivi {
                    position: fixed;
                    top: 20px;
                    right: 160px;
                }
                .messaggi-icon {
                    background: none;
                    border: none;
                    cursor: pointer;
                }
                .messaggi-form {
                    display: none;
                    position: absolute;
                    top: 30px;
                    right: 60;
                    background-color: #f9f9f9;
                    border: 1px solid #ddd;
                    padding: 10px;
                    z-index: 1;
                }
            </style>
            
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

    <h1>Creazione di un Nuovo Test</h1>
    <form action="TestPage.php" method="GET">
        Titolo del Test:<br><br>
        <input type="text" name="test_title">
        <br><br> <input type=checkbox name=visualizza> Visualizzazione delle risposte <br><br>
        <label for="img">Scegli immagine:</label>
        <input type="text" id="testImg" name="testImg"><br>
        <button type="submit" name="crea_test">Crea Test</button><br><br><br>
        </form>
        <h2>Inserisci i quesiti</h2>        
        <br>
        <a href="CreaQuesito.php"><button type="button" name=quesito>Aggiungi Quesito</button></a>
  

    
    <a href=hpDocente.php><h2><-</h2></a>
</body>

</html>

<?php
// Connessione al database
    ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include 'connessione.php';
	mysqli_begin_transaction($conn);
    
    
	if(isset($_GET["crea_test"])) {
        $mail = $_SESSION['mailDocente'];
        $_SESSION['test_title']=$_GET['test_title'];
        $test_title=$_SESSION['test_title'];
        $visualizza=0;
        if(isset($_GET['visualizza'])){
            $visualizza=1;
        }
        
        $_SESSION['testImg'] = $_GET['testImg'];
        $testImg = $_SESSION['testImg'];
        $testImg = addslashes($testImg);
        

        //echo "dati: ".$mail." ".$test_title." ".$visualizza;
        // Controlla se il test esiste già nel database
        $query = "SELECT * FROM test WHERE Titolo = '$test_title'";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) > 0) {
            // Test già presente nel database
            echo "Test già presente nel database.";
        }else {
            // Esegui la query per inserire il titolo del test nel database
            $sql = "CALL CreaTest('$test_title', '$testImg', '$visualizza', '$mail')";
            
            $risultato=mysqli_query($conn, $sql);
            if ($risultato === false) {
                // Errore durante la creazione del test
                echo "Errore durante la creazione del test: " . mysqli_error($conn);
                
            } else {
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
<<<<<<< HEAD
=======

>>>>>>> 5d266fa9b442be930038b3f645d2964bcd44465a
