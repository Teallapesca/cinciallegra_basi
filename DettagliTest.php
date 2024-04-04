<!doctype html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="stile.css">
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
		<div class="principale">
            <?php
                if (isset($_GET['titolo'])) {
                    $titoloTest = $_GET['titolo'];
                    echo "<h1>$titoloTest</h1>";  
                } else {
                    echo "Nessun titolo test specificato nella query string.";
                }

                $query="CALL VisualizzazioneQuesiti('$titoloTest')";
                $risultato=mysqli_query($conn,$query);

                while($row = mysqli_fetch_array($risultato)){
                    echo "
                        <p>{$row['Progressivo']}) {$row['Descrizione']}<br>
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