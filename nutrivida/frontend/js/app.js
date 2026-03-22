document.getElementById('btnSair').addEventListener('click', () => {
    fetch('path/to/controller/logout', { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // O JavaScript lê o 'redirect' que você mandou no JSON e obedece
            window.location.href = data.redirect; 
        }
    });
});



fetch('path/to/controller', {
    method: 'POST',
    body: JSON.stringify({ email: '...', password: '...' })
})
.then(response => response.json())
.then(data => {
    if (data.status === 'success') {
        // AQUI acontece o redirecionamento
        window.location.href = data.redirect;
    } else {
        alert(data.message); // "E-mail ou senha incorretos"
    }
});