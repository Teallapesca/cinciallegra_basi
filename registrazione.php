<!DOCTYPE html>
<html class="page-size">
<head>
    <link type="text/css" rel="stylesheet" href="stile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <style>
    .page-size{
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        background-color: rgb(241, 246, 249);
    }
    </style>
</head>
<body class="d-flex flex-column align-items-center justify-content-center page-size">
    
    <?php include 'Navbar.php' ?>
   
    <div class="card">
    <div class="card-body">

    <div >
        <form name="registrazione" method="GET" action="registrazione.php" class="d-flex flex-row mb-4">
            <input class="btn btn-primary btn-lg" type="submit" name="docente" value="docente"> <br><br>
            <input class="btn btn-primary btn-lg" style="margin-left:20px;" type="submit" name="studente" value="studente"> <br><br>
        </form>
        <?php
            session_start();
            if (isset($_GET["docente"])) {
                $_SESSION['utente']  = "docente";
                 
                ?>
                
                    <form name=regutente method=GET action=signin.php>

                        <div class="input-group mb-3">  
                        <span class="input-group-text" id="basic-addon1">Mail</span> <input class="form-control" type='text' name='mail' value=''> <br>
                        </div>

                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Nome</span> <input class="form-control" type='text' name='nome' value=''> <br>
                        </div>

                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Cognome</span> <input class="form-control"  type='text' name='cognome' value=''> <br>
                        </div>

                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Telefono</span> <input class="form-control"  type='number' name='telefono' > <br>
                        </div>

                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Corso</span> <input class="form-control"  type='text' name='corso' value=''> <br>
                        </div>

                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Dipartimento</span> <input class="form-control"  type='text' name='dip' value=''>
                        </div>
                        <input class="btn btn-primary"  type=submit name=reg value=registrati>
                    </form>
                
                <?php

            } else if (isset($_GET["studente"])) {
                $_SESSION['utente'] = "studente";
                ?>
               
                    <form name=regutente method=GET action=signin.php>

                    <div class="input-group mb-3"> 
                    <span class="input-group-text" id="basic-addon1">Mail</span> <input class="form-control" type='text' name='mail' value=''> <br>
                    </div>

                    <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Nome</span> <input class="form-control" type='text' name='nome' value=''> <br>
                    </div>

                    <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Cognome</span> <input class="form-control"  type='text' name='cognome' value=''> <br>
                    </div>

                    <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Telefono</span> <input class="form-control"  type='number' name='telefono' > <br>
                    </div>

                    <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Matricola</span> <input class="form-control" type='text' name='matricola' value=''> <br><br>
                    </div>

                    <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">Anno di immatricolazione</span> <input class="form-control" type='number' name='anno'>
                    </div>

                    <input class="btn btn-primary"  type=submit name=reg value=registrati>
                    </form>
               
                <?php
                
            }
        ?>
        
    </div>


    </div>
    </div>
   
    <?php include 'footer.php' ?>


</body>
</html>
