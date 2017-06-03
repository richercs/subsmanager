
$(document).ready(function () {

    $('#appbundle_useraccount_source_user_account_id').change(function () {

        var selectedUserId = $('#appbundle_useraccount_source_user_account_id').val();

        var userContactId = $('#userContactId').html();

        $.ajax({
            type: 'GET',
            url: '/fill_out_selected_user', // This is the url that will be requested

            // This is an object of values that will be passed as GET variables and
            // available inside changeStatus.php as $_GET['selectFieldValue'] etc...
            data: {
                selectFieldValue: selectedUserId,
                userContactId: userContactId
            },

            // This is what to do once a successful request has been completed - if
            // you want to do nothing then simply don't include it. But I suggest you
            // add something so that your use knows the db has been updated
            success: function(data) {
                var parsedData = JSON.parse(data);
                $('#appbundle_useraccount_first_name').val(parsedData['contact']['firstname']);
                $('#appbundle_useraccount_last_name').val(parsedData['contact']['lastname']);
                $('#appbundle_useraccount_email').val(parsedData['contact']['email']);
                $('#appbundle_useraccount_username').val(parsedData['user']['username']);
            },
            dataType: 'html'
        });

    });
});


