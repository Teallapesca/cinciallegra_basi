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
    <script>
        //********da sistemare il js */
        function mostraTesto(tipo) {
            var doc = document.getElementById("DIVdocente");
            var stu = document.getElementById("DIVstudente");
            var campi = document.getElementById("campi");

            campi.style.display = "block";
            <?php
            session_start();
            ?>
            if (tipo === "docente") {
              
                stu.style.display = "none";
                doc.style.display = "block";
             
            } else if (tipo === "studente") {
                
                doc.style.display = "none";
                stu.style.display = "block";
            }
        }
        function validateForm() {
            // Ottieni i valori dei campi del modulo
            var email = document.forms["regutente"]["mail"].value;
            var nome = document.forms["regutente"]["nome"].value;
            var cognome = document.forms["regutente"]["cognome"].value;
            var telefono = document.forms["regutente"]["telefono"].value;
            var dip = document.forms["regutente"]["corso"].value;
            var corso = document.forms["regutente"]["dip"].value;
            var anno = document.forms["regutente"]["anno"].value;
            var matr = document.forms["regutente"]["matricola"].value;

            // Controlla se tutti i campi richiesti sono stati compilati o selezionati
            if (email == "" || nome == "" || cognome == "" || !telefono || ((dip=="" || corso=="")&&(anno =="" || matr==""))) {
                alert("Compila tutti i campi richiesti!");
                return false; // Impedisci l'invio del modulo
            }
            return true; // Consenti l'invio del modulo
        }
    </script>
</head>
<body class="d-flex flex-column align-items-center justify-content-center page-size">
    <div class="card">
        <div class="card-body">
            <div>
                <form name="registrazione"class="d-flex flex-row mb-4">
                    <input class="btn btn-primary btn-lg" type="button" name="docente" value="docente" onClick="mostraTesto('docente')"> <br><br>
                    <input class="btn btn-primary btn-lg" style="margin-left:20px;" type="button" name="studente" value="studente" onClick="mostraTesto('studente')"> <br><br>
                </form>
            </div>  
            <div>
                <form name=regutente method=GET action=signin.php onsubmit="return validateForm()">

                    <div id="campi" style="display:none;">
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
                    </div>

                    <div id="DIVdocente" style="display:none;">
                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Corso</span> <input class="form-control"  type='text' name='corso' value=''> <br>
                        </div>

                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Dipartimento</span> <input class="form-control"  type='text' name='dip' value=''>
                        </div>

                        <input class="btn btn-primary"  type=submit name=regDoc value=registrati  >

                    </div>
                    <div id="DIVstudente" style="display:none;">
                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Matricola</span> <input class="form-control" type='text' name='matricola' value=''> <br><br>
                        </div>
                        
                        <div class="input-group mb-3">
                        <span class="input-group-text" id="basic-addon1">Anno di immatricolazione</span> <input class="form-control" type='number' name='anno'>
                        </div>

                        <input class="btn btn-primary"  type=submit name=regStu value=registrati  >
                    </div>
                    
                </form>
            </div>
        </div>
    </div>


</body>
</html>
