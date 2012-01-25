<?php
$GLOBALS['errors'] = array();

function addError($data, $name = null)
{
	if($name)
		$GLOBALS['errors'][$name] = $data;
	else
		$GLOBALS['errors'][] = $data;
}

function ferror($name = false, $table = false)
{

	if( !$name )
	{
		$tmp = array_keys($GLOBALS['errors']);
		foreach($tmp as $key)
			ferror($name);
	}
	else
	{
		if(array_key_exists($name, $GLOBALS['errors']))
		{
			if($table) echo '<tr><td colspan="999">';
			echo '<div class="error">' . $GLOBALS['errors'][$name] . '</div>';
			if($table) echo '</td></tr>';
			unset($GLOBALS['errors'][$name]);
		}
	}
}

function radioList($name, $options, $other = True)
{
	echo '<ul>';
	foreach($options as $option)
		echo '<li><input name="'.$name.'" type="radio" value="'.$option.'"' . ($_POST[$name] == $option?' CHECKED':'') . '>'.$option.'</input></li>';
	if($other)
		echo '<li><input name="'.$name.'" type="radio" value="annet"' . ($_POST[$name] == 'annet'?' CHECKED':'') . '>Annet : <input name="'.$name.'_annet" value="'.@$_POST[$name.'_annet'].'" /></input></li>';
	echo '</ul>';
	global $errors;
	ferror($name);
}

function hasErrors()
{
	return count($GLOBALS['errors']) > 0;
}

function postForm()
{
	$columns = array('frist',
					'hvem',
					'kontakt_navn',
					'kontakt_email',
					'kontakt_nummer',
					'format',
					'arkstorrelse',
					'farge',
					'marger',
					'innhold');

	if(!preg_match('|^\d{4}-\d{2}-\d{2}$|', $_POST['frist']))
		addError('Ugyldig dato', 'frist');
	
	if(!$_POST['hvem'])
		addError('Bestiller er nødvendig', 'hvem');

	if(!$_POST['kontakt_navn'])
		addError('Kontaktperson er nødvendig', 'kontakt_navn');
	if(!$_POST['kontakt_email'])
		addError('Kontaktperson email er nødvendig', 'kontakt_email');
	
	if(!isset($_POST['format']))
		addError('Format er nødvendig','format');
	
	if($_POST['arkstorrelse'] == 'annet')
		$_POST['arkstorrelse'] = $_POST['arkstorrelse_annet'];
	
	if(!isset($_POST['arkstorrelse']))
		addError('Arkstørrelse er nødvendig','arkstorrelse');

	if(!isset($_POST['farge']))
		addError('Fargevalg er nødvendig', 'farge');
	
	if(!isset($_POST['marger']))
		addError('Vil du ha marger?', 'marger');
	
	if(!isset($_POST['innhold']))
		addError('Du må skrive en beskrivelde', 'innhold');
	
	if( hasErrors() )
	{
		return -1;
	}
	$keys = array();
	$values = array();
	
	foreach($columns as $column)
	{
		$keys[] = $column;
		$values[] = mysql_real_escape_string( $_POST[$column] );
	}
	
	$keys = '`' . implode('`, `', $keys) . '`';
	$values = '\'' . implode('\', \'', $values) . '\'';
	
	$res = mysql_query("INSERT INTO `bestilling`($keys) VALUES ($values)");
	
	if(!$res)
	{
		addError('Mysql error, klag til edb :P' . mysql_error());
		mail("edb@studentersamfundet.no", "bestillingserror", mysql_error() . "plakat/php/function.php:113");
		return false;
	}
	//designere@studentersamfundet.no
	$to = 'designere@studentersamfundet.no';
	$subject = 'Bestilling fra ' . $_POST['hvem'];
	$message  = "Frist :" . $_POST['frist'] . "\n";
	$message .= 'Kontaktperson :' . $_POST['kontakt_navn'] . "\n";
	$message .= 'Mer info : http://www.studentersamfundet.no/bestilling/inside.php?view='.mysql_insert_id() . "\n";
	$message .= "\n";
	$message .= $_POST['innhold'];
	
	if(!mail($to, $subject, $message))
		die("Error sending mail");
	
	// kopi til bestiller
	$to = $_POST('kontakt_email');
	$subject = 'Bestilling fra ' . $_POST['hvem'] . ', BESTILLINGSBEKREFTELSE';
	$message  = "Frist :" . $_POST['frist'] . "\n";
	$message .= 'Kontaktperson :' . $_POST['kontakt_navn'] . "\n";
	$message .= 'Mer info : http://www.studentersamfundet.no/bestilling/inside.php?view='.mysql_insert_id() . "\n";
	$message .= "\n";
	$message .= $_POST['innhold'];
	
	if(!mail($to, $subject, $message))
		die("Error sending mail");
	
	return true;
}

