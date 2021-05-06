const showAlert = function (type, title, message) {
  Swal.fire({
    title: title,
    text: message,
    icon: type,
    confirmButtonText: 'Ok'
  })
}

const getButtonForObject = function (objective) {
  if (objective.status === 0) {
    return `<td><button onclick="markAsDone(this)" class="btn btn-outline-success" data-id="` + objective.id + `">Mark as done</button></td>`
  } else {
    return `<td><button onclick="markAsDone(this)" class="btn btn-outline-success" data-id="` + objective.id + `">Mark as undone</button></td>`
  }
}




const markAsDone = function (button) {
  let id = $(button).data('id')

  $.ajax({
    url: 'http://localhost/public/index.php/objective/mark/' + $(button).data('id'),
    method: 'post',
    success: function (data) {
      refreshObjectiveTable()
    }
  })
}

const addToGoogleCalendar = function (button) {
  let id = $(button).data('id')
  $.ajax({
    url: 'http://127.0.0.1/public/index.php/g/calendar/' + id,
    method: 'POST',
    dataType: 'JSON',
    data: {
      code: localStorage.getItem('GoogleCode')
    },
    success: function (data) {

    }

  })
}

let url = new URL(window.location.href)
let params = url.searchParams
if (params.get('code')) {
  $.ajax({
    url: 'http://127.0.0.1/public/index.php/g/oauth',
    method: 'GET',
    data: {
      code: params.get("code")
    },
    success: function (data) {
      localStorage.setItem('GoogleCode', data.token.access_token)

    },
    error: function (error) {
      console.log(error)
    }

  })

}

$('#google').on('click', function () {
  $.ajax({
    url: 'http://127.0.0.1/public/index.php/g/oauth',
    method: 'GET',
    success: function (data) {
      window.location.href = data.AuthURL

    },
    error: function (error) {
      console.log(error)
    }

  })
})

function getHtmlForObjective (objective, rowNum) {

  return `<tr>
    <td>` + rowNum + `</td>
     <td>` + objective.name + ` </td>
       <td><button onclick="deleteObjective(this)" class="btn btn-outline-success" data-id="` + objective.id + `">Delete</button></td>
<td><a href="http://localhost/src/Client/index.html?id=` + objective.id + ` "class="btn btn-outline-success" > Check</a> </td>
<td>` + getButtonForObject(objective) + `</td>
`
}

const addObjectiveInTable = function (objective) {
  let rowNum = $('#objective tbody tr').length + 1
  return $('#objective tbody').append(getHtmlForObjective(objective, rowNum))
}

function refreshObjectiveTable () {
  let tbody = $('#objective tbody')
  tbody.html('Retrieve data..')

  $.ajax({
    url: 'http://localhost/public/index.php/objective/',
    method: 'GET',
    headers: {
      'X-AUTH-TOKEN': localStorage.getItem('X-AUTH-TOKEN')
    },
    success: function (data) {
      console.log(data)
      tbody.html('')
      data.objectives.forEach((objective) => addObjectiveInTable(objective))
    },
    error: function (error) {
      console.log(error)
    }
  })
}

$('#add_objectiv_form').on('submit', function (event) {
  event.preventDefault()
  let formData = new FormData(this)
  $.ajax({
    url: 'http://localhost/public/index.php/objective/create',
    method: 'POST',
    data: JSON.stringify({
      name: formData.get('name')
    }),
    success: function (data) {
      refreshObjectiveTable()
    }
  })
})

const deleteObjective = function (button) {
  let id = $(button).data('id')

  $.ajax({
    url: 'http://localhost/public/index.php/objective/delete/' + id,
    method: 'POST',
    success: function () {
      refreshObjectiveTable()
    }

  })
}

const getTasks = function (button) {
  let id = $(button).data('id')

  $.ajax({
    url: 'http://localhost/public/index.php/task/get/' + id,
    method: 'GET',
    success: function (data) {
      console.log(data)
    },
    error: function (error) {
      console.log(error)
    }
  })
}

$(document).ready(function (event) {
  refreshObjectiveTable()
})