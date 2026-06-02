<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $page = "Rekomendasi";
  session_start();
  include 'auth/connect.php';
  include "part/head.php";
  
  $id_pengguna=@$_SESSION["id_pengguna"];
  
  //Rekomendasi
  $dtlt = mysqli_query($conn, "DELETE FROM rekomendasi where id_pengguna='$id_pengguna'");
  
  //Penulis Yang Sama Dengan Riwayat
  $sql = mysqli_query($conn, "SELECT * FROM riwayat_pencarian join buku on riwayat_pencarian.id_buku=buku.id_buku where riwayat_pencarian.id_pengguna='$id_pengguna' group by buku.penulis");
  while($row=mysqli_fetch_array($sql)){
	  $penulis=$row["penulis"];
	  $sql_b = mysqli_query($conn, "SELECT * FROM buku where penulis='".$penulis."'");
	  while($row_b=mysqli_fetch_array($sql_b)){
		  $id_buku=$row_b["id_buku"];
		  $sql_ada = mysqli_query($conn, "SELECT * FROM rekomendasi where id_pengguna='$id_pengguna' and id_buku='$id_buku'");	  
		  if(mysqli_num_rows($sql_ada)<=0){
			$dlt = mysqli_query($conn, "INSERT INTO rekomendasi (id_pengguna, id_buku) VALUES ('$id_pengguna', '$id_buku')");	
		  }
	  }
  }
  
  //Judul [Kata Kunci] Buku Yang Pernah Dibuka
  $sql = mysqli_query($conn, "SELECT * FROM riwayat_pencarian join buku on riwayat_pencarian.id_buku=buku.id_buku where riwayat_pencarian.id_pengguna='$id_pengguna' group by buku.penulis");
  while($row=mysqli_fetch_array($sql)){
	  $judul_buku=strtolower(str_replace('"','',$row["judul_buku"]));
	  //$judul_buku=strtolower(str_replace('"','',$row["judul_buku"]." ".$row["keterangan"]));
	  $k=explode(" ",$judul_buku);
	  for($a=1;$a<=count($k);$a++){
		  if($a>10){
			  break;
		  }elseif(
			$k[$a-1]!=""			
			and $k[$a-1]!="the"
			and $k[$a-1]!="and"
			and $k[$a-1]!="book"
			and $k[$a-1]!="for"
			and $k[$a-1]!="to"
			and $k[$a-1]!="with"
			and $k[$a-1]!="this"
			and $k[$a-1]!="all"
			and $k[$a-1]!="are"
			and $k[$a-1]!="is"
			and $k[$a-1]!="it"
			and $k[$a-1]!="in"
			and $k[$a-1]!="of"
			and $k[$a-1]!="as"
			and $k[$a-1]!="a"
			
			and $k[$a-1]!="dan"
			and $k[$a-1]!="yang"
			and $k[$a-1]!="di"
			and $k[$a-1]!="ke"
			and $k[$a-1]!="dari"
			and $k[$a-1]!="pada"
			and $k[$a-1]!="untuk"
			and $k[$a-1]!="dengan"
			and $k[$a-1]!="ini"
			and $k[$a-1]!="nya"
		  ){
			//echo $k[$a-1]." ";
			  $sql_b = mysqli_query($conn, "SELECT * FROM buku where judul_buku like '%".$k[$a-1]."%' or keterangan like '%".$k[$a-1]."%'");
			  while($row_b=mysqli_fetch_array($sql_b)){
				  $id_buku=$row_b["id_buku"];
				  $sql_ada = mysqli_query($conn, "SELECT * FROM rekomendasi where id_pengguna='$id_pengguna' and id_buku='$id_buku'");	  
				  if(mysqli_num_rows($sql_ada)<=0){
					$dlt = mysqli_query($conn, "INSERT INTO rekomendasi (id_pengguna, id_buku) VALUES ('$id_pengguna', '$id_buku')");	
				  }
			  }			  
		  }
	  }
  }

  //Keyword Pencarian
  $sql = mysqli_query($conn, "SELECT * FROM riwayat_pencarian where id_pengguna='$id_pengguna' and id_buku='0'");
  if(mysqli_num_rows($sql)>0){
	  $row=mysqli_fetch_array($sql);
	  $kata_kunci=$row["kata_kunci"];
	  $sql_b = mysqli_query($conn, "SELECT * FROM buku where judul_buku like '%".$kata_kunci."%'");	  
	  while($row_b=mysqli_fetch_array($sql_b)){
		  $id_buku=$row_b["id_buku"];
		  $sql_ada = mysqli_query($conn, "SELECT * FROM rekomendasi where id_pengguna='$id_pengguna' and id_buku='$id_buku'");	  
		  if(mysqli_num_rows($sql_ada)<=0){
			$dlt = mysqli_query($conn, "INSERT INTO rekomendasi (id_pengguna, id_buku) VALUES ('$id_pengguna', '$id_buku')");	
		  }
	  }
  }

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
				$sql = mysqli_query($conn, "SELECT * FROM rekomendasi join buku on rekomendasi.id_buku=buku.id_buku where id_pengguna='".@$_SESSION["id_pengguna"]."' order by rating desc limit 20");
				if(@$_GET["cari_buku"]){
					$sql = mysqli_query($conn, "SELECT * FROM buku where  genre like '%".$_GET["cari_buku"]."%' or judul_buku like '%".$_GET["cari_buku"]."%' order by rating desc limit 20");
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
						<div class="col-lg-3 col-md-6 col-sm-12 col-12">
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