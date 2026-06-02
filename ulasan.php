<!DOCTYPE html>
<html lang="en">

<head>
	<?php
	$page = "Ulasan Produk";
	session_start();
	include 'auth/connect.php';
	include "part/head.php";
  ?>
</head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class="navbar-bg"></div>

      <?php
      include 'part/navbar.php';
      include 'part/sidebar.php';
      ?>

      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1><?php echo $page; ?></h1>
          </div>
          <div class="section-body">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h4><?php echo $page; ?></h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="table-0">
                        <thead>
                          <tr>
                            <th>Nama Produk</th>
                            <th>Berat</th>
                            <th>Stok</th>
                            <th>Harga</th>
                            <th>Keterangan</th>
                            <th>Nilai</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
							$sql = mysqli_query($conn, "SELECT * FROM produk where id='".@$_GET["id"]."'");
							$i = 0;
							while ($row = mysqli_fetch_array($sql)) {
								$gambar="assets/img/produk/".$row['id'].".jpg";
								$i++;

							?>
                            <tr>
                              <td><?php echo ucwords($row['nama_produk']) ?><br><img src="<?php echo $gambar; ?>" width="100"></td>
                              <td><?php echo ucwords($row['berat'])." gram"; ?></td>
                              <td><?php echo ucwords($row['stok']) ?></td>
                              <td><?php echo "Rp". number_format($row['harga'],0) ?></td>
                              <td><p align="justify"><?php echo ucwords(str_replace("\n","<br />", $row['keterangan'])) ?></p></td>
                              <td><?php echo ucwords($row['nilai']) ?><img src='assets/img/star.png' height='10px' style='border:solid 0px red;margin:0'/></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
		  <div class="row">
					<?php
					$cek_ulas=mysqli_query($conn, "SELECT * FROM ulasan join penjualan_header on ulasan.nota_penjualan=penjualan_header.nota_penjualan join pelanggan on penjualan_header.id_pelanggan=pelanggan.id WHERE ulasan.id_produk='".$_GET["id"]."'");
					if(mysqli_num_rows($cek_ulas)>0){ ?>
						<div class="col-lg-<?php if (@$_GET["id"]){echo "12"; }else{ echo 3;} ?> col-md-12 col-sm-12 col-12">
								<?php 
								//$id_produk = $row["id_produk"];
						?>
								<div class="section-body">
									<div class="row">
										<div class="col-12">
											<div class="card">
												<div class="row">
													<div class="col-lg-12 col-md-12 col-sm-12 col-12">
													  <div class="card card-statistic-1">
														<div class="card-wrap">
														  <div class="card-header">
															<font color='blue' size='+2'>Ulasan</font>
														  </div>
														</div>
													  </div>
													</div>
													<?php while($data_ulas=mysqli_fetch_array($cek_ulas)){
														$nama=$data_ulas["nama_lengkap"];
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
																<p align='justify'>
																<?php echo $nama; ?> &nbsp;[ <?php echo $tanggal; ?> ] &nbsp; <?php echo $str; ?> <br>
																<?php echo str_replace("\n","<br />", $ulasan); ?></p>
															  </div>
															</div>
														  </div>
														</div>
													<?php
													} ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
					<?php }else{?>
					<div class="col-lg-12 col-md-6 col-sm-6 col-12">
						<div class="section-body">
							<div class="row">
								<div class="col-12">
									<div class="card">
										<div class="card-header">
											<div class="input-group col-sm-12">												  
													<div style="color:red;">
													  Ulasan tidak ditemukan!
													</div>
											  </div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>					
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