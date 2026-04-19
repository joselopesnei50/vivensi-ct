-- =========================================
-- Guardião Digital - Schema do Banco de Dados
-- ECA/SUAS Compliance System
-- =========================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Tabela de Tenants (Multi-tenancy)
CREATE TABLE IF NOT EXISTS tenants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    municipio VARCHAR(255) NOT NULL,
    estado CHAR(2) NOT NULL,
    cnpj VARCHAR(20),
    telefone VARCHAR(20),
    email VARCHAR(255),
    plano ENUM('gratuito','basico','profissional') DEFAULT 'gratuito',
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de Usuários
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('super_admin','admin','conselheiro') DEFAULT 'conselheiro',
    registro_funcional VARCHAR(100),
    telefone VARCHAR(20),
    avatar VARCHAR(255),
    ativo TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Rede de Serviços Municipais
CREATE TABLE IF NOT EXISTS rede_servicos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    nome_servico VARCHAR(255) NOT NULL,
    tipo_servico VARCHAR(255) NOT NULL,
    tipificacao_suas VARCHAR(255),
    orgao_responsavel VARCHAR(255),
    endereco TEXT,
    telefone VARCHAR(20),
    email VARCHAR(255),
    responsavel VARCHAR(255),
    horario_funcionamento VARCHAR(255),
    observacoes TEXT,
    ativo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Atendimentos (Casos)
CREATE TABLE IF NOT EXISTS atendimentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT NOT NULL,
    user_id INT NOT NULL,
    numero_protocolo VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('aberto','em_andamento','encerrado','arquivado') DEFAULT 'aberto',
    -- Dados da Criança/Adolescente (criptografados via app)
    nome_crianca TEXT,
    data_nascimento_enc TEXT,
    genero VARCHAR(30),
    filiacao TEXT,
    endereco_enc TEXT,
    escola VARCHAR(255),
    -- Dados do Atendimento
    tipo_demanda VARCHAR(255) NOT NULL,
    relato_visita LONGTEXT,
    levantamento_preliminar LONGTEXT,
    -- SIPIA CT Web (Sistema Nacional de Informação para Infância e Adolescência)
    sipia_natureza VARCHAR(10),           -- código ex.: V01, S02, F01
    sipia_abrangencia ENUM('individual','coletiva') DEFAULT 'individual',
    sipia_protocolo VARCHAR(100),         -- nº protocolo SIPIA após registro (criptografado via app)
    -- Saída da IA
    analise_ia LONGTEXT,
    fluxo_encaminhamento LONGTEXT,
    mapa_mental_mermaid LONGTEXT,
    minutas_geradas LONGTEXT,
    -- Metadados
    prioridade ENUM('baixa','media','alta','urgente') DEFAULT 'media',
    data_ocorrencia DATE,
    data_atendimento DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Encaminhamentos
