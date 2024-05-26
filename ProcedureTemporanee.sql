DROP DATABASE IF EXISTS prova;
CREATE DATABASE prova;
USE prova;
CREATE TABLE a(
	b INT,
    c VARCHAR(30), /* Rappresenta l'oggetto della mail ad esempio.*/
    
    PRIMARY KEY (b, c)
 ) engine=INNODB; 
 create table x(
	y INT,
    z VARCHAR(30), /* Rappresenta l'oggetto della mail ad esempio.*/
    
    PRIMARY KEY (y, z)
 ) engine=INNODB; 


DELIMITER $
CREATE PROCEDURE Vincoli ( IN tabella2 VARCHAR(30),IN tabella1 VARCHAR(30), IN chiaviprimarie VARCHAR(500),IN chiaviesterne VARCHAR(500))
BEGIN
	
    SET @sql = CONCAT('ALTER TABLE ', tabella2, ' ADD FOREIGN KEY (', chiaviesterne, ') REFERENCES ', tabella1, '(', chiaviprimarie, ') ON DELETE CASCADE');
    
    select @sql;
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END $ 
DELIMITER ;
CALL Vincoli('a', 'x', 'z', 'c');