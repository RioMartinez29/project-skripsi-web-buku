<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $page = "Dashboard";
  session_start();
  include 'auth/connect.php';
  include "part/head.php";
  //include 'part_func/tgl_ind.php';

	if(@$_SESSION["pesan"]=='berhasil_registrasi'){
		unset($_SESSION["pesan"]);
		//unset($_SESSION['email']);
		echo '<script>
				setTimeout(function() {
					swal({
						title: "Berhasil! AUTO LOGIN",
						text: "Anda telah melakukan registrasi akun pelanggan. Anda dapat login dengan email ('. @$_SESSION["email"] .'). Untuk Keamanan data, silahkan ubah password anda secara berkala. Jika Lupa password, anda dapat kontak langung admin untuk mereset password anda!",
						icon: "success"
						});
					}, 500);
		</script>';		
	}

   	if (@$_SESSION['status_user']==3){
		$sqlpelanggan = mysqli_query($conn, "SELECT * FROM pengguna where id='".$_SESSION["id_pengguna"]."'");
		$rowpelanggan=mysqli_fetch_array($sqlpelanggan);
		$emailpelanggan=$rowpelanggan["email"];

		$sqlpelanggan2 = mysqli_query($conn, "SELECT * FROM pelanggan where email='$emailpelanggan'");
		if(mysqli_num_rows($sqlpelanggan2)){
			$rowpelanggan2=mysqli_fetch_array($sqlpelanggan2);
			$_SESSION["id_pelanggan"]=$rowpelanggan2["id"];		
		}
	}

  $pengguna = mysqli_query($conn, "SELECT * FROM pengguna");
  $jumpengguna = mysqli_num_rows($pengguna);

  $buku = mysqli_query($conn, "SELECT * FROM buku");
  $jumbuku = mysqli_num_rows($buku);
  
  $ulasan = mysqli_query($conn, "SELECT * FROM ulasan");
  $jumulasan = mysqli_num_rows($ulasan);

  ?>
  <style>
    #link-no {
      text-decoration: none;
    }
  </style>
</head>

