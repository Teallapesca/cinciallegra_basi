/* OPERAZIONI SUI DATI */
use moodle;

/*---------------------------------------------------------------------------------*/
/*OPERAZIONI che riguardano TUTTI GLI UTENTI:*/
/* 1) autenticazione sulla piattaforma. */
/* who è 1 per docente e 2 per studente*/
DELIMITER $
CREATE PROCEDURE Autenticazione(IN InputMail VARCHAR(30), OUT who INT)
BEGIN
    DECLARE countDocenti INT DEFAULT 0;
    DECLARE countStudenti INT DEFAULT 0;
   

    SELECT COUNT(*) INTO countDocenti FROM DOCENTE WHERE docente.Mail = InputMail;
    SELECT COUNT(*) INTO countStudenti FROM STUDENTE WHERE studente.Mail = InputMail;

    IF countDocenti = 1 THEN
        SET who = 1;
    ELSEIF countStudenti = 1 THEN
        SET who = 2;
    END IF;
END$
DELIMITER ;

DELIMITER $  /* 1) registrazione sulla piattaforma*/
CREATE PROCEDURE RegistrazioneStudente(IN Mail VARCHAR(30), IN Nome VARCHAR(30), IN Cognome VARCHAR(30), IN Telefono BIGINT, IN Matricola VARCHAR(16), IN AnnoImmatricolazione BIGINT )
BEGIN
    DECLARE countDocenti INT DEFAULT 0;
    DECLARE countStudenti INT DEFAULT 0;
    
    SET countDocenti = (SELECT COUNT(*) FROM STUDENTE WHERE (Mail = STUDENTE.Mail));
    SET countStudenti = (SELECT COUNT(*) FROM DOCENTE WHERE (Mail = DOCENTE.Mail));
    
    IF(countDocenti = 0) AND (countStudenti = 0) THEN
		INSERT INTO STUDENTE VALUES (Mail, Nome, Cognome, Telefono, AnnoImmatricolazione, Matricola);
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


DELIMITER $  /* 2) Visualizzazione di tutti i test disponibili. (per lo studente) */
CREATE PROCEDURE VisualizzazioneTest()
BEGIN
    SELECT * 
    FROM TEST; 
END$

DELIMITER $  /* 2) Visualizzazione dei test disponibili creati da un determinato docente. */
CREATE PROCEDURE VisualizzazioneTestDoc(IN Mail VARCHAR(40))
BEGIN
    SELECT * 
    FROM TEST
    WHERE MailDocente = Mail;
END$

DELIMITER $  /* 3) Visualizzazione dei quesiti presenti all’interno di ciascun test. */
CREATE PROCEDURE VisualizzazioneQuesiti(IN Titolo VARCHAR(30))
BEGIN
    SELECT *
    FROM QUESITO
    WHERE TitoloTest = Titolo;
END$

/*---------------------------------------------------------------------------------*/
/*OPERAZIONI che riguardano SOLO DOCENTI:*/
DELIMITER $  /* 1) Inserimento di una nuova tabella di esercizio, con relativi meta-dati. */
CREATE PROCEDURE InserimentoTabellaEsercizio (
    IN NomeTabella VARCHAR(30),
    IN MailDocente VARCHAR(40))
BEGIN
    INSERT INTO TABELLA_ESERCIZIO(Nome, Creazione, NumeroRighe, MailDocente)
    VALUES (NomeTabella, now(), 0, MailDocente);
END $ DELIMITER ;

DELIMITER $  /* 2) Inserimento di una riga per una tabella di esercizio, definita dal docente. */


DELIMITER $  /* 2) Inserimento di una riga per una tabella di esercizio, definita dal docente. */
CREATE PROCEDURE InserimentoRigaTabellaEsercizio (
    IN NomeTabella VARCHAR(30),
    IN Docente VARCHAR(40))
BEGIN
    UPDATE TABELLA_ESERCIZIO
    SET NumeroRighe=NumeroRighe+1
    WHERE (Nome=NomeTabella) AND (MailDocente=Docente);
END $ DELIMITER ;

DELIMITER $  /* 3) Creazione di nuovo test. */
CREATE PROCEDURE CreaTest (
    IN TitoloTest VARCHAR(30),
    IN FotoTest VARCHAR(200),
    IN VisualizzaRisposteTest BOOLEAN,
    IN MailDocente VARCHAR(30)
)
BEGIN
    INSERT INTO TEST (Titolo, DataTest, Foto, VisualizzaRisposte, MailDocente)
    VALUES (TitoloTest, NOW(), FotoTest, VisualizzaRisposteTest, MailDocente);
END $ DELIMITER ;

DELIMITER $  /* 4) Creazione di un nuovo quesito con le relative risposte. */
CREATE PROCEDURE NewSketchCodice (
	IN ProgressivoCodice INT,
    IN TitoloTest VARCHAR(30),
    IN Difficolta VARCHAR(5),
    IN DescrizioneQuesito VARCHAR(40),
    IN Soluzione VARCHAR(300)
)
BEGIN
	INSERT INTO QUESITO (Progressivo, TitoloTest, Difficolta, Descrizione, NumRisposte)
    VALUES (ProgressivoCodice, TitoloTest, Difficolta, DescrizioneQuesito, 0);
    INSERT INTO SKETCH_CODICE (Progressivo, TitoloTest, Difficolta, Descrizione, NumRisposte, Soluzione)
    VALUES (ProgressivoCodice, TitoloTest, Difficolta, DescrizioneQuesito, 0, Soluzione);
