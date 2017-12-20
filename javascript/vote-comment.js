function comment_upvote(id) {
	var points = document.getElementById('points'+id);
	var up = document.getElementById('btn-up'+id);
	var down = document.getElementById('btn-down'+id);

	$.ajax({
		type: 'POST',
		data: { comment_id : id, dir : true},
		datatype: 'text',
		url: 'vote-comment.php',
		success: function(data) {
			$('#points'+id).html(data);

			if (up.classList.contains('up-arrow-click')) {
				up.classList.remove('up-arrow-click');
			} else {
				up.classList.add('up-arrow-click');
			}

			down.classList.remove('down-arrow-click');
		}
	});
}

function comment_downvote(id) {
	var points = document.getElementById('points'+id);
	var up = document.getElementById('btn-up'+id);
	var down = document.getElementById('btn-down'+id);

	$.ajax({
		type: 'POST',
		data: { comment_id : id, dir : false},
		url: 'vote-comment.php',
		success: function(data) {
			$('#points'+id).html(data);

			if (down.classList.contains('down-arrow-click')) {
				down.classList.remove('down-arrow-click');
			} else {
				down.classList.add('down-arrow-click');
			}
			
			up.classList.remove('up-arrow-click');
		}
	});
}