/**
 * 
 */
$(document).ready(function() {
	$("#dependentname").show();
	$("section").delegate("#dependentYes", "click", function(e){ $("#dependentname").show(); });
	$("section").delegate("#dependentNo", "click", function(e){ $("#dependentname").hide(); });
	$("section").delegate("#dependentYesAdmin", "click", function(e){
																		$("#addpatientemail").hide();
																	});
    /***

	$("#cancelappointmentconfirm").on("show.bs.modal", function(event) {
																		var link = $("#" + event.relatedTarget.id).attr("goto");
																		console.log(link);
                                                                        console.log(event);
																	   });
	$("#finishappointmentconfirm").on("show.bs.modal", function(event) {
																		var link = $("#" + event.relatedTarget.id).attr("goto");
																		console.log(link);
                                                                        console.log(event);
																	   });
    ***/

	$("section").delegate(".finish", "click", function(e){
															e.preventDefault();
                                                            $("#finishappointmentconfirm-confirmyes").attr("goto",
                                                                                                     $("#" + e.target.id).attr("goto"));
                                                            $("#finishappointmentconfirm").modal().show();
                                                            
														 });
	
    $("section").delegate("#finishappointmentconfirm-confirmyes", "click", function(e) {
        e.preventDefault();
        $.ajax({
				url: $("#" + e.target.id).attr("goto"),
                context: document.body
			   }).done(function(response) {
										    $("#ajaxcontainer").html(response);
			                              });
    });

    $("section").delegate("#cancelappointmentconfirm-confirmyes", "click", function(e) {
        e.preventDefault();
        $.ajax({
				url: $("#" + e.target.id).attr("goto"),
                context: document.body
			   }).done(function(response) {
										    $("#ajaxcontainer").html(response);
			                              });
    });

	$("section").delegate(".cancel", "click", function(e){
															e.preventDefault();
                                                            $("#cancelappointmentconfirm-confirmyes").attr("goto",
                                                                                                     $("#" + e.target.id).attr("goto"));
															$("#cancelappointmentconfirm").modal().show();
														 });

	$("section").delegate("#dependentNoAdmin", "click", function(e){
																		$("#addpatientemail").show();
																   });
	$("section").delegate("#appointmentsubmitbutton", "click", function(e){
																			e.preventDefault();
																			console.log($('#addappointmentform').attr("action"));
																			 $.ajax({
																		            	type: 'post',
																		            	url: $('#addappointmentform').attr("action"),
																		            	data: $('#addappointmentform').serialize(),
																		            	success: function(response)
                                                                                        {
                                                                                            $("#ajaxcontainer").html(response);
																		                }
																			 })
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
														$("#addpatientemail").hide();
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
	
	$("#thedoctorisin").click(function(e){
											 var location = $("#thedoctorisin").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
														$("#thedoctorisin").hide();
														$("#thedoctorisout").hide();
													});
										   });

	$("#thedoctorisout").click(function(e){
											 var location = $("#thedoctorisout").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
														$("#thedoctorisin").show();
														$("#thedoctorisout").hide();
													});
										   });

	$("#openqueue").click(function(e){
											 var location = $("#openqueue").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
														$("#closequeue").show();
														$("#openqueue").hide();
													});
										   });

	$("#closequeue").click(function(e){
											 var location = $("#closequeue").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
														$("#closequeue").show();
														$("#openqueue").hide();
													});
										   });

	$("#addadmin").click(function(e){
											 var location = $("#addadmin").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
													});
										   });
	
});
