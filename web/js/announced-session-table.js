$(document).ready(function () {

    let loadSigneeInfo = function() {

        let selectedAnnouncedSession = $('#appbundle_sessionevent_announcedSession').val();

        if(selectedAnnouncedSession) {
            // if there is a selected value in the announcedSession field
            // the ajax request is sent to receive signees
            $.ajax({
                url: '/loadAnnouncedSessionInfo',
                data: {
                    announced_session_id: selectedAnnouncedSession
                },
                success: function (data) {
                    $('#signee-table-wrapper').css("display", "block");

                    if(data['signees'] !== 'undefined' && data['signees'].length <= 0) {
                        $("#signee-table").find('tbody')
                            .text("");
                    }

                    $.each(data['signees'], function (key, value) {

                        let waitListedText = 'Nem';

                        if (value['is_wait_listed'] === true) {
                            waitListedText = 'Igen'
                        }

                        $("#signee-table").find('tbody')
                            .append($('<tr>')
                                .append($('<td>')
                                    .text(value['announced_session_id'])
                                )
                                .append($('<td>')
                                    .text(value['signee_name'])
                                )
                                .append($('<td>')
                                    .text(value['extras'])
                                )
                                .append($('<td>')
                                    .text(waitListedText)
                                )
                            );
                    });
                }
            });
        } else {
            $('#signee-table-wrapper').css("display", "none");

            $("#signee-table").find('tbody')
                .text("");
        }
    };

    loadSigneeInfo();

    let announcedSessionSelect = $('#appbundle_sessionevent_announcedSession');

    announcedSessionSelect.on('change', loadSigneeInfo);
});


