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
        <h1>VINCOLI DI INTEGRITA' REFERENZIALE</h1>
    </div>
    <div class="principale">
        <?php
            //select per scegliere la tabella da cui prendere la chiave primaria
            $mail = $_SESSION['mailDocente'];
            //$tabelle = $_SESSION["tabelle"];
            //var_dump($tabelle);
            if(isset($_GET['aggiungi'])){
                $_SESSION['prima']=1;
            }
            
            if(isset($_SESSION['prima'])){
                $query = "SELECT Nome FROM tabella_esercizio WHERE MailDocente='$mail' ;";

                $ris = mysqli_query($conn, $query);

                if (!$ris) {
                    echo "ricerca fallita: " . die(mysqli_error($conn));
                }
                elseif (mysqli_num_rows($ris) == 0) {
                    echo "non ci sono righe" . die();
                }
                else{
                    echo"  <form name=vincoli method=GET action='vincoli.php'>
                        <select name=nt>
                        <option> --- </option>";
                    while ($row = mysqli_fetch_array($ris)) {
                        echo "<option value=" . $row['Nome'] . ">" . $row['Nome'] . "</option>";
                    }
                    if (!mysqli_commit($conn)) {
                        mysqli_rollback($conn);
                        echo "Errore durante il commit della transazione.";
                    }
                    echo "</select> <br><br>
                        <input type=submit name=scelta value='scegli tabella'><br><br>
                        </form>";
                    
                }
                unset($_SESSION['prima']);
            }
            if (isset($_GET["scelta"])) {
                $_SESSION['tabella1'] = $_GET['nt'];
                $tabella = $_SESSION['tabella1'];
                echo "hai selezionato la tabella " . $tabella;
                //mostro la sua chiave primaria
                $PK = 1;               //il tipo serve per fare il controllo che il vincolo abbia due attributi con lo steso tipo
                $query = "SELECT Nome, Tipo          
                                    FROM attributo
                                    WHERE NomeTabella='$tabella' AND PossibileChiavePrimaria='$PK';";

                $risult = mysqli_query($conn, $query);

                if (!$risult) {
                    echo "ricerca fallita: " . mysqli_error($conn);
                }elseif (mysqli_num_rows($risult) == 0) {
                    echo "non ci sono righe" . die();
                }
                else{
                    $_SESSION["primarykey"] = array();
                    echo "<br><br>";
                    while ($row = mysqli_fetch_array($risult)) {
                        $val=$row['Nome']."-".$row['Tipo'];
                        $_SESSION["primarykey"][] = $val;
                        echo  $row['Nome']." ".$row['Tipo'] . "<br>";
                    }
                  
                }
          
                $tabella = $_SESSION['tabella1'];
                
                $query = "SELECT Nome FROM tabella_esercizio WHERE MailDocente='$mail' AND NOT Nome= '$tabella';";

                $ris = mysqli_query($conn, $query);

                if (!$ris) {
                    echo "ricerca fallita: " . die(mysqli_error($conn));
                }
                elseif (mysqli_num_rows($ris) == 0) {
                    echo "non ci sono righe" . die();
                }else{
                    echo " <form name=vincoli method=GET action='vincoli.php'>
                        <select name='nt2'>
                        <option> --- </option>";
                        while ($row = mysqli_fetch_array($ris)) {
                            echo "<option value=" . $row['Nome'] . ">" . $row['Nome'] . "</option>";
                        }
                    if (!mysqli_commit($conn)) {
                        mysqli_rollback($conn);
                        echo "Errore durante il commit della transazione.";
                    }
                    echo "</select> <br><br>
                        <input type=submit name=scelta2 value='scegli tabella'><br><br>
                        </form>";
                }
            }

            if (isset($_GET["scelta2"])) {
                //mostro gli attributi della tabella
                $_SESSION['tabella2'] = $_GET['nt2'];
                
                
                $tabella = $_SESSION['tabella2'];
                $tabella1 = $_SESSION['tabella1'];

                
                echo "<form name=vincoli2 method=GET action='vincoli.php'>
                <table>
                    <tr>
                        <td>".$tabella1."</td><td>".$tabella."</td>
                    </tr>";

                    foreach($_SESSION["primarykey"] as $chiave){
                        echo $chiave."<br>";
                        $primarykey = $chiave;
                        $valoriSeparati = explode("-", $primarykey);
                        $nomepk = $valoriSeparati[0];
                        $tipopk = $valoriSeparati[1];
                
                        $query = "SELECT Nome, Tipo, NomeTabella        
                        FROM attributo
                        WHERE NomeTabella='$tabella' AND Tipo='$tipopk'";

                        $ris_attri = mysqli_query($conn, $query);

                        if (!$ris_attri) {
                        echo "ricerca fallita: " . mysqli_error($conn);
                        }
                        else{
                            if (mysqli_num_rows($ris_attri) == 0) {
                                echo "non ci sono righe" ;
                            }
                            echo "<tr>
                                    <td>".$nomepk."</td>
                                    <td>
                                        <select name=fk$nomepk>
                                        <option> --- </option>";
                                        while ($row = mysqli_fetch_array($ris_attri)) {
                                            echo "<option value=" . $row['Nome'] . ">" . $row['Nome'] ." ".$row['Tipo'] . "</option>";
                                        }
                                        echo "</select> <br><br>
                                    </td>
                                </tr>";
    
                        }
                        
                    }

                echo "</table>
                <input type=submit name=sceltafk value='scegli attributi'><br><br>
                </form>";
                if (!mysqli_commit($conn)) {
                    mysqli_rollback($conn);
                    echo "Errore durante il commit della transazione.";
                }
            }

            if (isset($_GET["sceltafk"])) {

                $tabella = $_SESSION['tabella2'];
                $tabella1 = $_SESSION['tabella1'];
                $chiaviprimarie="";
                $chiaviesterne="";
                foreach($_SESSION["primarykey"] as $chiave){
                    echo $chiave."<br>";
                    $primarykey = $chiave;
                    $valoriSeparati = explode("-", $primarykey);
                    $nomepk = $valoriSeparati[0];
                    $tipopk = $valoriSeparati[1];
                    $_SESSION["fkey$nomepk"] = $_GET["fk$nomepk"];
                    $fkey = $_SESSION["fkey$nomepk"];
                    $chiaviprimarie .= $nomepk . ", ";
                    $chiaviesterne .= $fkey . ", ";
                    $query="INSERT INTO vincolo(NomeAttributoPK, NomeTabellaPK, NomeAttributoFK, NomeTabellaFK) VALUES ('$nomepk', '$tabella1', '$fkey', '$tabella');";
                    $inserimento = mysqli_query($conn, $query);

                    if (!$inserimento) {
                        echo "ricerca fallita insert vincolo: " . mysqli_error($conn);
                    }else{
                        echo "inserimento vincolo effettuato";
                    }


                }
                    $chiaviprimarie=rtrim($chiaviprimarie, ', ');
                    $chiaviesterne=rtrim($chiaviesterne, ', ');

                    echo "<br>" .$chiaviprimarie ."<br>" .$chiaviesterne;
                    
                    $key='CALL Vincoli("'.$tabella.'", "'.$tabella1.'", "'.$chiaviprimarie.'", "'.$chiaviesterne.'");';
                    $foreign = mysqli_query($conn, $key);
                    if (!$foreign) {
                        echo "chiave primaria fallita: " . mysqli_error($conn);
                    }
                    else{ echo "creazione vincolo fisico effettuato";}
                    
                
            }
        
            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                echo "Errore durante il commit della transazione.";
            }
        ?>
        <form name=aggiungi method=GET action='vincoli.php'>
            <input type=submit name=aggiungi value="crea vincolo">
        </form>
        

        <br> <br> <a href=hpDocente.php > <- </a>
    </div>

    <?php

    

    // chiusura della connessione
    mysqli_close($conn);
    ?>

</body>

</html>
