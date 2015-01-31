CREATE TABLE a_issue2 (
	ida_issue2     INT AUTO_INCREMENT NOT NULL,
	PRIMARY KEY (ida_issue2))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE b_issue2 (
	fk_ida_issue2  INT NOT NULL,
	PRIMARY KEY (fk_ida_issue2),
	FOREIGN KEY (fk_ida_issue2) REFERENCES a_issue2 (ida_issue2))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE c_issue2 (
	idc_issue2     INT AUTO_INCREMENT NOT NULL,
	PRIMARY KEY (idc_issue2))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE d_issue2 (
	idd_issue2     INT AUTO_INCREMENT NOT NULL,
	PRIMARY KEY (idd_issue2))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE e_issue2 (
	fk_idc_issue2  INT NOT NULL,
	fk_idd_issue2  INT NOT NULL,
	PRIMARY KEY (fk_idc_issue2,fk_idd_issue2),
	FOREIGN KEY (fk_idc_issue2) REFERENCES c_issue2 (idc_issue2),
	FOREIGN KEY (fk_idd_issue2) REFERENCES d_issue2 (idd_issue2))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE f_issue2 (
	idf_issue2     INT AUTO_INCREMENT NOT NULL,
	fk_ida_issue2  INT NOT NULL,
	fk_idc_issue2  INT NOT NULL,
	fk_idd_issue2  INT NOT NULL,
	PRIMARY KEY (idf_issue2),
	FOREIGN KEY (fk_ida_issue2) REFERENCES b_issue2 (fk_ida_issue2),
	FOREIGN KEY (fk_idc_issue2) REFERENCES e_issue2 (fk_idc_issue2),
	FOREIGN KEY (fk_idd_issue2) REFERENCES e_issue2 (fk_idd_issue2))
ENGINE = MYISAM CHARACTER SET UTF8;
