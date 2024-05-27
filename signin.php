<!DOCTYPE html>
<html class="page-size">
<head>
    <link type="text/css" rel="stylesheet" href="stile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

	<style>
    .page-size{
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        background-color: rgb(241, 246, 249);
    }
    </style>
</head>

<body class="d-flex flex-column align-items-center justify-content-center page-size">
<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include 'connessione.php';
	mysqli_begin_transaction($conn);
	include 'ConnessioneMongoDB.php';

		if(isset($_GET["regStu"])){
			$mail=$_GET['mail'];
			$nome=$_GET['nome'];
			$cognome=$_GET['cognome'];
			$tel=$_GET['telefono'];
			$matricola=$_GET['matricola'];
			$anno=$_GET['anno'];

			//chiamo la procedura che inserisce lo studente nella tabella
			$query="CALL RegistrazioneStudente('$mail','$nome','$cognome', $tel, '$matricola', $anno);";
			$risultato = mysqli_query($conn,$query);
			if($risultato === false){
				echo "errore nella ricerca" . die (mysqli_error($conn));}
			else{
				logEvent("Nuovo studente $mail registrato");
				?>	
			<div class="d-flex flex-column justify-content-center align-items-center">
				<h2>Registrazione avvenuta con successo!</h2> 
				<br><br><a class="btn btn-primary" href=login.php>Accedi</a><br><br>	
			</div>
				<?php
			}
			
		}
		else if(isset($_GET["regDoc"])){
			$mail=$_GET['mail'];
			$nome=$_GET['nome'];
			$cognome=$_GET['cognome'];
			$tel=$_GET['telefono'];
			$corso=$_GET['corso'];
			$dip=$_GET['dip'];

			//chiamo la procedura che inserisce il docente nella tabella
			$query="CALL RegistrazioneDocente('$mail','$nome','$cognome',$tel,'$corso','$dip');";
			$risultato = mysqli_query($conn,$query);
			if($risultato === false){
				echo "errore nella ricerca" . die(mysqli_error($conn));}
			else{
				logEvent("Nuovo docente $mail registrato");
				?>	
			<div class="d-flex flex-column justify-content-center align-items-center">
				<h2>Registrazione avvenuta con successo!</h2> 
				<br><br><a class="btn btn-primary" href=login.php>Accedi</a><br><br>
			</div>
				<?php
				}
		// esecuzione query	
			
		}
	
	if (!mysqli_commit($conn)) {
        mysqli_rollback($conn);
        echo "Errore durante il commit della transazione.";
    }

	if ($risultato instanceof mysqli_result) {
		mysqli_free_result($risultato);
	}

	// chiusura della connessione
		mysqli_close($conn);
?>

</body>
