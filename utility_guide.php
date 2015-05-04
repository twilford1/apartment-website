<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
		
	require_once("models/header.php");
	
	//echo "<center>";
	
	echo resultBlock($errors,$successes);
	echo "
	<div>
		
		<p><span style='font-size:36px;'>Utility Guide</span></p>
		<p><span style='font-size:30px;'>Trash Removal Services:</span></p>
		<ul>
			<span style='font-size:24px;'>Iowa City Sewer System </span>
			<span style='font-size:18px;'>
			     
				<li>Phone: (319)-356-5066 </li>
				<li>Website: http://www.icgov.org/?id=1699 </li>
				<li>Address: 410 E Washington St, Iowa City, IA 52240 </li>
			
			</span>
			
		</ul>
		<div>
		<p><span style='font-size:30px;'>Internet / Phone</span></p>
		<ul>
			<span style='font-size:24px;'>Mediacom</span>
			<span style='font-size:18px;'>
			     
				<li>Phone: (800)-332-0245 </li>
				<li>Website: http://mediacomcable.com </li>
				<li>Address: 546 Southgate Ave, Iowa City, IA 52240 </li>
			
			</span>
			<br>
			<span style='font-size:24px;'>CenturyLink</span>
			<span style='font-size:18px;'>
			     
				<li>Phone: (319)-351-2242 </li>
				<li>Website: http://www.centurylink.com </li>
				<li>Address: 302 S Linn St, Iowa City, IA 52240 </li>
			
			</span>
			
		</ul>
		
			
		</div>
		<div>
		<p><span style='font-size:30px;'>Cable </span></p>
		<ul>
			<span style='font-size:24px;'>DirectTV</span>
			<span style='font-size:18px;'>
			     
				<li>Phone: (855)-833-4388 </li>
				<li>Website: http://www.directv.com/city/iowa-city-ia/ </li>
			
			</span>
			<br>
			<span style='font-size:24px;'>DishTV</span>
			<span style='font-size:18px;'>
			     
				<li>Phone: (877)-504-1682 </li>
				<li>Website: http://www.usdish.com/ia-dishnetwork-iowa-city.html </li>
							
			</span>
			
		</ul>
		
		
			
		</div>
	
	
			
			
			
			
			
			
		
	</div>";
	
	echo "
	<br>
	<center>
		<a class='btn btn-primary' href='apartment_listing.php?id=".$_SESSION['flaw_apt']."'>Back to Listing</a>

	</center>
	<br>
	<br>";

	//echo fetchImageID('kuhl.jpg');

	
	include 'models/footer.php';
?>