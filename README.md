# GestCTT - Sistema de Gestão de Equipamentos

Sistema completo de gestão de equipamentos escolares desenvolvido em PHP com Bootstrap 5.

## 📋 Funcionalidades

- **Autenticação e Autorização**
  - Login de usuários
  - Cadastro com aprovação de administrador
  - Controle de acesso por tipo de usuário (Admin/Funcionário)

- **Gerenciamento de Alas**
  - Criar, editar e desativar alas (salas/locais)
  - Visualizar equipamentos por ala
  - Busca e filtros

- **Gerenciamento de Equipamentos**
  - CRUD completo de equipamentos
  - Upload de imagens
  - Categorização por tipo (Ar Condicionado, Lâmpada, Tomada, etc.)
  - Controle de status (Ativo, Inativo, Problema)
  - Associação com alas

- **Administração (apenas Admin)**
  - Gerenciamento de usuários
  - Aprovação de cadastros
  - Gerenciamento de prestadores de serviço
  - Relatórios

- **Dashboard**
  - Estatísticas gerais
  - Equipamentos recentes
  - Ações rápidas

## 🚀 Instalação

### Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior / MariaDB 10.3 ou superior
- Servidor web (Apache, Nginx)
- Extensões PHP: mysqli, gd, fileinfo

### Passo a Passo

1. **Extrair os arquivos**
   ```bash
   unzip gestctt_new.zip
   cd gestctt_new
   ```

2. **Configurar o banco de dados**
   - Crie um banco de dados MySQL chamado `ctt`
   - Importe o arquivo SQL:
     ```bash
     mysql -u root -p ctt < app/database/gestctt_schema.sql
     ```

3. **Configurar a conexão com o banco**
   - Edite o arquivo `app/config/database.php`
   - Ajuste as credenciais do banco de dados:
     ```php
     define('DB_SERVER', 'localhost');
     define('DB_USERNAME', 'seu_usuario');
     define('DB_PASSWORD', 'sua_senha');
     define('DB_NAME', 'ctt');
     ```

4. **Configurar a URL base**
   - Edite o arquivo `app/config/init.php`
   - Ajuste a constante BASE_URL de acordo com seu ambiente:
     ```php
     define('BASE_URL', 'http://localhost/gestctt_new/public/');
     ```

5. **Configurar permissões**
   ```bash
   chmod -R 755 gestctt_new
   chmod -R 777 gestctt_new/uploads
   ```

6. **Acessar o sistema**
   - Abra o navegador e acesse: `http://localhost/gestctt_new/public/`

## 👤 Credenciais Padrão

**Administrador:**
- Email: `admin@gestctt.com`
- Senha: `admin123`

**Funcionário:**
- Email: `funcionario@gestctt.com`
- Senha: `admin123`

⚠️ **IMPORTANTE:** Altere essas senhas após o primeiro acesso!

## 📁 Estrutura de Diretórios

```
gestctt_new/
├── app/
│   ├── config/          # Configurações do sistema
│   ├── controllers/     # Controladores (futuro)
│   ├── models/          # Modelos de dados (futuro)
│   ├── views/           # Views e templates
│   │   └── includes/    # Componentes reutilizáveis
│   └── database/        # Scripts SQL
├── assets/
│   ├── css/            # Estilos CSS
│   ├── js/             # JavaScript
│   └── img/            # Imagens do sistema
├── public/             # Arquivos públicos
│   ├── admin/          # Área administrativa
│   ├── index.php       # Dashboard
│   ├── login.php       # Login
│   ├── cadastro.php    # Cadastro
│   ├── alas.php        # Gerenciar alas
│   └── equipamentos.php # Gerenciar equipamentos
└── uploads/            # Uploads de usuários
    └── equipamentos/   # Imagens de equipamentos
```

## 🎨 Tecnologias Utilizadas

- **Backend:** PHP 7.4+
- **Banco de Dados:** MySQL/MariaDB
- **Frontend:** Bootstrap 5.3
- **Ícones:** Bootstrap Icons
- **JavaScript:** Vanilla JS

## 📝 Tipos de Equipamentos Suportados

- Ar Condicionado
- Lâmpada
- Tomada
- Interruptor
- Ventilador
- Projetor
- Computador
- Outro (customizável)

## 🔒 Segurança

- Senhas criptografadas com `password_hash()`
- Proteção contra SQL Injection com prepared statements
- Validação de formulários no cliente e servidor
- Controle de sessão
- Verificação de permissões por tipo de usuário

## 🐛 Solução de Problemas

### Erro de conexão com o banco de dados
- Verifique as credenciais em `app/config/database.php`
- Certifique-se de que o MySQL está rodando
- Verifique se o banco `ctt` foi criado

### Imagens não aparecem
- Verifique as permissões da pasta `uploads/`
- Certifique-se de que a pasta existe: `mkdir -p uploads/equipamentos`

### Erro 404 nas páginas
- Verifique se a constante BASE_URL está correta em `app/config/init.php`
- Certifique-se de que o mod_rewrite está habilitado (Apache)

## 📧 Suporte

Para dúvidas ou problemas, entre em contato com a equipe de desenvolvimento.

## 📄 Licença

Este sistema foi desenvolvido para uso interno da instituição.

---

**Desenvolvido com ❤️ para GestCTT**
