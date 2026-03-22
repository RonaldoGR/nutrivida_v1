import { saveDiet  } from "../js/diet.js";
import { saveMeal } from "../js/meal.js";


const urlParams = new URLSearchParams(window.location.search);
const dietId = urlParams.get('id');


let fullData = null;
let isEditMode = false;

let modalMealItems = []; 
let allAlimentsList = [];

document.addEventListener('DOMContentLoaded', () => {
    if(!dietId) {
        alert("ID da dieta não fornecido.");
        window.location.href = 'nutritionist_dashboard.html';
        return;
    }
    

    loadDietDetails();

   document.getElementById('btn-toggle-edit').addEventListener('click', toggleEditMode);
   document.getElementById('btn-save-changes').addEventListener('click', saveAllChanges);

   // Modal Listeners para a edição
    document.getElementById('btn-close-modal').addEventListener('click', closeModal);
    document.getElementById('btn-modal-add-item').addEventListener('click', addAlimentToModalCart);
    document.getElementById('btn-modal-save').addEventListener('click', saveModalData);

    const modalPhotoInput = document.getElementById('modal-meal-photo');
    const fileNameDisplay = document.getElementById('file-name-text');

    if (modalPhotoInput) {
    modalPhotoInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            fileNameDisplay.innerText = this.files[0].name; // Mostra nome do arquivo
            fileNameDisplay.style.color = "#333"; // Deixa texto mais escuro
            fileNameDisplay.style.fontStyle = "normal";
        } else {
            fileNameDisplay.innerText = "Nenhum arquivo selecionado";
        }
    });
}

});


async function loadDietDetails() {
    try {
        const time = new Date().getTime(); 
        const req = await fetch(`../../api/diet.php?action=selectFullDetails&id=${dietId}&ts=${time}`);

        const res = await req.json();

        if(res.status === 'success') {
            fullData = res.data;
            if(isEditMode) renderEditMode();
            else renderViewMode();
        } else {
            alert(res.message);
        }
    } catch (e) {
        console.error(`Erro: ${e}`);
    }
}

function renderViewMode(){
    const diet = fullData.diet_info;
    const meals = fullData.meals;

    document.getElementById('patient-name').innerText = `Paciente: ${diet.patient_name}`;
    document.getElementById('diet-start').innerText = formatDate(diet.start_date);
    document.getElementById('diet-end').innerText = formatDate(diet.end_date);

    const badge = document.getElementById('diet-status');
    badge.innerText = diet.status;
    badge.className = `status-badge status-${diet.status}` // mudando estilo conforme status da dieta

    const container = document.getElementById('meals-container');
    container.innerHTML = "";

    if(meals.length === 0) {
        container.innerHTML = '<p class="food-empty">Nenhuma refeição cadastrada.</p>';
        return;
    }

    meals.forEach(meal => {
        let foodsHtml = '<ul class="food-list">';
        let totalCalories = 0;
        if(meal.aliments && meal.aliments.length > 0){
          meal.aliments.forEach(f => {
                const calories = parseFloat(f.calories) || 0;
                const baseQuantity = parseFloat(f.quantity) || 100; 
                
                const consumedQuantity = parseFloat(f.aliment_quantity) || 0; 
                const itemTotalCal = (calories / baseQuantity) * consumedQuantity;

                totalCalories += itemTotalCal;

                foodsHtml += `
                    <li>
                        <span>${f.description}</span>
                        <span><strong>${consumedQuantity}</strong> ${f.unit || 'g/ml'}</span>
                        
                        <span style="font-size:0.85rem; color:#666; margin-left:5px;">
                             (${Math.round(itemTotalCal)} kcal)
                        </span>
                    </li>`;
            });
        } else {
            foodsHtml += '<li class="food-empty">Sem alimentos</li>';
        }
        foodsHtml += '</ul>';

        const card = document.createElement('div');

        card.className = 'meal-card';
        card.innerHTML = `
            <div class="meal-card-header">
                    <div>
                        <strong>${meal.type}</strong>
                        <span style="font-size:0.85rem; color:#666; margin-left:10px;">
                            (${Math.round(totalCalories)} kcal)
                        </span>
                    </div>
                    <span>${meal.description || ''}</span>
                </div>
                <div class="meal-card-body">
                    <div class="meal-content-wrapper">
                        ${meal.photo ? `<img src="../assets/images/${meal.photo}" class="meal-image">` : ''}
                        <div class="meal-items-container">
                            ${foodsHtml}
                        </div>
                    </div>
                </div>
             `;
        container.appendChild(card);
    
    });
}

