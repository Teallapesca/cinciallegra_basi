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
			<h1>CREA TABELLE</h1>
		</div>
		<div class="principale">
			<form name="nuovaTabella" method="GET" action="CreaTabelle.php">
				Inserisci il nome della tabella<br><br>
				<input type="text" name="nomeTabella" value=""><br><br>
				<input type="submit" name="tab" value="Inserisci nome">
			</form>

			<?php
                //echo $_SESSION['mailDocente'];
				if(isset($_GET["tab"])){
                    $_SESSION['nomeTabella'] = $_GET["nomeTabella"];
                    if($_SESSION['nomeTabella']!=null){
                        $nomeTabella = $_SESSION['nomeTabella'];
                        $mail = $_SESSION['mailDocente'];
                        $query = 'CALL InserimentoTabellaEsercizio("'.$nomeTabella.'", "'.$mail.'");';
                        // esecuzione query
                        $risultato = mysqli_query($conn,$query);
                        if($risultato === false){
                            echo "errore nella ricerca" . die (mysqli_error($conn));
                        } else{
                            echo "tabella inserita" . "<br><br>
                            <form name=nuovaTabella method=GET action=CreaTabelle.php>
                            Inserisci il numero di colonne della tabella<br><br>
                            <input type='number' name='numCol' value=''><br><br>
                            <input type='submit' name='col' value='Seleziona numero colonne'>
                            </form>";
                        }
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
                        echo "<br><br><form>";
                        for($i=0; $i<$colonne; $i++){
                            if($i==0){
                                echo "
                                <pre>inserisci nome attributo   inserisci tipo          è chiave primaria?</pre>
                                <pre><input type='text' name='attributo[]' value=''>   <select name='tipo[]'>
                                                                                            <option value='VARCHAR'>VARCHAR</option>
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
                                                                                            <option value='VARCHAR'>VARCHAR</option>
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
                    for($i = 0; $i < $colonne; $i++) {
                        // Ottieni il valore dell'attributo dall'input dell'utente
                        $attributo = $_GET['attributo'][$i];
                        $tipo = $_GET['tipo'][$i];
                        //$PK = intval($_GET['PK'][$i]);
                        // Verifica se la checkbox è stata selezionata
                        if($_GET['PK'][$i]== 'SI'){
                            // La checkbox è stata selezionata
                            $PK = 1;
                            //echo"true";
                        } else {
                            // La checkbox non è stata selezionata
                            $PK = 0;
                            //echo"false";
                        }
                        
                        // Scrivi la query SQL per inserire l'attributo nella tabella "Attributo"
                        $query = 'INSERT INTO Attributo (NomeTabella, Nome, Tipo, PossibileChiavePrimaria) VALUES ("'.$nomeTabella.'", "'.$attributo.'", "'.$tipo.'", "'.$PK.'");';
                        // Esegui la query
                        $result = mysqli_query($conn, $query);
                        
                        // Verifica se la query ha avuto successo
                        if($result) {
                            echo "'$PK' Attributo '$attributo' inserito correttamente.<br>";
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

                   //
                    /*echo "<p>
                                <a href=vincoli.php> <input type=button name=vincoli value='fai vincoli di integrita'></a>
                            </p>";*/
                }
            ?>
            <form name=crea method=GET action='tabFisica.php'>
                <input type=submit name=fine value="fine">
            </form>
        <br> <br> <a href=hpDocente.php> <- </a>
        </div>
	</body>
</html>

