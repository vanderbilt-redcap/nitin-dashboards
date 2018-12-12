$(function() {
	// click handler for nav buttons
	$("nav div").on("click", function(e) {
		if ($(this).hasClass('selected')) return;
		
		$("nav div").removeClass('selected');
		$(this).addClass('selected');
		
		// ajax requested screen
		// var url = window.location.origin + "/plugins/nitin/index.php";
		$.ajax({
			url: "index.php",
			data: { screen : $(this).text() },
			dataType: "html",
			success : function(data) {
				$("#content").html(data);
			},
			fail : function(data) {
				$("#content").html("<pre>There was an error:\n" + data + "</pre>")
			}
		})
	})
})