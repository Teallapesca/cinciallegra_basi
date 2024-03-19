<!doctype html>
<html>
<head>
	<title> moodleStudente </title>
</head>
<body>
    <?php
    if (isset($_POST["logout"])) {
        session_destroy();
        header('Location: HomePage.html');
        exit();
    }
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        include 'connessione.php';
        mysqli_begin_transaction($conn);

        echo "benvenuto " . $_SESSION['mail'];
    ?>
	<div class=principale>
		<form name="visTest" method="GET" action="hpStudente.php">
            <input type="submit" name="test" value="visualizza test"> <br><br>
        </form>
        <form method="POST" action="hpStudente.php">
            <button type="submit" name="logout">Logout</button>
        </form>
        <?php
            if (isset($_GET["test"])) {
                $query="SELECT Titolo, DataTest FROM test;";

                $risult=mysqli_query($conn,$query);

                if(!$risult){
                    echo "errore nella ricerca" . die (mysqli_error($conn));
                }
                else{

                    if(mysqli_num_rows($risult)==0){
                        echo "non ci sono righe";
                        }
                    else{
                        while($row = mysqli_fetch_array($risult, MYSQLI_ASSOC))
                        {
                            echo $row['Titolo']. "&nbsp;". $row['DataTest'] . "<br>"; 
                        }
                    }
                }
            }
            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                echo "Errore durante il commit della transazione.";
            }
        
            // chiusura della connessione
            mysqli_close($conn);
            
            
        ?>
	</div>
</body>
</html>