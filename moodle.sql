/* OPERAZIONI SUI DATI */
use moodle;

/*---------------------------------------------------------------------------------*/
/*OPERAZIONI che riguardano TUTTI GLI UTENTI:*/
/* 1) autenticazione sulla piattaforma. */
/* who è 1 per docente e 2 per studente*/
DELIMITER $
CREATE PROCEDURE Autenticazione(IN Mail VARCHAR(30), OUT who INT)
BEGIN
    DECLARE countDocenti INT DEFAULT 0;
    DECLARE countStudenti INT DEFAULT 0;
    DECLARE who INT DEFAULT 0;

    SELECT COUNT(*) INTO countDocenti FROM DOCENTE WHERE Mail = Mail;
    SELECT COUNT(*) INTO countStudenti FROM STUDENTE WHERE Mail = Mail;

    IF countDocenti = 1 THEN
        SET who = 1;
    ELSEIF countStudenti = 1 THEN
        SET who = 2;
    END IF;
END$
DELIMITER ;

DELIMITER $  /* 1) registrazione sulla piattaforma*/
CREATE PROCEDURE RegistrazioneStudente(IN Mail VARCHAR(30), IN Nome VARCHAR(30), IN Cognome VARCHAR(30), IN Telefono BIGINT, IN Matricola INT, IN AnnoImmatricolazione BIGINT )
BEGIN
    DECLARE countDocenti INT DEFAULT 0;
    DECLARE countStudenti INT DEFAULT 0;
    
    SET countDocenti = (SELECT COUNT(*) FROM STUDENTE WHERE (Mail = STUDENTE.Mail));
    SET countStudenti = (SELECT COUNT(*) FROM DOCENTE WHERE (Mail = DOCENTE.Mail));
    
    IF(countDocenti = 0) AND (countStudenti = 0) THEN
		INSERT INTO STUDENTE VALUES (Mail, Nome, Cognome, Telefono, Matricola, AnnoImmatricolazione);
	END IF;
END$

DELIMITER $  /* 1) registrazione sulla piattaforma*/
CREATE PROCEDURE RegistrazioneDocente(IN Mail VARCHAR(30), IN Nome VARCHAR(30), IN Cognome VARCHAR(30), IN Telefono BIGINT, IN Corso VARCHAR(30), IN Dipartimento VARCHAR(30) )
BEGIN
    DECLARE countDocenti INT DEFAULT 0;
    DECLARE countStudenti INT DEFAULT 0;
    
    SET countDocenti = (SELECT COUNT(*) FROM STUDENTE WHERE (Mail = STUDENTE.Mail));
    SET countStudenti = (SELECT COUNT(*) FROM DOCENTE WHERE (Mail = DOCENTE.Mail));
    
    IF(countDocenti = 0) AND (countStudenti = 0) THEN
		INSERT INTO DOCENTE VALUES (Mail, Nome, Cognome, Telefono, Corso, Dipartimento);
	END IF;
END$


DELIMITER $  /* 2) Visualizzazione dei Test Disponibili. */
CREATE PROCEDURE VisualizzazioneTest()
BEGIN
    SELECT * 
    FROM TEST; 
END$

DELIMITER $  /* 3) Visualizzazione dei quesiti presenti all’interno di ciascun test. */
CREATE PROCEDURE VisualizzazioneQuesiti(IN Titolo VARCHAR(30))
BEGIN
    SELECT TitoloTest, Progressivo, Difficoltà, Descrizione 
    FROM QUESITO
    WHERE Titolo = TitoloTest;
END$

/*---------------------------------------------------------------------------------*/
/*OPERAZIONI che riguardano SOLO DOCENTI:*/
DELIMITER $  /* 1) Inserimento di una nuova tabella di esercizio, con relativi meta-dati. */
CREATE PROCEDURE InserimentoTabellaEsercizio (
    IN NomeTabella VARCHAR(30),
    IN NumeroRighe INT,
    IN TitoloTest VARCHAR(30),
    IN MailDocente VARCHAR(40))
BEGIN
    INSERT INTO TABELLA_ESERCIZIO(Nome, DataCreazione, NumeroRighe, MailDocente)
    VALUES (NomeTabella, date, 0, MailDocente);
END $ DELIMITER ;

DELIMITER $  /* 2) Inserimento di una riga per una tabella di esercizio, definita dal docente. */
CREATE PROCEDURE InserimentoRigaTabellaEsercizio (
    IN NomeTabella VARCHAR(30),
    IN Docente VARCHAR(40))
BEGIN
    UPDATE TABELLA_ESERCIZIO
    SET NumRighe=NumRighe+1
    WHERE (Nome=NomeTabella) AND (MailDocente=Docente);
END $ DELIMITER ;

DELIMITER $  /* 3) Creazione di nuovo test. */
CREATE PROCEDURE CreaTest (
    IN TitoloTest VARCHAR(30),
    IN FotoTest BOOLEAN,
    IN VisualizzaRisposteTest BOOLEAN,
    IN MailDocente VARCHAR(30)
)
BEGIN
    INSERT INTO TEST (Titolo, DataTest, Foto, VisualizzaRisposte, MailDocente)
    VALUES (TitoloTest, Data, FotoTest, VisualizzaRisposteTest, MailDocente);
END $ DELIMITER ;

