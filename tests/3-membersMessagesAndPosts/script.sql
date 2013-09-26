CREATE TABLE member (
	idmember            INT AUTO_INCREMENT NOT NULL,
	login               VARCHAR(20) NOT NULL,
	isadmin             TINYINT(1) DEFAULT FALSE NOT NULL,
	password            VARCHAR(40) NOT NULL,
	PRIMARY KEY (idmember),
	UNIQUE (login))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE message (
	idmessage           INT AUTO_INCREMENT NOT NULL,
	sender_idmember     INT NOT NULL,
	recipient_idmember  INT NOT NULL,
	title               VARCHAR(20) NOT NULL,
	date                DATETIME NOT NULL,
	content             VARCHAR(250) NOT NULL,
	PRIMARY KEY (idmessage),
	FOREIGN KEY (sender_idmember) REFERENCES member (idmember),
	FOREIGN KEY (recipient_idmember) REFERENCES member (idmember))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE post (
	idpost              INT AUTO_INCREMENT NOT NULL,
	fk_idmember         INT NOT NULL,
	title               VARCHAR(20) NOT NULL,
	date                DATETIME NOT NULL,
	content             VARCHAR(250) NOT NULL,
	PRIMARY KEY (idpost),
	FOREIGN KEY (fk_idmember) REFERENCES member (idmember))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE question (
	parent_idpost       INT NOT NULL,
	PRIMARY KEY (parent_idpost),
	FOREIGN KEY (parent_idpost) REFERENCES post (idpost))
ENGINE = MYISAM CHARACTER SET UTF8;

CREATE TABLE answer (
	parent_idpost       INT NOT NULL,
	fk_idpost           INT NOT NULL,
	PRIMARY KEY (parent_idpost),
	FOREIGN KEY (parent_idpost) REFERENCES post (idpost),
	FOREIGN KEY (fk_idpost) REFERENCES question (parent_idpost))
ENGINE = MYISAM CHARACTER SET UTF8;
