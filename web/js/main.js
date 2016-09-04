/**
 * 
 */
$(document).ready(function() {
	$.ajax({
				url: "/admin/loadComponentsToHide",
				dataType: 'json',
				success: function(response)
				{
					var componentList = response["components"];
					console.log(componentList);
					
					for(var component in componentList)
					{
						$("#" + componentList[component]).hide();
					}
				}
			});

	$("section").delegate("#dependentYes", "click", function(e){ $("#dependentname").show(); });
	$("section").delegate("#dependentNo", "click", function(e){ $("#dependentname").hide(); });
	
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
        console.log($("#" + e.target.id).attr("goto"));
        $.ajax({
				url: $("#" + e.target.id).attr("goto"),
                context: document.body
			   }).done(function(response) {
										    $("#ajaxcontainer").html(response);
			                              });
    });

    $("section").delegate("#cancelallappointmentsconfirm-confirmyes", "click", function(e) {
        e.preventDefault();
        console.log($("#" + e.target.id).attr("goto"));
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

	$("section").delegate("#appointmentsubmitbutton", "click", function(e){
																			e.preventDefault();
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
													$("#cancelallappointmentsconfirm-confirmyes").attr("goto",
                                                                                                    $("#" + e.target.id).attr("goto"));
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
	
	$("#thedoctorisin").click(function(e){
											 var location = $("#thedoctorisin").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
													});
										   });
	
	$("section").delegate("#thedoctorisinsubmit", "click", function(e){
																			e.preventDefault();
																			console.log($('#thedoctorisinform').attr("action"));
																			 $.ajax({
																		            	type: 'post',
																		            	url: $('#thedoctorisinform').attr("action"),
																		            	data: $('#thedoctorisinform').serialize(),
																		            	success: function(response)
                                                                                        {
                                                                                            $("#ajaxcontainer").html(response);
                                                                                            $("#thedoctorisin").hide();
                                                                                            $("#thedoctorisout").show();
																		                }
																			 })
																		  });
    $("section").delegate("#addadminsubmit", "click", function(e){
																			e.preventDefault();
																			console.log($('#addadminform').attr("action"));
																			 $.ajax({
																		            	type: 'post',
																		            	url: $('#addadminform').attr("action"),
																		            	data: $('#addadminform').serialize(),
																		            	success: function(response)
                                                                                        {
                                                                                            $("#ajaxcontainer").html(response);
																		                }
																			 })
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
														$("#closequeue").hide();
														$("#openqueue").show();
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

	$("#viewadmin").click(function(e){
											 var location = $("#viewadmin").attr("goto"); 
											 $.ajax({
												  		url: location,
												  		context: document.body
													}).done(function(response) {
														$("#ajaxcontainer").html(response);
													});
										   });

});
