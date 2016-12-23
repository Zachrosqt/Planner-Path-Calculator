
CREATE TABLE Albero (

    AlberoID int PRIMARY KEY AUTO_INCREMENT,
    Nome varchar(50) NOT NULL UNIQUE,
    Split int NOT NULL,
    High int NOT NULL,
    usabile BOOLEAN NOT NULL,
    dataCreazione DATETIME NOT NULL

);

CREATE TABLE Nodes (

    AlberoID int NOT NULL,
    NodoID int NOT NULL,
    PadreID int,
    Livello int NOT NULL,
    UNIQUE (AlberoID, NodoID),
    Foreign Key (AlberoID) references Albero(AlberoID)
);

CREATE TABLE attrNodes (

    AlberoID int NOT NULL,
    NodoID int NOT NULL,
    nome varchar(50) NOT NULL,
    attr int NOT NULL,
    Foreign Key (AlberoID) references Nodes(AlberoID),
    Foreign Key (NodoID) references Nodes(NodoID)
    
);

CREATE TABLE attrEdges (

    AlberoID int NOT NULL,
    NodoID int NOT NULL,
    PadreID int NOT NULL,
    nome varchar(50) NOT NULL,
    attr int NOT NULL,
    Foreign Key (AlberoID) references Nodes(AlberoID),
    Foreign Key (NodoID) references Nodes(NodoID),
    Foreign Key (PadreID) references Nodes(PadreID)
    
);