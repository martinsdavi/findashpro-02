-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS findash_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE findash_pro;

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    cargo VARCHAR(100) DEFAULT 'Analista de Dados',
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de transações
CREATE TABLE IF NOT EXISTS transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    descricao VARCHAR(255) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    data DATE NOT NULL,
    tipo ENUM('Receita', 'Despesa') NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabela de objetivos
CREATE TABLE IF NOT EXISTS objetivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    valor_atual DECIMAL(10, 2) DEFAULT 0,
    valor_meta DECIMAL(10, 2) NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Inserir usuário padrão (senha: 123456)
INSERT INTO usuarios (nome, cargo, email, senha) VALUES 
('Davi Martins', 'Analista de Dados', 'davi@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Inserir transações de exemplo
INSERT INTO transacoes (usuario_id, descricao, categoria, data, tipo, valor) VALUES
(1, 'Baufilho', 'Transferência', '2024-11-25', 'Despesa', 230.00),
(1, 'Claudinho', 'Transferência', '2024-11-25', 'Despesa', 120.00),
(1, 'Alexa', 'Compras', '2024-11-25', 'Despesa', 90.00),
(1, 'Lanches', 'Alimentação', '2024-11-25', 'Despesa', 80.00),
(1, 'Parcela do moto', 'Transporte', '2024-11-25', 'Despesa', 990.00),
(1, 'Aluguel', 'Moradia', '2024-11-25', 'Despesa', 980.00),
(1, 'Shopping', 'Compras', '2024-11-25', 'Despesa', 300.00),
(1, 'Salário', 'Renda', '2024-11-01', 'Receita', 5000.00),
(1, 'Serviço Freelance', 'Renda Extra', '2024-11-15', 'Receita', 593.00);

-- Inserir objetivos de exemplo
INSERT INTO objetivos (usuario_id, titulo, valor_atual, valor_meta) VALUES
(1, 'Economizar para Viagem', 7500.00, 10000.00),
(1, 'Fundo de Emergência', 15000.00, 15000.00),
(1, 'Novo Notebook', 2700.00, 6000.00),
(1, 'Investimento em Ações', 6000.00, 20000.00);
