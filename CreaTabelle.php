<!doctype html>
<html>
    <head>
        <title>Creazione tabella</title>
        <link type="text/css" rel="stylesheet" href="grafica.css">
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
            //include 'Navbar.php';
		?>
        <div class="intesta">
            <a href="hpDocente.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-arrow-left-circle-fill">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </a>
            <h1>CREA TABELLE</h1>
        </div>
		<div class="principale">
			<form name="nuovaTabella" method="GET" action="CreaTabelle.php" onsubmit="return validateNome()">
				<label>Inserisci il nome della tabella</label><br><br>
				<input type="text" name="nomeTabella" value="" class="textfield"><br><br>
				<input type="submit" name="tab" value="Inserisci nome" class="button">
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
                            <input type='submit' name='col' value='Seleziona numero colonne' class='button'>
                            </form>
                        ";
                        
                    }
                   
				}
            
                if(isset($_GET["col"])){
                    $_SESSION['numCol'] = $_GET["numCol"];
                    echo "<form>";
                    if($_SESSION['numCol']!=null){
                        $colonne = intval($_SESSION['numCol']);
                        for($i=0; $i<$colonne; $i++){
                            if($i==0){
                                echo"
                                <table>
                                <tr>
                                    <td><label class='sobrio'>inserisci nome attributo</label></td>
                                    <td><label class='sobrio'>inserisci tipo</label></td>
                                    <td><label class='sobrio'>è chiave primaria?</label></td>
                                </tr>
                                <tr>
                                    <td><input type='text' name='attributo[]' value=''></td>
                                    <td><select name='tipo[]'>
                                    <option value='VARCHAR(10)'>VARCHAR(10)</option>
                                    <option value='VARCHAR(50)'>VARCHAR(50)</option>
                                    <option value='VARCHAR(100)'>VARCHAR(100)</option>
                                    <option value='INT' selected>INT</option>
                                    <option value='BOOLEAN'>BOOLEAN</option>
                                    <option value='DOUBLE'>DOUBLE</option>
                                    </select></td>
                                    <td><select name='PK[]'>
                                    <option value='SI'selected>SI</option>
                                    <option value='NO'>NO</option>
                                    </select></td>
                                </tr>
                                </table>
                                ";
                            }else{
                                echo"
                                <table>
                                <tr>
                                    <td><label class='sobrio'>inserisci nome attributo</label></td>
                                    <td><label class='sobrio'>inserisci tipo</label></td>
                                    <td><label class='sobrio'>è chiave primaria?</label></td>
                                </tr>
                                <tr>
                                    <td><input type='text' name='attributo[]' value=''></td>
                                    <td><select name='tipo[]'>
                                    <option value='VARCHAR(10)'>VARCHAR(10)</option>
                                    <option value='VARCHAR(50)'>VARCHAR(50)</option>
                                    <option value='VARCHAR(100)'>VARCHAR(100)</option>
                                    <option value='INT' selected>INT</option>
                                    <option value='BOOLEAN'>BOOLEAN</option>
                                    <option value='DOUBLE'>DOUBLE</option>
                                    </select></td>
                                    <td><select name='PK[]'>
                                    <option value='SI'>SI</option>
                                    <option value='NO' selected>NO</option>
                                    </select></td>
                                </tr>
                                </table>
                                ";
                            }
                        }
                       
                        echo "<br><input type='submit' name='conf' value='Conferma attributi' class='button' style='margin-bottom:50px'>";
                        echo "</form>";
                    }
                }
                if(isset($_GET["conf"])) {
                    $colonne = intval($_SESSION['numCol']);
                    $nomeTabella = $_SESSION["nomeTabella"];
                    // Ciclo attraverso gli attributi inseriti dall'utente
                    $query = "CALL InserimentoTabellaEsercizio('$nomeTabella', '$mail')";
                        // esecuzione query
                    $risultato = mysqli_query($conn,$query);
                    if($risultato === false){
                        echo "errore nella ricerca" . die (mysqli_error($conn));
                    } else{
                        echo "tabella inserita";
                        //logEvent("Nuova tabella esercizio ($nomeTabella) inserita");
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
                            //logEvent("Nuovo attributo $attributo inserito");
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
        </div>
	</body>
</html>
