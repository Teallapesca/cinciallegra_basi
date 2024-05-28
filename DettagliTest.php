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
                    if (isset($_GET['titolo'])) {//eseguirò tutto questo codice se non c'è stato alcun probelma di settaggio del test scelto
                        $_SESSION['titoloTest']=$_GET['titolo'];
                        $titoloTest = $_GET['titolo'];
                        echo "<h1 class='ml-3'>".$_SESSION['titoloTest']."</h1>";        
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

        <div class="principale">
            <?php
                if (isset($_GET['titoloTest'])) {
                    $_SESSION['titoloTest'] = $_GET['titoloTest'];
                    $titoloTest = $_SESSION['titoloTest'];
                    $sql="SELECT Foto FROM Test WHERE Titolo = '$titoloTest'";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()) {
                        $fotoTest = $row["Foto"];
                    }
                } else {
                    $titoloTest = $_SESSION['titoloTest'];
                }
                echo "<h1>$titoloTest</h1><br>";

                $relativePath = '/img/' . $fotoTest;
                echo "<img src='" . htmlspecialchars($relativePath, ENT_QUOTES, 'UTF-8') . "' style = 'width:30%' alt='Foto'>";
                
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
            ?>
		</div>

        

        <a href=VisualizzaTest.php><-</a>
	</body>
    <?php
        // chiusura della connessione
        mysqli_close($conn);
    ?>
</html>
