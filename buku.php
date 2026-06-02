<!DOCTYPE html>
<html lang="en">

<head>
<?php
set_time_limit(600);
  $page = "Buku";
  session_start();
  include 'auth/connect.php';
  include "part/head.php";

  if(@$_SESSION["pesan"]=="favorit_ditambahkan"){
	unset($_SESSION["pesan"]);
	echo '<script>
				setTimeout(function() {
					swal({
					title: "Berhasil",
					text: "Buku ditambahkan ke daftar favorit!",
					icon: "success"
					});
					}, 900);
				</script>';
  }
  if(@$_SESSION["pesan"]=="favorit_dihapus"){
	unset($_SESSION["pesan"]);
	echo '<script>
				setTimeout(function() {
					swal({
					title: "Berhasil",
					text: "Buku dihapus dari daftar favorit!",
					icon: "success"
					});
					}, 900);
				</script>';
  }
  if (isset($_POST['submit3'])) {
    $id_buku = $_POST['id_buku'];	
	$dlt = mysqli_query($conn, "DELETE FROM buku WHERE id_buku='$id_buku'");	
	echo '<script>
				setTimeout(function() {
					swal({
					title: "Data Dihapus",
					text: "Data buku berhasil dihapus!",
					icon: "success"
					});
					}, 500);
				</script>';	 
  }
  if (@$_GET["id_buku"] and @$_SESSION["status_pengguna"]==2) {
	$tanggal = date("Y-m-d");
	$id_pengguna = @$_SESSION['id_pengguna'];	
    $id_buku = $_GET['id_buku'];
	$cek_ada = mysqli_query($conn, "SELECT * from riwayat_pencarian where id_pengguna='$id_pengguna' and id_buku='$id_buku'");
	if(mysqli_num_rows($cek_ada)>0){
		$add = mysqli_query($conn, "DELETE FROM riwayat_pencarian where id_pengguna='$id_pengguna' and id_buku='$id_buku'");
	}
	$dlt = mysqli_query($conn, "INSERT INTO riwayat_pencarian (tanggal, id_pengguna, id_buku) VALUES ('$tanggal', '$id_pengguna', '$id_buku')");	
  }
  
  if (@$_GET["favorit"]) {
	$id_pengguna = $_SESSION['id_pengguna'];	
    $id_buku = $_GET['favorit'];	
	$dlt = mysqli_query($conn, "INSERT INTO favorit (id_pengguna, id_buku) VALUES ('$id_pengguna', '$id_buku')");	
	$_SESSION["pesan"]="favorit_ditambahkan";
	echo "<script>document.location='buku.php?id_buku=".$_GET["favorit"]."';</script>";

  }

  if (@$_GET["hapus_favorit"]) {
	$id_pengguna = $_SESSION['id_pengguna'];	
    $id_buku = $_GET['hapus_favorit'];	
	$dlt = mysqli_query($conn, "DELETE FROM favorit where id_pengguna='$id_pengguna' and id_buku='$id_buku'");	
	$_SESSION["pesan"]="favorit_dihapus";
	echo "<script>document.location='buku.php?id_buku=".$_GET["hapus_favorit"]."';</script>";
  }
  
  if(@$_GET["sinkron_google_api"]){
	for ($a=1; $a<=7;$a++){
		$genre="";
		if($a==1){
			$genre="Fiction";
		}elseif($a==2){
			$genre="Science";
		}elseif($a==3){
			$genre="History";
		}elseif($a==4){
			$genre="Fantasy";
		}elseif($a==5){
			$genre="Mistery";			
		}elseif($a==6){
			$genre="Romance";		
		}elseif($a==7){
			$genre="Thriller";		
		}
	// Mendapatkan input dari form
		//$genre = $_POST['genre'];  // Default ke 'fiction' jika tidak ada genre yang dipilih
		$rating = 0;

// Google Books API key
$googleApiKey = "AIzaSyAYcM4a8lHOnkamC6_S3WzeTq6z_g92Q_4";
$allBooks = [];

// --- Ambil buku Indonesia ---
for ($i = 0; $i <= 40; $i += 40) {
    $url = "https://www.googleapis.com/books/v1/volumes?q={$genre}&langRestrict=id&maxResults=40&startIndex={$i}&key={$googleApiKey}";
    $data = @file_get_contents($url);
    $decoded = $data ? json_decode($data, true) : null;
    if (!empty($decoded['items'])) {
        $allBooks = array_merge($allBooks, $decoded['items']);
    }
}

// --- Ambil buku Global ---
for ($i = 0; $i <= 40; $i += 40) {
    $url = "https://www.googleapis.com/books/v1/volumes?q={$genre}&maxResults=40&startIndex={$i}&key={$googleApiKey}";
    $data = @file_get_contents($url);
    $decoded = $data ? json_decode($data, true) : null;
    if (!empty($decoded['items'])) {
        $allBooks = array_merge($allBooks, $decoded['items']);
    }
}

// --- Open Library API ---
$openLibraryData = @file_get_contents("https://openlibrary.org/subjects/{$genre}.json?limit=10");
$openLibrary = $openLibraryData ? json_decode($openLibraryData, true) : ['works' => []];

// --- Gabungkan semua buku ---
$combinedBooks = array_merge($allBooks, $openLibrary['works'] ?? []);



		// Filter berdasarkan rating pengguna
		$finalBooks = [];
		foreach ($combinedBooks as $book) {
			$bookRating = isset($book['volumeInfo']['averageRating']) ? $book['volumeInfo']['averageRating'] : (rand(1, 5));  // Jika rating tidak ada, random rating
			if ($bookRating >= $rating) {
				$finalBooks[] = [
					'title' => $book['volumeInfo']['title'] ?? $book['title'],
					'author' => $book['volumeInfo']['authors'][0] ?? 'Unknown',
					'author' => $book['volumeInfo']['authors'][0] ?? 'Unknown',
					'published_date' => $book['volumeInfo']['publishedDate'] ?? 'Unknown',
					'description' => $book['volumeInfo']['description'] ?? 'Unknown',
					'previewLink' => $book['volumeInfo']['previewLink'] ?? 'Unknown',
					'cover' => $book['volumeInfo']['imageLinks']['thumbnail'] ?? 'default_cover.jpg',
					'rating' => $bookRating
				];
			}
		}

		// Tampilkan hasil rekomendasi buku
		//echo "<section class='book-results'>";
		//echo "<div class='container'>";
		//echo "<h2>Recommended Books for Genre: $genre</h2>";
		if (!empty($finalBooks)) {
			foreach ($finalBooks as $book) {
				$foto="{$book['cover']}";
				$genre= $genre;
				$judul_buku= "{$book['title']}";
				$judul_buku= str_replace("'","`",$judul_buku);
				$penulis="{$book['author']}";
				$keterangan="{$book['description']}";
				$keterangan=str_replace("'","`",$keterangan);
				$rating="{$book['rating']}";
				$link_buku="{$book['previewLink']}";
				$tahun_terbit ="{$book['published_date']}";
				$tahun_terbit =substr($tahun_terbit,0,4);
				$cek_ada = mysqli_query($conn, "SELECT * from buku where judul_buku='$judul_buku'");
				if(mysqli_num_rows($cek_ada)>0){
					$row_ad=mysqli_fetch_array($cek_ada);
					$id_buku=$row_ad["id_buku"];
					
					$add = mysqli_query($conn, "UPDATE buku SET tahun_terbit='$tahun_terbit', genre='$genre', penulis='$penulis', link_buku='$link_buku', keterangan='$keterangan' where judul_buku='$judul_buku'");					

					$nama_file = $id_buku.".jpg";
					$lokasi_file=$foto;
					$file_upload="assets/img/buku/".$nama_file;
					@copy(@$lokasi_file,@$file_upload);		

				}else{
					$add = mysqli_query($conn, "INSERT INTO buku (tahun_terbit, judul_buku, genre, penulis, link_buku, keterangan, rating) VALUES ('$tahun_terbit', '$judul_buku', '$genre', '$penulis', '$link_buku', '$keterangan', '$rating')");

					$cek_ss = mysqli_query($conn, "SELECT * from buku where judul_buku='$judul_buku'");
					$id_buku=0;
					if(mysqli_num_rows($cek_ss)>=0){
						$row_ss=mysqli_fetch_array($cek_ss);
						$id_buku=$row_ss["id_buku"];
						
						$nama_file = $id_buku.".jpg";
						$lokasi_file=$foto;
						$file_upload="assets/img/buku/".$nama_file;
						@copy(@$lokasi_file,@$file_upload);		
					}
				}
			}
		}
	}
	echo "<script>document.location='buku.php';</script>";
  }
  
  if (isset($_POST['submit'])) {
    $id_buku = $_POST['id_buku'];
    $judul_buku = $_POST['judul_buku'];
    $penulis = $_POST['penulis'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $genre = $_POST['genre'];
	$rating = $_POST['rating'];
	
	$cek_ada = mysqli_query($conn, "SELECT * from buku where id_buku<>'$id_buku' and tahun_terbit='$tahun_terbit' and judul_buku='$judul_buku'");
	if(mysqli_num_rows($cek_ada)>0){
		echo '<script>
				setTimeout(function() {
					swal({
						title: "Gagal!",
						text: "judul_buku telah diinput hari yang sama!",
						icon: "warning"
						});
					}, 500);
			</script>';

	}else{
		$up2 = mysqli_query($conn, "UPDATE buku SET judul_buku='$judul_buku', penulis='$penulis', tahun_terbit='$tahun_terbit', genre='$genre', rating='$rating' WHERE id_buku='$id_buku'");
		
		if(isset($_FILES['foto'])){
			$nama_gambar = $id_buku;
			$nama_file=$_FILES['foto']['tmp_name'];
			$file_upload="assets/img/buku/".$id_buku.".jpg";
			move_uploaded_file($nama_file,$file_upload);
		}
		echo '<script>
				setTimeout(function() {
					swal({
					title: "Data Diubah",
					text: "Data buku berhasil diubah!",
					icon: "success"
					});
					}, 500);
				</script>';
	}
  }

  if (isset($_POST['submit2'])) {
    $tahun_terbit = $_POST['tahun_terbit'];
    $judul_buku = $_POST['judul_buku'];
    $genre = $_POST['genre'];
	$rating = $_POST['rating'];
    $penulis = $_POST['penulis'];
	
	$cek_ada = mysqli_query($conn, "SELECT * from buku where tahun_terbit='$tahun_terbit' and judul_buku='$judul_buku'");
	if(mysqli_num_rows($cek_ada)>0){
		echo '<script>
				setTimeout(function() {
					swal({
						title: "Gagal!",
						text: "judul_buku telah diinput hari yang sama!",
						icon: "warning"
						});
					}, 500);
			</script>';

	}else{
		$add = mysqli_query($conn, "INSERT INTO buku (tahun_terbit, judul_buku, genre, penulis, rating) VALUES ('$tahun_terbit', '$judul_buku', '$penulis', '$genre', '$rating')");
		
		$sl = mysqli_query($conn, "SELECT * from buku order by id_buku desc");
		$row45=mysqli_fetch_array($sl);
		$id_buku=$row45["id_buku"];
		if(isset($_FILES['foto'])){
			$nama_gambar = $id_buku;
			$nama_file=$_FILES['foto']['tmp_name'];
			$file_upload="assets/img/buku/".$id_buku.".jpg";
			move_uploaded_file($nama_file,$file_upload);
		}
		echo '<script>
					setTimeout(function() {
						swal({
							title: "Berhasil!",
							text: "buku baru telah ditambahkan!",
							icon: "success"
							});
						}, 500);
			</script>';
	}
  }
  
  
  if (isset($_POST['submit4'])) {
    $id_buku = $_GET['id_buku'];
    $id_pengguna = $_SESSION['id_pengguna'];
	$tanggal = date("Y-m-d");
	$nilai = $_POST['nilai'];
    $ulasan = $_POST['ulasan'];
	
	$cek_ada = mysqli_query($conn, "SELECT * from ulasan where id_buku='$id_buku' and id_pengguna='$id_pengguna'");
	if(mysqli_num_rows($cek_ada)>0){
		
		$upd = mysqli_query($conn, "UPDATE ulasan set tanggal='$tanggal', nilai='$nilai',ulasan='$ulasan' where id_buku='$id_buku' and id_pengguna='$id_pengguna'");
		
		
		$hitung_ulasan = mysqli_query($conn, "SELECT * FROM ulasan WHERE id_buku='$id_buku'");
		$total_nilai=0;
		$jumlah_ulasan=0;
		$rating=0;
		if(mysqli_num_rows($hitung_ulasan)>0){
			while($uls=mysqli_fetch_array($hitung_ulasan)){
				$jumlah_ulasan=$jumlah_ulasan+1;
				$nilai = $uls['nilai'];
				$total_nilai=$total_nilai+$nilai;
			}
			$rating=number_format(($total_nilai/$jumlah_ulasan),1);
		}
		$up_nilai_buku = mysqli_query($conn, "UPDATE buku set rating='$rating' WHERE id_buku='$id_buku'");	
		
		echo '<script>
					setTimeout(function() {
						swal({
							title: "Berhasil!",
							text: "Ulasan telah diedit!",
							icon: "success"
							});
						}, 500);
			</script>';
	}else{
		$add = mysqli_query($conn, "INSERT INTO ulasan (tanggal, id_buku, id_pengguna, nilai, ulasan) VALUES ('$tanggal', '$id_buku', '$id_pengguna', '$nilai', '$ulasan')");
		
		$hitung_ulasan = mysqli_query($conn, "SELECT * FROM ulasan WHERE id_buku='$id_buku'");
		$total_nilai=0;
		$jumlah_ulasan=0;
		$rating=0;
		if(mysqli_num_rows($hitung_ulasan)>0){
			while($uls=mysqli_fetch_array($hitung_ulasan)){
				$jumlah_ulasan=$jumlah_ulasan+1;
				$nilai = $uls['nilai'];
				$total_nilai=$total_nilai+$nilai;
			}
			$rating=number_format(($total_nilai/$jumlah_ulasan),1);
		}
		$up_nilai_buku = mysqli_query($conn, "UPDATE buku set rating='$rating' WHERE id_buku='$id_buku'");	
		
		echo '<script>
					setTimeout(function() {
						swal({
							title: "Berhasil!",
							text: "Ulasan telah dikirim!",
							icon: "success"
							});
						}, 500);
			</script>';
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
			<?php
			if(@$_GET["id_buku"]){
				$id_buku=$_GET["id_buku"];
				?>
				<div class="row">
				<?php $sql = mysqli_query($conn, "SELECT * FROM buku where id_buku='$id_buku'");
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
					$row=mysqli_fetch_array($sql);
						$id_buku=$row["id_buku"];
						$tahun_terbit=$row["tahun_terbit"];	
						$judul_buku=ucwords($row["judul_buku"]);
						$genre=ucwords($row["genre"]);
						$rating=$row["rating"];
						$link_buku=$row["link_buku"];
						$keterangan=$row["keterangan"];
						$foto="assets/img/buku/".$row["id_buku"].".jpg";
						$penulis=str_replace("\n","<br/>",$row["penulis"]);							
					?>					
						<div class="col-lg-12 col-md-12 col-sm-12 col-12">
							<div class="section-body" style="background-color:rgba(250,250,250,.9)">
								<div class="row">
									<div class="col-lg-12 col-md-12 col-sm-12 col-12" style="color:#000;">
										<div class="card-body">
											<div class="row">
												<div class="col-lg-3 col-md-9 col-sm-12 col-12">
													<center>
														<img src="<?php echo $foto; ?>" width="100%"/>
													</center>
												</div>
												<div class="col-lg-9 col-md-9 col-sm-12 col-12">
													<h4><?php echo ucwords($judul_buku); ?></h4><hr>
													<p align='justify'>
													<b>Penulis : </b><?php echo "<b><font color='blue'>".$penulis."</font></b>"; ?><br>
													<b>Genre : </b><?php echo "<b><font color='blue'>".$genre."</font></b>"; ?><br>
													<b>Penerbit : </b><?php echo "<b><font color='orange'>".$penulis."</font></b>"; ?><br>
													<hr>
													<a target='_blank' href="<?php echo $link_buku; ?>" class="btn btn-danger btn-action mr-1" title="" data-toggle="tooltip"><i class="fas fa-image"></i> Beli/Baca</a>
													
													<?php if(@$_SESSION["status_pengguna"]==2){
													$sql_cekkk = mysqli_query($conn, "SELECT * FROM favorit where id_pengguna='".@$_SESSION['id_pengguna']."' and id_buku='$id_buku'");
														if(mysqli_num_rows($sql_cekkk)>0){ ?>
															<a href="buku.php?hapus_favorit=<?php echo $id_buku; ?>" title="Hapus favorit" data-toggle="tooltip"><img src="assets/img/favorit.png" height="35px"/></a>
														<?php }else{ ?>
															<a href="buku.php?favorit=<?php echo $id_buku; ?>" title="Tambahkan favorit" data-toggle="tooltip"><img src="assets/img/non_favorit.png" height="35px"/></a>
														<?php } ?>
														<span data-target="#addulasan" data-toggle="modal" data-id_buku="<?php echo $row["id_buku"]; ?>" data-judul_buku="<?php echo $row["judul_buku"]; ?>" data-tahun_terbit="<?php echo $row["tahun_terbit"]; ?>" data-rating="<?php echo $row["rating"]; ?>" data-penulis="<?php echo $row["penulis"]; ?>" data-link_buku="<?php echo $row["link_buku"]; ?>" data-foto="<?php echo $foto; ?>">
															<a class="btn btn-warning btn-action mr-1" title="" data-toggle="tooltip"><i class="fas fa-star"></i> Berikan Ulasan</a>
														</span>
													<?php } ?>
													<?php 
													if(@$_SESSION["status_pengguna"]==""){ ?>
														<a target='_blank' href="auth/" class="btn btn-warning btn-action mr-1" title="" data-toggle="tooltip"><i class="fas fa-star"></i> Berikan Ulasan</a>
													<?php } ?>
													<hr>

													<b>Sinopsis : </b><?php echo "<b><font color='#574513'>".$keterangan."</font></b>"; ?><br>&nbsp;<br>
												</div>
											</div>
												
												<hr>

												<div class="resp-sharing">
													<!-- Sharingbutton Facebook -->
													<a class="resp-sharing-button__link" href='https://www.facebook.com/sharer/sharer.php?u=<?php echo "buku.php?$id_buku"; ?>' target="_blank" rel="noopener" aria-label="Facebook" title='Share on Facebook'>
														<div class="btn btn-" style="background-color:#3B5998">
															<img src="assets/img/facebook.png" height="20px"/> 
															<span>Facebook</span>
														</div>
													</a>

													<a class="resp-sharing-button__link" href="https://twitter.com/intent/tweet?text=YourTextHere&amp;url=YourUrlHere" target="_blank" rel="noopener" aria-label="Twitter" title='Share on Twitter'>
														<div class="btn btn-" style="background-color:#55ACEE">
															<img src="assets/img/twitter.png" height="20px"/> 
															<span>Twitter</span>
														</div>
														</a>

													<a class="resp-sharing-button__link" href="https://www.instagram.com/YourProfileHere/" target="_blank" rel="noopener" aria-label="Instagram" title='Share on Instagram'>
														<div class="btn btn-" style="background-color:#fbad50">
															<img src="assets/img/instagram.png" height="20px"/> 
															<span>Instagram</span>
														</div>
													</a>
													<!-- Sharingbutton WhatsApp -->
													<a class="resp-sharing-button__link" href='https://web.whatsapp.com//send?text=<?php echo $server."buku.php?id_buku=".$id_buku; ?>' target="_blank" rel="noopener" aria-label="WhatsApp" title='Share on WhatsApp'>
														<div class="btn btn-" style="background-color:#25D366">
															<img src="assets/img/whatsapp.png" height="20px"/> 
															<span>WhatsApp</span>
														</div>
													</a>
												</div>
										</div>
										<div class="card">
													<div class="col-lg-12 col-md-12 col-sm-12 col-12" style="background-color:rgba(0,0,0,.7)">
														<br>
													  <div class="card card-statistic-1">
														<div class="card-wrap">
														  <div class="card-body">
															<font color='black' size='+2'>Ulasan</font>
														  </div>
														</div>
													  </div>
													</div>
											<div class="card-body" style="background-color:rgba(0,0,0,.7)">
												<?php 
												$cek_ulas=mysqli_query($conn, "SELECT * FROM ulasan join buku on ulasan.id_buku=buku.id_buku join pengguna on ulasan.id_pengguna=pengguna.id_pengguna where ulasan.id_buku='".$_GET["id_buku"]."'");
												if(mysqli_num_rows($cek_ulas)>0){ ?>
													
													<?php while($data_ulas=mysqli_fetch_array($cek_ulas)){
														$nama=$data_ulas["nama_pengguna"];
														$tanggal=$data_ulas["tanggal"];
														$nilai=$data_ulas["nilai"];
														$str="<img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  ";
														if($nilai==1){
														   $str="<img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  ";
														}elseif($nilai==2){
														  $str="<img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  ";
														}elseif($nilai==3){
														  $str="<img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  ";
														}elseif($nilai==4){
														  $str="<img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star_off.png' height='16px' style='border:solid 0px red;margin:0'/>
															  ";
														}elseif($nilai==5){
														  $str="<img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  <img src='assets/img/star.png' height='16px' style='border:solid 0px red;margin:0'/>
															  ";
														}
														$ulasan=$data_ulas["ulasan"];
														?>
														<div class="col-lg-12 col-md-12 col-sm-12 col-12">
														  <div class="card card-statistic-1">
															<div class="card-wrap">
															  <div class="card-body">
																<p align='justify'>&nbsp;<br>
																<?php echo $nama; ?> &nbsp;[ <?php echo date("d M Y",strtotime($tanggal)); ?> ] &nbsp; [ <?php echo $str; ?> ]<hr>
																<?php echo str_replace("\n","<br />", $ulasan); ?></p>
															  </div>
															</div>
														  </div>
														</div>
													<?php
													} 
												} ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
				<?php } ?>
			</div>
			<?php }else{
			?>
			<div class="row">		
				<?php
				$sql = mysqli_query($conn, "SELECT * FROM buku order by rating desc");
				if(@$_GET["genre"]){
					$sql = mysqli_query($conn, "SELECT * FROM buku where genre='".$_GET["genre"]."' order by rating desc");
				}elseif(@$_GET["cari_buku"]){
					$sql = mysqli_query($conn, "SELECT * FROM buku where genre like '%".$_GET["cari_buku"]."%' or penulis like '%".$_GET["cari_buku"]."%' or judul_buku like '%".$_GET["cari_buku"]."%' order by rating desc");
					if(@$_SESSION["status_pengguna"]==2){
						$kata_kunci=$_GET["cari_buku"];
						$id_pengguna = @$_SESSION['id_pengguna'];
						$tanggal = date("Y-m-d");	
						$cek_ada = mysqli_query($conn, "SELECT * from riwayat_pencarian where id_pengguna='$id_pengguna' and id_buku='0'");
						if(mysqli_num_rows($cek_ada)>0){
							$add = mysqli_query($conn, "DELETE FROM riwayat_pencarian where id_pengguna='$id_pengguna' and id_buku='0'");
						}
						$dlt = mysqli_query($conn, "INSERT INTO riwayat_pencarian (tanggal, id_pengguna, id_buku, kata_kunci) VALUES ('$tanggal', '$id_pengguna', '0', '$kata_kunci')");	
					}
				}
				if(mysqli_num_rows($sql)<=0){
				?>
					<div class="col-lg-12 col-md-12 col-sm-12 col-12">
							<div class="section-body">
								<div class="row">
									<div class="col-12">
										<div class="card">
											<div class="card-body">
												<center><h5>Masih Kosong!</h5></center>
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
												
												<?php if(@$_SESSION["status_pengguna"]==1){ ?>
														<span data-target="#editbuku" data-toggle="modal" data-id_buku="<?php echo $row["id_buku"]; ?>" data-judul_buku="<?php echo $row["judul_buku"]; ?>" data-genre="<?php echo $row["genre"]; ?>" data-tahun_terbit="<?php echo $row["tahun_terbit"]; ?>" data-rating="<?php echo $row["rating"]; ?>" data-penulis="<?php echo $row["penulis"]; ?>" data-link_buku="<?php echo $row["link_buku"]; ?>" data-foto="<?php echo $foto; ?>">
														  <a class="btn btn-primary btn-action mr-1" title="Ubah" data-toggle="tooltip"><i class="fas fa-pencil-alt"></i></a>
														</span>
														<span data-target="#hapusbuku" data-toggle="modal" data-id_buku="<?php echo $row["id_buku"]; ?>" data-judul_buku="<?php echo $row["judul_buku"]; ?>" data-genre="<?php echo $row["genre"]; ?>" data-tahun_terbit="<?php echo $row["tahun_terbit"]; ?>" data-rating="<?php echo $row["rating"]; ?>" data-penulis="<?php echo $row["penulis"]; ?>" data-link_buku="<?php echo $row["link_buku"]; ?>" data-foto="<?php echo $foto; ?>">
														  <a class="btn btn-danger btn-action mr-1" title="Hapus" data-toggle="tooltip"><i class="fas fa-trash"></i></a>
														</span>
												<?php } ?>
												
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		<?php } ?>
        </section>
      </div>

      <div class="modal fade" tabindex="-1" role="dialog" id="addbuku">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Tambah Buku</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="" method="POST" class="needs-validation" novalidate="" autocomplete="off" enctype="multipart/form-data" >
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Tahun Terbit</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="tahun_terbit" required="" id="gettahun_terbit">
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Judul Buku</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="judul_buku" required="" id="getjudul_buku">
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Genre</label>
                  <div class="col-sm-9">
						<select class="form-control" name="genre" required="">
							<option value="">-Pilih-</option>
							<option value="Fiction">Fiction</option>
							<option value="Science">Science</option>
							<option value="History">History</option>
							<option value="Fantasy">Fantasy</option>
							<option value="Mistery">Mistery</option>
							<option value="Romance">Romance</option>
							<option value="Thriller">Thriller</option>
						</select>
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Penulis</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="penulis" required="" id="getpenulis"/>
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Rating</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="rating" required="" id="getrating">
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Link Buku</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="link_buku" required="" id="getlink_buku">
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Cover Buku</label>
                  <div class="col-sm-9">
                    <input type="file" class="form-control" name="foto" required="" accept="image/*">
                    <div class="invalid-feedback">
                      Mohon masukkan foto/gambar!
                    </div>
                  </div>
                </div>
            </div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit2">Tambah</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" tabindex="-1" role="dialog" id="editbuku">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Data</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="" method="POST" class="needs-validation" novalidate="" enctype="multipart/form-data" >
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Tahun Terbit</label>
                  <div class="col-sm-9">
                    <input type="hidden" class="form-control" name="id_buku" required="" id="getid_buku">
                    <input type="text" class="form-control" name="tahun_terbit" required="" id="gettahun_terbit">
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">judul_buku</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="judul_buku" required="" id="getjudul_buku">
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Genre</label>
                  <div class="col-sm-9">
						<select class="form-control" name="genre" required="" id="getgenre">
							<option value="">-Pilih-</option>
							<option value="Fiction">Fiction</option>
							<option value="Science">Science</option>
							<option value="History">History</option>
							<option value="Fantasy">Fantasy</option>
							<option value="Mistery">Mistery</option>
							<option value="Romance">Romance</option>
							<option value="Thriller">Thriller</option>
						</select>
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Penulis</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" name="penulis" required="" id="getpenulis"></textarea>
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Rating</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="rating" required="" id="getrating">
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Link Nonton</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="link_buku" required="" id="getlink_buku">
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Foto</label>
                  <div class="col-sm-9">
					<img id="foto_edit" src="" width="100%"/>
                    <input type="file" class="form-control" name="foto" accept="image/*">
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
			</div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-danger" name="submit">Edit</button>
              </form>
            </div>
          </div>
        </div>
      </div>
	  
	  <div class="modal fade" tabindex="-1" role="dialog" id="hapusbuku">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Hapus Data</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-header">
              <h5 class="modal-title">Apakah anda ingin menghapus data ini?</h5>
            </div>
            <div class="modal-body">
              <form action="" method="POST" class="needs-validation" novalidate="" enctype="multipart/form-data" >
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Tahun Terbit</label>
                  <div class="col-sm-9">
                    <input type="hidden" class="form-control" name="id_buku" required="" id="getid_buku">
                    <input type="date" class="form-control" name="tahun_terbit" id="gettahun_terbit" readonly>
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">judul_buku</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="judul_buku" id="getjudul_buku" readonly>
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Genre</label>
                  <div class="col-sm-9">
					<input type="text" class="form-control" name="genre" id="getgenre" readonly>
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Penulis</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" name="penulis" id="getpenulis" readonly></textarea>
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Rating</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="rating" id="getrating" readonly>
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Link Nonton</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="link_buku" required="" id="getlink_buku" readonly>
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Foto</label>
                  <div class="col-sm-9">
					<img id="foto_hapus" src="" width="100%"/>
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
			</div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
              <button type="submit3" class="btn btn-danger" name="submit3">Ya</button>
              </form>
            </div>
          </div>
        </div>
      </div>
	  
	  <div class="modal fade" tabindex="-1" role="dialog" id="addulasan">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Berikan Ulasan</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form action="" method="POST" class="needs-validation" novalidate="" enctype="multipart/form-data" >
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Judul Buku</label>
                  <div class="col-sm-9">
                    <input type="hidden" class="form-control" name="id_buku" required="" id="getid_buku">
                    <input type="text" class="form-control" name="judul_buku" id="getjudul_buku" readonly>
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Foto</label>
                  <div class="col-sm-9">
					<img id="foto_ulasan" src="" width="100%"/>
                    <div class="invalid-feedback">
                      Mohon data dipenulis!
                    </div>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-sm-3 col-form-label">Nilai</label>
                  <div class="col-sm-9">
					<select class="form-control" name="nilai" required="">
						<option value="">-Pilih-</option>
						<option value="5">5</option>
						<option value="4">4</option>
						<option value="3">3</option>
						<option value="2">2</option>
						<option value="1">1</option>
					</select>
                  </div>
                </div>
				<div class="form-group row">
                  <label class="col-sm-3 col-form-label">Ulasan</label>
                  <div class="col-sm-9">
                    <textarea type="text" class="form-control" name="ulasan" required="" id="getulasan"></textarea>
                    <div class="invalid-feedback">
                      Mohon isi data ini!
                    </div>
                  </div>
                </div>
			</div>
            <div class="modal-footer bg-whitesmoke br">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-danger" name="submit4">Kirim</button>
              </form>
            </div>
          </div>
        </div>
      </div>
	 
      <?php include 'part/footer.php'; ?>
    </div>
  </div>
  <?php include "part/all-js.php"; ?>

  <script>
    $('#editbuku').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget)
      var id_buku = button.data('id_buku')
      var tahun_terbit = button.data('tahun_terbit')
      var judul_buku = button.data('judul_buku')
      var genre = button.data('genre')
	  var penulis = button.data('penulis')
      var link_buku = button.data('link_buku')
      var rating = button.data('rating')
      var foto = button.data('foto')
      var modal = $(this)
      modal.find('#getid_buku').val(id_buku)
      modal.find('#gettahun_terbit').val(tahun_terbit)
      modal.find('#getjudul_buku').val(judul_buku)
	  modal.find('#getgenre').val(genre)
      modal.find('#getpenulis').val(penulis)
      modal.find('#getlink_buku').val(link_buku)
      modal.find('#getrating').val(rating)
	  document.getElementById('foto_edit').src=foto;	  
    })
  </script>
  <script>
    $('#hapusbuku').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget)
      var id_buku = button.data('id_buku')
      var tahun_terbit = button.data('tahun_terbit')
      var judul_buku = button.data('judul_buku')
      var genre = button.data('genre')
	  var penulis = button.data('penulis')
      var link_buku = button.data('link_buku')
      var rating = button.data('rating')
      var foto = button.data('foto')
      var modal = $(this)
      modal.find('#getid_buku').val(id_buku)
      modal.find('#gettahun_terbit').val(tahun_terbit)
      modal.find('#getjudul_buku').val(judul_buku)
	  modal.find('#getgenre').val(genre)
      modal.find('#getpenulis').val(penulis)
      modal.find('#getlink_buku').val(link_buku)
      modal.find('#getrating').val(rating)
	  document.getElementById('foto_hapus').src=foto;	  
    })
  </script>
  <script>
    $('#addulasan').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget)
      var id_buku = button.data('id_buku')
      var judul_buku = button.data('judul_buku')
      var foto = button.data('foto')
      var modal = $(this)
      modal.find('#getid_buku').val(id_buku)
      modal.find('#getjudul_buku').val(judul_buku)
	  document.getElementById('foto_ulasan').src=foto;	  
    })
  </script>
  </body>

</html>