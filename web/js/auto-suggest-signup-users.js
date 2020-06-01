$(document).ready(function () {

    $('.session-signup-record').collection({
        prefix: 'signup-collection',
        add: '<a href="#" class="btn btn-success">Új Bejelentkező</a>',
        allow_up: false,
        allow_down: false,
        add_at_the_end: true,
        after_add: function (collection, element) {
            // $('.subscription-info-wrap').find('textarea').attr('disabled', true);
            // element.find('select').on('change', function () {
            //     var recordNumber = parseInt(this.id.replace(/[^0-9\.]/g, ''), 10);
            //     loadSubscriptionInfo(this.value, recordNumber);
            // });
            // after adding a new record the form the new input field gets the autocompleter functionality
            element.find('input').autocompleter({
                url_list: '/useraccount_search',
                url_get: '/useraccount_get/',
                // on_select_callback: function (item) {
                //     var recordNumber = parseInt(item.attr('id').replace(/[^0-9\.]/g, ''), 10);
                //     if (!isNaN(recordNumber)) {  // Only load subscriptions on session event handling
                //         loadSubscriptionRecord(item.val(), recordNumber);
                //     }
                // }
            });
        }
    });
    $('.session-waitlist-record').collection({
        prefix: 'waitlist-collection',
        add: '<a href="#" class="btn btn-warning">Új Várólistás</a>',
        allow_up: false,
        allow_down: false,
        add_at_the_end: true,
        after_add: function (collection, element) {
            // $('.subscription-info-wrap').find('textarea').attr('disabled', true);
            // element.find('select').on('change', function () {
            //     var recordNumber = parseInt(this.id.replace(/[^0-9\.]/g, ''), 10);
            //     loadSubscriptionInfo(this.value, recordNumber);
            // });
            // after adding a new record the form the new input field gets the autocompleter functionality
            element.find('input').autocompleter({
                url_list: '/useraccount_search',
                url_get: '/useraccount_get/',
                // on_select_callback: function (item) {
                //     var recordNumber = parseInt(item.attr('id').replace(/[^0-9\.]/g, ''), 10);
                //     if (!isNaN(recordNumber)) {  // Only load subscriptions on session event handling
                //         loadSubscriptionRecord(item.val(), recordNumber);
                //     }
                // }
            });
        }
    });

    // autocomplete search
    // this is for the input fields that are already on the form when the document loads
    $('.attendee-input-wrap input').autocompleter({
        url_list: '/useraccount_search',
        url_get: '/useraccount_get/'
    });
    //
    // function loadSubscriptionRecord(id, recordNumber) {
    //     $.ajax({
    //         url: '/loadSubscription',
    //         data: {
    //             owner_id: id
    //         },
    //         success: function (data) {
    //             var subscriptionFiledId = "#appbundle_sessionevent_attendees_".concat(recordNumber, '_subscription');
    //
    //             $(subscriptionFiledId).find('option').not('option:first').remove();
    //
    //             $.each(data, function (key, value) {
    //
    //                 var selectedText = value['is_owned'] ? 'selected' : '';
    //
    //                 $(subscriptionFiledId)
    //                     .append($("<option " + selectedText + " ></option>")
    //                         .attr("value", value['id'])
    //                         .text(value['label']));
    //             });
    //
    //             $.each(data, function (key, value) {
    //               if (value['is_owned']) {
    //                   loadSubscriptionInfo(value['id'], recordNumber);
    //               }
    //             });
    //         }
    //     });
    // }

    // $('.subscriptions-input-wrap').find('select').attr('disabled', true);
    // $('.subscription-info-wrap').find('textarea').attr('disabled', true);

    // var form = $('.form-horizontal');
    //
    // form.submit(function () {
    //     $('.subscriptions-input-wrap').find('select').prop("disabled", false);
    //     $('.subscription-info-wrap').find('textarea').prop("disabled", false);
    // });

    var pathname = window.location.pathname; // Returns path only

    if (pathname != '/announced_session/add_announced_session') {

        $('#appbundle_announced_session_scheduleItem').attr('disabled', true);

        form.submit(function () {
            $("#appbundle_announced_session_scheduleItem").prop("disabled", false);
        });
    }
    //
    // function loadSubscriptionInfo(id, recordNumber) {
    //     $.ajax({
    //         url: '/loadSubscriptionInfo',
    //         data: {
    //             subscription_id: id
    //         },
    //         success: function (data) {
    //
    //             var subscriptionInfo = "#appbundle_sessionevent_attendees_".concat(recordNumber, '_subscription_info');
    //
    //             if (data['id'] != null) {
    //                 if (data['attendance_limit'] == null) {
    //                     $(subscriptionInfo).val("Havi bérlet (használatok száma: " + data['attendance_count'] + ")");
    //                 } else {
    //                     $(subscriptionInfo).val("Alkalmak Száma: " + data['attendance_limit']
    //                         + "\n"
    //                         + "Fennmaradó: " + data['attendance_left']);
    //                 }
    //             }
    //         }
    //     });
    // }

});


