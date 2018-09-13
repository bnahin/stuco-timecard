require('./bootstrap')

/* * * * * * * * * * * * * * * *
*                              *
*     StuCo App JS             *
*     @author Blake Nahin      *
*                              *
* * * * * * * * * * * * * * * * */

/** Functions */
let Config = {
  baseURL: ($('#is-dev').length) ? 'http://clubs.ecrchs.test/' : 'https://clubs.ecrchs.net/',
  isDev  : true
}
let Helpers = {
  datetime: {
    formatDate (date = null) {
      let d = (date) ? new Date(date) : new Date()
      let hh = d.getHours()
      let m = d.getMinutes()
      let s = d.getSeconds()
      let dd = 'AM'
      let h = hh
      if (h >= 12) {
        h = hh - 12
        dd = 'PM'
      }
      if (h == 0) {
        h = 12
      }
      m = m < 10 ? '0' + m : m

      s = s < 10 ? '0' + s : s

      /* 2 digit hours:
      h = h<10?"0"+h:h; */

      let pattern = new RegExp('0?' + hh + ':' + m + ':' + s)

      let replacement = h + ':' + m
      replacement += ':' + s
      replacement += ' ' + dd

      return replacement
    }
  },
  buttons : {
    activityBtnDisable (btn) {
      btn.attr('disabled', true)
      btn.html('<i class="fas fa-spinner fa-pulse"></i>')
    },
    activityBtnEnable (btn, glyph, text = '', reset = true) {
      if (reset) btn.attr('disabled', false)
      btn.html('<i class="fas fa-' + glyph + '"></i> ' + text)
    }
  },
  updateAdminMarkedCount () {
    $('.marked-badge').each(function () {
      let $this = $(this),
          val   = parseInt($this.text())
      if (val == 1) {
        $this.remove()
      }
      else {
        $this.text(val - 1)
      }
    })
  },
  hideAllTooltips () {
    $('[rel="tooltip"], .tooltip').tooltip('hide')
  }
}
let Request = {
  send (url, type, data, success, error) {
    $.ajax({
        url    : Config.baseURL + url,
        type   : type,
        data   : data,
        success: success,
        error  : error
      }
    )
  }
}

let timeCounter
let elapsedTimer
if ($('#current-time').length) {
  $('#student-id').focus();
  (function () {
    function checkTime (i) {
      return (i < 10) ? '0' + i : i
    }

    function startTime () {
      $('#current-time').html(Helpers.datetime.formatDate())
      timeCounter = setTimeout(function () {
        startTime()
      }, 500)
    }

    startTime()
  })()
}

/*New Activity Submission */
function activityBtnDisable (btn) {
  Helpers.buttons.activityBtnDisable(btn)
}

function activityBtnEnable (btn, glyph, text = '', reset = true) {
  Helpers.buttons.activityBtnEnable(btn, glyph, text, reset)
}

$('#new-activity-submit').on('click', function (e, submit = false) {
  e.preventDefault()
  let btn  = $(this),
      form = $('#new-activity'),
      box  = $('#student-info')
  activityBtnDisable(btn)

  $.post(form.attr('action'),
    {
      id      : $('#student-id').val(),
      event   : $('#event-name').val(),
      comments: $('#comments').val()
    })
    .done(function () {
      activityBtnEnable(btn, 'check', 'Success', false)
      return swal({
        title: 'Success!',
        text : 'The time punch was successful.',
        icon : 'success'
      }).then(() => {
        if (!submit) {
          location.reload()
        }
        else {
          box.hide()
          $('#student-id').val('')
          //Panel
          $('#clock-out-card').attr('id', 'new-activity-card')
          $('#comments').attr('disabled', false) //TODO keep??
          $('#clock-in-title').text('Add New Activity')

          //Button
          Helpers.buttons.activityBtnEnable($('#new-activity-submit').show(), 'sign-in-alt', 'Clock In')
          $('#clock-out-submit').hide().attr('data-id', 0)

          //Current Time
          clearInterval(elapsedTimer)
          $('#elapsed-time-p').hide()
          $('#current-time-p').show()
        }
      })
    })
    .fail(function (xhr, status, error) {
      activityBtnEnable(btn, 'sign-in-alt', 'Clock In')
      //Validation error
      return swal('Error!', 'There was a problem clocking in. ' + xhr.responseJSON.errors.id[0], 'error')
    })
})
$('#clock-out-submit').on('click', function (e, short = false) {
  /** Admin Clock Out **/

  e.preventDefault()
  let btn  = $(this),
      id   = btn.data('id'),
      box  = $('#student-info'),
      form = $('#new-activity')
  Helpers.buttons.activityBtnDisable(btn)
  Request.send('hours/clockout/' + id, 'POST', {}, (success) => {
    Helpers.buttons.activityBtnEnable(btn, 'sign-out-alt', 'Clock Out')
    return swal({
      title: 'Success!',
      text : 'The student has been clocked out.',
      icon : 'success',
      timer: (short) ? 1000 : 4000
    }).then(() => {
      box.hide()
      $('#student-id').val('')
      //Panel
      $('#clock-out-card').attr('id', 'new-activity-card')
      $('#comments').attr('disabled', false) //TODO keep??
      $('#clock-in-title').text('Add New Activity')

      //Button
      $('#new-activity-submit').show()
      $('#clock-out-submit').hide().attr('data-id', 0)

      //Current Time
      clearInterval(elapsedTimer)
      $('#elapsed-time-p').hide()
      $('#current-time-p').show()
    })
  }, (error) => {
    activityBtnEnable(btn, 'sign-out-alt', 'Clock Out')
    console.log(error.responseJSON)
    return swal('Error!', 'Unable to clock out. :(', 'error')

  })
})
$('input#student-id').on('blur', function (e, submit = false) {
  let input = $(this),
      val   = input.val(),
      box   = $('#student-info').hide()
  if (!val.length) return false

  let loading = $('#loading-student').show()
  input.attr('disabled', true)

  Request.send('user/ajax/getInfo', 'POST', {id: val}, (request) => {
      let name        = request.user.name,
          grade       = request.user.grade,
          isClockedIn = request.hasOwnProperty('currentHour'),
          hourData    = (isClockedIn) ? request.currentHour : null
      $('#student-info-name').text(name)
      $('#student-info-grade').text(grade)
      box.show()

      if (!isClockedIn) {
        //Panel
        $('#clock-out-card').attr('id', 'new-activity-card')
        $('#comments').attr('disabled', false) //TODO keep??
        $('#clock-in-title').text('Add New Activity')

        //Button
        $('#new-activity-submit').show()
        $('#clock-out-submit').hide().attr('data-id', 0)
        $('#new-activity-submit').attr('data-action')

        //Current Time
        clearInterval(elapsedTimer)
        $('#elapsed-time-p').hide()
        $('#current-time-p').show()
      }
      else {
        //Panel
        $('#new-activity-card').attr('id', 'clock-out-card')
        $('#comments').attr('disabled', true) //TODO keep??
        $('#clock-in-title').text('Clock Out')

        //Button
        $('#new-activity-submit').hide()
        $('#clock-out-submit').show().attr('data-id', hourData.id)

        //Elapsed Time
        $('#current-time-p').hide()
        let p = $('#elapsed-time-p').show()
        window.start_time = hourData.start_time
        elapsedTimer = setInterval(function () {
          let start = new Date(window.start_time),
              diff  = (new Date() - start) * 0.001,
              h     = parseInt(Math.floor(((diff % 31536000) % 86400) / 3600), 10),
              m     = parseInt(Math.floor((((diff % 31536000) % 86400) % 3600) / 60), 10),
              s     = parseInt((((diff % 31536000) % 86400) % 3600) % 60, 10)
          p.find('#hours').text((h < 10) ? '0' + h : h)
          p.find('#minutes').text((m < 10) ? '0' + m : m)
          p.find('#seconds').text((s < 10) ? '0' + s : s)

        }, 500)
      }

      input.attr('disabled', false)
      loading.hide()

      if (submit) {
        if (!isClockedIn) {
          //Clock in!
          $('#new-activity-submit').trigger('click', [true, hourData])
        }
        else {
          //Clock out!
          $('#clock-out-submit').trigger('click', [true])
        }
      }

    }, (xhr) => {
      input.attr('disabled', false)
      loading.hide()
      swal('Error!', 'Could not locate student. ' + xhr.responseJSON.errors.id[0], 'error')
    }
  )
})

