$(document).ready(function () {
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let refreshPromise = null;

    function refreshCSRFToken() {
        if (refreshPromise) return refreshPromise;

        refreshPromise = $.ajax({
            url: '/refresh-csrf',
            type: 'GET',
            dataType: 'json'
        }).then(function (data) {
            csrfToken = data.csrfToken;
            $('meta[name="csrf-token"]').attr('content', csrfToken);
            $('input[name="_token"]').val(csrfToken);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            refreshPromise = null;
            return csrfToken;
        }).fail(function (xhr, status, error) {
            console.error('Error refreshing CSRF token:', error);
            refreshPromise = null;
            throw error;
        });

        return refreshPromise;
    }

    function retryWithFreshToken(originalSettings) {
        return refreshCSRFToken().then(function (token) {
            let newSettings = $.extend(true, {}, originalSettings);
            newSettings.headers = newSettings.headers || {};
            newSettings.headers['X-CSRF-TOKEN'] = token;

            if (newSettings.data instanceof FormData) {
                newSettings.data.set('_token', token);
            } else if (typeof newSettings.data === 'string') {
                newSettings.data = newSettings.data.replace(/_token=[^&]*/, '_token=' + token);
            } else if (typeof newSettings.data === 'object') {
                newSettings.data._token = token;
            }

            return $.ajax(newSettings);
        });
    }

    $.ajaxSetup({
        beforeSend: function (xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        }
    });

    $(document).ajaxError(function (event, jqXHR, settings, thrownError) {
        if (jqXHR.status === 419) {
            console.log('CSRF token expired. Retrying with a new token...');
            event.preventDefault();
            retryWithFreshToken(settings)
                .then(function (response) {
                    console.log('Request retried successfully');
                    if (settings.success) settings.success(response);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.error('Error retrying request:', errorThrown);
                    if (settings.error) settings.error(jqXHR, textStatus, errorThrown);
                });
        }
    });

    // Refresh token periodically (every 30 minutes)
    setInterval(refreshCSRFToken,  60 * 1000);

    // Initial token refresh
    refreshCSRFToken();
});