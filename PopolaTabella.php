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
                                WHERE NomeTabella='$tabella';";  //seleziono gli attributi della tabella selezionata
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
                                if($row['PossibileChiavePrimaria']==1){
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
                        if(!isset($_SESSION['num'])){
                            $_SESSION['num']=1;
                        }else{
                            $_SESSION['num']=$_SESSION['num']+1;
                        }
                        $num=$_SESSION['num'];
                        echo "chiave: " . $chiave . " num=".$num;
                        //var_dump($_SESSION["attributi"]);
                        //creazione fisica delle tabelle
                        foreach($_SESSION["attributi"] as $key => $attributo){
                            echo "<br>ok ". $attributo["nome"]. " val: ".$_GET["nome_{$attributo["nome"]}"];
                            $valore=$_GET["nome_{$attributo["nome"]}"]; // in valore salvo il valore messo nel campo testo dell'attributo che sto prendendo in considerazione
                            $attr=$attributo["nome"]; // in attr c'è l'attributo che sto prendendo in considerazione
                            if($key === 0 && ($attr!=$chiave)){
                                $attr1=$attr;
                                if($attributo["tipo"]=="VARCHAR"){//non mi ricordo cosa cambia fra i due, se funziona uguale lo tolgo
                                    $query="INSERT INTO $tabella({$attr},{$chiave}) VALUES ('".$valore."','".$num."');";
                                }
                                else{
                                    $query="INSERT INTO $tabella({$attr},{$chiave}) VALUES ('".$valore."','".$num."');";;
                                }
                                $risultato = mysqli_query($conn,$query);
                                if($risultato === false){
                                    echo "errore nella ricerca 3" . mysqli_error($conn);}
                                else{
                                        echo "inserimento avvenuto con successo";
                                }
                            }elseif($key === 0 && ($attr==$chiave)){
                                $attr1=$attr;
                                if($attributo["tipo"]=="VARCHAR"){//non mi ricordo cosa cambia fra i due, se funziona uguale lo tolgo
                                    $query="INSERT INTO $tabella({$attr}) VALUES ('".$valore."');";
                                }
                                else{
                                    $query="INSERT INTO $tabella({$attr}) VALUES ('".$valore."');";;
                                }
                                $num=$valore; //assegno a num il valore di valore così nel prossimo if non devo fare un controllo se la pk è già stata settata oppure no
                                $risultato = mysqli_query($conn,$query);
                                if($risultato === false){
                                    echo "errore nella ricerca 1" . mysqli_error($conn);}
                                else{
                                        echo "inserimento avvenuto con successo";
                                }
                            }
                            else{
                                if($attr==$chiave){
                                    $num=$valore;
                                }
                                if($attributo["tipo"]=="VARCHAR"){
                                    $query="UPDATE $tabella SET $attr='$valore' WHERE $chiave=$num;";
                                }
                                else{
                                    $query="UPDATE $tabella SET $attr=$valore WHERE $chiave=$num ;";
                                }
                                $risultato = mysqli_query($conn,$query);
                                if($risultato === false){
                                    echo "errore nella ricerca 2" . mysqli_error($conn);}
                                else{
                                        echo "inserimento avvenuto con successo";
                                }
                                //se l'attributo da aggiornare è la chiave, aggiorno anche il valore da prendere in considerazione
                                if($attributo['nome']==$chiave){
                                    $num=$valore;
                                }
                                
                            }
                            
                        }
                        /*if (!mysqli_commit($conn)) {
                            mysqli_rollback($conn);
                            echo "Errore durante il commit della transazione.";
                        }*/
                    }

                    /*$numrighe="CALL InserimentoRigaTabellaEsercizio('$tabella', '$mail');";
                    $risultato = mysqli_query($conn,$numrighe);
                    if($risultato === false){
                        echo "errore nella ricerca " . mysqli_error($conn);
                    }*/
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