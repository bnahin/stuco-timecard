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
      var today = new Date(),
          h     = checkTime(today.getHours()),
          m     = checkTime(today.getMinutes()),
          s     = checkTime(today.getSeconds())
      $('#current-time').html(h + ':' + m + ':' + s)
      t = setTimeout(function () {
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
  btn.button('disabled')
  btn.html('<i class="fas fa-spinner fa-pulse"></i>')
}

function activityBtnEnable (btn) {
  btn.button('enabled')
  btn.html('<i class="fas fa-sign-out-alt"></i> Clock Out')
}

$('#new-activity-submit').click(function (e) {
  let btn  = $(this),
      form = $('#new-activity')
  activityBtnDisable(btn)

  e.preventDefault()
  $.post(form.attr('action'),
    {
      id      : $('#student-id').val(),
      event   : $('#event-name').val(),
      comments: $('#comments').html()
    }, function (result) {
      activityBtnEnable(btn)
      console.log(result)
    })
})
