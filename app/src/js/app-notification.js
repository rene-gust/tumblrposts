import AppNotificationHistory from './app-notification-history';

export default class AppNotification {

    constructor(registration) {
        this.serviceWorkerRegistration = registration;
        Notification.requestPermission(function(status) {
            console.log('Notification permission status:', status);
        });
        this.notificationHistrory = new AppNotificationHistory();
    }

    start () {
        var options = {
            body: 'Here is a notification body!',
            icon: 'images/ChihuahuaMomentsLogo.192.png',
            vibrate: [100, 50, 100],
            data: {
                dateOfArrival: Date.now(),
                primaryKey: 1
            }
        };

        /*window.setInterval(
            function () {
                if (this.isInAccaptableHours() && this.lastMessageLongEnough()) {
                    this.serviceWorkerRegistration.showNotification('Hello world!', options);
                }
            }.bind(this),
            1000
        )*/
    }

    isInAccaptableHours() {
        var date = new Date();
        return date.getHours() >= 9 && date.getHours() <= 23;
    }

    lastMessageLongEnough() {
        var interval = (new Date()).getTime() - this.notificationHistrory.getLastNotificationTime();

        return interval > 5 * 3000;
    }
}
