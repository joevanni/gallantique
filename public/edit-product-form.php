<?php
	include_once('includes/connect_database.php'); 
	include_once('functions.php'); 
?>
<div id="content">
	<?php 
	
		if(isset($_GET['id'])){
			$ID = $_GET['id'];
		}else{
			$ID = "";
		}
		
		// create array variable to store category data
		$category_data = array();
			
		$sql_query = "SELECT Category_ID, Category_name 
				FROM tbl_category 
				ORDER BY Category_ID ASC";
				
		$stmt_category = $connect->stmt_init();
		if($stmt_category->prepare($sql_query)) {	
			// Execute query
			$stmt_category->execute();
			// store result 
			$stmt_category->store_result();
			$stmt_category->bind_result($category_data['Category_ID'], 
				$category_data['Category_name']
				);
				
		}
			
		$sql_query = "SELECT Menu_image FROM tbl_menu WHERE Menu_ID = ?";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($previous_menu_image);
			$stmt->fetch();
			$stmt->close();
		}
		
		
		// get currency symbol from setting table
		$sql_query = "SELECT Value 
				FROM tbl_setting 
				WHERE Variable = 'Currency'";
		
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($currency);
			$stmt->fetch();
			$stmt->close();
		}	
		
		
		if(isset($_POST['btnEdit'])){
			
			$menu_name = $_POST['menu_name'];
			$category_ID = $_POST['category_ID'];
			$price = $_POST['price'];
			$serve_for = $_POST['serve_for'];
			$description = $_POST['description'];
			$quantity = $_POST['quantity'];
			
			// get image info
			$menu_image = $_FILES['menu_image']['name'];
			$image_error = $_FILES['menu_image']['error'];
			$image_type = $_FILES['menu_image']['type'];
				
			// create array variable to handle error
			$error = array();
			
			if(empty($menu_name)){
				$error['menu_name'] = " <span class='label label-danger'>Required!</span>";
			}
				
			if(empty($category_ID)){
				$error['category_ID'] = " <span class='label label-danger'>Required!</span>";
			}				
				
			if(empty($price)){
				$error['price'] = " <span class='label label-danger'>Required!</span>";
			}else if(!is_numeric($price)){
				$error['price'] = " <span class='label label-danger'>Price in number!</span>";
			}

			if(empty($quantity)){
				$error['quantity'] = " <span class='label label-danger'>Required!</span>";
			}else if(!is_numeric($quantity)){
				$error['quantity'] = " <span class='label label-danger'>Quantity in number!</span>";
			}
				
			if(empty($serve_for)){
				$error['serve_for'] = " <span class='label label-danger'>Not choosen</span>";
			}			

			if(empty($description)){
				$error['description'] = " <span class='label label-danger'>Required!</span>";
			}
			
			// common image file extensions
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			
			// get image file extension
			error_reporting(E_ERROR | E_PARSE);
			$extension = end(explode(".", $_FILES["menu_image"]["name"]));
			
			if(!empty($menu_image)){
				if(!(($image_type == "image/gif") || 
					($image_type == "image/jpeg") || 
					($image_type == "image/jpg") || 
					($image_type == "image/x-png") ||
					($image_type == "image/png") || 
					($image_type == "image/pjpeg")) &&
					!(in_array($extension, $allowedExts))){
					
					$error['menu_image'] = "*<span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
				}
			}
			
					
			if(!empty($menu_name) && !empty($category_ID) && !empty($price) && is_numeric($price) &&
				!empty($serve_for) && !empty($description) && empty($error['menu_image']) && !empty($quantity) && is_numeric($quantity)){
				
				if(!empty($menu_image)){
					
					// create random image file name
					$string = '0123456789';
					$file = preg_replace("/\s+/", "_", $_FILES['menu_image']['name']);
					$function = new functions;
					$menu_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
				
					// delete previous image
					$delete = unlink("$previous_menu_image");
					
					// upload new image
					$upload = move_uploaded_file($_FILES['menu_image']['tmp_name'], 'upload/images/'.$menu_image);
	  
					// updating all data
					$sql_query = "UPDATE tbl_menu 
							SET Menu_name = ? , Category_ID = ?, Price = ?, Serve_for = ?, Menu_image = ?, Description = ?, Quantity = ? 
							WHERE Menu_ID = ?";
					
					$upload_image = 'upload/images/'.$menu_image;
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('ssssssss', 
									$menu_name, 
									$category_ID, 
									$price, 
									$serve_for, 
									$upload_image,
									$description,
									$quantity,
									$ID);
						// Execute query
						$stmt->execute();
						// store result 
						$update_result = $stmt->store_result();
						$stmt->close();
					}
				}else{
					
					// updating all data except image file
					$sql_query = "UPDATE tbl_menu 
							SET Menu_name = ? , Category_ID = ?, 
							Price = ?, Serve_for = ?, Description = ?, Quantity = ? 
							WHERE Menu_ID = ?";
							
					$stmt = $connect->stmt_init();
					if($stmt->prepare($sql_query)) {	
						// Bind your variables to replace the ?s
						$stmt->bind_param('sssssss', 
									$menu_name, 
									$category_ID, 
									$price, 
									$serve_for, 
									$description,
									$quantity,
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
					$error['update_data'] = " <span class='label label-primary'>Success update</span>";
				}else{
					$error['update_data'] = " <span class='label label-danger'>failed update</span>";
				}
			}
			
		}
		
		// create array variable to store previous data
		$data = array();
			
		$sql_query = "SELECT * FROM tbl_menu WHERE Menu_ID = ?";
			
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			$stmt->bind_param('s', $ID);
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['Menu_ID'], 
					$data['Menu_name'], 
					$data['Category_ID'], 
					$data['Price'], 
					$data['Serve_for'], 
					$data['Menu_image'],
					$data['Description'],
					$data['Quantity']
					);
			$stmt->fetch();
			$stmt->close();
		}
		
			
	?>
	<div class="col-md-12">
	<h1>Edit Product <?php echo isset($error['update_data']) ? $error['update_data'] : '';?></h1>
	<hr />
	</div>
	
	<form method="post" enctype="multipart/form-data" role="form" class="form-horizontal form-groups-bordered">	

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Product Name:</label>					
			<div class="col-sm-5">
				<input type="text" name="menu_name" class="form-control" value="<?php echo $data['Menu_name']; ?>"/>
				<?php echo isset($error['menu_name']) ? $error['menu_name'] : '';?>
			</div>
	</div>		
	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Price (<?php echo $currency;?>) :</label>					
			<div class="col-sm-5">
				<input class="form-control" type="text" name="price" class="form-control" value="<?php echo $data['Price'];?>"/>
				<?php echo isset($error['price']) ? $error['price'] : '';?>
			</div>
	</div>	

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Stock :</label>					
			<div class="col-sm-5">
				<input type="text" name="quantity" class="form-control" value="<?php echo $data['Quantity'];?>"/>
				<?php echo isset($error['quantity']) ? $error['quantity'] : '';?>
			</div>
	</div>	

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Status :</label>					
			<div class="col-sm-5">
						<select name="serve_for" class="form-control">
							<option>Available</option>
							<option>Sold Out</option>
						</select>
				<?php echo isset($error['serve_for']) ? $error['serve_for'] : '';?>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Category :</label>					
			<div class="col-sm-5">
						<select name="category_ID" class="form-control">
							<?php while($stmt_category->fetch()){ 
								if($category_data['Category_ID'] == $data['Category_ID']){?>
									<option value="<?php echo $category_data['Category_ID']; ?>" selected="<?php echo $data['Category_ID']; ?>" ><?php echo $category_data['Category_name']; ?></option>
								<?php }else{ ?>
									<option value="<?php echo $category_data['Category_ID']; ?>" ><?php echo $category_data['Category_name']; ?></option>
								<?php }} ?>
						</select>
				<?php echo isset($error['category_ID']) ? $error['category_ID'] : '';?>
			</div>
	</div>	
	
	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Image :</label>					
			<div class="col-sm-5">
						<input class="form-control" type="file" name="menu_image" id="menu_image"/>
						<img src="<?php echo $data['Menu_image']; ?>" width="210" height="160"/>
				<?php echo isset($error['menu_image']) ? $error['menu_image'] : '';?>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Description :</label>					
			<div class="col-sm-5">
				<textarea name="description" id="description" class="form-control" rows="16"><?php echo $data['Description']; ?></textarea>
				<script type="text/javascript" src="css/js/ckeditor/ckeditor.js"></script>
				<script type="text/javascript">                        
		            CKEDITOR.replace( 'description' );
		        </script>
				<?php echo isset($error['description']) ? $error['description'] : '';?>
			</div>
	</div>		


	<div class="form-group">		
			<div class="col-sm-offset-3 col-sm-5">
				<input type="submit" class="btn-primary btn" value="Update" name="btnEdit" />
	
			</div>
	</div>	
	</form>
	<div class="separator"> </div>
</div>

<?php 
	$stmt_category->close();
	include_once('includes/close_database.php'); ?>