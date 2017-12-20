$(document).ready(function() {
	var selectedItem;

	$('#modal').on('shown.bs.modal', function() {
		$('#txt-search').focus();
	});

	$('#search').click(function() {
		var usersearch = $('#txt-search').val();
		search(usersearch);
	});

	$('#txt-search').bind("enterKey",function(e) {
		var usersearch = $('#txt-search').val();
		search(usersearch);
	});

	$('#txt-search').keyup(function(e) {
		if(e.keyCode == 13)
		{
			$(this).trigger("enterKey");
		}
	});

	$('#btn-post').click(function() {
		post(selectedItem);
	});

	function search(usersearch) {
		$('#music-container').html('');
		var apiurl = 'https://itunes.apple.com/search?media=music&entity=musicTrack&sort=recent&callback=?&term=';

		$.getJSON(apiurl + usersearch, function(data) {

			if (data.results.length === 0) {
				$('#music-container').append('<li id="error">No matches for "' + usersearch + '"</li>');
			} else {
				$.each(data.results, function(i, field) {
					search_artist = field.artistName;
					search_track_name = field.trackName;
					search_album = field.collectionName;
					search_artwork = field.artworkUrl100.replace('100x100','400x400');
					search_preview_url = field.previewUrl;
					search_buy_url = field.trackViewUrl;

					if (search_artwork) {
						buildTracks(i,search_artist,search_track_name,search_album,search_artwork,search_preview_url,search_buy_url);
					}
				});

				var $song = $('.songs').click(function(e) {
					var id = $(this).attr("id");
					selectedItem = data.results[id];
					selectTrack(e, $song, this);
				});
			}
		});
	};

	function selectTrack(e, $song, selected) {
		e.preventDefault();
		$song.removeClass('highlight');
		$(selected).addClass('highlight');
		$('#btn-post').prop("disabled", false);
	}

	function reset() {
		$('#music-container').html('');
		$('#txt-search').val('');
		$('#btn-post').prop("disabled", true);
		$('#modal').modal('hide');
	}

	function buildTracks(i,search_artist,search_track_name,search_album,search_artwork,search_preview_url,search_buy_url) {
		trackinfo = '<div class="audio-container">\n';
		trackinfo += '<img src="' + search_artwork + '">\n';
		trackinfo += '<div class="overlay"></div>\n';
		trackinfo += '<audio class="audio" id="song' + i + '" src="' + search_preview_url + '"></audio>\n';
		trackinfo += '<script>\n';
		trackinfo += 'var audio = require("audio");\n';
		trackinfo += 'var el = document.querySelector("#song' + i + '");\n';
		trackinfo += '$()\n';
		trackinfo += 'audio(el);\n';
		trackinfo += '$("audio").on("play", function() {\n';
		trackinfo += '$("audio").not(this).each(function(index, audio) {\n';
		trackinfo += 'audio.pause();\n';
		trackinfo += '});\n';
		trackinfo += '});\n';
		trackinfo += '$("#modal").on("hidden.bs.modal", function () {\n';
		trackinfo += '$("audio").not(this).each(function(index, audio) {\n';
		trackinfo += 'audio.pause();\n';
		trackinfo += '});\n';
		trackinfo += '});\n';
		trackinfo += '</script>\n';
		trackinfo += '</div>\n';
		trackinfo += '<div class="music-title"><span class="music-artist">' + search_artist + '</span><br/> Song: ' + search_track_name + '<br/> Album: '  + search_album + '</div>\n';
		alltracks = $('<li id="' + i + '" class="songs col-sm-4 thumbnail" tabindex=-1>' + trackinfo + '</li>');

		$('#music-container').append(alltracks);		
		$('#modal-body').focus();
	}

	function post(field) {
		reset();
		$.ajax({
			type: "post",
			url: "post.php",
			data: { artist : field.artistName,
					song : field.trackName,
					album : field.collectionName,
					artwork : field.artworkUrl100.replace("100x100", "600x600"),
					preview : field.previewUrl,
					buy : field.trackViewUrl },
			success : function(data) {
				if (data != "") {
					$('#error').text(data);
					$('#err-modal').modal('toggle');
				} else {
					location.reload();
				}
			}
		});
	}
});