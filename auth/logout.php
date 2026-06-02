<?php
	session_start();
	unset($_SESSION['id_pengguna']);
	unset($_SESSION['status_pengguna']);
	session_destroy();
	header('location:../');
?>