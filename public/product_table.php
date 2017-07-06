<?php
	include_once('includes/connect_database.php'); 
	include_once('functions.php'); 
?>

<div id="content" class="container col-md-12">
	<?php 
		// create object of functions class
		$function = new functions;
		
		// create array variable to store data from database
		$data = array();
		
		if(isset($_GET['keyword'])){	
			// check value of keyword variable
			$keyword = $function->sanitize($_GET['keyword']);
			$bind_keyword = "%".$keyword."%";
		}else{
			$keyword = "";
			$bind_keyword = $keyword;
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
		
		// get all data from menu table and category table
		if(empty($keyword)){
			$sql_query = "SELECT Menu_ID, Menu_name, Category_name, Price, Serve_for, Menu_image, Quantity 
					FROM tbl_menu m, tbl_category c
					WHERE m.Category_ID = c.Category_ID  
					ORDER BY m.Menu_ID DESC";
		}else{
			$sql_query = "SELECT Menu_ID, Menu_name, Category_name, Price, Serve_for, Menu_image, Quantity  
					FROM tbl_menu m, tbl_category c
					WHERE m.Category_ID = c.Category_ID AND Menu_name LIKE ? 
					ORDER BY m.Menu_ID DESC";
		}
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			if(!empty($keyword)){
				$stmt->bind_param('s', $bind_keyword);
			}
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['Menu_ID'], 
					$data['Menu_name'], 
					$data['Category_name'],
					$data['Price'], 
					$data['Serve_for'],
					$data['Menu_image'],
					$data['Quantity']
					);
					
			// get total records
			$total_records = $stmt->num_rows;
		}
		
		// check page parameter
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
		
		// number of data that will be display per page		
		$offset = 10;
						
		//lets calculate the LIMIT for SQL, and save it $from
		if ($page){
			$from 	= ($page * $offset) - $offset;
		}else{
			//if nothing was given in page request, lets load the first page
			$from = 0;	
		}
		
		// get all data from reservation table
		if(empty($keyword)){
			$sql_query = "SELECT Menu_ID, Menu_name, Category_name, Price, Serve_for, Menu_image, Quantity  
					FROM tbl_menu m, tbl_category c
					WHERE m.Category_ID = c.Category_ID  
					ORDER BY m.Menu_ID DESC LIMIT ?, ?";
		}else{
			$sql_query = "SELECT Menu_ID, Menu_name, Category_name, Price, Serve_for, Menu_image, Quantity  
					FROM tbl_menu m, tbl_category c
					WHERE m.Category_ID = c.Category_ID AND Menu_name LIKE ? 
					ORDER BY m.Menu_ID DESC LIMIT ?, ?";
		}
		
		$stmt_paging = $connect->stmt_init();
		if($stmt_paging ->prepare($sql_query)) {
			// Bind your variables to replace the ?s
			if(empty($keyword)){
				$stmt_paging ->bind_param('ss', $from, $offset);
			}else{
				$stmt_paging ->bind_param('sss', $bind_keyword, $from, $offset);
			}
			// Execute query
			$stmt_paging ->execute();
			// store result 
			$stmt_paging ->store_result();
			
			$stmt_paging->bind_result($data['Menu_ID'], 
					$data['Menu_name'], 
					$data['Category_name'],
					$data['Price'], 
					$data['Serve_for'],
					$data['Menu_image'],
					$data['Quantity']
					);
			
			// for paging purpose
			$total_records_paging = $total_records; 
		}

		// if no data on database show "No Reservation is Available"
		if($total_records_paging == 0){
	
	?>
	<h1>Product Not Available
	</h1>
	<hr />
	
	<?php 
		// otherwise, show data
		}else{
			$row_number = $from + 1;
	?>
	<h1>Product List</h1>
	<table class="table table-bordered datatable" id="table-1">
		<thead>
		<tr>
			<th>Name</th>
			<th>Image</th>
			<th>Status</th>
			<th>Stock</th>
			<th>Price</th>
			<th>Category</th>
			<th>Action</th>
		</tr>
		</thead>
	<?php 
		// get all data using while loop
		while ($stmt_paging->fetch()){ ?>
		<tbody>
		<tr>
			<td><?php echo $data['Menu_name'];?></td>
			<td ><img src="<?php echo $data['Menu_image']; ?>" width="60" height="40"/></td>
			<td><?php echo $data['Serve_for'];?></td>
			<td><?php echo $data['Quantity'];?></td>
			<td><?php echo $data['Price']." ".$currency;?></td>
			<td ><?php echo $data['Category_name'];?></td>
			<td >
				<a class="btn btn-primary" href="product-detail.php?id=<?php echo $data['Menu_ID'];?>">
					View
				</a>&nbsp;

				<a class="btn btn-primary" href="edit-product.php?id=<?php echo $data['Menu_ID'];?>">
					Edit
				</a>&nbsp;

				<a class="btn btn-primary" href="delete-product.php?id=<?php echo $data['Menu_ID'];?>">
					Delete
				</a>
			</td>
		</tr>
		</tbody>
	<?php } }?>
	</table>
	</div>
	<div id="option_menu">
		<a  class="btn btn-primary" href="add-product.php">Add New Product</a>
	</div>
	<script type="text/javascript">
		jQuery(document).ready(function($)
		{
			$("#table-1").dataTable({
				"sPaginationType": "bootstrap",
				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				"bStateSave": true
			});
			
			$(".dataTables_wrapper select").select2({
				minimumResultsForSearch: -1
			});
		});
	</script>
	
	<div class="separator"> </div>
</div> 
<?php 
	$stmt->close();
	include_once('includes/close_database.php'); ?>
					
				