DELIMITER $  /* 4) Creazione di un nuovo quesito con le relative risposte. */
CREATE PROCEDURE NewSketchCodice (
	IN ProgressivoCodice INT,
    IN TitoloTest VARCHAR(30),
    IN Difficolta VARCHAR(5),
    IN DescrizioneQuesito VARCHAR(40),
    IN NumeroRisposte INT,
    IN Soluzione VARCHAR(300)
)
BEGIN
    INSERT INTO SKETCH_CODICE (Progressivo, TitoloTest, Difficolta, Descrizione, NumRisposte, Soluzione)
    VALUES (ProgressivoCodice, TitoloTest, Difficolta, DescrizioneQuesito, 0, Soluzione);
	INSERT INTO QUESITO (Progressivo, TitoloTest, Difficolta, Descrizione, NumRisposte)
    VALUES (ProgressivoCodice, TitoloTest, Difficolta, DescrizioneQuesito, 0);
END $ DELIMITER ;
DELIMITER $
CREATE PROCEDURE NewQuesitoChiuso (
	IN ProgressivoQuesito INT,
    IN TitoloTest VARCHAR(30),
    IN Difficolta VARCHAR(5),
    IN DescrizioneQuesito VARCHAR(40),
    IN NumeroRisposte INT,
    IN OpzioneGiusta VARCHAR(1)
)
BEGIN
    INSERT INTO QUESITO_CHIUSO (Progressivo, TitoloTest, Difficolta, Descrizione, NumRisposte, OpzioneGiusta)
    VALUES (ProgressivoQuesito, TitoloTest, Difficolta, DescrizioneQuesito, 0, OpzioneGiusta);
	INSERT INTO QUESITO (Progressivo, TitoloTest, Difficolta, Descrizione, NumRisposte)
    VALUES (ProgressivoCodice, TitoloTest, Difficolta, DescrizioneQuesito, 0);
END $ DELIMITER ;

/* 5) Abilitare / disabilitare la visualizzazione delle risposte per uno specifico test. */
DELIMITER $
CREATE PROCEDURE VisualizzazioneRisposte (
	IN TitoloTest VARCHAR(30),
    IN VisualizzaRisposte boolean
)
BEGIN
    UPDATE TEST
    SET TEST.VisualizzaRisposte = VisualizzaRisposte
    WHERE (TitoloTest = TEST.Titolo);
END $ DELIMITER ;

/* 6) Inserimento di un messaggio (da parte del docente). 
DELIMITER $
CREATE PROCEDURE InserimentoMessaggioDocente (
    IN TestoMessaggio VARCHAR(100),
    IN TitoloMessaggio VARCHAR(30),
    IN TitoloTest VARCHAR(30),
    IN MailDocente VARCHAR(40))
BEGIN
    -- Inserimento del messaggio nella tabella MESSAGGIO
    INSERT INTO MESSAGGIO (DataInserimento, Testo, Titolo, TitoloTest, MailStudente, MailDocente)
    VALUES (CURDATE(), TestoMessaggio, TitoloMessaggio, TitoloTest, NULL, MailDocente);

    SELECT 'Messaggio inserito con successo nella tabella MESSAGGIO';
END $ DELIMITER ;
*/

/*---------------------------------------------------------------------------------*/
/*OPERAZIONI che riguardano SOLO STUDENTI*/
DELIMITER $  /* 1) Inserimento di una nuova risposta (ad un quesito di codice o un quesito chiuso). */
CREATE PROCEDURE InserimentoRisposta (IN ProgressivoQuesito INT, IN TitoloTest VARCHAR(30), IN MailStudente VARCHAR(40))
BEGIN
 INSERT INTO RISPOSTA(ProgressivoQuesito, TitoloTest, MailStudente)
    VALUES (ProgressivoQuesito, TitoloTest, MailStudente);
END $ DELIMITER ;

DELIMITER $  /* 2) Visualizzazione dell'esito di una risposta (ad un quesito di codice o un quesito chiuso). */
CREATE PROCEDURE VisualizzaEsitoRisposta (IN ProgressivoQuesito INT, IN TitoloTest VARCHAR(30), IN MailStudente VARCHAR(30))
BEGIN
	SELECT Esito
	FROM RISPOSTA
	WHERE RISPOSTA.ProgressivoQuesito = ProgressivoQuesito 
			AND RISPOSTA.MailStudente = MailStudente 
			AND RISPOSTA.TitoloTest = TitoloTest;
END $ DELIMITER ;

/* 3) Inserimento di un messaggio (da parte dello studente).
DELIMITER $
CREATE PROCEDURE InserimentoMessaggioStudente (
    IN TestoMessaggio VARCHAR(100),
    IN TitoloMessaggio VARCHAR(30),
    IN TitoloTest VARCHAR(30),
    IN MailDocente VARCHAR(40),
    IN MailStudente VARCHAR(40))
BEGIN
    INSERT INTO MESSAGGIO (DataInserimento, Testo, Titolo, TitoloTest, MailStudente, MailDocente)
    VALUES (CURDATE(), TestoMessaggio, TitoloMessaggio, TitoloTest, MailStudente, MailDocente);

    SELECT 'Messaggio inserito con successo nella tabella MESSAGGIO';
END $ DELIMITER ;
*/

