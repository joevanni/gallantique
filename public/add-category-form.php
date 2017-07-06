<?php
	include_once('includes/connect_database.php'); 
	include_once('functions.php'); 
?>
<div id="content">
	<?php 
		if(isset($_POST['btnAdd'])){
			$category_name = $_POST['category_name'];
			
			// get image info
			$menu_image = $_FILES['category_image']['name'];
			$image_error = $_FILES['category_image']['error'];
			$image_type = $_FILES['category_image']['type'];
			
			// create array variable to handle error
			$error = array();
			
			if(empty($category_name)){
				$error['category_name'] = " <span class='label label-danger'>Required!</span>";
			}
			
			// common image file extensions
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			
			// get image file extension
			error_reporting(E_ERROR | E_PARSE);
			$extension = end(explode(".", $_FILES["category_image"]["name"]));
					
			if($image_error > 0){
				$error['category_image'] = " <span class='label label-danger'>Not Uploaded!!</span>";
			}else if(!(($image_type == "image/gif") || 
				($image_type == "image/jpeg") || 
				($image_type == "image/jpg") || 
				($image_type == "image/x-png") ||
				($image_type == "image/png") || 
				($image_type == "image/pjpeg")) &&
				!(in_array($extension, $allowedExts))){
			
				$error['category_image'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
			}
			
			if(!empty($category_name) && empty($error['category_image'])){
				
				// create random image file name
				$string = '0123456789';
				$file = preg_replace("/\s+/", "_", $_FILES['category_image']['name']);
				$function = new functions;
				$menu_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
					
				// upload new image
				$upload = move_uploaded_file($_FILES['category_image']['tmp_name'], 'upload/images/'.$menu_image);
		
				// insert new data to menu table
				$sql_query = "INSERT INTO tbl_category (Category_name, Category_image)
						VALUES(?, ?)";
				
				$upload_image = 'upload/images/'.$menu_image;
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('ss', 
								$category_name, 
								$upload_image
								);
					// Execute query
					$stmt->execute();
					// store result 
					$result = $stmt->store_result();
					$stmt->close();
				}
				
				if($result){
					$error['add_category'] = " <h4><div class='alert alert-success'>
														* Success add new category
														<a href='category.php'>
														<i class='fa fa-check fa-lg'></i>
														</a></div>
												  </h4>";
				}else{
					$error['add_category'] = " <span class='label label-danger'>Failed add category</span>";
				}
			}
			
		}

		if(isset($_POST['btnCancel'])){
			header("location: category.php");
		}

	?>
	<div class="col-md-12">

	<h1>Add Category</h1>
	<hr />
	<form method="post" enctype="multipart/form-data" role="form" class="form-horizontal form-groups-bordered">	

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Category Name:</label>					
			<div class="col-sm-5">
				<input type="text" name="category_name" class="form-control"/>
				<?php echo isset($error['category_name']) ? $error['category_name'] : '';?>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Image :</label>					
			<div class="col-sm-5">
				<input type="file" name="category_image" class="form-control"/>
				<?php echo isset($error['category_image']) ? $error['category_image'] : '';?>
			</div>
	</div>
	
	<div class="form-group">		
			<div class="col-sm-offset-3 col-sm-5">
				<input class="btn btn-default" type="submit" value="Submit" name="btnAdd" />
				<input class="btn btn-default" type="reset" value="Clear"/>
				<input class="btn btn-default" type="submit" class="btn-danger btn" value="Cancel" name="btnCancel"/>
				<?php echo isset($error['add_category']) ? $error['add_category'] : '';?>			
			</div>
	</div>	
	
	</form>

	<div class="separator"> </div>
</div>
	
<?php include_once('includes/close_database.php'); ?>