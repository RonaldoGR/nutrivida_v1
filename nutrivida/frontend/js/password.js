document.addEventListener('DOMContentLoaded', () => {
    loadHeaderData();

    document.getElementById('btnLogout').addEventListener('click', async () => {
        try { await fetch('../../api/patient.php?action=logout'); } catch (e) {}
        window.location.href = 'login.html';
    });

    document.getElementById('password-form').addEventListener('submit', handlePasswordUpdate);
});

async function loadHeaderData() {
    try {
        const req = await fetch('../../api/patient.php?action=getLogged');
        const res = await req.json();
        if(res.status === 'success') {
            const user = res.data;
            const firstName = user.name ? user.name.split(' ')[0] : 'Paciente';
            document.getElementById('headerName').innerText = 'Olá, ' + firstName;
        } else {
            window.location.href = 'login.html';
        }
    } catch(e) { console.error(e); }
}

async function handlePasswordUpdate(e) {
    e.preventDefault();

    const currentPassword = document.getElementById('current-password').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;
    const errorMsg = document.getElementById('password-match-error');

    // Validação Frontend: Senhas iguais
    if (newPassword !== confirmPassword) {
        errorMsg.style.display = 'block';
        return; 
    } else {
        errorMsg.style.display = 'none';
    }

     try {
        const formData = new FormData();
        formData.append('updatePassword', true); 
        formData.append('currentPassword', currentPassword);
        formData.append('newPassword', newPassword);

        const req = await fetch('../../api/patient.php?action=updatePassword', {
            method: 'POST',
            body: formData
        });

        const text = await req.text();
        let res;
        try { res = JSON.parse(text); } catch(err) { throw new Error(text); }

        if(res.status === 'success') {
            alert("Senha alterada com sucesso!");
            document.getElementById('password-form').reset();
        } else {
            alert("Erro: " + res.message);
        }

    } catch(err) {
        console.error(err);
        alert("Erro ao conectar com o servidor.");
    }
}