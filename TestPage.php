<?php
// Connessione al database
    ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include 'connessione.php';
	mysqli_begin_transaction($conn);
    
	if(isset($_POST["crea_test"])) {
        $mail = $_SESSION['mail'];
        $test_title=$_POST['test_title'];
        $visualizza=0;
        if(isset($_POST['visualizza'])){
            $visualizza=1;
        }
    

        echo "dati: ".$mail." ".$test_title." ".$visualizza;
        // Controlla se il test esiste già nel database
        $query = "SELECT * FROM test WHERE Titolo = '$test_title'";
        $result = mysqli_query($conn, $query);
        echo "tutto ok";
        if(mysqli_num_rows($result) > 0) {
            // Test già presente nel database
            echo "Test già presente nel database.";
        } else {
            // Esegui la query per inserire il titolo del test nel database
            $sql = "CALL CreaTest('$test_title', 0, '$visualizza', '$mail')";
            //$sql="INSERT INTO test (Titolo, DataTest, Foto, VisualizzaRisposte, MailDocente) VALUES ('$mail', 0, '$visualizza', '$mail');"
            $risultato=mysqli_query($conn, $sql);
            if ($risultato === false) {
                // Errore durante la creazione del test
                echo "Errore durante la creazione del test: " . mysqli_error($conn);
                
            } else {
                echo "Test creato.";
            }
        }
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>Creazione Test</title>
    <style>
        body {
            background-color: #ffcccc; /* Rosso chiaro chiaro */
        }
        form {
            margin-bottom: 20px; /* Aggiunge spazio sotto il modulo */
        }
    </style>
</head>

<body>

    <h1>Creazione di un Nuovo Test</h1>
    <form action="TestPage.php" method="post">
        Titolo del Test:<br><br>
        <input type="text" name="test_title">
        <br><br> <input type=checkbox name=visualizza> Visualizzazione delle risposte
        <button type="submit" name="crea_test">Crea Test</button>
    </form>
    
    <h2>Aggiungi un nuovo Quesito</h2>
    <a href="CreaQuesito.php"><button type="button">Aggiungi Quesito</button></a>
    

</body>

</html>