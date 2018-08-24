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
          messageTop   : 'Name: Nahin, Blake\nGrade: 12\nStudent ID: 115602',
          exportOptions: {
            columns: ':not(.print-hide)'
          }
        }, {
          extend       : 'excel',
          messageTop   : 'Name: Nahin, Blake',
          filename     : 'Nahin, Blake - Time Punches',
          sheetName    : 'Nahin, Blake - Time Punches',
          exportOptions: {
            columns: ':not(.print-hide)'
          }
        },
        {
          extend       : 'print',
          messageTop   : 'Name: Nahin, Blake | Grade: 12 | Student ID: 115602',
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

  //--AJAX---//
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
          let data = result.data;


          //Then, show Modal
          $('#marked-modal').modal('toggle')
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
  $(function () {
    $('.clockpicker').clockpicker({
      twelvehour: true,
    })
    $('#start-time').datetimepicker({
      formatDate: 'm/d/Y',
      step      : 60,
      timepicker: false
    })
  })
}