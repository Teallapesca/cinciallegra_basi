<!doctype html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="stile.css">
        <title>Cinciallegra-Popolamento tabelle</title>
	</head>
	<body>
		<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
		?>
		<div class="intesta">
			<h1> Popolamento tabelle </h1>
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
                <input type=submit name=scelta value=scegli /><br><br>
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
                        //$query="CALL VisualizzaQuesiti('$tabella');"; //è commentata perchè non lo ancora caricata su phpmyadmin ma sul file sql c'è
                        $risult=mysqli_query($conn,$query);

                        if(!$risult){
                            echo "ricerca fallita: " . die (mysqli_error($conn));
                        }else
                        if(mysqli_num_rows($risult)==0){
                            echo "non ci sono righe";
                        }
                        else{
                            echo "tabella: " .$tabella ."<br><br>";
                            while($row = mysqli_fetch_array($risult))
                            {//potrei fare un if che se il tipo è varchar mi mette nel value le virgolette
                                echo "
                                    {$row['Nome']} ({$row['Tipo']}):<br>
                                    <input type='text' name='nome_{$row['Nome']}' value=''><br><br>
                                ";
                                if($row['PossibileChiavePrimaria']==1 && !isset($_SESSION["chiave"])){
                                    $_SESSION["chiave"]=$row['Nome']; //sarà da sistemare per le chiavi multiple
                                }
                                $_SESSION["attributi"][]=Array("nome" => $row['Nome'],"tipo" => $row['Tipo']);
                            }
                            echo "<input type='submit' name=submit value='Invia'>";
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
                        //var_dump($_SESSION["attributi"]);
                        
                        foreach($_SESSION["attributi"] as $key => $attributo){
                            $valore = $_GET["nome_{$attributo["nome"]}"];
        
                            // Escapa il valore
                            $valore_esc = mysqli_real_escape_string($conn, $valore);

                            // Verifica il tipo di dato e aggiungi apici singoli per le stringhe
                            if (strpos($attributo["tipo"], "VARCHAR") !== false || strpos($attributo["tipo"], "CHAR") !== false) {
                                $valori .= "'" . $valore_esc . "', ";
                            } else {
                                $valori .= $valore_esc . ", ";
                            }                         
                        }
                        $valori=rtrim($valori, ', ');
                        //echo $valori;
                        $query='CALL PopolaTabella("'.$tabella.'", "'.$valori.'");';      
                        $risultato = mysqli_query($conn,$query);
                        //echo "<br><br><pre>Query: $query</pre>";

                        if($risultato === false){
                            echo "errore nell popolamento della tabella" . mysqli_error($conn);}
                        else{
                                echo "inserimento avvenuto con successo";
                        }

                    }

                    if (!mysqli_commit($conn)) {
                        mysqli_rollback($conn);
                        echo "Errore durante il commit della transazione.";
                    }
                               
                }
            
            ?>
		</div>
        <a href=hpDocente.php><-</a>
	</body>
    <?php
    
        // chiusura della connessione
        mysqli_close($conn);
    ?>
</html>