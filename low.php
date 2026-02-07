<?php

if( isset( $_REQUEST[ 'Submit' ] ) ) {
	// Get input
	$id = $_REQUEST[ 'id' ];

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// Use prepared statement to prevent SQL injection
			// This separates SQL logic from user data, preventing malicious input from altering the query structure
			$stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ?");
			if ($stmt) {
				// Bind the user input as an integer parameter to the prepared statement
				mysqli_stmt_bind_param($stmt, "i", $id);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				
				// Get results
				while( $row = mysqli_fetch_assoc( $result ) ) {
					// Get values
					$first = $row["first_name"];
					$last  = $row["last_name"];

					// Feedback for end user
					$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
				}
				
				mysqli_stmt_close($stmt);
			} else {
				die('<pre>Error preparing statement: ' . mysqli_error($GLOBALS["___mysqli_ston"]) . '</pre>');
			}

			mysqli_close($GLOBALS["___mysqli_ston"]);
			break;
		case SQLITE:
			global $sqlite_db_connection;

			#$sqlite_db_connection = new SQLite3($_DVWA['SQLITE_DB']);
			#$sqlite_db_connection->enableExceptions(true);

			// Use prepared statement to prevent SQL injection
			// This separates SQL logic from user data, preventing malicious input from altering the query structure
			$stmt = $sqlite_db_connection->prepare("SELECT first_name, last_name FROM users WHERE user_id = :id");
			if ($stmt) {
				// Bind the user input as an integer parameter to the prepared statement
				$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
				$results = $stmt->execute();
				
				if ($results) {
					// Use SQLITE3_ASSOC to fetch only associative array (matching original behavior)
					while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
						// Get values
						$first = $row["first_name"];
						$last  = $row["last_name"];

						// Feedback for end user
						$html .= "<pre>ID: {$id}<br />First name: {$first}<br />Surname: {$last}</pre>";
					}
				}
			} else {
				echo "Error preparing statement: " . $sqlite_db_connection->lastErrorMsg();
			}
			break;
	} 
}

?>