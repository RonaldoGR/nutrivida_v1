let allDiets = [];
let currentFilter = 'ativa';
       
       
       document.addEventListener('DOMContentLoaded', () => {
            loadProfile();
            loadDiets();
        });

        async function loadProfile() {
        try {
        const req = await fetch('../../api/patient.php?action=getLogged');
        const res = await req.json();

        if(res.status === 'success') {
            const user = res.data;
            
            const headerName = document.getElementById('headerName');
            if (headerName) {
                const firstName = user.name ? user.name.split(' ')[0] : 'Paciente';
                headerName.innerText = 'Olá, ' + firstName;
            }

            const pName = document.getElementById('pName');
            const pEmail = document.getElementById('pEmail');
            const pFoto = document.getElementById('pFoto');

            if(pName) pName.innerText = user.name || 'Nome não encontrado';
            if(pEmail) pEmail.innerText = user.email || 'Email não encontrado';
            
            if (user.photo && user.photo !== 'Sem foto') {
                pFoto.src = '../assets/images/' + user.photo;
            } 
            const loading = document.getElementById('profileLoading');
            const data = document.getElementById('profileData');

            if (loading) {
                loading.classList.add('hidden');
                loading.style.display = 'none';
            }
            
            if (data) {
                data.classList.remove('hidden');
                data.style.display = 'flex';
            }

        } else {
            console.warn("Sessão inválida");
            window.location.href = 'login.html';
        }
    } catch(e) {
        console.error("Erro ao carregar perfil:", e);
    }
}


 async function loadDiets(statusFilter = 'ativa', buttonElement = null) {
    const dietListDiv = document.getElementById('dietList');
    
       if (allDiets.length === 0 && statusFilter !== 'reload') { 
        dietListDiv.innerHTML = '<p>Buscando dietas...</p>';
        try {
            const req = await fetch('../../api/diet.php?action=selectByPatient'); 
            const res = await req.json();

            if(res.status === 'success' && res.data.length > 0) {
                allDiets = res.data; 
            } else {
                dietListDiv.innerHTML = '<p>Nenhuma dieta encontrada.</p>';
                return; 
            }
        } catch(e) {
            console.error("Erro ao carregar dietas:", e);
            dietListDiv.innerHTML = '<p>Erro ao carregar dietas.</p>';
            return;
        }
    }

    currentFilter = statusFilter;
    let filteredDiets = allDiets;

    if (statusFilter !== 'todas') {
        filteredDiets = allDiets.filter(diet => diet.status === statusFilter);
    }
    
    
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.classList.remove('status-filter-active'); 
    });
    if (buttonElement) {
        buttonElement.classList.add('status-filter-active'); 
    } else {

        const initialButton = document.querySelector(`.btn-view[data-status="${statusFilter}"]`);
        if (initialButton) initialButton.classList.add('status-filter-active');
    }
    
    dietListDiv.innerHTML = ''; 

    if (filteredDiets.length === 0) {
        dietListDiv.innerHTML = `<p>Nenhuma dieta ${currentFilter !== 'todas' ? currentFilter : ''} encontrada.</p>`;
        return;
    }
    
    filteredDiets.forEach(diet => {
        const dietContainer = document.createElement('div');
        dietContainer.className = 'details-container'; 
        
        let statusClass = diet.status === 'ativa' ? 'status-active' : (diet.status === 'finalizada' ? 'status-finalized' : 'status-default');
        
        let headerHtml = `
            <div class="header-info">
                <div class="header-data">
                    <div id="read-header">
                        <h2>Dieta ${diet.status === 'ativa' ? 'Atual' : diet.status}</h2>
                        <p>Início: <span id="diet-start">${diet.start_date}</span> | Fim: <span id="diet-end">${diet.end_date}</span></p>
                        <div class="status-wrapper">
                            <span class="status-badge ${statusClass}">${diet.status}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        let separator = '<hr class="separator">';
        let mealsTitle = '<h3 class="section-title">Refeições Detalhadas</h3>';
        let mealsHtml = '<div id="meals-container">'; 
        
        if(diet.meals && diet.meals.length > 0) {
             
             diet.meals.forEach(meal => {
                 let foodsHtml = '<ul class="food-list">';
                 let totalCalories = 0;
                 
                 // Itera sobre os Alimentos
               if(meal.aliments && meal.aliments.length > 0) {
                     meal.aliments.forEach(aliment => {
                         
                         const calories = parseFloat(aliment.calories) || 0;
                         const baseQuantity = parseFloat(aliment.quantity) || 100; 

                         const consumedQuantity = parseFloat(aliment.aliment_quantity) || 0;
                         
                         const itemTotalCal = (calories / baseQuantity) * consumedQuantity;
                         
                         totalCalories += itemTotalCal;

                         foodsHtml += `
                             <li>
                                 <span>${aliment.description}</span>
                                 <span class="quantity-text">${consumedQuantity} ${aliment.unit || 'g/ml'}</span>
                                 </li>
                         `;
                     });
                 } else {
                     foodsHtml += `<li class="food-empty">Sem alimentos cadastrados.</li>`;
                 }
                 foodsHtml += '</ul>';
                 
                 // Card da Refeição
                 mealsHtml += `
                    <div class="meal-card">
                        <div class="meal-card-header">
                            <div>
                                <strong>${meal.type}</strong>
                                <span style="font-size:0.85rem; color:#666; margin-left:10px;">
                                    (${Math.round(totalCalories)} kcal)
                                </span>
                            </div>
                            <span class="meal-description-text">${meal.description || ''}</span>
                        </div>
                        <div class="meal-card-body">
                            <div class="meal-content-wrapper">
                                ${meal.photo ? `<img src="../assets/images/${meal.photo}" class="meal-image">` : ''} 
                                <div class="meal-items-container">
                                    ${foodsHtml}
                                </div>
                            </div>
                            </div>
                    </div>
                 `;
             });
             
        } else {
            mealsHtml += '<p class="food-empty">Nenhuma refeição cadastrada para esta dieta.</p>';
        }
        mealsHtml += '</div>';

        dietContainer.innerHTML = headerHtml + separator + mealsTitle + mealsHtml;
        
        dietListDiv.appendChild(dietContainer);
        
        if (filteredDiets.length > 1) {
            dietListDiv.appendChild(document.createElement('br'));
            dietListDiv.appendChild(document.createElement('hr')); 
            dietListDiv.appendChild(document.createElement('br'));
        }
    });
}

async function logout() {
    await fetch('../../api/patient.php?action=logout');
    window.location.href = 'login.html';
}

async function loadAndFilterDiets() {
    try {
        const req = await fetch('../../api/diet.php?action=selectByPatient'); 
        const res = await req.json();

        if(res.status === 'success' && res.data.length > 0) {
            allDiets = res.data; 
            
            filterDiets('ativa'); 
            
        } else {
            document.getElementById('dietList').innerHTML = '<p>Nenhuma dieta encontrada.</p>';
        }

    } catch(e) {
        console.error(e);
        document.getElementById('dietList').innerHTML = '<p>Erro ao carregar dietas.</p>';
    }
}


window.filterDiets = (status, buttonElement = null) => {
    
    currentFilter = status;
    const dietListDiv = document.getElementById('dietList');
    
    let filteredDiets = allDiets;

    if (status !== 'todas') {
        filteredDiets = allDiets.filter(diet => diet.status === status);
    }
    document.querySelectorAll('.btn-filter').forEach(btn => {
        btn.classList.remove('status-active');
    });
    if (buttonElement) {
        buttonElement.classList.add('status-active');
    } else {
        document.querySelector(`.btn-filter[data-status="${status}"]`).classList.add('status-active');
    }
    renderDietList(filteredDiets, dietListDiv);
};


window.filterDiets = loadDiets;