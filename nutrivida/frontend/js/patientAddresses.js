document.addEventListener('DOMContentLoaded', () => {
    loadAddresses();

    document.getElementById('btnLogout').addEventListener('click', async () => {
        try { await fetch('../../api/patient.php?action=logout'); } catch (e) {}
        window.location.href = 'login.html';
    });

    document.getElementById('btn-new-address').addEventListener('click', () => showForm(null));

    document.getElementById('btn-cancel-address').addEventListener('click', () => {
        document.getElementById('address-form-section').classList.add('hidden');
        document.getElementById('address-list-section').classList.remove('hidden');
    });

    document.getElementById('address-form').addEventListener('submit', saveAddress);
});

let currentAddressList = [];

async function loadAddresses() {
    try {
        const req = await fetch('../../api/patient.php?action=getLogged');
        const res = await req.json();

        if(res.status === 'success') {
            const user = res.data;
            const container = document.getElementById('addresses-container');
            container.innerHTML = '';

            if(!user.address || user.address.length === 0) {
                container.innerHTML = '<p class="text-center text-muted">Nenhum endereço cadastrado.</p>';
                currentAddressList = [];
                return;
            }

            currentAddressList = Array.isArray(user.address) ? user.address : [user.address];

            currentAddressList.forEach((addr, index) => {
                if(!addr) return; 
                const card = document.createElement('div');
                card.className = 'address-card';
                card.innerHTML = `
                    <div class="address-info">
                        <h4>Endereço Principal</h4>
                        <p>${addr.street || ''}, ${addr.number || ''} - ${addr.neighborhood || ''}</p>
                        <p>${addr.city || ''}/${addr.state || ''} - CEP: ${addr.zip_code || addr.cep || ''}</p>
                    </div>
                    <div>
                        <button class="btn-view" onclick="editAddress(${index})">Editar</button>
                    </div>
                `;
                container.appendChild(card);
            });
        } else {
            window.location.href = 'login.html';
        }
    } catch(e) {
        console.error(e);
        document.getElementById('addresses-container').innerHTML = '<p class="text-center text-muted">Erro ao carregar endereços.</p>';
    }
}

window.editAddress = function(index) {
    showForm(currentAddressList[index]);
};

function showForm(addr) {
    const title = document.getElementById('form-title');
    const form = document.getElementById('address-form');
    form.reset();

    if(addr) {
        title.innerText = "Editar Endereço";
        document.getElementById('addr-cep').value = addr.zip_code || addr.cep || '';
        document.getElementById('addr-street').value = addr.street || '';
        document.getElementById('addr-number').value = addr.number || '';
        document.getElementById('addr-neighborhood').value = addr.neighborhood || '';
        document.getElementById('addr-city').value = addr.city || '';
        document.getElementById('addr-state').value = addr.state || '';
        document.getElementById('addr-country').value = addr.country || 'Brasil';
    } else {
        title.innerText = "Novo Endereço";
    }

    document.getElementById('address-list-section').classList.add('hidden');
    document.getElementById('address-form-section').classList.remove('hidden');
}

async function saveAddress(e) {
    e.preventDefault();

    const idAddress = document.getElementById('addr-id').value;
    
    const cep = document.getElementById('addr-cep').value;
    const street = document.getElementById('addr-street').value;
    const number = document.getElementById('addr-number').value;
    const neighborhood = document.getElementById('addr-neighborhood').value;
    const city = document.getElementById('addr-city').value;
    const state = document.getElementById('addr-state').value;
    const country = document.getElementById('addr-country').value;

    try {
        const formData = new FormData();
        
         formData.append('saveAddress', true); 
        
        if(idAddress) {
            formData.append('id_address', idAddress);
        }

        formData.append('cep', cep);
        formData.append('street', street);
        formData.append('number', number);
        formData.append('neighborhood', neighborhood);
        formData.append('city', city);
        formData.append('state', state);
        formData.append('country', country);

        const req = await fetch('../../api/patient.php?action=saveAddress', {
            method: 'POST',
            body: formData
        });

        const text = await req.text();
        let res;
        try { 
            res = JSON.parse(text); 
        } catch(err) { 
            console.error("Resposta inválida:", text);
            throw new Error("Erro no servidor"); 
        }

        if(res.status === 'success') {
            alert(res.message || "Endereço salvo!");
            document.getElementById('address-form-section').classList.add('hidden');
            document.getElementById('address-list-section').classList.remove('hidden');
            loadAddresses(); 
        } else {
            alert("Erro ao salvar: " + res.message);
        }
    } catch(err) {
        console.error(err);
        alert("Erro de conexão.");
    }
}