<?php
	include_once('includes/connect_database.php'); 
	include_once('functions.php'); 
?>
<div id="content" class="container col-md-12">
	<?php 
		if(isset($_GET['id'])){
			$ID = $_GET['id'];
		}else{
			$ID = "";
		}
		
		// create array variable to store category data
		$category_data = array();
			
		$sql_query = "SELECT Category_image 
				FROM tbl_category 
				WHERE Category_ID = ?";
				
		$stmt_category = $connect->stmt_init();
		if($stmt_category->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt_category->bind_param('s', $ID);
			// Execute query
			$stmt_category->execute();
			// store result 
			$stmt_category->store_result();
			$stmt_category->bind_result($previous_category_image);
			$stmt_category->fetch();
			$stmt_category->close();
		}
		
			
		if(isset($_POST['btnEdit'])){
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
			
			if(!empty($menu_image)){
				if(!(($image_type == "image/gif") || 
					($image_type == "image/jpeg") || 
					($image_type == "image/jpg") || 
					($image_type == "image/x-png") ||
					($image_type == "image/png") || 
					($image_type == "image/pjpeg")) &&
					!(in_array($extension, $allowedExts))){
					
					$error['category_image'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
				}
			}
				
			if(!empty($category_name) && empty($error['category_image'])){
					
				if(!empty($menu_image)){
					
					// create random image file name
					$string = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['category_image']['name']);
					$function = new functions;
					$category_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
				
					// delete previous image
					$delete = unlink("$previous_category_image");
					
					// upload new image
					$upload = move_uploaded_file($_FILES['category_image']['tmp_name'], 'upload/images/'.$category_image);
	  
					$sql_query = "UPDATE tbl_category 
							SET Category_name = ?, Category_image = ?
							WHERE Category_ID = ?";
							
					$upload_image = 'upload/images/'.$category_image;
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('sss', 
									$category_name, 
									$upload_image,
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				}else{
					
					$sql_query = "UPDATE tbl_category 
							SET Category_name = ?
							WHERE Category_ID = ?";
					
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('ss', 
									$category_name, 
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				}
				
				// check update result
				if($update_result){
					$error['update_category'] = " <h4><div class='alert alert-success'>
														Success update category
														<a href='category.php'>
														<i class='fa fa-check fa-lg'></i>
														</a></div>
												  </h4>";
				}else{
					$error['update_category'] = " <span class='label label-danger'>Failed update category</span>";
				}
			}
				
		}
			
		// create array variable to store previous data
		$data = array();
		
		$sql_query = "SELECT * 
				FROM tbl_category 
				WHERE Category_ID = ?";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['Category_ID'], 
					$data['Category_name'],
					$data['Category_image']
					);
			$stmt->fetch();
			$stmt->close();
		}

		if(isset($_POST['btnCancel'])){
			header("location: category.php");
		}
		
	?>
	<div class="col-md-12">
		<h1>Edit Category</h1>
		<?php echo isset($error['update_category']) ? $error['update_category'] : '';?>
		<hr />
	</div>
	
	<form method="post" enctype="multipart/form-data" role="form" class="form-horizontal form-groups-bordered">	

		<div class="form-group">
			<label for="field-1" class="col-sm-3 control-label">Category Name:</label>					
				<div class="col-sm-5">
					<input type="text" class="form-control" name="category_name" value="<?php echo $data['Category_name']; ?>"/>
					<?php echo isset($error['category_name']) ? $error['category_name'] : '';?>
				</div>
		</div>

		<div class="form-group">
			<label for="field-1" class="col-sm-3 control-label">Category Name:</label>					
				<div class="col-sm-5">
					<input class="form-control" type="file" name="category_image" id="category_image" />
					<img src="<?php echo $data['Category_image']; ?>" width="280" height="190"/>
					<?php echo isset($error['category_image']) ? $error['category_image'] : '';?>
				</div>
		</div>

		<div class="form-group">		
				<div class="col-sm-offset-3 col-sm-5">
					<input class="btn-primary btn" type="submit" value="Submit" name="btnEdit" />
					<input class="btn-danger btn" type="btnCancel" value="Cancel"/>
				</div>
		</div>	
		</form>
	</div>

	<div class="separator"> </div>
</div>
	
<?php include_once('includes/close_database.php'); ?>