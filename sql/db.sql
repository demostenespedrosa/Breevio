-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25/05/2025 às 02:13
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `parar_de_fumar`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `diario`
--

CREATE TABLE `diario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_registro` date NOT NULL,
  `humor` tinyint(4) NOT NULL,
  `gatilhos` set('ansiedade','estresse','social','trabalho','fissura','outro') DEFAULT NULL,
  `energia` enum('baixo','medio','alto') DEFAULT NULL,
  `conquista` varchar(50) DEFAULT NULL,
  `buddy_hoje` tinyint(1) DEFAULT 0,
  `texto_opcional` varchar(200) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `diario`
--

INSERT INTO `diario` (`id`, `usuario_id`, `data_registro`, `humor`, `gatilhos`, `energia`, `conquista`, `buddy_hoje`, `texto_opcional`, `criado_em`) VALUES
(2, 1, '2025-05-20', 2, 'estresse,trabalho', 'baixo', 'Resisti à fissura', 0, '', '2025-05-20 05:02:20');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens_motivacionais`
--

CREATE TABLE `mensagens_motivacionais` (
  `id` int(11) NOT NULL,
  `texto` text NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mensagens_motivacionais`
--

INSERT INTO `mensagens_motivacionais` (`id`, `texto`, `criado_em`) VALUES
(1, 'Cada dia sem fumar é uma vitória! Continue assim!', '2025-05-20 00:43:59'),
(2, 'Seu corpo já está se recuperando. Mantenha o foco!', '2025-05-20 00:43:59'),
(3, 'Você está economizando dinheiro e ganhando saúde!', '2025-05-20 00:43:59'),
(4, 'Respire fundo e lembre-se: você é mais forte que o cigarro!', '2025-05-20 00:43:59'),
(5, 'Cada hora sem fumar é um passo para uma vida mais saudável!', '2025-05-20 00:43:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `metas`
--

CREATE TABLE `metas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `dias` decimal(10,3) NOT NULL,
  `descricao` text NOT NULL,
  `icone` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `metas`
--

INSERT INTO `metas` (`id`, `titulo`, `dias`, `descricao`, `icone`) VALUES
(1, 'Primeiro Passo', 0.041, 'Primeira hora sem fumar! Seu corpo já começa a se recuperar', 'bi-1-circle'),
(2, 'Força de Vontade', 0.083, 'Duas horas sem fumar! Continue assim!', 'bi-2-circle'),
(3, 'Determinação', 0.125, 'Três horas sem fumar! Você está indo muito bem!', 'bi-3-circle'),
(4, 'Superação', 0.167, 'Quatro horas sem fumar! Seu corpo agradece!', 'bi-4-circle'),
(5, 'Vitória Inicial', 0.208, 'Cinco horas sem fumar! Você é mais forte que o vício!', 'bi-5-circle'),
(6, 'Primeiro Dia', 1.000, 'Um dia inteiro sem fumar! Incrível!', 'bi-calendar-check'),
(7, 'Respiração Melhor', 1.500, 'Seu pulmão já está respirando melhor!', 'bi-lungs'),
(8, 'Primeira Semana', 7.000, 'Uma semana sem fumar! Você é um guerreiro!', 'bi-trophy'),
(9, 'Economia Inicial', 7.500, 'Já economizou dinheiro suficiente para um lanche!', 'bi-wallet2'),
(10, 'Fôlego Renovado', 14.000, 'Duas semanas! Sua respiração está muito melhor!', 'bi-wind'),
(11, 'Força Total', 15.000, 'Seu sistema imunológico está mais forte!', 'bi-shield-check'),
(12, 'Primeiro Mês', 30.000, 'Um mês sem fumar! Você é inspiração!', 'bi-star'),
(13, 'Economia Real', 31.000, 'Já economizou para um presente especial!', 'bi-gift'),
(14, 'Superação Total', 60.000, 'Dois meses! Você é um exemplo!', 'bi-emoji-smile'),
(15, 'Vida Nova', 61.000, 'Seu corpo está se regenerando completamente!', 'bi-heart-pulse'),
(16, 'Trimestre Vitorioso', 90.000, 'Três meses! Você é uma inspiração!', 'bi-award'),
(17, 'Economia Impressionante', 91.000, 'Já economizou para uma viagem!', 'bi-airplane'),
(18, 'Meio Ano', 180.000, 'Seis meses! Você é um campeão!', 'bi-trophy-fill'),
(19, 'Saúde Renovada', 181.000, 'Seu risco de doenças cardíacas diminuiu!', 'bi-heart'),
(20, 'Primeiro Ano', 365.000, 'Um ano sem fumar! Você é uma lenda!', 'bi-crown'),
(21, 'Economia Máxima', 366.000, 'Economizou o suficiente para uma reforma!', 'bi-house-heart'),
(22, 'Primeiro Milhar', 50.000, 'Evitou 1000 cigarros! Incrível!', 'bi-1k-circle'),
(23, 'Dez Mil', 500.000, 'Evitou 10.000 cigarros! Você é demais!', 'bi-10k-circle'),
(24, 'Primeira Economia', 10.000, 'Economizou R$100! Já pode se presentear!', 'bi-cash-stack'),
(25, 'Economia Mil', 100.000, 'Economizou R$1000! Que conquista!', 'bi-cash-coin'),
(26, 'Pulmão Renovado', 45.000, 'Seu pulmão está muito mais saudável!', 'bi-lungs-fill'),
(27, 'Coração Forte', 120.000, 'Seu coração está mais forte que nunca!', 'bi-heart-fill'),
(28, 'Olfato Aumentado', 20.000, 'Seu olfato está mais aguçado!', 'bi-nose'),
(29, 'Paladar Renovado', 25.000, 'Sua comida está mais saborosa!', 'bi-cup-hot'),
(30, 'Primeiro Desafio', 5.000, 'Superou a primeira semana!', 'bi-flag'),
(31, 'Força Interior', 15.000, 'Você é mais forte que o vício!', 'bi-lightning'),
(32, 'Energia Total', 40.000, 'Sua energia aumentou!', 'bi-lightning-charge'),
(33, 'Pele Renovada', 35.000, 'Sua pele está mais bonita!', 'bi-stars'),
(34, 'Primeira Hora', 0.041, 'Primeira hora sem fumar!', 'bi-clock'),
(35, 'Primeiro Dia', 1.000, 'Primeiro dia completo!', 'bi-calendar-day'),
(36, 'Autocontrole', 10.000, 'Seu autocontrole está incrível!', 'bi-shield-lock'),
(37, 'Disciplina', 20.000, 'Sua disciplina é inspiradora!', 'bi-check2-circle'),
(38, 'Sono Melhor', 30.000, 'Seu sono está mais tranquilo!', 'bi-moon-stars'),
(39, 'Exercícios', 45.000, 'Sua capacidade física aumentou!', 'bi-bicycle'),
(40, 'Família Feliz', 15.000, 'Sua família está orgulhosa!', 'bi-people'),
(41, 'Amigos Inspirados', 30.000, 'Você inspira seus amigos!', 'bi-person-heart'),
(42, 'Ar Puro', 1.000, 'Seu ambiente está mais limpo!', 'bi-cloud-sun'),
(43, 'Natureza Agradece', 7.000, 'Você está ajudando o planeta!', 'bi-tree'),
(44, 'Carteira Feliz', 5.000, 'Sua carteira está mais cheia!', 'bi-wallet'),
(45, 'Investimento', 30.000, 'Economizou para investir!', 'bi-graph-up'),
(46, 'Mente Clara', 10.000, 'Sua mente está mais clara!', 'bi-brain'),
(47, 'Estresse Reduzido', 20.000, 'Seu estresse diminuiu!', 'bi-emoji-dizzy'),
(48, 'Foco Total', 15.000, 'Seu foco aumentou!', 'bi-bullseye'),
(49, 'Produtividade', 25.000, 'Sua produtividade está nas alturas!', 'bi-rocket'),
(50, 'Vida Longa', 100.000, 'Aumentou sua expectativa de vida!', 'bi-hourglass-split'),
(51, 'Qualidade de Vida', 150.000, 'Sua qualidade de vida melhorou muito!', 'bi-gem'),
(52, 'Campeão', 365.000, 'Um ano sem fumar! Você é um campeão!', 'bi-trophy-fill'),
(53, 'Lenda', 730.000, 'Dois anos sem fumar! Você é uma lenda!', 'bi-stars');

-- --------------------------------------------------------

--
-- Estrutura para tabela `recaidas`
--

CREATE TABLE `recaidas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_recaida` date NOT NULL,
  `motivo` text DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `recaidas`
--

INSERT INTO `recaidas` (`id`, `usuario_id`, `data_recaida`, `motivo`, `criado_em`) VALUES
(1, 1, '2025-05-20', 'teste', '2025-05-20 03:49:45'),
(2, 1, '2025-05-27', 'teste', '2025-05-20 03:52:07'),
(3, 1, '2025-05-20', 'teste', '2025-05-20 03:54:26');

-- --------------------------------------------------------

--
-- Estrutura para tabela `ultima_mensagem`
--

CREATE TABLE `ultima_mensagem` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `mensagem_id` int(11) NOT NULL,
  `data_exibicao` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `ultima_mensagem`
--

INSERT INTO `ultima_mensagem` (`id`, `usuario_id`, `mensagem_id`, `data_exibicao`) VALUES
(1, 1, 1, '2025-05-20'),
(2, 1, 1, '2025-05-24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cigarros_por_dia` int(11) NOT NULL,
  `preco_carteira` decimal(10,2) NOT NULL,
  `data_parar` date NOT NULL,
  `hora_parar` time NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `cigarros_por_dia`, `preco_carteira`, `data_parar`, `hora_parar`, `data_cadastro`) VALUES
