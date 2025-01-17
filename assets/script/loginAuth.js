let errorDiv = document.querySelector('#erroDiv');
let errorDivTitle = document.querySelector('#erroDivTitle');
let errorDivDesc = document.querySelector('#erroDivDesc');
document.querySelector('#erroDivCloseBtn').addEventListener("click", function(){
    errorDiv.style.display = 'none';
})
function validacaoEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}
document.querySelector("#mf_button").addEventListener("click", function(){
    let email = document.querySelector("#imail");
    let pass = document.querySelector("#ipass");
    let email_error = true;
    let pass_error = true;
    if(validacaoEmail(email.value)){
        email.style.border = '1px solid rgb(51 61 70 / var(--tw-border-opacity, 1))';
        email_error = false;
    }else{
        email.style.border = '1px solid red';
        email_error = true;
    }
    if(pass.value.length > 4){
        pass_error = false;
        pass.style.border = '1px solid rgb(51 61 70 / var(--tw-border-opacity, 1))';
    }else{
        pass_error = true;
        pass.style.border = '1px solid red';
    }
    if(!email_error && !pass_error){
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "server.php");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4){
                if(xhr.status == 201){
                    errorDiv.style.display = 'block';
                    errorDivTitle.textContent = 'SUCESS';
                    errorDivDesc.textContent = 'login successful';
                }
                if(xhr.status == 400){
                    errorDiv.style.display = 'block';
                    errorDivTitle.textContent = 'BAD INFO';
                    errorDivDesc.textContent = 'INVALID CREDENTIALS';
                }
                if(xhr.status == 429){
                    errorDiv.style.display = 'block';
                    errorDivTitle.textContent = 'TOO MANY REQUESTS';
                    errorDivDesc.textContent = 'TRY AGAIN ' + xhr.responseText;
                }
                if(xhr.status == 500){
                    errorDiv.style.display = 'block';
                    errorDivTitle.textContent = 'SERVER ERROR';
                    errorDivDesc.textContent = 'TRY AGAIN LATER';
                }
            }
        }
        xhr.send("email="+email.value+"&password="+pass.value);
    }
})

