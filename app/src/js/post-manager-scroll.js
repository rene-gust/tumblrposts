export default class PostManagerScroll {

    constructor(postManager) {
        this.postManager = postManager;
    }

    addEventListener () {
        document.addEventListener("DOMContentLoaded",function() {
            document.querySelector('.page__content').addEventListener('scroll', function (event) {
                var scrollTopPosition = event.target.scrollTop,
                    scrollHeight = event.target.scrollHeight,
                    clientHeight = event.target.clientHeight,
                    paginateTopPosition = scrollHeight - clientHeight - 500;

                if (scrollTopPosition > paginateTopPosition) {
                    window.setTimeout(
                        function () {
                            this.postManager.fetchNextPosts();
                        }.bind(this),
                        500
                    )
                }

            }.bind(this));
        }.bind(this));
    }
}
