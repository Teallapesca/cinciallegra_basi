<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    $mail="";
    $nome="";

    if($_SESSION['aut']==1){
        $mail=$_SESSION['mailDocente'];
        $query="SELECT Nome FROM docente WHERE Mail='$mail';";
    }else{
        $mail=$_SESSION['mailStudente'];
        $query="SELECT Nome FROM studente WHERE Mail='$mail';";
    }
    $risultato = mysqli_query($conn,$query);
    if($risultato === false){
        echo "errore nella ricerca" . mysqli_error($conn);}
    else{
        if (mysqli_num_rows($risultato) > 0) {
            $row = mysqli_fetch_array($risultato);
            $nome = $row['Nome']; 
        }
    }
?>
<nav class="fixed-top navbar navbar-expand-lg bg-body-tertiary" style="height: 100px;">
    <div class="container-fluid">
        <div class="collapse navbar-collapse d-flex justify-content-center" id="navbarNavAltMarkup">
            <div class="navbar-nav position-absolute top-10 start-0 ms-5">
            <a class="icon-link icon-link-hover " style="--bs-icon-link-transform: translate3d(0, -.125rem, 0);" href="HomePage.html">
                <img src="icona2.png" class="bi w-50 h-100" aria-hidden="true">
            </a>
            <h3>Cinciallegra </h3>
            </div>
        </div>
        <h3 style="color: black" class="mb-2 me-5"> <?php echo "benvenuto/a " . $nome; ?> </h3>
    </div>
</nav>
