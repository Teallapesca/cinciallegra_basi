<!doctype html>
<html>
    <head>
    <script>
        function mostraTesto(tipo) {
            var quesitoChiuso = document.getElementById("quesitoChiuso");
            var sketchCodice = document.getElementById("sketchCodice");

            if (tipo === "chiuso") {
                quesitoChiuso.style.display = "block";
                sketchCodice.style.display = "none";
            } else if (tipo === "sketch") {
                quesitoChiuso.style.display = "none";
                sketchCodice.style.display = "block";
            }
        }
    </script>
    </head>
	<body>
		<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
		?>
		<div class="titolo">
			<h1>CREA QUESITO</h1>
		</div>
		<div class="principale">
			<form name="quesito" method="GET" action="CreaQuesito.php">
				Descrizione quesito<br><br>
				<input type="text" name="descQuesito" value=""><br><br>
                Difficoltà quesito: <input type=radio name=difficolta value=basso> basso &nbsp &nbsp
                <input type=radio name=difficolta value=medio> Medio &nbsp &nbsp
                <input type=radio name=difficolta value=alto> Alto <br><br>
                <select name="nt">
                    <option> Seleziona La tabella </option>
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
                Tipo di risposta:
                <input type=radio name=tipo value=chiuso onChange="mostraTesto('chiuso')"> Quesito chiuso &nbsp &nbsp
                <input type=radio name=tipo value=sketch onChange="mostraTesto('sketch')"> Sketch di codice <br><br>

                <div id="quesitoChiuso" style="display:none;">
                <form name=opzioni method="GET" action="CreaQuesito.php"> 
                    Opzione 1: &nbsp&nbsp <input type=text name=op1> &nbsp&nbsp <input type=radio name=giusta value=opr1> opzione giusta<br><br>
                    Opzione 2: &nbsp&nbsp <input type=text name=op2> &nbsp&nbsp <input type=radio name=giusta value=opr2> opzione giusta<br><br>
                    Opzione 3: &nbsp&nbsp <input type=text name=op3> &nbsp&nbsp <input type=radio name=giusta value=opr3> opzione giusta<br><br>
                    <input type=submit name=opzione value=crea>
                </form>
                </div>
                <?php
                    if(isset($_GET["opzione"])){
                       /* $opzione1=$_GET['op1'];
                        $opzione2=$_GET['op2'];
                        $opzione3=$_GET['op3'];
                        $_SESSION['giusta']=$_GET['giusta'];
                        $test=$_SESSION['TitoloTest'];

                        $query="SELECT * FROM opzioni";
                        $risult=mysqli_query($conn,$query);
                        if(!$risult){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        $numerazione=mysqli_num_rows($risult);

                        $query="SELECT * FROM quesito";
                        $ris=mysqli_query($conn,$query);
                        if(!$ris){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        $_SESSION['progQuesito']=mysqli_num_rows($ris);
                        $progQuesito=$_SESSION['progQuesito'];

                        $query1='insert into opzioni(Numerazione, ProgressivoChiuso, TitoloTest, Testo) values ("'.$numerazione.'"," '.$progQuesito.'","'.$test.'","'.$opzione1.'");' ;
                        $risult1=mysqli_query($conn,$query1);
                        if(!$risult1){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        $numerazione=$numerazione+1;
                        $query2='insert into opzioni(Numerazione, ProgressivoChiuso, TitoloTest, Testo) values ("'.$numerazione.'"," '.$progQuesito.'","'.$test.'","'.$opzione2.'");' ;
                        $risult2=mysqli_query($conn,$query2);
                        if(!$risult2){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        $numerazione=$numerazione+1;
                        $query3='insert into opzioni(Numerazione, ProgressivoChiuso, TitoloTest, Testo) values ("'.$numerazione.'"," '.$progQuesito.'","'.$test.'","'.$opzione3.'");' ;
                        $risult3=mysqli_query($conn,$query3);
                        if(!$risult3){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        else{
                            echo"inserimento avvenuto con successo";
                        }
*/
                        echo "opzioni create";
                    }
                ?>

                <div id="sketchCodice" style="display:none;">
                <form name=codice method="GET" action="CreaQuesito.php"> 
                    inserisci la soluzione: &nbsp&nbsp <input type=text name=sketch>
                    <input type=submit name=creasketch value=crea>
                </form>
                </div>
                <?php
                    if(isset($_GET["creasketch"])){
                       /* 
                        $testoSKetch=$_GET['sketch'];
                        $test=$_SESSION['TitoloTest'];

                        $query="SELECT * FROM quesito";
                        $ris=mysqli_query($conn,$query);
                        if(!$ris){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        $_SESSION['progQuesito']=mysqli_num_rows($ris);
                        $progQuesito=$_SESSION['progQuesito'];

                        $query1='insert into opzioni(Numerazione, ProgressivoChiuso, TitoloTest, Testo) values ("'.$numerazione.'"," '.$progQuesito.'","'.$test.'","'.$opzione1.'");' ;
                        $risult1=mysqli_query($conn,$query1);
                        if(!$risult1){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        $numerazione=$numerazione+1;
                        $query2='insert into opzioni(Numerazione, ProgressivoChiuso, TitoloTest, Testo) values ("'.$numerazione.'"," '.$progQuesito.'","'.$test.'","'.$opzione2.'");' ;
                        $risult2=mysqli_query($conn,$query2);
                        if(!$risult2){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        $numerazione=$numerazione+1;
                        $query3='insert into opzioni(Numerazione, ProgressivoChiuso, TitoloTest, Testo) values ("'.$numerazione.'"," '.$progQuesito.'","'.$test.'","'.$opzione3.'");' ;
                        $risult3=mysqli_query($conn,$query3);
                        if(!$risult3){
                            echo "ricerca fallita: " . die (mysqli_error());
                        }
                        else{
                            echo"inserimento avvenuto con successo";
                        }
                        */
                        echo "sketch creato";
                    }
                ?>
                <br><br>
				<input type="submit" name="quesito" value="Crea">
			</form>

			<?php
            /* 
                $progQuesito=$_SESSION['progQuesito'];
                $test=$_SESSION['TitoloTest'];
                $query="CALL NewQuesitoChiuso('$progQuesito', '$test', '$cognome', '$tel', '$anno', '$matricola')";
					*/
                if(isset($_GET['quesito'])){
                    echo "ciaone";
                }
                
			?>
			<a href=hpDocente.php><-</a>
		</div>
	</body>
</html>