
/**
 * Send some data event from iframe to parent window
 */
(function($) {$(document).ready(function() {

    // Verify if params exist
    if($_GET('sendToParent') !== '') {

        // Send event information to parent
        parent.postMessage(JSON.parse($_GET('sendToParent')), '*');

    }

    /**
     * Get query parameter
     * @param name
     * @param url
     * @returns {*}
     */
    function $_GET(name, url) {
        if (!url) {
            url = window.location.href;
        }
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

});})(jQuery);
