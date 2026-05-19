#  Mini Sistema de Gestão de Produtos

> Sistema web para gestão de produtos, fornecedores e cestas de compras.  
> Desenvolvido com PHP puro, MySQL (PDO), Bootstrap 5 e JavaScript (AJAX).

---

##  Integrantes

| Nome              | RA       |
|-------------------|----------|
| Vinicius Cordeiro | 60002252 |

---

##  Funcionalidades

- Cadastro e autenticação de usuários com senha em hash **SHA-256**
- **CRUD completo** de Produtos, Fornecedores e Cestas
- Atualização via **AJAX** sem recarregar a página
- **Vitrine** de produtos com seleção por checkbox e validação
- **Carrinho de compras** com resumo, total de itens e valor total
- Proteção contra **SQL Injection** (PDO Prepared Statements)
- Proteção **CSRF** em todos os formulários

---

##  Tecnologias

- PHP 8.1+ (sem frameworks)
- MySQL 8.0+ com PDO
- HTML5 + CSS3
- Bootstrap 5.3
- JavaScript ES6+ (AJAX com Fetch API)

---

##  Pré-requisitos

- PHP 8.1 ou superior
- MySQL 8.0 ou superior
- Servidor local: **XAMPP**, Laragon ou similar
- Git

---

##  Como rodar o projeto

### 1. Clone o repositório

```bash
git clone https://github.com/VHC2201/Mini_sistema_produtos.git
cd Mini_sistema_produtos
```

### 2. Inicie o Apache e o MySQL no XAMPP

Abra o painel do XAMPP e clique em **Start** no Apache e no MySQL.

### 3. Crie o banco de dados e as tabelas

> **Este passo é obrigatório antes de acessar o sistema.**  
> O banco de dados **não é criado automaticamente** — você precisa executar o script SQL manualmente.

**Opção A — pelo MySQL Workbench:**
1. Abra o MySQL Workbench e conecte ao servidor local
2. Clique em **File → Open SQL Script**
3. Selecione o arquivo `config/schema.sql` deste repositório
4. Pressione **Ctrl + Shift + Enter** para executar

**Opção B — pelo terminal:**
```bash
mysql -u root -p < config/schema.sql
```
> Pressione **Enter** quando pedir senha (padrão XAMPP é sem senha).

Após executar, o banco `mini_sistema` e todas as tabelas serão criados.

### 4. Configure as credenciais do banco de dados

> O arquivo `config/database.php` está no `.gitignore` por conter credenciais.  
> Você precisa criá-lo a partir do modelo fornecido.

**No terminal (Git Bash / CMD):**
```bash
cp config/database.example.php config/database.php
```

**No Windows (CMD):**
```cmd
copy config\database.example.php config\database.php
```

Agora abra o arquivo `config/database.php` e preencha com suas credenciais:

```php
define('DB_HOST', 'localhost');    // Servidor (padrão: localhost)
define('DB_NAME', 'mini_sistema'); // Nome do banco
define('DB_USER', 'root');         // Usuário MySQL (padrão XAMPP: root)
define('DB_PASS', '');             // Senha MySQL  (padrão XAMPP: vazio)
```

> Se o seu MySQL tiver senha, coloque entre as aspas de `DB_PASS`.

### 5. Acesse o sistema

**Opção A — via Apache do XAMPP** *(recomendado)*:

Certifique-se de que o projeto está dentro de `C:\xampp\htdocs\` e acesse:
```
http://localhost/Mini_sistema_produtos
```

**Opção B — via servidor PHP embutido**:
```bash
cd C:\xampp\htdocs\Mini_sistema_produtos
php -S localhost:8080
```
Acesse: `http://localhost:8080`

### 6. Crie seu primeiro usuário

Acesse a tela de cadastro e crie uma conta:
```
http://localhost/Mini_sistema_produtos?pagina=cadastro
```

---

##  Estrutura de Pastas

```
Mini_sistema_produtos/
├── api/                        ← Endpoints AJAX (retornam JSON)
│   ├── produto.php
│   ├── fornecedor.php
│   └── cesta.php
├── assets/
│   ├── css/style.css           ← Estilos globais
│   └── js/
│       ├── ajax.js             ← Chamadas AJAX
│       └── validacoes.js       ← Validação de formulários
├── config/
│   ├── database.example.php    ← Modelo de configuração
│   ├── database.php            ← Configuração real com senha 
│   └── schema.sql              ← Script SQL de criação das tabelas
├── controllers/
│   └── AuthController.php
├── models/
│   ├── Model.php               ← Classe base (herança)
│   ├── Usuario.php
│   ├── Produto.php
│   ├── Fornecedor.php
│   └── Cesta.php
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   └── cadastro.php
│   ├── produtos/
│   │   ├── index.php           ← CRUD + AJAX
│   │   └── vitrine.php         ← Seleção com checkbox
│   ├── fornecedores/
│   │   └── index.php           ← CRUD + AJAX
│   ├── cestas/
│   │   └── index.php           ← CRUD + AJAX
│   ├── carrinho/
│   │   └── index.php           ← Resumo e finalização
│   ├── partials/
│   │   ├── header.php
│   │   ├── navbar.php
│   │   └── footer.php
│   └── dashboard.php
├── .gitignore
├── index.php                   ← Roteador central
└── README.md
```

---

##  Diagrama Entidade-Relacionamento (DER)

![DER](docs/der.png)

---

##  Esboços de Tela

| Tela | Preview |
|------|---------|
| Login | ![Login](docs/figma/tela-login.png) |
| Cadastro | ![Cadastro](docs/figma/tela-cadastro.png) |
| Dashboard | ![Dashboard](docs/figma/tela-dashboard.png) |
| CRUD Produtos | ![Produtos](docs/figma/tela-crud-produtos.png) |
| Vitrine | ![Vitrine](docs/figma/tela-vitrine.png) |
| Carrinho | ![Carrinho](docs/figma/tela-carrinho.png) |

---

##  Segurança implementada

| Ameaça | Solução |
|--------|---------|
| SQL Injection | PDO com Prepared Statements em 100% das queries |
| Senhas expostas | Hash SHA-256 — senha nunca salva em texto puro |
| CSRF | Token gerado por sessão, validado em todo POST |
| XSS | `htmlspecialchars()` em todo output de dados do usuário |
| Session Fixation | `session_regenerate_id(true)` após login |
| Timing Attack | `hash_equals()` na comparação de hashes |
| Credenciais expostas | `config/database.php` no `.gitignore` |

---

##  Observações

- Cada produto pode aparecer **apenas uma vez** por cesta (regra de negócio definida pela atividade)
- A senha é armazenada usando `hash('sha256', $senha)` conforme especificado na atividade
- Nenhum framework PHP foi utilizado (Laravel, Symfony, etc. são proibidos pela atividade)