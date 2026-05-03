#  NutriVida v1

> Protótipo fullstack desenvolvido nas disciplinas de **Desenvolvimento Backend I** e **Desenvolvimento Frontend I** do 3º semestre do Curso Superior de Tecnologia em Sistemas para Internet — **IFSUL**.

Este projeto é a **primeira versão** do sistema NutriVida, construída sem nenhum framework — HTML, CSS, JavaScript e PHP puros. O objetivo principal foi aprender os fundamentos do desenvolvimento web: como as requisições HTTP funcionam, como estruturar um backend em camadas, como separar responsabilidades e como construir uma API consumida por um frontend, tudo na base, sem abstrações prontas.

---

## Sumário

- [Sobre o Projeto](#-sobre-o-projeto)
- [Arquitetura do Sistema](#-arquitetura-do-sistema)
- [Módulos](#-módulos)
  - [Frontend](#-frontend)
  - [Backend](#-backend)
  - [API](#-api)
- [Tecnologias Utilizadas](#-tecnologias-utilizadas)
- [Como Rodar o Projeto](#-como-rodar-o-projeto)
- [Variáveis de Ambiente](#-variáveis-de-ambiente)
- [Estrutura de Pastas](#-estrutura-de-pastas)

---

##  Sobre o Projeto

O **NutriVida** é uma plataforma web voltada para a **gestão de dietas nutricionais**, conectando pacientes a nutricionistas para acompanhamento personalizado de alimentação. Esta versão é um **protótipo educacional** que cobre as funcionalidades essenciais de um sistema web:

- Autenticação e cadastro de **pacientes** e **nutricionistas**
- Gerenciamento de **dietas** personalizadas
- Gestão de **refeições** com alimentos específicos
- Catalogação de **alimentos** com informações nutricionais
- Gerenciamento de **endereços** dos pacientes
- Painel administrativo para nutricionistas
- Área do paciente para acompanhamento de dietas

Todo o **CRUD foi implementado com SQL escrito manualmente**, sem ORM, para garantir o aprendizado real das operações com banco de dados.

---

##  Arquitetura do Sistema

A arquitetura foi inspirada no padrão do **Spring Framework (Java)**, adaptada para PHP puro, com separação clara de responsabilidades em camadas:

```
┌─────────────────────────────────────────────────────┐
│  FRONTEND (Views + Services + Assets)               │
├─────────────────────────────────────────────────────┤
│ Views (HTML)                                        │
│ └── Renderização e Eventos                          │
│                                                     │
│ Services (JavaScript)                               │
│ └── Lógica de Aplicação + Fetch API                 │
│                                                     │
│ Assets (CSS + Images)                               │
│ └── Estilos e Recursos                              │
└────────┬────────────────────────────────────────────┘
         │ HTTP/JSON
         ▼
┌─────────────────────────────────────────────────────┐
│  API (Endpoints REST)                               │
├─────────────────────────────────────────────────────┤
│ patient.php, diet.php, meal.php, aliment.php...     │
└────────┬────────────────────────────────────────────┘
         │ HTTP
         ▼
┌─────────────────────────────────────────────────────┐
│  BACKEND (Controllers → Services → Repositories)    │
├─────────────────────────────────────────────────────┤
│ Controllers ─┐                                      │
│              ├─→ Services ─┐                        │
│              │             ├─→ Repositories ─→ SQL  │
│              └─────────────┤                        │
│                            └─→ Models               │
└────────┬────────────────────────────────────────────┘
         │ SQL
         ▼
┌─────────────────────────────────────────────────────┐
│  MySQL 8 (Docker)                                   │
└─────────────────────────────────────────────────────┘
```





### Fluxo de uma requisição

1. O **Frontend** faz uma chamada HTTP para a **API** (ex: `GET /api/diet.php?action=selectFullDetails&id=1`)
2. A **API** recebe a requisição, valida os dados e chama o **Controller** correspondente
3. O **Controller** delega a lógica de negócio para o **Service**
4. O **Service** chama o **Repository**, que executa o SQL diretamente no banco
5. O **Repository** usa o **Model** para mapear os dados retornados
6. A resposta percorre o caminho inverso e retorna como **JSON** ao frontend
7. O **Frontend** processa o JSON e atualiza a interface dinamicamente

---

##  Módulos

### Frontend

Desenvolvido com **HTML, CSS e JavaScript puros**, sem nenhuma biblioteca ou framework.

**Principais funcionalidades:**

**Para Pacientes:**
- `login.html` - Tela de autenticação (paciente/nutricionista)
- `patient_registration.html` - Cadastro de paciente
- `patient_dashboard.html` - Dashboard com dietas ativas/finalizadas
- `patient_details.html` - Perfil do paciente
- `patient_addresses.html` - Gerenciamento de endereços
- `patient_password.html` - Redefinição de senha
- `diet_details.html` - Visualização detalhada de dieta

**Para Nutricionistas:**
- `nutritionist_dashboard.html` - Painel administrativo (CRUD de alimentos, refeições, dietas)
- `nutritionist_patients.html` - Gerenciamento de pacientes
- `nutritionist_password.html` - Redefinição de senha

#### **Services (JavaScript)**

Camada de lógica de aplicação que comunica com a API do backend.

| Serviço | Responsabilidade |
|---|---|
| **auth.js** | Autenticação (login/logout), gerenciamento de sessão |
| **patientDashboard.js** | Lógica da dashboard de pacientes |
| **nutritionistDashboard.js** | Lógica da dashboard de nutricionistas |
| **diet.js** | Operações CRUD de dietas |
| **meal.js** | Operações CRUD de refeições |
| **aliment.js** | Operações CRUD de alimentos |
| **patientDetails.js** | Gerenciamento de perfil de paciente |
| **patientAddresses.js** | Gerenciamento de endereços |
| **nutritionistPatients.js** | Gerenciamento de pacientes (nutricionista) |
| **password.js** | Alteração de senha |
| **register.js** | Lógica de registro de novo usuário |

**Cada Service:**
- Faz requisições HTTP para a API
- Processa respostas JSON
- Passa dados para as Views
- Implementa tratamento de erros

**Técnicas utilizadas:**
- Manipulação de DOM com JavaScript vanilla
- Requisições HTTP via `fetch` API nativa
- Renderização dinâmica de conteúdo
- Armazenamento local com `localStorage` e sessões PHP
- Upload de imagens com validação frontend

## Estilos (CSS)
Responsivos e organizados por página/funcionalidade:
```
css/
├── dashboard.css        # Estilos do layout principal
├── meal.css            # Estilos de formulários de refeição
├── diet.css            # Estilos da gestão de dietas
├── aliment.css         # Estilos da gestão de alimentos
├── login.css           # Estilos da página de login
├── register.css        # Estilos da página de registro
├── password.css        # Estilos para alteração de senha
├── patientDetails.css  # Estilos do perfil do paciente
├── address.css         # Estilos de gerenciamento de endereços
├── dietDetail.css      # Estilos de detalhes de dieta
└─ nutritionistPatients.css # Estilos da gestão de paciente
```


## Fluxo de dados Frontend:
```
View (HTML)
    ↓ (dispara eventos)
Service (JavaScript)
    ↓ (faz requisição)
Fetch API → Backend
    ↓ (recebe JSON)
Service (processa dados)
    ↓ (manipula DOM)
View (atualiza elementos)
```

**Estrutura:**
```
frontend/
├── views/                     # Páginas HTML
│   ├── login.html
│   ├── patient_registration.html
│   ├── patient_dashboard.html
│   ├── patient_details.html
│   ├── patient_addresses.html
│   ├── patient_password.html
│   ├── nutritionist_dashboard.html
│   ├── nutritionist_patients.html
│   ├── nutritionist_password.html
│   └── diet_details.html
│
└── assets/
    ├── css/                   # Estilos (Services de UI)
    │   ├── dashboard.css
    │   ├── meal.css
    │   ├── diet.css
    │   ├── aliment.css
    │   ├── login.css
    │   ├── register.css
    │   ├── password.css
    │   ├── patientDetails.css
    │   ├── address.css
    │   ├── dietDetail.css
    │   └── nutritionistPatients.css
    │
    ├── js/                    # Services (Lógica de Aplicação)
    │   ├── auth.js           # Autenticação
    │   ├── patientDashboard.js
    │   ├── nutritionistDashboard.js
    │   ├── diet.js
    │   ├── meal.js
    │   ├── aliment.js
    │   ├── patientDetails.js
    │   ├── patientAddresses.js
    │   ├── nutritionistPatients.js
    │   ├── password.js
    │   ├── register.js
    │   └── app.js            # Arquivo raiz/configurações
    │
    └── images/               # Fotos de perfil e refeições
```





---

### Backend

Desenvolvido em **PHP puro**, organizado em camadas inspiradas no padrão do Spring:

| Camada | Responsabilidade |
|---|---|
| **Controller** | Recebe a requisição da API, valida e coordena o fluxo |
| **Service** | Contém a lógica de negócio da aplicação |
| **Repository** | Executa as queries SQL diretamente no banco de dados |
| **Model** | Representa a estrutura de dados (getters/setters) de cada entidade |
| **Config** | Gerencia conexões com o banco (PDO) e configurações |

**Principais funcionalidades:**
- Autenticação de **Pacientes** (login/logout com sessão PHP)
- Autenticação de **Nutricionistas** (login/logout com sessão PHP)
- CRUD completo de **Pacientes**
- CRUD completo de **Nutricionistas**
- CRUD completo de **Dietas**
- CRUD completo de **Refeições**
- CRUD completo de **Alimentos**
- CRUD completo de **Endereços**
- Todo SQL escrito manualmente (INSERT, SELECT, UPDATE, DELETE, JOIN) — sem ORM
- Upload e gerenciamento de imagens
- Transações no banco de dados (ex: cadastro de paciente + endereço)

**Entidades do Projeto:**
- `Patient` - Pacientes que acompanham dietas
- `Nutritionist` - Profissionais que criam dietas
- `Diet` - Dietas personalizadas para pacientes
- `Meal` - Refeições que compõem uma dieta (café, almoço, jantar, etc)
- `Aliment` - Alimentos com informações nutricionais
- `MealAliment` - Relação entre refeições e alimentos
- `Address` - Endereços dos pacientes

**Estrutura:**

```
backend/
├── controllers/
│   ├── PatientController.php
│   ├── NutritionistController.php
│   ├── DietController.php
│   ├── MealController.php
│   ├── AlimentController.php
│   └── AddressController.php
│
├── services/
│   ├── PatientService.php
│   ├── NutritionistService.php
│   ├── DietService.php
│   ├── MealService.php
│   ├── AlimentService.php
│   └── AddressService.php
│
├── repositories/
│   ├── PatientRepository.php
│   ├── NutritionistRepository.php
│   ├── DietRepository.php
│   ├── MealRepository.php
│   ├── MealAlimentRepository.php
│   ├── AlimentRepository.php
│   └── AddressRepository.php
│
├── models/
│   ├── PatientModel.php
│   ├── NutritionistModel.php
│   ├── DietModel.php
│   ├── MealModel.php
│   ├── MealAlimentModel.php
│   ├── AlimentModel.php
│   └── AddressModel.php
│
├── routes/
│   ├── PatientRouter.php
│   ├── NutritionistRouter.php
│   ├── DietRouter.php
│   ├── MealRouter.php
│   ├── AlimentRouter.php
│   └── AddressRouter.php
│
└── config/
    ├── Connection.php      # Gerencia conexão PDO com MySQL
    └── Upload.php          # Configurações de upload de imagens
```



---

### API

Camada de entrada do sistema, que expõe os endpoints REST consumidos pelo frontend. Recebe as requisições HTTP, interpreta os parâmetros (action, id) e direciona para o controller correto.

**Padrão de requisição:**
/api/{entidade}.php?action={acao}&id={id}


**Principais endpoints:**

| Endpoint | Método | Action | Descrição |
|---|---|---|---|
| `/api/patient.php` | POST | `login` | Autenticação de paciente |
| `/api/patient.php` | POST | `register` | Cadastro de paciente |
| `/api/patient.php` | GET | `getLogged` | Retorna dados do paciente logado |
| `/api/patient.php` | POST | `logout` | Encerramento de sessão |
| `/api/patient.php` | POST | `updateProfile` | Atualiza perfil do paciente |
| `/api/nutritionist.php` | POST | `login` | Autenticação de nutricionista |
| `/api/nutritionist.php` | POST | `register` | Cadastro de nutricionista |
| `/api/nutritionist.php` | GET | `getLogged` | Retorna dados do nutricionista logado |
| `/api/nutritionist.php` | POST | `logout` | Encerramento de sessão |
| `/api/diet.php` | POST | `register` | Cria uma nova dieta |
| `/api/diet.php` | POST | `update` | Atualiza uma dieta |
| `/api/diet.php` | GET | `selectFullDetails` | Retorna dieta com todas as refeições |
| `/api/diet.php` | GET | `selectAll` | Lista todas as dietas do nutricionista |
| `/api/diet.php` | POST | `delete` | Remove uma dieta |
| `/api/meal.php` | POST | `register` | Cria uma nova refeição |
| `/api/meal.php` | POST | `update` | Atualiza uma refeição |
| `/api/meal.php` | GET | `select` | Retorna dados de uma refeição |
| `/api/meal.php` | POST | `delete` | Remove uma refeição |
| `/api/aliment.php` | POST | `registerAliment` | Cadastra um novo alimento |
| `/api/aliment.php` | POST | `updateAliment` | Atualiza um alimento |
| `/api/aliment.php` | GET | `select` | Retorna dados de um alimento |
| `/api/aliment.php` | GET | (sem action) | Lista todos os alimentos |
| `/api/aliment.php` | POST | `deleteAliment` | Remove um alimento |
| `/api/address.php` | POST | `registerAddress` | Cadastra um novo endereço |
| `/api/address.php` | POST | `updateAddress` | Atualiza um endereço |
| `/api/address.php` | POST | `deleteAddress` | Remove um endereço |

**Exemplo de requisição:**
```javascript
fetch('../../api/diet.php?action=selectFullDetails&id=1', {
    method: 'GET'
})
.then(response => response.json())
.then(data => {
    if(data.status === 'success') {
        console.log(data.data); // Dieta com refeições
    }
})
````

Padrão de resposta (sucesso):
```json
{
    "status": "success",
    "data": { /* dados */ },
    "message": "Operação realizada com sucesso!"
}
```

Padrão de resposta (erro):
```json
{
    "status": "error",
    "message": "Descrição do erro"
}
```

## Tecnologias Utilizadas

| Tecnologia | Versão | Uso |
|---|---:|---|
| PHP | 8.2 | Backend e API |
| HTML | 5 | Estrutura das páginas |
| CSS | 3 | Estilização responsiva |
| JavaScript | ES6+ | Interatividade e consumo da API |
| MySQL | 8 | Banco de dados relacional |
| Apache | 2.4 | Servidor web |
| PDO | nativo | Abstração para acesso ao banco (prepared statements) |
| phpMyAdmin | latest | Interface visual para o banco de dados |
| Docker | latest | Containerização da aplicação |
| Docker Compose | latest | Orquestração de containers |



## Como Rodar o Projeto
### Pré-requisitos
Docker instalado
Docker Compose instalado
Terminal/Command Prompt

### Passo a passo
1. Clone o repositório
```bash
git clone https://github.com/RonaldoGR/nutrivida_v1.git
cd nutrivida_v1
```

2. Navegue até a pasta do projeto
```bash
cd nutrivida
```
3. Suba os containers

```bash
docker compose up -d
```

Esse comando irá subir três serviços automaticamente:

| Serviço     | Imagem             | Descrição                         | Porta (host:container) |
|-------------|--------------------:|----------------------------------|------------------------:|
| php         | `php:8.2-apache`    | Servidor web com PHP e Apache     | `8080:80` (acesso: http://localhost:8080) |
| mysql       | `mysql:latest`      | Banco de dados MySQL              | `3306:3306` (acesso externo: :3306) |
| phpmyadmin  | `phpmyadmin:latest` | Interface web para o banco        | `8081:80` (acesso: http://localhost:8081) |

## 4. Acesse a aplicação

- Sistema NutriVida: http://localhost:8080  
- phpMyAdmin: http://localhost:8081

Credenciais (desenvolvimento)
- Servidor: `mysql` (nome do serviço usado pelo phpMyAdmin e pela aplicação dentro da rede Docker)  
- Usuário: `root`  
- Senha: (deixar em branco — configuração com senha vazia para desenvolvimento)

## 5. Primeiros passos
Acesse a página de login e escolha "Sou Nutricionista" ou "Sou Paciente"
Clique em "Cadastrar" para criar uma conta
Faça o login e explore o sistema

Observação: No arquivo `init.sql` está os dados cadastrados de teste. Você pode verificar por lá qual usuário deseja utilizar. 
Por exemplo --- Login de um páciente: carlos@email.com | senha: 123

## 6. Para derrubar os containers
```bash
docker compose down
```

Para derrubar e remover os volumes (apaga os dados do banco):
```bash
docker compose down -v
```

Para visualizar logs dos containers:
```bash
docker compose logs -f
```

 ## Variáveis de Ambiente
As variáveis de ambiente são configuradas diretamente no docker-compose.yml. Para um ambiente de produção, crie um arquivo .env:

```.env
MYSQL_ROOT_PASSWORD=sua_senha_root
MYSQL_DATABASE=nutrivida
MYSQL_USER=nutrivida_user
MYSQL_PASSWORD=sua_senha_usuario

PMA_HOST=mysql
PMA_PORT=3306
PMA_USER=root
PMA_PASSWORD=sua_senha_root
```

Depois, atualize o docker-compose.yml para usar essas variáveis:

```YAML
environment:
  MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
  MYSQL_DATABASE: ${MYSQL_DATABASE}
```


## Observações
Este projeto foi construído intencionalmente sem frameworks e bibliotecas para consolidar os fundamentos do desenvolvimento web. As decisões de arquitetura foram tomadas com fins didáticos e educacionais:

### Por que sem ORM?
Entender como SQL funciona de verdade.
Aprender sobre prepared statements e segurança (SQL injection).
Dominar operações CRUD na essência.
Compreender transações no banco de dados.

### Por que sem framework PHP?
Entender o ciclo de vida completo de uma requisição HTTP.
Aprender como rotas, controllers e middlewares funcionam.
Dominar gerenciamento de sessões e autenticação.
Compreender a separação de responsabilidades sem abstrações prontas.

### Por que sem biblioteca JavaScript?
Entender manipulação de DOM nativa.
Aprender o fetch API e requisições HTTP.
Dominar closure, promises e async/await.
Compreender event handling e tratamento de eventos.

### Por que arquitetura em camadas?
Preparar para usar um framework profissional (Spring Boot em Java).
Aprender padrões de design e arquitetura de software.
Entender a importância da separação de responsabilidades.
Facilitar testes e manutenção de código.



Desenvolvido por Ronaldo Gandra Rocha — IFSUL, 3º Semestre de Sistemas para Internet.