<body style="background-color:;background-image:url('assets/img/bg.jpg');repeat:repeat-x;background-size:100%;">
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <?php
      include 'part/navbar.php';
      include 'part/sidebar.php';
	  if(@$_SESSION["status_user"]==1){}else{
		  echo "<script>document.location='buku.php'</script>";
	  }
      ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
		<br>
          <div class="row">		
			<?php if(@$_SESSION["status_user"]==1 or @$_SESSION["status_user"]==2){ ?>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
				<a href="pemesanan_buku.php">
				  <div class="card card-statistic-1">
					<div class="card-icon bg-danger">
					  <i class="fas fa-list"></i>
					</div>
					<div class="card-wrap">
					  <div class="card-header">
						<h4>Penjualan</h4>
					  </div>
					  <div class="card-body">
						<?php echo $jumppenjualan; ?>
					  </div>
					</div>
				  </div>
				</a>
				</div>
			
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
				<a href="pelanggan.php">				
				  <div class="card card-statistic-1">
					<div class="card-icon bg-warning">
					  <i class="fas fa-users"></i>
					</div>
					<div class="card-wrap">
					  <div class="card-header">
						<h4>Pelanggan</h4>
					  </div>
					  <div class="card-body">
						<?php echo $jumpelanggan; ?>
					  </div>
					</div>
				  </div>
				</a>
				</div>
			
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
				<a href="pengguna.php">
				  <div class="card card-statistic-1">
					<div class="card-icon bg-success">
					  <i class="fas fa-user"></i>
					</div>
					<div class="card-wrap">
					  <div class="card-header">
						<h4>Pengguna</h4>
					  </div>
					  <div class="card-body">
						<?php echo $jumpengguna; ?>
					  </div>
					</div>
				  </div>
				</a>
				</div>
						
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
				<a href="buku.php">
				  <div class="card card-statistic-1">
					<div class="card-icon bg-primary">
					  <i class="fas fa-pills"></i>
					</div>
					<div class="card-wrap">
					  <div class="card-header">
						<h4>buku</h4>
					  </div>
					  <div class="card-body">
						<?php echo $jumbuku; ?>
					  </div>
					</div>
				  </div>
				</a>
				</div>
			
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
				<a href="provinsi.php">
				  <div class="card card-statistic-1">
					<div class="card-icon bg-primary">
					  <i class="fas fa-map"></i>
					</div>
					<div class="card-wrap">
					  <div class="card-header">
						<h4>Provinsi</h4>
					  </div>
					  <div class="card-body">
						<?php echo $jumprovinsi; ?>
					  </div>
					</div>
				  </div>
				</a>
				</div>
			
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
				<a href="kota.php">
				  <div class="card card-statistic-1">
					<div class="card-icon bg-primary">
					  <i class="fas fa-city"></i>
					</div>
					<div class="card-wrap">
					  <div class="card-header">
						<h4>Kota</h4>
					  </div>
					  <div class="card-body">
						<?php echo $jumkota; ?>
					  </div>
					</div>
				  </div>
				</a>
				</div>
			
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
				<a href="ongkos_kirim.php">
				  <div class="card card-statistic-1">
					<div class="card-icon bg-primary">
					  <i class="fas fa-paper-plane"></i>
					</div>
					<div class="card-wrap">
					  <div class="card-header">
						<h4>Ongkos Kirim</h4>
					  </div>
					  <div class="card-body">
						<?php echo $jumongkos_kirim; ?>
					  </div>
					</div>
				  </div>
				</a>
				</div>
			
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
				<a href="rekening.php">
				  <div class="card card-statistic-1">
					<div class="card-icon bg-primary">
					  <i class="fas fa-credit-card"></i>
					</div>
					<div class="card-wrap">
					  <div class="card-header">
						<h4>Rekening</h4>
					  </div>
					  <div class="card-body">
						<?php echo $jumrekening; ?>
					  </div>
					</div>
				  </div>
				</a>
				</div>
			<?php } ?>	
			<?php if(@$_SESSION["status_user"]==4){ ?>
				<div class="col-lg-3 col-md-6 col-sm-6 col-12">
					<a href="rekening.php">
					  <div class="card card-statistic-1">
						<div class="card-icon bg-primary">
						  <i class="fas fa-credit-card"></i>
						</div>
						<div class="card-wrap">
						  <div class="card-header">
							<h4>Penjualan buku Resep</h4>
						  </div>
						  <div class="card-body">
							<?php echo $jumpenjualan_buku_resep; ?>
						  </div>
						</div>
					  </div>
					</a>
				</div>
			<?php } ?>	
			<?php if(@$_SESSION["status_user"]==1 or @$_SESSION["status_user"]==2 or @$_SESSION["status_user"]==4){ ?>
				<div class="col-lg-12 col-md-12 col-sm-12 col-12">
					<div class="card">
						<div class="card card-statistic-1">
							<div class="card-icon bg-success">
							  <i class="fas fa-signal"></i>
							</div>
							<div class="card-wrap">
							  <div class="card-header">
								<h2>Statistik Penjualan</h2>
							  </div>
							</div>
						</div>
						<div class="col-lg-12">
							<?php include("grafik_penjualan.php"); ?>
						</div>
					</div>
				</div>
			<?php } ?>	
			<?php if(@$_SESSION["status_user"]==""){ ?>
					<div class="col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="card"style="background-color:rgba(0,0,0,.6);">
								&nbsp;<br>
								<center>
									<div class="col-lg-11 col-md-12 col-sm-12 col-12">
										<div class="slider" style="background-color:rgba(0,0,0,1)">
											<?php 
												$sql_pg = mysqli_query($conn, "SELECT * FROM buku");
												$i = 0;
												while ($row_pg = mysqli_fetch_array($sql_pg)) {
													$gambar_pg="assets/img/buku/".$row_pg['id'].".jpg";
													if(file_exists($gambar_pg)){ ?>
													<div>
														<a href="#">
															<img src="<?php echo $gambar_pg ; ?>" height="335px" width="100%">
														</a>            
													</div>
												<?php 
													}
												}
											?>
										</div>
									</div>
								</center>
								<br>&nbsp;
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-12">
						<div class="card"style="background-color:rgba(0,0,0,.6);">
							<div class="card card-statistic-1" style="background-color:rgba(0,0,0,.6);">
								<br>
									<h3>&nbsp;&nbsp;&nbsp;Lokasi DZMOON</h3>
								<br>
							</div>
							<div class="col-lg-12" style="background-color:rgba(0,0,0,.5);">
								<center>
									<a target="_blank" href="https://www.google.com/maps/place/Manhattan+Times+Square/@3.5914555,98.6269052,17z/data=!4m6!3m5!1s0x30312e5002c783ab:0xf2a9d83981179038!8m2!3d3.5913645!4d98.6267604!16s%2Fg%2F11bwqqhrtf?entry=ttu&g_ep=EgoyMDI1MDUxNS4xIKXMDSoASAFQAw%3D%3D"><img src="assets/img/lokasi.jpg" width="99%"></a><br><br>
								</center>
							</div>
						</div>
					</div>
			<?php } ?>
        </section>
      </div>
      <?php include 'part/footer.php'; ?>
    </div>
  </div>

  <?php include "part/all-js.php"; ?>
    <script type="text/javascript" src="slick/slick.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.slider').slick({
            autoplay: true,
            autoplaySpeed: 2500,
            dots: true
            });
        });
    </script>
</body>

</html>