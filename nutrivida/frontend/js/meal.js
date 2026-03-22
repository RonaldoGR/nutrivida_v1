let currentMealItems = [];
let alimentListGlobal = [];

export async function renderMealForm() {
    const contentDiv = document.getElementById('dynamicContent');

    try {
        const [ reqDiet, reqAliment ] = await Promise.all([
            fetch('../../api/diet.php?action=selectAll'),
            fetch('../../api/aliment.php?action=listAll')
        ]);


        const resDiet = await reqDiet.json();
        const resAliment = await reqAliment.json();

        let dietOptions = '<option value=""> Selecione a Dieta </option>';
       
        
        if(resDiet.status === 'success') {
            const diets = resDiet.data;

            diets.forEach(diet => {
                const start = diet.start_date ? diet.start_date.split('-').reverse().join('/') : "";
                dietOptions += `<option value="${diet.id_diet}">${diet.patient_name} - Início:${start} - [${diet.status}] `;
            });
        }

            let alimentOptions = '<options value=""> Selecione um alimento </option>';
        if (resAliment.status === 'success') {
                 const aliments = resAliment.data;
                 aliments.forEach(aliment => {
                    alimentOptions += `<option value="${aliment.id_aliment}"> ${aliment.description}${aliment.calories}kcal`; 
                    alimentListGlobal[aliment.id_aliment] = aliment.description;
                 });
        }

        contentDiv.innerHTML = `<div class="meal-header">
                    <h3>Nova Refeição</h3>
                    <button class="btn-back" onclick="loadContent('refeicoes')">Voltar</button>
                </div>
                <hr>
                
                <form id="form-meal" class="meal-form">
                    
                    <div class="form-grid-row">
                        <div class="form-group">
                            <label>Vincular à Dieta:</label>
                            <select id="meal-diet" class="form-control" required>
                                ${dietOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tipo (Ex: Almoço):</label>
                            <input type="text" id="meal-type" class="form-control" required placeholder="Ex: Café da Manhã">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Descrição:</label>
                        <textarea id="meal-desc" class="form-control" rows="2"></textarea>
                    </div>
                    
                  <div class="form-group">
                    <label>Foto (Opcional):</label>
                    <div class="upload-wrapper">
                        <label for="meal-photo" class="btn-file-select">Escolher Arquivo</label>
                        <span id="meal-file-name" class="file-name-display">Nenhum arquivo selecionado</span>
                        <input type="file" id="meal-photo" class="hidden-file-input">
                    </div>
                </div>

                    <div class="meal-cart-container">
                        <h4 class="cart-header">Adicionar Alimentos</h4>
                        
                        <div class="add-item-row">
                            <div class="item-select-wrapper">
                                <label>Alimento:</label>
                                <select id="temp-aliment-id" class="form-control">${alimentOptions}</select>
                            </div>
                            <div class="item-qty-wrapper">
                                <label>Qtd (g/ml):</label>
                                <input type="number" id="temp-aliment-qty" class="form-control" placeholder="0">
                            </div>
                            <button type="button" onclick="addAlimentItem()" class="btn-add-item">+</button>
                        </div>

                        <table class="meal-table">
                            <thead>
                                <tr>
                                    <th width="60%">Item</th>
                                    <th width="20%">Qtd</th>
                                    <th width="20%" style="text-align:center;">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="meal-items-list">
                                </tbody>
                        </table>
                    </div>

                    <button id="save-btn-meal" type="button" class="btn-save-meal">Salvar Refeição</button>
                </form>`
            
            currentMealItems = [];
            updateMealItemsTable();
            document.getElementById('save-btn-meal').addEventListener('click', saveMeal);

            const photoInput = document.getElementById('meal-photo');
            const fileNameSpan = document.getElementById('meal-file-name');

            if(photoInput) {
                photoInput.addEventListener('change', function() {
                    if (this.files && this.files.length > 0) {
                        fileNameSpan.innerText = this.files[0].name;
                        fileNameSpan.style.color = "#333";
                    } else {
                        fileNameSpan.innerText = "Nenhum arquivo selecionado";
                    }
                });
            }


    } catch (e) {
        console.error(e);
        contentDiv.innerHTML = `<p class="error-msg"> Erro ao carregar dados do formulário</p>`;
    }
        
}

export function addAlimentItem() {
    const select = document.getElementById('temp-aliment-id');
    const inputQty = document.getElementById('temp-aliment-qty');

    const idAliment = select.value;
    const qty = inputQty.value;

    if(!idAliment || !qty || qty <= 0) {
        alert("Selecione um alimento e uma quantidade válida");
        return;
    }

    currentMealItems.push({
        id_aliment: idAliment,
        quantity: qty,
        description: alimentListGlobal[idAliment] 
    });

    select.value = "";
    inputQty.value = "";
    updateMealItemsTable();
}


export function removeAlimentItem(index) {
    currentMealItems.splice(index, 1);
    updateMealItemsTable();
}

function updateMealItemsTable() {
    const tbody = document.getElementById('meal-items-list');
    tbody.innerHTML = "";

    if(currentMealItems.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="empty-cart-message">Nenhum alimento adicionado</td></tr>';
        return;
    }

    currentMealItems.forEach((item, index) => {
        tbody.innerHTML += `
            <tr>
                <td>${item.description}</td>
                <td>${item.quantity}</td>
                <td style="text-align:center;">
                    <button onclick="removeAlimentItem(${index})" class="btn-remove-item">X</button>
                </td>
            </tr>
        `;
    });
}


