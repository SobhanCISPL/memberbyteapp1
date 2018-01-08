var color = '';
$( document ).ready(function() {
	// var full = window.location.host;
	// var parts = full.split('.')
	// var sub = parts[0];
	$.ajax({
            type: "POST",
            url: 'app/api-data',
            success: function( response ) {
            	console.log(response);
                // $("#ajaxResponse").append("<div>"+msg+"</div>");
            }
        });
});