DROP DATABASE IF EXISTS moodle;
CREATE DATABASE moodle;
USE moodle;
 
 /* Tabella del Docente */
 create table DOCENTE(
 /* Attributi comuni che hanno entrambi gli utenti */
	Mail VARCHAR(40) PRIMARY KEY,
    Nome VARCHAR(40),
    Cognome VARCHAR(40) ,
    Telefono BIGINT,
    
/* Attributi speciali di cui solo il Docente dispone*/
    Corso VARCHAR(30),
    Dipartimento VARCHAR(60)
 ) engine=INNODB;
 
/* Tabella dello Studente */
 create table STUDENTE(
/* Attributi comuni che hanno entrambi gli utenti */
	Mail VARCHAR(40) PRIMARY KEY,
    Nome VARCHAR(40),
    Cognome VARCHAR(40) ,
    Telefono BIGINT, /*Il recapito telefonico è eventuale */
    
/* Attributi speciali di cui solo lo Studente dispone*/
    AnnoImmatricolazione BIGINT,
    /* Attenzione deve essere un codice alfanumerico di lunghezza pari a 16 caratteri */ 
    CodiceMatricola VARCHAR(16),
	CONSTRAINT FORMATO_Codice CHECK( LENGTH(CodiceMatricola) = 16)
 ) engine=INNODB;
 
/* Tabella del Test*/
 create table TEST(
	Titolo VARCHAR(30) PRIMARY KEY,
    DataTest datetime,
    Foto VARCHAR(200),  /*la foto è eventuale */
    VisualizzaRisposte tinyint, /* se true gli studenti possono vederle, altrimenti se settato a false non possono.*/
	MailDocente VARCHAR(40),
     
	FOREIGN KEY (MailDocente) REFERENCES DOCENTE(Mail) ON DELETE CASCADE
 ) engine=INNODB;
 create table SVOLGIMENTO(
	MailStudente VARCHAR(40),
    TitoloTest VARCHAR(30),
    DataInizio datetime,
    DataFine datetime,
    Stato ENUM('Aperto','InCompletamento','Concluso') DEFAULT 'Aperto',
    
    PRIMARY KEY (MailStudente, TitoloTest),
    FOREIGN KEY (MailStudente) REFERENCES STUDENTE(Mail) ON DELETE CASCADE,
    FOREIGN KEY (TitoloTest) REFERENCES TEST(Titolo) ON DELETE CASCADE
 ) engine=INNODB;
 
 create table QUESITO(
	Progressivo INT,
    TitoloTest VARCHAR(30),
    Difficolta ENUM ('Basso','Medio','Alto'),
    Descrizione VARCHAR(40),
    NumRisposte INT,
    
	PRIMARY KEY (Progressivo, TitoloTest), 
    FOREIGN KEY (TitoloTest) REFERENCES TEST(Titolo) ON DELETE CASCADE
 ) engine=INNODB; 
  create table SKETCH_CODICE(  /* Ha senso differenziarli perché derivano da una generalizzazione Totale di quesito. */
	Progressivo INT,
    TitoloTest VARCHAR(30),
    Difficolta ENUM ('Basso','Medio','Alto'),
    Descrizione VARCHAR(40),
    NumRisposte INT,
    
    Soluzione VARCHAR(300),  /*La soluzione è la query come richiesta dal docente*/
    
    PRIMARY KEY (Progressivo, TitoloTest),
	FOREIGN KEY (Progressivo) REFERENCES QUESITO(Progressivo) ON DELETE CASCADE,
    FOREIGN KEY (TitoloTest) REFERENCES QUESITO(TitoloTest) ON DELETE CASCADE
 ) engine=INNODB;
create table QUESITO_CHIUSO(
	Progressivo INT,
    TitoloTest VARCHAR(30),
    Difficolta ENUM ('Basso','Medio','Alto'),
    Descrizione VARCHAR(40),
    NumRisposte INT,
    
    OpzioneGiusta VARCHAR(1), /*L'opzione giusta è l'opzione segnata come esatta dal docente: a, b, c...*/
    
	PRIMARY KEY (Progressivo, TitoloTest),
	FOREIGN KEY (Progressivo) REFERENCES QUESITO(Progressivo) ON DELETE CASCADE,
    FOREIGN KEY (TitoloTest) REFERENCES QUESITO(TitoloTest) ON DELETE CASCADE
 ) engine=INNODB;
