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
				
				Nitin.tables = []
				$(".dataTable").each(function(index, item) {
						Nitin.tables.push($(item).DataTable())
				})
				Nitin.order_tables()
				
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

Nitin = {}
Nitin.order_tables = function() {
	Nitin.tables.forEach(function(table, index) {
		var col = $(table.table().node()).attr('data-order-col')
		var direction = $(table.table().node()).attr('data-order-direction')
		
		if (col && direction) {
			table.order([col, direction])
			table.draw()
		}
	})
}