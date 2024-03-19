<?php
// Connessione al database
    ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include 'connessione.php';
	mysqli_begin_transaction($conn);

	if(isset($_POST["crea_test"])) {
        $test_title = $_POST["test_title"];
        $mail = $_SESSION['mail'];
    
        // Controlla se il test esiste già nel database
        $query = "SELECT * FROM test WHERE Titolo = '$test_title'";
        $result = mysqli_query($conn, $query);
    
        if(mysqli_num_rows($result) > 0) {
            // Test già presente nel database
            echo "Test già presente nel database.";
        } else {
            // Esegui la query per inserire il titolo del test nel database
            $sql = "INSERT INTO test (Titolo, mail) VALUES ('$test_title', '$mail')";
            if (mysqli_query($conn, $sql)) {
                // Test creato con successo
                echo "Test creato.";
            } else {
                // Errore durante la creazione del test
                echo "Errore durante la creazione del test: " . mysqli_error($conn);
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
        <label for="test_title">Titolo del Test:</label>
        <input type="text" id="test_title" name="test_title">
        <button type="submit" name="crea_test">Crea Test</button>
    </form>

    <h2>Aggiungi un nuovo Quesito</h2>
    <form action="TestPage.php" method="post">
        <label for="question">Quesito:</label><br>
        <textarea id="question" name="question"></textarea><br><br>
        <button type="submit" name="crea_quesito">Aggiungi Quesito</button>
    </form>

</body>

</html>