   import './diet.js';
   import './aliment.js';
   import './meal.js';

   let allNutritionistDiets = [];
      
      document.addEventListener('DOMContentLoaded', loadProfile);

        async function loadProfile() {
            const req = await fetch('../../api/nutritionist.php?action=getLogged');
            const res = await req.json();

            if(res.status === 'success') {
                const user = res.data;
                document.getElementById('headerName').innerText = 'Dr(a). ' + user.name;
                document.getElementById('nutriEmail').innerText = user.email;
                document.getElementById('nutriCrn').innerText = 'CRN: ' + user.crn;

                const imgElement = document.getElementById('nutriFoto');
    
                if (user.photo) {
                    imgElement.src = '../assets/images/' + user.photo;
                }


            } else {
                window.location.href = 'login.html';
            }
        }

        async function logout() {
            await fetch('../../api/nutritionist.php?action=logout');
            window.location.href = 'login.html';
        }

       async function loadContent(type) {
            const contentDiv = document.getElementById('dynamicContent');
            
            if(type === 'alimentos') {
              contentDiv.innerHTML = `
                        <div class="flex-header">
                            <h3>Lista de Alimentos</h3>
                            <button class="btn-new-aliment" onclick="renderAlimentForm()">+ Novo</button>
                        </div>
                        <hr>
                        <div>
                            <table class="patients-table table-margin-top">
                            <thead>
                                <tr>
                                    <th>Descrição</th>
                                    <th>Qtd</th>
                                    <th>Kcal</th>
                                    <th>Ações</th> </tr>
                            </thead>
                            <tbody id="aliments-list"> 
                            </tbody>
                        </table>
                        </div>
                    `;
                try {
                    const req = await fetch('../../api/aliment.php?action=listAll');
                    const res = await req.json();
                    console.log(res)
                     
                    if(res.status === 'success') {
                        const aliment = res.data;
                        const alimentList = document.getElementById('aliments-list');
                        aliment.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `<td> ${item.description} </td>
                                             <td> ${item.quantity}g/ml </td>
                                             <td> ${item.calories}kcal</td> 
                                             <td>
                                                <button onclick="editAliment(${item.id_aliment})" class="btn-edit">Editar</button>
                                                <button onclick="deleteAliment(${item.id_aliment})" class="btn-delete">Excluir</button>
                                            </td>  
                                            `
                            alimentList.appendChild(row);
                            
                        });

                    }
                } catch (e) {
                    console.error(e);
                }
                
            } else if(type === 'refeicoes') {
               contentDiv.innerHTML = `
                                    <div class="diet-header">
                                        <h3>Gerenciar Refeições</h3>
                                        <button class="btn-new-diet" onclick="renderMealForm()">+ Nova Refeição</button>
                                    </div>
                                    <hr>
                                    
                                    <table class="patients-table table-margin-top">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Paciente</th>
                                                <th>Descrição</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="meals-list-container">
                                        </tbody>
                                    </table>
                                `;

                try {
                    const req = await fetch('../../api/meal.php?action=listAll');
                    const res = await req.json();

                    const tbodyM = document.getElementById('meals-list-container');
                    tbodyM.innerHTML = "";

                    if(res.status === 'success' && res.data.length > 0) {
                        const meals = res.data;
                        meals.forEach((m) => {
                            const rowM = document.createElement('tr');
                            rowM.id = `meal-row-${m.id_meal}`;    
                            rowM.innerHTML = `
                                <td><strong>${m.type}</strong></td>
                                <td>${m.patient_name}</td>
                                <td>${m.description || '-'}</td>
                                <td>
                                    <button onclick="toggleMealDetails(${m.id_meal})" class="btn-view">Ver</button>
                                    <button onclick="editMeal(${m.id_meal})" class="btn-edit">Editar</button>
                                    <button onclick="deleteMeal(${m.id_meal})" class="btn-delete">Excluir</button>
                                </td>
                            `;
                            tbodyM.appendChild(rowM);
                        });

                    } else {
                       tbodyM.innerHTML = '<tr><td colspan="4" class="table-empty-msg">Nenhuma refeição cadastrada.</td></tr>';
                    }
                } catch (e) {
                    console.error("Erro ao listar refeiçoes", e)
                     
                }


            } else if (type === 'dietas') {
                  contentDiv.innerHTML = `
                                        <div class="diet-header">
                                            <h3>Gerenciar Dietas</h3>
                                            <button class="btn-new-diet" onclick="renderDietForm()">+ Nova Dieta</button>
                                        </div>
                                        
                                        <div class="filter-buttons" style="margin-bottom: 15px;">
                                            <button class="btn-view status-filter-active" data-status="ativa" onclick="filterNutritionistDiets('ativa', this)">Ativas</button>
                                            <button class="btn-view" data-status="todas" onclick="filterNutritionistDiets('todas', this)">Todas Dietas</button>
                                            <button class="btn-view" data-status="finalizada" onclick="filterNutritionistDiets('finalizada', this)">Finalizadas</button>
                                            <button class="btn-view" data-status="cancelada" onclick="filterNutritionistDiets('cancelada', this)">Canceladas</button>
                                        </div>
                                        
                                        <hr>

                                        <div class="diet-table-container">
                                            <table class="diet-table">
                                                <thead>
                                                    <tr>
                                                        <th>Paciente</th>
                                                        <th>Início</th>
                                                        <th>Fim</th>
                                                        <th>Status</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="diets-list">
                                                    <tr><td colspan="5" class="table-empty-msg">Carregando...</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    `;

                try {
                    const reqD = await fetch('../../api/diet.php?action=selectAll');
                    const resD = await reqD.json();

                    if(resD.status === 'success') {
                        allNutritionistDiets = resD.data; 
                        filterNutritionistDiets('ativa'); 
                    } else {
                        document.getElementById('diets-list').innerHTML = '<tr><td colspan="5" class="table-empty-msg">Nenhuma dieta encontrada.</td></tr>';
                    }
                } catch (e) {
                    console.error("Erro ao listar as dietas:", e);
                    document.getElementById('diets-list').innerHTML = '<tr><td colspan="5" class="error-text">Erro ao buscar dietas.</td></tr>';
                }

            }
        }
    