function toggleEditMode() {
    isEditMode = !isEditMode;
    const diet = fullData.diet_info;

    const readHeader = document.getElementById('read-header');
    const editHeader = document.getElementById('edit-header');
    const btnEdit = document.getElementById('btn-toggle-edit');
    const btnSave = document.getElementById('btn-save-changes');
    const mealsContainer = document.getElementById('meals-container');
    const addMealArea = document.getElementById('add-meal-area');

    if(isEditMode) {
       readHeader.classList.add('hidden');
        editHeader.classList.remove('hidden');
        btnEdit.innerText = "Cancelar Edição";
        btnEdit.classList.add('btn-cancel-mode');
        btnSave.classList.remove('hidden');
        addMealArea.classList.remove('hidden');;

        document.getElementById('edit-patient-name').innerText = document.getElementById('patient-name').innerText;
        document.getElementById('edit-start').value = diet.start_date;
        document.getElementById('edit-end').value = diet.end_date;
        document.getElementById('edit-status').value = diet.status;

        mealsContainer.innerHTML = ''
        renderEditMode();
    } else {
        // modo de leitura
        readHeader.classList.remove('hidden');
        editHeader.classList.add('hidden');
        btnEdit.innerText = "Editar Dieta";
        btnEdit.classList.remove('btn-cancel-mode');
        btnSave.classList.add('hidden');
        addMealArea.classList.add('hidden');
        document.getElementById('meal-modal-overlay').classList.add('hidden');
        renderViewMode();
    }

}


async function saveAllChanges() {
    const id = dietId;
    const start = document.getElementById('edit-start').value;
    const end = document.getElementById('edit-end').value;
    const status = document.getElementById('edit-status').value;
    const fkPatient = fullData.diet_info.fk_patient_id;

    try {
        const data = new FormData();
        data.append('id', id);
        data.append('startDate', start);
        data.append('endDate', end);
        data.append('status', status);
        data.append('fkPatientId', fkPatient);
        data.append('update', true);

        const photoInput = document.getElementById('edit-diet-photo');
        if (photoInput && photoInput.files[0]) {
            formData.append('photo', photoInput.files[0]);
        }



        const req = await fetch('../../api/diet.php?action=update', {
            method: 'POST',
            body: data
        });

        const res = await req.json();
        if(res.status === 'success') {
            alert("Dados da dieta foram atualizados com sucesso!");
            isEditMode = false;
            loadDietDetails(); // recarrega
            toggleEditMode(); // modo leitura novamente
        } else { 
            alert("Erro ao salvar dieta: " + res.message);
        }
    } catch(e) {
        console.error(e);
        alert("Erro de conexão");
    }
}

// só pra formatar a data melhor
function formatDate(dateString) {
    if(!dateString) return '-';
    const parts = dateString.split('-');
    return `${parts[2]}/${parts[1]}/${parts[0]}`;
}



function renderEditMode() {
    const mealsContainer = document.getElementById('meals-container');
    mealsContainer.innerHTML = ''; 
        
    fullData.meals.forEach(meal => {
        const editCard = document.createElement('div');
        editCard.className = 'meal-card meal-card-editing'; 
        
        editCard.innerHTML = `
            <div class="meal-card-header meal-header-editing">
                <strong>${meal.type}</strong>
                <button class="btn-edit-item" onclick="openMealEditModal(${meal.id_meal})">Editar Itens/Dados</button>
            </div>
            <div class="meal-card-body">
                <p class="meal-card-text">
                   <strong>Itens Atuais:</strong> ${meal.aliments ? meal.aliments.length : 0} alimentos.
                   <br>Clique em editar para alterar.
                </p>
            </div>
        `;
        mealsContainer.appendChild(editCard);
    });
}


// lógica do Modal de edição

window.openMealEditModal = async (idMeal) => {
    const meal = fullData.meals.find( m => m.id_meal == idMeal);
    if(!meal) requestAnimationFrame;

    if(allAlimentsList.length === 0) {
        try {
            const req = await fetch('../../api/aliment.php?action=listAll');
            const res = await req.json();
            if(res.status === 'success') {
                allAlimentsList = res.data.array || res.data;
            }
        } catch(e) {
            console.error("Erro ao carregar alimentos", e);
            return;
        }
    }

    document.getElementById('modal-title').innerText = "Editar Refeição";
    const select = document.getElementById('modal-aliment-select');
    select.innerHTML = '<option value="">Selecione...</option>';
    allAlimentsList.forEach(a => {
        select.innerHTML += `<option value="${a.id_aliment}">${a.description} (${a.calories} kcal)</option>`;
    });

    document.getElementById('modal-meal-id').value = meal.id_meal;
    document.getElementById('modal-meal-type').value = meal.type;
    document.getElementById('modal-meal-desc').value = meal.description || '';
    if(document.getElementById('modal-meal-photo')) {
        document.getElementById('modal-meal-photo').value = "";
    }

    if(document.getElementById('file-name-text')) {
        document.getElementById('file-name-text').innerText = "Nenhum arquivo selecionado";
    }

    modalMealItems = [];
    if(meal.aliments) {
        modalMealItems = meal.aliments.map(a => ({
            id_aliment: a.id_aliment,
            description: a.description,
            quantity: a.consumed_quantity || a.quantity
        }));
    }
    renderModalCart();
    document.getElementById('meal-modal-overlay').classList.remove('hidden');
};