$('#student-id').keydown(function (e) {
  if (e.keyCode == 13) {
    e.preventDefault()

    if (($('#clock-out-card').length || $('#new-activity-card').length) && $('#clock-out-submit').length && $('#new-activity-submit').length) {
      /** is admin **/
      $('#student-id').triggerHandler('blur', [true])
    }

    return false
  }
})

/** Clock Out **/
if ($('#clock-out-table').length) {
  elapsedTimer = setInterval(function () {
    let start = new Date(window.start_time),
        diff  = (new Date() - start) * 0.001,
        h     = parseInt(Math.floor(((diff % 31536000) % 86400) / 3600), 10),
        m     = parseInt(Math.floor((((diff % 31536000) % 86400) % 3600) / 60), 10),
        s     = parseInt((((diff % 31536000) % 86400) % 3600) % 60, 10)

    if (h) {
      $('#hours').show()
      $('#ehours').text(h)
    }
    else {
      $('#hours').hide()
    }
    if (m) {
      $('#minutes').show()
      $('#eminutes').text(m)
    }
    else {
      $('#minutes').hide()
    }
    $('#esecs').text(s)

  }, 500)
}
$('.clock-out').click(function (e) {
  e.preventDefault()
  let mainBtn  = $('#co-main'),
      dropBtn  = $('#co-addon'),
      returnTo = $(this).attr('data-return'),
      action   = $('#clock-out-form').attr('action'),
      id       = $('#hour-id').val()

  if ($(this).hasClass('mark-review'))
    action += '/mark'

  //Disable Buttons
  mainBtn.attr('disabled', true)
  mainBtn.html('<i class="fas fa-spinner fa-pulse"></i>')
  dropBtn.attr('disabled', true)

  //Send Request
  $.post(action, {
    comments: $('#comments').val()
  })
    .done(function (r) {
      mainBtn.html('<i class="fas fa-check"></i> Success')

      return swal({
        title: 'Success!',
        text : 'You have clocked out.',
        icon : 'success',
        timer: 4000
      }).then(() => {
        window.location = returnTo
      })

    })
    .fail(function (xhr) {
      mainBtn.attr('disabled', false)
      mainBtn.html('<i class="fas fa-sign-in-alt"></i> Clock Out')
      dropBtn.attr('disabled', false)
      swal('Error!', 'There was an error processing the time punch.', 'error')
      console.log(xhr.responseJSON)
    })

})

/** Remove Timepunch **/
$('#clock-remove').click(function (e) {
  e.preventDefault()

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
        title: 'Success!',
        text : 'The time punch has been removed.',
        icon : 'success',
        timer: 4000
      }).then(() => {
        location.reload()
      })
    },
    error  : function (xhr) {
      activityBtnEnable(btn, 'times', 'Remove Time Punch')

      return swal('Error!', 'There was a problem removing the time punch. ' + xhr.responseJSON.message, 'error')
    }
  })
})

