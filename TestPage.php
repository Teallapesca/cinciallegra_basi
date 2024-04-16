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
</head>

<body>

    <h1>Creazione di un Nuovo Test</h1>
    <form action="TestPage.php" method="GET">
        Titolo del Test:<br><br>
        <input type="text" name="test_title">
        <br><br> <input type=checkbox name=visualizza> Visualizzazione delle risposte <br><br>
        <button type="submit" name="crea_test">Crea Test</button><br><br><br>
    </form>
        
  



<?php
// Connessione al database
    ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include 'connessione.php';
	mysqli_begin_transaction($conn);
    
    
	if(isset($_GET["crea_test"])) {
        $mail = $_SESSION['mailDocente'];
        if($_GET['test_title']!=""){
            $_SESSION['test_title']=$_GET['test_title'];
            $test_title=$_SESSION['test_title'];
            $visualizza=0;
            if(isset($_GET['visualizza'])){
                $visualizza=1;
            }
            $foto=0;

            //echo "dati: ".$mail." ".$test_title." ".$visualizza;
            // Controlla se il test esiste già nel database
            $query = "SELECT * FROM test WHERE Titolo = '$test_title'";
            $result = mysqli_query($conn, $query);

            
            if(mysqli_num_rows($result) > 0) {
                // Test già presente nel database
                echo "Test già presente nel database.";
            }else {
                // Esegui la query per inserire il titolo del test nel database
                $sql = "CALL CreaTest('$test_title', '$foto', '$visualizza', '$mail')";
                //$sql='INSERT INTO test (Titolo, Foto, VisualizzaRisposte, MailDocente) VALUES ("'.$test_title.'"," '. $foto .'"," '. $visualizza .'"," '.$mailValue.'");';


                /*$stmt = $conn->prepare("INSERT INTO test (Titolo, Foto, VisualizzaRisposte, MailDocente) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("siis", $test_title, $foto_value, $visualizza_value, $mailValue);
                $foto_value = 0; // Assumendo che Foto sia di tipo INT
                $visualizza_value = 0;
                $stmt->execute();
                if ($stmt->affected_rows === -1) {
                    // Errore durante l'esecuzione dell'istruzione preparata
                    echo "Errore durante la creazione del test: " . $stmt->error;
                } else {
                    echo "Test creato.";
                }*/



                $risultato=mysqli_query($conn, $sql);
                if ($risultato === false) {
                    // Errore durante la creazione del test
                    echo "Errore durante la creazione del test: " . mysqli_error($conn);
                    
                } else {
                    echo "<h2>Inserisci i quesiti</h2>        
                            <br>
                        <a href='CreaQuesito.php'><button type='button' name=quesito>Aggiungi Quesito</button></a>
                    
                    ";
                }
            }
        }else{
            echo die("Inserisci il titolo del test <br><br> 
                    <a href=hpDocente.php><h2><-</h2></a>");
        }
        
    }

    if (!mysqli_commit($conn)) {
        mysqli_rollback($conn);
        echo "Errore durante il commit della transazione.";
    }

    mysqli_close($conn);
?>

    
<a href=hpDocente.php><h2><-</h2></a>
</body>

</html>

