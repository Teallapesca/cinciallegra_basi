use moodle;
DELIMITER $
CREATE TRIGGER DiminuisciNumRighe
AFTER DELETE ON Risposta
FOR EACH ROW
BEGIN
    DECLARE numeroRisposte INT;
    DECLARE numeroQuesiti INT;
    
    -- Conta quante risposte corrette sono state date dallo studente al test
    SELECT COUNT(*) INTO numeroRisposte
    FROM Risposta
    WHERE MailStudente = NEW.MailStudente AND TitoloTest = NEW.TitoloTest AND Esito=1;
    
    -- Conta i quesiti totali del test
    SELECT COUNT(*) INTO numeroQuesiti
    FROM quesito
    WHERE TitoloTest = NEW.TitoloTest;
    
    -- Se è la prima risposta, aggiorna lo stato del test a "InCompletamento"
    IF numeroRisposte = numeroQuesiti THEN
        UPDATE Svolgimento
        SET Stato = 'Concluso'
        WHERE svolgimento.MailStudente = NEW.MailStudente AND svolgimento.TitoloTest = NEW.TitoloTest;
    END IF;
END;
$ DELIMITER ;


DELIMITER $  /* 2) Inserimento di una riga per una tabella di esercizio, definita dal docente. */
CREATE TRIGGER DiminuisciNumRighe (
    IN NomeTabella VARCHAR(30),
    IN Docente VARCHAR(40))
BEGIN
    UPDATE TABELLA_ESERCIZIO
    SET NumeroRighe=NumeroRighe-1
    WHERE (Nome=NomeTabella) AND (MailDocente=Docente);
END $ DELIMITER ;