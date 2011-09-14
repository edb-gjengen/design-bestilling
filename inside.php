<?php
include 'php/db.php';

if( isset($_POST['email']) && is_numeric($_GET['view']) )
{
	$email = mysql_real_escape_string($_POST['email']);
	
	mysql_query("INSERT INTO `kak_user`(`bestilling_id`,`email`)  VALUES ('{$_GET['view']}', '$email')");
	header('Location: ?');
	exit;
}

include 'php/header.php';

if(isset($_GET['view']) && is_numeric($_GET['view']))
{
	//denne vises om brukeren har valgt å se nærmere på en bestilling
	$res = mysql_query("SELECT best.*, DATEDIFF(best.frist,date(NOW())) as `togo`, reg.email FROM `bestilling` as best LEFT JOIN `kak_user` as reg 
					ON best.id = reg.bestilling_id WHERE best.id='{$_GET['view']}'");
	$row = mysql_fetch_assoc($res);
	
	$columns = array('Hvem' => 'hvem',
				'Bestilt' => 'posted',
				'Frist' => 'frist',
				'Dager igjen:' => 'togo',
				'Kontaktperson' => 'kontakt_navn',
				'Epost' => 'kontakt_email',
				'Telefon'=>'kontakt_nummer');
				
	$alt = true;
	echo '<h1>Bestilling fra ' . $row['hvem'] .'</h1>';
	echo '<table border="1">';
	foreach($columns as $title=>$value)
	{
		echo '<tr';
		if($alt = !$alt) echo ' class="alt"';
		echo '><th>' . $title . '</th><td>' . $row[$value] . '</td></tr>';
	}
	echo '<tr><th>Tildelt</th>';
	if(!$row['email'])
	{
		echo '<td><form method="post">Epost:<input type="text" name="email" /><br /><input type="submit" value="Ta oppgave!" /></form></td>';
		
	}
	else
	{
		echo '<td>' . $row['email'] . '</td>';
	}
	echo '</tr>';
	echo '</table>';
	echo '<a href="?">(lukk)</a>';
	echo '<p style="padding:5px;border:1px solid gray;">' . str_replace(    "\n","\n<br />",$row['innhold']) . '</p>';
	echo '<table>';
	echo '<tr><td>Format:</td><td>' . $row['format'] . '</td></tr>';
	echo '<tr><td>Ark størrelse:</td><td>' . $row['arkstorrelse'] . '</td></tr>';
	echo '<tr><td>Farger:</td><td>' . $row['farge'] . '</td></tr>';
	echo '<tr><td>Marger:</td><td>' . $row['marger'] . '</td></tr>';
	echo '</table>';
		
}


$columns = array('Hvem' => 'hvem',
				'Bestilt' => 'posted',
				'Frist' => 'frist',
				'Kontaktperson' => 'kontakt_navn',
				'Epost' => 'kontakt_email',
				'Tildelt' => 'email');
$alt = true;


$res = mysql_query("SELECT best.*, DATE(best.posted) as `posted`, reg.email, DATEDIFF(best.frist,date(NOW())) as `togo`
					FROM `bestilling` as best LEFT JOIN `kak_user` as reg 
					ON best.id = reg.bestilling_id  WHERE reg.registerd IS NULL OR reg.registerd >= date(NOW() - INTERVAL 7 DAY) OR best.frist >= date(NOW() - INTERVAL 7 DAY)");

$columns = array('Hvem' => 'hvem',
				'Bestilt' => 'posted',
				'Frist' => 'frist',
				'Dager igjen' => 'togo',
				'Kontaktperson' => 'kontakt_navn',
				'Epost' => 'kontakt_email',
				'Tildelt' => 'email');
$alt = true;

if(!$res)
	die(mysql_error());
?>




<h1>Liste med bestillinger</h1>
<table>
<tr>
<?php
foreach($columns as $title=>$column)
	echo '<th>' . $title . '</th>';?>
</tr>
<?php while($row = mysql_fetch_assoc($res)): 
	$taken = $row['email']?true:false;
	if(!$row['email']) $row['email'] = 'Nei';
	$class = array();
	if($alt = !$alt)
		$class[] = 'alt';
	if(isset($_GET['color']))
	{
		$togo = $row['togo'];
		if(intval($togo) < 4)
			$class[] = ' critical';
		else if(intval($togo) < 7)
			$class[] = 'warning';
	}
	$class = implode(' ', $class);
?>
	<tr<?= $class?' class="' . $class . '"':'' ?>>
	<?php
	foreach($columns as $column)
		echo '<td><a href="?view='.$row['id'].'">'.$row[$column].'</a></td>';
	?>
	</tr>
<?php endwhile; ?>
</table>

<?php
include 'php/footer.php';
