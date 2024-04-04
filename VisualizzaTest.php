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
			<h1>VISUALIZZA TEST</h1>
		</div>
		<div class="principale">
            <?php
                $docente = $_SESSION['mail'];
                $query="CALL VisualizzazioneTestDoc('$docente')";
                $risultato=mysqli_query($conn,$query);

                if(!$risultato){
                    echo "ricerca fallita: " . die (mysqli_error());
                }
                if(mysqli_num_rows($risultato)==0){
                    echo "Non hai ancora creato test";
                }
                else{
                    while($row = mysqli_fetch_array($risultato)){
                        $titoloTest = $row['Titolo'];
                        $_SESSION['titoloTest'] = $titoloTest;
                        echo "
                            <a href='DettagliTest.php?titolo=$titoloTest'> {$titoloTest} ({$row['DataTest']}) </a><br><br>
                        ";                        
                    }
                }
            
            ?>
		</div>
        <a href=hpDocente.php><-</a>
	</body>
    <?php
    
        // chiusura della connessione
        mysqli_close($conn);
    ?>
</html>