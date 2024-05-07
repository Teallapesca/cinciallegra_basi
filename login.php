<!doctype html>
<html class="page-size">
<head>
	<title> moodle </title>
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

	

		<div class="card" >
			<div class="card-body">
			<form name="accesso" method="GET" action="login.php">
				<!--<input class="form-check-input" type="radio" name="utente" value="docente"> Docente <br><br>
				<input class="form-check-input" type="radio" name="utente" value="studente"> Studente <br><br>-->

				<div class="input-group mb-3">  
					<span class="input-group-text" id="basic-addon1">Mail</span> <input class="form-control" type='text' name='mail' value=''> <br>
				</div>

				<input class="btn btn-primary" type="submit" name="log" value="accedi">
			</form>
			</div>
		</div>

			<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
			?>
			<?php
				if(isset($_GET["log"])){

					$mail=$_GET["mail"];
					$who=0;
					$query ="CALL Autenticazione('$mail', @who);";
					$risultato = mysqli_query($conn,$query);
					if(!$risultato){
						echo "errore nella ricerca" . die (mysqli_error($conn));
					}
					else{
						$result = mysqli_query($conn, "SELECT @who");
						$row = mysqli_fetch_array($result);
						$who = $row[0];
						if($who==1){
							$_SESSION['mailDocente']=$mail;
							header("Location: hpDocente.php");
							exit();
						}elseif($who==2){
							$_SESSION['mailStudente']=$mail;
							header("Location: hpStudente.php");
							exit();
						}else{
							echo "Utente non trovato, 
							<br><br><a href=registrazione.php>Registrati!!!!</a><br><br>";
						}
					}
					if (!mysqli_commit($conn)) {
						mysqli_rollback($conn);
						echo "Errore durante il commit della transazione.";
					}
				
					// chiusura della connessione
					mysqli_close($conn);
				}
			?>

	

	</body>
</html>