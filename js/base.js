$(function() {
	$(".dataTable").DataTable({
		paging: false,
		info: false,
		searching: false
	});
	
	// click handler for nav buttons
	$("nav div").on("click", function(e) {
		// if ($(this).hasClass('selected')) return;
		
		$("nav div").removeClass('selected');
		$(this).addClass('selected');
		
		// fadeOut content div and fadeIn loader before firing ajax request
		$("#content").fadeOut(150, function() {
			$("#loader").fadeIn(100);
		});
		
		// ajax requested screen
		// var url = window.location.origin + "/plugins/nitin/index.php";
		$.ajax({
			url: "index.php",
			data: { screen : $(this).text() },
			dataType: "html",
			success : function(data) {
				$("#content").html(data);
				$(".dataTable").DataTable({
					paging: false,
					info: false,
					searching: false
				});
				document.body.scrollTop = document.documentElement.scrollTop = 0;
				$("#loader").fadeOut(100, function() {
					$("#content").fadeIn(150);
				});
			},
			fail : function(data) {
				$("#content").html("<pre>There was an error:\n" + data + "</pre>")
			}
		})
	})
})