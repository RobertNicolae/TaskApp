function getHtmlForTask (task, rowNum) {
  return `<tr>
    <td>` + rowNum + `</td>
     <td>` + task.name + `</td>
       <td><button onclick="deleteTask(this)" class="btn btn-outline-success" data-id="` + task.id + `">Delete</button></td>
<td>` + getButtonForTask(task) + ` </td>`
}

const getButtonForTask = function (task) {
  if (task.status === 0) {
    return `<td><button onclick="markAsDone(this)" class="btn btn-outline-success" data-id="` + task.id + `">Mark as done</button></td>`
  } else {
    return `<td><button onclick="markAsDone(this)" class="btn btn-outline-success" data-id="` + task.id + `">Mark as undone</button></td>`
  }
}

const addTaskInTable = function (task) {
  let rowNum = $('#tasks tbody tr').length + 1

  return $('#tasks tbody').append(getHtmlForTask(task, rowNum))
}

const markAsDone = function (button) {
  let id = $(button).data('id')

  $.ajax({
    url: 'http://localhost/public/index.php/task/mark/' + id,
    method: 'post',
    success: function (data) {
      refreshTaskTable()
    }
  })
}

function refreshTaskTable () {
  let tbody = $('#tasks tbody')
  tbody.html('Retrieve data..')

  $.ajax({
    url: 'http://localhost/public/index.php/task/index',
    method: 'GET',
    success: function (data) {
      console.log(data)
      tbody.html('')
      data.tasks.forEach((task) => $('#tasks tbody').append(addTaskInTable(task)))
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
      name: formData.get('name')
    }),
    success: function (data) {
      refreshTaskTable()
    }

  })
})

const deleteTask = function (button) {
  let id = $(button).data('id')

  $.ajax({
    url: 'http://localhost/public/index.php/task/delete/' + $(button).data('id'),
    method: 'POST',
    success: function (data) {
      refreshTaskTable()

    }
  })
}

$(document).ready(function (event) {
  refreshTaskTable()

})