document.addEventListener('DOMContentLoaded', () => {
    checkSession();
    loadPatients();
});


let currentPatinetId = null;

async function checkSession() {
    const pathArray = window.location.pathname.split('/');
    const projectRoot = '/' + pathArray[1];
    
    const url = window.location.origin + projectRoot + '/api/nutritionist.php?action=getLogged';

    try {
        const req = await fetch(url);
        const res = await req.json();
        
        // Se não estiver logado ou não for nutri, chuta pro login
        if(res.status !== 'success') {
            window.location.href = 'login.html';
        }
    } catch (e) {
        console.error("Erro de sessão:", e);
        window.location.href = 'login.html';
    }
}


document.getElementById('btnLogout').addEventListener('click', async () => {
    const pathArray = window.location.pathname.split('/');
    const projectRoot = '/' + pathArray[1];
    await fetch(window.location.origin + projectRoot + '/api/nutritionist.php?action=logout');
    window.location.href = 'login.html';
});

async function loadPatients() {
    const tbody = document.getElementById('patientsList');
    tbody.innerHTML = '<tr><td colspan="4" class="msg-loading">Carregando...</td></tr>';
    document.getElementById('searchInput').value = ''; // Limpa o campo visual

    try {
        const pathArray = window.location.pathname.split('/');
        const projectRoot = '/' + pathArray[1];
        
        const url = window.location.origin + projectRoot + '/api/patient.php?action=selectAll';
        
        const req = await fetch(url);
        const res = await req.json();

        renderTable(res);

    } catch (e) {
        console.error(e);
        tbody.innerHTML = '<tr><td colspan="4" class="msg-error">Erro ao carregar lista de pacientes.</td></tr>';
    }
}


async function searchPatients() {
    const term = document.getElementById('searchInput').value;
    
    if(!term.trim()) {
        loadPatients(); 
        return;
    }

    const tbody = document.getElementById('patientsList');
   tbody.innerHTML = '<tr><td colspan="4" class="msg-loading">Buscando...</td></tr>';

    try {
        const pathArray = window.location.pathname.split('/');
        const projectRoot = '/' + pathArray[1];
        
        const url = window.location.origin + projectRoot + '/api/patient.php?action=searchByName';

        const req = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                searchByName: true, 
                name: term 
            })
        });
        
        const res = await req.json();
        renderTable(res);

    } catch (e) {
        console.error(e);
       tbody.innerHTML = '<tr><td colspan="4" class="msg-error">Erro na busca.</td></tr>';
    }
}


function renderTable(res) {
    const tbody = document.getElementById('patientsList');
    tbody.innerHTML = ''; 

    if (res.status === 'success' && res.data && res.data.length > 0) {
        res.data.forEach(patient => {
            const tr = document.createElement('tr');
            

            tr.innerHTML = `
                <td>${patient.name}</td>
                <td>${patient.email}</td>
                <td>${patient.cpf}</td>
                <td class = btn-container>
                    <button id="view-details" class="action-btn btn-view" onclick="viewDetails(${patient.id_patient})">Ver Perfil</button>
                    <button class="action-btn btn-delete" onclick="deletePatient(${patient.id_patient})">Excluir</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } else {
       tbody.innerHTML = '<tr><td colspan="4" class="msg-empty">Nenhum paciente encontrado.</td></tr>';
    }
}



function formatDate(dateString) {
    if (!dateString) return '-';
    const parts = dateString.split('-');
    return `${parts[2]}/${parts[1]}/${parts[0]}`;
}



async function viewDetails(id) {
    
    try {
        
        const detailsPanel = document.getElementById('patient_details');

        if (detailsPanel.style.display !== "none" && currentPatinetId === id) {
            detailsPanel.style.display = "none";
            currentPatinetId = null;
            return;
        } 

        detailsPanel.style.display = "block";
        currentPatinetId = id;
        
        const pathArray = window.location.pathname.split('/');
        const projectRoot = '/' + pathArray[1];

        const urlPatient = window.location.origin + projectRoot + '/api/patient.php?action=select' + `&id=${id}`;
        const reqPatient = await fetch(urlPatient);
        const resPatient = await reqPatient.json();
        const user = resPatient.data;

        const imgElement = document.getElementById('pFoto');
        document.getElementById('pName').innerText = user.name;
        document.getElementById('pEmail').innerText = user.email;
        document.getElementById('pCpf').innerText = user.cpf;

        if (user.photo && user.photo !== 'Sem foto') {
            imgElement.src = '../assets/images/' + user.photo;
        } else {
             imgElement.src = ''; 
             imgElement.alt = 'Sem foto';
        }
        
        document.getElementById('pName').innerText = user.name;
        document.getElementById('pEmail').innerText = user.email;
        document.getElementById('pCpf').innerText = user.cpf;


                    
        const addressContainer = document.getElementById('pAddress');
        addressContainer.innerHTML = '';


        if(user.address && user.address.length > 0) {
            const addr = user.address;
            addr.forEach((ad, index) => {
                const p = document.createElement('p');
                p.innerHTML = `<strong>Endereço ${index + 1}</strong><br> 
                                    ${ad.street}, ${ad.number} - ${ad.neighborhood}, ${ad.city}/${ad.state}`;
                            addressContainer.appendChild(p);
                 });
        } else {
             document.getElementById('pAddress').innerText = "Endereço não cadastrado.";
         }

         const dietListDiv = document.getElementById('dietList');
        dietListDiv.innerHTML = '<p class="loading-text">Buscando dietas...</p>';

        const urlDiets = window.location.origin + projectRoot + '/api/diet.php?action=selectByPatientId&id=' + id;
        
        const reqDiet = await fetch(urlDiets);
        const resDiet = await reqDiet.json();

        dietListDiv.innerHTML = ''; 

        if (resDiet.status === 'success' && resDiet.data.length > 0) {
            
            resDiet.data.forEach(diet => {
                const dietCard = document.createElement('div');
                dietCard.className = 'sidebar-diet-card';
                
                let statusClass = '';
                if(diet.status === 'ativa') statusClass = 'status-active';
                else if(diet.status === 'finalizada') statusClass = 'status-finalized';
                else statusClass = 'status-cancelled';

                dietCard.innerHTML = `
                    <div class="sidebar-diet-info">
                        <strong>${formatDate(diet.start_date)} até ${formatDate(diet.end_date)}</strong>
                        <span class="status-badge-small ${statusClass}">${diet.status}</span>
                    </div>
                    <button class="btn-view-diet-sidebar" onclick="goToDietDetails(${diet.id_diet})">
                        Ver Dieta
                    </button>
                `;
                dietListDiv.appendChild(dietCard);
            });

        } else {
                dietListDiv.innerHTML = '<p class="msg-empty">Nenhuma dieta cadastrada para este paciente.</p>';
        }
    } catch (e) {
        console.error(e);
       
    }
}

function deletePatient(id) {
    if(confirm('Tem certeza que deseja excluir este paciente?')) {
        console.log('Deletando ID:', id);
    }
}

function goToDietDetails(idDiet) {
    window.location.href = `diet_details.html?id=${idDiet}`;
}