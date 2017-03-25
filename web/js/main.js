// autocomplete search
$(document).ready(function () {
    $('.attendees-input-wrap input').autocompleter({
        url_list: '/useraccount_search',
        url_get: '/useraccount_get/'
    });

    $('.collection-rescue-add').click(function () {
            setTimeout(function () {
                $('.attendees-input-wrap input').last().autocompleter({
                    url_list: '/useraccount_search',
                    url_get: '/useraccount_get/'
                });
                console.log("assigned");
            }, 300)
        }
    );
});


