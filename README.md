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
     define('BASE_URL', 'http://localhost/viewSchool/public/');
     ```

5. **Configurar permissões**
   ```bash
   chmod -R 755 gestctt_new
   chmod -R 777 gestctt_new/uploads
   ```

6. **Acessar o sistema**
   - Abra o navegador e acesse: `http://localhost/gestctt_new/public/`

## 👤 Credenciais Padrão


⚠️ **IMPORTANTE:** Altere essas senhas após o primeiro acesso!

## 📁 Estrutura de Diretórios

```
GestCTT:
│   index.php
│   README.md
│   
├───app
│   ├───config
│   │       auth_admin.php
│   │       auth_user.php
│   │       database.php
│   │       init.php
│   │
│   ├───database
│   │       bd_cttguest.sql
│   │       gestctt_schema.sql
│   │
│   └───views
│       └───includes
│               footer.php
│               header.php
│               navbar.php
│               navbar_user.php
│
├───assets
│   ├───css
│   │       style.css
│   │
│   ├───img
│   │       FalvIcon GestCTT.png
│   │       LogoGestCTT.png
│   │       logomarca ctt png.png
│   │       logomarca-ctt.jpg
│   │
│   └───js
│           main.js
│
├───public
│   │   alas.php
│   │   cadastro.php
│   │   equipamentos.php
│   │   index.php
│   │   layout.php
│   │   login.php
│   │   logout.php
│   │   perfil.php
│   │
│   ├───admin
│   │       equipamentos_problema.php
│   │       paineladm.php
│   │       prestadores.php
│   │       relatorios.php
│   │       usuarios.php
│   │
│   └───user
│           alas.php
│           equipamentos.php
│           index.php
│           perfil.php
│           reportar_problema.php
│
└───uploads
    │   .htaccess
    │
    └───equipamentos
            68e5abd72cb02.jpeg
            68fb5f452c787.jpg
            68fb83be6feb1.webp
            68fb83f4ce474.webp
            69049c2f9c98f.jpeg
            69049cb37fc54.jpeg
            6916112415dc1.webp
            6916115daec51.webp
            6920988d86d3b.jpeg
            692098add78cb.jpeg
            69209e489b7b0.jpeg
            6921cf7d81672.jpeg
            6921d169acf06.webp
            6921d28da4527.webp
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

**Desenvolvido para GestCTT**
