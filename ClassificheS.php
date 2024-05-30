<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link type="text/css" rel="stylesheet" href="grafica.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <title>Classifiche</title>
</head>
<body>
    <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        include 'connessione.php';
        mysqli_begin_transaction($conn);
    ?>
    <div class="intesta">
        <a href="hpStudente.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-arrow-left-circle-fill">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </a>
			<h1>CLASSIFICHE</h1>
	</div>
    <div class="classifiche-container">
        <!-- Contenuto delle classifiche -->
        <?php
            // Query per visualizzare la classifica degli studenti in base al numero di test completati
            $sql1 = "SELECT * FROM classificaconcluso";
            $result1 = $conn->query($sql1);

            
            echo "<div class='classifica'>";
            echo "<h3>Classifica per numero di test completati:</h3>";
            if ($result1->num_rows > 0) {
                echo "<ol>";
                while($row = $result1->fetch_assoc()) {
                    echo "<li>Codice studente: " . $row["CodiceMatricola"] . " - Test completati: " . $row["Conteggio"] . "</li>";
                }
                echo "</ol>";
            } else {
                echo "<label class='lab-class'>Nessun risultato trovato per la classifica dei test completati.</label>";
            }
            echo "</div>";

            $sql2 = "SELECT * FROM classificacorretto";
            $result2 = $conn->query($sql2);

            
            echo "<div class='classifica'>";
            echo "<h3>Classifica per percentuale di risposte corrette:</h3>";
            if ($result2->num_rows > 0) {
                echo "<ol>";
                while($row = $result2->fetch_assoc()) {
                    echo "<li>Codice studente: " . $row["CodiceMatricola"] . " - Risposte giuste: " . $row["Percentuale"] . "</li>";
                }
                echo "</ol>";
            } else {
                echo "<label class='lab-class'>Nessun risultato trovato per la classifica delle risposte corrette.</label>";
            }
            echo "</div>";

            $sql3 = "SELECT * FROM classificaquesiti";
            $result3 = $conn->query($sql3);

            
            echo "<div class='classifica'>";
            echo "<h3>Classifica dei quesiti:</h3>";
            if ($result3->num_rows > 0) {
                echo "<ol>";
                while($row = $result3->fetch_assoc()) {
                    echo "<li>Descrizione quesito: " . $row["Descrizione"] . " - Numero risposte: " . $row["Conteggio"] . "</li>";
                }
                echo "</ol>";
            } else {
                echo "<label class='lab-class'>Nessun risultato trovato per la classifica dei quesiti.</label>";
            }
            echo "</div>";

            // Chiudi la connessione al database
            $conn->close();
        ?>

    </div>
</body>
</html>
