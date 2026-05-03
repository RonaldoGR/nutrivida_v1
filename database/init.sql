-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 15/12/2025 às 04:13
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
-- Banco de dados: `nutrivida`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `address`
--

USE nutrivida;



CREATE TABLE `address` (
  `id_address` bigint(20) UNSIGNED NOT NULL,
  `country` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `neighborhood` varchar(50) NOT NULL,
  `street` varchar(50) NOT NULL,
  `number` int(11) NOT NULL,
  `fk_patient_id` bigint(20) UNSIGNED NOT NULL,
  `zip_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `address`
--

INSERT INTO `address` (`id_address`, `country`, `state`, `city`, `neighborhood`, `street`, `number`, `fk_patient_id`, `zip_code`) VALUES
(3, 'Brasil', 'RS', 'Pelotas', 'Bairo de Teste', 'Rua Teste', 456, 4, '96000-000'),
(4, 'Brasil', 'SC', 'Florianópolis', 'Jardim Europa', 'Rodovia Rafael Da Costa Xavier', 4, 4, '88051001'),
(5, 'Brasil', 'RS', 'Pelotas', 'Bairro de Teste', 'Rua Teste', 247, 5, '950000-000');

-- --------------------------------------------------------

--
-- Estrutura para tabela `aliment`
--

CREATE TABLE `aliment` (
  `id_aliment` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `calories` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `aliment`
--

INSERT INTO `aliment` (`id_aliment`, `description`, `quantity`, `calories`) VALUES
(4, 'Croaissant', 200, 200),
(5, 'Café', 100, 80),
(6, 'Carne grelhada', 250, 300),
(7, 'Chimichurri', 50, 60),
(8, 'Azeite', 100, 120),
(9, 'Pão de Forma', 50, 70),
(10, 'Queijo Mussarela', 100, 120),
(11, 'Tomate', 50, 50),
(12, 'Frango grelhado', 200, 320),
(13, 'Brócolis refogado', 80, 50),
(14, 'Tomate cereja', 30, 25),
(15, 'Iogurte de Morango', 200, 250),
(16, 'Banana', 80, 105),
(17, 'Granola', 30, 130),
(18, 'Peito de Frango em cubos', 150, 160),
(19, 'Croutons/Torradas', 80, 100),
(20, 'Mix de Vegetais', 80, 80),
(21, 'Smoothie de Morango', 300, 180),
(22, 'Kiwi picado', 50, 40),
(23, 'Arroz branco', 100, 160),
(24, 'Feijão preto', 100, 130),
(25, 'Peito de Frango Gelhado', 100, 120),
(26, 'Salada', 100, 80),
(27, 'Macarrão com Molho Vermelho', 200, 200);

-- --------------------------------------------------------

--
-- Estrutura para tabela `diet`
--

CREATE TABLE `diet` (
  `id_diet` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(30) NOT NULL,
  `fk_nutritionist_id` bigint(20) UNSIGNED NOT NULL,
  `fk_patient_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `diet`
--

INSERT INTO `diet` (`id_diet`, `start_date`, `end_date`, `status`, `fk_nutritionist_id`, `fk_patient_id`) VALUES
(9, '2025-12-14', '2026-05-14', 'ativa', 2, 4),
(10, '2025-02-10', '2025-12-10', 'finalizada', 2, 5);

-- --------------------------------------------------------

--
-- Estrutura para tabela `meal`
--

CREATE TABLE `meal` (
  `id_meal` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `fk_diet_id` bigint(20) UNSIGNED NOT NULL,
  `photo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `meal`
--

INSERT INTO `meal` (`id_meal`, `type`, `description`, `fk_diet_id`, `photo`) VALUES
(9, 'Café da Manhã', 'Croissant com café', 9, 'cafe5.jfif'),
(10, 'Almoço', 'Carne bovina grelhada com chimichurri', 9, 'almoco4.jfif'),
(11, 'Lanche', 'Torrada com Queijo Mussarela e Tomate', 9, 'lanche1.jfif'),
(12, 'Janta', 'Frango Grelhado com Legumes', 9, 'janta1.jfif'),
(13, 'Café da Manhã', 'Bowl de Iogurte com Frutas e Granola', 10, 'cafe1.jfif'),
(14, 'Almoço', 'Arroz, Feijão, Frango e Salada', 10, 'almoco1.jfif'),
(15, 'Lanche', 'Smoothie com Kiwi ', 10, 'cafe2.jfif'),
(16, 'Janta', 'Peito de Frango Grelhado com Macarrão e Brócolis Cozido', 10, 'almoco3.jfif');

-- --------------------------------------------------------

--
-- Estrutura para tabela `meal_aliment`
--

CREATE TABLE `meal_aliment` (
  `fk_meal_id` bigint(20) UNSIGNED NOT NULL,
  `fk_aliment_id` bigint(20) UNSIGNED NOT NULL,
  `aliment_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `meal_aliment`
--

INSERT INTO `meal_aliment` (`fk_meal_id`, `fk_aliment_id`, `aliment_quantity`) VALUES
(9, 4, 200),
(9, 5, 50),
(10, 6, 300),
(10, 7, 50),
(10, 8, 100),
(11, 9, 50),
(11, 10, 100),
(11, 11, 50),
(12, 12, 200),
(12, 13, 80),
(12, 14, 30),
(13, 15, 200),
(13, 16, 80),
(13, 17, 50),
(14, 12, 200),
(14, 23, 100),
(14, 24, 100),
(14, 26, 100),
(15, 21, 200),
(15, 22, 50),
(16, 13, 50),
(16, 25, 120),
(16, 27, 100);

-- --------------------------------------------------------

--
-- Estrutura para tabela `nutritionist`
--

CREATE TABLE `nutritionist` (
  `id_nutritionist` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `crn` varchar(11) NOT NULL,
  `photo` varchar(50) NOT NULL DEFAULT 'sem foto',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `nutritionist`
--

INSERT INTO `nutritionist` (`id_nutritionist`, `name`, `email`, `cpf`, `crn`, `photo`, `password`) VALUES
(2, 'Nutricionista 1', 'nutriteste@email.com', '111.222.333', '123456789', 'nutricionista1.jpg', '$2y$10$gEBMpUwjEGYWFRAP7uRk5.TlTo1/9nsx26LcpIF.zagHkF1sG8/BG');

-- --------------------------------------------------------

--
-- Estrutura para tabela `patient`
--

CREATE TABLE `patient` (
  `id_patient` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(30) NOT NULL,
  `date_birth` date NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `photo` varchar(50) DEFAULT 'Sem foto',
  `phone` varchar(15) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `patient`
--

INSERT INTO `patient` (`id_patient`, `name`, `date_birth`, `cpf`, `photo`, `phone`, `email`, `password`) VALUES
(4, 'Carlos Silva', '0000-00-00', '12345678901', 'usuario4.jfif', '539999999', 'carlos@email.com', '$2y$10$S0UIY.C/QixrW2wK7d6IJejnHl98sLXjYzQ50F/kPSV8PR8ZXHTuq'),
(5, 'Ana Silva', '0000-00-00', '123456789', 'usuario3.jfif', '539999999', 'ana@email.com', '$2y$10$JDiXJxIrGdcUdLqJEjXvYeFfYih5WElRx1kW8KtxubIgj0V06ojI2');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id_address`);

--
-- Índices de tabela `aliment`
--
ALTER TABLE `aliment`
  ADD PRIMARY KEY (`id_aliment`);

--
-- Índices de tabela `diet`
--
ALTER TABLE `diet`
  ADD PRIMARY KEY (`id_diet`),
  ADD KEY `fk_diet_to_nutritionist` (`fk_nutritionist_id`),
  ADD KEY `fk_diet_to_patient` (`fk_patient_id`);

--
-- Índices de tabela `meal`
--
ALTER TABLE `meal`
  ADD PRIMARY KEY (`id_meal`),
  ADD KEY `fk_diet_to_meal` (`fk_diet_id`);

--
-- Índices de tabela `meal_aliment`
--
ALTER TABLE `meal_aliment`
  ADD PRIMARY KEY (`fk_meal_id`,`fk_aliment_id`),
  ADD KEY `fk_aliment_to_meal_aliment` (`fk_aliment_id`);

--
-- Índices de tabela `nutritionist`
--
ALTER TABLE `nutritionist`
  ADD PRIMARY KEY (`id_nutritionist`);

--
-- Índices de tabela `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id_patient`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `address`
--
ALTER TABLE `address`
  MODIFY `id_address` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `aliment`
--
ALTER TABLE `aliment`
  MODIFY `id_aliment` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `diet`
--
ALTER TABLE `diet`
  MODIFY `id_diet` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `meal`
--
ALTER TABLE `meal`
  MODIFY `id_meal` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `nutritionist`
--
ALTER TABLE `nutritionist`
  MODIFY `id_nutritionist` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `patient`
--
ALTER TABLE `patient`
  MODIFY `id_patient` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `diet`
--
ALTER TABLE `diet`
  ADD CONSTRAINT `fk_diet_to_nutritionist` FOREIGN KEY (`fk_nutritionist_id`) REFERENCES `nutritionist` (`id_nutritionist`),
  ADD CONSTRAINT `fk_diet_to_patient` FOREIGN KEY (`fk_patient_id`) REFERENCES `patient` (`id_patient`) ON DELETE CASCADE;

--
-- Restrições para tabelas `meal`
--
ALTER TABLE `meal`
  ADD CONSTRAINT `fk_diet_to_meal` FOREIGN KEY (`fk_diet_id`) REFERENCES `diet` (`id_diet`) ON DELETE CASCADE;

--
-- Restrições para tabelas `meal_aliment`
--
ALTER TABLE `meal_aliment`
  ADD CONSTRAINT `fk_aliment_to_meal_aliment` FOREIGN KEY (`fk_aliment_id`) REFERENCES `aliment` (`id_aliment`),
  ADD CONSTRAINT `fk_meal_to_meal_aliment` FOREIGN KEY (`fk_meal_id`) REFERENCES `meal` (`id_meal`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
