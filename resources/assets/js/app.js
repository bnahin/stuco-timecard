require('./bootstrap')
/* * * * * * * * * * * * * * * *
*                              *
*     StuCo App JS             *
*     @author Blake Nahin      *
*                              *
* * * * * * * * * * * * * * * * */

/**
 * Current Time Display (New Activity)
 */
if ($('#current-time').length) {
  (function () {
    function checkTime (i) {
      return (i < 10) ? '0' + i : i
    }

    function startTime () {
      let today = new Date(),
          h     = checkTime(today.getHours()),
          m     = checkTime(today.getMinutes()),
          s     = checkTime(today.getSeconds())
      $('#current-time').html(h + ':' + m + ':' + s)
      setTimeout(function () {
        startTime()
      }, 500)
    }

    startTime()
  })()
}

/**
 * New Activity Submission
 */
function activityBtnDisable (btn) {
  btn.attr('disabled', true)
  btn.html('<i class="fas fa-spinner fa-pulse"></i>')
}

function activityBtnEnable (btn, glyph, text, reset = true) {
  if (reset) btn.attr('disabled', false)
  btn.html('<i class="fas fa-' + glyph + '"></i> ' + text)
}

$('#new-activity-submit').click(function (e) {
  e.preventDefault()
  let btn  = $(this),
      form = $('#new-activity')
  activityBtnDisable(btn)

  $.post(form.attr('action'),
    {
      id      : $('#student-id').val(),
      event   : $('#event-name').val(),
      comments: $('#comments').html()
    })
    .done(function () {
      activityBtnEnable(btn, 'check', 'Success', false)
      return swal({
        title  : 'Success!',
        text   : 'The time punch was successful.',
        icon   : 'success',
        timer  : 4000,
        buttons: false
        //TODO make this self-destruct and redirect
      }).then(() => {
        location.reload()
      })
    })
    .fail(function (xhr, status, error) {
      activityBtnEnable(btn, 'sign-out-alt', 'Clock Out')
      //Validation error
      return swal('Error!', 'There was a problem clocking out. ' + xhr.responseJSON.errors.id[0], 'error')
    })
})

/**
 * Clock In Submission
 *
 */

//Remove Timepunch
$('#clock-remove').click(function () {
  let btn    = $(this),
      action = btn.data('action')

  activityBtnDisable(btn)
  $.ajax({
    url    : action,
    type   : 'DELETE',
    success: function () {
      activityBtnEnable(btn, 'check', 'Success', false)
      //Success
      return swal({
        title  : 'Success!',
        text   : 'The time punch has been removed.',
        icon   : 'success',
        timer  : 4000,
        buttons: false
        //TODO make this self-destruct and redirect
      }).then(() => {
        location.reload()
      })
    },
    error  : function (xhr) {
      activityBtnEnable(btn, 'sign-in-alt', 'Clock In')

      return swal('Error!', 'There was a problem removing the time punch. ' + xhr.responseJSON.message, 'error')
    }
  })
})