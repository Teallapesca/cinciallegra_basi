<!doctype html>
<html>
	<head>
        <link type="text/css" rel="stylesheet" href="grafica.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <title>Cinciallegra-Popolamento tabelle</title>
	</head>
	<body>
		<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
            include 'ConnessioneMongoDB.php';
		?>
		<div class="intesta">
        <a href="hpDocente.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-arrow-left-circle-fill">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </a>
			<h1> POPOLAMENTO TABELLE</h1>
		</div>
		<div class="principale">
            <form name=sceltaTabella method=GET action=PopolaTabella.php>
                <select name="nt">
                    <option> --- </option>
                    <?php
                    //selezione della tabella da popolare
                    $mail=$_SESSION['mailDocente'];
                    $query="SELECT Nome FROM tabella_esercizio WHERE MailDocente='$mail' ;";

                    $ris=mysqli_query($conn,$query);

                    if(!$ris){
                    echo "ricerca fallita: " . die (mysqli_error($conn));
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
                
                    ?>
                </select> <br><br>
                <input type=submit name=scelta value=Scegli class='button'/><br><br>
                </form>

            <form name='attributi' method='GET' action='PopolaTabella.php'>
                <?php
                //se è stato cliccato "scelta" prendo la tabella selezionata
                    if(isset($_GET["scelta"])){
                        $_SESSION["chiave"]="";
                        unset($_SESSION["attributi"]);//svuoto l'array attributi
                        $_SESSION['tabella']=$_GET['nt'];
                        $tabella=$_SESSION['tabella'];

                        $query="SELECT Nome, Tipo, PossibileChiavePrimaria
                                FROM attributo
                                WHERE NomeTabella='$tabella' ORDER BY attributo.PossibileChiavePrimaria DESC;";  //seleziono gli attributi della tabella selezionata
                       
                        $risult=mysqli_query($conn,$query);

                        if(!$risult){
                            echo "ricerca fallita: " . die (mysqli_error($conn));
                        }else
                        if(mysqli_num_rows($risult)==0){
                            echo "non ci sono righe";
                        }
                        else{
                            echo "<label class='sobrio'>Tabella: " .$tabella ."</label><br><br>";
                            while($row = mysqli_fetch_array($risult))
                            {
                                echo "
                                    <label class='sobrio'>{$row['Nome']} ({$row['Tipo']}):</label><br>
                                    <input type='text' name='nome_{$row['Nome']}' value=''><br><br>
                                ";
                                if($row['PossibileChiavePrimaria']==1 && !isset($_SESSION["chiave"])){
                                    $_SESSION["chiave"]=$row['Nome']; //sarà da sistemare per le chiavi multiple
                                }
                                $_SESSION["attributi"][]=Array("nome" => $row['Nome'],"tipo" => $row['Tipo']);
                            }
                            echo "<input type='submit' name=submit value='Invia' class='button'>";
                        }

                        if (!mysqli_commit($conn)) {
                            mysqli_rollback($conn);
                            echo "Errore durante il commit della transazione.";
                        }
                    }
                
                ?>            
            </form>

            <?php
                
                if(isset($_GET["submit"])){
                    $tabella=$_SESSION['tabella'];

                    if (isset($_SESSION["attributi"])) {
                        $valore1=0;
                        $attr1="";
                        $chiave=$_SESSION["chiave"];
                        $valori="";
                        
                        foreach($_SESSION["attributi"] as $key => $attributo){

                            $valore = $_GET["nome_{$attributo["nome"]}"];
                            $valore_esc = mysqli_real_escape_string($conn, $valore);

                            // Aggiungo gli apici se è un varchar 
                            if (strpos($attributo["tipo"], "VARCHAR") !== false || strpos($attributo["tipo"], "CHAR") !== false) {
                                $valori .= "'" . $valore_esc . "', ";
                            } else {
                                $valori .= $valore_esc . ", ";
                            }                         
                        }
                        
                        $valori=rtrim($valori, ', ');
                     
                        $query='CALL PopolaTabella("'.$tabella.'", "'.$valori.'");';      
                        $risultato = mysqli_query($conn,$query);

                        if($risultato === false){
                            echo "errore nell popolamento della tabella" . mysqli_error($conn);}
                        else{
                                echo "inserimento avvenuto con successo";
                                logEvent("Nuova riga nella tabella $tabella inserita");
                        }

                    }

                    if (!mysqli_commit($conn)) {
                        mysqli_rollback($conn);
                        echo "Errore durante il commit della transazione.";
                    }
                               
                }
            
            ?>
		</div>
	</body>
    <?php
    
        // chiusura della connessione
        mysqli_close($conn);
    ?>
</html>