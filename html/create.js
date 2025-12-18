
var submitButton = document.querySelector('#submitButton'); 
submitButton.addEventListener("click", validate);


function validate(){
    var email = document.getElementById('email').value.toLowerCase();
    if(email ===""){
            alert("Email is missing. Please enter.");
            document.getElementById("email").focus();
            return;
        }
  
    var username = document.getElementById('username').value.toLowerCase();
    if(username ===""){
            alert("Username is missing. Please enter.");
            document.getElementById("username").focus();
            return;
        }

    var password = document.getElementById('pw').value;
    if(password === ""){
        alert("Password is missing. Please enter.");
        document.getElementById("pw").focus();
        return;
    }


const formData = new URLSearchParams();
formData.append('username', username);
formData.append('email', email);
formData.append('password', password);

fetch('create.php', {
    method: 'POST',
    body: formData  // <- let fetch handle Content-Type
})
      .then(response => response.text())
      .then(data => {
    console.log("Response:", data); // debug
    alert(data.trim());
        if (data.trim() === 'verified') {
            alert("Account created!");
        }
        else{
          alert("The email and/or username you entered are already in use. Please try again.");
        }
      })
      .catch(error => {
        alert('Something went wrong. Try again later.');
      });
      
    }