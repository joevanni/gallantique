<?php
	include_once('includes/connect_database.php'); 
?>

<div id="content">
	<?php 
		
		if(isset($_POST['btnDelete'])){
			if(isset($_GET['id'])){
				$ID = $_GET['id'];
			}else{
				$ID = "";
			}
		
			// get image file from product table
			$sql_query = "SELECT Menu_image 
					FROM tbl_menu
					WHERE Menu_ID = ?";
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $ID);
				// Execute query
				$stmt->execute();
				// store result 
				$stmt->store_result();
				$stmt->bind_result($menu_image);
				$stmt->fetch();
				$stmt->close();
			}
			
			// delete image file from directory
			$delete = unlink("$menu_image");
			
			// delete data from product table
			$sql_query = "DELETE FROM tbl_menu
					WHERE Menu_ID = ?";
			
			$stmt = $connect->stmt_init();
			if($stmt->prepare($sql_query)) {	
				// Bind your variables to replace the ?s
				$stmt->bind_param('s', $ID);
				// Execute query
				$stmt->execute();
				// store result 
				$delete_result = $stmt->store_result();
				$stmt->close();
			}
				
			// if delete data success back to reservation page
			if($delete_result){
				header("location: product.php");
			}
		}		

		if(isset($_POST['btnNo'])){
			header("location: product.php");
		}

	?>
	<h1>Confirm Action</h1>
	<hr />
	<form method="post" enctype="multipart/form-data" role="form" class="form-horizontal form-groups-bordered">	

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Are you sure want to delete this product?</label>					
			<div class="col-sm-5">
				<input type="submit" class="btn btn-primary" value="Delete" name="btnDelete"/>
				<input type="submit" class="btn btn-danger" value="Cancel" name="btnNo"/>
			</div>
	</div>		
	</form>
	<div class="separator"> </div>
</div>
			
<?php include_once('includes/close_database.php'); ?>