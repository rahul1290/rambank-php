$(document).ready(function() {
	
	//------------------********--------------------------
    var baseUrl = $('#baseUrl').val();
    google.charts.load('current', { 'packages': ['bar'] });
    verifyedChilds();
    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const d = new Date();

    var std_flag = 0;
    function verifyedChilds() {
        $.ajax({
            type: 'GET',
            url: baseUrl + 'parent/Master_ctrl/verifyedChilds',
            headers: { "vivaartaAuthkey": getKey() },
            dataType: 'json',
            beforeSend: function() { $('#academic_performance').html('<div class="text-center"><img src="' + baseUrl + 'assets/images/loader.gif" width="50"></div>'); },
            success: function(response) {
            	std_flag = 1;
                var x = '';
                $.each(response, function(key, value) {
                    if (key == 0) {
                        var selected = 'selected';
                    } else {
                        var selected = '';
                    }
                    x = x + '<option data-sch_id="'+value.sch_id+'" data-std_id="' + value.std_id + '" data-class_id="' + value.class_id + '" data-class_name="' + value.class_name + '" value="' + value.adm_no + '" ' + selected + '>' + value.name + '</option>';
                });
                $('#select_child').html(x);
                $('#std_class').html('<option value="' + response[0].class_id + '">' + response[0].class_name + '</option>');
            },
            error: function(error) {
                alert(error.responseJSON);
            },
            complete: function(data) {
            	if(std_flag){
            		$('#std_name').html('Academic Performance of ' + data.responseJSON[0].name+' From 2017-18 & 2018-19').css('display', 'block');
                    google.setOnLoadCallback(function() { academicPerformance(data.responseJSON[0].std_id,data.responseJSON[0].adm_no); });
                    getClassTeachers(data.responseJSON[0].std_id);
                    dashboardLeaveList_post(data.responseJSON[0].std_id);
                    //attendance_record(data.responseJSON[0].std_id);
                    student_attendance(data.responseJSON[0].std_id);
                    notifications(data.responseJSON[0].std_id);
                    schoolNotifications(data.responseJSON[0].std_id);
                    stdFeeDisplay(data.responseJSON[0].std_id);
                    school_details_filter(data.responseJSON[0].sch_id);
                    assessment_feedback(data.responseJSON[0].std_id);
            	}
            }
        });
    }

    //---------------change student------------------------------
    $(document).on('change', '#select_child', function() {
        var adm_no = $(this).val();
        var class_id = $('#select_child option:selected').data('class_id');
        var class_name = $('#select_child option:selected').data('class_name');
        var std_id = $('#select_child option:selected').data('std_id');
        var sch_id = $('#select_child option:selected').data('sch_id');
        $('#std_class').html('<option value="' + class_id + '">' + class_name + '</option>');
        
        var studen_name = $('#select_child option:selected').html();
        $('#std_name').html('Academic Performance of ' + studen_name).css('display', 'block');
        
        getClassTeachers(std_id);
        //attendance_record(std_id);
        student_attendance(std_id);
        notifications(std_id);
        stdFeeDisplay(std_id);
        dashboardLeaveList_post(std_id);
        schoolNotifications(std_id);
        school_details_filter(sch_id);
        assessment_feedback(std_id);
        google.setOnLoadCallback(function() { academicPerformance(std_id,adm_no); });
    });
    
    //------------------assessment feedback details-----------------
    function assessment_feedback(std_id){
    	$.ajax({
    		type:'POST',
    		url: baseUrl + 'parent/Master_ctrl/assessmentFeedback',
            headers: { "vivaartaAuthkey": getKey() },
            data:{'std_id':std_id},
            dataType:'json',
            beforeSend:function(){},
            success:function(response){
            	var x='';
            	$.each(response,function(key,value){
            		if(value.view_status == 1){
            			var view_status = 'Viewed';
            		}else{
            			var view_status = 'New';
            		}
            		x=x+'<tr>'+
            			'<td>'+parseInt(key+1)+'</td>'+
            			'<td>'+value.warning_no+'</td>'+
            			'<td>'+value.created_at+'</td>'+
            			'<td>'+view_status+'</td>'+
            			'<td><a href="javascript:void(0);" class="view_feedback" data-sfb_id="'+value.sfb_id+'" data-ses_id="'+value.ses_id+'" data-sch_id="'+value.sch_id+'" data-adm_no="'+value.adm_no+'">View</a></td>'+
            			'</tr>';
            	});
            	$('#assment_feedback').html(x);
            },
            error:function(){
            	$('#assment_feedback').html('<tr><td colspan="5" style="text-align:center;">No assessment feedback.</td></tr>');
            }
    	});
    }
    
    //---------------view feedback-------------
    $(document).on('click','.view_feedback',function(){
    	var ses_id = $(this).data('ses_id');
    	var sch_id = $(this).data('sch_id');
    	var adm_no = $(this).data('adm_no');
    	var sfb_id = $(this).data('sfb_id');
    	$.ajax({
    		type:'POST',
    		url:baseUrl+'parent/Master_ctrl/view_feedback',
    		data:{'ses_id':ses_id,'sch_id':sch_id,'adm_no':adm_no,'sfb_id':sfb_id},
    		beforeSend:function(){
    			$('#loader').modal('show');
    		},
    		success:function(response){
    			var x='';
    				x=x+'<tr>'+
    					'<td>';
    				var i = 1;
    				$.each(response.feedback,function(fKey,fVal){
    					x=x+'<p><b>'+parseInt(i++)+')</b> '+fVal.feedback+'</p>';
    				});
    				
    				if(response.custom_msg != ''){
    					x=x+'<p><b>'+parseInt(i++)+')</b> '+response.custom_msg+'</p>';
    				}
    				
    				x=x+'</td><td>';
    				$.each(response.action_taken,function(atKey,atVal){
    					x=x+'<p><b>'+parseInt(atKey + 1)+')</b> '+atVal.description+'</p>';
    				});
    				x=x+'</td></tr>';
    			$('#feedback_details').html(x);
    		},
    		error:function(){
    			alert('Something went wrong!');
    		},
    		complete:function(){
    			$('#loader').modal('hide');
    			$('#feedback_modal').modal('show');
    			var std_id = $('#select_child option:selected').data('std_id');
    			assessment_feedback(std_id);
    		}
    	});
    });
    
    //--------------------Student wise change school logo-----------------
    function school_details_filter(sch_id){
    	var sch_id =  parseInt(sch_id);
    	var x='';
    	if(sch_id == 1){
    		x=x+'<div class="float-left">'+
	        '<img class="float-left" style="width:50px;" alt="" src ="'+baseUrl+'assets/images/school-logo/shakuntala.png" />'+
	        '<div class="school-name" style="color:#7b2d0f;">'+
	           '<span>SHAKUNTALA VIDYALAYA</span><br>'+
			    '<p class="mb-0" style="font-size:14px;line-height: 16px;">Ramnagar, Bhilai</p>'+ 
	        '</div>'+
	    '</div>';
    		$('#sharda_activities').css('display','none');
     		$('#shakuntala_activities').css('display','block');
    	}else{
    		x=x+'<div class="float-left">'+
	        '<img class="float-left" style="width:50px;" alt="" src ="'+baseUrl+'assets/images/school-logo/sharda-logo.png" />'+
	        '<div class="school-name" style="color:#7b2d0f;">'+
	           '<span>SHARDA VIDYALAYA</span><br>'+
			    '<p class="mb-0" style="font-size:14px;line-height: 16px;">Risali, Bhilai</p>'+ 
	        '</div>'+
	    '</div>';
    		$('#shakuntala_activities').css('display','none');
    		$('#sharda_activities').css('display','block');
    	}
    	$('#school_logo').html(x);
    	
    }
    
    //------------------add new leave intimation----------------------
    $(document).on('click', '#new_application', function() {
        var std_id = $('#select_child option:selected').data('std_id');
        window.location.href = baseUrl + "parent/leave-application/" + std_id;
    });

    
    //-------------student message to class teacher------------------
    function getClassTeachers(std_id) {
        $.ajax({
            type: 'POST',
            url: baseUrl + 'parent/Student_ctrl/getClassTeachers',
            headers: { "vivaartaAuthkey": getKey() },
            data: { 'std_id': std_id },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                var x = '<option value="">Select Teacher</option>';
                $.each(response, function(key, value) {
                    x = x + '<option value="' + value.t_id + '">' + value.teacher_name + '</option>';
                });
                $('#std_teachers').html(x);
            },
            error: function(error) {
                $('#std_teachers').html('<option value="">Select Teacher</option>');
            },
        });
    }

    //-----------student acadmin performace---------------------
    function academicPerformance(std_id,adm_no) {
        $.ajax({
            type: 'POST',
            url: baseUrl + 'parent/Student_ctrl/academicPerformance',
            headers: { "vivaartaAuthkey": getKey() },
            data: { 'std_id':std_id,'adm_no': adm_no },
            dataType: 'json',
            beforeSend: function() { $('#academic_performance').html('<div class="text-center"><img src="' + baseUrl + 'assets/images/loader.gif" width="30"></div>'); },
            success: function(response) {
                var data = new google.visualization.DataTable();
                data.addColumn('string', 'Subjects'); // first column is bottom name
                data.addColumn('number', '2017-18');
                data.addColumn('number', '2018-19');
                $.each(response, function(key, value) {
                    data.addRow(value);
                });
                var options = {
                    chart: {
                        //					            title: 'Company Performance',
                        //					            subtitle: 'Sales, Expenses, and Profit: 2014-2017',
                    },
                    bars: 'vertical', // Required for Material Bar Charts.
                    hAxis: { format: 'number' },
                    height: 300,
                    backgroundColor: { fill: 'transparent' },
                    legend: { position: 'none' },
                    //vAxis: { viewWindow: { min: 0, max: 100 } },
                    colors: ['#00b12d', '#ff5722']
                };
                var chart = new google.charts.Bar(document.getElementById('academic_performance'));
                chart.draw(data, google.charts.Bar.convertOptions(options));
            },
            error: function(error) {
                $('#academic_performance').html(error.responseJSON).css('display', 'block');
            }
        });
    }

    //-------------------student send message------------------------------
    $(document).on('click', '#send_msg', function() {
        var msg_text = $('#msg_text').val();
        var std_class = $('#std_class').val();
        var std_teachers = $('#std_teachers').val();
        var std_id = $('#select_child option:selected').data('std_id');
        var formvalidate = true;
        if (msg_text == '') {
            $('#msg_text_err').html('This is Required.').css('display', 'block');
            formvalidate = false;
        } else {
            $('#msg_text_err').css('display', 'none');
        }

        if (std_class == '') {
            $('#std_class_err').html('This is Required.').css('display', 'block');
            formvalidate = false;
        } else {
            $('#std_class_err').css('display', 'none');
        }

        if (std_teachers == '') {
            $('#std_teachers_err').html('This is Required.').css('display', 'block');
            formvalidate = false;
        } else {
            $('#std_teachers_err').css('display', 'none');
        }

        if (formvalidate) {
            if (confirm('Are you sure!')) {
                $.ajax({
                    type: 'POST',
                    url: baseUrl + 'parent/Master_ctrl/teacher_notifications',
                    headers: { "vivaartaAuthkey": getKey() },
                    data: { 'std_id': std_id, 'msg_text': msg_text, 'std_class': std_class, 'std_teachers': std_teachers },
                    dataType: 'json',
                    beforeSend: function() {},
                    success: function(response) {
                        alert(response);
                    },
                    complete: function() {
                        $('#msg_text').val('');
                        $('#std_teachers').prop('selectedIndex', '');
                    },
                    error: function() {
                    	
                    },
                });
            }
        }

    });

    
    //---------------student attendance details-----------------
    function student_attendance(std_id){
    	$.ajax({
    		type:'POST',
    		url:baseUrl+'parent/Master_ctrl/student_attendance',
    		headers: { "vivaartaAuthkey": getKey() },
    		data: { 'std_id': std_id },
    		dataType: 'json',
            beforeSend: function() {},
            success:function(response){
            	if(parseInt(response) >= 75){
            		var atten_color = 'green';
            	}else{
            		var atten_color = 'orange';
            	}
            	var x='<tr><td style="text-align:center;"><div class="clearfix"><div class="c100 p'+response+' '+atten_color+' small"><span>'+response+'%</span><div class="slice"><div class="bar"></div><div class="fill"></div></div></div></div></td></tr>';
            	x = x + '<tr><td><a href="' + baseUrl + 'parent/attendance-record/' + std_id + '"><i class="fa fa-th-large"></i> View Month Wise</a></td></tr>';
            	$('#attendance_details').html(x);
            },
            error: function(error) {
                $('#attendance_details').html('<tr><td colspan="4" style="text-align:center;">No attendance record.</td></tr> <tr><td colspan="4"><a href="' + baseUrl + 'parent/attendance-record/' + std_id + '"><i class="fa fa-th-large"></i> View Month Wise</a></td></tr>');
            }
    	});
    	
    }
    
    
//    function attendance_record(std_id) {
//        $.ajax({
//            type: 'POST',
//            url: baseUrl + 'parent/Master_ctrl/todayAttendance',
//            headers: { "vivaartaAuthkey": getKey() },
//            data: { 'std_id': std_id },
//            dataType: 'json',
//            beforeSend: function() {},
//            success: function(response) {
////                if (response[0].total_period == null) {
////                    var period = 0;
////                } else {
////                    var period = response[0].total_period;
////                }
////
////                if (response[0].present == null) {
////                    var present = 0;
////                } else {
////                    present = response[0].present;
////                }
////
////                if (response[0].absent == null) {
////                    var absent = 0;
////                } else {
////                    var absent = response[0].absent;
////                }
////
////                if (response[0].leave == null) {
////                    var leave = 0;
////                } else {
////                    var leave = response[0].leave;
////                }
////
////                var x = '';
////                x = x + '<tr>' +
////                    '<td>' + period + '</td>' +
////                    '<td>' + present + '</td>' +
////                    '<td>' + absent + '</td>' +
////                    '<td>' + leave + '</td>' +
////                    '</tr>';
////                x = x + '<tr><td colspan="4"><a href="' + baseUrl + 'parent/attendance-record/' + std_id + '"><i class="fa fa-th-large"></i> View Month Wise</a></td></tr>';
////                $('#attendance_details').html(x);
//            	
//            	
//            	if(parseInt(response[0].present) > parseInt(0)){
//            		var attendance = "<b>Present</b>";
//            	}else{
//            		var attendance = "<b>Absent</b>";
//            	}
//            	var x = '';
//	              x = x + '<tr>' +
//	                  '<td colspan="4">' + attendance + '</td>' +
//	                '</tr>';
//            	x = x + '<tr><td colspan="4"><a href="' + baseUrl + 'parent/attendance-record/' + std_id + '"><i class="fa fa-th-large"></i> View Month Wise</a></td></tr>';
//            	$('#attendance_details').html(x);
//            },
//            error: function(error) {
//                $('#attendance_details').html('<tr><td colspan="4" style="text-align:center;">Record not found.</td></tr> <tr><td colspan="4"><a href="' + baseUrl + 'parent/attendance-record/' + std_id + '"><i class="fa fa-th-large"></i> View Month Wise</a></td></tr>');
//            }
//        });
//    }

    //--------------leave details------------------
    function dashboardLeaveList_post(std_id) {
        $.ajax({
            type: 'POST',
            url: baseUrl + 'parent/Master_ctrl/dashboardLeaveList',
            headers: { "vivaartaAuthkey": getKey() },
            data: { 'std_id': std_id },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                var x = '';
                $.each(response, function(key, value) {
                    if (value.approval == 'Viewed') {
                        var class_is = 'approved';
                    } else {
                        var class_is = 'disapproved';
                    }
                    x = x + '<tr>' +
                        '<td>' + parseInt(key + 1) + '.</td>' +
                        '<td class="text-center">' + value.created_at + '</td>' +
                        '<td class="text-center">' + value.from_date + '</td>' +
                        '<td class="text-center">' + value.to_date + '</td>' +
                        '<td class="text-center ' + class_is + '">' + value.approval + '</td>' +
                        '</tr>';
                });
                x = x + '<tr><td colspan="3"><a href="' + baseUrl + 'parent/leave-list/' + std_id + '"><i class="fa fa-th-large"></i> View All</a></td></tr>';
                $('#list_leave').html(x);
            },
            error: function(error) {
                $('#list_leave').html('<tr><td align="center" colspan="5">No leave intimation.</td></tr><tr><td colspan="5"><a href="' + baseUrl + 'parent/leave-list/' + std_id + '"><i class="fa fa-th-large"></i> View All</a></td></tr>');
            },
        });
    }


    //--------------teachers notification------------------------
    function notifications(std_id) {
        $.ajax({
            type: 'POST',
            url: baseUrl + 'parent/Master_ctrl/getNotifications',
            headers: { "vivaartaAuthkey": getKey() },
            data: { 'std_id': std_id },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                var x = '';
                $.each(response, function(key, value) {
                    if (value.nstatus == 'new') {
                        var new_img = '<img alt="" src="' + baseUrl + 'assets/images/new.gif"/>';
                    } else {
                        new_img = '';
                    }
                    x = x + '<p>' + value.msg_body + '</p>';
                });
                $('#teacher_alert').html(x);
            },
            error:function(){
            	 $('#teacher_alert').html('');
            }
        });
    }


    //---------admin notification------------------------
    function schoolNotifications(std_id) {
        $.ajax({
            type: 'POST',
            url: baseUrl + 'parent/Master_ctrl/getSchoolNotifications',
            headers: { "vivaartaAuthkey": getKey() },
            data: { 'std_id': std_id },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
                var x = '';
                $.each(response, function(key, value) {
                    if (value.nstatus == 'new') {
                        var new_img = '<img alt="" src="' + baseUrl + 'assets/images/new.gif"/>';
                    } else {
                        new_img = '';
                    }
                    x = x + '<p>' + value.msg_body + '</p>';
                });
                $('#school_notification').html(x);
            },
            error:function(){
            	$('#school_notification').html('');
            },
        });
    }

    //----------open notification-------------------------------
    $(document).on('click', '.open_notice', function() {
        var n_id = $(this).data('n_id');
        $.ajax({
            type: 'POST',
            url: baseUrl + 'parent/Master_ctrl/open_notice',
            headers: { "vivaartaAuthkey": getKey() },
            data: { 'n_id': n_id },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {},
        });
    });

    //--------------go to fee detail fee----------------------
    $(document).on('click', '#fee_details', function() {
        var std_id = $('#select_child option:selected').data('std_id');
        window.location.href = baseUrl + "parent/fee-details/" + std_id;
    });
    
    //-------------go to payment history----------------------
    $(document).on('click', '#payment_history', function() {
        var std_id = $('#select_child option:selected').data('std_id');
        window.location.href = baseUrl + "parent/payment-history/" + std_id;
    });


    //-------------display fee details-----------------------
    function stdFeeDisplay(std_id) {
        $.ajax({
            type: 'POST',
            url: baseUrl + 'parent/Master_ctrl/stdFeeDisplay',
            headers: { "vivaartaAuthkey": getKey() },
            data: { 'std_id': std_id },
            dataType: 'json',
            beforeSend: function() {},
            success: function(response) {
            	if(response == "No Fee"){
            		$('#online_fee').css('display','none');
            		$('#no_fee').css('display','block');
            		$('#payment_history').css('display','none');
            		$('#no_fee_msg').html("No Fee").css('display', 'block');
            	}else{
            		$('#online_fee').css('display','block');
            		$('#no_fee').css('display','none');
            		$('#payment_history').css('display','block');
            		if (response[0].pay_status == "Success") {
                    	$('#due_and_paid_date').html('<b>Last Fee Paid on</b> <br/>'+response[0].payment_date+'');
                        $('#fee_amount').html("Rs. "+ parseFloat(response[0].grand_total).toFixed(2) + "/-").css('display', 'block');
                        $('#quick_pay').css('display', 'none');
                        $('#fee_details').css('display', 'none');
                        $('#fee_status').html('<span style="color:#50a81c;">Paid</span>');
                    } else {
                    	$('#due_and_paid_date').html('Due Date <br/>'+response[0].due_date+'');
                        $('#fee_amount').html("Rs. "+ parseFloat(response[0].grand_total).toFixed(2) + "/-").css('display', 'block');
                        $('#quick_pay').css('display', 'block');
                        $('#fee_details').css('display', 'block');
                        $('#fee_status').html('<span style="color:#b60000;">Not Paid</span>');
                    }
            	}
            },
            error: function() {
                $('#fee_amount').html('<span class="text-success">Not Found.</span>.').css('display', 'block');
                $('#quick_pay').css('display', 'none');
                $('#fee_details').css('display', 'none');
            },
        });
    }
    
    //----------------go to payment history page--------------------
    $(document).on('click', '#quick_pay', function() {
        var std_id = $('#select_child option:selected').data('std_id');
        window.location.href = baseUrl + "parent/payment/" + std_id;
    });

    //-----------view acadmic details-------------------------------
    $(document).on('click', '#view_acadmic_details', function() {
        var std_id = $('#select_child option:selected').data('std_id');
        window.location.href = baseUrl + "parent/details-analysis/" + std_id;
    });
});