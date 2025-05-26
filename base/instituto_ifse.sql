-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-05-2025 a las 01:26:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `instituto_ifse`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cursos`
--

CREATE TABLE `cursos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_hours` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cursos`
--

INSERT INTO `cursos` (`id`, `titulo`, `description`, `imagen`, `price`, `duration_hours`) VALUES
(1, 'GUARDIA NIVEL I', 'Es un nivel básico y obligatorio para quienes desean desempeñarse como guardias de seguridad. No portan armas en la mayoría de los casos', 'img/nivel1.jpg', 120.00, 120),
(2, 'GUARDIA NIVEL II', 'Es un curso que requiere conocimientos y habilidades más especiales. El uso de armas está reservado para este nivel.', 'img/nivel2.jpg', 170.00, 60),
(3, 'REENTRENAMIENTO', 'Es una capacitación periódica que debe realizarse cada 2 años. El requisito es tener acreditación Nivel I.', 'img/reentrenamiento.jpg', 45.00, 20),
(4, 'MANEJO DE CONSOLAS DE VIDEO VIGILANCIA', 'Los operadores de CCTV cumplen un rol preventivo y de respuesta ante incidentes mediante la observación, análisis y riesgo de imágenes en tiempo real.', 'img/consolas.jpg', 50.00, 20),
(5, 'SEGURIDAD FINANCIERA', 'Especialízate en lo relacionado a las acciones, procesos, protocolos en instituciones financiera.', 'img/sfinanciera.jpg', 140.00, 60),
(6, 'SUPERVISOR', 'Profesional encargado de coordinar, controlar y supervisar al personal operativo (guardias)', 'img/supervisor.jpg', 190.00, 60),
(7, 'BARES Y RESTAURANTES', 'Tiene como objeto proteger la integridad de los clientes, el personal y los bienes de un establecimiento. ', 'img/bares.jpg', 110.00, 50),
(8, 'CONTROL DE EVENTOS Y ESCENARIOS', 'Se refiere a la vigilancia y protección de personas, instalaciones y bienes durante eventos masivos como: conciertos, ferias, etc.', 'img/eventos.jpg', 120.00, 60);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` datetime DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `matriculas`
--

INSERT INTO `matriculas` (`id`, `user_id`, `course_id`, `enrollment_date`, `payment_method`, `status`) VALUES
(1, 13, 1, '2025-05-23 20:30:07', 'deposito_bancario', 'completado'),
(2, 13, 3, '2025-05-23 20:30:07', 'deposito_bancario', 'completado'),
(3, 14, 2, '2025-05-25 00:41:01', 'deposito_bancario', 'completado'),
(4, 14, 3, '2025-05-25 00:41:01', 'deposito_bancario', 'completado'),
(5, 14, 2, '2025-05-25 00:41:01', 'deposito_bancario', 'en curso');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `edad` int(3) DEFAULT NULL,
  `sexo` varchar(10) DEFAULT NULL,
  `cedula` varchar(10) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `activation_code` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombres`, `email`, `password`, `edad`, `sexo`, `cedula`, `telefono`, `is_active`, `activation_code`, `reset_token`, `reset_expires_at`, `created_at`) VALUES
