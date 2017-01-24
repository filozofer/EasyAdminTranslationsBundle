/**
 * Change the "referer" params in the query when page is and new or edit form for a Translation entity loaded in an iframe
 */
(function($) {$(document).ready(function() {

    // Get informations about actual page & request
    var weAreInIframe = window.self !== window.top;
    var weAreInEditPage = ($_GET('action') == 'edit' && $_GET('entity') != ''); // Far from perfect !

    // Verify if we are in iframe on a new or edit page
    if(weAreInIframe && weAreInEditPage) {

        // Build edit url
        var actionUrl = (window.location.href.indexOf('&referer=') !== -1 || window.location.href.indexOf('?referer=') !== -1) ? window.location.href + '&updated_referer' : window.location.href + '&referer=REPLACETHIS&updated_referer';
        actionUrl = actionUrl.replace(new RegExp('(.*referer=)(.*)(&.*)'), '$1' + encodeURIComponent(window.location.href) + '$3');

        // Update edit form action attribute to redirect to edit after form submit
        $('form[name=' + $_GET('entity').toLowerCase() + '][data-view=' + $_GET('action') + '][data-entity]').attr('action', actionUrl);

        // Build delete url
        var actualDeleteUrl = $('#form-actions-row .action-delete').attr('href');
        var newRefererUrl = $('a[template-name=delete-link].' + window.name , window.parent.document).attr('href');
        actualDeleteUrl = (actualDeleteUrl.indexOf('&referer=') !== -1 || actualDeleteUrl.indexOf('?referer=') !== -1) ? actualDeleteUrl : actualDeleteUrl + '&referer=REPLACETHIS&updated_referer';
        newDeleteUrl = actualDeleteUrl.replace(new RegExp('(.*referer=)([^&]+)(.*)'), '$1' + encodeURIComponent(newRefererUrl) + '$3');

        // Update delete form action attribute to redirect to close tab page after submit
        $('#form-actions-row .action-delete').attr('href', newDeleteUrl);
        $('form[name=delete_form]').attr('action', newDeleteUrl);

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