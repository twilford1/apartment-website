<?php
	require_once("models/config.php");
	
	if (!securePage($_SERVER['PHP_SELF']))
	{
		die();
	}
	
	require_once("models/header.php");	
	
	echo "
	<center>
		<h2>Apartment Walk-through Guide</h2>
	</center>
	<br>

	<link rel='stylesheet' href='//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css'>
	<div class='container'>
		<div class='panel panel-default'>
			<div class='panel-heading'>
				<h3 class='panel-title'><b>Select a section from the drop-down list to begin.</b></h3>
			</div>  
			<form class='form-horizontal radio' name='walkthrough' action='apt_eval_guide.php' method='post'>
			<ul class='list-group'>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-1' data-toggle='detail-1'>
						<div class='col-xs-10'>
							<b>General</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-1'>
						<hr></hr>
						<div class='container'>
							<div class='col-md-4'>
								<b>Apartment:</b>
								<div class='radio'>
								 <label><input type='radio' name='AC' value='1_Air-Conditioning'>Air-Conditioning</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='outlets' value='1_Ample electrical outlets'>Ample electrical outlets</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='privacy' value='1_Privacy'>Privacy</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='blinds_curtains' value='1_Blinds/curtains'>Blinds/curtains</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='cable' value='1_Cable TV connection'>Cable TV connection</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='floors' value='1_Floors (carpet/hardwood)'>Floors (carpet/hardwood)</label>
								</div>							
								<div class='radio'>
								 <label><input type='radio' name='fireplace' value='1_Fireplace'>Fireplace</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='lighting' value='1_Lighting/Natural light'>Lighting/Natural light</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='markings' value='1_Paint/wall markings/stains'>Paint/wall markings/stains</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='patio_balcony' value='1_Patio/balcony'>Patio/balcony</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='phone_ethernet' value='1_Phone jack(s)/Ethernet jack(s)'>Phone jack(s)/Ethernet jack(s)</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='view' value='1_View'>View</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='water_pressure' value='1_Water pressure'>Water pressure</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Lease:</b>
								<div class='radio'>
								 <label><input type='radio' name='pets' value=''>Pets allowed</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='changes_allowed' value=''>Physical changes allowed</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='break_lease_penalty' value=''>Penalty for breaking lease</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='late_payment' value=''>Late payment fee</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='lease_length' value=''>Lease length</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='date_available' value=''>Date available</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='rent' value=''>Rent amount</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='address' value=''>Unit Address</label>
								</div>							
								<div class='radio'>
								 <label><input type='radio' name='utilities' value=''>Utilities included in rent</label>
								</div>							
								<b>Neighhborhood:</b>
								<div class='radio'>
								 <label><input type='radio' name='public_transit' value=''>Proximity to public transit</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='distance_to_work_school' value=''>Distance to school/work</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='proximity_bank_shops' value=''>Proximity to bank and shops</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Community:</b>
								<div class='radio'>
								 <label><input type='radio' name='laundry_facilities' value=''>Laundry/Facilities nearby</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='trash' value=''>Garbage chute/trash removal nearby</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='noise' value=''>Noise level</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='parking' value=''>Parking (free or otherwise)</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='bike' value=''>Bike racks</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='elevator_stairs' value=''>Elevator/stairs</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='roof_access' value=''>Roof access</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='soundproof' value=''>Soundproof walls</label>
								</div>							
								<div class='radio'>
								 <label><input type='radio' name='mail' value=''>Mailbox</label>
								</div>							
								<b>Safety:</b>
								<div class='radio'>
								 <label><input type='radio' name='emergency_exit' value=''>Emergency exits</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='fire_extinguishers' value=''>Fire extinguishers</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='windows' value=''>Functioning windows</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='locks' value=''>Locks on all doors</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='outside_lighting' value=''>Outside lighting</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='screens' value=''>Screens</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='smoke_detectors' value=''>Smoke detectors</label>
								</div>
							</div>
						</div>
					</div>
				</li>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-2' data-toggle='detail-2'>
						<div class='col-xs-10'>
							<b>Bathroom</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-2'>
						<hr></hr>
						<div class='container'>
							<div class='col-md-4'>
								<b>Shower:</b>
								<div class='radio'>
								 <label><input type='radio' name='shower_head' value='2_Broken/leaky shower head'>Broken/leaky shower head</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='shower_stains' value='2_Stains'>Stains</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='shower_curtains' value='2_Damaged shower curtains or shower rod'>Damaged shower curtains or shower rod</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='shower_broken_tile' value='2_Broken or chipped tiles'>Broken or chipped tiles</label>
								</div>
								<b>Bathtub:</b>
								<div class='radio'>
								 <label><input type='radio' name='bath_faucet' value='2_Broken/leaky faucet or knobs'>Broken/leaky faucet or knobs</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='bath_stains' value='2_Stains'>Stains</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='bath_caulk' value='2_Damaged caulk'>Damaged caulk</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='bath_mold' value='2_Mold (could be at outer base of tub or around inside edges)'>Mold (could be at outer base of tub or around inside edges)</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='bath_broken' value='2_Broken or chipped porcelain or other tub material'>Broken or chipped porcelain or other tub material</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Toilet:</b>
								<div class='radio'>
								 <label><input type='radio' name='toilet_flush' value='2_Difficult/damaged flushing mechanism'>Difficult/damaged flushing mechanism</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='toilet_stains' value='2_Stains in toilet bowl or on seat'>Stains in toilet bowl or on seat</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='toilet_mold' value='2_Mold around base or inner rim'>Mold around base or inner rim</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='toilet_lid' value='2_Broken lid, toilet seat, etc.'>Broken lid, toilet seat, etc.</label>
								</div>
								<b>Sink:</b>
								<div class='radio'>
								 <label><input type='radio' name='sink_faucet' value='2_Faucet works (both hot and cold)'>Faucet works (both hot and cold)</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='sink_stains' value='2_Stains/chips/rust/other damage'>Stains/chips/rust/other damage</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='sink_mold' value='2_Mould around drain/faucet/outside of basin'>Mould around drain/faucet/outside of basin</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='sink_caulk' value='2_Caulking in tact'>Caulking in tact</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Countertops/Drawers/Cabinets</b>
								<div class='radio'>
								 <label><input type='radio' name='counter_old' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='counter_vinyl' value=''>Vinyl peeling</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='counter_dirty' value=''>Dirty/mouldy</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='counter_water_damage' value=''>Water damage on wood</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='counter_drawer_open' value=''>Drawers/cabinets open properly</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' name='counter_knobs' value=''>Knobs/handles fastened securely</label>
								</div>							
								<div class='radio'>
								 <label><input type='radio' name='counter_space' value=''>Enough space</label>
								</div>				
							</div>
						</div>
					</div>
				</li>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-3' data-toggle='detail-3'>
						<div class='col-xs-10'>
							<b>Kitchen</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-3'>
						<hr></hr>
						<div class='container'>
							<div class='col-md-4'>
								<b>Refrigerator:</b>
								<div class='radio'>
								 <label><input type='radio' value='3_Old/outdated'>Old/outdated</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Working freezer</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Enough space inside</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Broken or chipped shelving</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Leaking seals (can you feel cold air coming out?)</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Scratches/stains/other damage</label>
								</div>
								<b>Sink/Garbage Disposal:</b>
								<div class='radio'>
								 <label><input type='radio' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Faucets work (both hot and cold water)</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Garbage disposal present/works</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Stains/chips/rust/other damage</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Caulk in tact</label>
								</div>
							</div>
							<div class='col-md-4'>
								<b>Dishwasher:</b>
								<div class='radio'>
								 <label><input type='radio' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Space within</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Special operating instructions</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Knobs/timer/other controls working</label>
								</div>
								<b>Countertops/Drawers/Cabinets</b>
								<div class='radio'>
								 <label><input type='radio' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Vinyl peeling</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Dirty/mouldy</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Water damage on wood</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Drawers/cabinets open properly</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Knobs/handles fastened securely</label>
								</div>							
								<div class='radio'>
								 <label><input type='radio' value=''>Enough space</label>
								</div>	
							</div>
							<div class='col-md-4'>
								<b>Oven/Stove/Microwave:</b>
								<div class='radio'>
								 <label><input type='radio' value=''>Old/outdated</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Gas or Electric</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Dirty/damaged</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Stove pans/ microwave plate present/clean/undamaged</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Oven drawer</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Knobs/timer/other controls working</label>
								</div>							
							</div>
						</div>
					</div>
				</li>
				<li class='list-group-item'>
					<div class='row toggle' id='dropdown-detail-4' data-toggle='detail-4'>
						<div class='col-xs-10'>
							<b>Bedroom</b>
						</div>
						<div class='col-xs-2'><i class='fa fa-chevron-down pull-right'></i></div>
					</div>
					<div id='detail-4'>
						<hr></hr>
						<div class='container'>
							<div class='col-md-4'>
								<b>Closet</b>
								<div class='radio'>
								 <label><input type='radio' value='4_Lighting'>Lighting</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Walk-in</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Space/height</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Storage/shelving</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Doors/curtains</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Knobs/handles fastened securely</label>
								</div>							
							</div>
							<div class='col-md-4'>
								<b>Other</b>
								<div class='radio'>
								 <label><input type='radio' value='TESTTESTTESTTESETEST'>Lighting</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Space/height</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Large window(s)</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Storage/shelving</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Doors/curtains</label>
								</div>
								<div class='radio'>
								 <label><input type='radio' value=''>Knobs/handles fastened securely</label>
								</div>							
								<div class='radio'>
								 <label><input type='radio' value=''>Stains on floor/walls or other markings</label>
								</div>							
							</div>
						</div>
					</div>
				</li>
			</ul>
			</form>
		</div>";
	echo "
	<div class='container'>
	</div>";

	include 'models/footer.php';
?>

<script>
$(document).ready(function() {
  $('[id^=detail-]').hide();
  $('.toggle').click(function() {
    $input = $( this );
    $target = $('#'+$input.attr('data-toggle'));
    $target.slideToggle();
  });
});
</script>