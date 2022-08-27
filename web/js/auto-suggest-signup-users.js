$(document).ready(function () {

    $('.session-signup-record').collection({
        prefix: 'signup-collection',
        add: '<a href="#" class="btn btn-success">Új Bejelentkező</a>',
        allow_up: false,
        allow_down: false,
        add_at_the_end: true,
        after_add: function (collection, element) {
            // after adding a new record the form the new input field gets the autocompleter functionality
            element.find('input[type=text]').autocompleter({
                url_list: '/useraccount_search',
                url_get: '/useraccount_get/',
            });
        }
    });

    // autocomplete search
    // this is for the input fields that are already on the form when the document loads
    $('.attendee-input-wrap input').autocompleter({
        url_list: '/useraccount_search',
        url_get: '/useraccount_get/'
    });

    var form = $('.form-horizontal');

    var pathname = window.location.pathname; // Returns path only

	const announced_session_paths = [
		'/announced_session/add_announced_session',
		'/announced_session/add_weekly_online_announced_session'
	]

    if (!announced_session_paths.includes(window.location.pathname)) {

        $('#appbundle_announced_session_scheduleItem').attr('disabled', true);

        form.submit(function () {
            $("#appbundle_announced_session_scheduleItem").prop("disabled", false);
        });
    }
});


