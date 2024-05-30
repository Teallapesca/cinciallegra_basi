<!doctype html>
<html>
	<head>
    <link type="text/css" rel="stylesheet" href="grafica.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
            
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
		?>
		<div class="intesta">
            <a href="hpDocente.php" name="esci" id="esci">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-arrow-left-circle-fill">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </a>
			
            <h1>DETTAGLI TEST</h1>

            <div class="messaggi-dropdown">
                <button class="messaggi-icon" onclick="leggiMessaggi()"><u>Messaggi ricevuti</u></button>
                <div class="messaggi-content" id="messaggiContent">
                    <!-- Contenuto dei messaggi qui -->
                    <?php
                        $titoloTest = $_SESSION['titoloTest'];
                        echo "<h1 class='messaggi'>".$_SESSION['titoloTest']."</h1>";        
                        $query1 = "SELECT * FROM MESSAGGIOSTUDENTE WHERE TitoloTest = '$titoloTest'";
                        $result1 = $conn->query($query1);
                
                        if ($result1->num_rows > 0) {
                            echo "<ul>";
                            while ($row = $result1->fetch_assoc()) {
                                echo "<li class='messaggi'><b>{$row['MailStudente']} - {$row['DataInserimento']}</b><br>  {$row['TitoloMess']}<br> {$row['Testo']}</li>";
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
                    <form name="invio-messaggio" method="GET" action="DettagliTest.php?titoloTest=<?php $_SESSION['titoloTest']?>">
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
                    
                           $query2 = 'CALL InserimentoMessaggioDocente("'.$titoloMess.'", "'.$testoMess.'", "'.$titoloTest.'", "'.$mailDocente.'")';
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
        </div>

        <div class="principale">
            <?php
                $titoloTest = $_SESSION['titoloTest'];
                echo "<h1>$titoloTest</h1><br>";

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
                
                $query="CALL VisualizzazioneQuesiti(?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $titoloTest);
                $stmt->execute();
                $risultato = $stmt->get_result();
                $num=0;
                while($row = mysqli_fetch_array($risultato)){
                    $num=$num+1;
                    echo "
                        <p>$num) {$row['Descrizione']}<br>
                        Livello: {$row['Difficolta']}
                        </p>
                    ";                        
                }
                $risultato->free();
                $stmt->close();
                //$query->close();
                ?>

                <form method="get" action="DettagliTest.php?titoloTest=<?php $_SESSION['titoloTest']?>">
                    <label class="sobrio">Visualizza risposte&emsp;</label><input type='checkbox' id='VisRisp' name='VisRisp' value='VisRisp'><br><br>
                    <label class="sobrio">Elimina test&emsp;</label><input type='checkbox' id='elimina' name='elimina' value='elimina'><br><br>
                    <input type='submit' name='conferma' id='conferma' value='Conferma' class="button"><br><br>
                    <!--<input type='submit' name='elimina' id='elimina' value='Elimina Test' class="button">-->
                </form>

                <?php
                $titoloTest = $_SESSION['titoloTest'];
                $visualizza = 0;
                if(isset($_GET['conferma'])){
                    
                    if(isset($_GET['elimina'])){
                        //$titoloTest = $_SESSION['titoloTest'];
                        $query3 = "DELETE FROM TEST WHERE TITOLO = '$titoloTest'";
                        $stmt3 = $conn->prepare($query3);
                        if ($stmt3->execute()) {
                            // Esegui operazioni dopo il successo dell'esecuzione della query
                            echo "Test eliminato con successo.";
                        } else {
                            // Gestisci eventuali errori nell'esecuzione della query
                            echo "Errore nell'esecuzione della query: " . $stmt3->error;
                        }
                        $stmt3->close(); // Chiudi la query preparata
                        if (!mysqli_commit($conn)) {
                            mysqli_rollback($conn);
                            echo "Errore durante il commit della transazione. quii??";
                        }
                        header('Location: VisualizzaTest.php');
                        exit(); // Termina l'esecuzione dello script dopo il reindirizzamento
                        
                    }
                     if (isset($_GET['VisRisp'])){
                        $visualizza = 1;
                        $query4 = "UPDATE TEST SET VisualizzaRisposte = $visualizza WHERE Titolo = '$titoloTest'";
                        $stmt4 = $conn->prepare($query4);
                        if ($stmt4->execute()) {
                            // Esegui operazioni dopo il successo dell'esecuzione della query
                            echo "Visualizzazione risposte aggiornata con successo.";
                        } else {
                            // Gestisci eventuali errori nell'esecuzione della query
                            echo "Errore nell'esecuzione della query: " . $stmt4->error;
                        }
                        $stmt4->close(); // Chiudi la query preparata
                    }
                    if (!mysqli_commit($conn)) {
                        mysqli_rollback($conn);
                        echo "Errore durante il commit della transazione. quii??";
                    }
                    header('Location: VisualizzaTest.php');
                    exit(); // Termina l'esecuzione dello script dopo il reindirizzamento
                    

                }

                
    
            ?>
		</div>

	</body>
    <?php
        // chiusura della connessione
        mysqli_close($conn);
    ?>
</html>
