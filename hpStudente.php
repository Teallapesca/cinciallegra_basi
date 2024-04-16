<!doctype html>
<html>
<head>
	<title> Studente </title>
    <link type="text/css" rel="stylesheet" href="stile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

    <?php 
     if (isset($_POST["logout"])) {
        session_destroy();
        session_unset();
        header('Location: HomePage.html');
        exit();
    }
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        include 'connessione.php';
        mysqli_begin_transaction($conn);
        //include 'Navbar.php' 
    ?> 

    <div class="student-image">
       <h1 style="color: white" class="mb-5 hero-text"> <?php echo "benvenuto " . $_SESSION['mailStudente']; ?> </h1>
    </div>
    
	<div class="d-flex flex-row  justify-content-center">
		<form name="visTest" method="GET" action="hpStudente.php">
            <input  class="btn btn-primary btn-lg m-4" type="submit" name="test" value="visualizza test">
        </form>
        <form method="POST" action="hpStudente.php">
            <button  class="btn btn-primary btn-lg m-4" type="submit" name="logout">Logout</button>
        </form>
    </div>

        <?php
            if (isset($_GET["test"])) {
                $query="CALL VisualizzazioneTest();";

                $risult=mysqli_query($conn,$query);

                if(!$risult){
                    echo "errore nella ricerca" . die (mysqli_error($conn));
                }
                else{

                    if(mysqli_num_rows($risult)==0){
                        echo "non ci sono righe";
                        }
                    else{
                        while($row = mysqli_fetch_array($risult))
                        {
                            $titoloTest = $row['Titolo'];
                            $_SESSION['titoloTest'] = $titoloTest;
                            echo "
                            <a href='SvolgiTest.php?titolo=$titoloTest'> {$titoloTest} ({$row['DataTest']}, {$row['MailDocente']})  </a><br><br>
                        ";
                        }
                    }
                }
            }
            
            // chiusura della connessione
            mysqli_close($conn);
        ?>
	</div>
</body>
</html>