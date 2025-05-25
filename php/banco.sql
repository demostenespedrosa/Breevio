-- Tabela de metas
CREATE TABLE IF NOT EXISTS metas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    dias DECIMAL(10,3) NOT NULL,
    descricao TEXT NOT NULL,
    icone VARCHAR(50) NOT NULL
);

-- Inserir as 50 metas
INSERT INTO metas (titulo, dias, descricao, icone) VALUES
-- Primeiras horas
('Primeiro Passo', 0.041, 'Primeira hora sem fumar! Seu corpo já começa a se recuperar', 'bi-1-circle'),
('Força de Vontade', 0.083, 'Duas horas sem fumar! Continue assim!', 'bi-2-circle'),
('Determinação', 0.125, 'Três horas sem fumar! Você está indo muito bem!', 'bi-3-circle'),
('Superação', 0.167, 'Quatro horas sem fumar! Seu corpo agradece!', 'bi-4-circle'),
('Vitória Inicial', 0.208, 'Cinco horas sem fumar! Você é mais forte que o vício!', 'bi-5-circle'),

-- Primeiro dia
('Primeiro Dia', 1, 'Um dia inteiro sem fumar! Incrível!', 'bi-calendar-check'),
('Respiração Melhor', 1.5, 'Seu pulmão já está respirando melhor!', 'bi-lungs'),

-- Primeira semana
('Primeira Semana', 7, 'Uma semana sem fumar! Você é um guerreiro!', 'bi-trophy'),
('Economia Inicial', 7.5, 'Já economizou dinheiro suficiente para um lanche!', 'bi-wallet2'),

-- Duas semanas
('Fôlego Renovado', 14, 'Duas semanas! Sua respiração está muito melhor!', 'bi-wind'),
('Força Total', 15, 'Seu sistema imunológico está mais forte!', 'bi-shield-check'),

-- Um mês
('Primeiro Mês', 30, 'Um mês sem fumar! Você é inspiração!', 'bi-star'),
('Economia Real', 31, 'Já economizou para um presente especial!', 'bi-gift'),

-- Dois meses
('Superação Total', 60, 'Dois meses! Você é um exemplo!', 'bi-emoji-smile'),
('Vida Nova', 61, 'Seu corpo está se regenerando completamente!', 'bi-heart-pulse'),

-- Três meses
('Trimestre Vitorioso', 90, 'Três meses! Você é uma inspiração!', 'bi-award'),
('Economia Impressionante', 91, 'Já economizou para uma viagem!', 'bi-airplane'),

-- Seis meses
('Meio Ano', 180, 'Seis meses! Você é um campeão!', 'bi-trophy-fill'),
('Saúde Renovada', 181, 'Seu risco de doenças cardíacas diminuiu!', 'bi-heart'),

-- Um ano
('Primeiro Ano', 365, 'Um ano sem fumar! Você é uma lenda!', 'bi-crown'),
('Economia Máxima', 366, 'Economizou o suficiente para uma reforma!', 'bi-house-heart'),

-- Metas de quantidade
('Primeiro Milhar', 50, 'Evitou 1000 cigarros! Incrível!', 'bi-1k-circle'),
('Dez Mil', 500, 'Evitou 10.000 cigarros! Você é demais!', 'bi-10k-circle'),

-- Metas de economia
('Primeira Economia', 10, 'Economizou R$100! Já pode se presentear!', 'bi-cash-stack'),
('Economia Mil', 100, 'Economizou R$1000! Que conquista!', 'bi-cash-coin'),

-- Metas de saúde
('Pulmão Renovado', 45, 'Seu pulmão está muito mais saudável!', 'bi-lungs-fill'),
('Coração Forte', 120, 'Seu coração está mais forte que nunca!', 'bi-heart-fill'),

-- Metas de bem-estar
('Olfato Aumentado', 20, 'Seu olfato está mais aguçado!', 'bi-nose'),
('Paladar Renovado', 25, 'Sua comida está mais saborosa!', 'bi-cup-hot'),

-- Metas de superação
('Primeiro Desafio', 5, 'Superou a primeira semana!', 'bi-flag'),
('Força Interior', 15, 'Você é mais forte que o vício!', 'bi-lightning'),

-- Metas de estilo de vida
('Energia Total', 40, 'Sua energia aumentou!', 'bi-lightning-charge'),
('Pele Renovada', 35, 'Sua pele está mais bonita!', 'bi-stars'),

-- Metas de tempo
('Primeira Hora', 0.041, 'Primeira hora sem fumar!', 'bi-clock'),
('Primeiro Dia', 1, 'Primeiro dia completo!', 'bi-calendar-day'),

-- Metas de superação pessoal
('Autocontrole', 10, 'Seu autocontrole está incrível!', 'bi-shield-lock'),
('Disciplina', 20, 'Sua disciplina é inspiradora!', 'bi-check2-circle'),

-- Metas de qualidade de vida
('Sono Melhor', 30, 'Seu sono está mais tranquilo!', 'bi-moon-stars'),
('Exercícios', 45, 'Sua capacidade física aumentou!', 'bi-bicycle'),

-- Metas de relacionamento
('Família Feliz', 15, 'Sua família está orgulhosa!', 'bi-people'),
('Amigos Inspirados', 30, 'Você inspira seus amigos!', 'bi-person-heart'),

-- Metas de ambiente
('Ar Puro', 1, 'Seu ambiente está mais limpo!', 'bi-cloud-sun'),
('Natureza Agradece', 7, 'Você está ajudando o planeta!', 'bi-tree'),

-- Metas de economia
('Carteira Feliz', 5, 'Sua carteira está mais cheia!', 'bi-wallet'),
('Investimento', 30, 'Economizou para investir!', 'bi-graph-up'),

-- Metas de saúde mental
('Mente Clara', 10, 'Sua mente está mais clara!', 'bi-brain'),
('Estresse Reduzido', 20, 'Seu estresse diminuiu!', 'bi-emoji-dizzy'),

-- Metas de produtividade
('Foco Total', 15, 'Seu foco aumentou!', 'bi-bullseye'),
('Produtividade', 25, 'Sua produtividade está nas alturas!', 'bi-rocket'),

-- Metas de longevidade
('Vida Longa', 100, 'Aumentou sua expectativa de vida!', 'bi-hourglass-split'),
('Qualidade de Vida', 150, 'Sua qualidade de vida melhorou muito!', 'bi-gem'),

-- Metas de superação final
('Campeão', 365, 'Um ano sem fumar! Você é um campeão!', 'bi-trophy-fill'),
('Lenda', 730, 'Dois anos sem fumar! Você é uma lenda!', 'bi-stars'); 