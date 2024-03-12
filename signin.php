<?php
	ini_set('display_errors', 1);
	error_reporting(E_ALL);
	include 'connessione.php';
	mysqli_begin_transaction($conn);

	//$pdo accesso controllato in mysql
	if(isset($_GET["reg"])){
		if($_SESSION['utente'] =='studente'){
			$mail=$_GET['mail'];
			$nome=$_GET['nome'];
			$cognome=$_GET['cognome'];
			$tel=$_GET['telefono'];
			$matricola=$_GET['matricola'];
			$anno=$_GET['anno'];

			//echo "dati studente: ".$mail." ".$nome." ".$cognome." ".$tel." ".$matricola." ".$anno ;
			$query = 'INSERT INTO studente (Mail, Nome, Cognome, Telefono, AnnoImmatricolazione, CodiceMatricola) VALUES("'.$mail.'"," ' .$nome .'","' .$cognome.'","' .$tel.'","'.$anno.'","'.$matricola.'");' ;
			$risultato = mysqli_query($conn,$query);
			if($risultato === false){
				echo "errore nella ricerca" . die (mysqli_error($conn));}
			else{
				echo "registrazione avvenuta con successo 
				<br><br><a href=login.php>accedi</a><br><br>
					";
			}
		// esecuzione query	
			
		}
		else if($_SESSION['utente'] =='docente'){
			$mail=$_GET['mail'];
			$nome=$_GET['nome'];
			$cognome=$_GET['cognome'];
			$tel=$_GET['telefono'];
			$corso=$_GET['corso'];
			$dip=$_GET['dip'];
			//echo "l'utente è un docente "." ".$mail." ".$nome." ".$cognome." ".$tel;
			$query = 'INSERT INTO docente (Mail ,Nome, Cognome, Telefono, Corso, Dipartimento) VALUES("'.$mail.'"," '.$nome.'","'.$cognome.'","'.$tel.'","'.$corso.'","'.$dip.'");' ;
			$risultato = mysqli_query($conn,$query);
			if($risultato === false){
				echo "errore nella ricerca" . die(mysqli_error($conn));}
			else{
					echo "registrazione avvenuta con successo 
						<br><br><a href=login.php>accedi</a><br><br>
					";
			}
		// esecuzione query	
			
		}

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

