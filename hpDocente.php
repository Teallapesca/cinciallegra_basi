<!doctype html>
<html>
<head>
    <title> Docente </title>
    <link type="text/css" rel="stylesheet" href="stile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>

    <?php 
      ini_set('display_errors', 1);
      error_reporting(E_ALL);
      include 'connessione.php';
      mysqli_begin_transaction($conn);
    include 'Navbar.php' ?> 

    <div class="hero-image">
       <h1 style="color: white" class="mb-5 hero-text"> <?php echo "benvenuto " . $_SESSION['mailDocente']; ?> </h1>
    </div>  

    <div class="m-5 d-flex flex-column justify-content-center align-items-center">
        <div class="d-flex flex-row align-items-center" style="display: block;">
            <button onclick="toggleDropdown('tabelleDropdown')" class="btn btn-primary btn-lg m-4">Tabelle <?php  $_SESSION['prima']=1; //per mostrare la scelta della prima tabella in vincoli.php?></button>
            <button id="button-style" onclick="openPage('CreaTest')" class="btn btn-primary btn-lg m-4">Crea test</button>
            <button id="button-style" onclick="openPage('Messaggio')" class="btn btn-primary btn-lg m-4">Invia messaggio</button>
            <button id="button-style" onclick="openPage('VisualizzaTest')"  class="btn btn-primary btn-lg m-4">Visualizza test</button>

            <form name="logoutf" method="GET" action="hpDocente.php">
                <button class="btn btn-primary btn-lg m-4" type="submit" name="logout">Logout</button>
            </form> 
        </div>
        <!-- Dropdown Menu -->
        <div id="tabelleDropdown" style="display: none;">
            <button onclick="openPage('CreaTabelle')" class="dropdown-btn btn btn-primary btn-lg m-4">Crea un nuova tabella</button>
            <button onclick="openPage('PopolaTabelle')" class="dropdown-btn btn btn-primary btn-lg m-4">Popola una tabella</button>
            <button onclick="openPage('Vincoli')" class="dropdown-btn btn btn-primary btn-lg m-4">Crea vincoli di integrità</button>
        </div> 
        <!-- Fine Dropdown Menu -->
    </div>


    

    <?php
            if (!mysqli_commit($conn)) {
                mysqli_rollback($conn);
                echo "Errore durante il commit della transazione.";
            }
        
            // chiusura della connessione
            mysqli_close($conn);
        if (isset($_GET["logout"])) {
            session_destroy();
            session_unset();
            header('Location: HomePage.html');
            exit();
        }    
    ?>

</body>
</html>

<script>
    function toggleDropdown(id) {
        var dropdown = document.getElementById(id);
        if (dropdown.style.display === "none") {
            dropdown.style.display = "block";
        } else {
            dropdown.style.display = "none";
        }
    }
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
            }else if (tipoPagina==="Vincoli") {
                pagina = "vincoli.php";
            }
            
            // Reindirizza l'utente alla pagina corrispondente
            window.location.href = pagina;
        }
</script>

