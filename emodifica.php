<?php
session_start();
define("INCLUDING", 'TRUE');
include('config.php');
require_once (METHODS_PATH . '/idea.card.php');
//include_once 'configurazioneDB.php';	
include_once 'database.php';
$db = Database::getInstance();
$mysqli = $db->getConnection();
 // tutti i valori modificati presi dal form di modificaW.php
//recupero tutti vi valori da inserire nella tabella AZIENDE
$rag_soc= $_REQUEST["nome"];
$cap = $_REQUEST["cap"];
$indirizzo =$_REQUEST["indirizzo"];
$citta = $_REQUEST["citta"];
$nazione = $_REQUEST["nazione"];
$provincia = $_REQUEST["provincia"];
if($provincia!=null){
$sql="select CODICE from province where NOME='".$provincia."';";
$result=$mysqli->query($sql);
$prov=$result->fetch_assoc();
}
$regione = $_REQUEST["regione"];
$p_iva=$_REQUEST["partita_iva"];
$descrizione=$_REQUEST["descrizione"];
//echo $_SESSION['loggeduser']->who();
//aggiorno la tabella AZIENDE
if($provincia!=null)
	$upd_azienda="UPDATE AZIENDE SET RAGIONE_SOCIALE='$rag_soc',CAP='$cap', INDIRIZZO='$indirizzo', CITTA='$citta', NAZIONE='$nazione', REGIONE='$regione', PARTITA_IVA='$p_iva',DESCRIZIONE='$descrizione',PROVINCIA='$prov[CODICE]' where ID_UTENTE='$_SESSION[ID]'";
else{
	$upd_azienda="UPDATE AZIENDE SET RAGIONE_SOCIALE='$rag_soc',CAP='$cap', INDIRIZZO='$indirizzo', CITTA='$citta', NAZIONE='$nazione', REGIONE='$regione', PARTITA_IVA='$p_iva',DESCRIZIONE='$descrizione' ,PROVINCIA='ND' where ID_UTENTE='$_SESSION[ID]'";
}
//echo $upd_azienda;

$risultato_az= $mysqli->query($upd_azienda);
//recupero i valori della tabella CONTATTI
$cellulare= $_REQUEST["cellulare"];
$face=$_REQUEST["facebook"];
$fax=$_REQUEST["fax"];
$linkedin=$_REQUEST["linkedin"];
$sito=$_REQUEST["sito_web"];
$tel=$_REQUEST["telefono"];
$twit=$_REQUEST["twitter"];
//aggiorno la tabella CONTATTI
$upd_contatti="UPDATE CONTATTI SET CELLULARE='$cellulare', FACEBOOK='$face',FAX='$fax',LINKEDIN='$linkedin', SITO_WEB='$sito',TELEFONO='$tel',TWITTER='$twit' where PROPRIETARIO='$_SESSION[ID]'";
$risultato_cont= $mysqli->query($upd_contatti);
//echo $upd_contatti;
//recupero la mail e aggiorno la tabella UTENTI
$email=$_REQUEST["email"];
$conf_email=$_REQUEST["conf_email"];;
if($email==$conf_email){
	if(!empty($email) ){
	$upd_email="UPDATE UTENTI SET EMAIL='$email' where ID='$_SESSION[ID]'";
	$risultato_email= $mysqli->query($upd_email);
	}
}

$password=$_REQUEST["password"];
$conf_password=$_REQUEST["conf_password"];
if($password==$conf_password){
	if(!empty($password) ){
		echo "aggiornato";
		$upd_pass="UPDATE UTENTI SET PWD=PASSWORD('".$password."') where ID='$_SESSION[ID]'";
		echo $upd_pass;
		$risultato_pass= $mysqli->query($upd_pass);
	}
}



//recupero valori relativi all'immagine che serviranno anche per i controlli relativi ad essa
if ($img_name = $_FILES['immagine']['name'])
{	//Se � stata selezionata un'immagine la modifico, altrimenti non eseguo la modifica
$img_new_name = $_SESSION['ID'].'.jpg';
$img_temp_name = $_FILES['immagine']['tmp_name'];
$img_dir = 'img/profile/' . $img_new_name;
$img_err = $_FILES['immagine']['error'];
$img_size = $_FILES['immagine']['size'];
$img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
//Gestione errori immagine
if($img_err!=UPLOAD_ERR_OK)
{
	switch ($img_err)
	{
		case UPLOAD_ERR_INI_SIZE:
			header("Location:settingsAzienda.php?errore=2");
			break;
		case UPLOAD_ERR_FORM_SIZE:
			header("Location:settingsAzienda.php?errore=2");
			break;
		case UPLOAD_ERR_PARTIAL:
			header("Location:settingsAzienda.php?errore=3");
			break;
		case UPLOAD_ERR_NO_FILE:
			header("Location:settingsAzienda.php?errore=3");
			break;
		case UPLOAD_ERR_NO_TMP_DIR:
			header("Location:settingsAzienda.php?errore=3");
			break;
		case UPLOAD_ERR_CANT_WRITE:
			header("Location:settingsAzienda.php?errore=3");
			break;
		case UPLOAD_ERR_EXTENSION:
			header("Location:settingsAzienda.php?errore=1");
			break;
		default:
			header("Location:settingsAzienda.php?errore=3");
			break;
	}
	exit;
}
else
{ //nessun errore di compatibilit� con il sistema

//Controllo estensione  (stessi valori messi da angelo)
if ( !in_array($img_ext, array('jpg','jpeg','png','gif')) ) {
	header("Location:settingsAzienda.php?errore=1");
	exit;
}
//Controllo dimensione (stessi valori messi da angelo)
if ( $size/1024/1024 > 2 ) {
	header("Location:settingsAzienda.php?errore=2");
	exit;
}
//Spostamento effettivo
}
//Fine gestione errori immagine
move_uploaded_file($img_temp_name,$img_dir);
$sel_foto = "SELECT FOTO FROM  AZIENDE WHERE ID_UTENTE='$_SESSION[ID]'";
$result_foto=$mysqli->query($sel_foto);
$foto_field=$result_foto->fetch_assoc();
if (!$foto_field['FOTO']) {
	$upd_foto="UPDATE AZIENDE SET FOTO='$img_new_name' where ID_UTENTE='$_SESSION[ID]'";
	$result_foto= $mysqli->query($upd_foto);
}
}

	header("location: profile_azienda.php?id=$_SESSION[ID]");
?>