CREATE TABLE IF NOT EXISTS encaminhamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atendimento_id INT NOT NULL,
    orgao_destino VARCHAR(255) NOT NULL,
    servico_id INT,
    tipo_encaminhamento VARCHAR(255),
    descricao TEXT,
    status ENUM('pendente','realizado','recusado') DEFAULT 'pendente',
    data_encaminhamento DATE,
    data_resposta DATE,
    resposta TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (atendimento_id) REFERENCES atendimentos(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES rede_servicos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Medidas de Proteção
CREATE TABLE IF NOT EXISTS medidas_protecao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atendimento_id INT NOT NULL,
    user_id INT NOT NULL,
    tipo_medida VARCHAR(255) NOT NULL,
    artigo_eca VARCHAR(100),
    descricao TEXT NOT NULL,
    fundamentacao_legal TEXT,
    prazo_cumprimento DATE,
    status ENUM('aplicada','cumprida','descumprida','suspensa') DEFAULT 'aplicada',
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (atendimento_id) REFERENCES atendimentos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Documentos Gerados (com controle de expurgo LGPD)
CREATE TABLE IF NOT EXISTS documentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    atendimento_id INT NOT NULL,
    user_id INT NOT NULL,
    tipo_documento VARCHAR(255) NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho_arquivo VARCHAR(500),
    assinatura_data TEXT,
    assinante_nome VARCHAR(255),
    assinante_cargo VARCHAR(255),
    assinado TINYINT(1) DEFAULT 0,
    data_assinatura TIMESTAMP NULL,
    expira_em DATETIME NOT NULL,
    notificacao_enviada TINYINT(1) DEFAULT 0,
    excluido TINYINT(1) DEFAULT 0,
    data_exclusao TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (atendimento_id) REFERENCES atendimentos(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Logs de Auditoria (LGPD)
CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    tenant_id INT,
    acao VARCHAR(255) NOT NULL,
    tabela VARCHAR(100),
    registro_id INT,
    dados_anteriores LONGTEXT,
    dados_novos LONGTEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================
-- DADOS INICIAIS
-- =========================================

-- Tenant padrão (demo)
INSERT INTO tenants (nome, municipio, estado, email, plano) VALUES
('Conselho Tutelar - Demo', 'São Paulo', 'SP', 'admin@guardiao.digital', 'profissional');

-- Super Admin
INSERT INTO users (tenant_id, nome, email, password, role) VALUES
(1, 'Super Administrador', 'admin@guardiao.digital', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- Conselheiro demo
INSERT INTO users (tenant_id, nome, email, password, role, registro_funcional) VALUES
(1, 'Maria Silva', 'conselheiro@guardiao.digital', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'conselheiro', 'CT-001/2024');

-- Rede de serviços demo
INSERT INTO rede_servicos (tenant_id, nome_servico, tipo_servico, tipificacao_suas, orgao_responsavel, telefone) VALUES
(1, 'CRAS Centro', 'CRAS', 'Centro de Referência de Assistência Social', 'Secretaria de Assistência Social', '(11) 1234-5678'),
(1, 'CREAS Municipal', 'CREAS', 'Centro de Referência Especializado de Assistência Social', 'Secretaria de Assistência Social', '(11) 1234-5679'),
(1, 'UBS Centro', 'Saúde', 'Unidade Básica de Saúde', 'Secretaria de Saúde', '(11) 1234-5680'),
(1, 'CAPS Infantil', 'Saúde Mental', 'Centro de Atenção Psicossocial', 'Secretaria de Saúde', '(11) 1234-5681'),
(1, 'Escola Municipal Padre Anchieta', 'Educação', 'Ensino Fundamental', 'Secretaria de Educação', '(11) 1234-5682');

SET FOREIGN_KEY_CHECKS = 1;

-- =========================================
-- Migração v1.2 — Páginas Legais (Privacidade + Termos)
-- =========================================
CREATE TABLE IF NOT EXISTS paginas_legais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(50) UNIQUE NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    conteudo LONGTEXT,
    updated_by INT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO paginas_legais (slug, titulo, conteudo) VALUES
('privacidade', 'Política de Privacidade', '<p>Configure o conteúdo desta página no Painel Administrativo → Páginas Legais.</p>'),
('termos', 'Termos de Uso', '<p>Configure o conteúdo desta página no Painel Administrativo → Páginas Legais.</p>');

-- =========================================
-- Migração v1.1 — Campos SIPIA CT Web
-- Execute somente em bancos existentes:
-- =========================================
ALTER TABLE atendimentos
    ADD COLUMN IF NOT EXISTS sipia_natureza    VARCHAR(10)                        AFTER minutas_geradas,
    ADD COLUMN IF NOT EXISTS sipia_abrangencia ENUM('individual','coletiva') DEFAULT 'individual' AFTER sipia_natureza,
    ADD COLUMN IF NOT EXISTS sipia_protocolo   VARCHAR(100)                       AFTER sipia_abrangencia;

-- Cadastros Pendentes de Aprovação
CREATE TABLE IF NOT EXISTS cadastros_pendentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    data_nascimento DATE NOT NULL,
    email VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    cidade VARCHAR(255) NOT NULL,
    endereco TEXT NOT NULL,
    ano_posse YEAR NOT NULL,
    status ENUM('pendente','aprovado','rejeitado') DEFAULT 'pendente',
    aprovado_por INT NULL,
    data_aprovacao TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (aprovado_por) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configurações Globais da API (Super Admin)
CREATE TABLE IF NOT EXISTS api_configs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chave VARCHAR(100) UNIQUE NOT NULL,
    valor TEXT,
    descricao VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chamados de Suporte (Assinantes → Super Admin)
CREATE TABLE IF NOT EXISTS chamados_suporte (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tenant_id INT,
    user_id INT,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    tipo ENUM('bug','duvida','financeiro','outro') DEFAULT 'duvida',
    status ENUM('aberto','em_atendimento','resolvido','fechado') DEFAULT 'aberto',
    prioridade ENUM('baixa','media','alta','urgente') DEFAULT 'media',
    resposta TEXT,
    respondido_por INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (respondido_por) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
