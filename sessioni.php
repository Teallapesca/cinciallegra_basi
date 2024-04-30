<html>
    <body>
        <?php
        // Avvia la sessione
        session_start();

        // Visualizza tutte le variabili di sessione
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
        ?>
    </body>
</html>
