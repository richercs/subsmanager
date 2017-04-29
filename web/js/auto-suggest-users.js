$(document).ready(function () {
  
  $('.attendance-record').collection({
    add: '<a href="#" class="btn btn-success">Új Résztvevő</a>',
    allow_up: false,
    allow_down: false,
    add_at_the_end: true,
    after_add: function(collection, element) {
      element.find('input').autocompleter({
        url_list: '/useraccount_search',
        url_get: '/useraccount_get/',
        on_select_callback: function (item) {
          var recordNumber = parseInt(item.attr('id').replace(/[^0-9\.]/g, ''), 10);
          if (!isNaN(recordNumber)) {  // Only load subscriptions on session event handling
            loadSubscriptionRecord(item.val(), recordNumber)
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
  
        $(subscriptionFiledId).find('option').remove();
        
        $.each(data, function(key, value) {
          
          var selectedText = value['is_owned'] ? 'selected' : '';
          
          $(subscriptionFiledId)
            .append($("<option " + selectedText + " ></option>")
            .attr("value", value['id'])
            .text(value['label']));
        });

        $('#subscription-info')
      }
    });
  }
  
  $('.subscriptions-input-wrap').find('select').attr('disabled', true);

  var form = $('.form-horizontal');

  form.submit(function() {
      $('.subscriptions-input-wrap').find('select').prop("disabled", false);
  });

  var pathname = window.location.pathname; // Returns path only

  if (pathname != '/sessionevent/add_session_event') {

    $('#appbundle_sessionevent_scheduleItem').attr('disabled', true);

    form.submit(function() {
        $("#appbundle_sessionevent_scheduleItem").prop("disabled", false);
    });
  }

});


