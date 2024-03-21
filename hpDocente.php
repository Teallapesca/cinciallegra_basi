<!doctype html>
<html>
<head>
    <title> Docente </title>
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
    
</head>
<body>
    <div class=intesta><h1> Funzioni Docente </h1>
    <br><br><button class="homepage-btn" onclick="openPage('CreaTabelle')" style="float:center; color: #D40000;">Crea tabelle</button>
    <button class="homepage-btn" onclick="openPage('CreaTest')" style="float:center; color: #D40000;">Crea test</button>
    <button class="homepage-btn" onclick="openPage('Messaggio')" style="float:center; color: #D40000;">Invia messaggio</button>
    <button class="homepage-btn" onclick="openPage('PopolaTabelle')" style="float:center; color: #D40000;">Popola tabelle</button>
    <button class="homepage-btn" onclick="openPage('VisualizzaTest')" style="float:center; color: #D40000;">Visualizza test</button>
    </div>

    <?php
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        include 'connessione.php';
        mysqli_begin_transaction($conn);

        echo "benvenuto " . $_SESSION['mail'];
    
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

<script>
    function openPage(page) {
            //var tipoUtente = document.querySelector('input[name="utente"]:checked').value;
            var tipoPagina = page;
            // Reindirizza l'utente alla pagina specificata
            var pagina;


            if (tipoPagina==="CreaTabelle") {
                pagina = "CreaTabelle.php";
            } else if (tipoPagina==="CreaTest") {
                pagina = "TestPage.php";
            } else if (tipoPagina==="Messaggio") {
                pagina = "MessaggioDocente.html";
            } else if (tipoPagina==="PopolaTabelle") {
                pagina = "PopolaTabella.php";}
            else if (tipoPagina==="VisualizzaTest") {
                pagina = "VisualizzaTest.php";
            }
            // Reindirizza l'utente alla pagina corrispondente
            window.location.href = pagina;
        }
</script>