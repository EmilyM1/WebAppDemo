$(document).ready(function() {

	$("tbody tr td:first-child").each(function() {
		$(this).on("click", function() { 
			var regex = new RegExp('</?' + $(this).text() + '[^>]*>'); 
			$("#response").removeHighlight().highlight(regex);
		});
	});

});
