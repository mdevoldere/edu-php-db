DROP TABLE IF EXISTS people;

CREATE TABLE IF NOT EXISTS people (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	lastname VARCHAR(20) NOT NULL,
	firstname VARCHAR(20)
);

INSERT INTO people (lastname, firstname) VALUES 
('DEV', 'Mike'),
('DUPONT', 'Cindy'),
('DOE', 'John');
