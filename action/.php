<?php
$act = $_REQUEST['act'];
if($act=='logout'){
	session_start();
	session_destroy();
	echo"
		<script >
		window.location='../';
		</script>
		";
	exit;
#alert('Logout complete!');
}
if ($_SESSION['UID']=="") {

	session_start();

	session_destroy();

	echo"

		<script >

		window.location='../';

		</script>

		";

	exit;

}


?>