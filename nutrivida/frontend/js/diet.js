export async function saveDiet(startDate, endDate, status, fkPatientId, id) {

    try {
        const data = new URLSearchParams();
        data.append('startDate', startDate);
        data.append('endDate', endDate);
        data.append('status', status);
        data.append('fkPatientId', fkPatientId);
      

        let url;

        if(id) {
            url = '../../api/diet.php?action=update';
            data.append('id', id);
            data.append('update', true)
        } else {
            url = '../../api/diet.php?action=register';
            data.append('register', true);
        }

        const req = await fetch(url, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: data
        })

        const res = await req.json();

        if(res.status === 'success') {
            alert(id ? "Dieta atualizada!" : "Nova dieta registrada!");
            window.location.reload();
        } else {
            alert(`Erro: ${res.message}`);
        }

    } catch (e) {
        console.error(e);
    }

}


export async function editDiet(id) {
    try {
        const req = await fetch(`../../api/diet.php?action=select&id=${id}`);
        const res = await req.json();
        if(res.status === 'success') {
            const diet = res.data;
            
            await renderDietForm();

            const title = document.querySelector("#dynamicContent h3");
            if(title) title.innerText = "Editar Dieta";

            document.getElementById('diet-patient').value = diet.fk_patient_id;
            document.getElementById('diet-start').value = diet.start_date;
            document.getElementById('diet-end').value = diet.end_date;
            document.getElementById('diet-status').value = diet.status;
            
            const saveBtn = document.getElementById('save-btn-diet');
            saveBtn.dataset.id = id;
        }
    } catch (e) {
        console.error(`Erro ao editar a dieta ${e}`);
    }

}


export async function deleteDiet(id) {
    if (!confirm("Tem certeza que deseja excluir esta dieta ?")) return;

    try {
        const data = new URLSearchParams();
        data.append("delete", id);
        

        const req = await fetch('../../api/diet.php?action=delete', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: data
        });

        const res = await req.json();

        if(res.status === 'success') {
            alert("Dieta excluída com sucesso!");
            if (window.loadContent) window.loadContent("dietas");
        } else { 
            alert(`Erro ao excluir: ${res.message}`);
        }
    } catch (e) {
        console.error(`Erro na exclusão da dieta: ${e}`);
    }
}


export async function renderDietForm() {
    const contentDiv = document.getElementById('dynamicContent');
    try {
        const req = await fetch('../../api/patient.php?action=selectAll');
        const res =  await req.json();

        let optionsPatients = '<option value="">Selecione um paciente...</option>';

        if (res.status === 'success') {
            const patient = res.data;

            patient.forEach(p => {
              optionsPatients += ` <option value="${p.id_patient}"> ${p.name}</option>`
                
            });
        }

        const formDiet = `
            <div class="meal-header">
                <h3>Nova Dieta</h3>
                <button class="btn-back" onclick="loadContent('dietas')">Voltar</button>
            </div>
            <hr>

            <div class="meal-form">
                <div class="form-group">
                    <label>Paciente:</label>
                    <select id="new-diet-patient" class="form-control">
                        ${optionsPatients}
                    </select>
                </div>

                <div class="form-grid-row">
                    <div class="form-group">
                        <label>Data Início:</label>
                        <input type="date" id="new-diet-start" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Data Fim:</label>
                        <input type="date" id="new-diet-end" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status Inicial:</label>
                    <select id="new-diet-status" class="form-control">
                        <option value="ativa">Ativa</option>
                        <option value="finalizada">Finalizada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>

               <button id="save-btn-diet" class="btn-save-meal">Salvar Dieta</button>
                <p id="form-msg" class="text-center mt-2"></p>
            </div>
        `;

        contentDiv.innerHTML = formDiet;
        const btnSave = document.getElementById('save-btn-diet');
       if (btnSave) {
            btnSave.addEventListener('click', (e) => {
                e.preventDefault();

                const patientId = document.getElementById('new-diet-patient').value; 
                const startDate = document.getElementById('new-diet-start').value;
                const endDate = document.getElementById('new-diet-end').value;
                const status = document.getElementById('new-diet-status').value;
                
                const currentDietId = btnSave.dataset.id || null; 

                if(!patientId || !startDate) {
                    alert("Selecione um paciente e uma data de início.");
                    return;
                }

                saveDiet(startDate, endDate, status, patientId, currentDietId);
            });
        } else {
            console.error("Botão save-btn-diet não encontrado no DOM");
        }

    } catch (error) {
        console.error("Erro ao carregar pacientes: ", error);
    }
}

window.saveDiet = saveDiet;
window.editDiet = editDiet;
window.deleteDiet = deleteDiet;
window.renderDietForm = renderDietForm;