CREATE TABLE person (
	idperson     INT AUTO_INCREMENT NOT NULL,
	firstname    VARCHAR(250) NOT NULL,
	lastname     VARCHAR(250) NOT NULL,
	PRIMARY KEY (idperson))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE car (
	idcar        INT AUTO_INCREMENT NOT NULL,
	model        VARCHAR(250) NOT NULL,
	brand        VARCHAR(250) NOT NULL,
	PRIMARY KEY (idcar))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE car_person (
	fk_idcar     INT NOT NULL,
	fk_idperson  INT NOT NULL,
	PRIMARY KEY (fk_idcar,fk_idperson),
	FOREIGN KEY (fk_idcar) REFERENCES car (idcar),
	FOREIGN KEY (fk_idperson) REFERENCES person (idperson))
ENGINE = MYISAM CHARACTER SET UTF8;
