<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $page = "riwayat_pencarian";
  session_start();
  include 'auth/connect.php';
  include "part/head.php";
  ?>
</head>

<body style="background-color:;background-image:url('assets/img/bg.jpg');repeat:repeat-x;background-size:100%;">
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <?php
		include 'part/navbar.php';		  
		include 'part/sidebar.php';  
      ?>

      <!-- Main Content -->
      <div class="main-content">&nbsp;
        <section class="section">
          <div class="section-body">
			<div class="card-header-action">
				<?php include("cari_buku.php"); ?>
			</div>&nbsp;
			<div class="row">		
				<?php
				echo $_SESSION["id_pengguna"];
				$sql = mysqli_query($conn, "SELECT * FROM riwayat_pencarian join buku on riwayat_pencarian.id_buku=buku.id_buku where id_pengguna='".@$_SESSION["id_pengguna"]."' order by rating desc");
				if(@$_GET["cari_buku"]){
					$sql = mysqli_query($conn, "SELECT * FROM buku where  genre like '%".$_GET["cari_buku"]."%' or judul_buku like '%".$_GET["cari_buku"]."%' order by rating desc");
				}
				if(mysqli_num_rows($sql)<=0){
				?>
					<div class="col-lg-12 col-md-12 col-sm-12 col-12">
							<div class="section-body">
								<div class="row">
									<div class="col-12">
										<div class="card">
											<div class="card-body">
												<center><h5>Not Found!</h5></center>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>	
				<?php 
				}else{
					while($row=mysqli_fetch_array($sql)){
						$id_buku=$row["id_buku"];
						$tahun_terbit=$row["tahun_terbit"];	
						$judul_buku=substr($row["judul_buku"],0,20);
						$rating=$row["rating"];
						$foto="assets/img/buku/".$row["id_buku"].".jpg";
						$penulis=str_replace("\n","<br/>",$row["penulis"]);
						?>
						<div class="col-lg-3 col-md-12 col-sm-12 col-12">
							<div class="section-body">
								<div class="row">
									<div class="col-12">
										<div class="card">
											<div class="card-body">
												<center>
												<a href="buku.php?id_buku=<?php echo $id_buku; ?>">
												<img src="<?php echo $foto; ?>" width="100%" height="230px"/>
												</a>
												</center>
												<hr/>
												<p align='left'>
													<?php echo "<b>".ucwords($judul_buku)."</b><br>"; ?>
													Rating : <?php echo "<b><font color='orange'>".$rating."</font></b>"; ?><br>
												</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
        </section>
      </div>

      <?php include 'part/footer.php'; ?>
    </div>
  </div>
  <?php include "part/all-js.php"; ?>
  </body>

</html>