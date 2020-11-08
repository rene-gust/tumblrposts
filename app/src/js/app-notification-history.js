export default class AppNotificationHistory {
    constructor() {
        if ('localStorage' in window) {
            this.hasLocalStorage = true;
        } else {
            this.hasLocalStorage = false;
        }
        this.lastNotificationTime = null;
    }

    setNotificationTime() {
        var now = new Date();

        if (this.hasLocalStorage) {
            window.localStorage.setItem('lastNotificationTime', now.getTime());
        }

        this.lastNotificationTime = now.getTime()
    }

    getLastNotificationTime() {
        if (this.hasLocalStorage) {
            return window.localStorage.getItem('lastNotificationTime');
        }

        return this.lastNotificationTime
    }
}
