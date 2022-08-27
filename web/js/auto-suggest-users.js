$(document).ready(function () {

    $('.attendance-record').collection({
        add: '<a href="#" class="btn btn-success">Új Résztvevő</a>',
        allow_up: false,
        allow_down: false,
        add_at_the_end: true,
        after_add: function (collection, element) {
            $('.subscription-info-wrap').find('textarea').attr('disabled', true);
            element.find('select').on('change', function () {
                var recordNumber = parseInt(this.id.replace(/[^0-9\.]/g, ''), 10);
                loadSubscriptionInfo(this.value, recordNumber);
            });
            element.find('input').autocompleter({
                url_list: '/useraccount_search',
                url_get: '/useraccount_get/',
                on_select_callback: function (item) {
                    var recordNumber = parseInt(item.attr('id').replace(/[^0-9\.]/g, ''), 10);
                    if (!isNaN(recordNumber)) {  // Only load subscriptions on session event handling
                        loadSubscriptionRecord(item.val(), recordNumber);
                    }
                }
            });
        }
    });

    // autocomplete search
    $('.attendee-input-wrap input').autocompleter({
        url_list: '/useraccount_search',
        url_get: '/useraccount_get/'
    });

    function loadSubscriptionRecord(id, recordNumber) {
        $.ajax({
            url: '/loadSubscription',
            data: {
                owner_id: id
            },
            success: function (data) {
                var subscriptionFiledId = "#appbundle_sessionevent_attendees_".concat(recordNumber, '_subscription');

                $(subscriptionFiledId).find('option').not('option:first').remove();

                $.each(data, function (key, value) {

                    var selectedText = value['is_owned'] ? 'selected' : '';

                    $(subscriptionFiledId)
                        .append($("<option " + selectedText + " ></option>")
                            .attr("value", value['id'])
                            .text(value['label']));
                });

                $.each(data, function (key, value) {
                  if (value['is_owned']) {
                      loadSubscriptionInfo(value['id'], recordNumber);
                  }
                });
            }
        });
    }

    $('.subscriptions-input-wrap').find('select').attr('disabled', true);
    $('.subscription-info-wrap').find('textarea').attr('disabled', true);

    var form = $('.form-horizontal');

    form.submit(function () {
        $('.subscriptions-input-wrap').find('select').prop("disabled", false);
        $('.subscription-info-wrap').find('textarea').prop("disabled", false);
    });

    var pathname = window.location.pathname; // Returns path only

    if (pathname != '/sessionevent/add_session_event') {

        $('#appbundle_sessionevent_scheduleItem').attr('disabled', true);

        form.submit(function () {
            $("#appbundle_sessionevent_scheduleItem").prop("disabled", false);
        });
    }

    function loadSubscriptionInfo(id, recordNumber) {
        $.ajax({
            url: '/loadSubscriptionInfo',
            data: {
                subscription_id: id
            },
            success: function (data) {

                var subscriptionInfo = "#appbundle_sessionevent_attendees_".concat(recordNumber, '_subscription_info');

                if (data['id'] != null) {
					if (data['subscription_type'] === 'credit') {
						$(subscriptionInfo).val(
							"Kreditek Száma: " + data['subscription_credit']
							+ "\n"
							+ "Fennmaradó: " + data['subscription_credit_left']
						);
						var creditRequirement = $('#appbundle_sessionevent_sessionCreditRequirement');
						if (!isNaN(creditRequirement.val()) && creditRequirement.val() > data['subscription_credit_left']) {
							$(subscriptionInfo).css("background-color", "red");
						} else {
							$(subscriptionInfo).css("background-color", "");
						}
					}
					if (data['subscription_type'] === 'attendance') {
						$(subscriptionInfo).val(
							"Alkalmak Száma: " + data['attendance_limit']
							+ "\n"
							+ "Fennmaradó: " + data['attendance_left']
						);

					}
                }
            }
        });
    }

});


