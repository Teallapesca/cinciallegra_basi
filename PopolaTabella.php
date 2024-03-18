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
			<h1> LOGIN  </h1>
		</div>
		<div class="principale">
            <select name="pr">
            <option> --- </option>
                <?php
                $query="SELECT Nome FROM tabella_esercizio;";

                $ris=mysqli_query($conn,$query);

                if(!$ris){
                echo "ricerca fallita: " . die (mysqli_error());
                }
                if(mysqli_num_rows($ris)==0){
                echo "non ci sono righe".die();
                }
                while($row = mysqli_fetch_array($ris))
                {
                    echo"<option value=".$row['Nome'].">". $row['Nome']. "</option>";
                }

                if (!mysqli_commit($conn)) {
                    mysqli_rollback($conn);
                    echo "Errore durante il commit della transazione.";
                }
            
                // chiusura della connessione
                mysqli_close($conn);
                ?>
            </select> <br><br>
		</div>
	</body>
</html>