export async function saveMeal() {
    const fkDietId = document.getElementById('meal-diet').value;
    const type = document.getElementById('meal-type').value;
    const desc = document.getElementById('meal-desc').value;
    const photoInput = document.getElementById('meal-photo');
    const photo = photoInput ? photoInput.files[0] : null;

    if(!fkDietId || !type) {
        alert("Preencha a Dieta e o Tipo da Refeição");
        return;
    }

    try {
        const formData = new FormData();
        formData.append('type', type);
        formData.append('description', desc);
        formData.append('fkDietId', fkDietId);
        if(photo) formData.append('photo', photo);

        formData.append('aliments', JSON.stringify(currentMealItems));

        const saveBtn = document.getElementById('save-btn-meal');
        const editingId = saveBtn.dataset.id;
        let url;

        if(editingId) {
            url = '../../api/meal.php?action=update';
            formData.append('update', editingId);
        } else {
             url = '../../api/meal.php?action=register';
            formData.append('register', true);
        }

        const req = await fetch(url, {
            method: 'POST',
            body: formData
        });

        const res = await req.json();

        if(res.status === 'success') {
            if(editingId) {
                alert("Refeição atualizada com sucesso!");
            } else {
                alert("Refeição cadastrada com sucesso!");
            }
            if(window.loadContent) window.loadContent('refeicoes');
        } else {
            alert("Erro: " + res.message);
        }
    } catch (e) {
        console.error("Erro na requisição: ",e);
    }
}


export async function deleteMeal(id) {
    if(!confirm("Tem certeza que deseja excluir esta refeição?")) return;

    try {
        const data = new URLSearchParams();
        data.append('delete', id);

        const req = await fetch('../../api/meal.php?&action=delete', {
            method: 'POST',
            body: data
        });
        
        const res = await req.json();

        if(res.status === 'success') {
            alert("Refeição excluída com sucesso!");
            if(window.loadContent) window.loadContent('refeicoes');
        } else {
            alert("Erro ao excluir: " + res.message);
        }
    } catch (e) {
        console.error(`Erro ao excluir: ${e}`);
    }

}

export async function editMeal(id) {
    try {
        const req = await fetch(`../../api/meal.php?action=selectById&id=${id}`);
        const res = await req.json();
        if(res.status === 'success') {
            const meal = res.data[0];    
            await renderMealForm();
    
            document.querySelector('.meal-header h3').innerText = "Editar Refeição";
            document.getElementById('meal-diet').value = meal.fk_diet_id; // ID da dieta
            document.getElementById('meal-type').value = meal.type;
            document.getElementById('meal-desc').value = meal.description || '';
    
    
            currentMealItems = meal.aliments.map(item => ({
                id_aliment: item.id_aliment,
                description: item.description,
                quantity: item.aliment_quantity 
            }));

            updateMealItemsTable();

            const saveBtn = document.getElementById('save-btn-meal');
            saveBtn.innerText = "Atualizar"
            saveBtn.dataset.id = id;
        }

    } catch (e) {
        console.error(`Erro ao atualizar: ${e}`);
        alert("Erro ao carregar dados da refeição. Por favor, tente novamente.");
    }

}


export async function toggleMealDetails(id) {
    const parentRow = document.getElementById(`meal-row-${id}`);
    const detailRowId = `meal-detail-${id}`;
    const existingDetailRow = document.getElementById(detailRowId);

    //abrir e fechar
    if(existingDetailRow) {
        existingDetailRow.remove();
        return;
    }

    try {
        const loadingRow = document.createElement('tr');
        loadingRow.id = detailRowId;
        loadingRow.innerHTML = `<td colspan="4" class="loading-detail-cell"> Detalhes da Refeição</td>`
        parentRow.after(loadingRow);

        const req = await fetch(`../../api/meal.php?action=selectById&id=${id}`);
        const res = await req.json();

        if(res.status === 'success') {
            const meal = res.data[0];
            const aliments = meal.aliments || [];

            let alimentsHtml = "";
            if(aliments.length > 0) {
                alimentsHtml = `
                  <table class="details-table">
                        <thead>
                            <tr class="details-table-head-row"><th>Alimento</th><th>Qtd</th><th>Kcal</th></tr>
                        </thead>
                        <tbody>
                            ${aliments.map(a => `
                                <tr>
                                    <td>${a.description}</td>
                                    <td>${a.aliment_quantity}</td> <td>${a.calories} kcal</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                `;
            } else {
               alimentsHtml = '<p class="meal-details-empty">Nenhum alimento registrado nesta refeição.</p>';
            }
            
            loadingRow.innerHTML = `
             <td colspan="4" class="meal-details-container">
                    <div class="meal-details-flex">
                        ${meal.photo ? `<img src="../assets/images/${meal.photo}" class="meal-details-img">` : ''}
                        
                        <div class="meal-details-content">
                            <h5 class="meal-details-title">Itens da Refeição:</h5>
                            ${alimentsHtml}
                        </div>
                    </div>
                </td>
            `;

        }
    } catch (e) {
        console.error(e);
        const row = document.getElementById(detailRowId);
        if(row) row.remove();
        alert("Erro ao carregar os detalhes da refeição.");
    }

}



// tornando as funções globais no DOM - exportando para o HTML
window.renderMealForm = renderMealForm;
window.addAlimentItem = addAlimentItem;
window.removeAlimentItem = removeAlimentItem;
window.saveMeal= saveMeal;
window.deleteMeal = deleteMeal;
window.editMeal = editMeal;
window.toggleMealDetails = toggleMealDetails;