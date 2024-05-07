<?php
//controllare le query studenti e professori

    function controlloCodice($conn, $progressivo, $risposta){

        $esito=0;
        $query="SELECT Soluzione FROM sketch_codice WHERE Progressivo=$progressivo;"; //prendo il testo della soluzione del prof
        $risult = mysqli_query($conn, $query);
        if (!$risult) {
            echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
        }else{
            $row = mysqli_fetch_assoc($risult);
            $query_sol="$row[Soluzione];"; //eseguo la query impostata come soluzione
            $risultato = mysqli_query($conn, $query_sol);
            if (!$risultato) {
                echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
            }else{
                $soluzione=mysqli_fetch_assoc($risultato);
            }
        }

        //eseguo la query data in risposta dallo studente
        $query_risp="$risposta";
        $risult = mysqli_query($conn, $query_risp);
        if (!$risult) {
            echo "Errore nel controllo delle risposte: " . mysqli_error($conn);
        }else{
            $risp = mysqli_fetch_assoc($risult);
            
        }

        if($risp==$soluzione){
            $esito=1;
        }

        return $esito;
    }
    

?>