<?php
function addPlayer($folder,$playerId){
?>	<div class="container" id="playerId<?= $playerId; ?>">
		<div class="column add-bottom">
			<div id="mainwrap">
				<div id="nowPlay">
					<span id="npAction">Paused...</span><span id="npTitle"></span>
				</div>
				<div id="audiowrap">
					<div id="audio0">
						<audio id="audio<?= $playerId; ?>" preload controls>Your browser does not support HTML5 Audio! 😢</audio>
					</div>
					<div id="tracks">
						<a id="btnPrev">&larr;</a><a id="btnNext">&rarr;</a>
					</div>
				</div>
				<div id="plwrap">
					<ul class="plList" id="plList<?= $playerId; ?>"></ul>
				</div>
			</div>
		</div>

	</div>	
<script type="text/javascript">
	jQuery(function ($) {
		'use strict'
		var supportsAudio = !!document.createElement('audio').canPlayType;
		if (supportsAudio) {
			// initialize plyr
			var player<?= $playerId; ?> = new Plyr('#audio<?= $playerId; ?>', {
				controls: [
					'restart',
					'play',
					'progress',
					'current-time',
					'duration',
					'mute',
					'volume',
					'download'
				]
			});
			// initialize playlist and controls
			var index = 0,
				playing = false,
				mediaPath = 'http://localhost/html5_audio_player/<?= $folder; ?>/',
				extension = '',
				tracks = [
				<?php
				// read target folder for files and include as tracks
				
				if ($handle = opendir($folder)) {
					$i = 1;
					while (false !== ($entry = readdir($handle))) {
						if ($entry != "." && $entry != "..") {
							$tracks[] = '
							{
							"track": '.$i.',
							"name": "'.str_replace('.mp3','',$entry).'",
							"duration": "2:46",
							"file": "'.str_replace('.mp3','',$entry).'"
							}';		
							$i++;
						}
					}
					closedir($handle);
					echo implode(',',$tracks);
				}
				?>],
				buildPlaylist = $.each(tracks, function(key, value) {
					var trackNumber = value.track,
						trackName = value.name,
						trackDuration = value.duration;
					if (trackNumber.toString().length === 1) {
						trackNumber = '0' + trackNumber;
					}
					$('#playerId<?= $playerId; ?> #plList<?= $playerId; ?>').append('<li> \
						<div class="plItem"> \
							<span class="plNum">' + trackNumber + '.</span> \
							<span class="plTitle">' + trackName + '</span> \
							<span class="plLength">' + trackDuration + '</span> \
						</div> \
					</li>');
				}),
				trackCount = tracks.length,
				npAction = $('#playerId<?= $playerId; ?> #npAction'),
				npTitle = $('#playerId<?= $playerId; ?> #npTitle'),
				audio = $('#audio<?= $playerId; ?>').on('play', function () {
					playing = true;
					npAction.text('Now Playing...');
				}).on('pause', function () {
					playing = false;
					npAction.text('Paused...');
				}).on('ended', function () {
					npAction.text('Paused...');
					if ((index + 1) < trackCount) {
						index++;
						loadTrack(index);
						audio.play();
					} else {
						audio.pause();
						index = 0;
						loadTrack(index);
					}
				}).get(0),
				btnPrev = $('#playerId<?= $playerId; ?> #btnPrev').on('click', function () {
					if ((index - 1) > -1) {
						index--;
						loadTrack(index);
						if (playing) {
							audio.play();
						}
					} else {
						audio.pause();
						index = 0;
						loadTrack(index);
					}
				}),
				btnNext = $('#playerId<?= $playerId; ?> #btnNext').on('click', function () {
					if ((index + 1) < trackCount) {
						index++;
						loadTrack(index);
						if (playing) {
							audio.play();
						}
					} else {
						audio.pause();
						index = 0;
						loadTrack(index);
					}
				}),
				li = $('#playerId<?= $playerId; ?> #plList<?= $playerId; ?> li').on('click', function () {
					var id = parseInt($(this).index());
					if (id !== index) {
						playTrack(id);
					}
				}),
				loadTrack = function (id) {
					$('#playerId<?= $playerId; ?> .plSel').removeClass('plSel');
					$('#playerId<?= $playerId; ?> #plList li:eq(' + id + ')').addClass('plSel');
					npTitle.text(tracks[id].name);
					index = id;
					audio.src = mediaPath + tracks[id].file + extension;
					updateDownload(id, audio.src);
				},
				updateDownload = function (id, source) {
					player<?= $playerId; ?>.on('loadedmetadata', function () {
						$('a[data-plyr="download"]').attr('href', source);
					});
				},
				playTrack = function (id) {
					loadTrack(id);
					audio.play();
				};
			extension = audio.canPlayType('audio/mpeg') ? '.mp3' : audio.canPlayType('audio/ogg') ? '.ogg' : '';
			loadTrack(index);
		} else {
			// no audio support
			$('.column').addClass('hidden');
			var noSupport = $('#audio<?= $playerId; ?>').text();
			$('.container').append('<p class="no-support">' + noSupport + '</p>');
		}
	});
	</script>

	
	<?php

}
?>