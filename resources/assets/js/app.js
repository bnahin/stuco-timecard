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

function activityBtnEnable (btn, glyph, text = '', reset = true) {
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
      comments: $('#comments').val()
    })
    .done(function () {
      activityBtnEnable(btn, 'check', 'Success', false)
      return swal({
        title  : 'Success!',
        text   : 'The time punch was successful.',
        icon   : 'success',
        timer  : 4000,
        buttons: false
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
 * Clock In Submission]
 */

//Clock in
$('.clock-in').click(function (e) {
  e.preventDefault()
  let mainBtn  = $('#ci-main'),
      dropBtn  = $('#ci-addon'),
      returnTo = $(this).attr('data-return'),
      action   = $('#clock-in-form').attr('action'),
      id       = $('#hour-id').val()

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
        title  : 'Success!',
        text   : 'You have clocked in.',
        icon   : 'success',
        timer  : 4000,
        buttons: false
        //TODO make this self-destruct and redirect
      }).then(() => {
        window.location = returnTo
      })

    })
    .fail(function (xhr) {
      mainBtn.attr('disabled', false)
      mainBtn.html('<i class="fas fa-sign-in-alt"></i> Clock In')
      dropBtn.attr('disabled', false)
      swal('Error!', 'There was an error processing the time punch.', 'error')
      console.log(xhr.responseJSON)
    })

})

//Remove Timepunch
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
      activityBtnEnable(btn, 'times', 'Remove Time Punch')

      return swal('Error!', 'There was a problem removing the time punch. ' + xhr.responseJSON.message, 'error')
    }
  })
})

/**
 Hours Page
 */

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
          'label'      : 'Average Hours',
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
}

/**
 Admin Page
 */
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
          '[Actions] like block, etc.'
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
                title  : 'Success!',
                text   : 'The student has been dropped.',
                icon   : 'success',
                timer  : 4000,
                buttons: false
                //TODO make this self-destruct and redirect
              }).then(() => {
                location.reload()
              })
            } else {
              //Dropped already
              return swal({
                title  : 'Already Dropped',
                text   : 'The student has already been dropped.',
                icon   : 'info',
                timer  : 4000,
                buttons: false
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
                title  : 'Success!',
                text   : 'The students have been purged.',
                icon   : 'success',
                timer  : 4000,
                buttons: false
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
            title  : 'Success!',
            text   : 'The student has been unblocked.',
            icon   : 'success',
            timer  : 4000,
            buttons: false
            //TODO make this self-destruct and redirect
          }).then(() => {
            location.reload()
          })
        }
        else {
          //Unblocked already?
          return swal({
            title  : 'Already Unblocked',
            text   : 'The student has already been unblocked.',
            icon   : 'info',
            timer  : 4000,
            buttons: false
            //TODO make this self-destruct and redirect
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
          //TODO Put data in modal
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
              }).then(() =>
                tr.remove())
            } else {
              return swal({
                title: 'Error!',
                text : 'Could not remove Needs Review flag.',
                icon : 'error',
              })
            }
          },
          error  : (xhr) => {
            return swal('Error!', 'Could not remove Needs Review flag. ' + xhr.responseJSON.message)
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
  $(document).on('blur', '#edit-hour-form .is-invalid', function () {
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
        activityBtnEnable(btn, 'eye')
        swal('Error!', 'Could not update visibility. ' + xhr.responseJSON.message, 'error')
      }
    })
  })
  $(document).on('click', '.delete-event', function () {
    let btn    = $(this),
        id     = btn.data('id'),
        action = '/admin/events/delete'
    return swal({
      title  : 'Are you sure?',
      text   : 'Deleting this event will prevent it from being selected or edited. All hours recorded for this event if not purged will be final. You will also not be able to create another event with this name.',
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
                title  : 'Success!',
                text   : 'The event has been deleted.',
                icon   : 'success',
                timer  : 4000,
                buttons: false
                //TODO make this self-destruct and redirect
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
                title  : 'Already Deleted',
                text   : 'The event has already been deleted.',
                icon   : 'info',
                timer  : 4000,
                buttons: false
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

  /** System Log **/
  $('#syslog-table').DataTable({
    'order': [[0, 'desc']]
  })
}