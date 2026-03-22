let userType = 'patient'; 
function setLoginType(type) {
    userType = type;
    
    // Atualiza visual dos botões
    document.getElementById('btnPatient').className = type === 'patient' ? 'active' : '';
    document.getElementById('btnNutri').className = type === 'nutritionist' ? 'active' : '';
    
    // Atualiza título
    const title = type === 'patient' ? 'Login Paciente' : 'Área do Nutricionista';
    document.getElementById('loginTitle').innerText = title;
}
let loginForm =  document.getElementById('loginForm');

loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const msgDiv = document.getElementById('msg');


    const pathArray = window.location.pathname.split('/');
    const projectRoot = '';

    let url = '';
    if (userType === 'patient') {
        url = window.location.origin + projectRoot + '/api/patient.php?action=login';
    } else {
        url = window.location.origin + projectRoot + '/api/nutritionist.php?action=login';
    }
    console.log("Tentando logar em:", url);

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password, login: true }) // login: true para passar no isset
        });

        const data = await response.json();

        if (data.status === 'success') {
            // Salva alguns dados no localStorage para facilitar exibição 
            localStorage.setItem('userType', userType);
            msgDiv.innerText = 'Login realizado! Redirecionando...';
            window.location.href =  data.redirect; 
        } else {
            msgDiv.innerText = data.message;
        }

    } catch (error) {
        console.error(error);
        msgDiv.innerText = "Erro ao conectar com o servidor.";
    }
});