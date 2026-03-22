document.addEventListener('DOMContentLoaded', () => {
    

    const registerForm = document.querySelector('.form-registration-mode');
    
    if (registerForm) {

        registerForm.querySelectorAll(':disabled').forEach(element => {
            element.removeAttribute('disabled');
        });

        const photoLabel = document.getElementById('photo-label');
        if (photoLabel) {
            photoLabel.classList.remove('photo-label-disabled');
            const uploadText = document.getElementById('upload-text');
            if(uploadText) uploadText.innerText = "Clique para enviar foto";
        }
        const saveArea = document.getElementById('save-area');
        if (saveArea) {
            saveArea.classList.remove('hidden');
        }
    }

    const formPatient = document.getElementById('patient-full-form'); 
    
    if (formPatient) {
        formPatient.addEventListener('submit', (e) => handleRegister(e, 'patient'));
    }

    const formNutri = document.getElementById('formNutri');
    if (formNutri) {
        formNutri.addEventListener('submit', (e) => handleRegister(e, 'nutritionist'));
    }
});



async function handleRegister(event, type) {
    event.preventDefault(); 
    const form = event.target;
    

    let msg = document.getElementById('msg');
    if(!msg) { 
        msg = document.createElement('p');
        msg.id = 'msg';
        form.appendChild(msg);
    }
    
    const btn = form.querySelector('button[type="submit"]');

    if(btn) {
        btn.disabled = true;
        btn.innerText = 'Enviando...';
    }
    msg.innerText = '';

    const formData = new FormData(form);

    let url = '';
    
    if (type === 'patient') {
        url = '../../api/patient.php?action=register';
        formData.append('register', 'true'); 
    } else {
        url = '../../api/nutritionist.php?action=register';
        formData.append('register', 'true');
    }

    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData 
        });

        const data = await response.json();

        if (data.status === 'success') {
            msg.style.color = 'green';
            msg.innerText = 'Cadastro realizado! Redirecionando...';
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        } else {
            msg.style.color = 'red';
            msg.innerText = 'Erro: ' + (data.message || 'Erro desconhecido');
            if(btn) {
                btn.disabled = false;
                btn.innerText = 'Tentar Novamente';
            }
        }

    } catch (error) {
        console.error(error);
        msg.style.color = 'red';
        msg.innerText = 'Erro de conexão com o servidor.';
        if(btn) {
            btn.disabled = false;
            btn.innerText = 'Tentar Novamente';
        }
    }
}
