<!--
Name:            Noah Hevey
Section:         CSCI 466 1
Instructor Name: Jonathan Lehuta
Semester:        Spring 2017
Due Date:        21st April 2017
-->

<html>

<head>
</head>

<body bgcolor = "efefed" text = "7a6945">
<p>
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
?>
</p>

<form action = "" method = "POST">                                         <!-- initialize form sending data to the current url with the POST method -->

	<p>Create a new flight: <br/>                                      <!-- Allows a user to add a flight to the database -->
		Origination: <input type = "text" name = "newOrigination" />              <br/>
		Destination: <input type = "text" name = "newDestination" />              <br/>
		Miles:       <input type = "text" name = "newMiles" />                    <br/>
	</p>

	<p>
		<?php
			if ($_POST['newOrigination'] != '' && $_POST['newDestination'] != '' && $_POST['newMiles'] != '')   // if all data is entered
			{
				$origination = $_POST['newOrigination'];                                                    // retrieve data and store in variables
				$destination = $_POST['newDestination'];
				$miles = $_POST['newMiles'];

				$newFlight = $pdo->prepare("INSERT INTO flight (origination, destination, miles) VALUES(?, ?, ?)"); // query that inserts user entered data into the flight table, with an auto_incremented id
				$newFlight->execute(array($origination, $destination, $miles));

				echo "Flight Added";
			}
		?>
	</p>

	<p>Add a passenger:                                           <br/>   <!-- Allows a user to add a passenger to the database -->
		First Name: <input type = "text" name = "newFirst" /> <br/>
		Last Name:  <input type = "text" name = "newLast" />  <br/>
	</p>

	<p>
		<?php
			if ($_POST['newFirst'] != '' && $_POST['newLast'] != '')
			{
				$firstName = $_POST['newFirst'];
				$lastName = $_POST['newLast'];

				$newPassenger = $pdo->prepare("INSERT INTO passenger (firstName, lastName) VALUES(?, ?)"); // query that inserts user entered data into the passenger table with an auto_incremented id
				$newPassenger->execute(array($firstName, $lastName));

				echo "Passenger Added";
			}
		?>
	</p>

	<p>Book a flight: <br/>   <!-- Allows a user to book an existing passenger on an existing flight -->
			        Select a passenger:
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

	        		<br/>  Select a flight:
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

				<br/> Enter a Date:
					<input type = "text" name = "flightDate" /> <br/>
				Enter a Seat Number:
					<input type = "text" name = "seatNum" /> <br/>
	<p>
		<?php
			if ($_POST['passNum'] != '' && $_POST['flightNum'] != '' && $_POST['flightDate'] != '') // seatNum can be left blank
			{
				$passNum = $_POST['passNum'];
				$flightNum = $_POST['flightNum'];
				$flightDate = $_POST['flightDate'];
				$seatNum = $_POST['seatNum'];

				$newBooking = $pdo->prepare("INSERT INTO manifest (flightnum, flightDate, passnum, seatnum) VALUES(?, ?, ?, ?);"); // query that inserts user entered data into the manifest table
				$newBooking->execute(array($flightNum, $flightDate, $passNum, $seatNum));

				echo "Flight Booked";
			}
		?>
	</p>

	<p>Delete a flight: <br/>  <!-- Allows a user to remove a flight from the database -->
		Enter a flight number: <input type = "text" name = "deleteFlightNum" />
	</p>
	<p>
		<?php
			if ($_POST['deleteFlightNum'] != '')
			{
				$deleteFlightNum = $_POST['deleteFlightNum'];
				$deleteManifest = $pdo->prepare("DELETE FROM manifest WHERE flightnum = ?;");  // two querys that remove user entered flight number from both flight and manifest tables
				$deleteFlight = $pdo->prepare("DELETE FROM flight WHERE flightnum = ?;");
				$deleteManifest->execute(array($deleteFlightNum));
				$deleteFlight->execute(array($deleteFlightNum));

				echo "Flight Deleted";
			}
		?>
	</p>

	</p>

	<p>
		<input type = "submit" name = "formSubmit" value = "Submit" />
		<input type = "reset" name = "formReset" value = "Reset" />
	</p>
</form>

<p align = "center">Noah Hevey Section: 1 </p>

</body>

</html>
