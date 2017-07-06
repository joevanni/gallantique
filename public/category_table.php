<?php
	include_once('includes/connect_database.php'); 
	include_once('functions.php'); 
?>

<div id="content">
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
			
		if(empty($keyword)){
			$sql_query = "SELECT Category_ID, Category_name, Category_image
					FROM tbl_category
					ORDER BY Category_ID DESC";
		}else{
			$sql_query = "SELECT Category_ID, Category_name, Category_image
					FROM tbl_category
					WHERE Category_name LIKE ? 
					ORDER BY Category_ID DESC";
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
			$stmt->bind_result($data['Category_ID'], 
					$data['Category_name'],
					$data['Category_image']
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
		
		if(empty($keyword)){
			$sql_query = "SELECT Category_ID, Category_name, Category_image 
					FROM tbl_category
					ORDER BY Category_ID DESC LIMIT ?, ?";
		}else{
			$sql_query = "SELECT Category_ID, Category_name, Category_image 
					FROM tbl_category
					WHERE Category_name LIKE ? 
					ORDER BY Category_ID DESC LIMIT ?, ?";
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
			$stmt_paging->bind_result($data['Category_ID'], 
					$data['Category_name'],
					$data['Category_image']
					);
			// for paging purpose
			$total_records_paging = $total_records; 
		}

		// if no data on database show "No Reservation is Available"
		if($total_records_paging == 0){
	
	?>
	
	<h1>No Category is Available</h1>
	<hr />
	<?php 
		// otherwise, show data
		}else{
			$row_number = $from + 1;
	?>

	<h1>Category List</h1>
	<hr />
		<table class="table table-bordered datatable" id="table-1">
		<thead>
			<tr>
				<th>Name</th>
				<th>Image</th>
				<th>Action</th>
			</tr>
		</thead>	
		<?php while ($stmt_paging->fetch()){ ?>
		<tbody>
			<tr>
				<td><?php echo $data['Category_name'];?></td>
				<td><img src="<?php echo $data['Category_image']; ?>" width="40" height="40"/></td>
				<td>
					<a class="btn btn-primary" href="edit-category.php?id=<?php echo $data['Category_ID'];?>">
					Edit
					</a>&nbsp;
					<a class="btn btn-primary" href="delete-category.php?id=<?php echo $data['Category_ID'];?>">
					Delete
					</a>
				</td>
			</tr>
		</tbody>	
			<?php } }?>
		</table>
	</div>
	<div id="option_menu">
		<a  class="btn btn-primary" href="add-category.php">Add New Category</a>
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
<?php 
	$stmt->close();
	include_once('includes/close_database.php'); ?>
					
				