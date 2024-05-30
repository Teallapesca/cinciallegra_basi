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
	<h3>Accedi</h3>
	
		<div class="card mt-5" style="width: 20%; padding: 2px;">
			<div class="card-body">
			<form name="accesso" method="GET" action="login.php">

				<div class="form-floating mb-3">  
					<!--<input class="form-control" type="email" name='mail' value='' id="floatingInput" placeholder="name@example.com">-->
					<input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name='mail'>
					<label for="floatingInput">Email address</label>
				</div>
				<div class="form-floating">
					<input type="password" class="form-control" id="floatingPassword" placeholder="Password" name='password'>
					<label for="floatingPassword">Password</label>
				</div>
				<input class="btn btn-primary mt-3" type="submit" name="log" value="accedi">
			</form>
			
			</div>
		</div>
			<a href="HomePage.html">Esci</a>

			<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
			?>
			<?php
				if(isset($_GET["log"])){

					$mail=$_GET["mail"];
					$pass=$_GET["password"];
					$who=0;
					$query="CALL Autenticazione('$mail', '$pass', @who);";
					$risultato = mysqli_query($conn,$query);
					if(!$risultato){
						echo "errore nella ricerca" . die (mysqli_error($conn));
					}
					else{
						$result = mysqli_query($conn, "SELECT @who");
						$row = mysqli_fetch_array($result);
						$who = $row[0];
						$_SESSION['aut']=$who;
						if($who==1){
							$_SESSION['mailDocente']=$mail;
							header("Location: hpDocente.php");
							exit();
						}elseif($who==2){
							$_SESSION['mailStudente']=$mail;
							header("Location: hpStudente.php");
							exit();
						}else{
							echo "
								<label class='mb-5 text-danger-emphasis bg-danger-subtle border border-danger-subtle rounde' data-bs-container=body data-bs-toggle=popover data-bs-placement=bottom data-bs-content=Bottom popover> Utente non trovato, <br> 
								<a href=registrazione.php class='link-danger'>Registrati!!!!</a> <br>
								(O forse hai solo sbagliato password)
								</label>

								<br><br><br><br>
							";
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