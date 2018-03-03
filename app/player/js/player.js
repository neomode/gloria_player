let SERVER_URL 		= "http://127.0.0.1/gloria/ws.php";
let MEDIA_ROOT 		= "../player_rsc/";
let SYNC_TYPE 	 	= "sync";
let PLAYER_ID		= 1;
let DATA_TYPE 		= "json";
let REQUEST 		= "getmediajson";
let DATETIME_PERIOD = 1 * 1000;
let INIITIAL_VERSE_WAIT_TIME = 3 * 1000;
var SCROLLING_SPEED = 20;

var g_video_playlist = [];
var g_verse_playlist = [];
var g_video_index 	 = 0;
var g_verse_index 	 = 0;
var g_load_in_progress = false;
var g_verse_timer;
var g_datetimer;

$(function() {
	$("#videoContainer video").on('ended', onVideoEnded);

	g_datetimer = setInterval(updateDateTime, DATETIME_PERIOD);
	// g_verse_timer = setTimeout(playVerse, INIITIAL_VERSE_WAIT_TIME);
	loadPlaylist();
});

function loadPlaylist() {
	if(g_load_in_progress) {
		return;
	}

	g_load_in_progress = true;
	let data = {
		object: SYNC_TYPE,
        player_id: PLAYER_ID,
        type: DATA_TYPE,
        request: REQUEST
    }

    $.ajax({
        type: "POST",
        url: SERVER_URL,
        dataType: "json",
        data: data,
        success: function(resp){
            g_video_playlist = resp.videos;
            g_verse_playlist = resp.verses;
            g_video_index = 0;
            playVideo();
            g_load_in_progress = false;
        },
        error: function(errMsg) {
            console.log(errMsg);
        },
        failure: function(errMsg) {
            console.log(errMsg);
        }
    });
};

function playVerse() {
	if(g_verse_playlist.length > 0) {
		if(g_verse_index >= g_verse_playlist.length_) {
			g_verse_index = 0;
		}

		$(".verse").html(g_verse_playlist[g_verse_index].content + " - " + g_verse_playlist[g_verse_index].src );
		g_verse_timer = setTimeout(playVerse, (parseInt(g_verse_playlist[g_verse_index].duration) + SCROLLING_SPEED) * 1000);
		g_verse_index++;
	}
 }

function updateDateTime() {
	$("#clock").html(moment().format("hh:mm"));
	$("#date").html(moment().format("d MMM"));
}

function showVideo(show) {
	if (show) {
		$("#videoContainer").show();
		$(".loader").hide();
	} else {
		$("#videoContainer").hide();
		$(".loader").show();
	}
}

function playVideo() {
	if(g_video_playlist.length > 0) {
		$("#videoContainer video source").attr("src", MEDIA_ROOT + g_video_playlist[g_video_index++].src);
		$("#videoContainer video")[0].load();
		showVideo(true);
	}
}

function onVideoEnded() {
	if(g_video_index >= g_video_playlist.length) {
		loadPlaylist();
	} else {
		playVideo();
	}
}