# 🎸 Projeto Website Queen

Projeto desenvolvido ao longo de quatro módulos do curso de Web Developer - Full Stack, mostrando a evolução progressiva de um website temático da banda Queen, desde uma versão estática em HTML/CSS até uma aplicação completa com PHP, MySQL, autenticação, carrinho de compras, área administrativa e gestão de eventos.

## 📖 Visão geral
Este repositório foi organizado para apresentar, de forma clara e profissional, a evolução do mesmo projeto ao longo da formação. A estrutura em módulos facilita a compreensão do processo de aprendizagem, da progressão técnica e das funcionalidades implementadas em cada etapa.

A organização atual em quatro pastas principais reflete a publicação bem-sucedida da branch `main` com os módulos separados no GitHub, substituindo a antiga estrutura em que apenas o primeiro módulo aparecia na raiz do repositório.

## 📂 Estrutura do repositório

```text
Projeto-Website-Queen/
├── modulo-1-html-css/
├── modulo-2-javascript/
├── modulo-3-php/
├── modulo-4-mysql/
└── README.md
```


## 🧱 Módulo 1 - HTML e CSS
Primeira versão do projeto, focada na construção da base visual e estrutural do website da banda Queen.
**Funcionalidades:**
 * Página inicial (index.html).
 * Página sobre a banda (sobre.html).
 * Página de álbuns (albuns.html).
 * Página de concertos (tour.html).
 * Página de contactos com formulário (contactos.html).
 * Cabeçalho e rodapé comuns em todas as páginas.
 * Navegação principal entre secções do website.
**Tecnologias:** HTML5 | CSS3


## ⚡ Módulo 2 - JavaScript
Segunda fase do projeto, mantendo a base anterior e adicionando comportamento dinâmico no front-end com JavaScript, AJAX e JSON.
**Funcionalidades:**
 * Loja online com carregamento dinâmico de produtos e categorias.
 * Utilização de AJAX/JSON para exibição dinâmica dos dados.
 * Formulário de cálculo de valor total.
 * Lightbox para visualização detalhada dos produtos.
 * Carrinho de compras no lado do cliente.
**Tecnologias:** HTML5 | CSS3 | JavaScript | AJAX | JSON


## 🐘 Módulo 3 - PHP
Terceira fase do projeto, em que o website evolui de uma solução estática para uma aplicação web com backend em PHP e integração com base de dados.
**Funcionalidades:**
 * Conversão das páginas anteriores para PHP.
 * Sistema de login com verificação de credenciais.
 * Sistema de registo de utilizadores.
 * Envio de dados para a base de dados.
 * Carrinho de compras funcional.
 * Área de administrador para gestão de utilizadores, encomendas e produtos.
 * Dashboard com visão geral administrativa.
**🔒 Segurança e Autenticação:**
 * As páginas protegidas só podem ser acedidas por utilizadores autenticados.
 * O acesso é controlado através de sessão (session), impedindo entradas indevidas sem login.
 * As informações dos utilizadores são armazenadas na base de dados MySQL.
 * As palavras-passe são guardadas de forma segura com password_hash.
 * A validação do login é feita com password_verify.
**Tecnologias:** PHP | HTML5 | CSS3 | MySQL


## 🗄️ Módulo 4 - MySQL
Versão final e mais completa do projeto, consolidando todas as funcionalidades anteriores e acrescentando gestão de eventos, compra de bilhetes e administração avançada.
**Funcionalidades:**
 * Cadastro de utilizadores.
 * Login e logout.
 * CRUD de eventos: criar, visualizar, editar e excluir eventos.
 * Listagem e pesquisa de eventos por título ou data.
 * Página de detalhes do evento.
 * Compra de bilhetes para eventos.
 * Carrinho de compras para bilhetes.
 * Histórico de compras.
 * Página de perfil do utilizador com edição de dados e histórico.
 * Página de perfil do administrador para gestão de utilizadores, eventos e compras.
**Tecnologias:** PHP | MySQL | SQL | HTML5 | CSS3 | JavaScript


## 🚀 Como executar

### Módulos 1 e 2 (Front-end)
Podem ser executados diretamente no navegador, abrindo os ficheiros HTML ou através de extensões como o Live Server.

### Módulos 3 e 4 (Back-end)
Requerem um ambiente com servidor local (como XAMPP, WAMP ou Laragon), pois o GitHub não processa PHP e o projeto depende de base de dados ativa.
**Passos recomendados:**
 1. Colocar a pasta do módulo dentro do diretório do servidor local (ex: htdocs no XAMPP).
 2. Criar uma base de dados no phpMyAdmin nomeado queen_db e importar o ficheiro database.sql.
 3. Verifica se as definições da base de dados estão corretas no ficheiro `config.php`:
```php
$host = 'localhost';
$dbname = 'queen_db';
$username = 'root';
$password = 'Admin@369';
```
 4. Aceder ao projeto pelo navegador usando http://localhost/nome-da-pasta.
🔑 **Credenciais de Teste (Admin):**
 * **Email/Login:** admin
 * **Password:** admin369 

## 🌟 Boas práticas aplicadas
 * Organização do repositório por módulos para mostrar evolução técnica.
 * Separação entre front-end estático, front-end dinâmico e back-end.
 * Progressão gradual de complexidade, partindo de HTML/CSS até uma aplicação com autenticação e base de dados relacional.
 * Aplicação de conceitos de segurança em aplicações web.
 * Estrutura adequada para apresentação em portfólio técnico.

**Autor** **Pamela Medeiros** 
Junior Full-Stack Web Developer 💻
📍 Lisboa, Portugal