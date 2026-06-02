  <script src="../assets/modules/sweetalert/sweet2.js"></script>
  <link rel="stylesheet" href="../assets/modules/sweetalert/sweet2.css">

  <?php
    include 'connect.php';

    $tipe = $_GET['type'];
    $id = $_GET['id'];
	if($tipe=="pendaftaran"){
		$sql_cek = mysqli_query($conn, "SELECT * FROM $tipe WHERE id='$id'");
		$row=mysqli_fetch_array($sql_cek);
		$username=$row["nomor_pendaftaran"];
		$sql = mysqli_query($conn, "DELETE FROM pengguna WHERE username='$username'");
	}
	$sql = mysqli_query($conn, "DELETE FROM $tipe WHERE id='$id'");
	
    ?>
  <script>
      setTimeout(function() {
          swal({
              title: "Sukses",
              text: "Hapus data berhasil!",
              type: "success"
          }, function() {
              <?php
                    echo 'window.location.href="../'.$tipe.'.php";';
                ?>
          });
      }, 500);
  </script>