END $ DELIMITER ;
DELIMITER $
CREATE PROCEDURE NewQuesitoChiuso (
	IN ProgressivoChiuso INT,
    IN TitoloTest VARCHAR(30),
    IN Difficolta VARCHAR(5),
    IN DescrizioneQuesito VARCHAR(40)
)
BEGIN
	INSERT INTO QUESITO (Progressivo, TitoloTest, Difficolta, Descrizione, NumRisposte)
    VALUES (ProgressivoChiuso, TitoloTest, Difficolta, DescrizioneQuesito, 0);
    INSERT INTO QUESITO_CHIUSO (Progressivo, TitoloTest, Difficolta, Descrizione, NumRisposte)
    VALUES (ProgressivoChiuso, TitoloTest, Difficolta, DescrizioneQuesito, 0);
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

/* 6) Inserimento di un messaggio (da parte del docente). */
DELIMITER $
CREATE PROCEDURE InserimentoMessaggioDocente (
	IN TitoloMessaggio VARCHAR(30),
    IN TestoMessaggio VARCHAR(100),    
    IN TitoloTest VARCHAR(30),
    IN MailDocente VARCHAR(40))
BEGIN
    -- Inserimento del messaggio nella tabella MESSAGGIO
    INSERT INTO MESSAGGIODOCENTE (TitoloMess, Testo, DataInserimento, TitoloTest,  MailDocente, MailStudente)
    VALUES (TitoloMessaggio, TestoMessaggio, NOW(), TitoloTest, MailDocente, NULL);

    INSERT INTO MESSAGGIODOCENTE (TitoloMess, Testo, DataInserimento, TitoloTest,  MailDocente)
    VALUES (TitoloMessaggio, TestoMessaggio, NOW(), TitoloTest, MailDocente);
END $ DELIMITER ;

/*---------------------------------------------------------------------------------*/
/*OPERAZIONI che riguardano SOLO STUDENTI*/
DELIMITER $  /* 1) Inserimento di una nuova risposta (ad un quesito di codice o un quesito chiuso). */
CREATE PROCEDURE InserimentoRisposta (IN ProgressivoQuesito INT, IN TitoloTest VARCHAR(30), IN MailStudente VARCHAR(40), IN Esito TINYINT, IN Testo VARCHAR(300))
BEGIN
 INSERT INTO risposta(ProgressivoQuesito, TitoloTest, MailStudente, Esito, Testo)
    VALUES (ProgressivoQuesito, TitoloTest, MailStudente, Esito, Testo);
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

/* 3) Inserimento di un messaggio (da parte dello studente).*/
DELIMITER $
CREATE PROCEDURE InserimentoMessaggioStudente (
    IN TitoloMessaggio VARCHAR(30),
    IN TestoMessaggio VARCHAR(100),    
    IN TitoloTest VARCHAR(30),
    IN MailStudente VARCHAR(40),
    IN MailDocente VARCHAR(40))
BEGIN
    -- Inserimento del messaggio nella tabella MESSAGGIO
    INSERT INTO MESSAGGIOSTUDENTE (TitoloMess, Testo, DataInserimento, TitoloTest,  MailDocente, MailStudente)
    VALUES (TitoloMessaggio, TestoMessaggio, NOW(), TitoloTest, MailDocente, MailStudente);

    INSERT INTO MESSAGGIOSTUDENTE (TitoloMess, Testo, DataInserimento, TitoloTest, MailStudente,  MailDocente)
    VALUES (TitoloMessaggio, TestoMessaggio, NOW(), TitoloTest, MailStudente, MailDocente);

END $ DELIMITER ;

/*procedure nuove (non richieste da traccia)*/
-- aggiornamento della risposta se lo studente la cambia
DELIMITER $
CREATE PROCEDURE AggiornaRisposta(IN NuovoTesto VARCHAR(300), IN NuovoEsito TINYINT, IN Test VARCHAR(30), IN Mail VARCHAR(40), IN NuovoProgressivo INT)
BEGIN
	UPDATE risposta SET Testo=NuovoTesto, Esito=NuovoEsito WHERE MailStudente=mail AND TitoloTest=Test AND ProgressivoQuesito=NuovoProgressivo;
END$
DELIMITER ;

-- visualizzazione della tabella fisica dento il test per lo studente
DELIMITER $
CREATE PROCEDURE VisualizzaTabella (IN Test VARCHAR(30))
BEGIN
	SELECT DISTINCT r.NomeTabella, a.Nome AS NomeAttributo 
	FROM rif_tabella_quesito as r, attributo as a
	WHERE r.NomeTabella = a.NomeTabella AND r.TitoloTest=Test;
END$
DELIMITER ;

-- inserimento dell'attributo nelle tabelle esercizio fisiche
DELIMITER $
CREATE PROCEDURE InserimentoAttributo (IN Tabella VARCHAR(30), IN NomeAT VARCHAR(30), IN Tipo VARCHAR(30), IN PossibileChiavePrimaria TINYINT)
BEGIN
INSERT INTO Attributo (NomeTabella, Nome, Tipo, PossibileChiavePrimaria) VALUES (Tabella, NomeAT, Tipo, PossibileChiavePrimaria);
END $ DELIMITER ;

-- visualizzare gli attributi delle tabelle create dal docente
DELIMITER $
CREATE PROCEDURE VisualizzaAttributi (IN Tabella VARCHAR(30))
BEGIN
SELECT Nome, Tipo, PossibileChiavePrimaria
                                FROM attributo
                                WHERE NomeTabella=Tabella;
END $ DELIMITER ;