(1, 'Demóstenes Pedrosa', 'demostenestj@gmail.com', '$2y$10$XX3PGY9EWcvkwSVuuJCsI.uzBk.aWiZ5E8OJWCOeWcrnZZTUVzGAG', 15, 7.00, '2025-05-20', '00:54:00', '2025-05-20 03:11:07');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `diario`
--
ALTER TABLE `diario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario_id` (`usuario_id`,`data_registro`);

--
-- Índices de tabela `mensagens_motivacionais`
--
ALTER TABLE `mensagens_motivacionais`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `metas`
--
ALTER TABLE `metas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `recaidas`
--
ALTER TABLE `recaidas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `ultima_mensagem`
--
ALTER TABLE `ultima_mensagem`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `mensagem_id` (`mensagem_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `diario`
--
ALTER TABLE `diario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `mensagens_motivacionais`
--
ALTER TABLE `mensagens_motivacionais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `metas`
--
ALTER TABLE `metas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de tabela `recaidas`
--
ALTER TABLE `recaidas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `ultima_mensagem`
--
ALTER TABLE `ultima_mensagem`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `diario`
--
ALTER TABLE `diario`
  ADD CONSTRAINT `diario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `recaidas`
--
ALTER TABLE `recaidas`
  ADD CONSTRAINT `recaidas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `ultima_mensagem`
--
ALTER TABLE `ultima_mensagem`
  ADD CONSTRAINT `ultima_mensagem_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ultima_mensagem_ibfk_2` FOREIGN KEY (`mensagem_id`) REFERENCES `mensagens_motivacionais` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
