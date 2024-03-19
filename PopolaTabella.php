<!doctype html>
<html>
	<head>
		<link type="text/css" rel="stylesheet" href="stile.css">
	</head>
	<body>
		<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
		?>
		<div class="intesta">
			<h1> LOGIN  </h1>
		</div>
		<div class="principale">
            <form name=sceltaTabella method=GET action=PopolaTabella.php>
                <select name="nt">
                    <option> --- </option>
                    <?php
                    $mail=$_SESSION['mail'];
                    $query="SELECT Nome FROM tabella_esercizio WHERE MailDocente='$mail' ;";

                    $ris=mysqli_query($conn,$query);

                    if(!$ris){
                    echo "ricerca fallita: " . die (mysqli_error());
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
                    if(isset($_GET["scelta"])){
                        $_SESSION['tabella']=$_GET['nt'];
                        $tabella=$_SESSION['tabella'];

                        $query="SELECT Nome, Tipo, PossibileChiavePrimaria
                                FROM attributo
                                WHERE NomeTabella='$tabella';";

                        $risult=mysqli_query($conn,$query);

                        if(!$risult){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        if(mysqli_num_rows($risult)==0){
                            echo "non ci sono righe";
                        }
                        else{
                            echo "tabella: " .$tabella ."<br><br>";
                            while($row = mysqli_fetch_array($risult))
                            {
                                echo "
                                    {$row['Nome']} ({$row['Tipo']}):<br>
                                    <input type='text' name='nome_{$row['Nome']}' value=''><br><br>
                                ";
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
                $tabella=$_SESSION['tabella'];

                $query="SELECT Nome, Tipo, PossibileChiavePrimaria
                        FROM attributo
                        WHERE NomeTabella='$tabella';";
                $risult=mysqli_query($conn,$query);
                if(isset($_GET["submit"])){
                    while($row = mysqli_fetch_array($risult)){
                        echo "ok ". $row['Nome']. " val: ".$_GET["nome_{$row['Nome']}"];
                        //$query2="INSERT INTO $tabella({$row['Nome']}) VALUES ('"._GET["nome_{$row['Nome']}"]."');";
                    }
                   /* $risultato = mysqli_query($conn,$query);
                    if($risultato === false){
                        echo "errore nella ricerca" . die(mysqli_error($conn));}
                    else{
                            echo "inserimento avvenuto con successo";
                    }

                    if (!mysqli_commit($conn)) {
                        mysqli_rollback($conn);
                        echo "Errore durante il commit della transazione.";
                    }*/
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
