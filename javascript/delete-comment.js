function delete_comment(id) {
	$.ajax({
		type: 'POST',
		data: { id },
		url: 'delete-comment.php',
		success: function(data) {
			location.reload();
		}
	});
}