let intervalId = null;
let hour = 0;
let minute = 0;
let seconds = 0;
let totalSeconds = 0;

function startTimer() {
    ++totalSeconds;
    hour = Math.floor(totalSeconds / 3600);
    minute = Math.floor((totalSeconds - hour * 3600) / 60);
    seconds = totalSeconds - (hour * 3600 + minute * 60);

    hour = hour < 10 ? "0" + hour : hour;
    minute = minute < 10 ? "0" + minute : minute;
    seconds = seconds < 10 ? "0" + seconds : seconds;

    document.getElementById("hour").innerHTML = hour;
    document.getElementById("minute").innerHTML = minute;
    document.getElementById("seconds").innerHTML = seconds;
}

URL = window.URL || window.webkitURL;
let gumStream;
let rec;
let input;
let AudioContext = window.AudioContext || window.webkitAudioContext;
let audioContext;
let recorder = document.getElementsByClassName('wpast-recorder')[0];
let recordButton;
let stopButton;
let stopImg;
let pauseButton;
let recordingsList;

if (recorder) {
    recordButton = document.getElementById("record-button");
    stopButton = document.getElementById("stop-button");
    stopImg = stopButton.getElementsByTagName("img")[0];
    pauseButton = document.getElementById("pause-button");
    recordingsList = document.getElementById("wpast-recordings-list").getElementsByClassName('wpast-row')[0];

    recordButton.addEventListener("click", startRecording);
    stopButton.addEventListener("click", stopRecording);
}

function startRecording(e) {
    e.preventDefault();
    if (this.classList.contains('wpast-disabled')) return false;
    this.classList.add("wpast-pulse-button");
    this.classList.add("wpast-disabled");
    stopButton.classList.remove("wpast-disabled");
    let constraints = {audio: true, video: false}
    recordButton.disabled = true;
    stopButton.disabled = false;

    navigator.mediaDevices.getUserMedia(constraints).then(function (stream) {
        audioContext = new AudioContext();
        gumStream = stream;
        input = audioContext.createMediaStreamSource(stream);
        rec = new Recorder(input, {numChannels: 1})
        rec.record()
        intervalId = setInterval(startTimer, 1000);
    });
}

function pauseRecording(e) {
    e.preventDefault();
    recordButton.classList.remove("wpast-pulse-button");
    if (rec.recording) {
        rec.stop();
        pauseButton.innerHTML = '<img src="' + WPAST_DATA.img_url + 'play.svg" width="12" height="12" alt="play">';
        recordButton.classList.remove("wpast-pulse-button");
    } else {
        rec.record()
        pauseButton.innerHTML = '<img src="' + WPAST_DATA.img_url + 'pause.svg" width="12" height="12" alt="pause">';
        recordButton.classList.add("wpast-pulse-button");
    }
}

function stopRecording(e) {
    e.preventDefault();
    if (this.classList.contains('wpast-disabled')) return false;
    this.classList.add("wpast-disabled");
    recordButton.classList.remove("wpast-pulse-button");
    recordButton.classList.remove("wpast-disabled");
    rec.stop();
    gumStream.getAudioTracks()[0].stop();
    rec.exportWAV(createDownloadLink);
    if (intervalId) {
        clearInterval(intervalId);
        totalSeconds = 0;
    }
}

function createDownloadLink(blob) {
    let url = URL.createObjectURL(blob);

    let recordings = document.createElement('input');
    recordings.type = 'hidden';
    recordings.name = recorder.getAttribute('data-name') + '[]';
    recordings.classList.add('recordings');

    let audioCol = document.createElement('div');
    audioCol.classList.add('wpast-col-12');
    audioCol.classList.add('wpast-col-md-3');
    audioCol.classList.add('wpast-col-lg-4');
    let audioDiv = document.createElement('div');
    audioDiv.classList.add('wpast-record');
    let remove = document.createElement('img');
    remove.classList.add('wpast-remove');
    remove.src = WPAST_DATA.img_url + 'remove.svg';
    audioDiv.appendChild(remove);
    let check = document.createElement('img');
    check.classList.add('wpast-check');
    check.src = WPAST_DATA.img_url + 'check.svg';
    audioDiv.appendChild(check);
    let img = document.createElement('img');
    img.src = WPAST_DATA.img_url + 'voice.svg';
    img.classList.add('wpast-waveform');
    img.width = 128;
    img.height = 128;
    audioDiv.appendChild(img);
    let audio = document.createElement('audio');
    audio.controls = true;
    audio.src = url;
    audioDiv.appendChild(audio);

    stopImg.src = WPAST_DATA.img_url + 'loader.svg';
    let filename = 'Voice_' + Math.round(new Date().getTime() / 1000) + '.wav';

    let xhr = new XMLHttpRequest();
    xhr.onload = function (e) {
        if (this.readyState === 4) {
            let results = JSON.parse(e.target.responseText);
            if (results.success === true && results.url) {
                recordings.value = results.url;
                audioDiv.appendChild(recordings);
                audioCol.appendChild(audioDiv);
                recordingsList.appendChild(audioCol);
            }

            stopImg.src = WPAST_DATA.img_url + 'stop.svg';
        }
    };

    let fd = new FormData();
    fd.append("audio_data", blob, filename);
    fd.append('action', 'wpast_upload_voice');
    fd.append('nonce', document.getElementsByName("wpast-nonce")[0].getAttribute('content'));
    let ajaxURL = document.querySelector("body").classList.contains("wp-admin") ? ajaxurl : WPAST_DATA.ajax_url;
    xhr.open("POST", ajaxURL, true);
    xhr.send(fd);
}

const addEventForChild = function (parent, eventName, childSelector, cb) {
    parent.addEventListener(eventName, function (event) {
        const clickedElement = event.target, matchingChild = clickedElement.closest(childSelector);
        if (matchingChild) cb(matchingChild);
    })
};
addEventForChild(parent, 'click', '.wpast-remove', function (childElement) {
    childElement.parentElement.parentElement.remove();
})
