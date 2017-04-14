
$(document).ready(function () {

    // autocomplete search
    $('.attendee-input-wrap input').autocompleter({
        url_list: '/useraccount_search',
        url_get: '/useraccount_get/',
        on_select_callback: function (item) {
            var recordNumber = parseInt(item.attr('id').replace(/[^0-9\.]/g, ''), 10);
            if(!isNaN(recordNumber)) {  // Only load subscriptions on session event handling
                loadSubscriptionRecord(item.val(),recordNumber)
            }
        }
    });

    $('.collection-rescue-add').click(function () {
            setTimeout(function () {
                $('.attendee-input-wrap input').last().autocompleter({
                    url_list: '/useraccount_search',
                    url_get: '/useraccount_get/',
                    on_select_callback: function (item) {
                        var recordNumber = parseInt(item.attr('id').replace(/[^0-9\.]/g, ''), 10);
                        if(!isNaN(recordNumber)) { // Only load subscriptions on session event handling
                            loadSubscriptionRecord(item.val(), recordNumber)
                        }
                    }
                });
                console.log("assigned");
            }, 300)
        }
    );


    function loadSubscriptionRecord (id, recordNumber) {
        $.ajax({
            url: '/load_subscription_record',
            data: {
                owner_id : id
            },
            success: function (data) {
                var subscriptionFiledId ="appbundle_sessionevent_attendees_".concat(recordNumber,'_subscription');
                if(data['label']) {
                    console.log("ok label " + subscriptionFiledId);
                    console.log("ok label " + data['id']);
                    $('#' + subscriptionFiledId).val(parseInt(data['id']));
                    document.getElementById(subscriptionFiledId).style.backgroundColor = "lightblue";
                } else {
                    console.log(recordNumber);
                    document.getElementById(subscriptionFiledId).style.backgroundColor = "#f7a7a7";
                }
                console.log(data);
            }
        });
    }
});