window.filterNutritionistDiets = (status, buttonElement = null) => {
        document.querySelectorAll('.filter-buttons .btn-view').forEach(btn => {
            btn.classList.remove('status-filter-active');
        });

        if (buttonElement) {
            buttonElement.classList.add('status-filter-active');
        } else {
            const btn = document.querySelector(`.filter-buttons .btn-view[data-status="${status}"]`);
            if(btn) btn.classList.add('status-filter-active');
        }
        let filteredDiets = allNutritionistDiets;
        if (status !== 'todas') {
            filteredDiets = allNutritionistDiets.filter(diet => diet.status === status);
        }

        renderNutritionistDietTable(filteredDiets);
    }

function renderNutritionistDietTable(diets) {
    const tbodyD = document.getElementById('diets-list');
    tbodyD.innerHTML = '';

    if (diets.length === 0) {
        tbodyD.innerHTML = '<tr><td colspan="5" class="table-empty-msg">Nenhuma dieta encontrada com este status.</td></tr>';
        return;
    }

    const formateDate = (dateString) => {
        if(!dateString) return '-';
        const parts = dateString.split('-');
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }

    diets.forEach(item => {
        const rowDiet = document.createElement('tr');
        const start = formateDate(item.start_date);
        const end =  formateDate(item.end_date);

        let statusClass = '';
        if(item.status === 'ativa') statusClass = 'status-active';
        else if(item.status === 'finalizada') statusClass = 'status-finalized';
        else statusClass = 'status-cancelled';

        rowDiet.innerHTML = `
            <td>${item.patient_name}</td> 
            <td>${start}</td>
            <td>${end}</td>
            <td><span class="status-badge ${statusClass}">${item.status}</span></td>
            <td>
            <button onclick="window.location.href='diet_details.html?id=${item.id_diet}'" class="btn-view mr-sm">Ver</button>
            <button onclick="deleteDiet(${item.id_diet})" class="btn-delete">Excluir</button>
            </td>
        `;
        tbodyD.appendChild(rowDiet);
    });
}




window.loadContent = loadContent;
window.logout = logout;