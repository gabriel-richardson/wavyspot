function upvote(id) {
	var points = document.getElementById('points'+id);
	var up = document.getElementById('btn-up'+id);
	var down = document.getElementById('btn-down'+id);

	$.ajax({
		type: 'POST',
		data: { post_id : id, dir : true},
		datatype: 'text',
		url: 'vote.php',
		success: function(data) {
			$('#points'+id).html(data);

			if (up.classList.contains('up-arrow-click')) {
				up.classList.remove('up-arrow-click');
			} else {
				up.classList.add('up-arrow-click');
			}

			if (points.classList.contains('up-arrow-click')) {
				points.classList.remove('up-arrow-click');
			} else {
				points.classList.remove('down-arrow-click');
				points.classList.add('up-arrow-click');
			}

			down.classList.remove('down-arrow-click');
		}
	});
}

function downvote(id) {
	var points = document.getElementById('points'+id);
	var up = document.getElementById('btn-up'+id);
	var down = document.getElementById('btn-down'+id);

	$.ajax({
		type: 'POST',
		data: { post_id : id, dir : false},
		url: 'vote.php',
		success: function(data) {
			$('#points'+id).html(data);

			if (down.classList.contains('down-arrow-click')) {
				down.classList.remove('down-arrow-click');
			} else {
				down.classList.add('down-arrow-click');
			}

			if (points.classList.contains('down-arrow-click')) {
				points.classList.remove('down-arrow-click');
			} else {
				points.classList.remove('up-arrow-click');
				points.classList.add('down-arrow-click');
			}
			
			up.classList.remove('up-arrow-click');
		}
	});
}