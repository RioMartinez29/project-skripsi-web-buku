<!DOCTYPE html>
<html lang="en">

<head>
	<?php
	$page = "Pengguna";
	session_start();
	include 'auth/connect.php';
	include "part/head.php";
	
	if (isset($_POST['submit3'])) {
		$id_pengguna= $_POST['id_pengguna'];
		$up2 = mysqli_query($conn, "DELETE FROM pengguna WHERE id_pengguna='$id_pengguna'");
		echo '<script>
			setTimeout(function() {
				swal({
					title: "Berhasil!",
					text: "Data pengguna telah dihapus!",
					icon: "success"
					});
				}, 800);
		</script>';
	}
	if (isset($_POST['submit'])) {
		$id_pengguna = $_POST['id_pengguna'];
		$nama_pengguna = $_POST['nama_pengguna'];
		$email = $_POST['email'];
		$old_pass = $_POST['old_password'];
		$new_pass = $_POST['new_password'];

		if ($old_pass == "" && $new_pass == "") {
			$up1 = mysqli_query($conn, "UPDATE pengguna SET nama_pengguna='$nama_pengguna', email='$email' WHERE id_pengguna='$id_pengguna'");
			echo '<script>
			setTimeout(function() {
				swal({
					title: "Data Diubah",
					text: "Data berhasil diubah!",
					icon: "success"
					});
					}, 800);
					</script>';
		} elseif ($old_pass != "" && $new_pass != "") {
			$cekpass = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna='$id_pengguna' AND password='$old_pass'");
			$cekada = mysqli_num_rows($cekpass);
			if ($cekada == 0) {
				echo '<script>
						setTimeout(function() {
							swal({
								title: "Password salah",
								text: "Password salah, cek kembali form password anda!",
								icon: "error"
								});
								}, 800);
								</script>';
			} else {
				$up2 = mysqli_query($conn, "UPDATE pengguna SET nama_pengguna='$nama_pengguna', email='$email', password='$new_pass' WHERE id_pengguna='$id_pengguna'");
				echo '<script>
				setTimeout(function() {
					swal({
					title: "Data Diubah",
					text: "Data atau Password berhasil diubah!",
					icon: "success"
					});
					}, 800);
				</script>';
			}
		}
	}
	

	if (isset($_POST['submit2'])) {
		$nama_pengguna = $_POST['nama_pengguna'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$status_pengguna = $_POST['status_pengguna'];

		$cekemail = mysqli_query($conn, "SELECT * FROM pengguna WHERE email='$email'");
		$baris = mysqli_num_rows($cekemail);
		if ($baris >= 1) {
			echo '<script>
				setTimeout(function() {
					swal({
						title: "Email sudah digunakan",
						text: "Email sudah digunakan, gunakan email lain!",
						icon: "error"
						});
					}, 800);
			</script>';
		} else {
			$add = mysqli_query($conn, "INSERT INTO pengguna (email, password, nama_pengguna, status_pengguna) VALUES ('$email', '$password', '$nama_pengguna', '$status_pengguna')");
			echo '<script>
				setTimeout(function() {
					swal({
						title: "Berhasil!",
						text: "Pengguna telah ditambahkan!",
						icon: "success"
						});
					}, 800);
			</script>';
		}
	}
	
	if (isset($_POST['submit_reset'])) {
		$id_pengguna = $_POST['id_pengguna'];
		$password="12345";
		$up2 = mysqli_query($conn, "UPDATE pengguna SET password='$password' WHERE id='$id_pengguna'");
		echo '<script>
			setTimeout(function() {
				swal({
					title: "Berhasil!",
					text: "Password pengguna telah direset!",
					icon: "success"
					});
				}, 500);
		</script>';
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
		<div class="main-content">
			<section class="section">&nbsp;
				<div class="section-body">
					<div class="row">
						<div class="col-12">
							<div class="card">
								<div class="card-header">
									<h4><?php echo $page; ?></h4>
									<div class="card-header-action">
										<a href="#" class="btn btn-primary" data-target="#addemail" data-toggle="modal"><i class="fas fa-plus"></i> Tambah</a>
									</div>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table table-striped" id="table-1">
											<thead>
												<tr>
													<th class="text-center">
														#
													</th>
													<th>Email</th>
													<th>Nama Pengguna</th>
													<th>Status</th>
													<th>Aksi</th>
												</tr>
											</thead>
											<tbody>
												<?php
												$sql = mysqli_query($conn, "SELECT * FROM pengguna");
												$i = 0;
												while ($row = mysqli_fetch_array($sql)) {
													$i++;
												?>
													<tr>
														<td><?php echo $i; ?></td>
														<td><?php echo ucwords($row['email']); ?></td>
														<td><?php echo ucwords($row['nama_pengguna']); ?></td>
														<td><?php
															if ($row['status_pengguna'] == '1') {
																echo '<div class="badge badge-pill badge-primary mb-1">Admin</div>';
															} else {
																echo '<div class="badge badge-pill badge-success mb-1">Pengunjung</div>';
															} ?>										
														</td>
														<td>
															<?php if (@$_SESSION['status_pengguna_email']==2){ ?>
																<span data-target="#resetPassword" data-toggle="modal" data-id_pengguna="<?php echo $row['id_pengguna']; ?>" data-nama_pengguna="<?php echo $row['nama_pengguna']; ?>"  data-email="<?php echo $row['email']; ?>" data-password="<?php echo $row['password']; ?>">
																	<a class="btn btn-primary btn-action mr-1" title="Reset Password" data-toggle="tooltip"><i class="fas fa-undo"></i></a>
																</span>
															<?php }?>
															<span data-target="#editpengguna" data-toggle="modal" data-id_pengguna="<?php echo $row['id_pengguna']; ?>" data-nama_pengguna="<?php echo $row['nama_pengguna']; ?>" data-email="<?php echo $row['email']; ?>">
																<a class="btn btn-primary btn-action mr-1" title="Edit" data-toggle="tooltip"><i class="fas fa-pencil-alt"></i></a>
															</span>
															<?php if ($row['id_pengguna']!=@$_SESSION['id_pengguna']){ ?>
															<span data-target="#hapuspengguna" data-toggle="modal" data-id_pengguna="<?php echo $row['id_pengguna']; ?>" data-nama_pengguna="<?php echo $row['nama_pengguna']; ?>" data-email="<?php echo $row['email']; ?>"
																<a class="btn btn-danger btn-action mr-1" title="Hapus" data-toggle="tooltip"><i class="fas fa-trash"></i></a>
															</span>
															<?php } ?>
														</td>
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
			</section>
		</div>

		<div class="modal fade" tabindex="-1" role="dialog" id="addemail">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Tambah Pengguna</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form action="" method="POST" class="needs-validation" novalidate="">
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Nama Pengguna</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="nama_pengguna" required="">
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Email</label>
								<div class="col-sm-8">
									<input type="email" class="form-control" name="email" required="">
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Status</label>
								<div class="col-sm-8">
									<select class="form-control" name="status_pengguna" required="">
										<option value="">-Pilih-</option>
										<option value="1">Admin</option>
										<option value="2">Pengunjung</option>
									</select>
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Password</label>
								<div class="col-sm-8">
									<input type="password" name="password" id="getPassword" class="form-control" onfocus="unlockpassword()" onfocusout="lockpassword()" required="">
									<script>
										function unlockpassword(){
												document.getElementById('getPassword').type='text';
										}
										function lockpassword(){
												document.getElementById('getPassword').type='password';
										} 
									</script>
								</div>
							</div>
					</div>
					<div class="modal-footer bg-whitesmoke br">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-primary" name="submit2">Tambah</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" tabindex="-1" role="dialog" id="editpengguna">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Edit Data</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form action="" method="POST" class="needs-validation" novalidate="">
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Nama Pengguna</label>
								<div class="col-sm-8">
									<input type="hidden" class="form-control" name="id_pengguna" required="" id="getid_pengguna">
									<input type="text" class="form-control" name="nama_pengguna" required="" id="getnama_pengguna">
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Email</label>
								<div class="col-sm-8">
									<input type="email" class="form-control" name="email" required="" id="getemail">
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
							<div class="alert alert-light text-center">
								Jika password tidak diganti, form dibawah dikosongi saja.
							</div>
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Password Lama</label>
								<div class="col-sm-8">
									<input type="password" name="old_password" name="old_password" id="old_password1" class="form-control" onfocus="unlockpassword1()" onfocusout="lockpassword1()" >
									<script>
										function unlockpassword1(){
												document.getElementById('old_password1').type='text';
										}
										function lockpassword1(){
												document.getElementById('old_password1').type='password';
										} 
									</script>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-4 col-form-label">Password Baru</label>
								<div class="col-sm-8">
									<input type="password" name="new_password" name="new_password"  id="new_password1" class="form-control" onfocus="unlockpassword2()" onfocusout="lockpassword2()" >
									<script>
									function unlockpassword2(){
										document.getElementById('new_password1').type='text';
									} 
									function lockpassword2(){
										document.getElementById('new_password1').type='password';
									} 
								</script>
								</div>
							</div>
					</div>
					<div class="modal-footer bg-whitesmoke br">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-primary" name="submit">Edit</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		
		<div class="modal fade" tabindex="-1" role="dialog" id="hapuspengguna">
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
						<form action="" method="POST" class="needs-validation" novalidate="">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Nama Pengguna</label>
								<div class="col-sm-9">
									<input type="hidden" class="form-control" name="id_pengguna" required="" id="getid_pengguna">
									<input type="text" class="form-control" name="nama_pengguna" required="" id="getnama_pengguna" readonly>
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Email</label>
								<div class="col-sm-9">
									<input type="email" class="form-control" name="email" required="" id="getemail" readonly>
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
					</div>
					<div class="modal-footer bg-whitesmoke br">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
						<button type="submit" class="btn btn-primary" name="submit3">Ya</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		
		<div class="modal fade" tabindex="-1" role="dialog" id="resetPassword">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Reset Password</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form action="" method="POST" class="needs-validation" novalidate="">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Nama Pengguna</label>
								<div class="col-sm-9">
									<input type="hidden" class="form-control" name="id_pengguna" required="" id="getid_pengguna">
									<input type="text" class="form-control" name="nama_pengguna" required="" id="getnama_pengguna" readonly>
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Email</label>
								<div class="col-sm-9">
									<input type="text" class="form-control" name="email" required="" id="getemail" readonly>
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Password</label>
								<div class="col-sm-9">
									<input type="password" class="form-control" name="password" id="getPassword3" onfocus="unlockpassword3()" onfocusout="lockpassword3()" readonly>
									<script>
										function unlockpassword3(){
												document.getElementById('getPassword3').type='text';
										}
										function lockpassword3(){
												document.getElementById('getPassword3').type='password';
										} 
									</script>
									<div class="invalid-feedback">
										Mohon data diisi!
									</div>
								</div>
							</div>
					</div>
					<div class="modal-footer bg-whitesmoke br">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-primary" name="submit_reset">Reset</button>
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
		$('#editpengguna').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget)
			var nama_pengguna = button.data('nama_pengguna')
			var email = button.data('email')
			var id_pengguna = button.data('id_pengguna')
			var modal = $(this)
			modal.find('#getid_pengguna').val(id_pengguna)
			modal.find('#getnama_pengguna').val(nama_pengguna)
			modal.find('#getemail').val(email)
		})
	</script>
		<script>
		$('#hapuspengguna').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget)
			var nama_pengguna = button.data('nama_pengguna')
			var email = button.data('email')
			var id_pengguna = button.data('id_pengguna')
			var modal = $(this)
			modal.find('#getid_pengguna').val(id_pengguna)
			modal.find('#getnama_pengguna').val(nama_pengguna)
			modal.find('#getemail').val(email)
		})
	</script>
	<script>
		$('#resetPassword').on('show.bs.modal', function(event) {
			var button = $(event.relatedTarget)
			var nama_pengguna = button.data('nama_pengguna')
			var email = button.data('email')
			var password = button.data('password')
			var id_pengguna = button.data('id_pengguna')
			var modal = $(this)
			modal.find('#getid_pengguna').val(id_pengguna)
			modal.find('#getnama_pengguna').val(nama_pengguna)
			modal.find('#getemail').val(email)
			modal.find('#getPassword3').val(password)
		})
	</script>
</body>

</html>