/** Hours Page */
if ($('#hours-table').length && !$('#no-hours').length) {
  /** Data Table */
  $(document).ready(function () {
    let exportData = {
      clubName : $('#export-clubname').val(),
      header   : $('#export-header').val(),
      fullName : $('#export-name').val(),
      studentId: $('#export-stuid').val(),
      grade    : $('#export-grade').val()
    }
    $('#hours-table').DataTable({
      'order'     : [[0, 'desc']],
      'columnDefs': [
        {
          'targets'   : [0],
          'visible'   : false,
          'searchable': false
        }
      ],
      'buttons'   : [
        {
          extend       : 'pdf',
          messageTop   : 'Name: ' + exportData.fullName + '\nGrade: ' + exportData.grade + '\nStudent ID: ' + exportData.studentId,
          exportOptions: {
            columns: ':not(.print-hide)'
          }
        }, {
          extend       : 'excel',
          messageTop   : 'Name: ' + exportData.fullName + ' | Student ID: ' + exportData.studentId,
          filename     : exportData.fullName + ' - Time Punches',
          sheetName    : exportData.fullName + ' - Time Punches',
          exportOptions: {
            columns: ':not(.print-hide)'
          }
        },
        {
          extend       : 'print',
          messageTop   : 'Name: ' + exportData.fullName + ' | Grade: ' + exportData.grade + ' | Student ID: ' + exportData.studentId,
          exportOptions: {
            columns: ':not(.print-hide)'
          }
        }
      ],
      dom         :
        '<\'row\'<\'col-sm-3\'l><\'col-sm-6 text-center\'B><\'col-sm-3\'f>>' +
        '<\'row\'<\'col-sm-12\'tr>>' +
        '<\'row\'<\'col-sm-5\'i><\'col-sm-7\'p>>',
    })
  })

  /** Charts */
  let user_id = $('#user_id').val()

  //Chart Data
  $.get(
    '/hours/charts/' + user_id
  ).done(function (r) {
    loadGraphs(r)
  })
    .fail(function (xhr) {
      $('canvas').remove()
      console.log('Unable to retrieve charts :(')
    })

  function loadGraphs (data) {
    console.log(data)

    function dynamicColors () {
      var r = Math.floor(Math.random() * 255)
      var g = Math.floor(Math.random() * 255)
      var b = Math.floor(Math.random() * 255)
      return 'rgba(' + r + ',' + g + ',' + b + ', 0.5)'
    }

    function poolColors (a) {
      var pool = []
      for (i = 0; i < a; i++) {
        pool.push(dynamicColors())
      }
      return pool
    }

    let lineChart = new Chart(document.getElementById('line-chart'), {
      'type'   : 'line',
      'data'   : {
        'labels'  : data.line.labels,
        'datasets': [{
          'label'      : 'Average Duration',
          'data'       : data.line.data,
          'fill'       : false,
          'borderColor': 'rgb(75, 192, 192)',
          'lineTension': 0.1
        }]
      },
      'options': {}
    })
    let pieChart = new Chart(document.getElementById('pie-chart'), {
      'type': 'doughnut',
      'data': {
        'labels'  : data.pie.labels,
        'datasets': [{
          'label'          : 'Number of Events',
          'data'           : data.pie.data,
          'backgroundColor': poolColors(data.pie.data.length)
        }]
      }
    })
    let mixedChart = new Chart(document.getElementById('mixed-chart'), {
      type   : 'bar',
      data   : {
        labels  : data.mixed.labels,
        datasets: [{
          label          : 'Total Hours',
          data           : data.mixed.totals,
          backgroundColor: 'rgba(255,99,132,0.2)',
          borderColor    : poolColors(1)[0]
        }]
      },
      /*
        {
          label      : 'Out of Classroom',
          data       : [65, 59, 80, 81, 56, 55, 40],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        },
        {
          label      : 'Event 1',
          data       : [29, 19, 40, 11, 76, 5, 30],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        },
        {
          label      : 'Event 2',
          data       : [11, 19, 20, 10, 46, 25, 10],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        },
        {
          label      : 'Event 3',
          data       : [41, 29, 12, 33, 12, 32, 12],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        }]*/
      options: {}
    })

    //Add data to mixed chart
    for (let evt in data.mixed.datasets) {
      if (data.mixed.datasets.hasOwnProperty(evt)) {
        console.log(data.mixed.datasets[evt])
        mixedChart.data.datasets.push({
          label      : evt,
          data       : data.mixed.datasets[evt],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        })
        mixedChart.update()
      }
    }
  }

  $('.mark-hour').on('click', function () {
    let btn = $(this),
        id  = btn.data('id')
    Helpers.buttons.activityBtnDisable(btn)
    Request.send('hours/mark/' + id, 'POST', {}, () => {
      Helpers.buttons.activityBtnEnable(btn, 'check', 'Success', false)
      swal('Success!', 'The timepunch has been marked for review.', 'success')
        .then(() => location.reload())
    }, (xhr) => {
      Helpers.buttons.activityBtnEnable(btn, 'flag', 'Mark for Review')
      swal('Error!', 'Unable to mark for review.', 'error')
      console.log(xhr)
    })
  })
  $('.undo-mark').click(function () {
    let btn    = $(this),
        id     = btn.data('id'),
        action = '/hours/undoMark'
    Helpers.buttons.activityBtnDisable(btn)
    return swal({
      title  : 'Are you sure?',
      text   : 'This will remove the Needs Review flag on this timepunch. The club leaders will not be notified.',
      icon   : 'warning',
      buttons: {
        cancel : 'No, cancel',
        confirm: {
          text      : 'Yes, undo mark.',
          className : 'swal-btn-danger',
          value     : true,
          closeModal: false
        }
      }
    })
      .then(result => {
        if (!result) throw null

        $.ajax({
          url    : action,
          type   : 'POST',
          data   : {id: id},
          success: (result) => {
            if (result.status == 'success') {
              Helpers.buttons.activityBtnEnable(btn, 'check', 'Success', false)
              return swal({
                title: 'Success!',
                text : 'The Needs Review flag has been removed.',
                icon : 'success'
              }).then(() => {
                location.reload()
              })
            } else {
              Helpers.buttons.activityBtnEnable(btn, 'undo', 'Undo Mark for Review')

              return swal({
                title: 'Error!',
                text : 'Could not remove Needs Review flag.',
                icon : 'error',
              })
            }
          },
          error  : (xhr) => {
            Helpers.buttons.activityBtnEnable(btn, 'undo', 'Undo Mark for Review')

            return swal('Error!', 'Could not remove Needs Review flag. ' + xhr.responseJSON.message, 'error')
          }
        })
      })
  })

  /** Admin **/
  $('#date-new').datetimepicker({
    timepicker: false,
    mask      : true,
    format    : 'm/d/Y',
  })
  $('.clockpicker-new').clockpicker({
    twelvehour: true,
  })
  $('#create-hour').click(function () {
    let btn  = $(this),
        form = $('#create-hour-form'),
        data = form.serialize()
    Helpers.buttons.activityBtnDisable(btn)
    Request.send('/hours/create', 'POST', data, result => {
      Helpers.buttons.activityBtnEnable(btn, 'check', 'Create Timepunch')
      if (result.status == 'success') {
        swal('Success!', 'The timepunch has been created.', 'success')
        form[0].reset()
        window.hourCreated = true
      }
      else {
        swal('Error!', 'Unable to create timepunch.', 'error')
      }
    }, xhr => {
      Helpers.buttons.activityBtnEnable(btn, 'check', 'Create Timepunch')
      let errs = xhr.responseJSON.errors
      for (let i in errs) {
        if (errs.hasOwnProperty(i)) {
          $('[name="' + i + '"]').addClass('is-invalid')
        }
      }
    })
  })
  $('#create-hour-modal').on('hide.bs.modal', function () {
    if (window.hourCreated !== undefined) location.reload()
  })
  $('.hour-edit').click(function () {
    let btn = $(this),
        id  = btn.data('id')

    Helpers.buttons.activityBtnDisable(btn)
    $.ajax({
      url    : '/admin/hour/getdata',
      type   : 'POST',
      data   : {id: id},
      success: (result) => {
        Helpers.buttons.activityBtnEnable(btn, 'edit', '')
        if (result.status == 'success') {
          //Process.....
          let data = result.data
          $('#input-id').val(id)
          $('#name').html(data.name)
          $('option:selected').attr('selected', false)
          $('option[value="' + data.event + '"]').attr('selected', true)
          $('#date').val(data.date).datetimepicker({
            timepicker: false,
            mask      : true,
            format    : 'm/d/Y',
          })
          $('#start-time').val(data.startTime)
          $('#end-time').val(data.endTime)
          $('.action-btn').attr('data-id', id)

          //Then, show Modal
          $('#edit-modal').modal('toggle')
          $('.clockpicker').clockpicker({
            twelvehour: true,
          })
        }
        else {
          swal('Error!', 'Unable to retrieve timepunch data. ' + result.message, 'error')
        }
      },
      error  : (xhr) => {
        activityBtnEnable(btn, 'edit', '')
        return swal('Error!', 'Unable to retrieve hour data. ' + xhr.responseJSON.errors.id[0].message)
      }
    })
  })
  $('.remove-timepunch').click(function (e) {
    let btn    = $(this),
        id     = btn.data('id'),
        action = '/hours/delete/' + btn.data('id'),
        modal  = $('#marked-modal')
    return swal({
      title  : 'Are you sure?',
      text   : 'This will remove the entire timepunch.',
      icon   : 'warning',
      buttons: {
        cancel : 'No, cancel',
        confirm: {
          text      : 'Yes, remove timepunch.',
          className : 'swal-btn-danger',
          value     : true,
          closeModal: false
        }
      }
    })
      .then(result => {
        if (!result) throw null

        $.ajax({
          url    : action,
          type   : 'DELETE',
          success: function () {
            //Success
            return swal({
              title: 'Success!',
              text : 'The time punch has been removed.',
              icon : 'success'
            }).then(() => {
              //Close Modal
              location.reload()
            })
          },
          error  : function (xhr) {
            activityBtnEnable(btn, 'times', 'Remove Timepunch')

            return swal('Error!', 'There was a problem removing the time punch. ' + xhr.responseJSON.message, 'error')
          }
        })
      })
  })
  $(document).on('blur', '.is-invalid', function () {
    $(this).removeClass('is-invalid')
  })
  $('#save-timepunch').click(function (e) {
    e.preventDefault()
    let btn    = $(this),
        modal  = $('#marked-modal'),
        id     = btn.data('id'),
        data   = $('#edit-hour-form').serialize(),
        action = '/admin/hour/update'

    activityBtnDisable(btn)
    $.ajax({
      url    : action,
      type   : 'POST',
      data   : data,
      success: (result) => {
        activityBtnEnable(btn, 'check', 'Save Changes')
        if (result.status == 'success') {
          swal('Success!', 'The changes have been saved.', 'success')
        }
        else {
          swal('Error!', 'Unable to save changes.', 'error')
        }
      },
      error  : (xhr) => {
        activityBtnEnable(btn, 'check', 'Save Changes')
        let errs = xhr.responseJSON.errors
        for (let i in errs) {
          if (errs.hasOwnProperty(i)) {
            $('[name="' + i + '"]').addClass('is-invalid')
          }
        }
      }
    })

    activityBtnDisable(btn)

  })

}

