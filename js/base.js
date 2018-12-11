$(function() {
	// click handler for nav buttons
	$("nav div").on("click", function(e) {
		$("nav div").removeClass('selected');
		$(this).addClass('selected');
	})
})