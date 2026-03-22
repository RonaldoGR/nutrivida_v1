document.addEventListener('DOMContentLoaded', () => {
    loadPatientData();

    document.getElementById('btnLogout').addEventListener('click', async () => {
        try {
            await fetch('../../api/patient.php?action=logout');
            window.location.href = 'login.html';
        } catch (e) { console.error(e); }
    });

    document.getElementById('btn-toggle-edit').addEventListener('click', toggleEditMode);
    document.getElementById('btn-save-profile').addEventListener('click', saveProfileData);

    // Lógica do Input de Arquivo (Igual Meal: Atualiza texto do nome do arquivo)
   const fileInput = document.getElementById('patient-photo-upload');
    const fileNameSpan = document.getElementById('file-name-text');
    const previewImg = document.getElementById('patient-preview-img');

    if(fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = this.files[0];
            
            if (file) {
                // 1. Atualiza o texto (Igual Meal)
                fileNameSpan.innerText = file.name;
                fileNameSpan.style.color = "#333";
                fileNameSpan.style.fontStyle = "normal";

                // 2. Atualiza a imagem grande (Preview)
                const reader = new FileReader();
                reader.onload = (evt) => {
                    previewImg.src = evt.target.result;
                }
                reader.readAsDataURL(file);

            } else {
                // Reseta texto
                fileNameSpan.innerText = "Nenhum arquivo selecionado";
                fileNameSpan.style.color = "#666";
                fileNameSpan.style.fontStyle = "italic";
            }
        });
    }
});

let isEditing = false;

async function loadPatientData() {
    try {
        const req = await fetch('../../api/patient.php?action=getLogged');
        const res = await req.json();

        if(res.status === 'success') {
            const user = res.data;

            document.getElementById('p-name').value = user.name || '';
            document.getElementById('p-email').value = user.email || '';
            document.getElementById('p-cpf').value = user.cpf || '';
            const previewImg = document.getElementById('patient-preview-img');
            
            if (user.photo && user.photo !== 'Sem foto') {
                previewImg.src = '../assets/images/' + user.photo;
            } else {
                previewImg.src = '';
            }
        } else {
            window.location.href = 'login.html';
        }
    } catch(e) {
        console.error("Erro ao carregar perfil:", e);
    }
}

function toggleEditMode() {
    isEditing = !isEditing;
    
    const btn = document.getElementById('btn-toggle-edit');
    const inputs = document.querySelectorAll('.form-control');
    const saveArea = document.getElementById('save-area');

    const photoBtnLabel = document.getElementById('photo-btn-label');
    const fileInput = document.getElementById('patient-photo-upload');

    if(isEditing) {
        // MODO EDIÇÃO
        btn.innerText = "Cancelar Edição";
        btn.classList.add('btn-cancel-mode'); 

        inputs.forEach(input => {
             input.disabled = false;
        });

        fileInput.disabled = false;
        if(photoBtnLabel) photoBtnLabel.classList.remove('disabled');

        saveArea.classList.remove('hidden');

    } else {
        // MODO LEITURA
        btn.innerText = "Editar Dados";
        btn.classList.remove('btn-cancel-mode');

        inputs.forEach(input => input.disabled = true);
        fileInput.disabled = true;
        if(photoBtnLabel) photoBtnLabel.classList.add('disabled');
        
        saveArea.classList.add('hidden');
        if(document.getElementById('file-name-text')) {
             document.getElementById('file-name-text').innerText = "Nenhum arquivo selecionado";
        }
        loadPatientData(); 
        
        // Limpa texto do arquivo
        document.getElementById('file-name-text').innerText = "Nenhum arquivo selecionado";
    }
}

async function saveProfileData() {
    const name = document.getElementById('p-name').value;
    const email = document.getElementById('p-email').value;
    const cpf = document.getElementById('p-cpf').value;   
    const photoInput = document.getElementById('patient-photo-upload');

    try {
        const formData = new FormData();
        formData.append('updateProfile', true);
        formData.append('name', name);
        formData.append('email', email);
        formData.append('cpf', cpf);

        if(photoInput.files[0]) {
            formData.append('photo', photoInput.files[0]);
        }

        const req = await fetch('../../api/patient.php?action=update', {
            method: 'POST',
            body: formData
        });

        const text = await req.text();
        let res;
        try {
            res = JSON.parse(text);
        } catch(e) {
            throw new Error("Resposta inválida: " + text);
        }

        if(res.status === 'success') {
            alert("Dados atualizados com sucesso!");
            await loadPatientData(); 
            toggleEditMode(); 
        } else {
            alert("Erro ao atualizar: " + res.message);
        }

    } catch(e) {
        console.error("Erro no JS:", e);
        alert("Erro ao salvar: " + e.message);
    }
}