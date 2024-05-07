use moodle;

/* STATISTICHE (Visibili da tutti gli Utenti) */
/* 1) VISUALIZZARE LA CLASSIFICA DEGLI STUDENTI 
	(SULLA BASE DEL NUMERO DI TEST COMPLETATI). */
CREATE VIEW ClassificaConcluso(CodiceMatricola, Conteggio) AS (
	SELECT CodiceMatricola, COUNT(*) AS Conteggio
	FROM STUDENTE, SVOLGIMENTO
    WHERE (SVOLGIMENTO.MailStudente = STUDENTE.Mail) AND (SVOLGIMENTO.Stato = 'Concluso')
    GROUP BY CodiceMatricola
    ORDER BY Conteggio DESC 
    /* DESC perché gli studenti vanno ordinati in ordine decrescente rispetto
		al numero di test che hanno completato (chi ha concluso più test sta in alto, chi meno sta in basso)*/
);

/* 2) VISUALIZZARE LA CLASSIFICA DEGLI STUDENTI 
	(SULLA BASE DEL NUMERO DI RISPOSTE CORRETTE). */
CREATE VIEW ClassificaCorretto(CodiceMatricola, Percentuale) AS (
SELECT CodiceMatricola, ((COUNT(CASE WHEN RISPOSTA.Esito = 'true' THEN 1 END)) / COUNT(*)) AS Percentuale
FROM STUDENTE, RISPOSTA
WHERE (RISPOSTA.MailStudente = STUDENTE.Mail)
GROUP BY CodiceMatricola
ORDER BY Percentuale DESC
);

/* 3) VISUALIZZARE LA CLASSIFICA DEI QUESITI 
	(IN BASE AL NUMERO DI RISPOSTE INSERITE). */
CREATE VIEW ClassificaQuesiti(Descrizione, Conteggio) AS (
	SELECT Descrizione, SUM(NumRisposte) AS Conteggio
	FROM QUESITO
    GROUP BY Descrizione
    ORDER BY Conteggio DESC
);

/* TRIGGERS */
DELIMITER $
CREATE TRIGGER InCompletamento
AFTER INSERT ON Risposta
FOR EACH ROW
BEGIN
    DECLARE numeroRisposte INT;
    
    -- Conta quante risposte sono state date dallo studente al test
    SELECT COUNT(*) INTO numeroRisposte
    FROM Risposta
    WHERE MailStudente = NEW.MailStudente AND TitoloTest = NEW.TitoloTest;
    
    -- Se è la prima risposta, aggiorna lo stato del test a "InCompletamento"
    IF numeroRisposte = 1 THEN
        UPDATE Svolgimento
        SET Stato = 'InCompletamento'
        WHERE svolgimento.MailStudente = NEW.MailStudente AND svolgimento.TitoloTest = NEW.TitoloTest;
    END IF;
END;
$ DELIMITER ;


-- concluso
use moodle;
DELIMITER $
CREATE TRIGGER Concluso
AFTER INSERT ON Risposta
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

-- SE SI FA UN AGGIORNAMENTO IN RISPOSTA
DELIMITER $
CREATE TRIGGER Concluso2
AFTER UPDATE ON Risposta
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