/**
 * 
 */
$(document).ready(function() {
	$("#dependentname").show();
	$("section").delegate("#dependentYes", "click", function(e){ $("#dependentname").show(); });
	$("section").delegate("#dependentNo", "click", function(e){ $("#dependentname").hide(); });
	$("section").delegate("#dependentYesAdmin", "click", function(e){
																		$("#dependentname").show(); 
																		$("#addpatientemail").hide();
																	});
	$("section").delegate("#dependentNoAdmin", "click", function(e){
																	$("#dependentname").hide();
																	$("#addpatientemail").show();
																   });
	$("section").delegate("#appointmentsubmitbutton", "click", function(e){
																			e.preventDefault();
																			console.log("appointmentsubmitbutton");
																		  });
	$("section").delegate(".cancelschedule", "click", function(e){
																   e.preventDefault(); 
																   $("#cancelappointmentconfirm").modal().show();
																 });

	$("#cancelallappointments").click(function(e){
													e.preventDefault(); 
													$("#cancelallappointmentsconfirm").modal().show();
												 });

	$("#setappointment").click(function(e){ 
											var location = $("#setappointment").attr("goto"); 
											$.ajax({
												  url: location,
												  context: document.body
												}).done(function(response) {
												  $("#ajaxcontainer").html(response);
												});
										  });

	$("#viewappointment").click(function(e){
											 var location = $("#viewappointment").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
													});
										   });

	$("#addpatient").click(function(e){
											 var location = $("#addpatient").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
													});
										   });

	$("#viewqueue").click(function(e){
											 var location = $("#viewqueue").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
													});
										   });


});