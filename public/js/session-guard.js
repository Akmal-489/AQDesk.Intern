(function () {
    const TIMEOUT_MINUTES = 15;
    let timer;

    function logoutDueToInactivity() {
        localStorage.removeItem('aqdesk_token');
        alert('You were logged out due to inactivity. Please log in again.');
        window.location.href = 'Login';
    }

    function resetTimer() {
        clearTimeout(timer);
        timer = setTimeout(logoutDueToInactivity, TIMEOUT_MINUTES * 60 * 1000);
    }

    ['mousemove', 'keydown', 'click', 'scroll'].forEach(function (evt) {
        document.addEventListener(evt, resetTimer);
    });

    resetTimer();
})();