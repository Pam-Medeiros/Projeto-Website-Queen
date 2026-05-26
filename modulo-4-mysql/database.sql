-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 06/05/2026 às 15:24
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
-- Banco de dados: `queen_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `capacity` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'default_event.jpg',
  `status` enum('disponivel','esgotado','cancelado') DEFAULT 'disponivel',
  `tickets_sold` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `location`, `price`, `capacity`, `image`, `status`, `tickets_sold`) VALUES
(1, 'Queen - Lisbon Rebirth', 'Uma noite inesquecível em Lisboa! Queen + Adam Lambert regressam à Altice Arena para uma das actuações mais aguardadas do ano. Com um palco imponente, efeitos visuais deslumbrantes e um alinhamento que percorre os maiores êxitos da banda — de Bohemian Rhapsody a We Are the Champions — esta é uma experiência que vai ficar na memória para sempre.', '2025-07-15 21:00:00', 'Altice Arena, Lisboa', 80.00, 500, 'src/images/Queen_one_last_ride.jpg', 'disponivel', 7),
(2, 'I Want To Break Free', 'Um tributo épico aos clássicos imortais dos Queen! Esta noite especial na Arena MEO celebra os 50 anos de uma das discografias mais icónicas do rock mundial. Com músicos de renome e uma produção espectacular, o alinhamento inclui os maiores sucessos dos anos 70, 80 e 90. Uma viagem no tempo que vai fazer toda a audiência cantar, dançar e emocionar-se.', '2026-07-25 21:30:00', 'Arena Meo', 75.00, 1000, 'src/images/Queen_I_Want_To_Break_Free.png', 'disponivel', 1),
(3, 'Queen - Rhapsody', 'O espectáculo Rhapsody Tour chega ao Porto Arena numa produção colossal com mais de 200 músicos em palco, orquestra sinfónica ao vivo, telões de alta definição e um sistema de som de última geração. Queen + Adam Lambert prometem superar todas as expectativas com surpresas exclusivas e raridades nunca antes tocadas ao vivo em Portugal.', '2026-09-01 21:30:00', 'Porto Arena', 75.00, 1000, 'src/images/Queen_Rhapsody_banner.jpg', 'disponivel', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed') DEFAULT 'pending',
  `total` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `status`, `total`) VALUES
(2, 4, '2026-02-10 21:37:49', 'completed', 66.96),
(3, 5, '2026-02-11 00:25:44', 'completed', 418.83),
(4, 6, '2026-02-11 00:32:13', 'pending', 15.99),
(5, 7, '2026-02-11 00:46:45', 'pending', 83.28),
(9, 11, '2026-04-14 21:00:10', 'pending', 155.00),
(10, 11, '2026-04-14 21:01:27', 'pending', 51.39),
(11, 10, '2026-04-14 21:18:24', 'completed', 230.00),
(12, 9, '2026-04-17 12:58:03', 'pending', 80.00),
(13, 9, '2026-05-05 16:54:01', 'pending', 400.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `item_type` varchar(10) NOT NULL DEFAULT 'product',
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `item_type`, `quantity`, `price`) VALUES
(3, 2, 12, 'product', 1, 15.99),
(4, 2, 5, 'product', 1, 12.99),
(5, 2, 6, 'product', 2, 18.99),
(6, 3, 12, 'product', 5, 15.99),
(7, 3, 6, 'product', 1, 18.99),
(8, 3, 8, 'product', 1, 19.90),
(9, 3, 9, 'product', 1, 299.99),
(10, 4, 12, 'product', 1, 15.99),
(11, 5, 2, 'product', 1, 15.30),
(12, 5, 5, 'product', 1, 12.99),
(13, 5, 11, 'product', 1, 35.00),
(14, 5, 10, 'product', 1, 19.99),
(20, 9, 2, 'event', 1, 75.00),
(21, 9, 1, 'event', 1, 80.00),
(22, 10, 3, 'product', 1, 15.50),
(23, 10, 8, 'product', 1, 19.90),
(24, 10, 12, 'product', 1, 15.99),
(25, 11, 1, 'event', 1, 80.00),
(26, 11, 2, 'event', 1, 75.00),
(27, 11, 3, 'event', 1, 75.00),
(28, 12, 1, 'event', 1, 80.00),
(29, 13, 1, 'event', 5, 80.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 50,
  `category` varchar(50) DEFAULT 'Geral',
  `image` varchar(255) DEFAULT 'src/images/default-product.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `category`, `image`, `created_at`) VALUES
(1, 'T-shirt A Night At The Opera', 'Camiseta 100% algodão. Disponível em vários tamanhos.', 14.00, 100, 'Vestuário', 'src/images/produtos/T-shirt_A_Night_At The_Opera_Unisex.jpg', '2026-02-09 14:53:37'),
(2, 'T-Shirt Classic Crest Unisex Navy Blue', 'T-shirt com material de alta qualidade.', 15.30, 99, 'Vestuário', 'src/images/produtos/T-shirt_Classic_Crest_Unisex_Navy_Blue.jpg', '2026-02-09 14:53:37'),
(3, 'T-Shirt Don\'t Stop Me Now Black', 'Camiseta preta unissex com estampa da música icônica Don\'t Stop Me Now.', 15.50, 74, 'Vestuário', 'src/images/produtos/T-shirt_Don\'t_Stop_Me_Now_Unisex_Black.webp', '2026-02-09 14:53:37'),
(4, 'T-Shirt In Concert Unisex Black', 'Camiseta preta unissex com estampa do Queen em concerto, estilo clássico', 15.30, 60, 'Vestuário', 'src/images/produtos/T-shirt_In_Concert_Unisex_Black.jpg', '2026-02-09 14:53:37'),
(5, 'Caneca Queen Crest', 'Caneca de cerâmica premium com o brasão oficial da banda. Capacidade de 350ml.', 12.99, 28, 'Merch', 'src/images/produtos/caneca-crest.webp', '2026-02-09 14:53:37'),
(6, 'Boné Queen Logo', 'Boné ajustável com bordado do logo Queen. Design clássico e confortável.', 18.99, 47, 'Acessórios', 'src/images/produtos/Bone_Logo_Black.png', '2026-02-09 14:53:37'),
(7, 'Boné Classic Brasão', 'Boné preto com o clássico brasão do Queen, estilo casual e unissex.', 18.00, 100, 'Acessórios', 'src/images/produtos/Bone_Classic_Black_UNI.webp', '2026-02-09 14:53:37'),
(8, 'Chapéu Face It Alone Black', 'Chapéu preto com estampa Face It Alone, estilo unissex.', 19.90, 53, 'Acessórios', 'src/images/produtos/Chapeu_Face_it_Alone_Black.webp', '2026-02-09 14:53:37'),
(9, 'Box Set - The Studio Collection', 'Box completo com todos os 15 álbuns de estúdio remasterizados em vinil de 180g. Edição limitada.', 299.99, 4, 'Vinis', 'src/images/produtos/Box_Set_The_Studio_Collection.webp', '2026-02-09 14:53:37'),
(10, 'DVD - Live at Wembley Stadium', 'Performance completa do Queen no Wembley Stadium em 1986. Inclui extras e entrevistas exclusivas.', 19.99, 4, 'DVD', 'src/images/produtos/DVD_Live_at_Wembley_Stadium.jpg', '2026-02-09 14:53:37'),
(11, 'Livro - Queen: The Complete Works', 'Biografia completa da banda com centenas de fotografias exclusivas e histórias dos bastidores.', 35.00, 4, 'Livros', 'src/images/produtos/Livro_The_Complete_Works.webp', '2026-02-09 14:53:37'),
(12, 'Funko - Freddie Mercury', 'Crie uma experiência musical inesquecível ao dar as boas-vindas a Freddy Mercury.', 15.99, 7, 'Coleção', 'src/images/produtos/funko_freddie.jpg', '2026-02-10 19:43:51');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(4, 'Teste', 'teste@teste.com', '$2y$10$Cb0nrmjOZMeNJd9xVtfVxuV7oKUIokiyREUz.9CHnHeX.Amdd5ir.', 'user', '2026-02-10 21:35:39'),
(5, 'deco', 'deco@gmail.com', '$2y$10$aQH6syJXFJwJPMGQZEtb.u2BwKG20.J8FOQWQZvNOwwSuXwcwEeAa', 'user', '2026-02-11 00:23:45'),
(6, 'paolahmedeiros', 'paola.medeiros.pt@gmail.com', '$2y$10$7aflkLwgZQIVvJWzIW29M.yvjFxy1eCB3mBkmh37f86itSyAcSYM2', 'user', '2026-02-11 00:30:41'),
(7, 'Bruno Penna', 'bcpennaecosta@gmail.com', '$2y$10$.z1Vhmcce3RqEB2mc1s8M.xbIToOLCPlXOc7sZ51hpKTPiDlAiuDq', 'user', '2026-02-11 00:44:26'),
(9, 'admin', 'admin@admin.com', '$2y$10$mdPNWovAK1I/uIRixA6AmeLfH3/oD9HNE3LY2JIShl1YtqX5us2b.', 'admin', '2026-04-12 16:35:57'),
(10, 'Pamela', 'pamela@teste.com', '$2y$10$8nZSWA4iVhaznojuZxXkbOV73ZMGhMyV.y.hyxFAcDEQzbfXmU/pa', 'user', '2026-04-14 19:12:26'),
(11, 'Miriam Medeiros', 'miriamedeiros67@gmail.com', '$2y$10$NHsh802vzpz3bzCxgLt8zeTFxJwFBbhOSG1fA/vDQzGVOJ2SXwcf6', 'user', '2026-04-14 20:59:11');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
