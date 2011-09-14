<?php
require 'php/db.php';


include 'php/function.php';
if(isset($_POST['bestill']))
{
	$tmp = postForm();
	if($tmp === true)
	{
		header('Location: allok.php?ref=' . mysql_insert_id());
		exit;
	}
	else if($tmp === false)
	{
		ob_start();
		print_r($_POST);
		$form = urlencode(ob_get_contents());
		ob_end_clean();
		
		header('Location: fail.php?text=' . $form);
		exit;
	}
}
$center = true;
include 'php/header.php';
?>

	<script type="text/javascript">
  $(document).ready(function(){
  <?php
  $t = implode(',', explode('-',$_POST['frist']));
 ?>

    $("#frist").datepicker({altField: '#frist_field',
    						altFormat: 'yy-mm-dd',
    						minDate: '+2w',
    						defaultDate: new Date("<?= $t ?>") });
  	$("#frist_field").css("display", "none");
  });
</script>
		<h1>Kommunikasjonsavdelingen - Bestilling designoppdrag</h1>
		
		<form method="post" action="index.php" id="bestilling">
		
			<div class="formitem">
				<h2 class="title">Forening</h2>
				<div class="desc">Hvem bestiller?</div>
				<input name="hvem" value="<?php echo @$_POST['hvem'] ?>" />
				<?php ferror('hvem') ?>
			</div>
		
			<div class="formitem">
				<h2 class="title">Frist</h2>
				<div class="desc">Fristen må være tidligst to uker etter bestilling</div>
				<input id = "frist_field" value="<?php echo @$_POST['frist'] ?>" name="frist" />
				<div id="frist"></div>
				<?php ferror('frist') ?>
			</div>
			<div class="formitem">
				<h2 class="title">Kontaktperson</h2>
			
				<table>
				<tr>
						<td><label for="kontakt_navn">Navn:</label></td>
						<td><input name="kontakt_navn" value="<?php echo @$_POST['kontakt_navn'] ?>" /></td>
						<?php ferror('kontakt_navn', true) ?>
				</tr>
				<tr>
						<td><label for="kontakt_email">Email:</label></td>
						<td><input name="kontakt_email" value="<?php echo @$_POST['kontakt_email'] ?>" /></td>
						<?php ferror('kontakt_email', true) ?>
				</tr>
				<tr>
						<td><label for="kontakt_nummer">Tlf.:</label></td>
						<td><input name="kontakt_nummer" value="<?php echo @$_POST['kontakt_nummer'] ?>" /></td>
						<?php ferror('kontakt_nummer', true) ?>
				</tr>
				</table>
			</div>
		
			<div class="formitem">
				<h2 class="title">Format</h2>
			
				<?php radioList('format',array('Trykk - Flyer','Trykk - Plakat','Trykk - Banner','Web - Annonse','Web - Banner','Web - Bilde')) ?>
			</div>
			<div class="formitem">
				<h2 class="title">Størrelse</h2>
				<?php radioList('arkstorrelse', array('A2','A3','A4','A5','A6')) ?>
			</div>
		
			<div class="formitem">
				<h2 class="title">Farger eller svart/hvitt</h2>
				<?php radioList('farge', array('Farge','Svart/hvitt'), false) ?>
			</div>
		
			<div class="formitem">
				<h2 class="title">Bleed/marger</h2>
				<?php radioList('marger', array('Ja','Nei'), false) ?>
			</div>
		
			<div class="formitem">
				<h2 class="title">Innhold</h2>
				<div class="desc">Hvem, hva, hvor og når</div>
				<textarea name="innhold"><?php echo @$_POST['innhold'] ?></textarea>
				<?php ferror('innhold') ?>
			</div>
			<div class="submit">
				<?php ferror() ?>
			
				<input type="submit" value="Send bestilling" name="bestill" />
			</div>
		</form>
<?php
include 'php/footer.php';
