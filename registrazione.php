<!DOCTYPE html>
<html>
<head>
</head>
<body>
    <div class="titolo">
		<h1> REGISTRATI! </h1>
    </div>
    
    <div class="principale">
        <form name="registrazione" method="GET" action="registrazione.php">
            <input type="submit" name="docente" value="docente"> <br><br>
            <input type="submit" name="studente" value="studente"> <br><br>
        </form>
        <?php
            session_start();
            if (isset($_GET["docente"])) {
                $_SESSION['utente']  = "docente";
                echo "
                <form name=regutente method=GET action=signin.php>
                    Email <input type='text' name='mail' value=''> <br><br>
                    nome <input type='text' name='nome' value=''> <br><br>
                    cognome <input type='text' name='cognome' value=''> <br><br>
                    telefono <input type='number' name='telefono' > <br> <br>
                    corso <input type='text' name='corso' value=''> <br> <br>
                    Dipartimento <input type='text' name='dip' value=''>
                    <input type=submit name=reg value=registrati>
                    </form>
                ";

            } else if (isset($_GET["studente"])) {
                $_SESSION['utente'] = "studente";
                echo "
                <form name=regutente method=GET action=signin.php>
                    Email <input type='text' name='mail' value=''> <br><br>
                    nome <input type='text' name='nome' value=''> <br><br>
                    cognome <input type='text' name='cognome' value=''> <br><br>
                    telefono <input type='number' name='telefono'> <br> <br>
                    matricola <input type='text' name='matricola' value=''> <br><br>
                    anno di immatricolazione <input type='number' name='anno'>
                    <input type=submit name=reg value=registrati>
                </form>
                ";
            }
        ?>
        
    </div>
</body>
</html>
