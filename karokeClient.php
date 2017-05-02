<html>
<head>
    <title>
    Karoke 2017: Client Version
    </title>
    <style>
    table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
    }
    td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
    }
    #t01 {
        background-color: cadetblue;
        color: white;
    }
     #t02 {
        background-color: crimson;
        color: white;
    }
    tr:hover {background-color: #f5f5f5;}
   .button {
    background-color: #e7e7e7;
    color: black;
    padding: 5px;
    border: none;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}
    </style>
</head>
<body>
<?php
        $username = "z1754749"; // object to hold username
        $password = "1995Nov28"; // object to hold password
        try { // if something goes wrong, an exeption is thrown
                $dsn = "mysql:host=courses;dbname=z1754749";      // creating dsn string
                $pdo = new PDO($dsn, $username, $password);       // constructing an instance of the PDO class
        }
        catch(PDOexception $e) { // handle the exeption
                echo "Connection to database failed: " . $e->getMessage();
        }
?>

<table>
	<tr>
		<th> <a href="?sort=<?php echo isset($_GET['sort'])?!$_GET['sort']:1; ?>&field=songID">SongID:</a> </th>
		<th> <a href="?sort=<?php echo isset($_GET['sort'])?!$_GET['sort']:1; ?>&field=song">Song:</a> </th>
		<th> <a href="?sort=<?php echo isset($_GET['sort'])?!$_GET['sort']:1; ?>&field=timeLength">Length:</a> </th>
		<th> <a href="?sort=<?php echo isset($_GET['sort'])?!$_GET['sort']:1; ?>&field=artistName">Artist:</a> </th>
		<th> <a href="?sort=<?php echo isset($_GET['sort'])?!$_GET['sort']:1; ?>&field=album">Album:</a> </th>
	</tr>

<?php
	$field = "songID";

	$isAsc = isset($_GET['sort'])? (bool) $_GET['sort']: 1;

	if ($isAsc)
	{
		$order = "ASC";
	}
	else
	{
		$order = "DESC";
	}

	if ($_GET['field'] === "songID")
	{
		$field = "song.songID";
	}
	else if ($_GET['field'] === "song")
	{
		$field = "song.songName";
	}
	else if ($_GET['field'] === "timeLength")
	{
		$field = "timeLength";
	}
	else if ($_GET['field'] === "artistName")
	{
		$field = "artistName";
	}
	else if ($_GET['field'] === "album")
	{
		$field = "album";
	}
	if (!isset($_POST['formSearch']))
{
	$sql = "SELECT song.songID, songName, timeLength, album, artistName FROM song, artist, plays WHERE artist.artistID = plays.artistID AND song.songID = plays.songID ORDER BY $field $order;";
	$statement = $pdo->query($sql);
	while ($row = $statement->fetch(PDO::FETCH_ASSOC))
	{
		$songID = $row['songID'];
		$songName = $row['songName'];
		$timeLength = $row['timeLength'];
		$album = $row['album'];
		$artistName = $row['artistName'];
		echo "<tr>
			<td>$songID</td>
			<td>$songName</td>
			<td>$timeLength</td>
			<td>$artistName</td>
			<td>$album</td>
		      </tr>";
	}
	echo "</table>";
}
	if (isset($_POST['formSearch']))
	{
		$search = $_POST['search'];
		$value = $_POST['value'];
                $value = '%'.$value.'%';
		$query = "SELECT song.songID, songName, timeLength, album, artistName FROM song, artist, plays, contributor, performs_in WHERE artist.artistID = plays.artistID AND song.songID = plays.songID AND song.songID = performs_in.songID AND contributor.contributerID = performs_in.contributorID";
		// artist
		if ($search === "artist")
		{
			$query .= " AND artist.artistName LIKE ?;";
			$statement = $pdo->prepare($query);
			$statement->execute(array($value));
		}
		// title
		if ($search === "title")
		{
			$query .= " AND song.songName LIKE ?;";
			$statement = $pdo->prepare($query);
			$statement->execute(array($value));
		}
		// DOESNT WORK
		// contributor
		if ($search === "contributor")
		{
			$names = explode(' ', $value);
			$names[0] = $names[0] . '%';
			$names[1] = '%' . $names[1];
			$query .= " AND contributor.firstname LIKE ? AND contributor.lastname LIKE ?;";
			$statement = $pdo->prepare($query);
			$statement->execute(array($names[0], $names[1]));
		}
		while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        	{
			$songID = $row['songID'];
                	$songName = $row['songName'];
                	$timeLength = $row['timeLength'];
                	$album = $row['album'];
                	$artistName = $row['artistName'];
                	echo "<tr>
				<td>$songID</td>
                        	<td>$songName</td>
                        	<td>$timeLength</td>
                        	<td>$artistName</td>
                        	<td>$album</td>
                      	</tr>";
        	}
		echo "</table>";
	}
?>

<form action = "" method = "POST">
	<p>Search for a song: <br/>
		<select name = "search">
			<option value = "artist">Artist</option>
			<option value = "title">Title</option>
			<option value = "contributor">Contributor</option>
		</select>

		<input type = "text" name = "value" />
		<input type = "submit" name = "formSearch" value = "Search" />
	</p>

	<p>To select a song, enter user ID and songID, and select a payment option: <br/>
		UserID:    <input type = "text" name = "selectUser" /> <br/>
		SongID: <input type = "text" name = "selectSong" /> <br/>
		Payment:   <select name = "payment">
				<option value = "0">Free</option>
				<option value = "1">$1.00</option>
				<option value = "2">$2.00</option>
				<option value = "3">$3.00</option>
				<option value = "4">$4.00</option>
			  </select>

		<?php
			if ($_POST['selectUser'] != '' && $_POST['selectSong'] != '')
			{
				$selectUser = $_POST['selectUser'];
				$selectSong = $_POST['selectSong'];
				$payment = $_POST['payment'];
				$newQueue = $pdo->prepare("INSERT INTO queue (karokeID, userID, tier, dateQueued, timeQueued) VALUES (?, ?, ?, NOW(), NOW())");
				$newQueue->execute(array($selectSong, $selectUser, $payment));
			}
		?>
	</p>

	<p>
		<input type = "submit" name = "formSubmit" value = "Submit" />
	</p>
</form>
</body>
</html>
