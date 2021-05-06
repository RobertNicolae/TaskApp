const showAlert = function (type, title, message) {
  Swal.fire({
    title: title,
    text: message,
    icon: type,
    confirmButtonText: 'Ok'
  })
}
  $(document).ready(function () {

    $('#login').on('submit', function (event) {
      event.preventDefault();
      let formData = new FormData(this);

      $.ajax({
        url: "http://localhost/public/index.php/login",
        method: "post",
        data: JSON.stringify({
          username: formData.get("username"),
          password: formData.get("password")
        }),
        success: function (data){
          console.log(data)
            localStorage.setItem("X-AUTH-TOKEN", data.token)
             window.location="http://localhost/src/Client/objective.html"
        },
        error: function (xhr, status, error) {
          showAlert('error', 'Error', error)
        }
      })
    });
  })
