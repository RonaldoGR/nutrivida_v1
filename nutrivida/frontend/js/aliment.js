

 async function editAliment(id) {
    try {
        const req = await fetch(`../../api/aliment.php?action=select&id=${id}`);
        const res = await req.json();
        
        if(res.status === 'success') {
            const item = res.data; 

            renderAlimentForm();

           const titleElement = document.querySelector('.meal-header h3');
            if(titleElement) titleElement.innerText = "Editar Alimento";

            document.getElementById('aliment-description').value = item.description;
            document.getElementById('aliment-quantity').value = item.quantity;
            document.getElementById('aliment-calories').value = item.calories;

            // guarda o ID no botão para usar na hora de salvar
            // cria um atributo data-id no botão de salvar
            const saveBtn = document.getElementById('save-btn-aliment');
            saveBtn.dataset.id = id; 
            saveBtn.innerText = "Atualizar Alimento";
        }
    } catch(e) {
        console.error(e);
    }
}




     async function deleteAliment(id) {

        if(!confirm("Tem certeza que deseja excluir este alimento?")) return;
        try {
                const data = new URLSearchParams();
                data.append('deleteAliment', true);
                data.append('id', id);

                const req = await fetch(`../../api/aliment.php?action=delete&id=${id}`, {
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                method: 'POST',
                body: data
            });
            
            const res = await req.json();

            if(res.status === 'success') {
                alert('Alimento excluído!');
                loadContent('alimentos');
            } else {
                alert('Erro ao excluir.');
            }
        } catch(e) {
            console.error(e);
        }
    }



     async function saveAliment(description, quantity, calories, id) {
            try {

                const data = new URLSearchParams();
                data.append('description', description);
                data.append('quantity', quantity);
                data.append('calories', calories);
                
                let url;

                if(id) {
                    url = '../../api/aliment.php?action=update';
                    data.append('id', id);
                    data.append('updateAliment', true);
                } else { 
                    url = '../../api/aliment.php?action=register';
                    data.append('registerAliment', true);
                }
                
                const req = await fetch(url, {
                     method: 'POST',
                     headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                     body: data
                });

                const res = await req.json();
                if(res.status === 'success') {
                    alert(id ? "Alimento atualizado!" : "Novo alimento registrado!");
                    loadContent('alimentos');
                }
            } catch (e) {
                console.error(e);
            }

        }



      function renderAlimentForm() {
            const contentDiv = document.getElementById('dynamicContent');
           let formAliment = `
                                <div class="meal-header">
                                    <h3>Novo Alimento</h3>
                                    <button class="btn-back" onclick="loadContent('alimentos')">Voltar</button>
                                </div>
                                <hr>

                                <form id="form-aliment" class="meal-form">
                                    
                                    <div class="form-group">
                                        <label>Descrição:</label>
                                        <input id="aliment-description" type="text" class="form-control" placeholder="Ex: Arroz Branco Cozido" required>
                                    </div>

                                    <div class="form-grid-row">
                                        <div class="form-group">
                                            <label>Quantidade Padrão (g/ml):</label>
                                            <input id="aliment-quantity" type="number" class="form-control" placeholder="Ex: 100" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Calorias (kcal):</label>
                                            <input id="aliment-calories" type="number" class="form-control" placeholder="Ex: 130" required>
                                        </div>
                                    </div>

                                    <button id="save-btn-aliment" type="button" class="btn-save-meal">Salvar Alimento</button>
                                </form>
                            `;
                                    
                            
             contentDiv.innerHTML = formAliment;      
                      

            const saveBtn = document.getElementById("save-btn-aliment");
            saveBtn.dataset.id = "";


            saveBtn.addEventListener('click', (event) => {
                event.preventDefault();
                let description = document.getElementById("aliment-description").value;
                let quantity = document.getElementById("aliment-quantity").value;
                let calories = document.getElementById("aliment-calories").value;
                let currentId = saveBtn.dataset.id;
                saveAliment(description, quantity, calories, currentId);

            });


        }



window.renderAlimentForm = renderAlimentForm;
window.editAliment = editAliment;
window.deleteAliment = deleteAliment;