
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('password-form');
    form.addEventListener('submit', handlePasswordUpdate);
});

async function handlePasswordUpdate(e) {
    e.preventDefault();

    const currentPwd = document.getElementById('current-password').value; 
    const newPwd = document.getElementById('new-password').value;         
    const confirmPwd = document.getElementById('confirm-password').value; 
    const msgElement = document.getElementById('password-match-error');
    msgElement.style.display = 'none';
    msgElement.innerText = '';
    
    if (newPwd !== confirmPwd) {
        msgElement.style.display = 'block';
        msgElement.innerText = "A nova senha e a confirmação não coincidem.";
        return;
    }
    

    try {
        const data = new FormData();
        data.append('currentPassword', currentPwd);
        data.append('newPassword', newPwd);
        data.append('updatePassword', true); 

        const req = await fetch('../../api/nutritionist.php?action=updatePassword', {
            method: 'POST',
            body: data
        });

        const res = await req.json();

        msgElement.style.display = 'block';

        if (res.status === 'success') {
            msgElement.style.color = 'green';
            msgElement.innerText = res.message || "Senha atualizada com sucesso!";
            alert("Senha atualizada com sucesso!");
            e.target.reset();
        } else {
            msgElement.style.color = 'red';
            msgElement.innerText = res.message || "Erro ao atualizar senha.";
        }

    } catch (error) {
        msgElement.style.color = 'red';
        msgElement.innerText = "Erro de conexão com o servidor.";
        console.error("Erro no fetch:", error);
    }
}