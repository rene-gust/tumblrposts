window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent Chrome 67 and earlier from automatically showing the prompt
    e.preventDefault();
    // Stash the event so it can be triggered later.
    deferredPrompt = e;
    // Update UI to notify the user they can add to home screen
    addBtn.style.display = 'block';

    addBtn.addEventListener('click', (e) => {
        // hide our user interface that shows our A2HS button
        addBtn.style.display = 'none';
        // Show the prompt
        deferredPrompt.prompt();
        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the A2HS prompt');
            } else {
                console.log('User dismissed the A2HS prompt');
            }
            deferredPrompt = null;
        });
    });
});

let deferredPrompt;
const addBtn = document.querySelector('.add-button');
addBtn.style.display = 'none';

(function () {
    $(document).ready(function () {
        fetchNextPosts();
    })

    document.querySelector('.page__content').addEventListener('scroll', function (event) {
        var scrollTopPosition = event.target.scrollTop,
            scrollHeight = event.target.scrollHeight,
            clientHeight = event.target.clientHeight,
            paginateTopPosition = scrollHeight - clientHeight - 500;

        if (scrollTopPosition > paginateTopPosition) {
            window.setTimeout(
                function () {
                    fetchNextPosts();
                },
                500
            )
        }

    });

    var lastReceivedTimeStamp = 0,
        nextTimestampToRequest = 0,
        receivedTimestamps = {},
        requestedTimestamps = {};

    function fetchNextPosts() {
        var requestedTimeStamp = nextTimestampToRequest;

        if (!isNaN(requestedTimestamps[nextTimestampToRequest])) {
            return;
        }

        requestedTimestamps[nextTimestampToRequest] = nextTimestampToRequest;

        if (nextTimestampToRequest > 0) {
            $('#bottom-loading-modal').show();
        }

        $.get({
            url: '/app02/posts/chihuahua,chihuahuas,chihuahualife,chihuahualove,chihuahuaworld,chihuahualovers,chihuahuasofinstagram,chihuahuastagram,chihuahualover/' + nextTimestampToRequest,
            success: function (response) {
                lastReceivedTimeStamp = response[response.length - 1].timestamp;

                if (isNaN(receivedTimestamps[requestedTimeStamp])) {
                    renderPosts(response);
                    receivedTimestamps[requestedTimeStamp] = requestedTimeStamp;
                    nextTimestampToRequest = lastReceivedTimeStamp + 1;
                    $('#main-loading-modal').hide();
                    $('#bottom-loading-modal').hide();
                }

            },
            dataType: 'json'
        });
    }

    function renderPosts(apiResponse) {
        var itemsHtml = '',
            plyrIds = [],
            postCounter = 0;

        for (let i = 0; i < apiResponse.length; ++i) {
            itemsHtml = renderPost(apiResponse[i], plyrIds);

            $('#post_list').append(itemsHtml);

            if (plyrIds.length > 0) {
                for (let i = 0; i < plyrIds.length; ++i) {
                    initPlyr(plyrIds[i]);
                }
            }

            postCounter++;

            if (postCounter % 7 == 0) {
                $('#post_list').append(
                    '<ons-list-item>' +
                    '    <ons-card>' +
                    '<ins class="adsbygoogle"\n' +
                    '     style="display:block"\n' +
                    '     data-ad-client="ca-pub-9450457991119200"\n' +
                    '     data-ad-slot="8046968024"\n' +
                    '     data-ad-format="auto"\n' +
                    '     data-full-width-responsive="true"></ins>' +
                    '    </ons-card>' +
                    '</ons-list-item>'
                );
                (adsbygoogle = window.adsbygoogle || []).push({});
            }
        }
    }

    function initPlyr(plyrId) {
        player = new Plyr('#' + plyrId);

        player.on('ready', event => {
            playButtons = event.detail.plyr.elements.buttons.play;
            for (let i = 0; i < playButtons.length; ++i) {
                $element = $(playButtons[i]);
                if ($element.hasClass('plyr__control--overlaid')) {
                    $svg = $element.find('svg[role=presentation]');
                    $svg.attr('viewBox', '0 0 30 30');
                }
            }
        });
    }

    var plyrIdCounter = 1;

    function renderPost(post, plyrIds) {
        var i = 0,
            date = (new Date(post.timestamp * 1000)).toLocaleString(),
            textContent = '',
            imageHtml = '',
            videoContainerId;

        if (post.photos) {
            for (var i = 0; i < post.photos.length; ++i) {
                imageHtml += '<img src="' + post.photos[i].url + '"/><br/>';
            }
        }

        if (post.text) {
            textContent = post.text
        }

        if (post.videos) {

            // determine id
            match = post.videos.embedCode.match(/<video.*id=["'](\S+)["']/);
            if (match && match.length > 0) {
                videoContainerId = match[1];
            }
            if (!videoContainerId) {
                videoContainerId = 'plyr-id-' + plyrIdCounter++;
                post.videos.embedCode = post.videos.embedCode.replace('<video', '<video id="' + videoContainerId + '"');
            }

            post.videos.embedCode = post.videos.embedCode.replace('<video', '<video id="' + videoContainerId + '"');

            plyrIds.push(videoContainerId);

            textContent += '<div class="video-container">' + post.videos.embedCode + '</div>';
        }

        if (imageHtml) {
            textContent += imageHtml;
        }

        return '<ons-list-item>' +
            '    <ons-card>' +
            '       <div class="post-container">' +
            '        <div class="title">From ' + post.blogger + ' at ' + date + '</div>' +
            '        <div class="card__content">' + textContent + '</div>' +
            '       </div>' +
            '    </ons-card>' +
            '</ons-list-item>';
    }
}());

