function delete_post(id) {
	$.ajax({
		type: 'POST',
		data: { id },
		url: 'delete-post.php',
		success: function(data) {
			location.reload();
		}
	});
}