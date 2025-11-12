
var submitButton = document.querySelector('#submitButton'); 
submitButton.addEventListener("click", validate);


function validate(){
    var email = document.getElementById('email').value.toLowerCase();
    if(email ===""){
            alert("Email is missing. Please enter.");
            document.getElementById("email").focus();
            return;
        }
  
    var password = document.getElementById('pw').value;
    if(password === ""){
        alert("Password is missing. Please enter.");
        document.getElementById("pw").focus();
        return;
    }

const formData = new URLSearchParams();
formData.append('email', email);
formData.append('password', password);

fetch('login.php', {
    method: 'POST',
    body: formData  // <- let fetch handle Content-Type
})
.then(response => response.text())
.then(data => {
    console.log("Response:", data);

if (data.trim() === 'verified') {
  window.location.href = "home.php";
  } 
else {
  alert("Credentials invalid. Please try again, or create an account if you do not have an existing one.");
  }

})
.catch(error => {
    alert('Something went wrong. Try again later.');
});

}