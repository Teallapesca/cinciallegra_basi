<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classifiche</title>
    <style>
        .classifiche-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* Spazio tra le classifiche */
        }

        .classifica {
            flex: 1 1 calc(33% - 20px); /* Distribuisci lo spazio equamente tra le classifiche */
            border: 1px solid #ccc;
            padding: 10px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        include 'connessione.php';
        mysqli_begin_transaction($conn);
    ?>
    <div class="intesta">
			<h1>CLASSIFICHE</h1>
	</div>
    <div class="classifiche-container">
        <!-- Contenuto delle classifiche -->
        <?php
            // Query per visualizzare la classifica degli studenti in base al numero di test completati
            $sql1 = "SELECT * FROM classificaconcluso";
            $result1 = $conn->query($sql1);

            if ($result1->num_rows > 0) {
                echo "<div class='classifica'>";
                echo "<h2>Classifica per numero di test completati:</h2>";
                echo "<ol>";
                while($row = $result1->fetch_assoc()) {
                    echo "<li>Codice studente: " . $row["CodiceMatricola"] . " - Test completati: " . $row["Conteggio"] . "</li>";
                }
                echo "</ol>";
                echo "</div>";
            } else {
                echo "<div class='classifica'>Nessun risultato trovato per la classifica dei test completati.</div>";
            }

            $sql2 = "SELECT * FROM classificacorretto";
            $result2 = $conn->query($sql2);

            if ($result2->num_rows > 0) {
                echo "<div class='classifica'>";
                echo "<h2>Classifica per percentuale di risposte corrette:</h2>";
                echo "<ol>";
                while($row = $result2->fetch_assoc()) {
                    echo "<li>Codice studente: " . $row["CodiceMatricola"] . " - Risposte giuste: " . $row["Percentuale"] . "</li>";
                }
                echo "</ol>";
                echo "</div>";
            } else {
                echo "<div class='classifica'>Nessun risultato trovato per la classifica delle risposte corrette.</div>";
            }

            $sql3 = "SELECT * FROM classificaquesiti";
            $result3 = $conn->query($sql3);

            if ($result3->num_rows > 0) {
                echo "<div class='classifica'>";
                echo "<h2>Classifica dei quesiti:</h2>";
                echo "<ol>";
                while($row = $result3->fetch_assoc()) {
                    echo "<li>Descrizione quesito: " . $row["Descrizione"] . " - Numero risposte: " . $row["Conteggio"] . "</li>";
                }
                echo "</ol>";
                echo "</div>";
            } else {
                echo "<div class='classifica'>Nessun risultato trovato per la classifica dei quesiti.</div>";
            }

            // Chiudi la connessione al database
            $conn->close();
        ?>

    </div>
</body>
</html>