/** Admin Page */
if ($('#admin-card').length) {
  //Assigned Students
  let $assignedTable = $('#assigned-table').DataTable({
    'order': [[1, 'asc']]
  })
  //Assign Students
  $('#manual-assign').click(function (e) {
    e.preventDefault()
    let form  = $('#manual-assign-form'),
        input = $('#assign-input'),
        btn   = $(this)
    activityBtnDisable(btn)

    form.find('.input-invalid').removeClass('input-invalid')

    if (!input.val().length || input.val().length < 6) {
      input.addClass('input-invalid')
      return activityBtnEnable(btn, 'plus', 'Add')
    }

    $.ajax({
      url    : form.attr('action'),
      type   : 'POST',
      data   : {id: input.val()},
      success: function (result) {
        activityBtnEnable(btn, 'plus', 'Add')
        console.log(result)
        if (result.status != 'success') {
          return swal('Error!', 'Could not add student. ' + result.message, 'error')
        }

        let student = result.message
        $assignedTable.row.add([
          student.student_id,
          student.last_name,
          student.first_name,
          student.grade,
          student.email,
          //TODO: Add dynamic action buttons
          '<em>Refresh for Actions</em>'
        ]).draw('full-hold')

        return swal('Success!', 'The student has been added.', 'success')
      },
      error  : function (xhr) {
        activityBtnEnable(btn, 'plus', 'Add')
        return swal('Error!', 'Could not add student. ' + xhr.responseJSON.errors.id[0], 'error')
      }
    })
  })

  //Drop Students
  $('.drop-student').click(function () {
    let btn    = $(this),
        id     = btn.data('id'),
        action = '/admin/students/drop'
    return swal({
      title  : 'Are you sure?',
      text   : 'Dropping this student will block them from using the system.',
      icon   : 'warning',
      buttons: {
        cancel : 'No, cancel',
        confirm: {
          text      : 'Yes, drop student',
          className : 'swal-btn-danger',
          value     : true,
          closeModal: false
        }
      }
    })
      .then(result => {
        if (!result) throw null

        $.ajax({
          url    : action,
          type   : 'PUT',
          data   : {id: id},
          success: (result) => {
            if (result.status == 'success') {
              return swal({
                title: 'Success!',
                text : 'The student has been dropped.',
                icon : 'success',
                timer: 4000
              }).then(() => {
                location.reload()
              })
            } else {
              //Dropped already
              return swal({
                title: 'Already Dropped',
                text : 'The student has already been dropped.',
                icon : 'info'
              }).then(() => {
                location.reload()
              })
            }
          },
          error  : (xhr) => {
            return swal('Error!', 'Could not drop student. ' + xhr.responseJSON.errors.id[0])
          }
        })
      })
  })
  $('#purge-students').click(function () {
    let btn    = $(this),
        action = '/admin/students/purge'
    return swal({
      title  : 'Are you sure?',
      text   : 'Purging will remove all students from your club!!',
      icon   : 'warning',
      buttons: {
        cancel : 'No, cancel',
        confirm: {
          text      : 'Yes, purge. Goodbye children.',
          className : 'swal-btn-danger',
          value     : true,
          closeModal: false
        }
      }
    })
      .then(result => {
        if (!result) return false

        $.ajax({
          url    : action,
          type   : 'POST',
          success: (result) => {
            if (result.status == 'success') {
              return swal({
                title: 'Success!',
                text : 'The students have been purged.',
                icon : 'success',
                timer: 4000
              }).then(() => {
                location.reload()
              })
            } else {
              //Dropped already
              return swal({
                title: 'Already Purged',
                text : 'The students have already been purged.',
                icon : 'info'
              })
            }
          },
          error  : (xhr) => {
            return swal('Error!', 'Could not purge the children.')
          }
        })
      })
  })

  //Enrolled Student DB
  $('#student-db:visible').DataTable({
    processing: true,
    serverSide: true,
    ajax      : {
      url : '/admin/enrolled/get',
      type: 'POST'
    },
    columns   : [
      {data: 'student_id', name: 'student_id'},
      {data: 'first_name', name: 'first_name'},
      {data: 'last_name', name: 'last_name'},
      {data: 'grade', name: 'grade'},
      {data: 'email', name: 'email'}
    ]

  })

  /** Blocked Students **/
  $('#blocked-table').DataTable({
    'order': [[1, 'asc']]
  })
  $('button.unblock').click(function (e) {
    e.preventDefault()

    let btn    = $(this),
        id     = btn.data('id'),
        action = '/admin/unblock'
    activityBtnDisable(btn)

    $.ajax({
      url    : action,
      type   : 'PUT',
      data   : {id: id},
      success: (result) => {
        if (result.status == 'success') {
          activityBtnEnable(btn, 'check', 'Success', false)
          return swal({
            title: 'Success!',
            text : 'The student has been unblocked.',
            icon : 'success',
            timer: 4000
          }).then(() => {
            location.reload()
          })
        }
        else {
          //Unblocked already?
          return swal({
            title: 'Already Unblocked',
            text : 'The student has already been unblocked.',
            icon : 'info'
          }).then(() => {
            location.reload()
          })
        }
      },
      error  : (xhr) => {
        activityBtnEnable(btn, 'undo', 'Unblock')

        return swal('Error!', 'Could not unblock student. ' + xhr.responseJSON.errors.id[0])

      }
    })
  })

  /** Marked Hours **/
  $('#marked-table').DataTable({
    'order': [[0, 'asc']]
  })
  $('.marked-edit').click(function () {
    let btn = $(this),
        id  = btn.data('id')

    activityBtnDisable(btn)
    $.ajax({
      url    : '/admin/hour/getdata',
      type   : 'POST',
      data   : {id: id},
      success: (result) => {
        activityBtnEnable(btn, 'pencil-alt', '')
        if (result.status == 'success') {
          //Process.....
          let data = result.data
          $('#input-id').val(id)
          $('#comments').html(data.comments)
          $('#name').html(data.name)
          $('option:selected').attr('selected', false)
          $('option[value="' + data.event + '"]').attr('selected', true)
          $('#date').val(data.date).datetimepicker({
            timepicker: false,
            mask      : true,
            format    : 'm/d/Y',
          })
          $('#start-time').val(data.startTime)
          $('#end-time').val(data.endTime)
          $('.action-btn').attr('data-id', id)

          //Then, show Modal
          $('#marked-modal').modal('toggle')
          $('.clockpicker').clockpicker({
            twelvehour: true,
          })
        }
        else {
          swal('Error!', 'Unable to retrieve timepunch data. ' + result.message, 'error')
        }
      },
      error  : (xhr) => {
        activityBtnEnable(btn, 'pencil-alt', '')
        return swal('Error!', 'Unable to retrieve hour data. ' + xhr.responseJSON.errors.id[0].message)
      }
    })
  })
  $('.undo-mark').click(function () {
    let btn    = $(this),
        id     = btn.data('id'),
        action = '/admin/hour/undoMark',
        tr     = btn.closest('tr')
    return swal({
      title  : 'Are you sure?',
      text   : 'This will remove the Needs Review flag on this timepunch. The student will not be notified.',
      icon   : 'warning',
      buttons: {
        cancel : 'No, cancel',
        confirm: {
          text      : 'Yes, undo mark.',
          className : 'swal-btn-danger',
          value     : true,
          closeModal: false
        }
      }
    })
      .then(result => {
        if (!result) throw null

        $.ajax({
          url    : action,
          type   : 'POST',
          data   : {id: id},
          success: (result) => {
            if (result.status == 'success') {
              return swal({
                title: 'Success!',
                text : 'The Needs Review flag has been removed.',
                icon : 'success'
              }).then(() => {
                tr.remove()
                Helpers.updateAdminMarkedCount()
              })
            } else {
              return swal({
                title: 'Error!',
                text : 'Could not remove Needs Review flag.',
                icon : 'error',
              })
            }
          },
          error  : (xhr) => {
            return swal('Error!', 'Could not remove Needs Review flag. ' + xhr.responseJSON.message, 'error')
          }
        })
      })
  })

  //Remove Timepunch
  $('#remove-timepunch').click(function (e) {
    let btn    = $(this),
        id     = btn.data('id'),
        action = '/hours/delete/' + btn.data('id'),
        tr     = $('tr#' + id),
        modal  = $('#marked-modal')
    return swal({
      title  : 'Are you sure?',
      text   : 'This will remove the entire timepunch.',
      icon   : 'warning',
      buttons: {
        cancel : 'No, cancel',
        confirm: {
          text      : 'Yes, remove timepunch.',
          className : 'swal-btn-danger',
          value     : true,
          closeModal: false
        }
      }
    })
      .then(result => {
        if (!result) throw null

        $.ajax({
          url    : action,
          type   : 'DELETE',
          success: function () {
            //Success
            return swal({
              title: 'Success!',
              text : 'The time punch has been removed.',
              icon : 'success'
            }).then(() => {
              //Close Modal
              modal.modal('toggle')
              tr.remove()
            })
          },
          error  : function (xhr) {
            activityBtnEnable(btn, 'times', 'Remove Timepunch')

            return swal('Error!', 'There was a problem removing the time punch. ' + xhr.responseJSON.message, 'error')
          }
        })
      })
  })
  $(document).on('blur', '.is-invalid', function () {
    $(this).removeClass('is-invalid')
  })
  $('#save-timepunch').click(function (e) {
    e.preventDefault()
    let btn    = $(this),
        modal  = $('#marked-modal'),
        id     = btn.data('id'),
        data   = $('#edit-hour-form').serialize(),
        action = '/admin/hour/update'

    activityBtnDisable(btn)
    $.ajax({
      url    : action,
      type   : 'POST',
      data   : data,
      success: (result) => {
        activityBtnEnable(btn, 'check', 'Save Changes')
        if (result.status == 'success') {
          Helpers.updateAdminMarkedCount()
          swal('Success!', 'The changes have been saved.', 'success')
        }
        else {
          swal('Error!', 'Unable to save changes.', 'error')
        }
      },
      error  : (xhr) => {
        activityBtnEnable(btn, 'check', 'Save Changes')
        let errs = xhr.responseJSON.errors
        for (let i in errs) {
          if (errs.hasOwnProperty(i)) {
            $('[name="' + i + '"]').addClass('is-invalid')
          }
        }
      }
    })

    activityBtnDisable(btn)

  })

  /** Events Management **/
  $(document).on('click', '.order-arrows button', function () {
      let btn       = $(this),
          thisRow   = btn.closest('tr'),
          thisRowId = thisRow.data('id'),
          prevRow   = thisRow.prev(),
          prevRowId = prevRow.data('id'),
          nextRow   = thisRow.next(),
          nextRowId = nextRow.data('id'),
          dir       = btn.data('dir')
      activityBtnDisable(btn)
      //Move up!
      $.ajax({
        url    : '/admin/events/changeOrder',
        type   : 'PUT',
        data   : {
          thisId: thisRowId,
          nextId: nextRowId,
          prevId: prevRowId,
          dir   : dir
        },
        success: function (result) {
          $('button').attr('disabled', false).removeClass('btn-hide')

          if (dir == 'up') {
            //"Going Up!" --Willy Wonka
            activityBtnEnable(btn, 'arrow-up')

            thisRow.after(prevRow)
          }
          else {
            activityBtnEnable(btn, 'arrow-down')

            nextRow.after(thisRow)
          }
          thisRow.stop(true, true).effect('highlight', 2000)
          $('#events-table > tbody > tr:last-child button[data-dir="down"]').addClass('btn-hide').attr('disabled', true)
          //"Up and Out!" -- Willy Wonka
          $('#events-table > tbody > tr:first-child button[data-dir="up"]').addClass('btn-hide').attr('disabled', true)

        },
        error  :

          function (xhr) {
            activityBtnEnable(btn, 'arrow-' + dir)

            return swal('Error!', 'There was a problem changing the order. ' + xhr.responseJSON.message, 'error')
          }
      })

    }
  )
  $(document).on('click', '.update-event', function () {
    let btn   = $(this),
        id    = btn.data('id'),
        input = btn.closest('div').prev(),
        val   = input.val()
    activityBtnDisable(btn)
    input.attr('disabled', true)

    $.ajax({
        url    : '/admin/events/updateName',
        type   : 'PUT',
        data   : {id: id, val: val},
        success: (result) => {
          if (result.status == 'success') {
            activityBtnEnable(btn, 'check')
            input.attr('disabled', false)
            return swal({
              title  : 'Success!',
              text   : 'The event\'s name has been changed to "' + val + '".',
              icon   : 'success',
              timer  : 4000,
              buttons: false
            })
          }
          else {
            activityBtnEnable(btn, 'check')
            input.attr('disabled', false)
            swal('Error!', 'Could not update event.', 'error')
          }

        },
        error  : (xhr) => {
          activityBtnEnable(btn, 'check')
          input.attr('disabled', false)
          swal('Error!', 'Could not update event. ' + xhr.responseJSON.message, 'error')
        }
      }
    )
  })
  $(document).on('click', '.update-vis', function () {
    let btn = $(this),
        id  = btn.data('id')
    activityBtnDisable(btn)
    $.ajax({
      url    : '/admin/events/toggleVis',
      type   : 'POST',
      data   : {id: id},
      success: (result) => {
        Helpers.hideAllTooltips()
        activityBtnEnable(btn, 'eye')
        if (result.status == 'success') {
          if (btn.hasClass('btn-outline-info')) {
            btn.removeClass('btn-outline-info')
              .addClass('btn-primary')
          }
          else {
            btn.removeClass('btn-primary')
              .addClass('btn-outline-info')
          }
        }
        else {
          swal('Error!', 'Could not update visibility.', 'error')
        }
      },
      error  : (xhr) => {
        Helpers.hideAllTooltips()
        activityBtnEnable(btn, 'eye')
        swal('Error!', 'Could not update visibility. ' + xhr.responseJSON.message, 'error')
      }
    })
  })
  $(document).on('click', '.delete-event', function () {
    let btn    = $(this),
        id     = btn.data('id'),
        action = '/admin/events/delete',
        isPerm = btn.attr('data-isperm') === 'true', //$.data() doesn't work here
        text   = ''
    if (isPerm) {
      text = 'This will purge all of its hours and allow the event name to be reused.'
    }
    else {
      text = 'Deleting this event will prevent it from being selected or edited. All hours recorded for this event - if not purged - will be final. You will also not be able to create another event with this name unless it is destroyed on the Deleted Events tab.'
    }
    return swal({
      title  : 'Are you sure?',
      text   : text,
      icon   : 'warning',
      buttons: {
        cancel : 'No, cancel',
        confirm: {
          text      : 'Yes, delete event.',
          className : 'swal-btn-danger',
          value     : true,
          closeModal: false
        }
      }
    })
      .then(result => {
        if (!result) throw null

        $.ajax({
          url    : action,
          type   : 'DELETE',
          data   : {id: id},
          success: (result) => {
            if (result.status == 'success') {
              return swal({
                title: 'Success!',
                text : 'The event has been deleted.',
                icon : 'success',
                timer: 4000
              }).then(() => {
                $('tr[data-id="' + id + '"]').remove()
                $('button').attr('disabled', false).removeClass('btn-hide')
                $('#events-table > tbody > tr:last-child button[data-dir="down"]').addClass('btn-hide').attr('disabled', true)
                //"Up and Out!" -- Willy Wonka
                $('#events-table > tbody > tr:first-child button[data-dir="up"]').addClass('btn-hide').attr('disabled', true)

              })
            } else {
              //Dropped already
              return swal({
                title: 'Already Deleted',
                text : 'The event has already been deleted.',
                icon : 'info'
              }).then(() => {
                location.reload()
              })
            }
          },
          error  : (xhr) => {
            return swal('Error!', 'Could not delete event. ' + xhr.responseJSON.message)
          }
        })
      })
  })
  $('.restore-event').click(function () {
    let btn = $(this),
        id  = btn.data('id')

    Helpers.buttons.activityBtnDisable(btn)
    Request.send('admin/events/restore', 'POST', {id: id},
      result => {
        Helpers.buttons.activityBtnEnable(btn, 'undo', 'Restore')
        if (result.status == 'success') {
          swal('Success!', 'The event has been restored.', 'success')
            .then(() => { $('tr[data-id="' + id + '"]').remove() })
        }
        else {
          swal('Error!', 'Unable to restore event.', 'error')
        }
      }, xhr => {
        Helpers.buttons.activityBtnEnable(btn, 'undo', 'Restore')
        swal('Error!', 'Unable to restore event. ' + xhr.errors.message, 'error')
      })
  })
  $(document).on('click', '.purge-event', function () {
    let btn    = $(this),
        id     = btn.data('id'),
        action = '/admin/events/purge'
    return swal({
      title  : 'Are you sure?',
      text   : 'Purging this event will remove *all* recorded hours for it from *all* students. Be careful.',
      icon   : 'warning',
      buttons: {
        cancel : 'No, cancel',
        confirm: {
          text      : 'Yes, purge event.',
          className : 'swal-btn-danger',
          value     : true,
          closeModal: false
        }
      }
    })
      .then(result => {
        if (!result) throw null

        $.ajax({
          url    : action,
          type   : 'POST',
          data   : {id: id},
          success: (result) => {
            if (result.status == 'success') {
              return swal({
                title: 'Success!',
                text : 'The event has been purged.',
                icon : 'success'
              })
            } else {
              //Dropped already
              return swal({
                title: 'Error!',
                text : 'Could not purge.',
                icon : 'error',
              })
            }
          },
          error  : (xhr) => {
            return swal('Error!', 'Could not purge event. ' + xhr.responseJSON.message)
          }
        })
      })
  })
  $(document).on('click', '.purge-event', function () {
    let btn    = $(this),
        id     = btn.data('id'),
        action = '/admin/events/purge'
    return swal({
      title  : 'Are you sure?',
      text   : 'Purging this event will remove *all* recorded hours for it from *all* students. Be careful.',
      icon   : 'warning',
      buttons: {
        cancel : 'No, cancel',
        confirm: {
          text      : 'Yes, purge event.',
          className : 'swal-btn-danger',
          value     : true,
          closeModal: false
        }
      }
    })
      .then(result => {
        if (!result) throw null

        $.ajax({
          url    : action,
          type   : 'POST',
          data   : {id: id},
          success: (result) => {
            if (result.status == 'success') {
              return swal({
                title: 'Success!',
                text : 'The event has been purged.',
                icon : 'success'
              })
            } else {
              //Dropped already
              return swal({
                title: 'Error!',
                text : 'Could not purge.',
                icon : 'error',
              })
            }
          },
          error  : (xhr) => {
            return swal('Error!', 'Could not purge event. ' + xhr.responseJSON.message)
          }
        })
      })
  })
  $('.add-event').click(function () {
    let btn   = $(this),
        input = btn.closest('div').prev(),
        name  = input.val()
    activityBtnDisable(btn)
    $.ajax({
      url    : '/admin/events/create',
      data   : {name: name},
      type   : 'POST',
      success: (result) => {
        activityBtnEnable(btn, 'check')
        if (result.status == 'success') {
          let id  = result.id,
              td1 = '<td class="order-arrows">' +
                '<button class="btn btn-warning order" data-dir="down" data-id="' + id + '">' +
                '<i class="fas fa-arrow-down"></i></button>' +
                '<button class="btn btn-primary order" data-dir="up" data-id="' + id + '">' +
                '<i class="fas fa-arrow-up"></i></button></td>',
              td2 = '<td>' +
                '<div class="input-group mb-3">' +
                '<input class="form-control event-input" value="' + name + '">' +
                '<div class="input-group-append">' +
                '<button class="btn btn-outline-success update-event" type="button" data-id="' + id + '">' +
                '<i class="fas fa-check"></i></button></div></div></td>',
              td3 = '<td>' +
                '<button' +
                ' class="btn btn-primary update-vis"' +
                ' data-id="' + id + '"' +
                ' rel="tooltip"' +
                ' title="Toggle visibility">' +
                '<i class="fas fa-eye"></i></button> ' +
                '<button class="btn btn-outline-danger delete-event" data-id="' + id + '" rel="tooltip"' +
                ' title="Delete event">' +
                '<i class="fas fa-times"></i></button> ' +
                '<button class="btn btn-outline-warning purge-event" data-id="' + id + '"' +
                ' rel="tooltip"\n' +
                ' title="Purge event hours"><i' +
                ' class="fas fa-backward"></i></button> ' +
                '</td>'

          $('#events-body').append('<tr data-id="' + id + '">' + td1 + td2 + td3 + '</tr>')
          $('tr[data-id=' + id + ']').stop(true, true).effect('highlight', 2000)

          input.val('')

          $('button').attr('disabled', false).removeClass('btn-hide')
          $('#events-table > tbody > tr:last-child button[data-dir="down"]').addClass('btn-hide').attr('disabled', true)
          //"Up and Out!" -- Willy Wonka
          $('#events-table > tbody > tr:first-child button[data-dir="up"]').addClass('btn-hide').attr('disabled', true)

        }
        else {
          swal('Error!', 'Could not create event.', 'error')
        }
      },
      error  : (xhr) => {
        activityBtnEnable(btn, 'check')
        swal('Error!', 'Could not update visibility. ' + xhr.responseJSON.errors.name[0], 'error')
      }
    })
  })

  /** Club Config */
  if ($('#club-manage').length) {
    $('.checkbox').bootstrapToggle('off')
    $('.checkbox[checked]').bootstrapToggle('on')

    $('#save-club').on('click', function (e) {
      e.preventDefault()
      let btn       = $(this),
          action    = btn.data('action'),
          desc      = $('#clubDesc').val(),
          master    = $('#master').prop('checked'),
          aDeletion = $('#aDeletion').prop('checked'),
          aMark     = $('#aMark').prop('checked'),
          aComments = $('#aComments').prop('checked')

      let data = {
        desc         : desc,
        master       : master ? 1 : 0,
        allowDeletion: aDeletion ? 1 : 0,
        allowMark    : aMark ? 1 : 0,
        allowComments: aComments ? 1 : 0
      }
      Helpers.buttons.activityBtnDisable(btn)
      Request.send(action, 'PUT', data, () => {
        Helpers.buttons.activityBtnEnable(btn, 'check', 'Save Changes')
        swal('Success!', 'The club has been updated.', 'success')
      }, xhr => {
        Helpers.buttons.activityBtnEnable(btn, 'check', 'Save Changes')
        swal('Error!', 'Unable to update club configuration.', 'error')
        console.log(xhr)
      })
    })

  }

  /** Hour Statistics **/
  //Chart Data
  $.get(
    '/admin/ajax/hours/statCharts'
  ).done(function (r) {
    aLoadGraphs(r)
  })
    .fail(function (xhr) {
      $('canvas').remove()
      console.log('Unable to retrieve charts :(')
    })

  function aLoadGraphs (data) {
    console.log(data)

    function dynamicColors () {
      var r = Math.floor(Math.random() * 255)
      var g = Math.floor(Math.random() * 255)
      var b = Math.floor(Math.random() * 255)
      return 'rgba(' + r + ',' + g + ',' + b + ', 0.5)'
    }

    function poolColors (a) {
      var pool = []
      for (i = 0; i < a; i++) {
        pool.push(dynamicColors())
      }
      return pool
    }

    let lineChart = new Chart(document.getElementById('line-chart'), {
      'type'   : 'line',
      'data'   : {
        'labels'  : data.line.labels,
        'datasets': [{
          'label'      : 'Average Duration',
          'data'       : data.line.data,
          'fill'       : false,
          'borderColor': 'rgb(75, 192, 192)',
          'lineTension': 0.1
        }]
      },
      'options': {}
    })
    let pieChart = new Chart(document.getElementById('pie-chart'), {
      'type': 'doughnut',
      'data': {
        'labels'  : data.pie.labels,
        'datasets': [{
          'label'          : 'Number of Events',
          'data'           : data.pie.data,
          'backgroundColor': poolColors(data.pie.data.length)
        }]
      }
    })
    let mixedChart = new Chart(document.getElementById('mixed-chart'), {
      type   : 'bar',
      data   : {
        labels  : data.mixed.labels,
        datasets: [{
          label          : 'Total Hours',
          data           : data.mixed.totals,
          backgroundColor: 'rgba(255,99,132,0.2)',
          borderColor    : poolColors(1)[0]
        }]
      },
      /*
        {
          label      : 'Out of Classroom',
          data       : [65, 59, 80, 81, 56, 55, 40],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        },
        {
          label      : 'Event 1',
          data       : [29, 19, 40, 11, 76, 5, 30],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        },
        {
          label      : 'Event 2',
          data       : [11, 19, 20, 10, 46, 25, 10],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        },
        {
          label      : 'Event 3',
          data       : [41, 29, 12, 33, 12, 32, 12],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        }]*/
      options: {}
    })

    //Add data to mixed chart
    for (let evt in data.mixed.datasets) {
      if (data.mixed.datasets.hasOwnProperty(evt)) {
        console.log(data.mixed.datasets[evt])
        mixedChart.data.datasets.push({
          label      : evt,
          data       : data.mixed.datasets[evt],
          borderColor: poolColors(1)[0],
          lineTension: 0.1,
          type       : 'line',
          fill       : false
        })
        mixedChart.update()
      }
    }
  }

  /** System Log **/
  $('#syslog-table').DataTable({
    'order': [[0, 'desc']]
  })
}

