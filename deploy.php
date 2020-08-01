<?php
session_start();

date_default_timezone_set('Asia/Kolkata');
$dir				= getcwd();
$a 					= scandir($dir);
$b 					= glob($dir."/*.zip");
$dpl_size			= sizeof($b);

$data['server']		=  gethostname();
if(!isset($_SESSION['log'])){
$timelog 			= date('D,d-M-Y H:i:s');
$_SESSION['log']	= array("[$timelog] Initiating Process");
array_push($_SESSION['log'] , "[$timelog] Preparing Deployment Sequence...");
array_push($_SESSION['log'] , "[$timelog] Scanning Directory for packages...");
array_push($_SESSION['log'] , "[$timelog] Packages Selection In Progress...");
}

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <title>Simple Deployment Portal</title>
  </head>
  <body>
  	<style>
  		.blink_me {
		  animation: blinker 1s linear infinite;
		}

		@keyframes blinker {
		  50% {
		    opacity: 0;
		  }
		}

  	</style>
  	<div class="container">  		
  		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
		  <a class="navbar-brand" href="#">Simple Deployment Portal (SDP)</a>
		  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		    <span class="navbar-toggler-icon"></span>
		  </button>

		  <div class="collapse navbar-collapse" id="navbarSupportedContent">
		    <ul class="navbar-nav ml-auto">
		      <li class="nav-item active"><a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a></li>
		      <li class="nav-item">
		        <a class="nav-link" href="#">Link</a>
		      </li>
		      <li class="nav-item dropdown">
		        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		          Dropdown
		        </a>
		        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
		          <a class="dropdown-item" href="#">Action</a>
		          <a class="dropdown-item" href="#">Another action</a>
		          <div class="dropdown-divider"></div>
		          <a class="dropdown-item" href="#">Something else here</a>
		        </div>
		      </li>
		      <li class="nav-item">
		        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
		      </li>
		    </ul>
		  </div>
		</nav>
	</div>


		<?php 

			if(!empty(($_GET))){

				if($_GET["action"] == "deploy" && isset($_GET["package"])){
					$pkg 	= $_GET["package"];
					?>
					<div class="container">
					<div class="row pt-2">
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="card">
							  <div class="card-body">
							  	<h5><span class="badge badge-pill badge-warning">Re-confirmation</span></h5>
							  	<p style="font-size: 16px;">
							  		Please review your deployment. Deployment is not reversable and can't be undone. In-case of an incorrect deployment, the setup needs to be deleted and needs to be re-run.
							  		<table class="table"><thead class="thead-dark"><tr><th scope="col">#</th><th scope="col">Package</th><th scope="col">Action</th></tr></thead>
							  		<tr><td>1</td><td>Server</td><td><?php echo $data['server']; ?></td></tr>
							  		<tr><td>2</td><td>Package</td><td><?php echo $b[$_GET['package']]; ?></td></tr>
							  		<tr><td>3</td><td>Implementation Date</td><td><?php echo date('D, d-M-Y'); ?></td></tr>
							  		<tr><td>4</td><td>Implementation Time</td><td><?php echo date('h:i:s'); ?></td></tr>
							  		</table>
							  	</p>
							  	<span class="mr-auto"><a href="/deploy.php" class="btn btn-danger">Cancel</a>  <a href="/deploy.php?action=implement&package=<?php echo $pkg;?>" class="btn btn-success">Deploy</a></span>
							  </div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="card">
							  <div class="card-body overflow-auto">
							    <div class="overflow-auto">
							  		<h3>Logs : </h3>
								    <?php 
									foreach ($_SESSION['log'] as $key => $value) {
								    	echo $value;
								    }						    
								    ?>
							  	</div>
							  </div>
							</div>
						</div>
					</div>
				</div>
					<?php
					$timelog 			= date('D,d-M-Y H:i:s');
					array_push($_SESSION['log'] , "[$timelog] Requesting final confirmation for deployment");

				}

				if($_GET["action"] == "implement" && isset($_GET["package"])){
					$pkg 	= $_GET["package"];
					?>
					<div class="container">
					<div class="row pt-2">
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="card">
							  <div class="card-body">
							  	<h5><span class="badge badge-pill badge-warning"></span></h5>
							  	<p style="font-size: 16px;" class="blink_me">
							  		Implementation in Progress, please wait...
							  	</p>
							  	<?php 
							  	$zip = new ZipArchive;
								$res = $zip->open($b[$_GET['package']]);
								if ($res === TRUE) {
								  $zip->extractTo($dir);
								  $zip->close();
								  echo "<p style='font-size: 16px;'>Deployment Successfully Completed<br>Your Zip has been deploy to $dir</p>";
								  echo "<a href='/deploy.php?action=clean' class='btn btn-success'>Run Cleaner</a>";
								  $timelog 			= date('D,d-M-Y H:i:s');
								  array_push($_SESSION['log'] , "[$timelog] Deployment Successfully completed");
								} else {
								  echo 'Deployment Failed, please try again!';
								  $timelog 			= date('D,d-M-Y H:i:s');
								  array_push($_SESSION['log'] , "[$timelog] Deployment failed. Either this is not a valid zip or zip is password protected.");
								  echo "<a href='/deploy.php?action=clean' class='btn btn-success'>Run Cleaner</a>";
								}


							  	?>
							  </div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="card">
							  <div class="card-body">
							  	<div class="overflow-auto">
							  		<h3>Logs : </h3>
								    <?php 
									foreach ($_SESSION['log'] as $key => $value) {
								    	echo $value."<br>";
								    }						    
								    ?>
							  	</div>
							  </div>
							</div>
						</div>
					</div>
				</div>
					<?php
				}

				if($_GET["action"] == "clean"){
					?>
					<div class="container">
					<div class="row pt-2">
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="card">
							  <div class="card-body">
							  	<h5><span class="badge badge-pill badge-warning"></span></h5>
							  	<p style="font-size: 16px;">
							  		Initiating Cleaning...<br>
							  		Creating log file...<br>
							  		<?php error_log('Creating Log',3,"deployment.log"); ?>
							  		Dumping the session to log for future reference...<br>
							  		<?php 
							  		$log_file = fopen("deployment.log", "w") or die("Unable to open file!");
							  		foreach ($_SESSION['log'] as $key => $value) {
							  			$log_value = $value."\n";
								    	fwrite($log_file,$log_value);
								    }
									fclose($log_file);
							  		?>
							  		Destroying Session...<br>
							  		<?php session_destroy(); ?>
							  		Cleanup process completed successfully.<br><br>
							  		You may view the logs from <a href="<?php echo $dir; ?>/deployment.log">here</a>

							  	</p>
							  </div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="card">
							  <div class="card-body">
							  	<div class="overflow-auto">
							  		<h3>Logs : </h3>
								    <?php 
									foreach ($_SESSION['log'] as $key => $value) {
								    	echo $value."<br>";
								    }						    
								    ?>
							  	</div>
							  </div>
							</div>
						</div>
					</div>
				</div>
					<?php
				}


			}else{

				?>
				<div class="container">
					<div class="row pt-2">
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="card">
							  <div class="card-body">
							  	<p style="font-size: 16px;"><span class="badge badge-pill badge-warning"><?php echo $dpl_size; ?></span> Deployment Packages available. <br>
							  	 </p>
							    <?php
							    if($dpl_size > 0){
							    	?><p>Available Deployment Packages: <br></p><?php
							    }
							    echo '<table class="table"><thead class="thead-dark"><tr><th scope="col">#</th><th scope="col">Package</th><th scope="col">Action</th></tr></thead>';
							    foreach ($b as $key => $value) {
									echo "<tr><td>".($key + 1) ."</td><td>".$value ."</td><td><a href='?action=deploy&package=$key' class='badge badge-warning'>Deploy</a></td></tr>";
								}
								 echo "</table>";
							    ?>
							  </div>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-6">
							<div class="card">
							  <div class="card-body">
							   <h3>Logs : </h3>
							    <?php 
								foreach ($_SESSION['log'] as $key => $value) {
							    	echo $value . "<br>";
							    }						    
							    ?>
							  </div>
							</div>
						</div>
					</div>
				</div>
				
				<?php
			}
		?>

    

    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
	<script>
		$('#myModal').on('shown.bs.modal', function () {
		  $('#myInput').trigger('focus')
		})
	</script>
  </body>
</html>