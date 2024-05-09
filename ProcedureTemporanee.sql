use moodle;
DELIMITER $
CREATE PROCEDURE VisualizzaAttributi (IN Tabella VARCHAR(30))
BEGIN
SELECT Nome, Tipo, PossibileChiavePrimaria
                                FROM attributo
                                WHERE NomeTabella=Tabella;
END $ DELIMITER ;