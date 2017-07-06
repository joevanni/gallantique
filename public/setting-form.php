<?php
	include_once('includes/connect_database.php');  
	include_once('functions.php'); 
?>

<div id="content">
	<?php 
		if(isset($_POST['btnChange'])){
			$tax = $_POST['tax'];
			$currency = $_POST['currency'];
			
			// create array variable to handle error
			$error = array();
			
			if(empty($tax)){
				$tax = 0;
			}else if(!is_numeric($tax)){
				$error['tax'] = "*Tax should be in numeric.";
			}
				
			if(empty($currency)){
				$currency = "USD";
			}else{
				
				// update currency symbol in setting table
				$sql_query = "UPDATE tbl_setting
						SET Value = ? 
						WHERE Variable = 'Currency'";
				
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('s', $currency);
					// Execute query
					$stmt->execute();
					// store result 
					$update_result = $stmt->store_result();
					$stmt->close();
				}
				
			}
			
			if(is_numeric($tax)){
			
				// update tax in setting table
				$sql_query = "UPDATE tbl_setting 
						SET Value = ? 
						WHERE Variable = 'Tax'";
				
				$stmt = $connect->stmt_init();
				if($stmt->prepare($sql_query)) {	
					// Bind your variables to replace the ?s
					$stmt->bind_param('s', $tax);
					// Execute query
					$stmt->execute();
					// store result 
					$update_result = $stmt->store_result();
					$stmt->close();
				}
			}
			
			
				// check update result
			if($update_result){
				$error['update_setting'] = " <h4><div class='alert alert-success'>
														* Update setting successfully!
												 </div>
												  </h4>";
			}else{
				$error['update_setting'] = "*Failed updating setting data";
			}
			
			
		}		
		
		// get previous tax from setting table
		$sql_query = "SELECT Value 
				FROM tbl_setting 
				WHERE Variable = 'Tax'";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($previous_tax);
			$stmt->fetch();
			$stmt->close();
		}	
		
		// get previous currency symbol from setting table
		$sql_query = "SELECT Value 
				FROM tbl_setting 
				WHERE Variable = 'Currency'";
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($previous_currency);
			$stmt->fetch();
			$stmt->close();
		}	
		
	?>

	<div class="col-md-12">
		<h1>Setting</h1>
		<?php echo isset($error['update_setting']) ? $error['update_setting'] : '';?>

	<form method="post" enctype="multipart/form-data" role="form" class="form-horizontal form-groups-bordered">	

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Tax (%) :</label>					
			<div class="col-sm-5">
				<input type="text" class="form-control" name="tax" value="<?php echo $previous_tax;?>" />
				<?php echo isset($error['tax']) ? $error['tax'] : '';?>
			</div>
	</div>

	<div class="form-group">
		<label for="field-1" class="col-sm-3 control-label">Currency :</label>					
			<div class="col-sm-5">
				<select name="currency" class="form-control">
				<?php 
				$function = new functions;
				$arr_currency = $function->currency_info;
				$size = count($arr_currency);
				for($i=0;$i<$size;$i++){
				if($previous_currency == $arr_currency[$i]['code']){?>
					<option value="<?php echo $arr_currency[$i]['code']; ?>" selected="<?php echo $arr_currency[$i]['code']; ?>" ><?php echo $arr_currency[$i]['code']." - ".$arr_currency[$i]['name']; ?></option>
				<?php }else{ ?>
					<option value="<?php echo $arr_currency[$i]['code']; ?>" ><?php echo $arr_currency[$i]['code']." - ".$arr_currency[$i]['name']; ?></option>
				<?php }} ?>
			</select>

			</div>
	</div>			

	<div class="form-group">	
			<div class="col-sm-5">
				<input type="submit" class="btn-primary btn" value="Update" name="btnChange"/>
			</div>
	</div>
		
			
		</form>
	<div class="separator"> </div>
</div>
			
<?php include_once('includes/close_database.php'); ?>