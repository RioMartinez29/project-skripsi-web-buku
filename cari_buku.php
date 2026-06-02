<?php
if(@$_POST["cari_buku"]){ 
	$cari_buku=$_POST["cari_buku"];
	echo '<script>
		document.location="buku.php?cari_buku='.$cari_buku.'";
	</script>';
}
if(@$_POST["genre"]){ 
	$genre=$_POST["genre"];
	if($genre=="Semua"){
		echo '<script>
			document.location="buku.php";
		</script>';
	}else{
		echo '<script>
			document.location="buku.php?genre='.$genre.'";
		</script>';
	}
}
?>
<form action="" method="POST" class="needs-validation" novalidate="" autocomplete="off" enctype="multipart/form-data" >																							 
		<div class="btn-group">
			<a href="#" class="btn btn-primary" data-toggle="dropdown"><i class="fas fa-list"> </i> Genre</a>  
				<div class="dropdown-menu dropdown-menu">
					<a href="buku.php?genre=Fiction" class="dropdown-item has-icon text-primary"><font color="<?php if(@$_GET["genre"]=="Fiction"){ echo "red;";} ?>">Fiction</font></a>
					<a href="buku.php?genre=Science" class="dropdown-item has-icon text-primary"><font color="<?php if(@$_GET["genre"]=="Science"){ echo "red;";} ?>">Science</font></a>
					<a href="buku.php?genre=History" class="dropdown-item has-icon text-primary"><font color="<?php if(@$_GET["genre"]=="History"){ echo "red;";} ?>">History</font></a>
					<a href="buku.php?genre=Fantasy" class="dropdown-item has-icon text-primary"><font color="<?php if(@$_GET["genre"]=="Fantasy"){ echo "red;";} ?>">Fantasy</font></a>
					<a href="buku.php?genre=Mistery" class="dropdown-item has-icon text-primary"><font color="<?php if(@$_GET["genre"]=="Mistery"){ echo "red;";} ?>">Mistery</font></a>
					<a href="buku.php?genre=Romance" class="dropdown-item has-icon text-primary"><font color="<?php if(@$_GET["genre"]=="Romance"){ echo "red;";} ?>">Romance</font></a>
					<a href="buku.php?genre=Thriller" class="dropdown-item has-icon text-primary"><font color="<?php if(@$_GET["genre"]=="Thriller"){ echo "red;";} ?>">Thriller</font></a>	
				</div>
			</label>
			<input type="text" class="btn btn-" name="cari_buku" required="" placeholder="Cari buku" value="<?php if (@$_GET["cari_buku"]){ echo @$_GET["cari_buku"]; } if (@$_GET["genre"]){ echo @$_GET["genre"]; } ?>">
			<button type="submit" class="btn btn-warning" name="submit_cari"><i class="fas fa-search"></i> </button>
			<a href="buku.php" class="btn btn-danger" name="submit_cari"><i class="fas fa-"></i> Semua </a>
			<?php if(@$_SESSION["status_pengguna"]==1){ ?>
				<span data-target="#addbuku" data-toggle="modal" class="btn btn-primary">
				  <a data-toggle="modal" data-toggle="tooltip"><i class="fas fa-plus"></i>  Tambah</a>
				</span>
			<?php } ?>
				<a href="buku.php?sinkron_google_api=1" class="btn btn-warning" name="submit_cari"><i class="fas fa-"></i> Sinkron Google BOOK API </a>
		</div>
</form>