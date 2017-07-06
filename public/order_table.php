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
		
		// get all data from pemesanan table
		if(empty($keyword)){
			$sql_query = "SELECT ID, Name, Address, Number_of_people, Date_n_Time, Phone_number, Status, Email
				FROM tbl_reservation  
				ORDER BY Date_n_Time DESC";
		}else{
			$sql_query = "SELECT ID, Name, Address, Number_of_people, Date_n_Time, Phone_number, Status, Email
				FROM tbl_reservation 
				WHERE Name LIKE ? 
				ORDER BY Date_n_Time DESC";
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
			$stmt->bind_result($data['ID'], 
					$data['Name'],
					$data['Address'],					
					$data['Number_of_people'], 
					$data['Date_n_Time'], 
					$data['Phone_number'],
					$data['Status'],
					$data['Email']
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
		$offset = 20;
							
		//lets calculate the LIMIT for SQL, and save it $from
		if ($page){
			$from 	= ($page * $offset) - $offset;
		}else{
			//if nothing was given in page request, lets load the first page
			$from = 0;	
		}
		
		// get all data from pemesanan table
		if(empty($keyword)){
			$sql_query = "SELECT ID, Name, Address, Number_of_people, Date_n_Time, Phone_number, Status, Email 
				FROM tbl_reservation 
				ORDER BY Date_n_Time DESC 
				LIMIT ?, ?";
		}else{
			$sql_query = "SELECT ID, Name, Address, Number_of_people, Date_n_Time, Phone_number, Status, Email 
				FROM tbl_reservation 
				WHERE Name LIKE ? 
				ORDER BY Date_n_Time ASC 
				LIMIT ?, ?";
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
			
			$stmt_paging ->bind_result($data['ID'], 
					$data['Name'], 
					$data['Address'], 
					$data['Number_of_people'], 
					$data['Date_n_Time'], 
					$data['Phone_number'],
					$data['Status'],
					$data['Email']
					);
			
			// for paging purpose
			$total_records_paging = $total_records; 
		}
						
		// if no data on database show "Tidak Ada Pemesanan"
		if($total_records_paging == 0){
	?>
	<h1>There is No Order</h1>
	<hr />
	
	<?php
		// otherwise, show data
		}else{ $row_number = $from + 1;?>
	
		<h1>Order List</h1>
		<hr/>
	<table class="table table-bordered datatable" id="table-1">
		<thead>
		<tr>
			<th>Name</th>
			<th>Address</th>
			<th>Email</th>
			<th>Shipping by</th>
			<th>Date & Time</th>
			<th>Phone</th>
			<th>Status</th>
			<th>Action</th>
		</tr>
		</thead>
		<?php
			// get all data using while loop
			while ($stmt_paging->fetch()){ ?>
			<tbody>
			<tr>
				<td><?php echo $data['Name'];?></td>
				<td><?php echo $data['Address'];?></td>
				<td><?php echo $data['Email'];?></td>
				<td><?php echo $data['Number_of_people'];?></td>
				<td><?php echo $data['Date_n_Time'];?></td>
				<td><?php echo $data['Phone_number'];?></td>
				<td><?php echo $data['Status'] == 1 ? "<span class='label label-primary'>PROCESSED</span>" : "<span class='label label-danger'>ON PROCESS</span>";?></td>
				<td>
					<a href="order-detail.php?id=<?php echo $data['ID'];?>">
						Detail
					</a>&nbsp;

					<a href="delete-order.php?id=<?php echo $data['ID'];?>">
						Delete
					</a>
				</td>
			</tr>
			</tbody>
		<?php } }?>
	</table>

	<script type="text/javascript">
		jQuery(document).ready(function($)
		{
			$("#table-1").dataTable({
				"sPaginationType": "bootstrap",
				"aLengthMenu": [[20, -1], [20, "All"]],
				"bStateSave": true
			});
			
			
		});
	</script>

<?php 
	$stmt->close();
	include_once('includes/close_database.php'); ?>
					
				