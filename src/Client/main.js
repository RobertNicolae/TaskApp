const urlService = new URL(window.location.href)
const idObjective = parseInt(urlService.searchParams.get('id'))



function getHtmlForTask (task, rowNum) {
  return `<tr>
    <td>` + rowNum + `</td>
     <td><button onclick="getDataForTask(this)" class="btn btn-outline-success" data-id="` + task.id + `"> ` + task.name + `</button></td>
       <td><button onclick="deleteTask(this)" class="btn btn-outline-success" data-id="` + task.id + `">Delete</button></td>
<td>` + getButtonForTask(task) + ` </td>
<td><button onclick="addToGoogleCalendar(this)" data-id="` + task.id + `" data-name="` + task.name + `">Add to calendar</button> </td>`
}

const getButtonForTask = function (task) {
  if (task.status === 0) {
    return `<td><button onclick="markAsDoneTask(this)" class="btn btn-outline-success" data-id="` + task.id + `">Mark as done</button></td>`
  } else {
    return `<td><button onclick="markAsDoneTask(this)" class="btn btn-outline-success" data-id="` + task.id + `">Mark as undone</button></td>`
  }
}





const addTaskInTable = function (task) {
  let rowNum = $('#tasks tbody tr').length + 1

  return $('#tasks tbody').append(getHtmlForTask(task, rowNum))
}

const getHtml = function (task) {
  return '<h1>` + task.name + ` </h1>\n' +
    '    <p class="deadline">$19.99</p>\n' +
    '    <p>Some text about the jeans..</p>\n' +
    '    <p><button>Add to Cart</button></p>'
}

const addDataOnCard = function (task) {
  return $('#card').append(getHtml(task))
}
const markAsDoneTask = function (button) {
  let id = $(button).data('id')

  $.ajax({
    url: 'http://localhost/public/index.php/task/mark/' + id,
    method: 'post',
    success: function (data) {
      refreshTaskTable()
    }
  })
}

const addTitle = function (data) {
  return '<h1>Progress of objective + `data` + </h1>'
}
``
function refreshTaskTable () {
  let tbody = $('#tasks tbody')
  tbody.html('Retrieve data..')

  $.ajax({
    url: 'http://localhost/public/index.php/task/get/' + idObjective,
    method: 'GET',
    success: function (data) {
      console.log(data.tasks[0])
      tbody.html('')
      data.tasks.forEach((task) => addTaskInTable(task))

      $('#header').html(`<h3> Your progress for objective: ` + data.tasks[0].objective + `</h3>`)
    },
    error: function (error) {
      console.log(error)
    }
  })
}

$('#add_task_form').on('submit', function (event) {
  event.preventDefault()
  let formData = new FormData(this)
  $.ajax({
    url: 'http://localhost/public/index.php/task/create',
    method: 'POST',
    data: JSON.stringify({
      name: formData.get('name'),
      deadline_date: formData.get("deadline_date"),
      description: formData.get("description"),
      objective: idObjective
    }),
    success: function (data) {
      refreshTaskTable()
      showAlert('success', 'Success', 'You added a task')
    },
    error: function (xhr, status, error) {
      showAlert('error', 'Error', 'Please enter a correct task name')
    }

  })
})

const deleteTask = function (button) {

  $.ajax({
    url: 'http://localhost/public/index.php/task/delete/' + $(button).data('id'),
    method: 'POST',
    success: function () {
      refreshTaskTable()
      showAlert('success', 'Success', 'You deleted a task')

    }
  })
}

const getDataForTask = function (button) {

  $.ajax({
    url: 'http://localhost/public/index.php/task/find/' + $(button).data('id'),
    method: 'get',
    success: function (data) {
      console.log(data)
      showAlert('info', 'Task name: ' + data.name, 'Description: ' + data.description + ' Deadline: ' + data.deadline_date.date)

    }
  })
}

$(document).ready(function (event) {
  refreshTaskTable()

})