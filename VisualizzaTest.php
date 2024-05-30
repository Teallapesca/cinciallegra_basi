<!doctype html>
<html>
	<head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
        form {
            margin-bottom: 20px; /* Aggiunge spazio sotto il modulo */
            margin-top: 30px;
        }

        svg{
            color:navy;
            background-size: 50px;
            position: absolute;
            size: 50px;
            left: 50px;
        }
        .bg-color {
            border: 2px solid black; 
            background-color: #394867;
            color: white;
        }
        button {
            background-color: #212A3E; /* Sfondo del bottone */
            color: white; /* Colore del testo del bottone */
            border-radius: 10px; /* Bordo arrotondato */
            padding: 10px 20px; /* Spazio intorno al testo */
            font-size: 15px; /* Dimensione del testo */
            cursor: pointer; /* Mostra il cursore a forma di mano */
            top: 50%; /* Posizione al centro verticalmente */
            border: none;
       }

       .textfield{
            background-color: white; /* Sfondo del bottone */
            color: black; /* Colore del testo del bottone */
            border: solid;
            border-color: grey;
            border-radius: 10px; /* Bordo arrotondato */
            padding: 10px 20px; /* Spazio intorno al testo */
            font-size: 15px; /* Dimensione del testo */
            top: 50%; /* Posizione al centro verticalmente */
       }

       label{
        font-weight: bold;
        font-size: 20px;
       }
       
        button:hover {
            background-color: #415379; /* Cambia il colore del background al passaggio del mouse */
        }

        .intesta {
            background-color: #DCDCDC;
            padding: 20px 0;
            position: relative;
            overflow: auto;
            font-weight: bold;
            color: navy;
            text-align: center;
        }

        .margine-sinistra {
            margin-left: 25px;
        }

        a{
            font-size: 20px;
        }
    
     </style>
	</head>
	<body>
		<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
		?>
		<div class="intesta">
        <a href="hpDocente.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-arrow-left-circle-fill">
                <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0m3.5 7.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5z"/>
                </svg>
            </a>
			<h1>VISUALIZZA TEST</h1>
		</div>
        <br><br>
		<ul>
            <?php
                $docente = $_SESSION['mailDocente'];

                //chiamo la procedura per visualizzare i test, ma solo quelli che ha creato il docente loggato
                $query="CALL VisualizzazioneTestDoc('$docente')";
                $risultato=mysqli_query($conn,$query);

                if(!$risultato){
                    echo "ricerca fallita: " . die (mysqli_error($conn));
                }
                if(mysqli_num_rows($risultato)==0){
                    echo "Non hai ancora creato test";
                }
                else{
                    while($row = mysqli_fetch_array($risultato)){
                        $titoloTest = $row['Titolo'];
                        $_SESSION['titoloTest'] = $titoloTest;
                        echo "
                            <li class='margine-sinistra'>
                            <a href='DettagliTest.php?titolo=$titoloTest'> {$titoloTest} ({$row['DataTest']}) </a>
                            </li><br>
                        ";                        
                    }
                }
            
            ?>
		</ul>
	</body>
    <?php
    
        // chiusura della connessione
        mysqli_close($conn);
    ?>
</html>