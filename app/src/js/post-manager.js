import PostManagerScroll from './post-manager-scroll';

export default class PostManager {
    constructor() {
        this.postManagerScroll = new PostManagerScroll(this);
        this.postManagerScroll.addEventListener();
        this.lastReceivedTimeStamp = 0;
        this.nextTimestampToRequest = 0;
        this.receivedTimestamps = {};
        this.requestedTimestamps = {};
        this.plyrIdCounter = 1;
    }

    start() {
        document.addEventListener("DOMContentLoaded",function() {
            this.fetchNextPosts();
        }.bind(this));
    }

    fetchNextPosts() {
        var requestedTimeStamp = this.nextTimestampToRequest;

        if (!isNaN(this.requestedTimestamps[this.nextTimestampToRequest])) {
            return;
        }

        this.requestedTimestamps[this.nextTimestampToRequest] = this.nextTimestampToRequest;

        if (this.nextTimestampToRequest > 0) {
            $('#bottom-loading-modal').show();
        }

        $.get({
            url: '/app02/posts/chihuahua,chihuahuas,chihuahualife,chihuahualove,chihuahuaworld,chihuahualovers,chihuahuasofinstagram,chihuahuastagram,chihuahualover/' + this.nextTimestampToRequest,
            success: function (response) {
                this.lastReceivedTimeStamp = response[response.length - 1].timestamp;

                if (isNaN(this.receivedTimestamps[requestedTimeStamp])) {
                    this.renderPosts(response);
                    this.receivedTimestamps[requestedTimeStamp] = requestedTimeStamp;
                    this.nextTimestampToRequest = this.lastReceivedTimeStamp + 1;
                    $('#main-loading-modal').hide();
                    $('#bottom-loading-modal').hide();
                }

            }.bind(this),
            dataType: 'json'
        });
    }

    renderPosts (apiResponse) {
        var itemsHtml = '',
            plyrIds = [],
            postCounter = 0;

        for (let i = 0; i < apiResponse.length; ++i) {
            itemsHtml = this.renderPost(apiResponse[i], plyrIds);

            $('#post_list').append(itemsHtml);

            if (plyrIds.length > 0) {
                for (let i = 0; i < plyrIds.length; ++i) {
                    this.initPlyr(plyrIds[i]);
                }
            }

            postCounter++;

            if (postCounter % 7 == 0) {
                // here we can add some advertisement
            }
        }
    }

    initPlyr(plyrId) {
        var player = new Plyr('#' + plyrId);

        player.on('ready', event => {
            var playButtons = event.detail.plyr.elements.buttons.play;
            for (let i = 0; i < playButtons.length; ++i) {
                var $element = $(playButtons[i]);
                if ($element.hasClass('plyr__control--overlaid')) {
                    var $svg = $element.find('svg[role=presentation]');
                    $svg.attr('viewBox', '0 0 30 30');
                }
            }
        });
    }

    renderPost(post, plyrIds) {
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
            var match = post.videos.embedCode.match(/<video.*id=["'](\S+)["']/);
            if (match && match.length > 0) {
                videoContainerId = match[1];
            }
            if (!videoContainerId) {
                videoContainerId = 'plyr-id-' + this.plyrIdCounter++;
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
}