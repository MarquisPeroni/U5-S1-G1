<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tabella_utenti";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// estrazione dati tabella utenti
$sql = "SELECT * FROM utenti";
$result = $conn->query($sql);

// creazione file csv con campi delimitati
$csvFile1 = fopen("utenti_delimitati.csv", "w");
if ($csvFile1) {
    while($row = $result->fetch_assoc()) {
        fputcsv($csvFile1, $row, "\t");
    }
    fclose($csvFile1);
    echo "File CSV con campi delimitati creato con successo.";
} else {
    echo "Impossibile aprire il file CSV per la scrittura.";
}

// Resetta il puntatore del risultato per consentire un'altra iterazione
$result->data_seek(0);

// creazione file csv senza delimitatori
$csvFile2 = fopen("utenti_nondelimitati.csv", "w");
if ($csvFile2) {
    while($row = $result->fetch_assoc()) {
        //dati senza delimitatori
        fputcsv($csvFile2, array_values($row));
    }
    fclose($csvFile2);
    echo "File CSV senza delimitatori creato con successo.";
} else {
    echo "Impossibile aprire il file CSV per la scrittura.";
}

//importazione dei dati dal CSV al database
$csvFile1 = fopen("utenti_delimitati.csv", "r");

if ($csvFile1 !== FALSE) {
    //ignora l'intestazione del file CSV (se presente)
    fgetcsv($csvFile1);

    //inserisci i dati nel database
    while (($data = fgetcsv($csvFile1)) !== FALSE) {
        // Rimuovi l'elemento dell'array relativo all'id
        array_shift($data);
    
        //prepara la query di inserimento
        $sql = "INSERT INTO utenti (nome, cognome, email, data_di_nascita, indirizzo, città, stato, CAP, data_creazione) 
                VALUES ('" . implode("', '", $data) . "', NOW())";
    
        //esegui la query di inserimento
        if ($conn->query($sql) === TRUE) {
            echo "Record inserito con successo nel database.<br>";
        } else {
            echo "Errore durante l'inserimento del record nel database: " . $conn->error . "<br>";
        }
    }
}

// Chiudi la connessione al database
$conn->close();

?>


