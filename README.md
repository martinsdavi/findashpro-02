# FinDash Pro - Sistema de Gestão Financeira

Sistema completo de gestão financeira pessoal desenvolvido com PHP, MySQL, HTML, CSS e JavaScript.

## Instalação no XAMPP

1. **Copie a pasta do projeto para htdocs:**
   - Coloque todos os arquivos dentro de `C:\xampp\htdocs\findash_pro\`

2. **Inicie o XAMPP:**
   - Inicie o Apache
   - Inicie o MySQL

3. **Crie o banco de dados:**
   - Acesse http://localhost/phpmyadmin
   - Clique em "Novo" para criar um novo banco de dados
   - Importe o arquivo `database.sql` ou execute o script SQL manualmente

4. **Acesse o sistema:**
   - Abra o navegador e acesse: http://localhost/findash_pro/login.php

## Credenciais Padrão

- **Email:** davi@exemplo.com
- **Senha:** 123456

## Funcionalidades

- ✅ Sistema de login e cadastro
- ✅ Dashboard com visão geral financeira
- ✅ Gestão de transações (CRUD completo)
- ✅ Gestão de objetivos financeiros
- ✅ Análise de receitas e despesas
- ✅ Gráficos e visualizações
- ✅ Filtros e busca
- ✅ Design responsivo

## Estrutura de Arquivos

\`\`\`
findash_pro/
├── api/
│   ├── transacoes.php
│   └── objetivos.php
├── css/
│   └── style.css
├── js/
│   ├── script.js
│   ├── transacoes.js
│   ├── objetivos.js
│   └── charts.js
├── includes/
│   ├── sidebar.php
│   └── header.php
├── config.php
├── login.php
├── cadastro.php
├── dashboard.php
├── transacoes.php
├── objetivos.php
├── analise.php
├── logout.php
└── database.sql
\`\`\`

## Tecnologias Utilizadas

- PHP 7.4+
- MySQL 5.7+
- HTML5
- CSS3
- JavaScript (Vanilla)
- Canvas API para gráficos

## Suporte

Para dúvidas ou problemas, verifique se:
- O XAMPP está rodando corretamente
- O banco de dados foi criado
- As credenciais do banco estão corretas em config.php
