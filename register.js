
//password validation 
var password = document.getElementById("password");
var confirm_password = document.getElementById("confirm_password");
var tick = document.getElementById("password-tick");

function validatePassword()
{
    if(confirm_password.value === "")
    {
        //if confirm field is empty
        tick.textContent = "";
        confirm_password.setCustomValidity('');
    }
    else if(password.value != confirm_password.value)
    {
        //passwords don't match
        tick.textContent = "\u2716"; //show x
        tick.style.color = "red";
        confirm_password.setCustomValidity("Passwords Don't Match");
    }
    else
    {
        //passwords match
        tick.textContent = "\u2714"; //show tick
        tick.style.color = "green";
        confirm_password.setCustomValidity(''); //valid
    }
}

password.addEventListener('keyup', validatePassword);
confirm_password.addEventListener('keyup', validatePassword);