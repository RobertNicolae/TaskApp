
$(document).ready(function () {
  $('#register').on("submit", function (event){
    event.preventDefault()
    let formData = new FormData(this);
    $.ajax({
      url: "http://localhost/public/index.php/register",
      method: "post",
      data: JSON.stringify({
        email: formData.get("email"),
        password: formData.get("password"),
      }),
      success: function (data){
        alert("Account created")

      },
      error: function (xhr, status, error) {

      }
    })
  })

});