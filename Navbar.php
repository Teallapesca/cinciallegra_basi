<?php
session_start();
    $mail="";
    if($_SESSION['aut']==1){
        $mail=$_SESSION['mailDocente'];
    }else{
        $mail=$_SESSION['mailStudente'];
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
        <h3 style="color: black" class="mb-2 me-5"> <?php echo "benvenuto/a " . $mail; ?> </h3>
    </div>
</nav>