function closeModal() {
    document.getElementById('meal-modal-overlay').classList.add('hidden');
}

function addAlimentToModalCart() {
    const select = document.getElementById('modal-aliment-select');
    const inputQty = document.getElementById('modal-aliment-qty');
    
    const id = select.value;
    const qty = inputQty.value;
    
    if(!id || !qty || qty <= 0) {
        alert("Selecione um alimento e quantidade válida.");
        return;
    }

    const exists = modalMealItems.find(item => item.id_aliment == id);

    if (exists) {
        alert("Este alimento já foi adicionado à lista. Remova-o para adicionar novamente ou altere a quantidade.");
        return; 
    }

    const alimentName = select.options[select.selectedIndex].text.split('(')[0]; 

    modalMealItems.push({
        id_aliment: id,
        description: alimentName,
        quantity: qty
    });

    select.value = "";
    inputQty.value = "";
    renderModalCart();
}

function renderModalCart() {
    const tbody = document.getElementById('modal-items-list');
    tbody.innerHTML = '';

    modalMealItems.forEach((item, index) => {
        tbody.innerHTML += `
            <tr>
                <td>${item.description}</td>
                <td>${item.quantity}</td>
                <td class="text-center">
                    <button type="button" onclick="removeModalItem(${index})" class="btn-remove-item">X</button>
                </td>
            </tr>
        `;
    });
}

window.removeModalItem = function(index) {
    modalMealItems.splice(index, 1);
    renderModalCart();
}

async function saveModalData() {
    const id = document.getElementById('modal-meal-id').value; 
    const type = document.getElementById('modal-meal-type').value;
    const desc = document.getElementById('modal-meal-desc').value;
    const fkDietId = fullData.diet_info.id_diet;
    if(!type) {
        alert("O tipo da refeição é obrigatório.");
        return;
    }

    try {
        const formData = new FormData();
        formData.append('type', type);
        formData.append('description', desc);
        formData.append('fkDietId', fkDietId);
        formData.append('aliments', JSON.stringify(modalMealItems));


        const photoInput = document.getElementById('modal-meal-photo');
        if (photoInput && photoInput.files[0]) {
            formData.append('photo', photoInput.files[0]);
        }

        let url = '../../api/meal.php?action=';

        if(id) {
            url += 'update';
            formData.append('update', id); 
        } else {
            url += 'register';
            formData.append('register', true); 
        }

        const req = await fetch(url, {
            method: 'POST',
            body: formData
        });

        const res = await req.json();

        if(res.status === 'success') {
            const msg = id ? "Refeição atualizada!" : "Nova refeição criada!";
            alert(msg);
            closeModal();
            loadDietDetails(); 
        }

    } catch(e) {
        console.error(e);
        alert("Erro ao salvar.");
    }
}


// adicionar nova refeição
window.openNewMealModal = async () => {
    if(allAlimentsList.length === 0) {
        try{
            const req = await fetch('../../api/aliment.php?action=listAll');
            const res = await req.json();
            console.log("Resposta da API Alimentos:", res);
            if(res.status === 'success') {
                allAlimentsList = res.data; 
            }
        } catch(e) {
            console.error(`Erro ao carregar alimentos: ${e}`);
            return;
        }
    }

    const select = document.getElementById('modal-aliment-select');
    select.innerHTML = '<option value="">Selecione...</option>';
    allAlimentsList.forEach((a) => {
        select.innerHTML += `<option value="${a.id_aliment}">${a.description} (${a.calories} kcal)</option>`;
    });

    
    document.getElementById('modal-meal-id').value = ""; // ID vazio indica cadastro novo
    document.getElementById('modal-meal-type').value = "";
    document.getElementById('modal-meal-desc').value = "";
    if(document.getElementById('modal-meal-photo')) {
        document.getElementById('modal-meal-photo').value = ""; 
    }
    
    if(document.getElementById('file-name-text')) {
        document.getElementById('file-name-text').innerText = "Nenhum arquivo selecionado";
    }

    modalMealItems = [];
    renderModalCart();

    document.getElementById('modal-title').innerText = "Nova Refeição";
    document.getElementById('meal-modal-overlay').classList.remove('hidden');

}