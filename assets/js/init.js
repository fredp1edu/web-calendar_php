
"use strict";

var processFile = "assets/inc/ajax.inc.php";

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
	boxIn : function(data, modal) {
		$("<div>").hide().addClass("modal-overlay").click(function(event) {
			fx.boxOut(event);
		}).appendTo("body");
		modal.hide().append(data).appendTo("body");
		$(".modal-window, .modal-overlay").fadeIn("fast");
	},
	boxOut : function(event) {
		if (event != undefined) 
			event.preventDefault();
		$('a').removeClass("active");
		$(".modal-window, .modal-overlay").fadeOut('slow', function() {
			$(this).remove();
		});
	}  
};
$('document').ready(function() {
	
    $('li>a').click(function(event) {
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