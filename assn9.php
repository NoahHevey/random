<!--
Name:            Noah Hevey
Section:         CSCI 466 1
Instructor Name: Jonathan Lehuta
Semester:        Spring 2017
Due Date:        14th April 2017
-->

<html>

<head>
</head>

<body bgcolor = "efefed" text = "7a6945">
<p>Passengers: <br/>
<?php

        $username = "z1799146"; // object to hold username
        $password = "1996Feb20"; // object to hold password

        try { // if something goes wrong, an exeption is thrown
                $dsn = "mysql:host=courses;dbname=z1799146";      // creating dsn string
                $pdo = new PDO($dsn, $username, $password);       // constructing an instance of the PDO class
        }
        catch(PDOexception $e) { // handle the exeption
                echo "Connection to database failed: " . $e->getMessage();
        }

        $passengerName = $pdo->query("SELECT firstName, lastName FROM passenger ORDER BY lastName ASC;"); // query to list all passengers by full name in alphabetic order of last name
                                                                                                          // result stored as a PDOStatent object in the passengerName object
	while ($row = $passengerName->fetch(PDO::FETCH_ASSOC)) // while there is data to be read, return row by row as an associative array and store in the $row object
	{
		$firstName = $row['firstName'];                // store the firstName field of the row in $firstName object
		$lastName = $row['lastName'];                  // store the lastName field of the row in $lastName object 

		echo $firstName;                               // print first and last name objects
		echo ", ";
		echo $lastName;
		echo "<br/>";
	}
?>
</p>

<form action = "" method = "POST">                                         <!-- initialize form sending data to the current url with the POST method -->
	<p>To see passengers, select a flight number:
		<select name = "flightNum">
			<option selected value = ''></option>              <!-- default blank value -->
				<?php                                                                   // populates select field with flight numbers
					$flights = $pdo->query("SELECT flightnum FROM flight;");        // query to list flight numbers
					while ($row = $flights->fetch(PDO::FETCH_ASSOC))                // while there is data to be read, return row by row as an associative array
					{
						$flightNumber = $row['flightnum'];

						echo '<option value = "'.$flightNumber.'">'.$flightNumber.'</option>';     // for each row send an option value with the flight number
					}
				?>
			</select>
	</p>

	<p>
        <?php // handle selected flight number
	if ($_POST['flightNum'] != '') // if a number has been selected
	{
        $flightNumber = $_POST['flightNum'];        // retrieve selected flight number from submitted form
        $flightPass = $pdo->prepare("SELECT firstName, lastName FROM passenger WHERE passnum IN(SELECT passnum FROM manifest WHERE flightnum = ?);");  // prepare a query to be ran, query will select a list of passengers on a given flight, allows user to chose a flight number
        $flightPass->execute(array($flightNumber));   // execute query, passing in the user selected flightnumber

        while ($row = $flightPass->fetch(PDO::FETCH_ASSOC)) // while there is data to be read, return row by row
        {
                $firstName = $row['firstName'];
                $lastName = $row['lastName'];

                echo $firstName;                  // display first and last name by row
                echo ", ";
                echo $lastName;
                echo "<br/>";
        }
	}

	?>
	</p>

	<p>To see flight history, select a passenger:
		<select name = "passNum">
		<option selected value = ''></option> 
			<?php   // populates select with passenger last and first names
				$passengers = $pdo->query("SELECT passnum, firstName, lastName FROM passenger;");    // query to list passenger number, first and last name
				while ($row = $passengers->fetch(PDO::FETCH_ASSOC))
				{
					$firstName = $row['firstName'];
					$lastName = $row['lastName'];
					$passNum = $row['passnum'];

					echo '<option value = "'.$passNum.'">'.$lastName.', '.$firstName.'</option>';    // for each row send an option value with passengerNum as value and display passenger names
				}
			?>
		</select>
	</p>

	<p>
		<?php  // handle selected passenger num
			if ($_POST['passNum'] != '') // if a number is selected
			{

				$passengerNumber = $_POST['passNum'];        // retrieve selected passenger from submitted form
				$flightHistory = $pdo->prepare("SELECT flightnum FROM flight WHERE flightnum IN(SELECT flightnum FROM manifest WHERE passnum = ?);");  // prepare query, lists flight history of a user selected passenger
				$flightHistory->execute(array($passengerNumber));  // execute query passing in user selected passenger 

				echo "<table>
					<tr>
					  <th>Flight #</th>
					</tr>";  // create a table to hold flights

				while ($row = $flightHistory->fetch(PDO::FETCH_ASSOC))   // while there is data, return row by row
				{
					$flightNum = $row['flightnum'];

					echo "<tr>
						<td>$flightNum</td>
					      </tr>";  // create a new table row for each flight
				}
				echo "</table>";
			}
		?>
	</p>

	<p>To see additional information about a flight, enter a flight number:
		<input type = "text" name = "flightNumber" />
	</p>

	<p>
		<?php
			if ($_POST['flightNumber'] != '') // if a value is entered
			{
				$flightNumber = $_POST['flightNumber']; // retrieve entered value
				$flightInfo = $pdo->prepare("SELECT * FROM flight WHERE flightnum = ?;"); // prepare query, lists all data about a user selected flight
				$flightInfo->execute(array($flightNumber)); // execute query passing in user selected flight number

		 		echo "<table>
                   			<tr>
                      		  	  <th>Flight</th>
                      		  	  <th>Origination</th>
                      		  	  <th>Destination</th>
                      		  	  <th>Miles</th>
                   			</tr>";       // create a table to hold data


				while ($row = $flightInfo->fetch(PDO::FETCH_ASSOC))   // while there is data, return row by row
				{
					$flightNum = $row['flightnum'];
					$origination = $row['origination'];
					$destination = $row['destination'];
					$miles = $row['miles'];

					echo "<tr>
						<td>$flightNum</td>
						<td>$origination</td>
						<td>$destination</td>
						<td>$miles</td>
					      </tr>";      // create a new table entry for each row
				}
				echo "</table>";
			}
		?>
	</p>

	<p>
		<input type = "submit" name = "formSubmit" value = "Submit" />
		<input type = "reset" name = "formReset" value = "Reset" />
	</p>
</form>

<p align = "center">Noah Hevey Section: 1 </p>

</body>

</html>
