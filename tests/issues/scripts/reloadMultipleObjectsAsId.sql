CREATE TABLE a_issue1 (
	ida_issue1     INT AUTO_INCREMENT NOT NULL,
	PRIMARY KEY (ida_issue1))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE b_issue1 (
	idb_issue1     INT AUTO_INCREMENT NOT NULL,
	PRIMARY KEY (idb_issue1))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE c_issue1 (
	fk_ida_issue1  INT NOT NULL,
	fk_idb_issue1  INT NOT NULL,
	PRIMARY KEY (fk_ida_issue1,fk_idb_issue1),
	FOREIGN KEY (fk_ida_issue1) REFERENCES a_issue1 (ida_issue1),
	FOREIGN KEY (fk_idb_issue1) REFERENCES b_issue1 (idb_issue1))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE d_issue1 (
	idd_issue1     INT AUTO_INCREMENT NOT NULL,
	e              VARCHAR(250) NOT NULL,
	fk_ida_issue1  INT NOT NULL,
	fk_idb_issue1  INT NOT NULL,
	PRIMARY KEY (idd_issue1),
	FOREIGN KEY (fk_ida_issue1) REFERENCES c_issue1 (fk_ida_issue1),
	FOREIGN KEY (fk_idb_issue1) REFERENCES c_issue1 (fk_idb_issue1))
ENGINE = MYISAM CHARACTER SET UTF8;
