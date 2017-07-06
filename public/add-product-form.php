<?php
	include_once('includes/connect_database.php'); 
	include_once('functions.php'); 
?>
<div id="content">
	<?php 
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
			
		//$max_serve = 10;
			
		if(isset($_POST['btnAdd'])){
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
					
			if($image_error > 0){
				$error['menu_image'] = " <span class='label label-danger'>Not uploaded!</span>";
			}else if(!(($image_type == "image/gif") || 
				($image_type == "image/jpeg") || 
				($image_type == "image/jpg") || 
				($image_type == "image/x-png") ||
				($image_type == "image/png") || 
				($image_type == "image/pjpeg")) &&
				!(in_array($extension, $allowedExts))){
			
				$error['menu_image'] = " <span class='label label-danger'>Image type must jpg, jpeg, gif, or png!</span>";
			}
				
			if(!empty($menu_name) && !empty($category_ID) && !empty($price) && is_numeric($price) &&
				!empty($serve_for) && empty($error['menu_image']) && !empty($description) && !empty($quantity) && is_numeric($quantity)){
				
				// create random image file name
				$string = '0123456789';
				$file = preg_replace("/\s+/", "_", $_FILES['menu_image']['name']);
				$function = new functions;
				$menu_image = $function->get_random_string($string, 4)."-".date("Y-m-d").".".$extension;
					
				// upload new image
				$upload = move_uploaded_file($_FILES['menu_image']['tmp_name'], 'upload/images/'.$menu_image);
		
				// insert new data to menu table
				$sql_query = "INSERT INTO tbl_menu (Menu_name, Category_ID, Price, Serve_for, Menu_image, Description, Quantity)
						VALUES(?, ?, ?, ?, ?, ?, ?)";
						
				$upload_image = 'upload/images/'.$menu_image;
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('sssssss', 
								$menu_name, 
								$category_ID, 
								$price, 
								$serve_for, 
								$upload_image,
								$description,
								$quantity
								);
					// Execute query
					$stmt->execute();
					// store result 
					$result = $stmt->store_result();
					$stmt->close();
				}
				
				if($result){
					$error['add_menu'] = " <span class='label label-primary'>Success Added</span>";
				}else {
					$error['add_menu'] = " <span class='label label-danger'>Failed</span>";
				}
			}
				
			}
	?>
	<div class="col-md-12">
	<h1>Add Product</h1>
	<hr />
	<form method="post" enctype="multipart/form-data" role="form" class="form-horizontal form-groups-bordered">	

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Product Name :</label>					
			<div class="col-sm-5">
				<input type="text" name="menu_name" class="form-control"/>
				<?php echo isset($error['menu_name']) ? $error['menu_name'] : '';?>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Price (<?php echo $currency;?>) :</label>					
			<div class="col-sm-5">
				<input type="text" name="price" class="form-control"/>
				<?php echo isset($error['price']) ? $error['price']:'';?>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Stock :</label>					
			<div class="col-sm-5">
				<input type="text" name="quantity" class="form-control"/>
				<?php echo isset($error['quantity']) ? $error['quantity']:'';?>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Status :</label>					
			<div class="col-sm-5">
			<select name="serve_for" class="form-control">
				<option>Available</option>
				<option>Sold Out</option>
			</select>
				<?php echo isset($error['serve_for']) ? $error['serve_for']:'';?>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Category :</label>					
			<div class="col-sm-5">
				<select name="category_ID" class="form-control">
					<?php while($stmt_category->fetch()){ ?>
					<option value="<?php echo $category_data['Category_ID']; ?>"><?php echo $category_data['Category_name']; ?></option>
			<?php } ?>
		</select>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Image :</label>					
			<div class="col-sm-5">
				<input type="file" name="menu_image" class="form-control"/>
				<?php echo isset($error['menu_image']) ? $error['menu_image'] : '';?>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Description :</label>					
			<div class="col-sm-5">
				<textarea name="description" id="description" class="form-control" rows="16"></textarea>
					<script type="text/javascript" src="css/js/ckeditor/ckeditor.js"></script>
					<script type="text/javascript"> CKEDITOR.replace( 'description' );</script>
				<?php echo isset($error['description']) ? $error['description'] : '';?>
			</div>
	</div>

	<div class="form-group">		
			<div class="col-sm-offset-3 col-sm-5">
				<input class="btn btn-default" type="submit" value="Add" name="btnAdd" />
				<input class="btn btn-default" type="reset" value="Clear"/>
			</div>
	</div>	

	</form>
	</div>	
	<div class="separator"> </div>
</div>
			

<?php 
	$stmt_category->close();
	include_once('includes/close_database.php'); ?>