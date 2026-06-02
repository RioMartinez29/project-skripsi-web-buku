<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <link rel="shortcut icon" type="image/x-icon" href="../assets/img/logo.png" />
  <title>Rekomendasi Film Animasi  - Login</title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="../assets/modules/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/modules/fontawesome/css/all.min.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/components.css">

  <?php
  session_start();
  if(isset($_SESSION['id_pengguna'])){
    header('location:../');
  }else{
    include 'connect.php';
    if(isset($_POST['submit'])){
      @$user = mysqli_real_escape_string($conn, $_POST['email']);
      @$pass = mysqli_real_escape_string($conn, $_POST['password']);

      $login = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$user' AND password='$pass'");
      $cek = mysqli_num_rows($login);
      $userid = mysqli_fetch_array($login);

      if($cek == 0){
        echo '
        <script>
        setTimeout(function() {
          swal({
            title: "Login Gagal",
            text: "email atau Password Anda Salah. Mohon periksa kembali form anda!",
            icon: "error"
            });
            }, 500);
            </script>
            ';
          }else{
            header('location:../');
            $_SESSION['id_pengguna'] = $userid['id_pengguna'];
            $_SESSION['status_pengguna'] = $userid['status_pengguna'];
          }
        }
		if (isset($_POST['submitemail'])) {
			$email=$_POST["email"];
			$cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");
			if(mysqli_num_rows($cek)>0){
				$row_user=mysqli_fetch_array($cek );
				$password_l=$row_user["password"];
				$email=$row_user["email"];
				ini_set( 'display_errors', 1 );
				//ini_set("smtp_port","25");
				error_reporting( E_ALL );
				$from = "triszai5@gmail.com";
				$to = $email;    
				$subject = "Lupa Password";
				$message = "Password anda adalah ".$password_l;
				$headers = "From:" . $from;    
				mail($to,$subject,$message, $headers);    
				echo '
					<script>
					setTimeout(function() {
					  swal({
						title: "Email Terkirim",
						text: "Password telah dikirim ke email, silahkan buka email anda untuk melihat password yang benar!",
						icon: "success"
						});
						}, 500);
						</script>
						';
			}else{
				echo '
					<script>
					setTimeout(function() {
					  swal({
						title: "Gagal",
						text: "Email tidak terdaftar!",
						icon: "error"
						});
						}, 500);
						</script>
						';				
			}
		}
		
		if (isset($_POST['submitpengguna'])) {
			$email = $_POST['email'];
			$nama_pengguna = $_POST['nama_pengguna'];
			$password = $_POST['password'];
			$status_pengguna = 2;
			$cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");
			if(mysqli_num_rows($cek)>0){
				echo '<script>
						setTimeout(function() {
							swal({
								title: "Gagal!",
								text: "Email sudah terdaftar!",
								icon: "warning"
								});
							}, 500);
					</script>';
			}else{
				$add = mysqli_query($conn, "INSERT INTO pengguna (email, nama_pengguna, password, status_pengguna) VALUES ('$email', '$nama_pengguna', '$password', '$status_pengguna')");
								
				$login = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");
				$cek = mysqli_num_rows($login);
				$userid = mysqli_fetch_array($login);
				$_SESSION['id_pengguna'] = $userid['id_pengguna'];
				$_SESSION['status_pengguna'] = $userid['status_pengguna']; 
				$_SESSION['email'] = $userid['email']; 
				
				$_SESSION["pesan"]='berhasil_registrasi';
				header('location:../');
			}
		}
        ?>
      </head>
      <body style="background-color:;background-image:url('../assets/img/bg.jpg');repeat:repeat-x;background-size:100%;">
        <div id="app">
          <section class="section">
            <div class="container mt-5">
			  <div class="row">			  
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">                  
				  <div class="card card-primary">
					<p align="right"><a href="../"><button type="button" class="close" aria-label="Close"><span aria-hidden="true">&nbsp;&times;&nbsp;</span></button></a></p>
                    <center>
					<img src="../assets/img/logo.png" alt="logo" width="160" class="shadow-dark">					
					</center>
                    <div class="card-body">
                      <form method="POST" action="" class="needs-validation" novalidate="" autocomplete="off">
                        <div class="form-group">
                          <label for="email">Email</label>
                          <input id="email" type="email" class="form-control" minlength="2" name="email" tabindex="1" required autofocus>
                          <div class="invalid-feedback">
                            Mohon isi email anda dengan benar!
                          </div>
                        </div>

                        <div class="form-group">
                          <div class="d-block">
                           <label for="password" class="control-label">Password</label>
                         </div>
                         <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                         <div class="invalid-feedback">
                          Mohon isi password anda!
                        </div>
                      </div>

                      <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                          Login
                        </button>
						<a href="#" class="btn btn-success btn-lg btn-block" data-target="#registrasiPengguna" data-toggle="modal"><i class="fas fa-"></i> Registrasi Pengguna</a>
						<a href="#" class="btn btn-danger btn-lg btn-block" data-target="#lupapassword" data-toggle="modal"><i class="fas fa-"></i> Lupa Password</a>
                      </div>
                    </form>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
	  
	  <!-- REGISTRASI pengguna -->
	<div class="modal fade" tabindex="-1" role="dialog" id="registrasiPengguna">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Registrasi Pengguna</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="" method="POST" class="needs-validation" novalidate="" autocomplete="off">
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Email</label>
                  <div class="col-sm-8">
                    <input type="hidden" class="form-control" name="tanggal" value="<?php echo date("Y-m-d") ?>" required="" id="getTanggal" readonly>
                    <input type="email" class="form-control" name="email" value="" required="" id="getemail">
                    <div class="invalid-feedback">
                      Mohon data diisi!
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Nama Pengguna</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" name="nama_pengguna" required="" id="getnama_pengguna">
                    <div class="invalid-feedback">
                      Mohon data diisi!
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-4 col-form-label">Password</label>
                  <div class="col-sm-8">
					 <input type="password" class="form-control" name="password" required="" id="getPassword">
                    <div class="invalid-feedback">
                      Mohon data diisi!
                    </div>
                  </div>
                </div>
			</div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary" name="submitpengguna">Daftar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
	  
	   <div class="modal fade" tabindex="-1" role="dialog" id="lupapassword">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Lupa Password</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="" method="POST" class="needs-validation" novalidate="" autocomplete="off" enctype="multipart/form-data" >
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Email</label>
                  <div class="col-sm-9">
                    <input type="email" class="form-control" name="email" value="" required="" id="getemail">
                    <div class="invalid-feedback">
                      Mohon data diisi!
                    </div>
                  </div>
                </div>
			</div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary" name="submitemail">Kirim Email</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- General JS Scripts -->
      <script src="../assets/modules/jquery.min.js"></script>
      <script src="../assets/modules/popper.js"></script>
      <script src="../assets/modules/tooltip.js"></script>
      <script src="../assets/modules/bootstrap/js/bootstrap.min.js"></script>
      <script src="../assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
      <script src="../assets/modules/moment.min.js"></script>
      <script src="../assets/js/stisla.js"></script>

      <!-- Template JS File -->
      <script src="../assets/js/scripts.js"></script>
      <script src="../assets/js/custom.js"></script>
      <!-- Sweet Alert -->
      <script src="../assets/modules/sweetalert/sweetalert.min.js"></script>
      <script src="../assets/js/page/modules-sweetalert.js"></script>
    </body>
  <?php } ?>
  </html>