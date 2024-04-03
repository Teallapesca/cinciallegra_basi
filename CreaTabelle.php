<!doctype html>
<html>
<!--<head>
    <title> Creazione tabelle </title>
    <style>
        .centrato{text-aligne:centre;}
        body{margin:0;}
        div.intesta{
            position:relative;
            width:100%;
            height:30%;
            float:left;
            overflow:auto;
            font-style:italic;
            font-weight:bold;
            font-size:20px;
            color:blue;
            text-align:center;
            font-family:helvetica, sans-serif;
            margin-left:20px;}
        div.sinistra{
            position:relative;
            width:40%;
            height:88%;
            float:left;
            overflow:auto;
            font-weight:bold;
            font-size:16px;
            font-family:arial, sans-serif;
            margin-left:20px;}
        div.principale{
            position:relative;
            width:20%;
            height:88%;
            float:left;
            overflow:auto;
            font-weight:bold;
            font-size:16px;
            font-family:arial, sans-serif;
            margin-left:100px;}
        .bordo{border-color:turquoise;
            border-style:solid;
            border-width:2px;
            text-align:center;  
        }
        legend{color:darkblue;}
       
    </style>
    
</head>-->
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
                echo $_SESSION['mail'];
				if(isset($_GET["tab"])){
                    $_SESSION['nomeTabella'] = $_GET["nomeTabella"];
                    if($_SESSION['nomeTabella']!=null){
                        $nomeTabella = $_SESSION['nomeTabella'];
                        $mail = $_SESSION['mail'];
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
				}
            
                if(isset($_GET["col"])){
                    $_SESSION['numCol'] = $_GET["numCol"];
                    
                    if($_SESSION['numCol']!=null){
                        $colonne = intval($_SESSION['numCol']);
                        echo "<br><br><form>";
                        for($i=0; $i<$colonne; $i++){
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
                }
            ?>

        </div>

        <?php
                if (!mysqli_commit($conn)) {
                    mysqli_rollback($conn);
                    echo "Errore durante il commit della transazione.";
                }
            
                // chiusura della connessione
                mysqli_close($conn);
					
			?>

	</body>
</html>

