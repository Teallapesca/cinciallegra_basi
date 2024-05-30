<!doctype html>
<html>
    <head>
        <title>Creazione tabella</title>
        <link type="text/css" rel="stylesheet" href="stile.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script>
            function validateNome() {
                var nomeTabella = document.forms["regutente"]["nomeTabella"].value;
                if (nomeTabella == "" ) {
                    alert("Inserisci il nome della tabella!");
                    return false; // Impedisci l'invio del modulo
                }
                return true; // Consenti l'invio del modulo
            }
        </script>
    </head>
	<body>
		<?php
            
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
            include 'ConnessioneMongoDB.php';
            include 'Navbar.php';
		?>
		<div class="m-5 d-flex flex-column justify-content-center align-items-center" style="padding-top: 100px;">
			<form name="nuovaTabella" method="GET" action="CreaTabelle.php" onsubmit="return validateNome()">
				Inserisci il nome della tabella<br><br>
				<input type="text" name="nomeTabella" value=""><br><br>
				<input type="submit" name="tab" value="Inserisci nome">
			</form>

			<?php
                $mail = $_SESSION['mailDocente'];
                //echo $_SESSION['mailDocente'];
				if(isset($_GET["tab"])){
                    $_SESSION['nomeTabella'] = $_GET["nomeTabella"];
                    if($_SESSION['nomeTabella']!=null){
                        $nomeTabella = $_SESSION['nomeTabella'];
                        //$mail = $_SESSION['mailDocente'];
                        echo"
                            <form name=nuovaTabella method=GET action=CreaTabelle.php onsubmit=return validateForm()>
                            Inserisci il numero di colonne della tabella<br><br>
                            <input type='number' name='numCol' value=''><br><br>
                            <input type='submit' name='col' value='Seleziona numero colonne'>
                            </form>
                        ";
                        
                    }
                    if (!mysqli_commit($conn)) {
                        mysqli_rollback($conn);
                        echo "Errore durante il commit della transazione.";
                    }
                
                    // chiusura della connessione
                    mysqli_close($conn);
				}
            
                if(isset($_GET["col"])){
                    $_SESSION['numCol'] = $_GET["numCol"];
                    
                    if($_SESSION['numCol']!=null){
                        $colonne = intval($_SESSION['numCol']);
                        echo "<br><br> <form>";
                        for($i=0; $i<$colonne; $i++){
                            if($i==0){
                                echo "
                                <pre>inserisci nome attributo   inserisci tipo          è chiave primaria?</pre>
                                <pre><input type='text' name='attributo[]' value=''>   <select name='tipo[]'>
                                                                                            <option value='VARCHAR(10)'>VARCHAR(10)</option>
                                                                                            <option value='VARCHAR(50)'>VARCHAR(50)</option>
                                                                                            <option value='VARCHAR(100)'>VARCHAR(100)</option>
                                                                                            <option value='INT' selected>INT</option>
                                                                                            <option value='BOOLEAN'>BOOLEAN</option>
                                                                                            <option value='DOUBLE'>DOUBLE</option>
                                                                                        </select>            <select name='PK[]'>
                                                                                                                <option value='SI'selected>SI</option>
                                                                                                                <option value='NO'>NO</option>
                                                                                                            </select>
                                ";
                            }else{
                                echo "
                                <pre>inserisci nome attributo   inserisci tipo          è chiave primaria?</pre>
                                <pre><input type='text' name='attributo[]' value=''>   <select name='tipo[]'>
                                                                                            <option value='VARCHAR(10)'>VARCHAR(10)</option>
                                                                                            <option value='VARCHAR(50)'>VARCHAR(50)</option>
                                                                                            <option value='VARCHAR(100)'>VARCHAR(100)</option>
                                                                                            <option value='INT' selected>INT</option>
                                                                                            <option value='BOOLEAN'>BOOLEAN</option>
                                                                                            <option value='DOUBLE'>DOUBLE</option>
                                                                                        </select>            <select name='PK[]'>
                                                                                                                <option value='SI'>SI</option>
                                                                                                                <option value='NO' selected>NO</option>
                                                                                                            </select>
                                ";
                            }
                        }
                        if (!mysqli_commit($conn)) {
                            mysqli_rollback($conn);
                            echo "Errore durante il commit della transazione.";
                        }
                    
                        // chiusura della connessione
                        mysqli_close($conn);
                        echo "<input type='submit' name='conf' value='Conferma attributi'> </form>";
                        
                    }
                }
                if(isset($_GET["conf"])) {
                    $colonne = intval($_SESSION['numCol']);
                    $nomeTabella = $_SESSION["nomeTabella"];
                    // Ciclo attraverso gli attributi inseriti dall'utente
                    $query = 'CALL InserimentoTabellaEsercizio("'.$nomeTabella.'", "'.$mail.'");';
                        // esecuzione query
                    $risultato = mysqli_query($conn,$query);
                    if($risultato === false){
                        echo "errore nella ricerca" . die (mysqli_error($conn));
                    } else{
                        echo "tabella inserita";
                        logEvent("Nuova tabella esercizio ($nomeTabella) inserita");
                    }
                    for($i = 0; $i < $colonne; $i++) {
                        // Ottieni il valore dell'attributo dall'input dell'utente
                        $attributo = $_GET['attributo'][$i];
                        $tipo = $_GET['tipo'][$i];
                        // Verifica se la checkbox è stata selezionata
                        if($_GET['PK'][$i]== 'SI'){
                            // La checkbox è stata selezionata
                            $PK = 1;
                        } else {
                            // La checkbox non è stata selezionata
                            $PK = 0;
                        }
                        
                        
                        // Scrivi la query SQL per inserire l'attributo nella tabella "Attributo"
                        //$query = 'INSERT INTO Attributo (NomeTabella, Nome, Tipo, PossibileChiavePrimaria) VALUES ("'.$nomeTabella.'", "'.$attributo.'", "'.$tipo.'", "'.$PK.'");';
                        $query ="CALL InserimentoAttributo('$nomeTabella', '$attributo', '$tipo', '$PK');"; //devo ancora caricarla su php my admin ma c'è sul file sql 
                        // Esegui la query
                        $result = mysqli_query($conn, $query);
                        
                        // Verifica se la query ha avuto successo
                        if($result) {
                            echo "'$PK' Attributo '$attributo' inserito correttamente.<br>";
                            logEvent("Nuovo attributo $attributo inserito");
                        } else {
                            echo "Errore nell'inserimento dell'attributo '$attributo'.<br>";
                        }
                    }
                    $_SESSION["tabelle"][] = $nomeTabella;
                    
                    if (!mysqli_commit($conn)) {
                        mysqli_rollback($conn);
                        echo "Errore durante il commit della transazione.";
                    }
                
                    // chiusura della connessione
                    mysqli_close($conn);

                    header('Location: tabFisica.php');
                    //exit();
                }
            ?>
        <br> <br> <a href=hpDocente.php> <- </a>
        </div>
	</body>
</html>

