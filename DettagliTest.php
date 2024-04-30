<!doctype html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="stile.css">
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
		<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
		?>
		<div class="intesta">
			<h1>DETTAGLI TEST</h1>
		</div>

        <div class="messaggi-dropdown">
           
            <button class="messaggi-icon" onclick="leggiMessaggi()"><u>Messaggi ricevuti</u></button>
            <div class="messaggi-content" id="messaggiContent">
                <!-- Contenuto dei messaggi qui -->
                <?php
                    $titoloTest = $_SESSION['titoloTest'];            
                    $query1 = "SELECT * FROM MESSAGGIODOCENTE WHERE TitoloTest = '$titoloTest'";
                    $result1 = $conn->query($query1);
            
                    if ($result1->num_rows > 0) {
                        echo "<ul>";
                        while ($row = $result1->fetch_assoc()) {
                            echo "<li><b>{$row['MailDocente']} - {$row['DataInserimento']}</b><br>  {$row['TitoloMess']}<br> {$row['Testo']}</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "Nessun messaggio trovato per questo test.";
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
                            echo "<label>Messaggio inviato<br>$titoloMess, $testoMess, $titoloTest, $mailDocente</label>";
                        }

                        if (!mysqli_commit($conn)) {
                            mysqli_rollback($conn);
                            echo "Errore durante il commit della transazione.";
                        }
                    }

                ?>
                
            </div>
        </div>

        <div class="principale">
            <?php
                if (isset($_GET['titoloTest'])) {
                    $_SESSION['titoloTest'] = $_GET['titoloTest'];
                    $titoloTest = $_SESSION['titoloTest'];
                    $fotoTest = $_SESSION['testImg'];
                } else {
                    $titoloTest = $_SESSION['titoloTest'];
                    $fotoTest = $_SESSION['testImg'];
                }
                echo "<h1>$titoloTest</h1><br>";

                echo "<img src='$fotoTest'>";


                $query="CALL VisualizzazioneQuesiti('$titoloTest')";
                $risultato=mysqli_query($conn,$query);
                $num=0;
                while($row = mysqli_fetch_array($risultato)){
                    $num=$num+1;
                    echo "
                        <p>$num) {$row['Descrizione']}<br>
                        Livello: {$row['Difficolta']}
                        </p>
                    ";                        
                }

                /*
                // leggi il contenuto del blob dal database
                    $blob = $domanda_sondaggio["Foto"];

                    // decodifica il contenuto del blob in una stringa base64
                    $base64 = base64_encode($blob);

                    // determina il tipo di immagine dal contenuto del blob con la funzione getimagesizefromstring e prendendo
                    //il valore della chiave mime che dice il tipo dell'immagine
                    $image_info = getimagesizefromstring($blob);
                    $mime_type = $image_info["mime"];
                    ?>
                    <img width="10%" src="data:<?php echo $mime_type; ?>;base64,<?php echo $base64; ?>">
                */
            ?>
		</div>

        

        <a href=VisualizzaTest.php><-</a>
	</body>
    <?php
        // chiusura della connessione
        mysqli_close($conn);
    ?>
</html>
