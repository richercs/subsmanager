$(document).ready(function () {

    let loadSigneeInfo = function() {

        let selectedAnnouncedSession = $('#appbundle_sessionevent_announcedSession').val();

        console.log(selectedAnnouncedSession);

        if(!selectedAnnouncedSession) {
            $('#signee-table-wrapper').css("display", "none");
        } else {
            // if there is a selected value in the announcedSession field
            // the ajax request is sent to receive signees
            $.ajax({
                url: '/loadAnnouncedSessionInfo',
                data: {
                    announced_session_id: selectedAnnouncedSession
                },
                success: function (data) {
                    $('#signee-table-wrapper').css("display", "block");


                    // var subscriptionInfo = "#appbundle_sessionevent_attendees_".concat(recordNumber, '_subscription_info');
                    //
                    // if (data['id'] != null) {
                    //     if (data['attendance_limit'] == null) {
                    //         $(subscriptionInfo).val("Havi bérlet (használatok száma: " + data['attendance_count'] + ")");
                    //     } else {
                    //         $(subscriptionInfo).val("Alkalmak Száma: " + data['attendance_limit']
                    //             + "\n"
                    //             + "Fennmaradó: " + data['attendance_left']);
                    //     }
                    // }
                }
            });
        }
    };

    loadSigneeInfo();

    let announcedSessionSelect = $('#appbundle_sessionevent_announcedSession');

    announcedSessionSelect.on('change', loadSigneeInfo);
});


