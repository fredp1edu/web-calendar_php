
"use strict";

var processFile = "assets/inc/ajax.inc.php";
var overlayOn = false;

var fx = {
	initModal :  function() {

		if ($('.modal-window').length == 0) {
			return $('<div>')
					.hide()
					.addClass("modal-window")
					.appendTo("body");
		}
		else {
			return $(".modal-window");
		}
	},
	initModal2 :  function() {

		if ($('.modal-window2').length == 0) {
			return $('<div>')
					.hide()
					.addClass("modal-window2")
					.appendTo("body");
		}
		else {
			return $(".modal-window2");
		}
	},
	boxIn : function(data, modal) {
		if (!overlayOn) {
			$("<div>").hide().addClass("modal-overlay").click(function(event) {
				fx.boxOut(event);
			}).appendTo("body");	
		}
		modal.hide().append(data).appendTo("body");
		$(".modal-window").fadeIn("fast");
		if (!overlayOn) {
			$(".modal-overlay").fadeIn("fast");
			overlayOn = true;	
		}
	},
	boxOut : function(event) {
		if (event != undefined) 
			event.preventDefault();
		$('a').removeClass("active");
		$(".modal-window").fadeOut('slow', function() {
			$(this).remove();
		});
		if ($('.modal-window2').length == 0) {
			$(".modal-overlay").fadeOut('slow', function() {
				$(this).remove();
			});
			overlayOn = false;
		}
	},
	boxIn2 : function(data, modal) {
		$("<div>").hide().addClass("modal-overlay").click(function(event) {
			fx.boxOut(event);
		}).appendTo("body");
		modal.hide().append(data).appendTo("body");
		$(".modal-window2, .modal-overlay").fadeIn("fast");
		overlayOn = true;
	},
	boxOut2 : function(event) {
		if (event != undefined) 
			event.preventDefault();
		$('a').removeClass("active");
		$(".modal-window2, .modal-overlay").fadeOut('slow', function() {
			$(this).remove();
		});
		overlayOn = false;
	}    
};
$('document').ready(function() {
	
    $('body').on('click', '.event, .dispTitle', function(event) {
    	event.preventDefault();
    	$(this).addClass("active");
    	var data = $(this)
    					.attr('href')
    					.replace(/.+?\?(.*)$/, "$1");
    	var modal = fx.initModal();

    	$("<a>") 
    		.attr("href", "#")
    		.addClass("modal-close-btn")
    		.html("&times;")
    		.click(function(event) {
    			fx.boxOut(event);
    		})
    		.appendTo(modal);

    	$.ajax({
    		type: "POST",
    		url: processFile,
    		data: "action=event_view&" + data,
    		success: function(data) {
    			fx.boxIn(data, modal);
    		},
    		error: function(msg) {
    			modal.append(msg);
    		}
    	});
    });
    $('body').on('click', '.dateNum, .viewMonth', function(event) {
    	event.preventDefault();
    	$(this).addClass("active");
    	var data = $(this)
    					.attr('href')
    					.replace(/.+?\?(.*)$/, "$1");
    	var modal2 = fx.initModal2();

    	$("<a>") 
    		.attr("href", "#")
    		.addClass("modal-close-btn")
    		.html("&times;")
    		.click(function(event) {
    			fx.boxOut2(event);
    		})
    		.appendTo(modal2);

    	$.ajax({
    		type: "POST",
    		url: processFile,
    		data: data,
    		success: function(data) {
    			fx.boxIn2(data, modal2);
    		},
    		error: function(msg) {
    			modal2.append(msg);
    		}
    	});
    });
    /*$('body').on('click', '.admin', function(event) {
    	event.preventDefault();
    	var action = 'edit_event';
    	$.ajax({
    		type: "POST",
    		url: processFile,
    		data: "action=" + action,
    		success: function(data) {
    			var form = $(data).hide();
    			var modal = fx.initModal();
    			fx.boxIn(null, modal);
    			form.appendTo(modal).addClass("edit-form").fadeIn('slow');
    		},
    		error: function(msg) {
    			alert(msg);
    		}
    	});
    }); */
    $('body').on('click', '.edit-form a:contains(cancel)', function(event) {
    	fx.boxOut(event);
    	fx.boxOut2(event);
    });
    $('body').on('click', '.edit-form input[type=submit]', function(event) {
    	event.preventDefault();
    	var formData = $(this).parents('form').serialize();
    	$.ajax({
    		type: 'POST',
    		url: processFile,
    		data: formData,
    		success: function(data) {
    			fx.boxOut();
    			console.log("Event saved");
    		},
    		error: function(msg) {
    			alert(msg);
    		}
    	});
    });
});