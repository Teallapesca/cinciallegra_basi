<!doctype html>
<html>
	<body>
		<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
		?>
		<div class="titolo">
			<h1> LOGIN  </h1>
		</div>
		<div class="principale">
			<form name="accesso" method="GET" action="login.php">
				<input type="radio" name="utente" value="docente"> Docente <br><br>
				<input type="radio" name="utente" value="studente"> Studente <br><br>
				Inserisci l'email<br><br>
				<input type="text" name="mail" value=""><br><br>
				<input type="submit" name="log" value="accedi">
			</form>

			<?php
				if(isset($_GET["log"])){
					if(isset($_GET["utente"])){
						$_SESSION['utente']=$_GET["utente"];
						if($_SESSION['utente']=='docente'){
							$_SESSION['mail']=$_GET["mail"];
							$query = "SELECT Mail FROM Docente ;";
						}
						else if($_SESSION['utente']=='studente'){
							$_SESSION['mail']=$_GET["mail"];
							$query = "SELECT Mail FROM studente ;";
						}
						
						// esecuzione query
						$risultato = mysqli_query($conn,$query);
						if(!$risultato){
							echo "errore nella ricerca" . die (mysqli_error($conn));
						}
						else{
							$trovato=false;
							while($row = mysqli_fetch_array($risultato, MYSQLI_ASSOC)){
								if($row['Mail']==$_SESSION['mail']){
									echo "ciao ". $_SESSION['mail'];
									$trovato=true;
									/*if($_SESSION['utente']=='docente'){
										header("Location: hpDocente.html");
										exit();
									}else*/ if($_SESSION['utente']=='studente'){
										header("Location: hpStudente.php");
										exit();
									}
									
								}
							}
							if($trovato==false){
								echo "utente non trovato, 
								<br><br><a href=registrazione.php>registrati!!!!</a><br><br>";
							}
							
							
						}

						if (!mysqli_commit($conn)) {
							mysqli_rollback($conn);
							echo "Errore durante il commit della transazione.";
						}
					
						// chiusura della connessione
						mysqli_close($conn);
					}
				}
			?>
			
		</div>
	</body>
</html>

<!--WHERE Mail=$_SESSION['mail']-->