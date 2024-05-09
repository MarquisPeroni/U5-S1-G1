<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tabella_utenti";

// Connessione al database
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
} else {
    echo "Connessione al database stabilita con successo.<br>";
}

// Estrazione dei dati dalla tabella utenti

// i dati che vanno da riga 21 fino a riga 50 "non servono" perchè il file csv non è necessario crearlo, ma io l'ho fatto lo stesso perchè sono un pò sciocchino.
$sql = "SELECT * FROM utenti";
$result = $conn->query($sql);

// Creazione del file CSV con campi delimitati
$csvFile1 = fopen("utenti_delimitati.csv", "w");
if ($csvFile1) {
    while($row = $result->fetch_assoc()) {
        fputcsv($csvFile1, $row, "\t");
    }
    fclose($csvFile1);
    echo "File CSV con campi delimitati creato con successo.<br>";
} else {
    echo "Impossibile aprire il file CSV per la scrittura.<br>";
}

// Resetta il puntatore del risultato per consentire un'altra iterazione
$result->data_seek(0);

// Creazione del file CSV senza delimitatori
$csvFile2 = fopen("utenti_nondelimitati.csv", "w");
if ($csvFile2) {
    while($row = $result->fetch_assoc()) {
        // Dati senza delimitatori
        fputcsv($csvFile2, array_values($row));
    }
    fclose($csvFile2);
    echo "File CSV senza delimitatori creato con successo.<br>";
} else {
    echo "Impossibile aprire il file CSV per la scrittura.<br>";
}

// Importazione dei dati dal CSV al database
$csvFile1 = fopen("utenti_delimitati.csv", "r");

if ($csvFile1 !== FALSE) {
    // Ignora l'intestazione del file CSV (se presente)
    // fgetcsv($csvFile1);

    // Inserisci i dati nel database
    while ($data = fgetcsv($csvFile1,null,"\t")) {
        // Rimuovi l'elemento dell'array relativo all'id
        array_shift($data);
        array_pop($data);

        // Prepara la query di inserimento
        $sql = "INSERT INTO utenti (nome, cognome, email, data_di_nascita, indirizzo, città, stato, CAP, data_creazione) 
                VALUES ('" . implode("', '", $data) . "', NOW())";
                echo $sql;

        // Esegui la query di inserimento
        if ($conn->query($sql) === TRUE) {
            echo "Record inserito con successo nel database.<br>";
        } else {
            echo "Errore durante l'inserimento del record nel database: " . $conn->error . "<br>";
        }
    }
} else {
    echo "Impossibile aprire il file CSV per la lettura.<br>";
}

// Chiudi la connessione al database
$conn->close();

?>

