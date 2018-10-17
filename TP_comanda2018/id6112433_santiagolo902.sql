-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 13-07-2018 a las 16:31:03
-- Versión del servidor: 10.1.30-MariaDB
-- Versión de PHP: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `id6112433_santiagolo902`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id` int(32) NOT NULL,
  `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `clave` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `tipo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id`, `email`, `clave`, `nombre`, `tipo`, `estado`) VALUES
(1, 'admin@comanda.com', '$2y$10$buLlXTR2hy1yVyyWw5eKHu8NgMgJSGznVblDVHuR8T.NTG.XfHCRe', 'santiago', 'socio', 'activo'),
(2, 'cocinero1@comanda.com', '$2y$10$rtiS9hByDVEQgGJTtUpzA.kln/tFWq6LB5wcj7ugRjI.ys3FglZVW', 'cocinero1', 'cocinero', 'activo'),
(3, 'cocinero2@comanda.com', '$2y$10$uB7sbDFCjsX9lYr9RxalPeLCigLwbwEWZVrIUh9zYPtWkz6OC97WC', 'cocinero2', 'cocinero', 'activo'),
(4, 'bartender1@comanda.com', '$2y$10$5fvqUnjdaiYL7TDNnUA/2.Uoljs33O2tNYqXvbXAA8S0HZ5fWzk76', 'bartender1', 'bartender', 'activo'),
(5, 'bartender2@comanda.com', '$2y$10$DeMttZdN9OxgV/FHT3/QmuEWy0mRw6p9imWZal.deoDTxvMqvADaC', 'bartender2', 'bartender', 'activo'),
(6, 'mozo1@comanda.com', '$2y$10$Lpt4d3ohImRVtN/jOm8bKeSL/2NXwluLVyTLulkVXpk6mvlf/jYDW', 'mozo1', 'mozo', 'activo'),
(7, 'mozo2@comanda.com', '$2y$10$hVIWGO9xZzKigZzaj6z4S.w2IsoIPD8luuK9gBgnla.1X7BwU8YFe', 'mozo2', 'mozo', 'activo'),
(8, 'cervecero1@comanda.com', '$2y$10$pLegbyQY7rwYOSXm3CVbxew9jGxanegVh/Is/1Q0PPHcLDr2NRYw6', 'cervecero1', 'cervecero', 'activo'),
(9, 'cervecero2@comanda.com', '$2y$10$GberjRYIe/43UCJxntX0ve.lHWsT94Bp2pq5vcPjjVvAnUoUl.sWK', 'cervecero2', 'cervecero', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuestas`
--

CREATE TABLE `encuestas` (
  `id` int(50) NOT NULL,
  `idPedido` int(50) NOT NULL,
  `nroMesa` varchar(50) NOT NULL,
  `estado_encuesta` varchar(50) NOT NULL,
  `puntos_mesa` int(50) DEFAULT NULL,
  `puntos_restaurante` int(50) DEFAULT NULL,
  `puntos_mozo` int(50) DEFAULT NULL,
  `puntos_cocinero` int(50) DEFAULT NULL,
  `comentario` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `encuestas`
--

INSERT INTO `encuestas` (`id`, `idPedido`, `nroMesa`, `estado_encuesta`, `puntos_mesa`, `puntos_restaurante`, `puntos_mozo`, `puntos_cocinero`, `comentario`) VALUES
(2, 1035, 'm0010', 'Finalizada', 6, 6, 6, 6, 'eeeeeeeeeeeeeeeeee');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_login`
--

CREATE TABLE `historial_login` (
  `id` int(32) NOT NULL,
  `idEmpleado` int(32) NOT NULL,
  `fecha` varchar(50) NOT NULL,
  `hora` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `historial_login`
--

INSERT INTO `historial_login` (`id`, `idEmpleado`, `fecha`, `hora`) VALUES
(1, 1, '2018-07-04', '14:58:50'),
(2, 1, '2018-07-04', '14:59:18'),
(3, 4, '2018-07-04', '15:16:16'),
(4, 6, '2018-07-04', '15:16:29'),
(5, 6, '2018-07-04', '15:16:41'),
(6, 1, '2018-07-04', '15:16:50'),
(7, 4, '2018-07-04', '15:20:24'),
(8, 6, '2018-07-04', '15:21:01'),
(9, 1, '2018-07-04', '15:21:18'),
(10, 6, '2018-07-04', '15:32:07'),
(11, 2, '2018-07-04', '15:45:32'),
(12, 6, '2018-07-04', '15:49:14'),
(13, 4, '2018-07-04', '15:49:35'),
(14, 6, '2018-07-05', '15:50:03'),
(15, 1, '2018-07-04', '15:50:25'),
(16, 1, '2018-07-04', '16:49:02'),
(17, 1, '2018-07-04', '17:23:06'),
(18, 8, '2018-07-04', '18:55:08'),
(19, 1, '2018-07-04', '18:55:15'),
(20, 1, '2018-07-05', '19:31:50'),
(21, 1, '2018-07-04', '20:20:38'),
(22, 8, '2018-07-04', '20:22:16'),
(23, 1, '2018-07-04', '20:27:04'),
(24, 1, '2018-07-04', '20:27:19'),
(25, 2, '2018-07-04', '20:31:44'),
(26, 4, '2018-07-04', '20:32:00'),
(27, 1, '2018-07-04', '20:34:24'),
(28, 6, '2018-07-04', '20:34:30'),
(29, 1, '2018-07-04', '20:34:43'),
(30, 1, '2018-07-05', '00:13:24'),
(31, 6, '2018-07-05', '00:28:55'),
(32, 1, '2018-07-05', '00:33:13'),
(33, 6, '2018-07-05', '00:33:29'),
(34, 8, '2018-07-05', '00:36:35'),
(35, 4, '2018-07-05', '00:37:08'),
(36, 6, '2018-07-05', '00:40:14'),
(37, 6, '2018-07-05', '00:43:07'),
(38, 1, '2018-07-05', '00:54:05'),
(39, 2, '2018-07-05', '00:54:26'),
(40, 4, '2018-07-05', '00:55:04'),
(41, 5, '2018-07-05', '00:55:24'),
(42, 4, '2018-07-05', '00:55:40'),
(43, 8, '2018-07-05', '00:55:47'),
(44, 6, '2018-07-05', '00:56:16'),
(45, 1, '2018-07-05', '00:56:53'),
(46, 1, '2018-07-05', '03:20:02'),
(47, 6, '2018-07-05', '04:09:37'),
(48, 1, '2018-07-05', '04:10:07'),
(49, 6, '2018-07-05', '04:28:46'),
(50, 2, '2018-07-05', '04:29:15'),
(51, 6, '2018-07-05', '04:29:32'),
(52, 1, '2018-07-05', '04:29:41'),
(53, 1, '2018-07-06', '03:36:35'),
(54, 1, '2018-07-08', '05:21:30'),
(55, 1, '2018-07-08', '05:49:11'),
(56, 1, '2018-07-08', '05:50:42'),
(57, 1, '2018-07-08', '05:50:49'),
(58, 1, '2018-07-08', '05:54:15'),
(59, 6, '2018-07-08', '05:54:21'),
(60, 6, '2018-07-08', '05:54:28'),
(61, 6, '2018-07-08', '05:54:36'),
(62, 6, '2018-07-08', '05:54:48'),
(63, 6, '2018-07-08', '05:56:14'),
(64, 1, '2018-07-08', '05:56:27'),
(65, 1, '2018-07-08', '06:17:26'),
(66, 1, '2018-07-08', '06:18:44'),
(67, 1, '2018-07-08', '06:20:13'),
(68, 1, '2018-07-08', '06:21:33'),
(69, 6, '2018-07-08', '06:23:12'),
(70, 1, '2018-07-08', '06:23:17'),
(71, 1, '2018-07-08', '06:24:20'),
(72, 6, '2018-07-08', '07:36:39'),
(73, 8, '2018-07-08', '07:38:44'),
(74, 4, '2018-07-08', '07:38:53'),
(75, 6, '2018-07-08', '07:39:01'),
(76, 2, '2018-07-08', '08:09:38'),
(77, 8, '2018-07-08', '08:09:46'),
(78, 4, '2018-07-08', '08:09:52'),
(79, 6, '2018-07-08', '08:10:06'),
(80, 6, '2018-07-08', '08:11:38'),
(81, 1, '2018-07-08', '08:17:20'),
(82, 1, '2018-07-08', '08:17:42'),
(83, 1, '2018-07-08', '08:31:56'),
(84, 1, '2018-07-08', '20:27:24'),
(85, 2, '2018-07-08', '21:28:39'),
(86, 6, '2018-07-08', '21:28:44'),
(87, 2, '2018-07-08', '21:29:34'),
(88, 6, '2018-07-08', '21:29:49'),
(89, 1, '2018-07-08', '21:30:19'),
(90, 8, '2018-07-08', '21:32:59'),
(91, 6, '2018-07-08', '21:33:24'),
(92, 1, '2018-07-08', '21:33:32'),
(93, 1, '2018-07-08', '21:55:02'),
(94, 1, '2018-07-09', '00:04:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesa`
--

CREATE TABLE `mesa` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `mesa`
--

INSERT INTO `mesa` (`id`, `codigo`, `estado`) VALUES
(1, 'm0001', 'libre'),
(2, 'm0002', 'libre'),
(3, 'm0003', 'libre'),
(4, 'm0004', 'libre'),
(5, 'm0005', 'libre'),
(6, 'm0006', 'libre'),
(7, 'm0007', 'libre'),
(8, 'm0008', 'libre'),
(9, 'm0009', 'libre'),
(10, 'm0010', 'libre');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `idPedido` int(32) NOT NULL,
  `nroMesa` varchar(32) NOT NULL,
  `cliente` varchar(50) NOT NULL,
  `foto` varchar(100) NOT NULL,
  `importe` int(32) NOT NULL,
  `fecha` varchar(50) NOT NULL,
  `detalleBar` varchar(50) DEFAULT NULL,
  `tiempo_estimado_bar` int(50) DEFAULT NULL,
  `tiempo_final_bar` int(50) DEFAULT NULL,
  `estadoBar` varchar(50) DEFAULT NULL,
  `idEmpladoBar` int(50) DEFAULT NULL,
  `detalleCer` varchar(50) DEFAULT NULL,
  `tiempo_estimado_cer` int(50) DEFAULT NULL,
  `tiempo_final_cer` int(50) DEFAULT NULL,
  `estadoCer` varchar(50) DEFAULT NULL,
  `idEmpladoCer` int(50) DEFAULT NULL,
  `detalleCoc` varchar(50) DEFAULT NULL,
  `tiempo_estimado_coc` int(50) DEFAULT NULL,
  `tiempo_final_coc` int(50) DEFAULT NULL,
  `estadoCoc` varchar(50) DEFAULT NULL,
  `idEmpladoCoc` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`idPedido`, `nroMesa`, `cliente`, `foto`, `importe`, `fecha`, `detalleBar`, `tiempo_estimado_bar`, `tiempo_final_bar`, `estadoBar`, `idEmpladoBar`, `detalleCer`, `tiempo_estimado_cer`, `tiempo_final_cer`, `estadoCer`, `idEmpladoCer`, `detalleCoc`, `tiempo_estimado_coc`, `tiempo_final_coc`, `estadoCoc`, `idEmpladoCoc`) VALUES
(1022, 'm0001', 'mario', 'fotos/mario20180704154345.png', 200, '2018-07-02', 'fernet', 10, -39, 'Finalizado', 4, 'nada', NULL, NULL, 'Sin pedido', NULL, 'vacio', 50, 1, 'Finalizado', 2),
(1023, 'm0001', 'juan', 'fotos/juan.jpeg', 150, '2018-07-03', 'martini', 10, -45, 'Finalizado', 4, 'nada', NULL, NULL, 'Sin pedido', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL),
(1024, 'm0002', 'pedro', 'fotos/pedro.jpeg', 200, '2018-07-03', 'vinoblanco', 10, -45, 'Finalizado', 5, 'nada', NULL, NULL, 'Sin pedido', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL),
(1025, 'm0003', 'mario', 'fotos/mario.jpeg', 100, '2018-07-04', 'fernet', NULL, NULL, 'Sin pedido', NULL, 'rubia', 10, -46, 'Finalizado', 8, 'nada', NULL, NULL, 'Sin pedido', NULL),
(1026, 'm0004', 'pepe', 'fotos/pepe.jpeg', 100, '2018-07-04', 'nada', NULL, NULL, 'Sin pedido', NULL, 'negra', 10, -46, 'Finalizado', 8, 'nada', NULL, NULL, 'Sin pedido', NULL),
(1027, 'm0005', 'jaime', 'fotos/jaime.jpeg', 250, '2018-07-04', 'nada', NULL, NULL, 'Sin pedido', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL, 'milanesa', 5, -49, 'Finalizado', 2),
(1028, 'm0006', 'maria', 'fotos/maria.jpeg', 400, '2018-07-04', 'nada', NULL, NULL, 'Sin pedido', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL, 'pizza', 10, -44, 'Finalizado', 2),
(1029, 'm0010', 'juan', 'fotos/juan20180705042908.jpg', 250, '2018-07-05', 'nada', NULL, NULL, 'Sin pedido', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL, 'pizza', 10, -19, 'Finalizado', 2),
(1030, 'm0001', 'mario', 'fotos/mario20180708073704.jpg', 105, '2018-07-08', 'nada', NULL, NULL, 'Sin pedido', NULL, 'negra', NULL, NULL, 'Cancelado', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL),
(1031, 'm0001', 'juan', 'fotos/juan20180708081031.jpg', 50, '2018-07-08', 'vinotinto', NULL, NULL, 'Cancelado', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL),
(1032, 'm0002', 'pedro', 'fotos/pedro20180708081203.jpg', 11, '2018-07-08', 'nada', NULL, NULL, 'Sin pedido', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL, 'pizza', NULL, NULL, 'Cancelado', NULL),
(1033, 'm0001', 'mario perez', 'fotos/mario perez20180708081327.jpg', 55, '2018-07-08', 'vinoblanco', NULL, NULL, 'Cancelado', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL),
(1034, 'm0010', 'mario', 'fotos/mario20180708212904.jpg', 150, '2018-07-08', 'nada', NULL, NULL, 'Sin pedido', NULL, 'nada', NULL, NULL, 'Sin pedido', NULL, 'pizza', 5, -24, 'Finalizado', 2),
(1035, 'm0010', 'pedro', 'fotos/pedro20180708213243.jpg', 50, '2018-07-08', 'nada', NULL, NULL, 'Sin pedido', NULL, 'roja', 10, -23, 'Finalizado', 8, 'nada', NULL, NULL, 'Sin pedido', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historial_login`
--
ALTER TABLE `historial_login`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesa`
--
ALTER TABLE `mesa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`idPedido`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `encuestas`
--
ALTER TABLE `encuestas`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `historial_login`
--
ALTER TABLE `historial_login`
  MODIFY `id` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT de la tabla `mesa`
--
ALTER TABLE `mesa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `idPedido` int(32) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1036;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