/** Club Select **/
$('#join-btn').click(function (e) {
  e.preventDefault();
  let form = $('#join-form')[0],
      btn = $(this);
  if (form.checkValidity())
    Helpers.buttons.activityBtnDisable(btn)
    form.submit();
})

/** My Clubs **/
$('.leave-club').click(function () {
  let btn = $(this),
      id  = btn.data('id')
  return swal({
    title  : 'Are you sure?',
    text   : 'This will delete all of your hours and remove you from the club! Archive first if necessary.',
    icon   : 'warning',
    buttons: {
      cancel : 'No, cancel',
      confirm: {
        text      : 'Yes, leave club.',
        className : 'swal-btn-danger',
        value     : true,
        closeModal: false
      }
    }
  }).then(result => {
    if (!result) throw null

    Request.send('clubs/leave', 'POST', {id: id}, result => {
      if (result.status == 'success') location.reload()
      else swal('Error!', 'Unable to leave club.', 'error')
    }, xhr => {
      swal('Error!', 'Unable to leave club. ' + xhr.responseJSON.errors.id[0], 'error')
    })
  })
})
$('.archive-club').click(function () {
  let btn      = $(this),
      club     = btn.data('id'),
      prevText = (btn.attr('data-prev-text')) ? btn.data('prev-text') : 'Archive'
  Helpers.buttons.activityBtnDisable(btn)
  Request.send('clubs/archive', 'POST', {club: club}, result => {
    Helpers.buttons.activityBtnEnable(btn, 'archive', prevText)
    if (result.status == 'success') {
      window.location = result.message //Download
    }
    else {
      swal('Error!', 'Unable to retrieve archive.', 'error')
    }
  }, xhr => {
    Helpers.buttons.activityBtnEnable(btn, 'archive', 'Archive')
    swal('Error!', 'Unable to retrieve archive. ' + xhr.error.message, 'error')
  })
})
