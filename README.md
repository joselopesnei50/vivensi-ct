# VivensiCT — Guardião Digital

Sistema de Gestão para Conselhos Tutelares com análise de legislação via IA, conforme ECA, SUAS e LGPD.

## Funcionalidades

- 📋 Registro e gestão de atendimentos com protocolo automático
- 🤖 Análise de casos com IA (DeepSeek / Gemini) — cita artigos do ECA e SUAS
- 🗺️ Mapa mental interativo gerado automaticamente
- ⚖️ Medidas de Proteção (ECA Art. 101)
- 📄 Geração de documentos com assinatura digital (Lei 14.063/20)
- 🌐 Rede de Serviços municipais (CRAS, CREAS, UBS, etc.)
- 🔒 Criptografia AES-256 para dados sensíveis de crianças (LGPD)
- 📊 Painel Super Admin multi-tenant
- 🎫 Sistema de chamados de suporte
- 📜 Páginas legais editáveis (Privacidade e Termos de Uso)

## Requisitos

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.4+
- Apache com `mod_rewrite` habilitado
- Extensões PHP: `pdo_mysql`, `openssl`, `mbstring`

## Instalação

```bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/ct-ai1.git
cd ct-ai1

# 2. Copie o arquivo de ambiente
cp .env.example .env

# 3. Gere a chave da aplicação
php -r "echo 'APP_KEY=' . base64_encode(random_bytes(32)) . PHP_EOL;"
# Cole o resultado no .env

# 4. Configure o banco de dados no .env
# DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Crie o banco e importe o schema
mysql -u root -p -e "CREATE DATABASE guardiao_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p guardiao_digital < database/schema.sql

# 6. Configure o Apache para apontar para /public
# Document root: /caminho/para/ct-ai1/public
```

## Configuração

Copie `.env.example` para `.env` e preencha:

| Variável | Descrição |
|----------|-----------|
| `APP_KEY` | Chave de criptografia (gere com o comando acima) |
| `APP_DEBUG` | `false` em produção |
| `DB_*` | Credenciais do banco de dados |
| `DEEPSEEK_API_KEY` | Chave da API DeepSeek (IA) |
| `GEMINI_API_KEY` | Chave da API Gemini (IA) |
| `BREVO_API_KEY` | Chave da API Brevo (e-mails) |

As chaves de API também podem ser configuradas diretamente no **Painel Admin → Configurações**.

## Acesso inicial

Após importar o schema, acesse com:
- **URL:** `http://localhost/ct-ai1/public`
- **E-mail:** `admin@guardiao.digital`
- **Senha:** `password`

> ⚠️ Troque a senha no primeiro acesso.

## Estrutura

```
ct-ai1/
├── app/
│   ├── Controllers/     # Controladores MVC
│   ├── Core/            # Auth, Database, Router, View, AuditLog...
│   ├── Models/
│   └── Services/        # AIService, PDFService, BrevoService
├── config/              # Configurações (app, database, ai)
├── database/
│   └── schema.sql       # Schema completo do banco
├── public/              # Document root (index.php, assets)
│   ├── css/app.css
│   ├── js/app.js
│   └── images/
├── routes/web.php       # Definição de rotas
├── storage/
│   ├── logs/
│   └── pdfs/
└── views/               # Templates PHP
    ├── layouts/
    ├── admin/
    ├── atendimentos/
    └── ...
```

## Segurança

- Autenticação com proteção contra força bruta (5 tentativas / 5 min)
- CSRF em todos os formulários e endpoints AJAX
- Criptografia AES-256-CBC para dados sensíveis (LGPD)
- Isolamento multi-tenant em todas as queries
- Audit log completo de ações
- Headers HTTP de segurança (CSP, X-Frame-Options, etc.)

## Base Legal

- **ECA** — Lei nº 8.069/1990
- **SUAS** — Tipificação Nacional (Res. CNAS 109/2009)
- **LGPD** — Lei nº 13.709/2018
- **Assinatura Digital** — Lei nº 14.063/2020

## Licença

Projeto social — uso gratuito para Conselhos Tutelares brasileiros.
