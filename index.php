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

// Importazione dei dati dal CSV al database
$csvFile1 = fopen("utenti_delimitati.csv", "r");

if ($csvFile1 !== FALSE) {
    // Ignora l'intestazione del file CSV (se presente)
    fgetcsv($csvFile1,null,",");

    // Inserisci i dati nel database solo se non esistono già
    while ($data = fgetcsv($csvFile1,null,",")) {
        // Verifica se i dati esistono già nel database
        $checkQuery = "SELECT * FROM utenti WHERE nome = '" . $data[0] . "' AND cognome = '" . $data[1] . "'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult->num_rows == 0) {
            // Prepara la query di inserimento solo se i dati non esistono già
            $sql = "INSERT INTO utenti (nome, cognome, data_creazione) 
                    VALUES ('" . $data[0] . "', '" . $data[1] . "', NOW())";
    
            // Esegui la query di inserimento
            if ($conn->query($sql) === TRUE) {
                echo "Record inserito con successo nel database.<br>";
            } else {
                echo "Errore durante l'inserimento del record nel database: " . $conn->error . "<br>";
            }
        } else {
            echo "I dati esistono già nel database.<br>";
        }
    }
} else {
    echo "Impossibile aprire il file CSV per la lettura.<br>";
}

// Chiudi la connessione al database
$conn->close();

?>