create table OPZIONE(
	Numerazione INT,
	ProgressivoChiuso INT,
    TitoloTest VARCHAR(30),
    
    Testo VARCHAR(40), /* Testo della singola opzione. */
    
    PRIMARY KEY (Numerazione, ProgressivoChiuso, TitoloTest),
    FOREIGN KEY (ProgressivoChiuso) REFERENCES QUESITO_CHIUSO(Progressivo) ON DELETE CASCADE,
    FOREIGN KEY (TitoloTest) REFERENCES QUESITO_CHIUSO(TitoloTest) ON DELETE CASCADE
) engine=INNODB;
 create table RISPOSTA(
    Progressivo INT,
    TitoloTest VARCHAR(30),
    MailStudente VARCHAR(40),
    
    Esito boolean, /* Campi di Risposta. */
    Testo VARCHAR(60),
    
    PRIMARY KEY(Progressivo, TitoloTest, MailStudente),
    FOREIGN KEY (Progressivo) REFERENCES QUESITO(Progressivo) ON DELETE CASCADE,
	FOREIGN KEY (TitoloTest) REFERENCES QUESITO(TitoloTest) ON DELETE CASCADE,
    FOREIGN KEY (MailStudente) REFERENCES STUDENTE(Mail) ON DELETE CASCADE
 ) engine=INNODB;
 
 /* La tabella dell'esercizio si riferisce: 
	Al DOCENTE che la crea (una Tabella di Esercizio può essere creata soltanto da un docente). */
 create table TABELLA_ESERCIZIO(
	Nome VARCHAR(30) PRIMARY KEY,
    Creazione DATE,
    NumeroRighe INT,
    MailDocente VARCHAR(40),
    
    FOREIGN KEY (MailDocente) REFERENCES DOCENTE(Mail) ON DELETE CASCADE
) engine=INNODB;
create table RIF_TABELLA_QUESITO(
	ProgressivoQuesito INT,
    TitoloTest VARCHAR(30),
    NomeTabella VARCHAR(30),
    
    FOREIGN KEY (ProgressivoQuesito, TitoloTest) REFERENCES QUESITO(Progressivo, TitoloTest) ON DELETE CASCADE,
    FOREIGN KEY (NomeTabella) REFERENCES TABELLA_ESERCIZIO(Nome) ON DELETE CASCADE
)engine=INNODB;

 create table ATTRIBUTO( /* Tabella relativa agli Attributi. */
    NomeTabella VARCHAR(30),
	Nome VARCHAR(30),
    Tipo VARCHAR(30),
    
	/* Ogni ATTRIBUTO può fare parte della Chiave Primaria della TABELLA_ESERCIZIO. */
    PossibileChiavePrimaria boolean NOT NULL,
    
    PRIMARY KEY (Nome, NomeTabella),
    FOREIGN KEY (NomeTabella) REFERENCES TABELLA_ESERCIZIO(Nome) ON DELETE CASCADE
) engine=INNODB;
create table VINCOLO(
	NomeAttributoPK VARCHAR(30),
    NomeTabellaPK VARCHAR(30),
    NomeAttributoFK VARCHAR(30),
    NomeTabellaFK VARCHAR(30),
    
    PRIMARY KEY (NomeAttributoPK, NomeTabellaPK),
	FOREIGN KEY (NomeAttributoPK) REFERENCES ATTRIBUTO(Nome) ON DELETE CASCADE,
    FOREIGN KEY (NomeTabellaPK) REFERENCES ATTRIBUTO(NomeTabella) ON DELETE CASCADE,
    FOREIGN KEY (NomeAttributoFK) REFERENCES ATTRIBUTO(Nome) ON DELETE CASCADE,
    FOREIGN KEY (NomeTabellaFK) REFERENCES ATTRIBUTO(NomeTabella) ON DELETE CASCADE
) engine=INNODB;
 
 
  create table MESSAGGIO(
	Id INT PRIMARY KEY,
    Titolo VARCHAR(30), /* Rappresenta l'oggetto della mail ad esempio.*/
    DataInserimento date,
    Testo VARCHAR(100),
	TitoloTest VARCHAR(30),
    Mittente VARCHAR(40),
    Destinatario VARCHAR(40),
    
    FOREIGN KEY (TitoloTest) REFERENCES TEST(Titolo) ON DELETE CASCADE
 ) engine=INNODB; 