(11, 'Patricia Terán', 'pteran@hotmail.com', '$2y$10$pDdG1FJRtRcoU4scZB4j8O7l2/lBX19A/cf1eEQSyRIEb8ROPZG5S', 24, 'Femenino', '1720567543', '2999999', 0, '42bcc7b9e4ddeb0b789325dfc214a419c7ad3e325d108f4cf3442176ffbe6f21f7ff672dc132449ca4760c623a2a150ca3159d3cd0cdb9beb5bb0be2f94ac4c8', NULL, NULL, '2025-05-21 14:49:30'),
(12, 'Patricia Terán 2222', 'carlos7@colegio.com', '$2y$10$ikQ2gpSPfXZe2RaX6efDXu0V9SzkyM5Qmcue/96SJMPVdIbKmuTaq', 31, 'Femenino', '1720568543', '24555789', 0, '51a4b0556b70897e133c0bf414af329dd53c744fad9cd26b92286cf99181e8198acb52038ff234a72c2a03cec1ebd0779b3c45fe1f6a262ac9ca86e05d7b9b0f', NULL, NULL, '2025-05-21 14:51:32'),
(13, 'Lucas Novato', 'lucasnovato@hotmail.com', '$2y$10$PqOP7T1UYttsP7hKl0bmkOqyovMSdsx5ESVWDEXJl0VqFWWSJOdpq', 29, 'Masculino', '1712584822', '2455963', 1, '32e65c7b93a9ae99c1163972d82bd03f9ad6759235b439b16bc741a2608f6390d60fb7210c0ab109e8d6d90e937a2b3d7b401e8240de01fe757c1c46803e95d2', '096d8df5ae34f775573343f337129ecc030eb3a4ad3870be50e77afe849473c1979dd825f14cf9fd1d56b71e1471f4ae5734626b88ff969e49e9e0e65bea650d', '2025-05-22 07:02:13', '2025-05-21 14:53:37'),
(14, 'Anita Vallejo', 'ana@verdes.com', '$2y$10$whMBrgwaDL4egBei2exzE.Zo0FnzlwKjBLhH2e1CD1gC6axN0G39.', 36, 'Femenino', '1710899947', '24557000', 1, 'c614705446e97e7ec115abe738675334a992c6e85588a94949adb39abb7aab4ae9f6591482419a0db56a265ec5eb96df8d344965287effe1ebbfdcb0bbedd563', '4bbd323366745946b9eaedaeec8e44bddca8aea4a7f7a97d8a50a1d79775822e103d98a7cf9929cd3c29b29e8e512839e08ff27921d35f68944f745c0b7dc326', '2025-05-26 02:08:42', '2025-05-24 01:10:17'),
(15, 'Leo Montero', 'lftipantiza@utpl.edu.ec', '$2y$10$KWIp.44nMVgFF5tHqPNo1.iy8WwYACrJVq.Mb8Tlip/h5SWQN2Mj6', 44, 'Masculino', '1713787844', '2900709', 0, '807c81e2e738b74581303474d80af2deb26a039c2b06f1b6f9a4076d0b1bed4f2d51ccfe4cb5dbbfd50557e9f0d0b377dcea590b143d4c3f3e49ec9500192c65', NULL, NULL, '2025-05-25 15:51:45'),
(16, 'Bolo Santana', 'bolo@verdes.com', '$2y$10$hI4K4kLzpkfDi7nXLItUYONVx63kuJaNlyV3Sff0mNk.nKO4lKS5y', 47, 'Masculino', '1713254584', '2900125', 1, 'ebcb99f7e200cbcf201c187cab50979578e2de1424c0d5720a89762fa9ed9bea257e15088d4be6fef8c7e8f3161b2f950cca16088a2f0180ec1f5b039f5249b6', NULL, NULL, '2025-05-25 17:29:05'),
(17, 'Santy Males', 'santym@verdes.com', '$2y$10$ab4SvGSSkS3wMBwA.WpbPOVgu8b2FfiKBYj1IP4WWxfTDIKAT71o.', 33, 'Masculino', '1710899955', '24557000', 1, 'dbe9d435859c18908852bac4560cfe5a90f6f91aa077c44f4de9ef06a5c950f65b03e5a6526a73d99d40412d14dd7d0e5a8df2eaf7e98f84b1c70d5eebbdbb6f', NULL, NULL, '2025-05-25 17:35:57'),
(18, 'Santy Mijares', 'smijares@verdes.com', '$2y$10$rT36WfWCapYuCF4lS0ZD7u/gvmWu1yoTpx2oZJlSjWSfW4Plx.te2', 33, 'Masculino', '0992541222', '0995621402', 1, 'ae638b17610e801c44a0488aa738dcaadc15f15dc7921fa1c14cfb1f61aaa6ea4f2b1f36d9ff586042a73f22519d85d130162f20f3f0c051134f2173c060b037', NULL, NULL, '2025-05-25 18:00:50');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indices de la tabla `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `cursos`
--
ALTER TABLE `cursos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `cursos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
