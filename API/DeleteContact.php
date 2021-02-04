<?php
    $inData = getRequestInfo();
	$contactId = $inData["id"];
	$userId = $inData["userId"];

	$servername = "104.131.122.53";
	$username = "root";
	$password = "cop4331g5mysql";
	$dbname = "COP4331";

    $conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		if (validateUser($contactId, $userId) != true)
		{
			returnWithError("User not authorized to delete contact");
		}
		else
		{
			$sql = "DELETE FROM Contacts WHERE id=?";
			$stmt = $conn->prepare($sql);
			$stmt->bind_param("i", $contactId);
			$stmt->execute();

			$result = $stmt->get_result();
			if( $result = $conn->query($sql) != TRUE )
			{
				returnWithError( $conn->error );
			}
			returnWithSuccess("Contact successfully deleted!");
			$conn->close();
		}
	}

	function validateUser($contactId, $userId)
    {
		$conn = new mysqli($servername, $username, $password, $dbname);
		if ($conn->connect_error) 
		{
			returnWithError( $conn->connect_error );
		}
		else
		{
			// create an sql query that retrieves the foreign key of this contact
			$sql = "SELECT userId FROM Contacts where id='" . $contactId . "'";
			if ($result = $conn->query($sql) != TRUE)
			{
				returnWithError( $conn->error );
			}
			else
			{
				if ($result->num_rows > 0)
				{
					$row = $result->fetch_assoc();
					$foreignKey = $row["userId"];
					// check if the foreignKey stored for this contact matches the current user's id
					if ($foreignKey === $userId)
					{
						$conn->close();
						return TRUE;
					}
				}
			}
		}
		$conn->close();
        return FALSE;
    }

    function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}

	function returnWithSuccess($message)
	{
		$retValue = '{"success":"' . $message . '"}';
		sendResultInfoAsJson( $retValue );
	}

?>