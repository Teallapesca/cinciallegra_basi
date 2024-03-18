<!doctype html>
<html>
<!--<head>
    <title> Creazione tabelle </title>
    <style>
        .centrato{text-aligne:centre;}
        body{margin:0;}
        div.intesta{
            position:relative;
            width:100%;
            height:30%;
            float:left;
            overflow:auto;
            font-style:italic;
            font-weight:bold;
            font-size:20px;
            color:blue;
            text-align:center;
            font-family:helvetica, sans-serif;
            margin-left:20px;}
        div.sinistra{
            position:relative;
            width:40%;
            height:88%;
            float:left;
            overflow:auto;
            font-weight:bold;
            font-size:16px;
            font-family:arial, sans-serif;
            margin-left:20px;}
        div.principale{
            position:relative;
            width:20%;
            height:88%;
            float:left;
            overflow:auto;
            font-weight:bold;
            font-size:16px;
            font-family:arial, sans-serif;
            margin-left:100px;}
        .bordo{border-color:turquoise;
            border-style:solid;
            border-width:2px;
            text-align:center;  
        }
        legend{color:darkblue;}
       
    </style>
    
</head>-->
	<body>
		<?php
			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			include 'connessione.php';
			mysqli_begin_transaction($conn);
		?>
		<div class="titolo">
			<h1>CREA TABELLE</h1>
		</div>
		<div class="principale">
			<form name="nuovaTabella" method="GET" action="CreaTabelle.php">
				Inserisci il nome della tabella<br><br>
				<input type="text" name="nomeTabella" value=""><br><br>
				<input type="submit" name="tab" value="Inserisci nome">
			</form>

			<?php 
                echo $_SESSION['mail'];
				if(isset($_GET["tab"])){
                    $_SESSION['nomeTabella'] = $_GET["nomeTabella"];
                    if($_SESSION['nomeTabella']!=null){
                        $nomeTabella = $_SESSION['nomeTabella'];
                        $mail = $_SESSION['mail'];
                        $query = 'CALL InserimentoTabellaEsercizio("'.$nomeTabella.'", "'.$mail.'");';
                        // esecuzione query
                        $risultato = mysqli_query($conn,$query);
                        if($risultato === false){
                            echo "errore nella ricerca" . die (mysqli_error($conn));}
                        else{
                            echo "tabella inserita";
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