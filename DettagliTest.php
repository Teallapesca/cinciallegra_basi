<!doctype html>
<html>
	<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
		<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
		?>
		<div class="intesta" style="align:center">
			
            <h1>DETTAGLI TEST</h1>

            <div class="messaggi-dropdown">
                <button class="messaggi-icon" onclick="leggiMessaggi()"><u>Messaggi ricevuti</u></button>
                <div class="messaggi-content" id="messaggiContent">
                    <!-- Contenuto dei messaggi qui -->
                    <?php
                        $titoloTest = $_SESSION['titoloTest'];    
                        if (isset($_GET['titolo'])) {//eseguirò tutto questo codice se non c'è stato alcun probelma di settaggio del test scelto
                            $_SESSION['titoloTest']=$_GET['titolo'];
                            $titoloTest = $_GET['titolo'];
                            echo "<h1 class='ml-3'>".$_SESSION['titoloTest']."</h1>";        
                            $query1 = "SELECT * FROM MESSAGGIOSTUDENTE WHERE TitoloTest = '$titoloTest'";
                            $result1 = $conn->query($query1);
                    
                            if ($result1->num_rows > 0) {
                                echo "<ul>";
                                while ($row = $result1->fetch_assoc()) {
                                    echo "<li><b>{$row['MailStudente']} - {$row['DataInserimento']}</b><br>  {$row['TitoloMess']}<br> {$row['Testo']}</li>";
                                }
                                echo "</ul>";
                            } else {
                                echo "Nessun messaggio trovato per questo test.";
                            }
                        }
                    ?>
                </div>
            </div>

            <div class="messaggi-scrivi">
                <button class="messaggi-icon" onclick="scriviMessaggi()"><u>Inserisci messaggio</u></button>
                <div class="messaggi-form" id="messaggiForm">
                    <form name="invio-messaggio" method="GET" action="DettagliTest.php?titoloTest=<?php $_SESSION['titoloTest']?>">
                        <label>Oggetto del messaggio:<label><br>
                        <input type='text' name='titoloMess' value=''><br>
                        <label>Testo del messaggio:<label><br>
                        <input type='text' name='testoMess' value='' style='height: 100px'><br><br>
                        <input type='submit' name='invio' value='Invia messaggio'>
                    </form>
                    
                    <!-- Contenuto dei messaggi qui -->
                    <?php
                        if (isset($_GET['invio'])) {
                            $titoloMess = $_GET['titoloMess'];
                            $testoMess = $_GET['testoMess'];
                            $titoloTest = $_SESSION['titoloTest'];
                            $mailDocente = $_SESSION['mailDocente'];
                    
                            //$query2 = "INSERT INTO MESSAGGIODOCENTE(TitoloMess, Testo, DataInserimento, TitoloTest, MailDocente) VALUES ('$titoloMess', '$testoMess', NOW(), '$titoloTest', '$mailDocente')";
                            $query2 = 'CALL InserimentoMessaggioDocente("'.$titoloMess.'", "'.$testoMess.'", "'.$titoloTest.'", "'.$mailDocente.'")';
                            $result2 = mysqli_query($conn, $query2);
                    
                            if (!$result2) {
                                echo "errore nella ricerca" . die (mysqli_error($conn));
                            }
                            else {
                                echo "<label>Messaggio inviato</label>";
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

                echo "<h1>$titoloTest</h1><br>";

                $relativePath = '/img/' . $fotoTest;
                echo "<img src='" . htmlspecialchars($relativePath, ENT_QUOTES, 'UTF-8') . "' style = 'width:30%'>";
                
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
                    Visualizza risposte      <input type='checkbox' id='VisRisp' name='VisRisp' value='VisRisp'><br><br>
                    <input type='submit' name='esci' id='esci' value='Esci'><br><br>
                    <input type='submit' name='elimina' id='elimina' value='Elimina Test'>
                </form>

                <?php
                $titoloTest = $_SESSION['titoloTest'];
                $visualizza = 0;
                if(isset($_GET['esci'])){
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
    
            ?>
		</div>

	</body>
    <?php
        // chiusura della connessione
        mysqli_close($conn);
    ?>
</html>
