<?php
    session_start();
    $conn=mysqli_connect("localhost:8889","root","root","moodle");
    // controllo sullo stato della connessione
    if (mysqli_connect_errno())
    {
        echo "Connessione fallita: " . die (mysqli_connect_error());
    }
            //portare la connessione in un'altra classe (dire di aver usato il pattern singleton) e richiamarlo con require 'nomefile'
        // controllo sullo stato della connessione
            
        // settaggio character set
    mysqli_query($conn,"SET CHARACTER SET 'utf8'");
?>
