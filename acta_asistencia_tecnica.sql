-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 26-12-2025 a las 16:24:18
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
-- Base de datos: `acta_asistencia_tecnica`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actas`
--

CREATE TABLE `actas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fecha` date NOT NULL,
  `establecimiento_id` bigint(20) UNSIGNED NOT NULL,
  `responsable` varchar(255) NOT NULL,
  `tema` varchar(255) NOT NULL,
  `modalidad` varchar(255) NOT NULL,
  `implementador` varchar(255) NOT NULL,
  `tipo` varchar(191) NOT NULL DEFAULT 'asistencia',
  `firmado_pdf` varchar(255) DEFAULT NULL,
  `firmado` tinyint(1) NOT NULL DEFAULT 0,
  `imagen1` varchar(255) DEFAULT NULL,
  `imagen2` varchar(255) DEFAULT NULL,
  `imagen3` varchar(255) DEFAULT NULL,
  `imagen4` varchar(255) DEFAULT NULL,
  `imagen5` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actas`
--

INSERT INTO `actas` (`id`, `user_id`, `fecha`, `establecimiento_id`, `responsable`, `tema`, `modalidad`, `implementador`, `tipo`, `firmado_pdf`, `firmado`, `imagen1`, `imagen2`, `imagen3`, `imagen4`, `imagen5`, `created_at`, `updated_at`) VALUES
(1, NULL, '2025-08-31', 77, 'AMELIA LOPEZ ALVA', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/0vLWwP4ay6tmKjNDrEeh1yy8YyvE3uXQRkgjeFSN.pdf', 1, 'actas/1759269505_68dc5281ae525_img1.jpg', NULL, NULL, NULL, NULL, '2025-09-30 21:58:25', '2025-10-11 02:01:37'),
(2, NULL, '2025-09-30', 86, 'GILDA ÑAUPA CUBA', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/3RrJkWuG1jidTKolWsD4zMtMT0RwSbzvJh6XWxhE.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-09-30 22:10:35', '2025-10-01 02:40:33'),
(3, NULL, '2025-09-30', 86, 'GILDA ÑAUPA CUBA', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/2IAmmfdFPeMT4JORH2xUecgrRnBMY7jePIF9upwn.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-09-30 22:12:50', '2025-10-01 02:40:41'),
(4, NULL, '2025-08-31', 96, 'LUIS ENRIQUE TENORIO AGUADO', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', NULL, 0, 'actas/1759365723_68ddca5b28d85_img1.png', 'actas/1759365723_68ddca5b28f36_img2.png', NULL, NULL, NULL, '2025-09-30 22:14:36', '2025-10-11 02:01:12'),
(5, NULL, '2025-09-30', 86, 'GILDA ÑAUPA CUBA', 'Reactivación de módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/L6euJmel9TbElcJKskpGUml2ImHSqeQvz6HUgowg.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-09-30 22:16:40', '2025-10-01 02:40:50'),
(6, NULL, '2025-09-30', 102, 'CINTHYA SANDRA BAUTISTA RAMOS', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/KYs3aHzkg3CJnFXdt4JDUJwLAWXYRGNP7yDPeo38.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-09-30 22:18:23', '2025-10-01 02:40:57'),
(7, NULL, '2025-09-30', 100, 'ELIZABETH MARIA TORRES PEÑA', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/JRnPJ5Tr73oKN2ynQ9HjlWRMEY8DHuVJIbc44RSc.pdf', 1, 'actas/1759290211_68dca363d087e_img1.jpeg', NULL, NULL, NULL, NULL, '2025-09-30 22:19:12', '2025-10-11 02:00:50'),
(8, NULL, '2025-09-29', 29, 'CAROLA PILAR ORTIZ VILLAFUERTE', 'Reactivación de módulo', 'Presencial', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/VFVoAYXj7AmaSicWvXIe8y0uEWvFZTKvDC1ogTsq.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-09-30 22:20:27', '2025-10-11 02:00:29'),
(9, NULL, '2025-09-23', 119, 'AMELIA FERNANDA SORIA SARAVIA', 'Otros', 'Presencial', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/zX2Z11uepp0RscT0TRwKju1m1rWaoPkDtTd6zePQ.pdf', 1, 'actas/1759416136_68de8f4830e05_img1.jpeg', NULL, NULL, NULL, NULL, '2025-09-30 22:21:15', '2025-10-06 01:20:26'),
(10, NULL, '2025-09-25', 131, 'VILMA ARIAS MUNAYCO', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/vvZFmaFn9n2Ex6xax8ZDH5lITNzf9qRPK0Ny6PmE.pdf', 1, 'actas/1759416864_68de9220a0ad5_img1.jpeg', NULL, NULL, NULL, NULL, '2025-09-30 22:22:18', '2025-10-02 16:46:03'),
(11, NULL, '2025-09-30', 108, 'BETZABE JASMIN ORTIZ SALAZAR', 'Reactivación de módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/LY2RNnJK9X9ZfU8m1G5ObYJD9HpCQUOYT13afFyF.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-09-30 22:22:53', '2025-10-02 02:21:02'),
(12, NULL, '2025-09-30', 16, 'VICTOR MANUEL NUÑEZ SANCHEZ', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/Q3Rk2vyxiQIniAEzuuXGZzw4oiXX5s8pwA3kI8ep.pdf', 1, 'actas/1759289135_68dc9f2f3829a_img1.png', 'actas/1759289135_68dc9f2f383ba_img2.png', NULL, NULL, NULL, '2025-10-01 03:25:35', '2025-10-11 01:59:57'),
(13, NULL, '2025-10-01', 15, 'CARLOS RAUL TABER RAMOS', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/LCmagwOWxXWMoWrpWwLq16M22LOgPOS7ZlmvCESK.pdf', 1, 'actas/1759329027_68dd3b03db190_img1.png', 'actas/1759329027_68dd3b03db371_img2.png', NULL, NULL, NULL, '2025-10-01 14:30:27', '2025-10-11 01:59:36'),
(14, NULL, '2025-10-01', 49, 'EDIS MILAGRITOS JHONG CASTRO', 'Ingreso de nuevo personal', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/CmVFoBiF3j8InVqee34uWYt0r9a8s6K7UPotOeOA.pdf', 1, 'actas/1759338888_68dd6188f1dc4_img1.jpg', 'actas/1759338888_68dd6188f1ece_img2.jpg', NULL, NULL, NULL, '2025-10-01 17:14:48', '2025-10-02 03:47:50'),
(15, NULL, '2025-10-01', 49, 'EDIS MILAGRITOS JHONG CASTRO', 'Reactivación de módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/qj1P4iOTDtJTudCUAS2XaN0MhIGaaFDs3NyO7JlT.pdf', 1, 'actas/1759339206_68dd62c66f93c_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-01 17:20:06', '2025-10-02 03:49:13'),
(16, NULL, '2025-10-01', 49, 'EDIS MILAGRITOS JHONG CASTRO', 'Reactivación de módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/YClLB8oHo3s3mUXe3thH5wgxrnLOBHtZjbuFmz8v.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-01 17:26:21', '2025-10-02 03:49:33'),
(17, NULL, '2025-10-01', 49, 'EDIS MILAGRITOS JHONG CASTRO', 'Reactivación de módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/Kmb0moe6MZWSD0KgC5dVlS4ySRL7DrxLtL4L7NeU.pdf', 1, 'actas/1759340272_68dd66f0d6361_img1.jpg', 'actas/1759340272_68dd66f0d64ee_img2.jpg', NULL, NULL, NULL, '2025-10-01 17:32:25', '2025-10-02 03:49:55'),
(18, NULL, '2025-10-01', 75, 'FREDDY ALBERTO VILCA CHACALTANA', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/GlzphB3sCeJRJvE1LgQVTc1AIMaYsteXPZT5iEtV.pdf', 1, 'actas/1759357937_68ddabf1416d0_img1.jpeg', 'actas/1759357937_68ddabf141795_img2.jpeg', NULL, NULL, NULL, '2025-10-01 22:32:17', '2025-10-02 18:30:34'),
(19, NULL, '2025-10-01', 75, 'FREDDY ALBERTO VILCA CHACALTANA', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/Ur55p1JUgPdiUNytStgEc3WWtyWUuT2ttQB8SpDd.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-01 22:38:02', '2025-10-02 18:30:25'),
(20, NULL, '2025-10-01', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/Uv7NUHQXqxljt30w14K3xmqj5ViZKiyLwg8OdqMl.pdf', 1, 'actas/1759364370_68ddc512e55bc_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-01 22:46:53', '2025-10-02 19:59:38'),
(21, NULL, '2025-09-29', 35, 'JORGE RODOLFO CHACALTANA SUAREZ', 'Reactivación de módulo', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/p5gLGMGtgz28cmDB8H6uHTVcdSCFNlTDhmdzwLSR.pdf', 1, 'actas/1759359759_68ddb30fb2392_img1.jpeg', 'actas/1759359759_68ddb30fb2486_img2.jpeg', NULL, NULL, NULL, '2025-10-01 23:02:39', '2025-10-02 18:42:53'),
(22, NULL, '2025-09-27', 35, 'JORGE RODOLFO CHACALTANA SUAREZ', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/auGlCbBJOL3RztW2VMvm2uoqLQA785G0utD8cqqG.pdf', 1, 'actas/1759360573_68ddb63dd6c9a_img1.jpeg', 'actas/1759360573_68ddb63dd6d85_img2.jpeg', 'actas/1759360573_68ddb63dd6e12_img3.jpeg', NULL, NULL, '2025-10-01 23:16:13', '2025-10-02 18:56:52'),
(23, NULL, '2025-09-26', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/b1PLa8VM0gAme2IC7ez1TPIiLJQPQnMiJtw83hOS.pdf', 1, 'actas/1759430913_68dec90171470_img1.jpeg', 'actas/1759430913_68dec90171592_img2.jpeg', NULL, NULL, NULL, '2025-10-01 23:47:05', '2025-10-02 19:59:24'),
(24, NULL, '2025-09-25', 144, 'NIDIA BRAVO HERNANDEZ', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/zbNZy0hV7Z740VMVPvzh8yjUNT0ttO0sCJVhtod6.pdf', 1, 'actas/1759363677_68ddc25d40dce_img1.jpeg', 'actas/1759363677_68ddc25d40ec8_img2.jpeg', NULL, NULL, NULL, '2025-10-02 00:07:57', '2025-10-13 04:26:13'),
(25, NULL, '2025-09-18', 43, 'ERIKA PEREZ LUQUE', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', NULL, 0, 'actas/1759434078_68ded55e6b1c7_img1.jpeg', 'actas/1759434078_68ded55e6b33c_img2.jpeg', NULL, NULL, NULL, '2025-10-02 00:13:48', '2025-10-02 19:41:18'),
(26, NULL, '2025-09-25', 56, 'JESUS AYQUIPA SANTI', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/6IatuLAupj8GvXax0p8ezYM0BkaosBW5qDNS5ThM.pdf', 1, 'actas/1759365524_68ddc9947fbb8_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-02 00:17:22', '2025-10-13 04:40:18'),
(27, NULL, '2025-10-02', 78, 'LUIS JOSE PAREDES MALDONADO', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/8hkWGw46VEShEK6dVZFSetN8dn5lxW1dJh003iLf.pdf', 1, 'actas/1759366557_68ddcd9dde710_img1.png', 'actas/1759366557_68ddcd9dde845_img2.png', 'actas/1759366557_68ddcd9dde976_img3.png', NULL, NULL, '2025-10-02 00:55:57', '2025-10-11 01:59:00'),
(28, NULL, '2025-09-25', 75, 'FREDDY ALBERTO VILCA CHACALTANA', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/H6z30YFquHYq3FKIxAAL3NACUaDKG6zurz5CPxW5.pdf', 1, 'actas/1759379286_68ddff56cdcbb_img1.jpg', 'actas/1759379286_68ddff56cdd9f_img2.jpg', 'actas/1759379286_68ddff56cde19_img3.png', 'actas/1759379286_68ddff56cdfb8_img4.jpg', NULL, '2025-10-02 04:28:06', '2025-10-02 21:49:38'),
(29, NULL, '2025-09-30', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Reactivación de módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/7U8agaENvdIC25dZwUNz46Qworiuda5fCEHWvDaV.pdf', 1, 'actas/1759380034_68de024224b30_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-02 04:40:34', '2025-10-02 21:50:40'),
(30, NULL, '2025-09-26', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/TKsQfUL9Su6PHO93p2wZk9gf1ZeqvFz2yy4VcOMB.pdf', 1, 'actas/1759380339_68de0373800c5_img1.jpg', 'actas/1759380339_68de037380196_img2.jpg', 'actas/1759380339_68de0373801f0_img3.jpg', 'actas/1759380339_68de037380241_img4.jpg', NULL, '2025-10-02 04:45:39', '2025-10-02 21:50:13'),
(31, NULL, '2025-09-30', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/NyeJjlrNrPGaC8w4ku5zdJ773HOtp1pBWsO0Ulfn.pdf', 1, 'actas/1759381944_68de09b8c9206_img1.jpg', 'actas/1759381944_68de09b8c92fb_img2.jpg', 'actas/1759381944_68de09b8c93ad_img3.jpg', NULL, NULL, '2025-10-02 05:12:24', '2025-10-02 21:51:09'),
(32, NULL, '2025-09-27', 35, 'JORGE RODOLFO CHACALTANA SUAREZ', 'Reactivación de módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/ciRRgbMiaKYHptZOU4sEsTVdVPD4iVz68Mqflf3K.pdf', 1, 'actas/1759440707_68deef43105dd_img1.jpg', 'actas/1759440707_68deef43106ca_img2.jpg', 'actas/1759440707_68deef431075f_img3.jpg', 'actas/1759412485_68de81050c6e1_img4.jpg', 'actas/1759412485_68de81050c73e_img5.jpg', '2025-10-02 05:18:42', '2025-10-02 21:51:25'),
(33, NULL, '2025-09-24', 79, 'HILDA MILAGRITOS DE LA CRUZ CHIPANA', 'Reactivación de módulo', 'Telefónica', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/6iZUvxfv1NtlTYVxMwK5k5HQ1PQXBExDb9NDbyhO.pdf', 1, 'actas/1759382803_68de0d1342390_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-02 05:26:43', '2025-10-02 21:51:46'),
(34, NULL, '2025-09-24', 138, 'ALDO MARCELO MONGE REYES', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/Vfm91pEje9bMymtbj3OWkhTVnecmt8mVrpy62mPU.pdf', 1, 'actas/1759440390_68deee061e316_img1.jpg', 'actas/1759440390_68deee061e41a_img2.jpg', 'actas/1759440390_68deee061e4ad_img3.jpg', NULL, NULL, '2025-10-02 05:36:51', '2025-10-02 21:51:59'),
(35, NULL, '2025-09-24', 138, 'ALDO MARCELO MONGE REYES', 'Reactivación de módulo', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/r1YrHJHghzNZ6O5gzeshuU3o8NfQCkR26NmQdFsa.pdf', 1, 'actas/1759406960_68de6b7056bcc_img1.jpeg', 'actas/1759406960_68de6b7056d6b_img2.jpeg', NULL, NULL, NULL, '2025-10-02 12:09:20', '2025-10-02 18:13:57'),
(36, NULL, '2025-09-25', 114, 'EDITH ALEJANDRA LOPEZ TACAS', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/AqrVOmZ1ZkSIjABfzSArFfAxm7afeNyhJRsl7u4X.pdf', 1, 'actas/1759408822_68de72b63c248_img1.jpeg', 'actas/1759408822_68de72b63c348_img2.jpeg', NULL, NULL, NULL, '2025-10-02 12:40:22', '2025-10-02 20:03:06'),
(37, NULL, '2025-09-24', 8, 'CYNTHIA YACORI CORREA PEREZ', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/3VC3XaYLpK7LbnkcQMxrRlXxgL88NeggRFxFLH4m.pdf', 1, 'actas/1759409241_68de74591ba05_img1.jpeg', 'actas/1759409241_68de74591baed_img2.jpeg', NULL, NULL, NULL, '2025-10-02 12:47:21', '2025-10-02 18:16:09'),
(38, NULL, '2025-10-02', 100, 'ELIZABETH MARIA TORRES PEÑA', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/LAAOdyTvAH44GU7OMyLekxhuCAMdLJJTFeNaJeb0.pdf', 1, 'actas/1759411660_68de7dcc7b531_img1.png', 'actas/1759411660_68de7dcc7b666_img2.png', NULL, NULL, NULL, '2025-10-02 13:27:40', '2025-10-11 01:58:20'),
(39, NULL, '2025-10-02', 86, 'GILDA ÑAUPA CUBA', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/PG0aTHzaubN4nz7ez3b5ihOaHE3Ue8JVU95GKX4U.pdf', 1, 'actas/1759412655_68de81af78e79_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-02 13:44:15', '2025-10-11 16:28:59'),
(40, NULL, '2025-09-23', 138, 'ALDO MARCELO MONGE REYES', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/RxKs5GyJ1UUJu0g84LKoruotWYp53Reft6gc0LRM.pdf', 1, 'actas/1759440171_68deed2b928c0_img1.jpg', 'actas/1759440171_68deed2b92a16_img2.jpg', NULL, NULL, NULL, '2025-10-02 14:47:49', '2025-10-02 21:52:28'),
(41, NULL, '2025-09-19', 124, 'LOURDES AVILES ALFARO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/iUzaRqMtaJ9EFxrF6Xxepm1tmxqVaHkPL6b0kpcf.pdf', 1, 'actas/1759440904_68def0087e7ce_img1.jpg', 'actas/1759440904_68def0087e8f2_img2.jpg', 'actas/1759440904_68def0087e9ee_img3.jpg', NULL, NULL, '2025-10-02 14:56:05', '2025-10-02 21:52:41'),
(42, NULL, '2025-09-25', 119, 'AMELIA FERNANDA SORIA SARAVIA', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/yMehCZS72ZrQ4NWwnSP8Li6yBXyhKCzjcVzKGYN5.pdf', 1, 'actas/1759420544_68dea08059b95_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-02 15:55:44', '2025-10-06 01:20:02'),
(43, NULL, '2025-10-02', 35, 'JORGE RODOLFO CHACALTANA SUAREZ', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/IN8zKvuBLKBWC3K4LVvm6itJIGa7hxFd5X7craKy.pdf', 1, 'actas/1759420721_68dea13154ca1_img1.jpeg', 'actas/1759420721_68dea13154dba_img2.jpeg', 'actas/1759420721_68dea13154e5f_img3.jpeg', 'actas/1759420721_68dea13154ef3_img4.jpeg', NULL, '2025-10-02 15:58:41', '2025-10-02 18:42:38'),
(44, NULL, '2025-09-26', 65, 'JOSE CARLOS VALLE BRAVO', 'Otros', 'Presencial', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/aaf8CduHkDzgR40RUsqrmIcIsUw6HpVdIIACtFFF.pdf', 1, 'actas/1759421722_68dea51a8ce82_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-02 16:15:22', '2025-10-06 01:19:18'),
(45, NULL, '2025-10-02', 100, 'ELIZABETH MARIA TORRES PEÑA', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/n3vpRdxQ0K0GDzxOoT33RxOQg5VCNRqmCH0rAHSh.pdf', 1, 'actas/1759422404_68dea7c488138_img1.png', 'actas/1759422404_68dea7c4882d9_img2.png', NULL, NULL, NULL, '2025-10-02 16:26:44', '2025-10-11 01:57:57'),
(46, NULL, '2025-10-02', 56, 'JESUS AYQUIPA SANTI', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/oxcN4yGKbiTuOjmCe6BCMrmnDL0vpkC5opFEuDQ8.pdf', 1, 'actas/1759427282_68debad2afecb_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-02 17:48:02', '2025-10-13 04:40:09'),
(47, NULL, '2025-09-08', 35, 'JORGE RODOLFO CHACALTANA SUAREZ', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/aP3RpHuNPNYfcD5IXXiwOBuvMadX5DwWyW9norTA.pdf', 1, 'actas/1759436450_68dedea27eca0_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-02 20:20:50', '2025-10-02 20:37:56'),
(48, NULL, '2025-09-19', 124, 'LOURDES AVILES ALFARO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/Y1Oh0u5Qb064CrTqEmBRGNHfEuGU1qDSlbtd1ZQB.pdf', 1, 'actas/1759438058_68dee4ea175c3_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-02 20:47:38', '2025-10-02 20:51:50'),
(49, NULL, '2025-10-02', 86, 'GILDA ÑAUPA CUBA', 'Ingreso de nuevo personal', 'Presencial', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/zojCxtezVMiFVLyZr4mPWE7xNUoIoruiAI3s03pJ.pdf', 1, 'actas/1759440273_68deed91083d2_img1.jpg', 'actas/1759440273_68deed91084c3_img2.jpg', NULL, NULL, NULL, '2025-10-02 21:24:33', '2025-10-06 19:11:54'),
(50, NULL, '2025-10-02', 42, 'ENMA MARUJA DE LA CRUZ CARRASCO', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/pplD9912gMhGZfaXpM4RfucSi2EslByrybvu6BLL.pdf', 1, 'actas/1759450052_68df13c488de1_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-03 00:07:32', '2025-10-06 15:13:35'),
(51, NULL, '2025-10-03', 144, 'NIDIA BRAVO HERNANDEZ', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/2tpLg0Ba9wO9E6VixfRimnRyS1CvbVRiP6idUkqC.pdf', 1, 'actas/1759717306_68e327baf27dc_img1.jpg', 'actas/1759717306_68e327baf294f_img2.jpg', 'actas/1759717306_68e327baf2a3a_img3.jpg', NULL, NULL, '2025-10-03 15:13:22', '2025-10-06 03:23:00'),
(52, NULL, '2025-10-06', 43, 'ERIKA PEREZ LUQUE', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/OvS9jcCdNIhsM4r8khcPbmK2HtapxaiswBq0Qeq8.pdf', 1, 'actas/1759783172_68e4290404e2c_img1.jpg', 'actas/1759783172_68e4290404f6a_img2.jpg', NULL, NULL, NULL, '2025-10-03 17:09:34', '2025-10-12 00:51:34'),
(53, NULL, '2025-10-06', 79, 'MARTINEZ ASCONA JOSELITO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/Q1PC9s6G2AzjpotPrvB5uWSUJosN65AUNpfnIbl7.pdf', 1, 'actas/1759785899_68e433ab9ff06_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-03 17:09:41', '2025-10-12 00:29:57'),
(54, NULL, '2025-10-03', 8, 'CYNTHIA YACORI CORREA PEREZ', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/GobO14llWPrVhSV4PcMV1dI3wL5t8gbNs2HjQIvp.pdf', 1, 'actas/1759723111_68e33e6773b42_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-03 17:36:50', '2025-10-06 04:45:41'),
(55, NULL, '2025-10-03', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/xZIIYL0ips0xNm03i0Jvr2UbtIIITA4vdFR4wb5q.pdf', 1, 'actas/1759723903_68e3417fa707b_img1.jpg', 'actas/1759723903_68e3417fa7198_img2.jpg', 'actas/1759723903_68e3417fa7229_img3.jpg', 'actas/1759723903_68e3417fa72c1_img4.jpg', NULL, '2025-10-03 21:40:30', '2025-10-06 04:24:37'),
(56, NULL, '2025-10-06', 38, 'CLOTILDE GUILLERMINA CAPCHA BALLON', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/BIAKIYmwFzgcGigfxHLT0u2AQofzYS6SBuTVQrGI.pdf', 1, 'actas/1759766730_68e3e8caca588_img1.png', 'actas/1759766730_68e3e8caca76d_img2.png', 'actas/1759766730_68e3e8caca827_img3.png', 'actas/1759766730_68e3e8caca905_img4.png', NULL, '2025-10-06 16:05:30', '2025-10-22 16:06:38'),
(57, NULL, '2025-10-06', 86, 'GILDA ÑAUPA CUBA', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/qhxyj5vfNT3cQ9zHjEMe1q6H13pJdc9L6XfCD4Tb.pdf', 1, 'actas/1759773354_68e402aaf22b9_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-06 17:55:54', '2025-10-11 16:32:39'),
(58, NULL, '2025-10-06', 12, 'ESTHER LILIAN ESCATE VENTURA', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/ei6vTqhqGXLpxRxjnRquQ4aDCTxhSqFS71x4aNIx.pdf', 1, 'actas/1760229259_68eaf78bcc1c2_img1.jpg', 'actas/1760229259_68eaf78bcc2c9_img2.jpg', NULL, NULL, NULL, '2025-10-06 20:04:14', '2025-10-12 00:42:43'),
(59, NULL, '2025-10-06', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/71dsTdLdu2gu6hV3V1tDhz9tAdkC8ZwPtLWnOacD.pdf', 1, 'actas/1760230643_68eafcf3a608a_img1.jpg', 'actas/1760230643_68eafcf3a6198_img2.jpg', NULL, NULL, NULL, '2025-10-06 20:11:01', '2025-10-12 01:05:37'),
(60, NULL, '2025-10-06', 127, 'BERTHA LUISA HERRERA LEVANO', 'Otros', 'Presencial', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/saNcv7ub0HEAeFHG4co6w75Io0PhaghTdHITBn5t.pdf', 1, 'actas/1760111615_68e92bffd03eb_img1.jpeg', 'actas/1760111615_68e92bffd05c2_img2.jpeg', 'actas/1760111615_68e92bffd0654_img3.jpeg', NULL, NULL, '2025-10-07 15:29:42', '2025-10-29 20:24:02'),
(61, NULL, '2025-10-06', 131, 'VILMA ARIAS MUNAYCO', 'Reactivación de módulo', 'Presencial', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/a8u8lAyhT0nDMq5cUl2iVNBirCennEHzZWrpccEY.pdf', 1, 'actas/1760112085_68e92dd548420_img1.jpeg', 'actas/1760112085_68e92dd548514_img2.jpeg', NULL, NULL, NULL, '2025-10-07 15:30:38', '2025-10-22 21:25:47'),
(62, NULL, '2025-10-07', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/MyDv7bM3gLPUzeQoepag1jCHb9QQcdmTdukQKLPk.pdf', 1, 'actas/1759856865_68e548e1de60c_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-07 17:07:45', '2025-10-13 04:30:24'),
(63, NULL, '2025-10-07', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/5H5oPCW2eJn38iboOrPSMPbueT0Ct7MrT4QbsERX.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-07 17:16:12', '2025-10-13 04:30:32'),
(64, NULL, '2025-10-09', 42, 'ENMA MARUJA DE LA CRUZ CARRASCO', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/IV49Eo2sDDKeigWQ3pme86m4BvEYZnCwAz7lg27g.pdf', 1, 'actas/1760033343_68e7fa3f10749_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-09 18:09:03', '2025-10-09 23:59:05'),
(65, NULL, '2025-10-09', 77, 'AMELIA LOPEZ ALVA', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/n7cc1i9UqLNUt0IKQRJjCJRPs1XYxiQYuIEUzH3q.pdf', 1, 'actas/1760035527_68e802c7c7853_img1.png', 'actas/1760035527_68e802c7c7a1e_img2.png', 'actas/1760035527_68e802c7c7b5f_img3.png', NULL, NULL, '2025-10-09 18:45:27', '2025-10-11 01:57:33'),
(66, NULL, '2025-10-09', 35, 'JORGE RODOLFO CHACALTANA SUAREZ', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', NULL, 0, NULL, NULL, NULL, NULL, NULL, '2025-10-09 20:18:44', '2025-10-09 20:18:44'),
(67, NULL, '2025-10-09', 56, 'JESUS AYQUIPA SANTI', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', NULL, 0, NULL, NULL, NULL, NULL, NULL, '2025-10-09 20:19:35', '2025-10-09 20:19:35'),
(68, NULL, '2025-10-09', 137, 'CARMEN ROSA VELASQUEZ DE LA ROCA', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/sAhAggMhKOnVH0grSklIX6MLynsxrTkCJ9joUyIG.pdf', 1, 'actas/1760232034_68eb02628d6f4_img1.jpg', 'actas/1760232034_68eb02628d828_img2.jpg', NULL, NULL, NULL, '2025-10-09 20:20:28', '2025-10-12 01:47:11'),
(69, NULL, '2025-10-09', 124, 'LOURDES AVILES ALFARO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/PALOyZGRmmwM7cOHvbr4X47J1jaFV45HxfuAr6Ab.pdf', 1, 'actas/1760234246_68eb0b0640173_img1.jpg', 'actas/1760234246_68eb0b06402a3_img2.jpg', 'actas/1760234246_68eb0b0640378_img3.jpg', 'actas/1760233882_68eb099ac730b_img4.jpg', NULL, '2025-10-09 20:21:18', '2025-10-12 02:08:05'),
(70, NULL, '2025-10-09', 91, 'ANTONIO GUMERCINDO CACERES CASADO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/Wj2FvtHnIMYV2Kfg0fgMo6vFMPuR3wwsbBl4u8bx.pdf', 1, 'actas/1760237020_68eb15dc6cc64_img1.jpg', 'actas/1760237020_68eb15dc6cd5c_img2.jpg', NULL, NULL, NULL, '2025-10-09 20:22:13', '2025-10-12 02:56:53'),
(71, NULL, '2025-10-09', 98, 'CARLOS ALBERTO CALDERON MARTINEZ', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/xu47oIGuEbryHIvN3HrbKTGJnYvdwoeh1WqBMfz1.pdf', 1, 'actas/1760237955_68eb198378683_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-09 20:22:49', '2025-10-12 03:11:40'),
(72, NULL, '2025-10-09', 19, 'YADIRA ANNAIS UCEDA AGUILAR', 'Reactivación de módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/3miwKHiBhIeck84Gi4QijwtUHUcy9tR55sTVzVJ5.pdf', 1, 'actas/1760045326_68e8290e8cca6_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-09 21:28:46', '2025-10-10 00:08:41'),
(73, NULL, '2025-10-09', 19, 'YADIRA ANNAIS UCEDA AGUILAR', 'Cambio de responsable del módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/WVDlbvMeLWio8t1dXev7YE767GvUdlUi0ThQmIgx.pdf', 1, 'actas/1760045458_68e829923a344_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-09 21:30:58', '2025-10-10 00:08:48'),
(74, NULL, '2025-10-13', 80, 'LUIS FORTUNATO PEREZ MURIANO', 'Otros', 'Presencial', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/hK9S5zhczQD96nhUkZTvkzusBgocSGMz8xhIU9I6.pdf', 1, 'actas/1760398431_68ed8c5f3d15a_img1.jpeg', 'actas/1760398431_68ed8c5f3d2e2_img2.jpeg', 'actas/1760398431_68ed8c5f3d3ad_img3.jpeg', 'actas/1760398431_68ed8c5f3d449_img4.jpeg', 'actas/1760398431_68ed8c5f3d4eb_img5.jpeg', '2025-10-10 04:05:44', '2025-10-14 00:18:59'),
(75, NULL, '2025-10-10', 50, 'JOSE LEONEL DORREGARAY AROSTIGUE', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/ZirXsp9HSnA2w5lcthfCOonF998ErY7Fa2NVW1TM.pdf', 1, 'actas/1760113680_68e93410d289f_img1.png', 'actas/1760113680_68e93410d2a38_img2.png', NULL, NULL, NULL, '2025-10-10 16:28:00', '2025-10-11 01:55:18'),
(76, NULL, '2025-10-10', 49, 'EDIS MILAGRITOS JHONG CASTRO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/dI8kb1JiDxWWzyvtW4Tsqg3mTyW85IpIzDKLB6Os.pdf', 1, 'actas/1760239940_68eb214404dc7_img1.jpg', 'actas/1760239940_68eb214404ed9_img2.jpg', 'actas/1760239940_68eb214404f77_img3.jpg', NULL, NULL, '2025-10-10 20:15:55', '2025-10-12 03:46:01'),
(77, NULL, '2025-10-10', 8, 'CYNTHIA YACORI CORREA PEREZ', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/RjoiO9bHlUfWWc6qc1maOxV1W604Jtyi7tWVokTF.pdf', 1, 'actas/1760241854_68eb28be91470_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-10 20:18:34', '2025-10-12 04:15:41'),
(78, NULL, '2025-10-10', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/UeuZa8mtP9HNEjALwX6bsL6zpCCva3h1txRtOWpi.pdf', 1, 'actas/1760243442_68eb2ef275b73_img1.jpg', 'actas/1760243442_68eb2ef275cb2_img2.jpg', 'actas/1760243442_68eb2ef275def_img3.jpg', 'actas/1760243442_68eb2ef275ece_img4.jpg', NULL, '2025-10-10 20:21:23', '2025-10-12 04:50:15'),
(79, NULL, '2025-10-10', 68, 'ANTONIO LIZARDO LOPEZ TREJO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/3eyjkqF9PW7knfy2pNmhTPzdmGL3WHPLEGjZohSV.pdf', 1, 'actas/1760244898_68eb34a2c9509_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-10 20:24:33', '2025-10-12 05:06:11'),
(80, NULL, '2025-10-10', 75, 'FREDDY ALBERTO VILCA CHACALTANA', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/CroeGPuO7YvsFxzMEwi2DwM63HmGgRTEl1ezfCce.pdf', 1, 'actas/1760245673_68eb37a9dbe27_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-10 20:26:11', '2025-10-12 05:17:43'),
(81, NULL, '2025-10-10', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/SIF5x3nyD1rAYt32T7cO04VHwZdoENTMPWdL9toz.pdf', 1, 'actas/1760246826_68eb3c2a59395_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-10 20:31:48', '2025-10-12 05:38:42'),
(82, NULL, '2025-10-10', 16, 'VICTOR MANUEL NUÑEZ SANCHEZ', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/jv9XYoRoIsz1hRM5dpwZJnlWc16rGYCX5fYgHsMY.pdf', 1, 'actas/1760147108_68e9b6a4b690a_img1.png', 'actas/1760147108_68e9b6a4b6a60_img2.png', 'actas/1760147108_68e9b6a4b6b70_img3.png', 'actas/1760147108_68e9b6a4b6c8c_img4.png', NULL, '2025-10-11 01:45:08', '2025-10-11 01:53:07'),
(83, NULL, '2025-10-11', 80, 'LUIS FORTUNATO PEREZ MURIANO', 'Ingreso de nuevo personal', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/zFSu2PqL7iT6m3gY3f88wGKpE21wRmQPt5yq7TzK.pdf', 1, 'actas/1760198363_68ea7edbb3850_img1.jpeg', 'actas/1760198363_68ea7edbb3952_img2.jpeg', 'actas/1760198363_68ea7edbb39fa_img3.jpeg', NULL, NULL, '2025-10-11 14:14:24', '2025-10-13 23:09:48'),
(84, NULL, '2025-10-11', 87, 'OMAR MEDINA CARDENAS', 'Cambio de responsable del módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/ZZFv6hLEGa5wlDAnZ7G6IgPb7YSNO8pYBCC84xoK.pdf', 1, 'actas/1760196824_68ea78d89b7b0_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-11 15:33:44', '2025-10-17 16:25:21'),
(85, NULL, '2025-10-11', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/6Z2WwC5RoBZGsOFNne9hFua2ufNVfEUUmlTu44yb.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-11 17:46:52', '2025-10-13 04:15:17'),
(86, NULL, '2025-10-11', 144, 'NIDIA BRAVO HERNANDEZ', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/b3f9d0l1H603C5Qj9ngnZOWWu1wPCBO1WSK2ghRo.pdf', 1, 'actas/1760207484_68eaa27c10751_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-11 18:31:24', '2025-10-13 15:09:52'),
(87, NULL, '2025-10-11', 87, 'OMAR MEDINA CARDENAS', 'Reactivación de módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/yQR3yWhoL6IIeOukjjgtOzg8OnX27nNs6P5oTDv8.pdf', 1, 'actas/1760210094_68eaacae851c0_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-11 19:14:54', '2025-10-17 16:25:30'),
(88, NULL, '2025-10-11', 87, 'OMAR MEDINA CARDENAS', 'Cambio de responsable del módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/uVgE2itwWKxvtIV8etsuWUWrBT9YCSNS4fWo1kjx.pdf', 1, 'actas/1760210406_68eaade640393_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-11 19:20:06', '2025-10-17 16:25:36'),
(89, NULL, '2025-10-06', 43, 'ERIKA PEREZ LUQUE', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/EzdZvtAgh0miWa6y7bjKGR4EHTHIgFL0mWGLkzqt.pdf', 1, 'actas/1760323727_68ec688f778a7_img1.jpeg', 'actas/1760323727_68ec688f779e8_img2.jpeg', NULL, NULL, NULL, '2025-10-13 02:48:47', '2025-10-13 04:01:25'),
(90, NULL, '2025-10-06', 79, 'MARTINEZ ASCONA JOSELITO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/vkMYm0YtmNF3DN4TML5JjMOs2Kwo6ESAyyhSvTYe.pdf', 1, 'actas/1760323859_68ec6913b3586_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-13 02:50:59', '2025-10-13 04:20:14'),
(91, NULL, '2025-10-06', 12, 'ESTHER LILIAN ESCATE VENTURA', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/myUSFBQpDhJhQVbMnWLhBcb0NSk8dTPhqcMwzz6e.pdf', 1, 'actas/1760323999_68ec699fec970_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-13 02:53:19', '2025-10-13 04:19:51'),
(92, NULL, '2025-10-06', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/2t7RTcdUBXH5jseU7EJ4C4MmL7TUjuIwoqZe32xn.pdf', 1, 'actas/1760324158_68ec6a3e1a657_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-13 02:55:58', '2025-10-13 04:19:26'),
(93, NULL, '2025-10-09', 137, 'CARMEN ROSA VELASQUEZ DE LA ROCA', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/3xGHX2qCgRRua0HCYDTACZJauxvcVaaMdVZhRJSf.pdf', 1, 'actas/1760324327_68ec6ae7909bd_img1.jpeg', 'actas/1760324327_68ec6ae790abd_img2.jpeg', NULL, NULL, NULL, '2025-10-13 02:58:47', '2025-10-13 04:07:48'),
(94, NULL, '2025-10-09', 124, 'LOURDES AVILES ALFARO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/Pweq7Omd48rNJPFJvrKEwYG5g8IyTUfuIJmM8Sbc.pdf', 1, 'actas/1760324801_68ec6cc128353_img1.jpeg', 'actas/1760324801_68ec6cc128489_img2.jpeg', NULL, NULL, NULL, '2025-10-13 03:06:41', '2025-10-13 04:08:08'),
(95, NULL, '2025-10-09', 91, 'ANTONIO GUMERCINDO CACERES CASADO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/DY4Bm8wXqpNgxZ9H1AzHKo61Y5KP9Eu0X6U5rjaD.pdf', 1, 'actas/1760324961_68ec6d615898d_img1.jpeg', 'actas/1760324961_68ec6d6158a8a_img2.jpeg', NULL, NULL, NULL, '2025-10-13 03:09:21', '2025-10-13 04:08:19'),
(96, NULL, '2025-10-09', 98, 'CARLOS ALBERTO CALDERON MARTINEZ', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/xAzO16q3RohfDVRfwdLCNpncqmVmQpnKi28D5Fp5.pdf', 1, 'actas/1760325163_68ec6e2b62795_img1.jpeg', 'actas/1760325163_68ec6e2b6289e_img2.jpeg', NULL, NULL, NULL, '2025-10-13 03:12:43', '2025-10-13 04:08:26'),
(97, NULL, '2025-10-10', 49, 'EDIS MILAGRITOS JHONG CASTRO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/hxCiuRmkzGNooXUJYzeDigOGAMOko8r63EtcT2W7.pdf', 1, 'actas/1760325271_68ec6e9743fe1_img1.jpeg', 'actas/1760325271_68ec6e97440fb_img2.jpeg', NULL, NULL, NULL, '2025-10-13 03:14:31', '2025-10-13 04:08:37'),
(98, NULL, '2025-10-10', 8, 'CYNTHIA YACORI CORREA PEREZ', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/gxOekZKVNO0hbvRk3dSgr6usjYMGXZM7LdtUrC76.pdf', 1, 'actas/1760325361_68ec6ef166482_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-13 03:16:01', '2025-10-13 04:08:49'),
(99, NULL, '2025-10-10', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/W7zqV20SqMNUW42xHjnq3OXRDRrDdrKIioCGRAPk.pdf', 1, 'actas/1760325662_68ec701e142d9_img1.jpeg', 'actas/1760325662_68ec701e14419_img2.jpeg', NULL, NULL, NULL, '2025-10-13 03:21:02', '2025-10-13 04:08:58'),
(100, NULL, '2025-10-10', 68, 'ANTONIO LIZARDO LOPEZ TREJO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/hna0qELILqp7NbvjtPCUGyL7t4z1N6sbw5ujrUfR.pdf', 1, 'actas/1760325751_68ec707774118_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-13 03:22:31', '2025-10-13 04:09:06'),
(101, NULL, '2025-10-10', 75, 'FREDDY ALBERTO VILCA CHACALTANA', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/u1P7HNUdaaEYWu9ubdXjk4OFnx8w9CJoSST7XUjw.pdf', 1, 'actas/1760325888_68ec7100203e8_img1.jpeg', 'actas/1760325888_68ec7100204e4_img2.jpeg', NULL, NULL, NULL, '2025-10-13 03:24:48', '2025-10-13 04:06:07'),
(102, NULL, '2025-10-10', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/hBoI4Rjn3HmAgFvl7iJWmMDJF02RITGVPTUqQFZE.pdf', 1, 'actas/1760325960_68ec7148308e6_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-13 03:26:00', '2025-10-13 04:17:22'),
(103, NULL, '2025-10-13', 128, 'HUAMAN HERNANDEZ JOSE JAVIER', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/3EkP7oUTq8HKBD6ZfDjFzDPC7GXbPYS5P5OeQmI8.pdf', 1, 'actas/1760374511_68ed2eefe2b72_img1.jpeg', 'actas/1760374511_68ed2eefe2c94_img2.jpeg', NULL, NULL, NULL, '2025-10-13 16:55:11', '2025-10-16 02:55:30'),
(104, NULL, '2025-10-13', 19, 'YADIRA ANNAIS UCEDA AGUILAR', 'Reactivación de módulo', 'Presencial', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/Bt9YxSP3MQffecCbWK2mkIjPrwnNbiQ6E3msX0IQ.pdf', 1, 'actas/1760387343_68ed610f7cc72_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-13 20:29:03', '2025-10-16 02:44:48'),
(105, NULL, '2025-10-13', 144, 'NIDIA BRAVO HERNANDEZ', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', NULL, 0, 'actas/1760395625_68ed8169645a1_img1.jpeg', 'actas/1760395625_68ed8169646b9_img2.jpeg', NULL, NULL, NULL, '2025-10-13 22:47:05', '2025-10-13 22:47:05'),
(106, NULL, '2025-10-14', 55, 'JACINTO JULIO GUTIERREZ CORTEZ', 'Ingreso de nuevo personal', 'Presencial', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/jfZgP13r3JHnYjMRZ0PBwtKYbWAyzp9vLPx3CI6Q.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-14 00:38:42', '2025-10-15 00:59:17'),
(107, NULL, '2025-11-30', 55, 'JACINTO JULIO GUTIERREZ CORTEZ', 'Ingreso de nuevo personal', 'Presencial', 'Yañez Medina Lida Graciela', 'asistencia', NULL, 0, NULL, NULL, NULL, NULL, NULL, '2025-10-14 00:43:50', '2025-12-01 19:23:32'),
(108, NULL, '2025-10-14', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/48MwIpvB2elD9hLTz8swLx8MCsZYuj3NboLvAceO.pdf', 1, 'actas/1760584038_68f06166efbd3_img1.jpeg', 'actas/1760584038_68f06166efd29_img2.jpeg', 'actas/1760584038_68f06166efdbe_img3.jpeg', NULL, NULL, '2025-10-14 19:29:51', '2025-10-16 03:20:15'),
(109, NULL, '2025-10-14', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/ovjwmOVBhkb2OSJJxaNIMGrgMSANgC8Kvy4e8hqD.pdf', 1, 'actas/1760491395_68eef783c5e15_img1.jpg', 'actas/1760491395_68eef783c5f0a_img2.jpg', 'actas/1760491395_68eef783c5fd0_img3.jpg', 'actas/1760491395_68eef783c6066_img4.jpg', NULL, '2025-10-14 20:28:09', '2025-10-15 02:32:36'),
(110, NULL, '2025-10-14', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/n5oRZ3AxzVnxIdbWBSv66VsEx13QeAEKfKKyO5X6.pdf', 1, 'actas/1760493055_68eefdfff39f4_img1.jpg', 'actas/1760493055_68eefdfff3ae6_img2.jpg', 'actas/1760493055_68eefdfff3b94_img3.jpg', NULL, NULL, '2025-10-14 20:31:19', '2025-10-15 02:33:11'),
(111, NULL, '2025-10-14', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Reactivación de módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/kS8m92BlTqeQK5QQ3diKcH0toVf5UDywfMnVREI4.pdf', 1, 'actas/1760494388_68ef033410b6d_img1.jpg', 'actas/1760494388_68ef033410c80_img2.jpg', 'actas/1760494388_68ef033410d4a_img3.jpg', 'actas/1760494388_68ef033410df8_img4.jpg', NULL, '2025-10-14 20:33:54', '2025-10-15 02:34:29'),
(112, NULL, '2025-10-14', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Reactivación de módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/0ZzcBUlKtb36Q0UaapHqa5hxLFnFiuRu2ZE4hEGg.pdf', 1, 'actas/1760494829_68ef04ed94973_img1.jpg', 'actas/1760494829_68ef04ed94a72_img2.jpg', 'actas/1760494829_68ef04ed94af9_img3.jpg', 'actas/1760494829_68ef04ed94b81_img4.jpg', 'actas/1760494829_68ef04ed94c25_img5.jpg', '2025-10-14 20:35:51', '2025-10-15 02:35:36'),
(113, NULL, '2025-10-14', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/dXbd3lOyd1yJpZ6IpVnHs9Uxmtjg1bBBav972yBH.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-14 20:38:36', '2025-10-16 03:20:24'),
(114, NULL, '2025-10-14', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Reactivación de módulo', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/kEt7I8RgNDdTBiBnATO5eeqN4NcAHh28WoB6kzg9.pdf', 1, 'actas/1760584339_68f0629339021_img1.jpeg', 'actas/1760584339_68f0629339134_img2.jpeg', 'actas/1760584339_68f06293391df_img3.jpeg', 'actas/1760584339_68f06293392a0_img4.jpeg', NULL, '2025-10-14 20:40:45', '2025-10-16 03:20:31'),
(115, NULL, '2025-10-14', 45, 'ROSA ANALI ASTOCAZA GALINDO', 'Reactivación de módulo', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/WmWbVhguebGMmefAOy9O2E1WgUa2tJFEp1N9guxJ.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-14 20:43:29', '2025-10-16 03:20:38'),
(116, NULL, '2025-10-14', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/IwwuxQlFTaiMssz0Bl6fzx0DTuWr2QWlJlczHx5s.pdf', 1, 'actas/1760485297_68eedfb14a52e_img1.jpg', 'actas/1760485297_68eedfb14a675_img2.jpg', 'actas/1760485297_68eedfb14a6fc_img3.jpg', NULL, NULL, '2025-10-14 23:41:37', '2025-10-15 02:47:12'),
(117, NULL, '2025-10-15', 73, 'BALVIN MARIO MEDINA SAAVEDRA', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/w1wvajmFudJylzzOgUCmROigV5dJscpwFynfvWAf.pdf', 1, 'actas/1760539381_68efb2f5d222c_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-15 14:43:01', '2025-10-30 20:15:43'),
(118, NULL, '2025-10-15', 124, 'LOURDES AVILES ALFARO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/vnXNBs6aH4chJt1KD1PM0iDQjZfiLVIhQcgDop6w.pdf', 1, 'actas/1761790187_6902c8eb1e7e4_img1.jpg', 'actas/1761790187_6902c8eb1e965_img2.jpg', 'actas/1761790187_6902c8eb1ea27_img3.jpg', NULL, NULL, '2025-10-15 16:19:25', '2025-10-30 02:59:01'),
(119, NULL, '2025-10-15', 124, 'LOURDES AVILES ALFARO', 'Cambio de responsable del módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/9697JlE1N2nHTOP256oMooZZbQGkBunDFPqtqDlP.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-15 16:27:04', '2025-10-30 02:59:36'),
(120, NULL, '2025-10-15', 124, 'LOURDES AVILES ALFARO', 'Reactivación de módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/jOwbNHrmMIJPZQqLS6IhK6Jjk61dLh239W1hSDJh.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-15 16:53:24', '2025-10-30 02:59:45'),
(121, NULL, '2025-10-15', 73, 'BALVIN MARIO MEDINA SAAVEDRA', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/GEL2TjqJBJepaIU6K9uD06fd2dpNKuSYH4T1l5JM.pdf', 1, 'actas/1760548830_68efd7de1918a_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-15 17:20:30', '2025-10-30 20:19:40'),
(122, NULL, '2025-10-15', 57, 'JUANA HAYDE INTIMAYTA SAYRITUPAC', 'Reactivación de módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/QFXJhqQCoQ5U5O86i21OR9qMg5PdyqPTvRg2BPlG.pdf', 1, 'actas/1760559242_68f0008a5dd60_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-15 20:14:02', '2025-10-15 22:44:36'),
(123, NULL, '2025-10-15', 57, 'JUANA HAYDE INTIMAYTA SAYRITUPAC', 'Reactivación de módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/W2MW4qlReLplJjY29qSQwSLefKtinuJKuiFCnaJH.pdf', 1, 'actas/1760561183_68f0081f42de6_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-15 20:46:23', '2025-10-15 22:44:41'),
(124, NULL, '2025-10-15', 35, 'JORGE RODOLFO CHACALTANA SUAREZ', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', NULL, 0, 'actas/1760575334_68f03f66ea882_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-16 00:42:14', '2025-10-16 00:42:14'),
(125, NULL, '2025-10-15', 49, 'EDIS MILAGRITOS JHONG CASTRO', 'Ingreso de nuevo personal', 'Virtual', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/k9DZANiDTL06YIL6fuMqZTHAYAtmBgRQK70JaiMI.pdf', 1, 'actas/1760576024_68f04218af991_img1.png', 'actas/1760576024_68f04218afab8_img2.png', 'actas/1760576024_68f04218afb62_img3.png', 'actas/1760576024_68f04218afbff_img4.jpg', NULL, '2025-10-16 00:53:44', '2025-10-29 23:38:32'),
(126, NULL, '2025-10-16', 112, 'MIGUEL ANGEL HERNANDEZ LOPEZ', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/hwumzb3fsU2KNbXC71C7Ik1yz3LAY2591T7gqUuo.pdf', 1, 'actas/1760720176_68f27530da629_img1.jpeg', 'actas/1760720176_68f27530da75a_img2.jpeg', 'actas/1760720176_68f27530da809_img3.jpeg', NULL, NULL, '2025-10-16 19:58:20', '2025-10-17 17:14:44'),
(127, NULL, '2025-10-16', 112, 'MIGUEL ANGEL HERNANDEZ LOPEZ', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/qhjaEYTInkTb5JKRdV3pUpVeAR2fWE57wcnJgM4Z.pdf', 1, 'actas/1760720204_68f2754cb9466_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-16 20:08:37', '2025-10-17 17:14:35'),
(128, NULL, '2025-10-16', 112, 'MIGUEL ANGEL HERNANDEZ LOPEZ', 'Reactivación de módulo', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/BhZlV1WHCx5cLLywWbTewmYf9Iq7TR5uROz8BKNX.pdf', 1, 'actas/1760720261_68f27585df37b_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-16 20:10:12', '2025-10-17 17:14:27'),
(129, NULL, '2025-10-16', 112, 'MIGUEL ANGEL HERNANDEZ LOPEZ', 'Actualización de cartera de servicios', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/JJpKEKZS6UqhKNLVVVyQuDoXLi7jhjfLdlfN44nj.pdf', 1, 'actas/1760720394_68f2760a54b6a_img1.jpeg', 'actas/1760720394_68f2760a54c95_img2.jpeg', 'actas/1760720394_68f2760a54d3c_img3.jpeg', 'actas/1760720394_68f2760a54dcf_img4.jpeg', NULL, '2025-10-16 20:11:47', '2025-10-17 17:14:18'),
(130, NULL, '2025-10-16', 112, 'MIGUEL ANGEL HERNANDEZ LOPEZ', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/dLq0boysVXNldJYBa6AgykTwqOLPcxvBsU98fD5K.pdf', 1, 'actas/1760924507_68f5935b51ef9_img1.jpg', 'actas/1760924268_68f5926c64f74_img2.jpg', NULL, NULL, NULL, '2025-10-16 20:20:16', '2025-10-20 01:51:49'),
(131, NULL, '2025-10-16', 112, 'MIGUEL ANGEL HERNANDEZ LOPEZ', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/QxRv2rCr3ceTHFflvsuy9ixUFMq1cAclJKOQnfYq.pdf', 1, 'actas/1760925374_68f596be4a806_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-16 20:21:14', '2025-10-20 02:19:59'),
(132, NULL, '2025-10-16', 112, 'MIGUEL ANGEL HERNANDEZ LOPEZ', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/sHJjWUD7azFRRWl7U8DteXC6NeFj4iXPgW203h6f.pdf', 1, NULL, NULL, NULL, NULL, NULL, '2025-10-16 20:22:27', '2025-10-20 04:30:08'),
(133, NULL, '2025-10-16', 112, 'MIGUEL ANGEL HERNANDEZ LOPEZ', 'Reactivación de módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/Kiq84IktLwIEwBHRmFFx6kloK67CNb7Aj0hFTFIh.pdf', 1, 'actas/1760935730_68f5bf32da2c3_img1.jpg', 'actas/1760935730_68f5bf32da409_img2.jpg', 'actas/1760935121_68f5bcd172b45_img3.jpg', NULL, NULL, '2025-10-16 20:25:14', '2025-10-20 04:58:48'),
(134, NULL, '2025-10-17', 8, 'CYNTHIA YACORI CORREA PEREZ', 'Otros', 'Virtual', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/SDVZy44IHeO9zBtk4eeRGFDsPTv3ujVLCDwOONjD.pdf', 1, 'actas/1760716056_68f265186e167_img1.jpeg', 'actas/1760716056_68f265186e458_img2.jpeg', NULL, NULL, NULL, '2025-10-17 15:47:36', '2025-10-17 16:30:13'),
(135, NULL, '2025-10-17', 94, 'AVELINO ADRIAN ALVA AQUIJE', 'Reactivación de módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/zAZiYDp2E5Yqq3F7rl6hbq7ouz8EC9sQ6ndQaNEn.pdf', 1, 'actas/1760716718_68f267ae79cea_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-17 15:58:38', '2025-10-24 15:23:47'),
(136, NULL, '2025-10-17', 94, 'AVELINO ADRIAN ALVA AQUIJE', 'Reactivación de módulo', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/zG3ASbq9hyl71ZWB18aLi0zl0jxelQ0ru8cfasl2.pdf', 1, 'actas/1760716780_68f267ec0d454_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-17 15:59:40', '2025-10-24 15:24:02'),
(137, NULL, '2025-10-18', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/14q50VCvdD5gRJogBndeaRcZbN9fAGb7ptlihYmy.pdf', 1, 'actas/1760919947_68f5818be97d5_img1.jpg', 'actas/1760919947_68f5818be9907_img2.jpg', 'actas/1760919947_68f5818be99dd_img3.jpg', 'actas/1760919947_68f5818be9ab2_img4.jpg', NULL, '2025-10-20 00:13:40', '2025-10-20 00:33:51'),
(138, NULL, '2025-10-21', 8, 'CYNTHIA YACORI CORREA PEREZ', 'Actualización de cartera de servicios', 'Virtual', 'Montes Guillermo Erick', 'asistencia', NULL, 0, 'actas/1761056819_68f79833c3f73_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-21 14:26:59', '2025-10-21 14:26:59'),
(139, NULL, '2025-10-22', 29, 'CAROLA PILAR ORTIZ VILLAFUERTE', 'Otros', 'Presencial', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/hsTzAgzhKzX3iIpCRWz5ZMDgd1Bxk7yCMHKMvVB4.pdf', 1, 'actas/1761229077_68fa39150cfb0_img1.jpeg', 'actas/1761229077_68fa39150d1cf_img2.jpeg', 'actas/1761229077_68fa39150d269_img3.jpeg', NULL, NULL, '2025-10-22 02:38:20', '2025-10-23 14:59:49'),
(140, NULL, '2025-10-22', 55, 'JACINTO JULIO GUTIERREZ CORTEZ', 'Otros', 'Presencial', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/MZnJE8JdpwsalAz4eDzRxy6EEj0ucNoZXeTo246Y.pdf', 1, 'actas/1761238926_68fa5f8e930d1_img1.jpeg', 'actas/1761238926_68fa5f8e931fa_img2.jpeg', 'actas/1761238926_68fa5f8e93292_img3.jpeg', 'actas/1761238926_68fa5f8e93323_img4.jpeg', 'actas/1761238926_68fa5f8e933b5_img5.jpeg', '2025-10-22 02:42:49', '2025-10-23 17:18:43'),
(141, NULL, '2025-10-14', 119, 'AMELIA FERNANDA SORIA SARAVIA', 'Otros', 'Presencial', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/5TBns1ftDdYlaYVegALKC3Ja9bBzdbB2Sti8L4Ig.pdf', 1, 'actas/1761170959_68f9560f3c624_img1.jpg', 'actas/1761170959_68f9560f3c7d4_img2.jpg', NULL, NULL, NULL, '2025-10-22 22:06:45', '2025-11-03 18:51:11'),
(142, NULL, '2025-10-15', 24, 'SANDY DANIEL CALDERA POLANCO', 'Ingreso de nuevo personal', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/aS9HPgMSt8YB0cFRkqsy12cWx0McKdkM2RMwrYQ4.pdf', 1, 'actas/1761171714_68f959026fc34_img1.jpeg', NULL, NULL, NULL, NULL, '2025-10-22 22:21:54', '2025-11-25 17:00:58'),
(143, NULL, '2025-10-15', 93, 'ROXANA MELCHORITA CASTILLA GUILLÉN', 'Ingreso de nuevo personal', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/MJ10MTxGv0RNAnXYZPm8DvoJl4qXhSDiTf2a91Zb.pdf', 1, 'actas/1761172209_68f95af124acb_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-22 22:30:09', '2025-10-28 19:50:09'),
(144, NULL, '2025-10-15', 131, 'VILMA ARIAS MUNAYCO', 'Ingreso de nuevo personal', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/Mjugq9mdEEMX74Ps5YfXkFX9DB7YVLCVT6y1LXwJ.pdf', 1, 'actas/1761172555_68f95c4bcf080_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-22 22:35:55', '2025-10-31 16:05:24'),
(145, NULL, '2025-10-17', 20, 'CINDY CRISTINA CHINCHAY ALMEIDA', 'Reactivación de módulo', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/sZMDDcDpKXs3Zlq7SRfalXcOLz1EmyJMLi7Bp2p3.pdf', 1, 'actas/1761172897_68f95da133363_img1.jpg', 'actas/1761172897_68f95da133544_img2.jpg', NULL, NULL, NULL, '2025-10-22 22:41:37', '2025-11-25 16:58:59'),
(146, NULL, '2025-10-22', 131, 'VILMA ARIAS MUNAYCO', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/QT3SqHOf2Rc1w2Ru5GAhiv7g0pXE6UzKEZd2yHcO.pdf', 1, 'actas/1761173155_68f95ea30a05f_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-22 22:45:55', '2025-10-31 16:05:34'),
(147, NULL, '2025-10-22', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Ingreso de nuevo personal', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/5CFsof7DqlwzyRyUO0GnS06w3epuUjoV2WEp29XR.pdf', 1, 'actas/1761771991_690281d758605_img1.jpg', 'actas/1761771991_690281d758736_img2.jpg', 'actas/1761771991_690281d758806_img3.jpg', 'actas/1761771991_690281d7588c2_img4.jpg', NULL, '2025-10-23 14:38:07', '2025-10-29 23:16:51'),
(148, NULL, '2025-10-23', 31, 'ERIKA MARLENY MEDINA CACERES', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/sB3D9ogzR3V8ZzUU6HcTWGp9d4c4CdqhpC69Vatm.pdf', 1, 'actas/1761238275_68fa5d03d3fa6_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-23 16:51:15', '2025-10-24 15:11:02');
INSERT INTO `actas` (`id`, `user_id`, `fecha`, `establecimiento_id`, `responsable`, `tema`, `modalidad`, `implementador`, `tipo`, `firmado_pdf`, `firmado`, `imagen1`, `imagen2`, `imagen3`, `imagen4`, `imagen5`, `created_at`, `updated_at`) VALUES
(149, NULL, '2025-10-23', 31, 'ERIKA MARLENY MEDINA CACERES', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/yGnDaqd8zJL7gMuE94pOozwdHS8qSGB6AvL4fN5c.pdf', 1, 'actas/1761238483_68fa5dd3518ee_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-23 16:54:43', '2025-10-24 15:11:08'),
(150, NULL, '2025-10-23', 52, 'MILAGROS ANTONIA MUÑANTE HERENCIA', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/sJbRrzR86KJp0ohoxNOnBimFGFAJ5TSHpZrA5hFu.pdf', 1, 'actas/1761241896_68fa6b282f243_img1.JPG', NULL, NULL, NULL, NULL, '2025-10-23 17:51:36', '2025-10-25 19:18:27'),
(151, NULL, '2025-10-24', 16, 'VICTOR MANUEL NUÑEZ SANCHEZ', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', NULL, 0, 'actas/1761342262_68fbf3361183c_img1.png', 'actas/1761342262_68fbf33611b84_img2.png', 'actas/1761342262_68fbf33611cb3_img3.png', NULL, NULL, '2025-10-24 21:44:22', '2025-10-24 21:44:22'),
(152, NULL, '2025-10-27', 123, 'CARMEN ROSA CHUMPITAZ VEGA', 'Ingreso de nuevo personal', 'Presencial', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/QBJafIXVbz4Jj0l9SpHro3VoP4VLKJmwn0go0q3X.pdf', 1, 'actas/1761600492_68ffe3ec9a0be_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-27 21:28:12', '2025-11-03 15:25:34'),
(153, NULL, '2025-10-27', 123, 'CARMEN ROSA CHUMPITAZ VEGA', 'Ingreso de nuevo personal', 'Presencial', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/lExdtWYoDcfSfo5XDgvzq3H6MMDfcFCf0OU8B82A.pdf', 1, 'actas/1761600827_68ffe53b25b86_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-27 21:33:47', '2025-11-03 15:25:48'),
(154, NULL, '2025-10-27', 123, 'CARMEN ROSA CHUMPITAZ VEGA', 'Ingreso de nuevo personal', 'Presencial', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/8UEDoctAaDeyxFnHRnR9QgDJLpAtu2dyKCgzna1c.pdf', 1, 'actas/1761601153_68ffe681870b2_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-27 21:39:13', '2025-11-03 15:25:59'),
(155, NULL, '2025-10-25', 127, 'BERTHA LUISA HERRERA LEVANO', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/sRmckH9O0HvkWkJ1dnQifkoXAP7uawOcfLaeZlsh.pdf', 1, 'actas/1761669031_6900efa79a18f_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-28 16:30:31', '2025-10-29 20:24:12'),
(156, NULL, '2025-10-28', 116, 'MARIA DEL CARMEN TAIPE HUAYRA', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/UOAI7akqR2SQcxDvDCC4YP9TJ2N3bs4yzshXU1OK.pdf', 1, 'actas/1761693183_69014dffb84c2_img1.jpg', NULL, NULL, NULL, NULL, '2025-10-28 23:13:03', '2025-10-29 20:20:00'),
(157, NULL, '2025-10-28', 116, 'MARIA DEL CARMEN TAIPE HUAYRA', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/VGTni79lti6LHw0SD493ZvDcPjNC7Xsbl52HMCOj.pdf', 1, 'actas/1761697418_69015e8a00b58_img1.jpg', 'actas/1761697418_69015e8a00c75_img2.png', NULL, NULL, NULL, '2025-10-29 00:22:53', '2025-10-29 20:20:06'),
(158, NULL, '2025-10-29', 138, 'ALDO MARCELO MONGE REYES', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/FJ8XPdMroodigsNpQksie9VM2t0orAf8t5pYMTnd.pdf', 1, 'actas/1761771089_69027e518e8d4_img1.jpg', 'actas/1761771089_69027e518e9e1_img2.jpg', 'actas/1761771089_69027e518ea70_img3.jpg', 'actas/1761771089_69027e518eb3d_img4.jpg', 'actas/1761771089_69027e518ebc7_img5.jpg', '2025-10-29 16:45:50', '2025-10-29 23:23:24'),
(159, NULL, '2025-10-29', 137, 'CARMEN ROSA VELASQUEZ DE LA ROCA', 'Otros', 'Presencial', 'Montes Guillermo Erick', 'asistencia', 'actas_firmadas/HcM4udK1O0vPLguiq8TsttaoxPXXsDC9RmohVyHQ.pdf', 1, 'actas/1761771764_690280f4619ec_img1.jpeg', 'actas/1761771764_690280f461b2c_img2.jpeg', 'actas/1761771764_690280f461bfd_img3.jpeg', 'actas/1761771764_690280f461cc4_img4.jpeg', NULL, '2025-10-29 18:49:45', '2025-10-30 01:52:05'),
(160, NULL, '2025-10-23', 12, 'ESTHER LILIAN ESCATE VENTURA', 'Otros', 'Virtual', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/44FxSRGIWa0S3pikTT0OqNcPaZK1xYMSQnpypfQe.pdf', 1, 'actas/1761839646_69038a1eef395_img1.jpg', 'actas/1761839646_69038a1eef47f_img2.jpg', NULL, NULL, NULL, '2025-10-30 15:39:24', '2025-10-30 15:58:03'),
(161, NULL, '2025-11-05', 16, 'VICTOR MANUEL NUÑEZ SANCHEZ', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/hTZfGEg9cI6f1OrzcqvINyEqE71q9riiORqScv6V.pdf', 1, 'actas/1762357198_690b6fcebf2da_img1.png', 'actas/1762357198_690b6fcebf567_img2.png', 'actas/1762357198_690b6fcebf695_img3.png', NULL, NULL, '2025-11-05 15:39:58', '2025-11-06 02:52:14'),
(162, NULL, '2025-11-03', 65, 'JOSE CARLOS VALLE BRAVO', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/MMTcKdDIIqcrkbheuUxSgLdvgLwk8tRDvMtYenLH.pdf', 1, 'actas/1763075558_691665e68f2f1_img1.jpg', NULL, NULL, NULL, NULL, '2025-11-13 23:12:38', '2025-11-25 17:45:40'),
(163, NULL, '2025-11-03', 131, 'VILMA ARIAS MUNAYCO', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/Qov0NGIzrO6P2XUBoSZIVwV7PBjdVmVHKtdXojRM.pdf', 1, 'actas/1763077936_69166f30120fc_img1.jpg', NULL, NULL, NULL, NULL, '2025-11-13 23:52:16', '2025-11-25 17:58:55'),
(164, NULL, '2025-11-03', 131, 'VILMA ARIAS MUNAYCO', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/4YGuhMYXl6XU1mDnOBMN5lUJbnjbHyqFdprfrbbE.pdf', 1, 'actas/1763079572_69167594734e2_img1.png', 'actas/1763079572_69167594736c4_img2.png', 'actas/1763079572_69167594737ea_img3.png', NULL, NULL, '2025-11-14 00:19:32', '2025-11-25 17:22:37'),
(165, NULL, '2025-11-15', 100, 'SARITA HUACCAMAITA CONTRERAS', 'Cambio de responsable del módulo', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/vyYeFQ0FymKtPPZRGkOnWjt5VXYzPqCMg0LyRxIH.pdf', 1, 'actas/1763222697_6918a4a92bbc1_img1.png', 'actas/1763222697_6918a4a92be6f_img2.png', 'actas/1763222697_6918a4a92c096_img3.png', NULL, NULL, '2025-11-15 16:04:57', '2025-11-25 01:47:32'),
(166, NULL, '2025-11-03', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Cambio de responsable del módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/N2vci1vMxoAP6olhzWiWASdpeXa4VDzzAmbPm5YR.pdf', 1, 'actas/1763486421_691caad5a24e2_img1.JPG', 'actas/1763486421_691caad5a272e_img2.JPG', NULL, NULL, NULL, '2025-11-18 14:51:16', '2025-11-27 22:27:36'),
(167, NULL, '2025-11-03', 138, 'ALDO MARCELO MONGE REYES', 'Otros', 'Telefónica', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/5hGR7Tl1pSQ8KjUDaGIxZfpaf5EDit03pvG5teTx.pdf', 1, 'actas/1764192639_6927717f282b1_img1.jpg', 'actas/1764192639_6927717f283e0_img2.jpg', 'actas/1764192639_6927717f2848a_img3.jpg', NULL, NULL, '2025-11-18 17:32:51', '2025-11-27 22:29:01'),
(168, NULL, '2025-11-05', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Cambio de responsable del módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/iBpMIWEooAD3RSFtzfkuQnq9QqawjKyu9LHNT3uE.pdf', 1, 'actas/1763489254_691cb5e6b50d0_img1.jpg', NULL, NULL, NULL, NULL, '2025-11-18 18:07:34', '2025-11-27 22:29:20'),
(169, NULL, '2025-11-06', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Cambio de responsable del módulo', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/MHzJhbUaIOpiAfMf6glFuzSrufDejxaXSe5IrdWW.pdf', 1, 'actas/1763489519_691cb6ef5a882_img1.jpg', 'actas/1763489519_691cb6ef5a98b_img2.jpg', 'actas/1763489519_691cb6ef5aa97_img3.jpg', 'actas/1763489519_691cb6ef5ab56_img4.jpg', 'actas/1763489519_691cb6ef5ac35_img5.jpg', '2025-11-18 18:11:59', '2025-11-27 22:29:30'),
(170, NULL, '2025-11-07', 114, 'ELIZABETH LOPEZ GOMEZ', 'Otros', 'Virtual', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/DyH3Qptjfra4f43aLTd0CyIy70aAeV0GUTK9Erec.pdf', 1, 'actas/1763490376_691cba48bbfd5_img1.jpg', 'actas/1763490376_691cba48bc135_img2.jpg', 'actas/1763490376_691cba48bc251_img3.jpg', 'actas/1763490376_691cba48bc320_img4.jpg', 'actas/1763490376_691cba48bc3e2_img5.jpg', '2025-11-18 18:25:52', '2025-11-26 22:15:46'),
(171, NULL, '2025-11-10', 49, 'EDIS MILAGRITOS JHONG CASTRO', 'Otros', 'Virtual', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/tTVSHhvFEklBlrkEZ7dVKnI560NrBD3dQopkYfr9.pdf', 1, 'actas/1763490786_691cbbe2aa2f3_img1.png', 'actas/1763490786_691cbbe2aa3d3_img2.jpg', 'actas/1763490786_691cbbe2aa473_img3.jpg', 'actas/1763490786_691cbbe2aa50f_img4.jpg', 'actas/1763490786_691cbbe2aa59a_img5.jpg', '2025-11-18 18:33:06', '2025-11-26 22:17:27'),
(172, NULL, '2025-11-11', 74, 'EDWIN SEGUNDO PAREDES MONTEJO', 'Ingreso de nuevo personal', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/M48TqN0bCHeWBgsyZoFD3uCN1znWA44qqa7lhdM3.pdf', 1, 'actas/1763491263_691cbdbfce92c_img1.jpg', 'actas/1763491263_691cbdbfcea38_img2.jpg', 'actas/1763491263_691cbdbfcead0_img3.jpg', 'actas/1763491263_691cbdbfceb63_img4.JPG', NULL, '2025-11-18 18:41:03', '2025-11-27 22:29:55'),
(173, NULL, '2025-11-17', 138, 'ALDO MARCELO MONGE REYES', 'Otros', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/I6dxCswIhobR7zy1ScLkyAvhHziKqhoPmy9vhU2K.pdf', 1, 'actas/1763491607_691cbf174b3f5_img1.jpg', 'actas/1763491607_691cbf174b4ee_img2.jpg', NULL, NULL, NULL, '2025-11-18 18:46:47', '2025-11-27 22:30:07'),
(174, NULL, '2025-11-18', 138, 'ALDO MARCELO MONGE REYES', 'Actualización de cartera de servicios', 'Presencial', 'Pineda Moran Carmen Selene', 'asistencia', 'actas_firmadas/edyO9mwgyjXLviq1sprINEBDvpVeotPHUjWyz1LW.pdf', 1, 'actas/1764193324_6927742c04bd9_img1.jpg', 'actas/1764193324_6927742c04d46_img2.jpg', 'actas/1764193324_6927742c04e11_img3.jpg', 'actas/1764193324_6927742c04efd_img4.jpg', NULL, '2025-11-18 18:54:32', '2025-11-27 22:30:17'),
(175, NULL, '2025-11-18', 86, 'GILDA ÑAUPA CUBA', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/Sd2QJ5Rd83un13dIraTZBNHHTkTFBszLaB9NmcFY.pdf', 1, 'actas/1763508645_691d01a5dede6_img1.JPG', NULL, NULL, NULL, NULL, '2025-11-18 23:30:45', '2025-11-25 00:54:06'),
(176, NULL, '2025-11-20', 87, 'OMAR MEDINA CARDENAS', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/LxpFgQOmd2pLx0IPTCB3QxKUuitMXQmFQODJWudm.pdf', 1, 'actas/1763655647_691f3fdf0f302_img1.png', NULL, NULL, NULL, NULL, '2025-11-20 16:20:47', '2025-11-25 01:18:38'),
(177, NULL, '2025-11-20', 94, 'AVELINO ADRIAN ALVA AQUIJE', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/JXVJ51Fx2MRGWzOajHE6GDKOYyg6Odop5i2Z4b4N.pdf', 1, 'actas/1763678333_691f987d8a955_img1.png', NULL, NULL, NULL, NULL, '2025-11-20 22:38:53', '2025-11-25 01:01:35'),
(178, NULL, '2025-11-21', 86, 'GILDA ÑAUPA CUBA', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/QFDcHp1Nxm5FePkZ50vFpJN7YNDmYt62VH9AcE5U.pdf', 1, 'actas/1763757360_6920cd30724b3_img1.jpg', NULL, NULL, NULL, NULL, '2025-11-21 20:36:00', '2025-11-25 00:54:14'),
(179, NULL, '2025-11-21', 94, 'AVELINO ADRIAN ALVA AQUIJE', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/6JgGx68me39PwTs9HaICTNnW5rcpqyay5FrT8xwB.pdf', 1, 'actas/1763759219_6920d4735cced_img1.jpg', NULL, NULL, NULL, NULL, '2025-11-21 21:06:59', '2025-11-25 01:01:41'),
(180, NULL, '2025-11-10', 16, 'VICTOR MANUEL NUÑEZ SANCHEZ', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/xHbJU8ovd6qHkGpYSMS2Bn09JkAb069ITXPg7mBu.pdf', 1, 'actas/1764037363_692512f3d7b8b_img1.png', 'actas/1764037363_692512f3d7da6_img2.png', NULL, NULL, NULL, '2025-11-25 02:22:43', '2025-11-25 02:27:27'),
(181, NULL, '2025-11-25', 93, 'ROXANA MELCHORITA CASTILLA GUILLÉN', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/duaefkkl7h8w7vQGI4rUN7TBoISPBcIeaeygfhS7.pdf', 1, 'actas/1764089063_6925dce7c23c1_img1.png', 'actas/1764089063_6925dce7c257e_img2.png', 'actas/1764089063_6925dce7c26b4_img3.png', NULL, NULL, '2025-11-25 16:44:23', '2025-11-25 16:58:07'),
(182, NULL, '2025-11-26', 80, 'LUIS FORTUNATO PEREZ MURIANO', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/lIQY9bCOp6xPsmTZp31Zz5mnzE893uJDcNOZ9BME.pdf', 1, 'actas/1764184134_692750464551f_img1.png', 'actas/1764184134_69275046456dd_img2.png', 'actas/1764184134_69275046457c9_img3.png', NULL, NULL, '2025-11-26 19:08:54', '2025-11-26 19:19:36'),
(183, NULL, '2025-11-27', 123, 'CARMEN ROSA CHUMPITAZ VEGA', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', NULL, 0, 'actas/1764253886_692860be84e23_img1.JPG', NULL, NULL, NULL, NULL, '2025-11-27 14:31:26', '2025-11-27 14:31:26'),
(184, NULL, '2025-11-27', 123, 'CARMEN ROSA CHUMPITAZ VEGA', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', NULL, 0, 'actas/1764258986_692874aa717f1_img1.jpg', NULL, NULL, NULL, NULL, '2025-11-27 15:56:26', '2025-11-27 15:56:26'),
(185, NULL, '2025-11-27', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Ingreso de nuevo personal', 'Presencial', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/fN7R0p5Jo4eFQQKnI0rrG7ZqpGV8ICjgTVGs81Nu.pdf', 1, 'actas/1764273555_6928ad9386b8d_img1.jpg', 'actas/1764273555_6928ad9386c9c_img2.jpg', NULL, NULL, NULL, '2025-11-27 19:59:15', '2025-11-28 00:16:45'),
(186, NULL, '2025-12-01', 35, 'JORGE RODOLFO CHACALTANA SUAREZ', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', NULL, 0, 'actas/1764633040_692e29d09fa21_img1.JPG', NULL, NULL, NULL, NULL, '2025-12-01 23:50:40', '2025-12-01 23:50:40'),
(187, NULL, '2025-12-02', 8, 'CYNTHIA YACORI CORREA PEREZ', 'Otros', 'Presencial', 'Gutierrez Hilario Juan Carlos', 'asistencia', NULL, 0, 'actas/1764689401_692f05f98ba47_img1.jpg', NULL, NULL, NULL, NULL, '2025-12-02 15:30:01', '2025-12-02 15:30:01'),
(188, NULL, '2025-12-03', 105, 'HUGO RUBEN VELIZ YATACO', 'Otros', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', NULL, 0, 'actas/1764821057_69310841c2a11_img1.JPG', NULL, NULL, NULL, NULL, '2025-12-04 04:04:17', '2025-12-04 04:04:17'),
(189, NULL, '2025-12-05', 57, 'JUANA HAYDE INTIMAYTA SAYRITUPAC', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', NULL, 0, 'actas/1764942331_6932e1fb90849_img1.JPG', NULL, NULL, NULL, NULL, '2025-12-05 13:45:31', '2025-12-05 13:45:31'),
(190, NULL, '2025-12-05', 57, 'JUANA HAYDE INTIMAYTA SAYRITUPAC', 'Ingreso de nuevo personal', 'Virtual', 'Gutierrez Hilario Juan Carlos', 'asistencia', NULL, 0, 'actas/1764942565_6932e2e58b0b8_img1.JPG', NULL, NULL, NULL, NULL, '2025-12-05 13:49:25', '2025-12-05 13:49:25'),
(191, NULL, '2025-12-11', 116, 'MARIA DEL CARMEN TAIPE HUAYRA', 'Otros', 'Presencial', 'Donayre Salinas Jordan Roberto', 'asistencia', NULL, 0, 'actas/1765475031_693b02d7ea534_img1.jpeg', 'actas/1765475031_693b02d7ea69e_img2.jpeg', NULL, NULL, NULL, '2025-12-11 16:51:16', '2025-12-11 17:43:51'),
(192, NULL, '2025-12-11', 131, 'VILMA ARIAS MUNAYCO', 'Otros', 'Presencial', 'Donayre Salinas Jordan Roberto', 'asistencia', NULL, 0, 'actas/1765479074_693b12a2abc79_img1.jpeg', 'actas/1765479074_693b12a2abd67_img2.jpeg', NULL, NULL, NULL, '2025-12-11 18:47:27', '2025-12-11 18:51:14'),
(193, NULL, '2025-12-15', 16, 'VICTOR MANUEL NUÑEZ SANCHEZ', 'Otros', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', NULL, 0, 'actas/1765821772_69404d4c46b0a_img1.png', 'actas/1765821772_69404d4c46c5a_img2.png', 'actas/1765821772_69404d4c46d91_img3.png', 'actas/1765821772_69404d4c46e93_img4.png', NULL, '2025-12-15 18:02:52', '2025-12-15 18:02:52'),
(194, NULL, '2025-12-19', 131, 'VILMA ARIAS MUNAYCO', 'Otros', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/1H78q7gdou36tiu2JBf8ecYSVDBDMc9Xsy5MrQD2.pdf', 1, 'actas/1766195294_6946005e25748_img1.png', NULL, NULL, NULL, NULL, '2025-12-20 01:48:14', '2025-12-20 05:21:53'),
(195, NULL, '2025-12-20', 65, 'JOSE CARLOS VALLE BRAVO', 'Reactivación de módulo', 'Presencial', 'Gutierrez Hilario Juan Carlos', 'asistencia', 'actas_firmadas/dJB66w7zGgq6GmgFoFBeLfBSrLE3iIYoQ6YzVcAO.pdf', 1, 'actas/1766243020_6946bacce88aa_img1.jpeg', 'actas/1766243020_6946bacceeb76_img2.jpeg', NULL, NULL, NULL, '2025-12-20 14:32:54', '2025-12-21 13:43:59'),
(196, NULL, '2025-12-21', 118, 'EPIFANIA ALEJANDRINA GARAYAR CASAVILCA', 'Reactivación de módulo', 'Virtual', 'Yañez Medina Lida Graciela', 'asistencia', 'actas_firmadas/T8jns2RQNk0kM69XSsYuu19YTQNjgNWqlvUTRp4Y.pdf', 1, 'actas/1766322371_6947f0c34daa6_img1.png', NULL, NULL, NULL, NULL, '2025-12-21 13:06:11', '2025-12-21 13:45:11'),
(197, NULL, '2025-12-21', 119, 'AMELIA FERNANDA SORIA SARAVIA', 'Ingreso de nuevo personal', 'Virtual', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/gVk5CzhOmsVS0furQUrA51cpCaNPR7j74wQrbxgV.pdf', 1, 'actas/1766324689_6947f9d19fb62_img1.png', NULL, NULL, NULL, NULL, '2025-12-21 13:44:49', '2025-12-21 13:45:26'),
(198, NULL, '2025-12-22', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Reactivación de módulo', 'Presencial', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/HAAqKipJaqUfWONF6Vz2to2jFaDYiqnxoHQt3w7r.pdf', 1, 'actas/1766442956_6949c7cc556c2_img1.jpeg', 'actas/1766442956_6949c7cc61704_img2.jpeg', NULL, NULL, NULL, '2025-12-22 11:58:57', '2025-12-24 20:55:57'),
(199, NULL, '2025-12-24', 24, 'SANDY DANIEL CALDERA POLANCO', 'Otros', 'Presencial', 'Donayre Salinas Jordan Roberto', 'asistencia', 'actas_firmadas/kVm71et4lyXD9NOEH2H6C0l9cuUCJElYJYcPTIBU.pdf', 1, 'evidencias/MNDGU19xMb4r9qF3D0powTsZU8u7TB8MnN6FI42x.png', NULL, NULL, NULL, NULL, '2025-12-24 21:29:26', '2025-12-26 12:54:58'),
(200, 6, '2025-12-26', 93, 'ROXANA MELCHORITA CASTILLA GUILLÉN', 'Monitoreo de Servicios', 'Presencial', 'Donayre Salinas Jordan Roberto', 'monitoreo', NULL, 0, NULL, NULL, NULL, NULL, NULL, '2025-12-26 15:42:37', '2025-12-26 15:42:37'),
(201, 6, '2025-12-26', 77, 'AMELIA LOPEZ ALVA', 'Monitoreo de Servicios', 'Presencial', 'Donayre Salinas Jordan Roberto', 'monitoreo', NULL, 0, NULL, NULL, NULL, NULL, NULL, '2025-12-26 15:43:54', '2025-12-26 15:43:54'),
(202, 6, '2025-12-26', 130, 'VILLAMARES RAMOS EDWIN JESUS', 'Monitoreo de Servicios', 'Presencial', 'Donayre Salinas Jordan Roberto', 'monitoreo', NULL, 0, NULL, NULL, NULL, NULL, NULL, '2025-12-26 16:20:40', '2025-12-26 16:20:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades`
--

CREATE TABLE `actividades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `acta_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `actividades`
--

INSERT INTO `actividades` (`id`, `acta_id`, `descripcion`, `created_at`, `updated_at`) VALUES
(93, 11, 'CAPACITACION', '2025-09-30 22:22:53', '2025-09-30 22:22:53'),
(95, 2, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE CITAS', '2025-10-01 02:34:59', '2025-10-01 02:34:59'),
(96, 3, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE TRIAJE', '2025-10-01 02:36:28', '2025-10-01 02:36:28'),
(97, 5, 'SE DA ASISTENCIA TECNICA SOBRE EL MODULO DE CONSULTA EXTERNA, REACTIVANDO EL MODULO.', '2025-10-01 02:38:22', '2025-10-01 02:38:22'),
(98, 6, 'SE CAPACITA AL PERSONAL, SOBRE EL MODULO DE CONSULTA EXTERNA', '2025-10-01 02:40:04', '2025-10-01 02:40:04'),
(104, 14, 'SE REALIZO MONITOREO DEL MODULO, SE ELIMINO 2 PROFESIONALES POR TERMINO DE CONTRATO', '2025-10-01 17:14:48', '2025-10-01 17:14:48'),
(108, 15, 'REGISTRO DE CITAS DE PACIENTES QUE ACUDEN AL ESTABLECIMIENTO DE SALUD', '2025-10-01 17:33:15', '2025-10-01 17:33:15'),
(109, 16, 'REGISTRO DE TRIAJE DE PACIENTES QUE ACUDEN AL ESTABLECIMIENTO DE SALUD', '2025-10-01 17:33:28', '2025-10-01 17:33:28'),
(111, 17, 'REGISTRO DE CONSULTA EXTERNA DE PACIENTE QUE ACUDEN AL ESTABLECIMIENTO DE SALUD', '2025-10-01 17:37:52', '2025-10-01 17:37:52'),
(112, 18, 'SE LE CAPACITO AL PERSONAL MEDICO CON LA FIRMA ELECTRONICA DE SUS ATENCIONES DE FORMA MASIVA EN LA OPCION DE BAND. DOCUME. ELECTRONICO', '2025-10-01 22:32:17', '2025-10-01 22:32:17'),
(113, 19, 'SE LE CAPACITO AL PERSONAL MEDICO CON LA FIRMA ELECTRONICA DE SUS ATENCIONES DE FORMA MASIVA EN LA OPCION DE BAND. DOCUME. ELECTRONICO', '2025-10-01 22:38:02', '2025-10-01 22:38:02'),
(115, 21, 'SE LE DIO ASISTENCIA TECNICA AL PERSONAL MEDICO CON EL LLENADO DE ATENCION DEL PACIENTE EN EL SIHCE Y SE REALIZA FIRMA DIGITAL DE FORMA MASIVA EN LA OPCION DE BAND. DOCUME. ELECTRONICO', '2025-10-01 23:02:39', '2025-10-01 23:02:39'),
(116, 22, 'SE LE CAPACITO AL PERSONAL MEDICO EL LLENADO DE SUS ATENCIONES DE PACIENTE EN EL SIHCE CON LA FIRMA ELECTRONICA DE SUS ATENCIONES DE FORMA MASIVA EN LA OPCION DE BAND. DOCUME. ELECTRONICO', '2025-10-01 23:16:13', '2025-10-01 23:16:13'),
(118, 24, 'SE LE DIO ASISTENCIA TECNICA AL PERSONAL MEDICO CON EL LLENADO DE ATENCION DEL PACIENTE EN EL SIHCE CON REFERENCIA Y SE REALIZA FIRMA DIGITAL DE FORMA MASIVA EN LA OPCION DE BAND. DOCUME. ELECTRONICO', '2025-10-02 00:07:57', '2025-10-02 00:07:57'),
(121, 20, 'SE LE DIO UNA ASISTENCIA TECNICA AL PERSONAL DE CITAS PARA QUE REPROGRAMEN Y ELIMINEN CITAS', '2025-10-02 00:19:30', '2025-10-02 00:19:30'),
(124, 26, 'SE LE DIO UNA ASISTENCIA TECNICA AL PERSONAL DE G.A. PARA LA PROGRAMACION DE MEDICOS', '2025-10-02 00:38:44', '2025-10-02 00:38:44'),
(129, 28, 'ASISTENCIA TECNICA A MEDICO SOBRE LLENADO DE MODULO DE CONSULTA EXTERNA', '2025-10-02 04:28:06', '2025-10-02 04:28:06'),
(130, 28, 'FIRMA DIGITAL DE FORMA MASIVA', '2025-10-02 04:28:06', '2025-10-02 04:28:06'),
(131, 29, 'REGISTRO DE TRIAJE DE PACIENTES QUE ACUDEN AL ESTABLECIMIENTO DE SALUD', '2025-10-02 04:40:34', '2025-10-02 04:40:34'),
(132, 30, 'SE CAPACITO PARA MANEJO DE MODULO CONSULTA EXTERNA -  MEDICINA', '2025-10-02 04:45:39', '2025-10-02 04:45:39'),
(133, 31, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA Y REGISTRO DE PACIENTE TRIADO', '2025-10-02 05:12:24', '2025-10-02 05:12:24'),
(134, 33, 'SE REALIZO MONITOREO DEL MODULO TRIAJE, MEDICINA Y FIRMA', '2025-10-02 05:26:43', '2025-10-02 05:26:43'),
(137, 35, 'MONITOREO DE REGISTRO DE ATENCIONES EN EL MODULO DE CONSULTA EXTERNA', '2025-10-02 12:09:20', '2025-10-02 12:09:20'),
(139, 37, 'SELE DIO ASISTENCIA TECNICA AL PERSONAL DE TRIAJE YA QUE NO SE ESTABA VISUALIZANDO LOS PACIENTE TRIADO EN EL MODULO DE CONSULTA EXTERNA', '2025-10-02 12:47:21', '2025-10-02 12:47:21'),
(145, 39, 'SE CAPACITO AL PERSONAL SOBRE INGRESO DE NUEVO PERSONAL MEDICO AL ESTABLECIMIENTO', '2025-10-02 13:44:15', '2025-10-02 13:44:15'),
(149, 9, 'SE REALIZA LA ASISTENCIA AL PERSONAL ENCARGADO DE GESTION ADMINISTRATIVA.', '2025-10-02 14:42:16', '2025-10-02 14:42:16'),
(150, 9, 'SE REALIZA LA CAPACITACION AL PERSONAL MEDICO.', '2025-10-02 14:42:16', '2025-10-02 14:42:16'),
(152, 10, 'SE REALIZA LA ASISTENCIA AL PERSONAL ENCARGADO DEL MODULO DE GESTION ADMINISTRATIVA.', '2025-10-02 14:54:24', '2025-10-02 14:54:24'),
(156, 43, 'SE LE DIO ASISTENCIA TECNICA AL PERSONAL NOMBRADO DE MEDICINA PARA COMPLETAR EL PROCESO DE ATENCION EN EL SIHCE', '2025-10-02 15:58:41', '2025-10-02 15:58:41'),
(157, 44, 'SE ASISTE AL PERSONAL DE OBSTETRICIA EN CUANTO A LOS MODULOS QUE VIENEN UTILIZANDO EN EL SIHCE', '2025-10-02 16:15:22', '2025-10-02 16:15:22'),
(158, 44, 'SE BRINDA INFORMACION SOBRE EL FLUJO DEL SIHCE EN CUANTO A LOS DEMAS SERVICIOS.', '2025-10-02 16:15:22', '2025-10-02 16:15:22'),
(160, 46, 'SE LE DIO ASISTENCIA TECNICA DE INSTALACION DE DRIVERS PARA LA FIRMA DIGITAL PARA EL CONSULTORIO DE GINECOLOGIA', '2025-10-02 17:48:02', '2025-10-02 17:48:02'),
(161, 42, 'SE REALIZA LA ASISTENCIA AL PERSONAL MEDICO EN LA SECCION DE RECETAS.', '2025-10-02 18:02:44', '2025-10-02 18:02:44'),
(163, 23, 'SE LE DIO UNA ASISTENCIA TECNICA AL PERSONAL DE G.A. PARA LA PROGRAMACION MEDICA DE UN PERSONAL DE LA SALUD QUE SE LE DIFICULTABA HACERLE LA PROGRAMACION EN TURNO TARDE Y SE LE ENSEÑO A SINCRONIZAR SUS PROGRAMACION', '2025-10-02 18:48:33', '2025-10-02 18:48:33'),
(164, 36, 'SE LE DIO UNA ASISTENCIA TECNICA AL PERSONAL DE VENTANILLA UNICA PARA CONFIRMAR A LOS PACIENTES PAGANTES Y REPROGRAMAR A LOS PACIENTES', '2025-10-02 19:01:39', '2025-10-02 19:01:39'),
(165, 25, 'SE LE DIO UNA ASISTENCIA TECNICA AL PERSONAL DE TRIAJE PARA QUE LOS MEDICOS DE CONSULTA EXTERNA PUEDAN VISUALIZAR A LOS PACIENTES', '2025-10-02 19:41:18', '2025-10-02 19:41:18'),
(166, 47, 'SE LE DIO UNA ASISTENCIA TECNICA AL PERSONAL DE G.A. PARA LA PROGRAMACION MEDICA DE UN PERSONAL DE LA SALUD QUE SE LE DIFICULTABA HACERLE LA PROGRAMACION EN TURNO TARDE Y SE LE ENSEÑO A SINCRONIZAR SUS PROGRAMACION', '2025-10-02 20:20:50', '2025-10-02 20:20:50'),
(169, 48, 'SE LE CAPACITO AL PERSONAL MEDICO EL MODULO DE CONSULTA EXTERNA CON LA FIRMA ELECTRONICA DE SUS ATENCIONES DE FORMA MASIVA EN LA OPCION DE BAND. DOCUME. ELECTRONICO', '2025-10-02 20:47:38', '2025-10-02 20:47:38'),
(172, 40, 'SE REALIZO MONITOREO DEL MODULO DE TRIAJE Y CONSULTA EXTERNA - PROBLEMAS DE ESCASEZ DE COMPUTADORAS', '2025-10-02 21:22:51', '2025-10-02 21:22:51'),
(173, 49, 'SE CAPACITO AL PERSONAL MEDICO, SOBRE EL USO DEL MODULO, CON FIRMA DIGITAL', '2025-10-02 21:24:33', '2025-10-02 21:24:33'),
(174, 34, 'MONITOREO DE REGISTRO DE INFORMACION EN MODULOS', '2025-10-02 21:26:30', '2025-10-02 21:26:30'),
(175, 34, 'ACOMPAÑAMIENTO EN REGISTRO DE INFORMACION EN MODULO DE CONSULTA EXTERNA', '2025-10-02 21:26:30', '2025-10-02 21:26:30'),
(176, 32, 'ACOPAÑAMIENTO EN REGISTRO DE TRIAJE DE PACIENTES QUE ACUDEN AL ESTABLECIMIENTO DE SALUD', '2025-10-02 21:31:47', '2025-10-02 21:31:47'),
(177, 32, 'CAPACITACION A MEDICOS - MODULO DE CONSULTA ENTERNA', '2025-10-02 21:31:47', '2025-10-02 21:31:47'),
(178, 41, 'ACOMPAÑAMIENTO EN CAPACITACION - REGISTRO DE CONSULTA EXTERNA DE PACIENTES', '2025-10-02 21:35:04', '2025-10-02 21:35:04'),
(180, 50, 'SE CAPACITA AL PERSONAL, SOBRE EL MODULO DE CITAS', '2025-10-03 00:10:30', '2025-10-03 00:10:30'),
(193, 51, 'CAPACITACION DE MODULO de CONSULTA EXTERNA - MEDICINA', '2025-10-06 02:21:47', '2025-10-06 02:21:47'),
(196, 54, 'Se realizó una visita al Establecimiento de Salud Carmen El Olivo junto con el Ing. Erick Montes y con el acompañamiento de la Dra. Stephanie Fernández, representante de la Red de Salud Ica, con el propósito de brindar capacitación sobre el Módulo de Consulta Externa del Sistema de Historia Clínica Electrónica (SIHCE). Durante la conversación con la Dra. Cecilia García, médico del establecimiento, se le explico sobre las funcionalidades y ventajas del uso del sistema SIHCE para optimizar la atención médica y fortalecer la gestión de información clínica.', '2025-10-06 03:58:31', '2025-10-06 03:58:31'),
(197, 55, 'SE CAPACITO A PERSONAL PARA APOYO EN MANEJO DEL MODULO CONSULTA EXTERNA - MEDICINA', '2025-10-06 04:11:43', '2025-10-06 04:11:43'),
(198, 56, 'SE ASISTE A PERSONAL MEDICO EN EL MODULO DE CONSULTA EXTERNA.', '2025-10-06 16:05:30', '2025-10-06 16:05:30'),
(199, 56, 'SE REVISA LA MIGRACION DE LAS ATENCIONES REGISTRADAS.', '2025-10-06 16:05:30', '2025-10-06 16:05:30'),
(200, 57, 'SE CAPACITA AL PERSONAL, SOBRE PACIENTES CON SIS Y NO SIS,', '2025-10-06 17:55:54', '2025-10-06 17:55:54'),
(217, 62, 'SE REALIZAO FIRMA DE DOCUMENTO DE PACIENTES CRED', '2025-10-07 17:07:45', '2025-10-07 17:07:45'),
(218, 63, 'SE REALIZO FIRMA DE DOCUMENTOS DE PACIENTES CRED DE FORMA MASIVA', '2025-10-07 17:16:12', '2025-10-07 17:16:12'),
(219, 64, 'SE CAPACITA AL PERSONAL, SOBRE EL ERROR DE NO RECONOCER SU CERTIFICADO DE FIRMA DIGITAL, EL CUAL SE REALIZA LAS PRUEBAS CORRESPONDIENTES, PARA EL RECONOCIMIENTO DEL CERTIFICADO.', '2025-10-09 18:09:03', '2025-10-09 18:09:03'),
(221, 66, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA', '2025-10-09 20:18:44', '2025-10-09 20:18:44'),
(222, 67, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA', '2025-10-09 20:19:35', '2025-10-09 20:19:35'),
(232, 72, 'SE CAPACITA AL PERSONAL, SOBRE CONSULTAS QUE TIENE SOBRE EL MODULO, CON PACIENTES SIS Y NO SIS', '2025-10-09 21:28:46', '2025-10-09 21:28:46'),
(233, 73, 'SE CAPACITA AL PERSONAL, SOBRE EL MODULO DE TRIAJE, DESPEJANDO DUDAS SOBRE EL LLENADO DE LOS CAMPOS QUE REQUIERE EL MODULO', '2025-10-09 21:30:58', '2025-10-09 21:30:58'),
(243, 61, 'SE REVISA EL USO DE LA FIRMA ELECTRONICA POR PARTE DE LOS MEDICOS.', '2025-10-10 16:05:59', '2025-10-10 16:05:59'),
(254, 82, 'Instalación de driver necesario para el Lector de DNIe', '2025-10-11 01:45:08', '2025-10-11 01:45:08'),
(255, 82, 'Explicación de la Firma Digital en el módulo de Consulta Externa-Medicina', '2025-10-11 01:45:08', '2025-10-11 01:45:08'),
(256, 75, 'REVISION DE ERROR AL REALIZAR FIRMA DIGITAL', '2025-10-11 01:55:18', '2025-10-11 01:55:18'),
(259, 65, 'REVISIÓN DE MENSAJE DE ERROR AL GUARDAR LA ATENCIÓN DE UN PACIENTE', '2025-10-11 01:57:33', '2025-10-11 01:57:33'),
(260, 45, 'Se apoya al médico en la firma de una atención', '2025-10-11 01:57:57', '2025-10-11 01:57:57'),
(261, 38, 'Instalación de drivers necesarios en el equipo del consultorio de medicina.', '2025-10-11 01:58:20', '2025-10-11 01:58:20'),
(262, 38, 'Reseteo de PIN de DNIe', '2025-10-11 01:58:20', '2025-10-11 01:58:20'),
(263, 38, 'Explicación de firma digital en el módulo de Medicina', '2025-10-11 01:58:20', '2025-10-11 01:58:20'),
(264, 27, 'SE APOYÓ EN LA MIGRACIÓN DE UNA ATENCIÓN EN EL MÓDULO DE CRED', '2025-10-11 01:59:00', '2025-10-11 01:59:00'),
(265, 13, 'SE APOYÓ A LA MÉDICO EN EL CASO DE UN PACIENTE QUE FUE CITADO DIAS ATRAS Y NO APARECÍA EN LA LISTA POR ATENDER DE HOY', '2025-10-11 01:59:36', '2025-10-11 01:59:36'),
(266, 12, 'SE APOYÓ A LA MÉDICO EN LA INSTALACIÓN DE LOS DRIVERS NECESARIOS PARA LA FIRMA DIGITAL', '2025-10-11 01:59:57', '2025-10-11 01:59:57'),
(267, 8, 'CAPACITACION', '2025-10-11 02:00:29', '2025-10-11 02:00:29'),
(268, 7, 'CREACIÓN DE NUEVO HORARIO Y PROGRAMACIÓN DE TURNO', '2025-10-11 02:00:50', '2025-10-11 02:00:50'),
(269, 4, 'SE BRINDÓ SOPORTE PARA RESOLVER UN CASO DEL MÓDULO DE CITAS', '2025-10-11 02:01:12', '2025-10-11 02:01:12'),
(270, 1, 'SE CAPACITA AL IMPLEMENTADOR MARIN', '2025-10-11 02:01:37', '2025-10-11 02:01:37'),
(274, 84, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE GESTION ADMINISTRATIVA, EN PROGRAMACION DE TURNOS SEGUN LA DIRECTIVA 378', '2025-10-11 15:33:44', '2025-10-11 15:33:44'),
(275, 83, 'CAPACITACIÓN SOBRE EL USO DEL MÓDULO DE CONSULTA EXTERNA-MEDICINA SIHCE', '2025-10-11 15:59:23', '2025-10-11 15:59:23'),
(276, 85, 'SE LE DIO UNA ASISTENCIA TECNICA AL PERSONAL INFORMATICO PARA CAMBIAR LA PROGRAMACION DE HORARIO DE UN CONSULTORIO', '2025-10-11 17:46:52', '2025-10-11 17:46:52'),
(277, 86, 'SE LE DIO UNA ASISTENCIA TECNICA AL PERSONAL DE SOPORTE INFORMATICO PARA QUE EL MEDICO FIRME SUS ATENCIONES DEL MES DE OCTUBRE', '2025-10-11 18:31:24', '2025-10-11 18:31:24'),
(279, 88, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE MEDICINA, ABSOLVIENDO DUDAS DE REGISTRO DE ANTECEDENTES Y MEDICAMENTOS INGRESADOS', '2025-10-11 19:20:06', '2025-10-11 19:20:06'),
(280, 87, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE TRIAJE', '2025-10-11 19:20:32', '2025-10-11 19:20:32'),
(283, 53, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA A PERSONAL MEDICO Y ESTADISTICO.', '2025-10-12 00:22:09', '2025-10-12 00:22:09'),
(285, 58, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA A PERSONAL MEDICO Y ESTADISTICO', '2025-10-12 00:34:19', '2025-10-12 00:34:19'),
(286, 52, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA A PERSONAL MEDICO Y ESTADISTICO.', '2025-10-12 00:46:30', '2025-10-12 00:46:30'),
(287, 59, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA A PERSONAL MEDICO Y ESTADISTICO.', '2025-10-12 00:57:23', '2025-10-12 00:57:23'),
(288, 68, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA A PERSONAL MEDICO Y ESTADISTICO.', '2025-10-12 01:20:34', '2025-10-12 01:20:34'),
(291, 69, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA A PERSONAL MEDICO Y ESTADISTICO.', '2025-10-12 01:57:26', '2025-10-12 01:57:26'),
(293, 71, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA A PERSONAL MEDICO Y ESTADISTICO.', '2025-10-12 02:59:15', '2025-10-12 02:59:15'),
(294, 76, 'CAPACITACION DE MODULO DE CONSULTA CITAS, TRIAJE Y CONSULTA EXTERNA', '2025-10-12 03:32:20', '2025-10-12 03:32:20'),
(296, 70, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA A PERSONAL MEDICO Y ESTADISTICO.', '2025-10-12 04:00:39', '2025-10-12 04:00:39'),
(297, 77, 'CAPACITACION DE MODULO DE CITAS, TRIAJE Y CONSULTA EXTERNA', '2025-10-12 04:04:14', '2025-10-12 04:04:14'),
(299, 78, 'CAPACITACION DE MODULO DE CITAS, TRIAJE Y  CONSULTA EXTERNA', '2025-10-12 04:30:42', '2025-10-12 04:30:42'),
(301, 79, 'CAPACITACION DE MODULO DE CITAS, TRIAJE Y  CONSULTA EXTERNA', '2025-10-12 04:54:58', '2025-10-12 04:54:58'),
(302, 80, 'CAPACITACION DE MODULO DE CITAS, TRIAJE Y  CONSULTA EXTERNA', '2025-10-12 05:07:53', '2025-10-12 05:07:53'),
(303, 81, 'CAPACITACION DE MODULO DE CITAS, TRIAJE Y  CONSULTA EXTERNA', '2025-10-12 05:27:06', '2025-10-12 05:27:06'),
(304, 89, 'ASISTENCIA TECNICA EN EL MODULO DE CONSULTA EXTERNA A PERSONAL MEDICO Y ESTADISTICO', '2025-10-13 02:48:47', '2025-10-13 02:48:47'),
(305, 90, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO Y ESTADISTICO', '2025-10-13 02:50:59', '2025-10-13 02:50:59'),
(306, 91, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO', '2025-10-13 02:53:19', '2025-10-13 02:53:19'),
(307, 92, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO Y ESTADISTICO', '2025-10-13 02:55:58', '2025-10-13 02:55:58'),
(315, 93, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO Y ESTADISTICO', '2025-10-13 03:02:49', '2025-10-13 03:02:49'),
(316, 94, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO Y ESTADISTICO', '2025-10-13 03:06:41', '2025-10-13 03:06:41'),
(317, 95, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO', '2025-10-13 03:09:21', '2025-10-13 03:09:21'),
(318, 96, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO, ESTADISTICO Y TRIAJE', '2025-10-13 03:12:43', '2025-10-13 03:12:43'),
(319, 97, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO', '2025-10-13 03:14:31', '2025-10-13 03:14:31'),
(320, 98, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO Y ESTADISTICO', '2025-10-13 03:16:01', '2025-10-13 03:16:01'),
(321, 99, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO Y ESTADISTICO', '2025-10-13 03:21:02', '2025-10-13 03:21:02'),
(322, 100, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO Y ESTADISTICO', '2025-10-13 03:22:31', '2025-10-13 03:22:31'),
(323, 101, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO', '2025-10-13 03:24:48', '2025-10-13 03:24:48'),
(324, 102, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO', '2025-10-13 03:26:00', '2025-10-13 03:26:00'),
(325, 103, 'CAPACITACION MODULO DE GESTION ADMINISTRATIVA, AGREGAR MEDICO NUEVO', '2025-10-13 16:55:11', '2025-10-13 16:55:11'),
(326, 104, 'SE CAPACITO AL PERSONA, SOBRE EL MODULO DE CONSULTA EXTERNA, MOSTRANDO ANTECEDENTES Y REGISTRO SEGUN DIAGNOSTICOS, PROCEDIMIENTOS, Y FIRMA DIGITAL', '2025-10-13 20:29:03', '2025-10-13 20:29:03'),
(327, 105, 'SE LE CAPACITO AL PERSONAL MEDICO CON LA FIRMA ELECTRONICA DE SUS ATENCIONES DE FORMA MASIVA EN LA OPCION DE BAND. DOCUME. ELECTRONICO', '2025-10-13 22:47:05', '2025-10-13 22:47:05'),
(328, 74, 'RESOLUCIÓN DE DUDAS SOBRE EL MÓDULO DE TRIAJE', '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(329, 74, 'INSTALACIÓN DE DRIVERS NECESARIOS PARA LA FIRMA DIGITAL DEL MÉDICO', '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(330, 74, 'CAPACITACIÓN DEL MÓDULO DE CONSULTA EXTERNA-MEDICINA', '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(332, 106, 'CAPACITACIÓN SOBRE EL USO DEL MÓDULO DE CONSULTA EXTERNA-MEDICINA SIHCE', '2025-10-14 00:39:54', '2025-10-14 00:39:54'),
(346, 113, 'CAPACITACION DE MODULO DE TRIAJE', '2025-10-14 20:38:36', '2025-10-14 20:38:36'),
(349, 116, 'ASISTENCIA TECNICA EN CITAS Y TRIAJE - SE INDICO PROCEDIMIENTO PARA VISUALZAR EN TRIAJE A PACIENTES QUE NO TENIAN SIS.', '2025-10-14 23:41:37', '2025-10-14 23:41:37'),
(351, 109, 'ASISTENCIA TECNICA EN CITAS - PACIENTES SIN SIS', '2025-10-15 01:23:15', '2025-10-15 01:23:15'),
(352, 110, 'ASISTENCIA TECNICA EN MODULO DE TRIAJE', '2025-10-15 01:50:56', '2025-10-15 01:50:56'),
(353, 111, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA - MEDICIINA', '2025-10-15 02:13:08', '2025-10-15 02:13:08'),
(354, 112, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA - MEDICINA', '2025-10-15 02:20:29', '2025-10-15 02:20:29'),
(356, 117, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE CITAS, ABSOLVIENDO DUDAS DE PACIENTES QUE NO TIENEN SIS.', '2025-10-15 14:43:52', '2025-10-15 14:43:52'),
(358, 119, 'SE REALIZO ASISTENCIA TECNICA EN MODULO TRIAJE - SE INDICO EL PROCEDIMIENTO PARA AQUELLOS PACIENTES QUE NO TENIAN SIS, SE PUEDAN VISUALIZAR EN MODULO DE TRIAJE.', '2025-10-15 16:27:04', '2025-10-15 16:27:04'),
(359, 120, 'SE REALIZO MONITOREO DEL MODULO, Y NO HA REGISTRADO INGRESO DE DATOS DE PACIENTES EN MODULO DE MEDICINA DEL SISTEMA SIHCE POR PROBLEMAS DE CONEXION INDICA EL MEDICO, JEFE DE ESTABLECIMIENTO ORDENO CONEXION CON CABLEADO.', '2025-10-15 16:53:24', '2025-10-15 16:53:24'),
(360, 121, 'SE DA ASISTENCIA TECNICA SOBRE EL MODULO DE CONSULTA EXTERNA, INSTRUYENDO DESDE ANTECEDENTES HASTA FIRMA DIGITAL', '2025-10-15 17:20:30', '2025-10-15 17:20:30'),
(361, 122, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE GESTION ADMINISTRATIVCA, INDICADO LOS CUPOS ADICIONALES, CAMBIO DE TURNO', '2025-10-15 20:14:02', '2025-10-15 20:14:02'),
(362, 123, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE CITAS', '2025-10-15 20:46:23', '2025-10-15 20:46:23'),
(363, 124, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO', '2025-10-16 00:42:14', '2025-10-16 00:42:14'),
(364, 125, 'SE REALIZO ASISTENCIA TECNICA EN MODULO DE GESTION ADMINISTRATIVA - SE UTLIZO ANYDESK PARA GUIAR TODO EL PROCEDIMIENTO PARA EL REGISTRO DE NUEVOS PROFESIONALES DE LA SALUD Y PROGRAMAR SUS HORARIOS.', '2025-10-16 00:53:44', '2025-10-16 00:53:44'),
(365, 108, 'ASISTENCIA TECNICA EN CITAS', '2025-10-16 03:07:18', '2025-10-16 03:07:18'),
(366, 114, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA - MEDICINA', '2025-10-16 03:12:19', '2025-10-16 03:12:19'),
(367, 115, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA - MEDICINA', '2025-10-16 03:15:22', '2025-10-16 03:15:22'),
(377, 134, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA AL PERSONAL MEDICO', '2025-10-17 15:47:36', '2025-10-17 15:47:36'),
(378, 135, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE CITAS, DIFERENCIANDO LOS PACIENTES SIS Y NO SIS.', '2025-10-17 15:58:38', '2025-10-17 15:58:38'),
(379, 136, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE TRIAJE, REGISTRANDO LOS DATOS VITALES.', '2025-10-17 15:59:40', '2025-10-17 15:59:40'),
(380, 126, 'ASISTENCIA TECNICA EN MODULO DE CITAS', '2025-10-17 16:56:16', '2025-10-17 16:56:16'),
(381, 127, 'ASISTENCIA TECNICA EN TRIAJE', '2025-10-17 16:56:44', '2025-10-17 16:56:44'),
(382, 128, 'ASISTENCIA TECNICA', '2025-10-17 16:57:41', '2025-10-17 16:57:41'),
(383, 129, 'ASISTENCIA TECNICA AL MODULO DE GESTION ADMINISTRATIVA', '2025-10-17 16:59:54', '2025-10-17 16:59:54'),
(386, 137, 'SE REALIZO FIRMA MASIVA DE ATENCIONES en MODULO DE CONSULTA EXTERNA - MEDICINA.', '2025-10-20 00:25:47', '2025-10-20 00:25:47'),
(392, 130, 'ASISTENCIA TECNICA AL MODULO DE GESTION ADMINISTRATIVA', '2025-10-20 01:41:47', '2025-10-20 01:41:47'),
(394, 131, 'ASISTENCIA TECNICA EN MODULO', '2025-10-20 01:56:14', '2025-10-20 01:56:14'),
(395, 132, 'ASISTENCIA TECNICA EN TRIAJE', '2025-10-20 04:01:02', '2025-10-20 04:01:02'),
(397, 133, 'ASISTENCIA TECNICA EN MODULO CONSULTA EXTERNA - MEDICINA', '2025-10-20 04:48:50', '2025-10-20 04:48:50'),
(398, 138, 'SE LE DIO UNA ASISTENCIA TECNICA AL PERSONAL INFORMATICO PARA CAMBIAR LA PROGRAMACION DE HORARIO DE UN CONSULTORIO', '2025-10-21 14:26:59', '2025-10-21 14:26:59'),
(407, 60, 'SE REALIZA LA ASISTENCIA AL PERSONAL MEDICO EN CUANTO AL SEGUIMIENTO DEL INDICADOR 34.', '2025-10-22 17:19:23', '2025-10-22 17:19:23'),
(408, 60, 'SE ORIENTA AL PERSONAL MEDICO EN CUANTO AL USO DEL DNI ELECTRONICO.', '2025-10-22 17:19:23', '2025-10-22 17:19:23'),
(409, 141, 'SE REALIZA LA ASISTENCIA AL PERSONAL MEDICO EN LA SECCION DE RECETAS.', '2025-10-22 22:09:19', '2025-10-22 22:09:19'),
(410, 141, 'SE REVISA LA MIGRACION DE LAS ATENCIONES REGISTRADAS.', '2025-10-22 22:09:19', '2025-10-22 22:09:19'),
(411, 142, 'SE ELIMINA PROFESIONAL DE SALUD QUE YA TERMINO SUS FUNCIONES DENTRO DEL CENTRO DE SALUD, SE REEMPLAZA POR OTRO PROFESIONAL', '2025-10-22 22:21:54', '2025-10-22 22:21:54'),
(414, 144, 'SE REALIZA LA ASISTENCIA AL PERSONAL MEDICO EN CUANTO AL SEGUIMIENTO DEL INDICADOR 34.', '2025-10-22 22:35:55', '2025-10-22 22:35:55'),
(415, 144, 'SE REVISA LA MIGRACION DE LAS ATENCIONES REGISTRADAS.', '2025-10-22 22:35:55', '2025-10-22 22:35:55'),
(418, 146, 'SE INSTALA LOS DRIVERS PARA LA FIRMA ELECTRONICA EN NUEVO EQUIPO ASIGNADO AL CONSULTORIO DE MEDICINA', '2025-10-22 22:45:55', '2025-10-22 22:45:55'),
(419, 139, 'SE BRINDA ASISTENCIA TECNICA EN CUANTO AL USO DEL MODULO DE CITAS', '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(420, 139, 'SE BRINDA ASISTENCIA TECNICA EN CUANTO AL USO DEL MODULO DE TRIAJE', '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(421, 139, 'SE BRINDA ASISTENCIA TECNICA EN CUANTO AL USO DEL MODULO DE CONSULTA EXTERNA-MEDICINA', '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(422, 139, 'SE BRINDA ASISTENCIA TECNICA EN CUANTO AL USO DEL MODULO DE CRED', '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(425, 148, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE CITAS', '2025-10-23 16:51:15', '2025-10-23 16:51:15'),
(426, 149, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE TRIAJE', '2025-10-23 16:54:43', '2025-10-23 16:54:43'),
(427, 140, 'CAPACITACIÓN SOBRE EL USO DEL MÓDULO DE CONSULTA EXTERNA-MEDICINA SIHCE', '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(428, 140, 'SE RESOLVIERON DUDAS EN CUANTO AL USO DEL MODULO DE CITAS', '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(429, 140, 'SE RESOLVIERON DUDAS EN CUANTO AL USO DEL MODULO DE TRIAJE', '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(430, 140, 'SE BRINDÓ APOYO EN LA CONFIGURACIÓN DE CUPOS ADICIONALES EN LOS SERVICIOS DE MEDICINA Y ODONTOLOGIA', '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(431, 140, 'CONFIGURACIÓN DE LAPTOP Y COMPUTADORA DEL ESTABLECIMIENTO PARA LA FIRMA DIGITAL', '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(433, 151, 'APOYO CON LA CREACION DE HISTORIA CLINICA DE PACIENTE EXTRANJERO QUE NO FIGURABA EN EL SIHCE', '2025-10-24 21:44:22', '2025-10-24 21:44:22'),
(434, 151, 'APOYO CON LA CREACION DE HISTORIA CLINICA DE UN MENOR DE EDAD', '2025-10-24 21:44:22', '2025-10-24 21:44:22'),
(435, 150, 'SE DA ASISTENCIA TECNICA SOBRE EL MODULO DE CONSULTA EXTERNA, INCLUYENDO LA OPCION DE ANTECEDENTES', '2025-10-24 21:51:04', '2025-10-24 21:51:04'),
(437, 153, 'SE CAPACITA AL PERSONAL EN EL MODULO DE TRIAJE', '2025-10-27 21:33:47', '2025-10-27 21:33:47'),
(438, 152, 'SE CAPACITA AL PERSONAL EN EL MODULO DE CITAS', '2025-10-27 21:34:38', '2025-10-27 21:34:38'),
(439, 154, 'CAPACITACIÓN SOBRE EL USO DEL MÓDULO DE CONSULTA EXTERNA-MEDICINA SIHCE', '2025-10-27 21:39:13', '2025-10-27 21:39:13'),
(440, 155, 'SE REVISA LA PROGRAMACION DE TURNOS.', '2025-10-28 16:30:31', '2025-10-28 16:30:31'),
(441, 155, 'SE VERIFICAN LOS CUPOS ADICIONALES POR CADA SERVICIO.', '2025-10-28 16:30:31', '2025-10-28 16:30:31'),
(442, 143, 'SE ASISTE A PERSONAL MEDICO EN EL MODULO DE CONSULTA EXTERNA.', '2025-10-28 16:45:27', '2025-10-28 16:45:27'),
(443, 143, 'SE REVISA LA MIGRACION DE LAS ATENCIONES REGISTRADAS.', '2025-10-28 16:45:27', '2025-10-28 16:45:27'),
(446, 145, 'SE REALIZA UN REPASO DE LAS DIVERSAS FUNCIONES DEL MODULO', '2025-10-28 16:50:00', '2025-10-28 16:50:00'),
(447, 145, 'SE PREVIENE AL USUARIO SOBRE POSIBLES ESCENARIOS A LOS QUE SE ENFRENTARA HACIENDO USO DEL MODULO', '2025-10-28 16:50:00', '2025-10-28 16:50:00'),
(449, 156, 'SE REALIZA LA ASISTENCIA AL PERSONAL ENCARGADO DEL MODULO DE GESTION ADMINISTRATIVA.', '2025-10-28 23:13:23', '2025-10-28 23:13:23'),
(452, 157, 'SE REVISAN LOS DRIVERS DEL EQUIPO, EL MEDICO REPORTA NO PODER FIRMAR SUS ATENCIONES.', '2025-10-29 00:23:38', '2025-10-29 00:23:38'),
(453, 157, 'EL MEDICO LOGRA FIRMAR EXITOSAMENTE SUS ATENCIONES.', '2025-10-29 00:23:38', '2025-10-29 00:23:38'),
(456, 158, 'ASISTENCIA TECNICA AL MODULO DE GESTION ADMINISTRATIVA Y CITAS. SE ADSOLVIO CASOS COMO PACIENTES QUE NO TENIA SIS O EXTRANJEROS QUE NO SE VEIAN EN TRIAJE, Y OTROS.', '2025-10-29 20:51:29', '2025-10-29 20:51:29'),
(458, 159, 'SE LE DIO UNA ASISTENCIA TECNICA A TODO EL PERSONAL DE LOS MODULOS DE GESTION ADMINISTRATIVA, CITAS, TRIAJE Y CONSULTA EXTERNA', '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(459, 147, 'SE REALIZO ASISTENCIA TECNICA EN MODULO DE CITAS Y TRIAJE - SE INDICO EL PROCEDIMIENTO PARA INGRESO CORRECTO DE DATOS EN TRIAJE PARA QUE MODULO DE CONSULTA EXTERNA NO TENGA QUE VOLVER A DIGITARLOS', '2025-10-29 21:06:31', '2025-10-29 21:06:31'),
(461, 118, 'SE REALIZO ASISTENCIA TECNICA EN MODULO DE CITAS Y TRIAJE - SE INDICO EL PROCEDIMIENTO PARA AQUELLOS PACIENTES QUE NO TENIAN SIS, Y SE PUEDAN VISUALIZAR EN MODULO DE TRIAJE.', '2025-10-30 02:09:47', '2025-10-30 02:09:47'),
(464, 160, 'ASISTENCIA TECNICA AL MODULO DE TRIAJE - PACIENTES QUE NO TENIAN SIS NO PODIAN VISUALIZARSE EN TRIAJE Y  TAMPOCO EN CONSULTA EXTERNA - MEDICINA', '2025-10-30 15:54:06', '2025-10-30 15:54:06'),
(466, 161, 'APOYO CON EL GUARDADO DE LA ATENCIÓN DE UN PACIENTE, APARECIA UN ERROR AL MOMENTO DE SELECCIONAR COMO DESTINO DE LA ATENCION EN CITADO', '2025-11-05 15:39:58', '2025-11-05 15:39:58'),
(467, 162, 'SE REALIZA LA ASISTENCIA AL PERSONAL ENCARGADO DEL MODULO DE GESTION ADMINISTRATIVA.', '2025-11-13 23:12:38', '2025-11-13 23:12:38'),
(468, 163, 'SE REALIZA LA ASISTENCIA AL PERSONAL ENCARGADO DEL MODULO DE GESTION ADMINISTRATIVA.', '2025-11-13 23:52:16', '2025-11-13 23:52:16'),
(469, 164, 'SE REVISA EL DOCUMENTO DE REFERENCIA GENERADO EN LA ATENCION DE CONSULTA EXTERNA.', '2025-11-14 00:19:32', '2025-11-14 00:19:32'),
(470, 165, 'CAPACITACIÓN SOBRE EL USO DEL MÓDULO DE GESTION ADMINISTRATIVA SIHCE', '2025-11-15 16:04:57', '2025-11-15 16:04:57'),
(472, 166, 'CAPACITACION Y ASISTENCIA TECNICA DE MODULO GESTION ADMINISTRATIVA', '2025-11-18 17:20:21', '2025-11-18 17:20:21'),
(475, 168, 'ASISTENCIA TECNICA EN TRIAJE', '2025-11-18 18:07:34', '2025-11-18 18:07:34'),
(476, 169, 'ASISTENCIA TECNICA AL MODULO DE GESTION ADMINISTRATIVA', '2025-11-18 18:11:59', '2025-11-18 18:11:59'),
(478, 170, 'ASISTENCIA TECNICA AL MODULO DE GESTION ADMINISTRATIVA', '2025-11-18 18:26:16', '2025-11-18 18:26:16'),
(479, 171, 'ASISTENCIA TECNICA AL MODULO DE GESTION ADMINISTRATIVA', '2025-11-18 18:33:06', '2025-11-18 18:33:06'),
(480, 172, 'CAPACITACION DE MODULO DE CONSULTA EXTERNA', '2025-11-18 18:41:03', '2025-11-18 18:41:03'),
(481, 173, 'ASISTENCIA TECNICA EN FIRMA DIGITAL A DRA ROSARIO MENDOZA', '2025-11-18 18:46:47', '2025-11-18 18:46:47'),
(484, 175, 'SE DA ASISTENCIA TECNICA SOBRE EL MODULO DE CONSULTA EXTERNA, CON FIRMA DIGITAL', '2025-11-18 23:31:47', '2025-11-18 23:31:47'),
(485, 176, 'SE CAPACITA AL PERSONAL EN LA FIRMA DIGITAL Y USO DEL SISTEMA', '2025-11-20 16:20:47', '2025-11-20 16:20:47'),
(486, 177, 'SE CAPACITA AL PERSONAL EN EL MODULO DE CONSULTA EXTERNA MEDICINA', '2025-11-20 22:38:53', '2025-11-20 22:38:53'),
(487, 178, 'SE INSTRUYE AL PERSONAL EN CAMBIO DE TURNO, CON CREACION DE HORARIO EXCEPCIONAL', '2025-11-21 20:36:00', '2025-11-21 20:36:00'),
(489, 179, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE GESTION, PARA CAMBIOS DE TURNO', '2025-11-21 21:06:59', '2025-11-21 21:06:59'),
(490, 180, 'CAPACITACION DE APLICATIVO TUASUSALUD', '2025-11-25 02:22:43', '2025-11-25 02:22:43'),
(491, 180, 'CARGA DE HORARIO DEL MES DE DICIEMBRE EN TUASUSALUD', '2025-11-25 02:22:43', '2025-11-25 02:22:43'),
(492, 181, 'SE AGREGA A PACIENTE EXTRANJERO AL MODULO DE CITAS, SINCRONIZANDO EXITOSAMENTE SU SIS TEMPORAL.', '2025-11-25 16:44:23', '2025-11-25 16:44:23'),
(493, 182, 'APOYO CON LA FIRMA DIGITAL DEL MEDICO', '2025-11-26 19:08:54', '2025-11-26 19:08:54'),
(494, 182, 'APOYO PARA CAMBIAR DE PIN DEL DNIe', '2025-11-26 19:08:54', '2025-11-26 19:08:54'),
(496, 167, 'ASISTENCIA TECNICA AL MODULO DE GESTION ADMINISTRATIVA', '2025-11-26 21:30:39', '2025-11-26 21:30:39'),
(498, 174, 'SE SINCRONIZO FARMACIA', '2025-11-26 21:44:21', '2025-11-26 21:44:21'),
(499, 183, 'SE DA ASISTENCIA TECNICA SOBRE EL MODULO DE CONSULTA EXTERNA, CON FIRMA DIGITAL', '2025-11-27 14:31:26', '2025-11-27 14:31:26'),
(500, 184, 'SE CAPACITA AL PROFESIONAL EN LA FIRMA DIGITAL DE ATENCIONES SIHCE', '2025-11-27 15:56:26', '2025-11-27 15:56:26'),
(502, 185, 'SE CAPACITA A PERSONAL EN EL MODULO DE SALUD BUCAL PARA QUE PUEDA INICIAR EL USO DEL SIHCE.', '2025-11-27 19:59:28', '2025-11-27 19:59:28'),
(503, 107, 'CAPACITACIÓN SOBRE EL USO DEL MÓDULO DE CONSULTA EXTERNA-MEDICINA SIHCE', '2025-12-01 19:23:32', '2025-12-01 19:23:32'),
(504, 186, 'SE DA ASISTENCIA TECNICA SOBRE EL MODULO DE SALUD BUCAL', '2025-12-01 23:50:40', '2025-12-01 23:50:40'),
(505, 187, 'SE INSTRUYO EN EL TEMA ESTADISTICO DEL ESTABLECIMIENTO, REVISANDO LOS REGISTROS EN LA UPS 302303', '2025-12-02 15:30:01', '2025-12-02 15:30:01'),
(506, 188, 'SE DA ASISTENCIA TECNICA SOBRE INGRESO DE NUEVO PERSONAL AL MODULO DE GESTION ADMINISTRATIVO', '2025-12-04 04:04:17', '2025-12-04 04:04:17'),
(507, 189, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE CITAS', '2025-12-05 13:45:31', '2025-12-05 13:45:31'),
(508, 190, 'SE CAPACITA AL PERSONAL, EN EL MODULO DE TRIAJE', '2025-12-05 13:49:25', '2025-12-05 13:49:25'),
(513, 191, 'SE CAPACITA AL MEDICO EN EL USO DE LLENADO DE PROCEDIMIENTOS, CONSEJERIAS Y ORDENES DE LABORATORIO.', '2025-12-11 17:43:51', '2025-12-11 17:43:51'),
(515, 192, 'SE CAPACITA AL MEDICO EN EL USO DE LLENADO DE PROCEDIMIENTOS, CONSEJERIAS Y ORDENES DE LABORATORIO.', '2025-12-11 18:51:14', '2025-12-11 18:51:14'),
(516, 193, 'APOYO CON LA CREACION DE HISTORIA CLINICA DE PACIENTE EXTRANJERO MENOR DE EDAD', '2025-12-15 18:02:52', '2025-12-15 18:02:52'),
(517, 193, 'CORRECCION DE ARCHIVO CLINICO EN UN PACIENTE EXTRANJERO MENOR DE EDAD', '2025-12-15 18:02:52', '2025-12-15 18:02:52'),
(519, 194, 'SE REALIZA LA ASISTENCIA AL PERSONAL MEDICO EN LA SECCION DE RECETAS.', '2025-12-20 02:02:17', '2025-12-20 02:02:17'),
(523, 196, 'SE CAPACITA AL MEDICO EN EL USO DE LLENADO DE PROCEDIMIENTOS, CONSEJERIAS Y ORDENES DE LABORATORIO.', '2025-12-21 13:40:52', '2025-12-21 13:40:52'),
(524, 195, 'SE REALIZA LA ASISTENCIA AL PERSONAL MEDICO EN LA SECCION DE RECETAS.', '2025-12-21 13:43:59', '2025-12-21 13:43:59'),
(525, 197, 'SE REALIZA LA ASISTENCIA AL PERSONAL ENCARGADO DEL MODULO DE GESTION ADMINISTRATIVA.', '2025-12-21 13:44:49', '2025-12-21 13:44:49'),
(531, 198, 'SE CAPACITA AL MEDICO EN EL USO DE LLENADO DE PROCEDIMIENTOS, CONSEJERIAS Y ORDENES DE LABORATORIO.', '2025-12-22 22:35:56', '2025-12-22 22:35:56'),
(542, 199, 'SE CAPACITA AL MEDICO EN EL USO DE LLENADO DE PROCEDIMIENTOS, CONSEJERIAS Y ORDENES DE LABORATORIO.', '2025-12-26 12:55:47', '2025-12-26 12:55:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acuerdos`
--

CREATE TABLE `acuerdos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `acta_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `acuerdos`
--

INSERT INTO `acuerdos` (`id`, `acta_id`, `descripcion`, `created_at`, `updated_at`) VALUES
(88, 11, 'SE COMPROMETE A UTILIZAR EL MODULO DE FORMA INMEDIATA', '2025-09-30 22:22:53', '2025-09-30 22:22:53'),
(90, 2, 'PERSONAL SE COMPROMETE A SEGUI USANDO EL MODULO CORRESPONDIENTE', '2025-10-01 02:34:59', '2025-10-01 02:34:59'),
(91, 3, 'PERSONAL SE COMPROMETE A SEGUI USANDO EL MODULO CORRESPONDIENTE', '2025-10-01 02:36:28', '2025-10-01 02:36:28'),
(92, 5, 'PERSONAL SE COMPROMETE A SEGUI USANDO EL MODULO CORRESPONDIENTE', '2025-10-01 02:38:22', '2025-10-01 02:38:22'),
(93, 6, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-01 02:40:04', '2025-10-01 02:40:04'),
(99, 14, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-01 17:14:48', '2025-10-01 17:14:48'),
(103, 15, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-01 17:33:15', '2025-10-01 17:33:15'),
(104, 16, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-01 17:33:28', '2025-10-01 17:33:28'),
(106, 17, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-01 17:37:52', '2025-10-01 17:37:52'),
(107, 18, '', '2025-10-01 22:32:17', '2025-10-01 22:32:17'),
(108, 19, '', '2025-10-01 22:38:02', '2025-10-01 22:38:02'),
(110, 21, '', '2025-10-01 23:02:39', '2025-10-01 23:02:39'),
(111, 22, '', '2025-10-01 23:16:13', '2025-10-01 23:16:13'),
(113, 24, '', '2025-10-02 00:07:57', '2025-10-02 00:07:57'),
(116, 20, '', '2025-10-02 00:19:30', '2025-10-02 00:19:30'),
(119, 26, '', '2025-10-02 00:38:44', '2025-10-02 00:38:44'),
(124, 28, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-02 04:28:06', '2025-10-02 04:28:06'),
(125, 29, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-02 04:40:34', '2025-10-02 04:40:34'),
(126, 30, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-02 04:45:39', '2025-10-02 04:45:39'),
(127, 31, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN CONSULTA EXTERNA - MEDICINA', '2025-10-02 05:12:24', '2025-10-02 05:12:24'),
(128, 33, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN MODULOS SIHCE', '2025-10-02 05:26:43', '2025-10-02 05:26:43'),
(130, 35, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-02 12:09:20', '2025-10-02 12:09:20'),
(132, 37, '', '2025-10-02 12:47:21', '2025-10-02 12:47:21'),
(137, 39, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE.', '2025-10-02 13:44:15', '2025-10-02 13:44:15'),
(139, 9, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-10-02 14:42:16', '2025-10-02 14:42:16'),
(140, 9, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA  EN DARLE SEGUIMIENTO Y REPORTAR CUALQUIER INCIDENCIA.', '2025-10-02 14:42:16', '2025-10-02 14:42:16'),
(142, 10, 'EL PERSONAL ASISTIDO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-10-02 14:54:24', '2025-10-02 14:54:24'),
(143, 10, 'EL PERSONAL ASISTIDO SE COMPROMETE A REPORTAR CUALQUIER CASO DE INCIDENCIA.', '2025-10-02 14:54:24', '2025-10-02 14:54:24'),
(148, 43, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-02 15:58:41', '2025-10-02 15:58:41'),
(149, 44, 'EL PERSONAL ASISTIDO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-10-02 16:15:22', '2025-10-02 16:15:22'),
(150, 44, 'EL PERSONAL ASISTIDO SE COMPROMETE A REPORTAR CUALQUIER CASO DE INCIDENCIA.', '2025-10-02 16:15:22', '2025-10-02 16:15:22'),
(152, 46, '', '2025-10-02 17:48:02', '2025-10-02 17:48:02'),
(153, 42, 'EL PERSONAL ASISTIDO ACUERDA SEGUIR UTILIZANDO EL MODULO CORRESPONDIENTE.', '2025-10-02 18:02:44', '2025-10-02 18:02:44'),
(154, 42, 'EL PERSONAL ASISTIDO SE COMPROMETE A REPORTAR CUALQUIER CASO DE INCIDENCIA.', '2025-10-02 18:02:44', '2025-10-02 18:02:44'),
(156, 23, '', '2025-10-02 18:48:33', '2025-10-02 18:48:33'),
(157, 36, '', '2025-10-02 19:01:39', '2025-10-02 19:01:39'),
(158, 25, '', '2025-10-02 19:41:18', '2025-10-02 19:41:18'),
(159, 47, '', '2025-10-02 20:20:50', '2025-10-02 20:20:50'),
(162, 48, '', '2025-10-02 20:47:38', '2025-10-02 20:47:38'),
(165, 40, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN TRIAJE Y CONSULTA EXTERNA - MEDICINA', '2025-10-02 21:22:51', '2025-10-02 21:22:51'),
(166, 49, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE.', '2025-10-02 21:24:33', '2025-10-02 21:24:33'),
(167, 34, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN CONSULTA EXTERNA - MEDICINA', '2025-10-02 21:26:30', '2025-10-02 21:26:30'),
(168, 32, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-02 21:31:47', '2025-10-02 21:31:47'),
(169, 41, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-02 21:35:04', '2025-10-02 21:35:04'),
(171, 50, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-03 00:10:30', '2025-10-03 00:10:30'),
(184, 51, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-06 02:21:47', '2025-10-06 02:21:47'),
(187, 54, '', '2025-10-06 03:58:31', '2025-10-06 03:58:31'),
(188, 55, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-06 04:11:43', '2025-10-06 04:11:43'),
(189, 56, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-10-06 16:05:30', '2025-10-06 16:05:30'),
(190, 56, 'EL PERSONAL ASISTIDO SE COMPROMETE A REPORTAR CUALQUIER CASO DE INCIDENCIA.', '2025-10-06 16:05:30', '2025-10-06 16:05:30'),
(191, 57, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE.', '2025-10-06 17:55:54', '2025-10-06 17:55:54'),
(208, 62, 'SE CONTO CON APOYO ENLA ASISTENCIA TECNICA DE ING SELENE PINEDA', '2025-10-07 17:07:45', '2025-10-07 17:07:45'),
(209, 63, '', '2025-10-07 17:16:12', '2025-10-07 17:16:12'),
(210, 64, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE.', '2025-10-09 18:09:03', '2025-10-09 18:09:03'),
(212, 66, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-09 20:18:44', '2025-10-09 20:18:44'),
(213, 67, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-09 20:19:35', '2025-10-09 20:19:35'),
(223, 72, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-09 21:28:46', '2025-10-09 21:28:46'),
(224, 73, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-09 21:30:58', '2025-10-09 21:30:58'),
(231, 61, 'EL PERSONAL ASISTIDO SE COMPROMETE A REPORTAR EL PROBLEMA ENCONTRADO AL PROVEEDOR DE INTERNET CON EL ACCESO A LAS PAGINAS DE RENIEC.', '2025-10-10 16:05:59', '2025-10-10 16:05:59'),
(241, 82, 'SE COMPROMETE A SEGUIR USANDO EL MODULO DE CONSULTA EXTERNA MEDICINA DEL SIHCE', '2025-10-11 01:45:08', '2025-10-11 01:45:08'),
(242, 75, 'SE REPORTARÁ LA INCIDENCIA A SOPORTE DE APLICATIVOS PARA LA SOLUCIÓN', '2025-10-11 01:55:18', '2025-10-11 01:55:18'),
(244, 65, 'EL ERROR QUE SE MUESTRA SERÁ REPORTADO A SOPORTE DE APLICATIVOS DEL MINSA', '2025-10-11 01:57:33', '2025-10-11 01:57:33'),
(245, 45, '', '2025-10-11 01:57:57', '2025-10-11 01:57:57'),
(246, 38, '', '2025-10-11 01:58:20', '2025-10-11 01:58:20'),
(247, 27, 'SE ACUERDA REVISAR NUEVAMENTE EL REPORTE DE CRED EL 02/10/25 PARA CONFIRMAR LA MIGRACIÓN DE LA ATENCIÓN', '2025-10-11 01:59:00', '2025-10-11 01:59:00'),
(248, 13, '', '2025-10-11 01:59:36', '2025-10-11 01:59:36'),
(249, 12, '', '2025-10-11 01:59:57', '2025-10-11 01:59:57'),
(250, 8, 'SE COMPROMETE A UTILIZAR EL MODULO DE FORMA INMEDIATA', '2025-10-11 02:00:29', '2025-10-11 02:00:29'),
(251, 7, '', '2025-10-11 02:00:50', '2025-10-11 02:00:50'),
(252, 4, '', '2025-10-11 02:01:12', '2025-10-11 02:01:12'),
(253, 1, 'PERSONAL SE COMPROMETE A SEGUI USANDO EL MODULO CORRESPONDIENTE', '2025-10-11 02:01:37', '2025-10-11 02:01:37'),
(256, 84, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-11 15:33:44', '2025-10-11 15:33:44'),
(257, 83, 'LA MEDICO SE COMPROMETE A USAR EL SIHCE PARA EL REGISTRO DE SUS ATENCIONES', '2025-10-11 15:59:23', '2025-10-11 15:59:23'),
(258, 85, 'UTILIZAR EL MODULO DE REFERENCIAS Y CONTRARREFERENCIAS PARA LABORATORIO', '2025-10-11 17:46:52', '2025-10-11 17:46:52'),
(259, 86, '', '2025-10-11 18:31:24', '2025-10-11 18:31:24'),
(261, 88, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-11 19:20:06', '2025-10-11 19:20:06'),
(262, 87, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-11 19:20:32', '2025-10-11 19:20:32'),
(265, 53, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 00:22:09', '2025-10-12 00:22:09'),
(267, 58, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 00:34:19', '2025-10-12 00:34:19'),
(268, 52, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 00:46:30', '2025-10-12 00:46:30'),
(269, 59, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 00:57:23', '2025-10-12 00:57:23'),
(270, 68, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 01:20:34', '2025-10-12 01:20:34'),
(273, 69, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 01:57:26', '2025-10-12 01:57:26'),
(275, 71, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 02:59:15', '2025-10-12 02:59:15'),
(276, 76, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 03:32:20', '2025-10-12 03:32:20'),
(278, 70, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 04:00:39', '2025-10-12 04:00:39'),
(279, 77, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN CONSULTA EXTERNA - MEDICINA', '2025-10-12 04:04:14', '2025-10-12 04:04:14'),
(281, 78, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 04:30:42', '2025-10-12 04:30:42'),
(283, 79, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 04:54:58', '2025-10-12 04:54:58'),
(284, 80, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 05:07:53', '2025-10-12 05:07:53'),
(285, 81, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-12 05:27:06', '2025-10-12 05:27:06'),
(286, 89, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 02:48:47', '2025-10-13 02:48:47'),
(287, 90, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 02:50:59', '2025-10-13 02:50:59'),
(288, 91, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 02:53:19', '2025-10-13 02:53:19'),
(289, 92, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 02:55:58', '2025-10-13 02:55:58'),
(295, 93, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:02:49', '2025-10-13 03:02:49'),
(296, 94, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:06:41', '2025-10-13 03:06:41'),
(297, 95, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:09:21', '2025-10-13 03:09:21'),
(298, 96, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:12:43', '2025-10-13 03:12:43'),
(299, 97, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:14:31', '2025-10-13 03:14:31'),
(300, 98, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:16:01', '2025-10-13 03:16:01'),
(301, 99, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:21:02', '2025-10-13 03:21:02'),
(302, 100, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:22:31', '2025-10-13 03:22:31'),
(303, 101, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:24:48', '2025-10-13 03:24:48'),
(304, 102, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 03:26:00', '2025-10-13 03:26:00'),
(305, 103, '', '2025-10-13 16:55:11', '2025-10-13 16:55:11'),
(306, 104, 'PERSONAL SE COMPROMETE A USAR EL SISTEMA SIIHCE', '2025-10-13 20:29:03', '2025-10-13 20:29:03'),
(307, 105, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-13 22:47:05', '2025-10-13 22:47:05'),
(308, 74, 'SE COMPROMETEN A UTILIZAR EL SIHCE DE FORMA REGULAR PARA REGISTRAR LAS ATENCIONES DEL MÉDICO', '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(309, 74, 'SE COMPROMENTEN A REALIZAR LA FIRMA DIGITAL DE LAS ATENCIONES REALIZADAS EN EL SIHCE', '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(311, 106, 'LA MEDICO SE COMPROMETE A USAR EL SIHCE PARA EL REGISTRO DE SUS ATENCIONES', '2025-10-14 00:39:54', '2025-10-14 00:39:54'),
(324, 113, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-14 20:38:36', '2025-10-14 20:38:36'),
(327, 116, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-14 23:41:37', '2025-10-14 23:41:37'),
(330, 109, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-15 01:23:15', '2025-10-15 01:23:15'),
(331, 110, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-15 01:50:56', '2025-10-15 01:50:56'),
(332, 111, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-15 02:13:08', '2025-10-15 02:13:08'),
(333, 112, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-15 02:20:29', '2025-10-15 02:20:29'),
(335, 117, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-15 14:43:52', '2025-10-15 14:43:52'),
(337, 119, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-15 16:27:04', '2025-10-15 16:27:04'),
(338, 120, 'MEDICO INDICA QUE SI EL INTERNET NO LE FALLA, REALIZARA EL REGISTRO DE INFORMACION DE PACIENTES.', '2025-10-15 16:53:24', '2025-10-15 16:53:24'),
(339, 121, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-15 17:20:30', '2025-10-15 17:20:30'),
(340, 122, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-15 20:14:02', '2025-10-15 20:14:02'),
(341, 123, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-15 20:46:23', '2025-10-15 20:46:23'),
(342, 124, '', '2025-10-16 00:42:14', '2025-10-16 00:42:14'),
(343, 125, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-16 00:53:44', '2025-10-16 00:53:44'),
(344, 108, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-16 03:07:18', '2025-10-16 03:07:18'),
(345, 114, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-16 03:12:19', '2025-10-16 03:12:19'),
(346, 115, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-16 03:15:22', '2025-10-16 03:15:22'),
(356, 134, 'CONTINUIDAD EN EL LLENADO DE LAS ATENCIONES DE PACIENTES EN EL SIHCE', '2025-10-17 15:47:36', '2025-10-17 15:47:36'),
(357, 135, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-17 15:58:38', '2025-10-17 15:58:38'),
(358, 136, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-17 15:59:40', '2025-10-17 15:59:40'),
(359, 126, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN MODULOS SIHCE', '2025-10-17 16:56:16', '2025-10-17 16:56:16'),
(360, 127, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN TRIAJE', '2025-10-17 16:56:44', '2025-10-17 16:56:44'),
(361, 128, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN CONSULTA EXTERNA - MEDICINA', '2025-10-17 16:57:41', '2025-10-17 16:57:41'),
(362, 129, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN MODULOS SIHCE', '2025-10-17 16:59:54', '2025-10-17 16:59:54'),
(365, 137, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-20 00:25:47', '2025-10-20 00:25:47'),
(371, 130, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN MODULO', '2025-10-20 01:41:47', '2025-10-20 01:41:47'),
(373, 131, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN CITAS', '2025-10-20 01:56:14', '2025-10-20 01:56:14'),
(374, 132, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN TRIAJE', '2025-10-20 04:01:02', '2025-10-20 04:01:02'),
(376, 133, 'CONTINUIDAD EN REGISTRO DE INFORMACION EN MODULO', '2025-10-20 04:48:50', '2025-10-20 04:48:50'),
(377, 138, '', '2025-10-21 14:26:59', '2025-10-21 14:26:59'),
(386, 60, 'EL PERSONAL ASISTIDO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-10-22 17:19:23', '2025-10-22 17:19:23'),
(387, 141, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-10-22 22:09:19', '2025-10-22 22:09:19'),
(388, 142, 'EL PERSONAL ASISTIDO ACUERDA SEGUIR UTILIZANDO EL MODULO CORRESPONDIENTE.', '2025-10-22 22:21:54', '2025-10-22 22:21:54'),
(390, 144, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-10-22 22:35:55', '2025-10-22 22:35:55'),
(392, 146, 'EL PERSONAL ASISTIDO ACUERDA REPORTAR CUALQUIER INCONVENIENTE CON EL SIHCE', '2025-10-22 22:45:55', '2025-10-22 22:45:55'),
(393, 139, 'LA MEDICO SE COMPROMETE A REGISTRAR ATENCIONES PROGRESIVAMENTE PARA ADAPTARSE AL USO DEL MÓDULO DE MEDICINA', '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(394, 139, 'SE COMPROMETEN A SUBIR SUS ROLES DE LOS SIGUIENTES MESES AL SISTEMA', '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(397, 148, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-23 16:51:15', '2025-10-23 16:51:15'),
(398, 149, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-23 16:54:43', '2025-10-23 16:54:43'),
(399, 140, 'EL MEDICO SE COMPROMETE A USAR EL SIHCE PARA EL REGISTRO DE SUS ATENCIONES', '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(400, 140, 'EL RESPONSABLE DE GESTIÓN SE COMPROMETE A SUBIR LA PROGRAMACIÓN DE LOS 3 PRIMEROS MESES DEL AÑO 2026', '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(402, 151, '', '2025-10-24 21:44:22', '2025-10-24 21:44:22'),
(403, 150, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-10-24 21:51:04', '2025-10-24 21:51:04'),
(405, 153, 'LOS PROFESIONALES SE COMPROMETEN A USAR EL SIHCE PARA EL REGISTRO', '2025-10-27 21:33:47', '2025-10-27 21:33:47'),
(406, 152, 'LOS PROFESIONALES SE COMPROMETEN A USAR EL SIHCE PARA EL REGISTRO', '2025-10-27 21:34:38', '2025-10-27 21:34:38'),
(407, 154, 'EL MEDICO SE COMPROMETE A USAR EL SIHCE PARA EL REGISTRO DE SUS ATENCIONES', '2025-10-27 21:39:13', '2025-10-27 21:39:13'),
(408, 155, 'EL PERSONAL ASISTIDO ACUERDA SUBIR LOS ROLES DE LOS MESES ENERO, FEBRERO Y MARZO ANTES DEL 10 DE NOVIEMBRE.', '2025-10-28 16:30:31', '2025-10-28 16:30:31'),
(409, 143, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-10-28 16:45:27', '2025-10-28 16:45:27'),
(411, 145, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-10-28 16:50:00', '2025-10-28 16:50:00'),
(413, 156, 'EL PERSONAL ASISTIDO ACUERDA SUBIR LOS ROLES DE LOS MESES ENERO, FEBRERO Y MARZO ANTES DEL 10 DE NOVIEMBRE.', '2025-10-28 23:13:23', '2025-10-28 23:13:23'),
(415, 157, 'EL PERSONAL ASISTIDO ACUERDA REPORTAR CUALQUIER INCONVENIENTE CON EL SIHCE', '2025-10-29 00:23:38', '2025-10-29 00:23:38'),
(418, 158, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-29 20:51:29', '2025-10-29 20:51:29'),
(420, 159, 'CONTINUIDAD EN EL USO DEL SIHCE EN TODOS LOS MODULOS CAPACITADOS', '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(421, 147, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-29 21:06:31', '2025-10-29 21:06:31'),
(423, 118, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-30 02:09:47', '2025-10-30 02:09:47'),
(426, 160, 'REGISTRO DE INFORMACION PERMANENTE', '2025-10-30 15:54:06', '2025-10-30 15:54:06'),
(428, 161, '', '2025-11-05 15:39:58', '2025-11-05 15:39:58'),
(429, 162, 'EL PERSONAL ASISTIDO ACUERDA SUBIR LOS ROLES DE LOS MESES ENERO, FEBRERO Y MARZO ANTES DEL 10 DE NOVIEMBRE.', '2025-11-13 23:12:38', '2025-11-13 23:12:38'),
(430, 163, 'EL PERSONAL ASISTIDO ACUERDA SUBIR LOS ROLES DE LOS MESES ENERO, FEBRERO Y MARZO ANTES DEL 10 DE NOVIEMBRE.', '2025-11-13 23:52:16', '2025-11-13 23:52:16'),
(431, 164, 'EL PERSONAL ASISTIDO ACUERDA REPORTAR CUALQUIER INCONVENIENTE CON EL SIHCE', '2025-11-14 00:19:32', '2025-11-14 00:19:32'),
(432, 165, '', '2025-11-15 16:04:57', '2025-11-15 16:04:57'),
(434, 166, 'REGISTRO DE INFORMACION PERMANENTE', '2025-11-18 17:20:21', '2025-11-18 17:20:21'),
(437, 168, 'REGISTRO DE INFORMACION PERMANENTE', '2025-11-18 18:07:34', '2025-11-18 18:07:34'),
(438, 169, 'REGISTRO DE INFORMACION PERMANENTE', '2025-11-18 18:11:59', '2025-11-18 18:11:59'),
(440, 170, 'REGISTRO DE INFORMACION PERMANENTE', '2025-11-18 18:26:16', '2025-11-18 18:26:16'),
(441, 171, 'REGISTRO DE INFORMACION PERMANENTE', '2025-11-18 18:33:06', '2025-11-18 18:33:06'),
(442, 172, 'REGISTRO DE INFORMACION PERMANENTE', '2025-11-18 18:41:03', '2025-11-18 18:41:03'),
(443, 173, '', '2025-11-18 18:46:47', '2025-11-18 18:46:47'),
(445, 175, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-11-18 23:31:47', '2025-11-18 23:31:47'),
(446, 176, 'EL PERSONAL SE COMPROMETE A UTILIZAR EL MODULO CORRESPONDIENTES', '2025-11-20 16:20:47', '2025-11-20 16:20:47'),
(447, 177, 'PERSONAL SE COMPROMETE A USAR EL SISTEMA SIHCE', '2025-11-20 22:38:53', '2025-11-20 22:38:53'),
(448, 178, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE.', '2025-11-21 20:36:00', '2025-11-21 20:36:00'),
(450, 179, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE.', '2025-11-21 21:06:59', '2025-11-21 21:06:59'),
(451, 180, '', '2025-11-25 02:22:43', '2025-11-25 02:22:43'),
(452, 181, 'EL PERSONAL SE COMPROMETE A UTILIZAR LOS MODULOS CORRESPONDIENTES', '2025-11-25 16:44:23', '2025-11-25 16:44:23'),
(453, 182, '', '2025-11-26 19:08:54', '2025-11-26 19:08:54'),
(455, 167, 'REGISTRO DE INFORMACION PERMANENTE', '2025-11-26 21:30:39', '2025-11-26 21:30:39'),
(457, 174, 'REGISTRO DE INFORMACION PERMANENTE', '2025-11-26 21:44:21', '2025-11-26 21:44:21'),
(458, 183, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-11-27 14:31:26', '2025-11-27 14:31:26'),
(459, 184, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE.', '2025-11-27 15:56:26', '2025-11-27 15:56:26'),
(461, 185, 'EL PERSONAL SE COMPROMETE A UTILIZAR EL MODULO CORRESPONDIENTE Y REPORTAR CUALQUIER INCIDENCIA.', '2025-11-27 19:59:28', '2025-11-27 19:59:28'),
(462, 107, 'EL MEDICO SE COMPROMETE A USAR EL SIHCE PARA EL REGISTRO DE SUS ATENCIONES', '2025-12-01 19:23:32', '2025-12-01 19:23:32'),
(463, 186, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-12-01 23:50:40', '2025-12-01 23:50:40'),
(464, 187, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE.', '2025-12-02 15:30:01', '2025-12-02 15:30:01'),
(465, 188, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-12-04 04:04:17', '2025-12-04 04:04:17'),
(466, 189, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-12-05 13:45:31', '2025-12-05 13:45:31'),
(467, 190, 'EL PERSONAL SE COMPROMETE A SEGUIR USANDO EL SISTEMA SIHCE', '2025-12-05 13:49:25', '2025-12-05 13:49:25'),
(472, 191, 'EL MEDICO SE COMPROMETE A LLENAR LOS PROCEDIMIENTOS Y EXAMENES DE LABORATORIO EN EL SIHCE.', '2025-12-11 17:43:51', '2025-12-11 17:43:51'),
(474, 192, 'EL MEDICO SE COMPROMETE A LLENAR LOS PROCEDIMIENTOS Y EXAMENES DE LABORATORIO EN EL SIHCE.', '2025-12-11 18:51:14', '2025-12-11 18:51:14'),
(475, 193, '', '2025-12-15 18:02:52', '2025-12-15 18:02:52'),
(477, 194, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-12-20 02:02:17', '2025-12-20 02:02:17'),
(481, 196, 'EL PERSONAL SE COMPROMETE A UTILIZAR LOS MODULOS CORRESPONDIENTES', '2025-12-21 13:40:52', '2025-12-21 13:40:52'),
(482, 195, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-12-21 13:43:59', '2025-12-21 13:43:59'),
(483, 197, 'EL PERSONAL ASISTIDO Y CAPACITADO ACUERDA SEGUIR UTILIZANDO LOS MODULOS DEL SIHCE CORRESPONDIENTES.', '2025-12-21 13:44:49', '2025-12-21 13:44:49'),
(489, 198, 'EL PERSONAL SE COMPROMETE A UTILIZAR LOS MODULOS CORRESPONDIENTES', '2025-12-22 22:35:56', '2025-12-22 22:35:56'),
(500, 199, 'EL PERSONAL SE COMPROMETE A UTILIZAR LOS MODULOS CORRESPONDIENTES', '2025-12-26 12:55:47', '2025-12-26 12:55:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `establecimientos`
--

CREATE TABLE `establecimientos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `provincia` varchar(255) NOT NULL,
  `distrito` varchar(255) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `red` varchar(255) NOT NULL,
  `microred` varchar(255) NOT NULL,
  `responsable` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `establecimientos`
--

INSERT INTO `establecimientos` (`id`, `codigo`, `nombre`, `provincia`, `distrito`, `categoria`, `red`, `microred`, `responsable`, `created_at`, `updated_at`) VALUES
(1, '3372', 'PACHACUTEC', 'ICA', 'PACHACUTEC', 'I-3', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'ROGER VIDAL GALA ESCOBAR ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(2, '3431', 'TOPARA', 'CHINCHA', 'GROCIO PRADO', 'I-2', 'CHINCHA - PISCO', 'CHINCHA', 'IVAN GIUSEPPE ESPINOZA ALIAGA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, '3389', 'PUESTO SALUD HUAMANI', 'ICA', 'SAN JOSE DE LOS MOLINOS', 'I-2', 'ICA-PALPA-NAZCA', 'LA TINGUIÑA/PARCONA', 'FLOR DE MARIA UCULMANA CASTILLO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, '3451', 'SAN JAVIER', 'NAZCA', 'CHANGUILLO', 'I-1', 'ICA-PALPA-NAZCA', 'NASCA', 'SILMY KATHERINE PECEROS CARLOS ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, '3411', 'CALLANGO', 'ICA', 'OCUCAJE', 'I-1', 'ICA-PALPA-NAZCA', 'SANTIAGO', 'MIRIAM LUZ ROJAS VASQUEZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, '3402', 'PARIÑA GRANDE', 'ICA', 'PUEBLO NUEVO', 'I-2', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'CLAUDIA DANITZA ELIAS QUISPE ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, '25933', 'CSMC TUPAC AMARU', 'PISCO', 'TUPAC AMARU INCA', 'I-3', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'ERIKA MARITZA CARTAGENA ESCALAYA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, '3386', 'EL CARMEN-OLIVO', 'ICA', 'SAN JUAN BAUTISTA', 'I-3', 'ICA-PALPA-NAZCA', 'ICA', 'CYNTHIA YACORI CORREA PEREZ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(9, '3398', 'COCHARCAS', 'ICA', 'YAUCA DEL ROSARIO', 'I-1', 'ICA-PALPA-NAZCA', 'LA PALMA', 'FLOR DE MARIA PEREZ TORREALVA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(10, '7015', 'CRUZ BLANCA', 'CHINCHA', 'CHINCHA ALTA', 'I-2', 'CHINCHA - PISCO', 'CHINCHA', 'CESAR AUGUSTO CESPEDES GONZALES ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(11, '28653', 'CSMC COLOR ESPERANZA', 'ICA', 'SALAS', 'I-3', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'FERNANDO ALEJANDRO JHON RAFAEL HUERTAS BELLIDO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(12, '3377', 'EL HUARANGO', 'ICA', 'ICA', 'I-2', 'ICA-PALPA-NAZCA', 'LA PALMA', 'ESTHER LILIAN ESCATE VENTURA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(13, '3429', 'WIRACOCHA', 'CHINCHA', 'EL CARMEN', 'I-2', 'CHINCHA - PISCO', 'CHINCHA BAJA', 'JOHNNY SALVADOR MATOS MELGAR', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(14, '3399', 'SAN JOSE DE CURIS', 'ICA', 'YAUCA DEL ROSARIO', 'I-2', 'ICA-PALPA-NAZCA', 'LA PALMA', 'ZOILA LEVANO VIZARRETA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(15, '3446', 'MARCONA', 'NAZCA', 'MARCONA', 'I-3', 'ICA-PALPA-NAZCA', 'NASCA', 'CARLOS RAUL TABER RAMOS', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(16, '3453', 'TUPAC AMARU', 'NAZCA', 'MARCONA', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'VICTOR MANUEL NUÑEZ SANCHEZ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(17, '3492', 'LAGUNA GRANDE', 'PISCO', 'PARACAS', 'I-1', 'CHINCHA - PISCO', 'PISCO', 'MARISOL GUEVARA RIVERA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(18, '6845', 'LAS CAÑAS', 'NAZCA', 'NAZCA', 'I-1', 'ICA-PALPA-NAZCA', 'NASCA', 'NORA ANGELICA TINCO FLORES ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(19, '3493', 'CASALLA', 'PISCO', 'TUPAC AMARU INCA', 'I-3', 'CHINCHA - PISCO', 'TUPAC AMARU INCA', 'YADIRA ANNAIS UCEDA AGUILAR', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(20, '3434', 'LOS ALAMOS', 'CHINCHA', 'PUEBLO NUEVO', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'CINDY CRISTINA CHINCHAY ALMEIDA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(21, '3459', 'RIO GRANDE', 'PALPA', 'RIO GRANDE', 'I-3', 'ICA-PALPA-NAZCA', 'PALPA', 'ARQUIMEDES BENDEZU GAVILAN', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(22, '3385', 'SAN MARTIN DE PORRAS', 'ICA', 'ICA', 'I-3', 'ICA-PALPA-NAZCA', 'ICA', 'JULIO WILLIAM CRUCES LECAROS', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(23, '3360', 'SAN JOAQUIN', 'ICA', 'ICA', 'I-3', 'ICA-PALPA-NAZCA', 'LA PALMA', 'JUAN MARTIN MAYAUTE ARCE', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(24, '3415', 'ALTO LARAN', 'CHINCHA', 'ALTO LARAN', 'I-3', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'SANDY DANIEL CALDERA POLANCO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(25, '3479', 'SAN MIGUEL', 'PISCO', 'PISCO', 'I-2', 'CHINCHA - PISCO', 'TUPAC AMARU INCA', 'ADRIANA COLINA AVILA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(26, '3481', 'HUANCANO', 'PISCO', 'HUANCANO', 'I-2', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'QUINTINA NICOLAZA CHAPARRO QUISPE ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(27, '3401', 'SAN RAFAEL', 'ICA', 'PUEBLO NUEVO', 'I-1', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'MARITZA ROSARIO HERNANDEZ LENGUA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(28, '3460', 'PUEBLO NUEVO', 'PALPA', 'PALPA', 'I-1', 'ICA-PALPA-NAZCA', 'PALPA', 'ROSARIO DEL PILAR SANCHEZ MORON', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(29, '3448', 'SAN LUIS DE PAJONAL', 'NAZCA', 'NAZCA', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'CAROLA PILAR ORTIZ VILLAFUERTE', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(30, '3485', 'LOS PARACAS', 'PISCO', 'HUMAY', 'I-2', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'ANALIA GIOVANA COAQUIRA DIAZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(31, '3487', 'DOS PALMAS', 'PISCO', 'INDEPENDENCIA', 'I-2', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'ERIKA MARLENY MEDINA CACERES', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(32, '20900', 'SAN MIGUEL DE LA PASCANA', 'NAZCA', 'EL INGENIO', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'DERLY CATHERINE TUBILLAS ALLCA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(33, '3436', 'SAN JUAN DE YANAC', 'CHINCHA', 'SAN JUAN DE YANAC', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'EVA LAURENTE MENDEZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(34, '3435', 'EL SALVADOR', 'CHINCHA', 'PUEBLO NUEVO', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'JORGE LUIS GONZALEZ CABRERA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(35, '3366', 'LA TINGUIÑA', 'ICA', 'LA TINGUIÑA', 'I-3', 'ICA-PALPA-NAZCA', 'LA TINGUIÑA/PARCONA', 'JORGE RODOLFO CHACALTANA SUAREZ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(36, '3440', 'BELLAVISTA', 'CHINCHA', 'SAN PEDRO DE HUACARPANA', 'I-2', 'CHINCHA - PISCO', 'CHINCHA', 'MAYRA ESTELA AYBAR LOPEZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(37, '3403', 'CALLEJON LOS ESPINOS', 'ICA', 'PUEBLO NUEVO', 'I-1', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'POLA JAZMIN ORMEÑO ROMANI', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(38, '3428', 'HOJA REDONDA', 'CHINCHA', 'EL CARMEN', 'I-2', 'CHINCHA - PISCO', 'CHINCHA BAJA', 'CLOTILDE GUILLERMINA CAPCHA BALLON', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(39, '3387', 'CAMINO DE REYES', 'ICA', 'SAN JUAN BAUTISTA', 'I-2', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'GERSON CESAR RODRIGUEZ TENORIO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(40, '3408', 'LA VENTA', 'ICA', 'SANTIAGO', 'I-2', 'ICA-PALPA-NAZCA', 'SANTIAGO', 'ROSA DOMINGA QUICHCA GOMEZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(41, '3474', 'INDEPENDENCIA', 'PISCO', 'INDEPENDENCIA', 'I-3', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'PILAR GARAYAR CALLISAYA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(42, '3480', 'SAN MARTIN DE PORRES', 'PISCO', 'PISCO', 'I-2', 'CHINCHA - PISCO', 'PISCO', 'ENMA MARUJA DE LA CRUZ CARRASCO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(43, '3369', 'LOS AQUIJES', 'ICA', 'LOS AQUIJES', 'I-3', 'ICA-PALPA-NAZCA', 'LA PALMA', 'ERIKA PEREZ LUQUE', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(44, '3488', 'CABEZA TORO LATERAL 5', 'PISCO', 'INDEPENDENCIA', 'I-2', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'ESTHER NELLY RAMOS SANTIAGO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(45, '3375', 'SENOR DE LUREN', 'ICA', 'ICA', 'I-3', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'ROSA ANALI ASTOCAZA GALINDO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(46, '3469', 'SAN FRANCISCO', 'PALPA', 'SANTA CRUZ', 'I-2', 'ICA-PALPA-NAZCA', 'PALPA', 'GLADYS ALTEMIRA VARGAS URTECHO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(47, '3426', 'SANTA ROSA', 'CHINCHA', 'CHINCHA BAJA', 'I-2', 'CHINCHA - PISCO', 'CHINCHA BAJA', 'JUDY ELENA SILVA MORA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(48, '27197', 'CSMC DECÍDETE A SER FELIZ', 'NAZCA', 'VISTA ALEGRE', 'I-3', 'ICA-PALPA-NAZCA', 'NASCA', 'VERONICA LEONOR DONAYRE FLORES ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(49, '3390', 'SANTA BARBARA', 'ICA', 'LA TINGUIÑA', 'I-2', 'ICA-PALPA-NAZCA', 'LA TINGUIÑA/PARCONA', 'EDIS MILAGRITOS JHONG CASTRO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(50, '3463', 'SARAMARCA', 'PALPA', 'PALPA', 'I-2', 'ICA-PALPA-NAZCA', 'PALPA', 'JOSE LEONEL DORREGARAY AROSTIGUE', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(51, '3423', 'AYLLOQUE', 'CHINCHA', 'ALTO LARAN', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'JEANPIERRE RAMOS SOLARI ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(52, '16773', 'LA ESPERANZA', 'PISCO', 'SAN ANDRES', 'I-2', 'CHINCHA - PISCO', 'PISCO', 'MILAGROS ANTONIA MUÑANTE HERENCIA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(53, '3486', 'CABEZA TORO LATERAL 4', 'PISCO', 'INDEPENDENCIA', 'I-2', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'MATIAS ESTEBAN SAAVEDRA GOÑI ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(54, '3380', 'YANQUIZA', 'ICA', 'SUBTANJALLA', 'I-2', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'HUGO BERNARDO AYAUJA HUANCAHUARE ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(55, '3457', 'BUENA FE', 'NAZCA', 'NAZCA', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'JACINTO JULIO GUTIERREZ CORTEZ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(56, '3367', 'PARCONA', 'ICA', 'PARCONA', 'I-3', 'ICA-PALPA-NAZCA', 'LA TINGUIÑA/PARCONA', 'JESUS AYQUIPA SANTI', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(57, '3491', 'SANTA CRUZ', 'PISCO', 'PARACAS', 'I-3', 'CHINCHA - PISCO', 'PISCO', 'JUANA HAYDE INTIMAYTA SAYRITUPAC', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(58, '3384', 'PAMPA DE VILLACURI', 'ICA', 'SALAS', 'I-2', 'ICA-PALPA-NAZCA', 'NO PERTENECE A NINGUNA MICRORED', 'EDITH ISMELDA VELASQUEZ HUARCAYA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(59, '3395', 'PP.JJ. EL ROSARIO', 'ICA', 'LOS AQUIJES', 'I-2', 'ICA-PALPA-NAZCA', 'LA PALMA', 'MELISSA ARACELLI MATTA VARGAS ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(60, '3483', 'CUCHILLA VIEJA', 'PISCO', 'HUMAY', 'I-2', 'CHINCHA - PISCO', 'TUPAC AMARU INCA', 'KATHERIN DIANA OLIVEROS HUERTA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(61, '3467', 'EL PALMAR', 'PALPA', 'RIO GRANDE', 'I-1', 'ICA-PALPA-NAZCA', 'PALPA', 'ARASELY YOBANY VALENCIA HUAMAN ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(62, '3441', 'VISTA ALEGRE', 'CHINCHA', 'SAN PEDRO DE HUACARPANA', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'JORGE DIEGO GALVEZ FLORES ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(63, '34021', 'CSMC SANTISIMA VIRGEN DE YAUCA', 'ICA', 'TATE', 'I-3', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'JOSE ALONSO VERA TORRES ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(64, '3456', 'TARUGA', 'NAZCA', 'VISTA ALEGRE', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'MARILU MARISOL CONDORI CRISOSTOMO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(65, '3419', 'PUEBLO NUEVO', 'CHINCHA', 'PUEBLO NUEVO', 'I-4', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'JOSE CARLOS VALLE BRAVO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(66, '25977', 'CSMC VITALIZA', 'ICA', 'PARCONA', 'I-3', 'ICA-PALPA-NAZCA', 'LA TINGUIÑA/PARCONA', 'YENY MARISELA GARAMENDI PEREZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(67, '3391', 'CHANCHAJALLA', 'ICA', 'LA TINGUIÑA', 'I-2', 'ICA-PALPA-NAZCA', 'ICA', 'ANTONIO ZACARIAS URIBE LEVANO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(68, '3374', 'OCUCAJE', 'ICA', 'OCUCAJE', 'I-3', 'ICA-PALPA-NAZCA', 'SANTIAGO', 'ANTONIO LIZARDO LOPEZ TREJO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(69, '3424', 'HUACHINGA', 'CHINCHA', 'ALTO LARAN', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'LUCIA DEL PILAR TORNERO HUAMAN ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(70, '3437', 'PS HUAÑUPIZA', 'CHINCHA', 'SAN JUAN DE YANAC', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'ROSSE MARY VALDIVIA PAREDES ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(71, '3388', 'PAMPA DE LA ISLA', 'ICA', 'SAN JOSE DE LOS MOLINOS', 'I-2', 'ICA-PALPA-NAZCA', 'LA TINGUIÑA/PARCONA', 'GLADYS CONSUELO SORIANO CORDOVA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(72, '3407', 'EL PALTO', 'ICA', 'PACHACUTEC', 'I-2', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'GLORIA ALICIA TRILLO MAYURI ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(73, '3478', 'TUPAC AMARU', 'PISCO', 'TUPAC AMARU INCA', 'I-3', 'CHINCHA - PISCO', 'TUPAC AMARU INCA', 'BALVIN MARIO MEDINA SAAVEDRA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(74, '3364', 'SAN JUAN BAUTISTA', 'ICA', 'SAN JUAN BAUTISTA', 'I-3', 'ICA-PALPA-NAZCA', 'ICA', 'EDWIN SEGUNDO PAREDES MONTEJO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(75, '3371', 'TATE', 'ICA', 'TATE', 'I-3', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'FREDDY ALBERTO VILCA CHACALTANA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(76, '3410', 'AGUADA DE PALOS', 'ICA', 'SANTIAGO', 'I-1', 'ICA-PALPA-NAZCA', 'SANTIAGO', 'KARINA SELENE ASCENCIO CALDERON ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(77, '3449', 'CABILDO', 'NAZCA', 'CHANGUILLO', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'AMELIA LOPEZ ALVA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(78, '3464', 'LLIPATA', 'PALPA', 'LLIPATA', 'I-3', 'ICA-PALPA-NAZCA', 'PALPA', 'LUIS JOSE PAREDES MALDONADO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(79, '3378', 'LA ANGOSTURA', 'ICA', 'SUBTANJALLA', 'I-3', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'MARTINEZ ASCONA JOSELITO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(80, '3447', 'VISTA ALEGRE', 'NAZCA', 'VISTA ALEGRE', 'I-3', 'ICA-PALPA-NAZCA', 'NASCA', 'LUIS FORTUNATO PEREZ MURIANO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(81, '3490', 'SAN JOSE DE CONDOR', 'PISCO', 'INDEPENDENCIA', 'I-2', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'ALESSA MARIA SOLARI CORDOVA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(82, '3432', 'BALCONCITO', 'CHINCHA', 'GROCIO PRADO', 'I-2', 'CHINCHA - PISCO', 'CHINCHA', 'NELSON GEORGE HUAYTA MIÑAN', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(83, '3400', 'HUARANGAL', 'ICA', 'YAUCA DEL ROSARIO', 'I-2', 'ICA-PALPA-NAZCA', 'LA PALMA', 'MERCEDES HAYDEE CUPE LUNASCO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(84, '3466', 'PAMPA BLANCA', 'PALPA', 'RIO GRANDE', 'I-1', 'ICA-PALPA-NAZCA', 'PALPA', 'DELFINA MARITZA CARDENAS GOMEZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(85, '3417', 'EL CARMEN', 'CHINCHA', 'EL CARMEN', 'I-2', 'CHINCHA - PISCO', 'CHINCHA BAJA', 'ALEJANDRINA LEONOR CORDOVA CASALINO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(86, '3475', 'PARACAS', 'PISCO', 'PARACAS', 'I-2', 'CHINCHA - PISCO', 'PISCO', 'GILDA ÑAUPA CUBA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(87, '3482', 'PAMPANO', 'PISCO', 'HUANCANO', 'I-2', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'OMAR MEDINA CARDENAS', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(88, '3362', 'SUBTANJALLA', 'ICA', 'SUBTANJALLA', 'I-3', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'CARMEN ROSA FRACCHIA HUAMAN ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(89, '3465', 'LA ISLA', 'PALPA', 'RIO GRANDE', 'I-2', 'ICA-PALPA-NAZCA', 'PALPA', 'FLOR DE MARIA MARGARITA QUISPE CULI ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(90, '3383', 'CERRO PRIETO', 'ICA', 'SALAS', 'I-2', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'MARIELA ZEA JURADO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(91, '3393', 'EL ARENAL', 'ICA', 'LOS AQUIJES', 'I-2', 'ICA-PALPA-NAZCA', 'LA PALMA', 'ANTONIO GUMERCINDO CACERES CASADO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(92, '3394', 'PARIÑA CHICO', 'ICA', 'LOS AQUIJES', 'I-2', 'ICA-PALPA-NAZCA', 'LA PALMA', 'CARMEN ROSA HERNANDEZ ALVITES ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(93, '3433', 'SAN ISIDRO', 'CHINCHA', 'PUEBLO NUEVO', 'I-3', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'ROXANA MELCHORITA CASTILLA GUILLÉN', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(94, '17023', 'SAN JUAN DE DIOS', 'PISCO', 'PISCO', 'I-4', 'CHINCHA - PISCO', 'PISCO', 'AVELINO ADRIAN ALVA AQUIJE', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(95, '3412', 'CORDOVA', 'ICA', 'OCUCAJE', 'I-2', 'ICA-PALPA-NAZCA', 'SANTIAGO', 'JHEFFERSON ENRIQUE SULLUCHUCO LIMA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(96, '33478', 'CSMC CRISTO MORENO DE LUREN', 'ICA', 'ICA', 'I-3', 'ICA-PALPA-NAZCA', 'LA PALMA', 'LUIS ENRIQUE TENORIO AGUADO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(97, '3373', 'C.S SANTIAGO', 'ICA', 'SANTIAGO', 'I-3', 'ICA-PALPA-NAZCA', 'SANTIAGO', 'FELIPE JUAN CARLOS LOPEZ QUIJANDRIA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(98, '3404', 'LUJARAJA', 'ICA', 'TATE', 'I-2', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'CARLOS ALBERTO CALDERON MARTINEZ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(99, '3468', 'SANTA CRUZ', 'PALPA', 'SANTA CRUZ', 'I-1', 'ICA-PALPA-NAZCA', 'PALPA', 'TANIA ROCIO PACHECO CONTRERAS ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(100, '3444', 'CHANGUILLO', 'NAZCA', 'CHANGUILLO', 'I-3', 'ICA-PALPA-NAZCA', 'NASCA', 'SARITA HUACCAMAITA CONTRERAS', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(101, '3489', 'TOMA DE LEON', 'PISCO', 'INDEPENDENCIA', 'I-2', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'YASURY GURMENDI SALAZAR ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(102, '3473', 'HUMAY', 'PISCO', 'HUMAY', 'I-2', 'CHINCHA - PISCO', 'PISCO', 'CINTHYA SANDRA BAUTISTA RAMOS', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(103, '3365', 'LOS MOLINOS', 'ICA', 'SAN JOSE DE LOS MOLINOS', 'I-3', 'ICA-PALPA-NAZCA', 'LA TINGUIÑA/PARCONA', 'LIZ SANDRA CHOQUE ASTOCAZA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(104, '3406', 'LOS CALDERONES', 'ICA', 'TATE', 'I-1', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'GLORIA HERMELINDA CERVANTES MALPARTIDA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(105, '3476', 'SAN ANDRES', 'PISCO', 'SAN ANDRES', 'I-2', 'CHINCHA - PISCO', 'PISCO', 'HUGO RUBEN VELIZ YATACO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(106, '3397', 'PAMPAHUASI', 'ICA', 'YAUCA DEL ROSARIO', 'I-2', 'ICA-PALPA-NAZCA', 'NO PERTENECE A NINGUNA MICRORED', 'MARIA ELENA MARCOS HUAMANI ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(107, '3461', 'SAN IGNACIO', 'PALPA', 'PALPA', 'I-2', 'ICA-PALPA-NAZCA', 'PALPA', 'ELSA PASCUALA VENTURA BALDEON ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(108, '3484', 'BERNALES', 'PISCO', 'HUMAY', 'I-3', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'BETZABE JASMIN ORTIZ SALAZAR', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(109, '3413', 'PAMPA CHACALTANA', 'ICA', 'OCUCAJE', 'I-2', 'ICA-PALPA-NAZCA', 'SANTIAGO', 'JOSSEF JULIANY ECHEVARRIA GUTIERREZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(110, '27199', 'CSMC NUEVO HORIZONTE', 'CHINCHA', 'SUNAMPE', 'I-3', 'CHINCHA - PISCO', 'CHINCHA', 'MARCO ANTONIO JUNIOR ROJAS VALLE ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(111, '3430', 'SAN JOSE', 'CHINCHA', 'EL CARMEN', 'I-2', 'CHINCHA - PISCO', 'CHINCHA BAJA', 'ALEJANDRINA LEONOR CORDOVA CASALINO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(112, '3381', 'FONAVI IV', 'ICA', 'SUBTANJALLA', 'I-3', 'ICA-PALPA-NAZCA', 'ICA', 'MIGUEL ANGEL HERNANDEZ LOPEZ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(113, '3438', 'SAN PEDRO DE HUACARPANA', 'CHINCHA', 'SAN PEDRO DE HUACARPANA', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'LIZ SETH LINGMEY CANDIA ALBORNOZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(114, '3396', 'YAURILLA', 'ICA', 'LOS AQUIJES', 'I-2', 'ICA-PALPA-NAZCA', 'LA TINGUIÑA/PARCONA', 'ELIZABETH LOPEZ GOMEZ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(115, '3454', 'COPARA', 'NAZCA', 'VISTA ALEGRE', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'EILEEN MABEL PALOMINO POLANCO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(116, '3420', 'SUNAMPE', 'CHINCHA', 'SUNAMPE', 'I-3', 'CHINCHA - PISCO', 'CHINCHA', 'MARIA DEL CARMEN TAIPE HUAYRA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(117, '3471', 'TIBILLO', 'PALPA', 'TIBILLO', 'I-2', 'ICA-PALPA-NAZCA', 'PALPA', 'MARIA PALOMINO ROJAS ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(118, '3450', 'COYUNGO', 'NAZCA', 'CHANGUILLO', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'EPIFANIA ALEJANDRINA GARAYAR CASAVILCA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(119, '3416', 'CHINCHA BAJA', 'CHINCHA', 'CHINCHA BAJA', 'I-3', 'CHINCHA - PISCO', 'CHINCHA BAJA', 'AMELIA FERNANDA SORIA SARAVIA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(120, '3470', 'EL CARMEN', 'PALPA', 'SANTA CRUZ', 'I-1', 'ICA-PALPA-NAZCA', 'PALPA', 'JANET MERCEDES ROJAS ROJAS ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(121, '3439', 'LISCAY', 'CHINCHA', 'SAN PEDRO DE HUACARPANA', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'YORMARY GABRIELA PADILLA ZARRAGA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(122, '3409', 'SANTA DOMINGUITA', 'ICA', 'SANTIAGO', 'I-2', 'ICA-PALPA-NAZCA', 'SANTIAGO', 'OMAR ANTONIO FLORES LEGUA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(123, '3477', 'SAN CLEMENTE', 'PISCO', 'SAN CLEMENTE', 'I-3', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'CARMEN ROSA CHUMPITAZ VEGA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(124, '3392', 'PASAJE TINGUIÑA VALLE', 'ICA', 'PARCONA', 'I-3', 'ICA-PALPA-NAZCA', 'ICA', 'LOURDES AVILES ALFARO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(125, '3442', 'SAN AGUSTIN', 'CHINCHA', 'CHINCHA ALTA', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'AYRTON ROMARIO ALBERTO ASIN ZUÑIGA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(126, '6677', 'CAMACHO', 'PISCO', 'SAN CLEMENTE', 'I-1', 'CHINCHA - PISCO', 'SAN CLEMENTE', 'LESLIE ESTHEPHANY ROSALES VENTURO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(127, '24769', 'TAMBO DE MORA', 'CHINCHA', 'TAMBO DE MORA', 'I-3', 'CHINCHA - PISCO', 'CHINCHA BAJA', 'BERTHA LUISA HERRERA LEVANO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(128, '3382', 'COLLAZOS', 'ICA', 'SALAS', 'I-2', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'HUAMAN HERNANDEZ JOSE JAVIER', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(129, '3452', 'TULIN', 'NAZCA', 'EL INGENIO', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'CARLOS ALBERTO MONGE MARQUEZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(130, '3361', 'LA PALMA GRANDE', 'ICA', 'ICA', 'I-3', 'ICA-PALPA-NAZCA', 'LA PALMA', 'VILLAMARES RAMOS EDWIN JESUS', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(131, '3418', 'GROCIO PRADO', 'CHINCHA', 'GROCIO PRADO', 'I-3', 'CHINCHA - PISCO', 'CHINCHA', 'VILMA ARIAS MUNAYCO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(132, '3370', 'PUEBLO NUEVO', 'ICA', 'PUEBLO NUEVO', 'I-3', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'CRISTHIAN RAUL PALACIOS NEYRA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(133, '3422', 'CONDORILLO ALTO', 'CHINCHA', 'CHINCHA ALTA', 'I-2', 'CHINCHA - PISCO', 'CHINCHA', 'DAVID ANGEL DIAZ SALVATIERRA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(134, '3425', 'CHAVIN', 'CHINCHA', 'CHAVIN', 'I-2', 'CHINCHA - PISCO', 'PUEBLO NUEVO', 'MARIA DEL PILAR CARHUAPUMA ELEFONIO ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(135, '3462', 'SACRAMENTO', 'PALPA', 'PALPA', 'I-2', 'ICA-PALPA-NAZCA', 'PALPA', 'LISSETTE EDELVIS MUÑOZ GAMONAL ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(136, '3455', 'LAS TRANCAS', 'NAZCA', 'VISTA ALEGRE', 'I-2', 'ICA-PALPA-NAZCA', 'NASCA', 'SONIA BEATRIZ PEREZ MURIANO', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(137, '3368', 'ACOMAYO', 'ICA', 'PARCONA', 'I-3', 'ICA-PALPA-NAZCA', 'LA TINGUIÑA/PARCONA', 'CARMEN ROSA VELASQUEZ DE LA ROCA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(138, '3376', 'CACHICHE', 'ICA', 'ICA', 'I-2', 'ICA-PALPA-NAZCA', 'LA PALMA', 'ALDO MARCELO MONGE REYES', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(139, '3379', 'ARRABALES', 'ICA', 'SUBTANJALLA', 'I-2', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'ALEJANDRO JOSE CABRERA QUISPE ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(140, '3445', 'EL INGENIO', 'NAZCA', 'EL INGENIO', 'I-3', 'ICA-PALPA-NAZCA', 'NASCA', 'CARMEN MONICA CCENCHO ESPINOZA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(141, '3405', 'PUNO', 'ICA', 'TATE', 'I-1', 'ICA-PALPA-NAZCA', 'PUEBLO NUEVO', 'TEODORA ALEJANDRINA ASCENCIO CORDOVA ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(142, '3427', 'LURINCHINCHA', 'CHINCHA', 'CHINCHA BAJA', 'I-2', 'CHINCHA - PISCO', 'CHINCHA BAJA', 'JULIO RENAN RAFFO AGREDA', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(143, '30478', 'CSMC MENTE SANA', 'PALPA', 'PALPA', 'I-3', 'ICA-PALPA-NAZCA', 'PALPA', 'OSCAR GUSTAVO GUTIERREZ HERNANDEZ ', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(144, '3363', 'GUADALUPE', 'ICA', 'SALAS', 'I-3', 'ICA-PALPA-NAZCA', 'SAN JOAQUIN', 'NIDIA BRAVO HERNANDEZ', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_19_132419_create_actas_table', 1),
(5, '2025_09_19_132419_create_participantes_table', 1),
(6, '2025_09_19_132420_create_actividads_table', 1),
(7, '2025_09_19_132420_create_acuerdos_table', 1),
(8, '2025_09_19_132421_create_observacions_table', 1),
(9, '2025_09_27_094450_add_imagenes_to_actas_table', 2),
(10, '2025_09_29_082627_add_unidad_ejecutora_to_participantes_table', 3),
(11, '2025_09_30_085146_add_firmado_to_actas_table', 4),
(12, '2025_12_17_084619_add_username_to_users_table', 5),
(13, '2025_12_17_105043_add_role_to_users_table', 6),
(14, '2025_12_17_154440_add_is_active_to_users_table', 7),
(15, '2025_12_20_015422_update_users_table_structure', 8),
(16, '2025_12_26_080238_add_user_id_and_tipo_to_actas_table', 9),
(17, '2025_12_26_085221_create_monitoreo_detalles_table', 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monitoreo_detalles`
--

CREATE TABLE `monitoreo_detalles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `acta_id` bigint(20) UNSIGNED NOT NULL,
  `modulo_nombre` varchar(191) NOT NULL,
  `contenido` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`contenido`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `observaciones`
--

CREATE TABLE `observaciones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `acta_id` bigint(20) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `observaciones`
--

INSERT INTO `observaciones` (`id`, `acta_id`, `descripcion`, `created_at`, `updated_at`) VALUES
(88, 11, 'NO CUENTAN LOS CONSULTORIOS CON TODOS LOS EQUIPOS SUFICIENTES', '2025-09-30 22:22:53', '2025-09-30 22:22:53'),
(90, 2, '.', '2025-10-01 02:34:59', '2025-10-01 02:34:59'),
(91, 3, '.', '2025-10-01 02:36:28', '2025-10-01 02:36:28'),
(92, 5, '.', '2025-10-01 02:38:22', '2025-10-01 02:38:22'),
(93, 6, '.', '2025-10-01 02:40:04', '2025-10-01 02:40:04'),
(99, 14, '', '2025-10-01 17:14:48', '2025-10-01 17:14:48'),
(103, 15, '', '2025-10-01 17:33:15', '2025-10-01 17:33:15'),
(104, 16, '', '2025-10-01 17:33:28', '2025-10-01 17:33:28'),
(106, 17, '', '2025-10-01 17:37:52', '2025-10-01 17:37:52'),
(107, 18, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-01 22:32:17', '2025-10-01 22:32:17'),
(108, 19, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-01 22:38:02', '2025-10-01 22:38:02'),
(110, 21, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-01 23:02:39', '2025-10-01 23:02:39'),
(111, 22, 'ASISTENCIA TECNICA - PRESENCIAL - ING. SELENE PINEDA ESTUVO PRESENTE EN LA ASISTENCIA TECNICA A LOS MEDICOS DE SALUD', '2025-10-01 23:16:13', '2025-10-01 23:16:13'),
(113, 24, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-02 00:07:57', '2025-10-02 00:07:57'),
(116, 20, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-02 00:19:30', '2025-10-02 00:19:30'),
(119, 26, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-02 00:38:44', '2025-10-02 00:38:44'),
(124, 28, '', '2025-10-02 04:28:06', '2025-10-02 04:28:06'),
(125, 29, '', '2025-10-02 04:40:34', '2025-10-02 04:40:34'),
(126, 30, '', '2025-10-02 04:45:39', '2025-10-02 04:45:39'),
(127, 31, '', '2025-10-02 05:12:24', '2025-10-02 05:12:24'),
(128, 33, '', '2025-10-02 05:26:43', '2025-10-02 05:26:43'),
(130, 35, 'ASISTENCIA TECNICA PRESENCIAL CON EL OBJETIVO DE REACTIVAR EL MODULO DE CONSULTA EXTERNA', '2025-10-02 12:09:20', '2025-10-02 12:09:20'),
(132, 37, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-02 12:47:21', '2025-10-02 12:47:21'),
(136, 39, '.', '2025-10-02 13:44:15', '2025-10-02 13:44:15'),
(138, 9, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-02 14:42:16', '2025-10-02 14:42:16'),
(140, 10, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-02 14:54:24', '2025-10-02 14:54:24'),
(144, 43, 'ASISTENCIA TECNICA - PRESENCIAL', '2025-10-02 15:58:41', '2025-10-02 15:58:41'),
(145, 44, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-02 16:15:22', '2025-10-02 16:15:22'),
(147, 46, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-02 17:48:02', '2025-10-02 17:48:02'),
(148, 42, 'SE ENCUENTRA LA DIFICULTAD DE AGREGAR RAM(REACCIÓN ALÉRGICA A MEDICAMENTO) POR LO CUAL SE DERIVARA EL CASO A MINSA.', '2025-10-02 18:02:44', '2025-10-02 18:02:44'),
(149, 42, 'SE CONFIGURARA EL REFCON CON LOS DATOS DEL JEFE DE ESTABLECIMIENTO PARA QUE PUEDAN A TRAVÉS DEL SIHCE REALIZAR LAS REFERENCIAS.', '2025-10-02 18:02:44', '2025-10-02 18:02:44'),
(151, 23, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-02 18:48:33', '2025-10-02 18:48:33'),
(152, 36, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-02 19:01:39', '2025-10-02 19:01:39'),
(153, 25, 'ASISTENCIA TECNICA - PRESENCIAL', '2025-10-02 19:41:18', '2025-10-02 19:41:18'),
(154, 47, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-02 20:20:50', '2025-10-02 20:20:50'),
(157, 48, 'ASISTENCIA TECNICA - PRESENCIAL', '2025-10-02 20:47:38', '2025-10-02 20:47:38'),
(160, 40, '', '2025-10-02 21:22:51', '2025-10-02 21:22:51'),
(161, 49, '.', '2025-10-02 21:24:33', '2025-10-02 21:24:33'),
(162, 34, '', '2025-10-02 21:26:30', '2025-10-02 21:26:30'),
(163, 32, '', '2025-10-02 21:31:47', '2025-10-02 21:31:47'),
(164, 41, '', '2025-10-02 21:35:04', '2025-10-02 21:35:04'),
(166, 50, '.', '2025-10-03 00:10:30', '2025-10-03 00:10:30'),
(179, 51, 'PARTICIAPANTES: Dra Stephanie, Erick Montes', '2025-10-06 02:21:47', '2025-10-06 02:21:47'),
(182, 54, 'Durante la visita, la profesional manifestó que, por el momento, no desea utilizar el sistema ni recibir la capacitación correspondiente, solicitando que se respete su decisión, dado que se encuentra próxima a jubilarse.', '2025-10-06 03:58:31', '2025-10-06 03:58:31'),
(183, 55, '', '2025-10-06 04:11:43', '2025-10-06 04:11:43'),
(184, 56, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-06 16:05:30', '2025-10-06 16:05:30'),
(185, 57, '.', '2025-10-06 17:55:54', '2025-10-06 17:55:54'),
(202, 62, '', '2025-10-07 17:07:45', '2025-10-07 17:07:45'),
(203, 63, 'SE DETECTO EL ERROR QUE NO ESTABA VISUALIZANDO EL CERTIFICADO DIGITAL, SE PASO ACTUALIZAR DRIVERS DEL LECTOR DE DNI E.', '2025-10-07 17:16:12', '2025-10-07 17:16:12'),
(204, 64, '.', '2025-10-09 18:09:03', '2025-10-09 18:09:03'),
(206, 66, '', '2025-10-09 20:18:44', '2025-10-09 20:18:44'),
(207, 67, '', '2025-10-09 20:19:35', '2025-10-09 20:19:35'),
(217, 72, '.', '2025-10-09 21:28:46', '2025-10-09 21:28:46'),
(218, 73, '.', '2025-10-09 21:30:58', '2025-10-09 21:30:58'),
(225, 61, 'SE ENCONTRO UNA DEFICIENCIA AL INTENTAR ACCEDER A LAS PAGINAS DE RENIEC, EL PROVEEDOR DE INTERNET BLOQUEA EL ACCESO.', '2025-10-10 16:05:59', '2025-10-10 16:05:59'),
(235, 82, '', '2025-10-11 01:45:08', '2025-10-11 01:45:08'),
(236, 75, '', '2025-10-11 01:55:18', '2025-10-11 01:55:18'),
(238, 65, '', '2025-10-11 01:57:33', '2025-10-11 01:57:33'),
(239, 45, 'LA ASISTENCIA SE REALIZÓ MEDIANTE ANYDESK', '2025-10-11 01:57:57', '2025-10-11 01:57:57'),
(240, 38, 'LA ASISTENCIA SE REALIZÓ MEDIANTE ANYDESK', '2025-10-11 01:58:20', '2025-10-11 01:58:20'),
(241, 27, 'LA ASISTENCIA SE REALIZÓ MEDIANTE ANYDESK', '2025-10-11 01:59:00', '2025-10-11 01:59:00'),
(242, 13, 'LA PACIENTE NO APARECÍA PORQUE NO SE HABIA CONFIRMADO LA CITA, UNA VEZ REALIZADO SE PUDO HACER EL TRIAJE Y LA ATENCIÓN.', '2025-10-11 01:59:36', '2025-10-11 01:59:36'),
(243, 12, '', '2025-10-11 01:59:57', '2025-10-11 01:59:57'),
(244, 8, 'NO CUENTAN LOS CONSULTORIOS CON TODOS LOS EQUIPOS SUFICIENTES', '2025-10-11 02:00:29', '2025-10-11 02:00:29'),
(245, 7, 'LA ASISTENCIA SE REALIZÓ MEDIANTE ANYDESK', '2025-10-11 02:00:50', '2025-10-11 02:00:50'),
(246, 4, 'SE APOYÓ EN LA SINCRONIZACIÓN PERO NO SE VIÓ REFLEJADA EN CITAS, SE ENVIÓ TICKET A MINSA', '2025-10-11 02:01:12', '2025-10-11 02:01:12'),
(247, 1, '.', '2025-10-11 02:01:37', '2025-10-11 02:01:37'),
(250, 84, '.', '2025-10-11 15:33:44', '2025-10-11 15:33:44'),
(251, 83, 'LA ASISTENCIA SE REALIZÓ MEDIANTE GOOGLE MEET (https://meet.google.com/rwt-shpd-zza)', '2025-10-11 15:59:23', '2025-10-11 15:59:23'),
(252, 85, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-11 17:46:52', '2025-10-11 17:46:52'),
(253, 86, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-11 18:31:24', '2025-10-11 18:31:24'),
(255, 88, '.', '2025-10-11 19:20:06', '2025-10-11 19:20:06'),
(256, 87, '.', '2025-10-11 19:20:32', '2025-10-11 19:20:32'),
(259, 53, 'IMPLEMENTADORES: Dra Stephanie (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 00:22:09', '2025-10-12 00:22:09'),
(261, 58, 'IMPLEMENTADORES: Dra Stephanie (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 00:34:19', '2025-10-12 00:34:19'),
(262, 52, 'IMPLEMENTADORES: Dra. Stephanie Fernandez (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 00:46:30', '2025-10-12 00:46:30'),
(263, 59, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 00:57:23', '2025-10-12 00:57:23'),
(264, 68, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 01:20:34', '2025-10-12 01:20:34'),
(267, 69, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 01:57:26', '2025-10-12 01:57:26'),
(269, 71, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 02:59:15', '2025-10-12 02:59:15'),
(270, 76, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 03:32:20', '2025-10-12 03:32:20'),
(272, 70, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 04:00:39', '2025-10-12 04:00:39'),
(273, 77, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 04:04:14', '2025-10-12 04:04:14'),
(275, 78, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 04:30:42', '2025-10-12 04:30:42'),
(277, 79, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 04:54:58', '2025-10-12 04:54:58'),
(278, 80, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 05:07:53', '2025-10-12 05:07:53'),
(279, 81, 'IMPLEMENTADORES: Dra Stephanie Fernández (Red de Salud), Erick Montes y Selene Pineda (ICATEC)', '2025-10-12 05:27:06', '2025-10-12 05:27:06'),
(280, 89, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 02:48:47', '2025-10-13 02:48:47'),
(281, 90, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 02:50:59', '2025-10-13 02:50:59'),
(282, 91, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 02:53:19', '2025-10-13 02:53:19'),
(283, 92, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 02:55:58', '2025-10-13 02:55:58'),
(287, 93, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:02:49', '2025-10-13 03:02:49'),
(288, 94, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:06:41', '2025-10-13 03:06:41'),
(289, 95, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:09:21', '2025-10-13 03:09:21'),
(290, 96, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:12:43', '2025-10-13 03:12:43'),
(291, 97, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:14:31', '2025-10-13 03:14:31'),
(292, 98, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:16:01', '2025-10-13 03:16:01'),
(293, 99, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:21:02', '2025-10-13 03:21:02'),
(294, 100, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:22:31', '2025-10-13 03:22:31'),
(295, 101, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:24:48', '2025-10-13 03:24:48'),
(296, 102, 'IMPLEMENTADORES: DRA. STEPHANIE FERNANDEZ (RED DE SALUD), SELENE PINEA (ICATEC)', '2025-10-13 03:26:00', '2025-10-13 03:26:00'),
(297, 103, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-13 16:55:11', '2025-10-13 16:55:11'),
(298, 104, '.', '2025-10-13 20:29:03', '2025-10-13 20:29:03'),
(299, 105, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-13 22:47:05', '2025-10-13 22:47:05'),
(300, 74, '', '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(302, 106, '', '2025-10-14 00:39:54', '2025-10-14 00:39:54'),
(312, 113, 'IMPLEMENTADORES:  Erick Montes y Selene Pineda', '2025-10-14 20:38:36', '2025-10-14 20:38:36'),
(315, 116, 'SE ACUDE A BRINDAR ASISTENCIA TECNICA A RESPONSABLES DE CITAS Y TRIAJE', '2025-10-14 23:41:37', '2025-10-14 23:41:37'),
(317, 109, 'RESPONSABLES DE LA CAPACITACION : Erick Montes y Selene Pineda', '2025-10-15 01:23:15', '2025-10-15 01:23:15'),
(318, 110, 'IMPLEMENTADORES: Erick Montes y Selene Pineda', '2025-10-15 01:50:56', '2025-10-15 01:50:56'),
(319, 111, 'IMPLEMENTADORES: Erick Montes y Selene Pineda', '2025-10-15 02:13:08', '2025-10-15 02:13:08'),
(320, 112, 'IMPLEMENTADORES: Erick Montes y Selene Pineda', '2025-10-15 02:20:29', '2025-10-15 02:20:29'),
(322, 117, '.', '2025-10-15 14:43:52', '2025-10-15 14:43:52'),
(324, 119, 'PERSONAL RESPONSABLE DE MODULO TIENE UN HORARIO LABORAL (NO FRECUENTE) - EN DIAS QUE NO ACUDE AL ESTABLECIMIENTO, OTRO PERSONAL DEBERA DE ASUMIR LA RESPONSABILIDAD DE DICHO MODULO.', '2025-10-15 16:27:04', '2025-10-15 16:27:04'),
(325, 120, 'JEFE REALIZO CONEXION DE RED POR CABLEADO DE MANERA PROVISIONAL PARA PROBAR LA FRECUENCIA DEL INTERNET Y SI COMPRUEBA ESTABILIDAD, SE PROCEDE A ASEGURAR EL CABLE', '2025-10-15 16:53:24', '2025-10-15 16:53:24'),
(326, 121, '.', '2025-10-15 17:20:30', '2025-10-15 17:20:30'),
(327, 122, '.', '2025-10-15 20:14:02', '2025-10-15 20:14:02'),
(328, 123, '.', '2025-10-15 20:46:23', '2025-10-15 20:46:23'),
(329, 124, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-16 00:42:14', '2025-10-16 00:42:14'),
(330, 125, 'APOYO EN LA IMPLEMENTACION CON USO DE ANYDESK', '2025-10-16 00:53:44', '2025-10-16 00:53:44'),
(331, 108, 'ASISTENCIA TECNICA - PRESENCIAL', '2025-10-16 03:07:18', '2025-10-16 03:07:18'),
(332, 114, 'ASISTENCIA TECNICA - PRESENCIAL', '2025-10-16 03:12:19', '2025-10-16 03:12:19'),
(333, 115, 'ASISTENCIA TECNICA - PRESENCIAL', '2025-10-16 03:15:22', '2025-10-16 03:15:22'),
(343, 134, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-17 15:47:36', '2025-10-17 15:47:36'),
(344, 135, '.', '2025-10-17 15:58:38', '2025-10-17 15:58:38'),
(345, 136, '.', '2025-10-17 15:59:40', '2025-10-17 15:59:40'),
(346, 126, 'ASISTENCIA TECNICA (PRESENCIAL)', '2025-10-17 16:56:16', '2025-10-17 16:56:16'),
(347, 127, 'ASISTENCIA TECNICA (PRESENCIAL)', '2025-10-17 16:56:44', '2025-10-17 16:56:44'),
(348, 128, 'ASISTENCIA TECNICA (PRESENCIAL)', '2025-10-17 16:57:41', '2025-10-17 16:57:41'),
(349, 129, 'ASISTENCIA TECNICA (PRESENCIAL)', '2025-10-17 16:59:54', '2025-10-17 16:59:54'),
(352, 137, 'ASISTENCIA TECNICA (PRESENCIAL)', '2025-10-20 00:25:47', '2025-10-20 00:25:47'),
(358, 130, 'ASISTENCIA TECNICA (PRESENCIAL)', '2025-10-20 01:41:47', '2025-10-20 01:41:47'),
(360, 131, 'ASISTENCIA TECNICA (PRESENCIAL)', '2025-10-20 01:56:14', '2025-10-20 01:56:14'),
(361, 132, 'ASISTENCIA TECNICA (PRESENCIAL)', '2025-10-20 04:01:02', '2025-10-20 04:01:02'),
(363, 133, 'ASISTENCIA TECNICA (PRESENCIAL)', '2025-10-20 04:48:50', '2025-10-20 04:48:50'),
(364, 138, 'ASISTENCIA TECNICA - ANYDESK (REMOTO)', '2025-10-21 14:26:59', '2025-10-21 14:26:59'),
(370, 60, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-22 17:19:23', '2025-10-22 17:19:23'),
(371, 141, 'EL EQUIPO QUE UTILIZA EL MEDICO EN SU CONSULTORIO PRESENTA DEFICIENCIAS AL REALIZAR LA FIRMA ELECTRONICA.', '2025-10-22 22:09:19', '2025-10-22 22:09:19'),
(372, 142, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-22 22:21:54', '2025-10-22 22:21:54'),
(374, 144, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-22 22:35:55', '2025-10-22 22:35:55'),
(376, 146, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-22 22:45:55', '2025-10-22 22:45:55'),
(379, 148, '.', '2025-10-23 16:51:15', '2025-10-23 16:51:15'),
(380, 149, '.', '2025-10-23 16:54:43', '2025-10-23 16:54:43'),
(381, 140, '', '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(383, 151, 'SE BRINDÓ ASISTENCIA POR ANYDESK', '2025-10-24 21:44:22', '2025-10-24 21:44:22'),
(384, 150, '.', '2025-10-24 21:51:04', '2025-10-24 21:51:04'),
(386, 153, '.', '2025-10-27 21:33:47', '2025-10-27 21:33:47'),
(387, 152, '.', '2025-10-27 21:34:38', '2025-10-27 21:34:38'),
(388, 154, '.', '2025-10-27 21:39:13', '2025-10-27 21:39:13'),
(389, 155, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-28 16:30:31', '2025-10-28 16:30:31'),
(390, 143, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-28 16:45:27', '2025-10-28 16:45:27'),
(392, 145, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-28 16:50:00', '2025-10-28 16:50:00'),
(394, 156, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-28 23:13:23', '2025-10-28 23:13:23'),
(396, 157, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-10-29 00:23:38', '2025-10-29 00:23:38'),
(399, 158, 'ASISTENCIA TECNICA (PRESENCIAL) - PACIENTES QUE NO TENIAN SIS NO PODIAN SER TRIADAS - DESDE CITAS - INGRESAR A ORDENES SOLICITADAS Y CONFIRMAR', '2025-10-29 20:51:29', '2025-10-29 20:51:29'),
(401, 159, 'IMPLEMENTADOR: SELENE PINEDA (ICATEC)', '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(402, 147, 'ASISTENCIA TECNICA (PRESENCIAL).', '2025-10-29 21:06:31', '2025-10-29 21:06:31'),
(404, 118, 'IMPLEMENTADORES: Erick Montes y Selene Pineda', '2025-10-30 02:09:47', '2025-10-30 02:09:47'),
(407, 160, 'ASISTENCIA TECNICA VIRTUAL - AnyDesk', '2025-10-30 15:54:06', '2025-10-30 15:54:06'),
(409, 161, 'SE BRINDÓ ASISTENCIA POR ANYDESK', '2025-11-05 15:39:58', '2025-11-05 15:39:58'),
(410, 162, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-11-13 23:12:38', '2025-11-13 23:12:38'),
(411, 163, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-11-13 23:52:16', '2025-11-13 23:52:16'),
(412, 164, 'SE VISUALIZA QUE EN DOCUMENTOS ELECTRONICOS GENERADOS NO APARECE EL DOCUMENTO DE LA REFERENCIA, MAS SI APARECE CUANDO VAMOS A LA OPCION DE EDITAR, POR OTRO LADO EL DOCUMENTO LO PODEMOS DESCARGAR DESDE EL APLICATIVO REFCON.', '2025-11-14 00:19:32', '2025-11-14 00:19:32'),
(413, 165, 'SE BRINDÓ CAPACITACIÓN A TRAVES DE GOOGLE MEET (https://meet.google.com/hzs-ktrc-rfq)', '2025-11-15 16:04:57', '2025-11-15 16:04:57'),
(415, 166, 'Se realizo capacitacion y asistencia técnica de manera presencial, a nuevo responsable de Modulo de Gestión Administrativa para actualizar datos de personal y otros en programación de turnos de meses: Noviembre y Diciembre 2025', '2025-11-18 17:20:21', '2025-11-18 17:20:21'),
(418, 168, 'pacientes que no tienes SIS no visualizados en TRIAJE. Mensaje de error en Modulo de Consulta externa por datos mal ingresados en TRIAJE', '2025-11-18 18:07:34', '2025-11-18 18:07:34'),
(419, 169, 'Se Brindo Asistencia técnica a nueva responsable del módulo de Gestión Administrativa del CS San Juan Bautista para programar los meses de enero, febrero y marzo del 2026, según normativa vigente.', '2025-11-18 18:11:59', '2025-11-18 18:11:59'),
(421, 170, 'Se realizo Asistencia técnica virtual (AnyDesk) en P.S Yaurilla en módulo de Gestión Administrativa', '2025-11-18 18:26:16', '2025-11-18 18:26:16'),
(422, 171, 'Se realizo Asistencia técnica virtual (AnyDesk) en P.S Santa Barbara en módulo de Gestión Administrativa', '2025-11-18 18:33:06', '2025-11-18 18:33:06'),
(423, 172, '', '2025-11-18 18:41:03', '2025-11-18 18:41:03'),
(424, 173, 'LUEGO DE SOLUCIONAR EL ERROR, SE PROCEDIO  CON LA FIRMA DE TODAS LAS ATENCIONES REALIZADAS DEL MES', '2025-11-18 18:46:47', '2025-11-18 18:46:47'),
(427, 175, '.', '2025-11-18 23:31:47', '2025-11-18 23:31:47'),
(428, 176, '.', '2025-11-20 16:20:47', '2025-11-20 16:20:47'),
(429, 177, '.', '2025-11-20 22:38:53', '2025-11-20 22:38:53'),
(430, 178, '.', '2025-11-21 20:36:00', '2025-11-21 20:36:00'),
(432, 179, '.', '2025-11-21 21:06:59', '2025-11-21 21:06:59'),
(433, 180, 'SE BRINDÓ ASISTENCIA POR ANYDESK', '2025-11-25 02:22:43', '2025-11-25 02:22:43'),
(434, 181, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-11-25 16:44:23', '2025-11-25 16:44:23'),
(435, 182, 'SE BRINDÓ ASISTENCIA POR ANYDESK', '2025-11-26 19:08:54', '2025-11-26 19:08:54'),
(436, 182, 'EL MEDICO NO PUDO REALIZAR LA FIRMA DIGITAL POR CONTAR CON EL DNIe 3.0', '2025-11-26 19:08:54', '2025-11-26 19:08:54'),
(438, 167, '', '2025-11-26 21:30:39', '2025-11-26 21:30:39'),
(440, 174, 'ASISTENCIA TÉCNICA – SINCRONIZACIÓN DE FARMACIA: Visualización del stock de medicamentos del establecimiento de salud en el Módulo de Consulta Externa.', '2025-11-26 21:44:21', '2025-11-26 21:44:21'),
(441, 183, '.', '2025-11-27 14:31:26', '2025-11-27 14:31:26'),
(442, 184, '.', '2025-11-27 15:56:26', '2025-11-27 15:56:26'),
(444, 185, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-11-27 19:59:28', '2025-11-27 19:59:28'),
(445, 107, '.', '2025-12-01 19:23:32', '2025-12-01 19:23:32'),
(446, 186, 'SE CAPACITO CON EL ING. IMPLEMENTADOR ERNESTO MUÑANTE', '2025-12-01 23:50:40', '2025-12-01 23:50:40'),
(447, 187, 'LA CAPACITACION SE LLEVO A CABO CONJUNTAMENTE CON LOS IMPLEMENTADORES, EL ING. ERNESTO MUÑANTE Y LA ING. LIDA YAÑEZ', '2025-12-02 15:30:01', '2025-12-02 15:30:01'),
(448, 188, '', '2025-12-04 04:04:17', '2025-12-04 04:04:17'),
(449, 189, 'SE CAPACITO CON EL ING. IMPLEMENTADOR ERNESTO MUÑANTE', '2025-12-05 13:45:31', '2025-12-05 13:45:31'),
(450, 190, 'SE CAPACITO CON EL ING. IMPLEMENTADOR ERNESTO MUÑANTE', '2025-12-05 13:49:25', '2025-12-05 13:49:25'),
(455, 191, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-12-11 17:43:51', '2025-12-11 17:43:51'),
(457, 192, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-12-11 18:51:14', '2025-12-11 18:51:14'),
(458, 193, 'SE BRINDÓ ASISTENCIA POR ANYDESK', '2025-12-15 18:02:52', '2025-12-15 18:02:52'),
(460, 194, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-12-20 02:02:17', '2025-12-20 02:02:17'),
(464, 196, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-12-21 13:40:52', '2025-12-21 13:40:52'),
(465, 195, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-12-21 13:43:59', '2025-12-21 13:43:59'),
(466, 197, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-12-21 13:44:49', '2025-12-21 13:44:49'),
(472, 198, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-12-22 22:35:56', '2025-12-22 22:35:56'),
(491, 199, 'NINGUNA OBSERVACION ENCONTRADA.', '2025-12-26 12:55:47', '2025-12-26 12:55:47'),
(492, 199, 'PRUEBA', '2025-12-26 12:55:47', '2025-12-26 12:55:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participantes`
--

CREATE TABLE `participantes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `acta_id` bigint(20) UNSIGNED NOT NULL,
  `dni` varchar(15) NOT NULL,
  `apellidos` varchar(255) NOT NULL,
  `nombres` varchar(255) NOT NULL,
  `cargo` varchar(255) DEFAULT NULL,
  `modulo` varchar(255) DEFAULT NULL,
  `unidad_ejecutora` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `participantes`
--

INSERT INTO `participantes` (`id`, `acta_id`, `dni`, `apellidos`, `nombres`, `cargo`, `modulo`, `unidad_ejecutora`, `created_at`, `updated_at`) VALUES
(113, 11, '71883058', 'DONAYRE SALINAS', 'ROBERTO', 'MEDICO', 'Inmunizaciones', NULL, '2025-09-30 22:22:53', '2025-09-30 22:22:53'),
(117, 2, '48187801', 'TATAJE CARPIO', 'ANA', 'CITAS', 'Citas', NULL, '2025-10-01 02:34:59', '2025-10-01 02:34:59'),
(118, 2, '22273456', 'CHAVEZ VALENTIN', 'MARIA DEL CARMEN', 'CITAS', 'Citas', NULL, '2025-10-01 02:34:59', '2025-10-01 02:34:59'),
(119, 2, '22285609', 'HERNANDEZ DE LA CRUZ', 'WILFREDO EUSEBIO', 'CITAS', 'Citas', NULL, '2025-10-01 02:34:59', '2025-10-01 02:34:59'),
(120, 3, '48187801', 'TATAJE CARPIO', 'ANA', 'TRIAJE', 'Triaje', NULL, '2025-10-01 02:36:28', '2025-10-01 02:36:28'),
(121, 3, '22273456', 'CHAVEZ VALENTIN', 'MARIA DEL CARMEN', 'TRIAJE', 'Triaje', NULL, '2025-10-01 02:36:28', '2025-10-01 02:36:28'),
(122, 3, '22285609', 'HERNANDEZ DE LA CRUZ', 'WILFREDO EUSEBIO', 'TRIAJE', 'Triaje', NULL, '2025-10-01 02:36:28', '2025-10-01 02:36:28'),
(123, 5, '214512141', 'ANGULO LEGUA', 'JOSE ESTEBAN', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-01 02:38:22', '2025-10-01 02:38:22'),
(124, 6, '70314272', 'FLORES RAMIREZ', 'CRISTHIAN EDUARDO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-01 02:40:04', '2025-10-01 02:40:04'),
(130, 14, '41995189', 'QUISPE JANAMPA', 'ANGELICA', 'TECNICA DE ENFERMERIA', 'Gestión Administrativa', NULL, '2025-10-01 17:14:48', '2025-10-01 17:14:48'),
(137, 15, '42432198', 'VALLE TATAJE DE SOLIS', 'NILA DARIA', 'TECNICA DE ENFERMERIA', 'Citas', NULL, '2025-10-01 17:33:15', '2025-10-01 17:33:15'),
(138, 15, '21463014', 'RAMIREZ OCHOA', 'LILIANA SOLEDAD', 'TECNICO DE ENFERMERIA', 'Citas', NULL, '2025-10-01 17:33:15', '2025-10-01 17:33:15'),
(139, 16, '41995189', 'QUISPE JANAMPA', 'ANGELICA', 'TECNICA DE ENFERMERIA', 'Triaje', NULL, '2025-10-01 17:33:28', '2025-10-01 17:33:28'),
(140, 16, '42432198', 'VALLE TATAJE DE SOLIS', 'NILA DARIA', 'TECNICO DE ENFERMERIA', 'Triaje', NULL, '2025-10-01 17:33:28', '2025-10-01 17:33:28'),
(141, 16, '21463014', 'RAMIREZ OCHOA', 'LILIANA SOLEDAD', 'TECNICO DE ENFERMERIA', 'Triaje', NULL, '2025-10-01 17:33:28', '2025-10-01 17:33:28'),
(143, 17, '73039179', 'SALINAS LAOS', 'ARANTZA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-01 17:37:52', '2025-10-01 17:37:52'),
(144, 18, '22194238', 'HERNANDEZ ALVITES', 'YRENE YRIS', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-01 22:32:17', '2025-10-01 22:32:17'),
(145, 19, '70137275', 'SOLIS CALLE', 'KATHERIN JANETH', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-01 22:38:02', '2025-10-01 22:38:02'),
(147, 21, '99999999', 'CHACALTANA SUAREZ', 'JORGE RODOLFO', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-01 23:02:39', '2025-10-01 23:02:39'),
(148, 22, '21417743', 'ALVA PERALTA', 'ESTHER REBECA', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-01 23:16:13', '2025-10-01 23:16:13'),
(149, 22, '21536155', 'ALDORADIN CHAHUA', 'MARCI PATRICIA', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-01 23:16:13', '2025-10-01 23:16:13'),
(151, 24, '40981285', 'GALINDO MATTA', 'CARMEN MARLENI', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 00:07:57', '2025-10-02 00:07:57'),
(154, 20, '18145061', 'CABRERA CHUNQUE', 'SARITA', 'CITAS', 'Citas', 'RED DE SALUD ICA', '2025-10-02 00:19:30', '2025-10-02 00:19:30'),
(157, 26, '21477139', 'DE LA CRUZ RAMOS', 'JOSE JULIO', 'GESTION ADMINISTRATIVA', 'Gestión Administrativa', 'RED DE SALUD ICA', '2025-10-02 00:38:44', '2025-10-02 00:38:44'),
(162, 28, '70137275', 'SOLIS CALLE', 'KATHERIN JANET', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 04:28:06', '2025-10-02 04:28:06'),
(163, 29, '28806793', 'LOPEZ ALDERETE', 'DOMITILA', NULL, 'Triaje', 'RED DE SALUD ICA', '2025-10-02 04:40:34', '2025-10-02 04:40:34'),
(164, 30, '18074362', 'PAREDES MONTEJO', 'EDWIN SEGUNDO', 'JEFE PS', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 04:45:39', '2025-10-02 04:45:39'),
(165, 31, '22319140', 'AURIS HERNANDEZ', 'EDGAR ANTONIO', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 05:12:24', '2025-10-02 05:12:24'),
(166, 33, '41814467', 'MARTINEZ ASCONA', 'JOSELITO', NULL, NULL, 'RED DE SALUD ICA', '2025-10-02 05:26:43', '2025-10-02 05:26:43'),
(169, 35, '42860433', 'ALTAMIRANO VELASQUEZ', 'BRENDA MARGOT', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 12:09:20', '2025-10-02 12:09:20'),
(171, 37, '21443649', 'CRUZ SERRUTO', 'GLORIA BEATRIZ', 'TRIAJE', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 12:47:21', '2025-10-02 12:47:21'),
(176, 39, '40441419', 'PEÑA FLORES', 'ALEX', 'MEDICO', 'Gestión Administrativa', NULL, '2025-10-02 13:44:15', '2025-10-02 13:44:15'),
(178, 9, '47439795', 'PUELLES PORTILLA', 'MERY NOELIA', 'ADMINISTRATIVO', 'Gestión Administrativa', NULL, '2025-10-02 14:42:16', '2025-10-02 14:42:16'),
(179, 9, '21798273', 'FELIX PACHAS', 'FLOR DE MARIA VICTORIA', 'DOCTOR', 'Consulta Externa: Medicina', NULL, '2025-10-02 14:42:16', '2025-10-02 14:42:16'),
(182, 10, '40212151', 'ONOFRE AVALOS', 'JOSE CARLOS', 'ESTADISTICO', 'Gestión Administrativa', NULL, '2025-10-02 14:54:24', '2025-10-02 14:54:24'),
(187, 43, '21425970', 'MOTTA', 'YENNIFER YSABEL', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 15:58:41', '2025-10-02 15:58:41'),
(188, 43, '21532239', 'MENESES VICENCIO', 'LEONEL', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 15:58:41', '2025-10-02 15:58:41'),
(189, 43, '21423398', 'MEDINA ZEA', 'CARLOS', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 15:58:41', '2025-10-02 15:58:41'),
(190, 43, '21417743', 'ALVA PERALTA', 'ESTHER REBECA', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 15:58:41', '2025-10-02 15:58:41'),
(191, 44, '07868057', 'VIVANCO RAMOS', 'ELVIA ULANINA', 'OBSTETRA', 'Atencion Prenatal', NULL, '2025-10-02 16:15:22', '2025-10-02 16:15:22'),
(192, 44, '45827160', 'AYBAR PARIONA', 'IBETH', 'OBSTETRA', 'Atencion Prenatal', NULL, '2025-10-02 16:15:22', '2025-10-02 16:15:22'),
(194, 46, '21477139', 'DE LA CRUZ RAMOS', 'JOSE JULIO', 'GESTION ADMINISTRATIVA', 'Gestión Administrativa', 'RED DE SALUD ICA', '2025-10-02 17:48:02', '2025-10-02 17:48:02'),
(195, 42, '70123620', 'FERNANDEZ BULEJE', 'THALIA CHASKA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-02 18:02:44', '2025-10-02 18:02:44'),
(197, 23, '18145061', 'CABRERA CHUNQUE', 'SARITA', 'GESTION ADMINISTRATIVA', 'Gestión Administrativa', 'RED DE SALUD ICA', '2025-10-02 18:48:33', '2025-10-02 18:48:33'),
(198, 36, '47125272', 'MEDINA OSCCO', 'LORENA LISBETH', 'CITAS', 'Citas', 'RED DE SALUD ICA', '2025-10-02 19:01:39', '2025-10-02 19:01:39'),
(199, 25, '21530877', 'JARA CANTORAL', 'MARIBEL NANCY', 'TRIAJE', 'Triaje', 'RED DE SALUD ICA', '2025-10-02 19:41:18', '2025-10-02 19:41:18'),
(200, 25, '21526781', 'ANCHANTE PEREZ', 'DIOMEDES ALBERTO', 'TRIAJE', 'Triaje', 'RED DE SALUD ICA', '2025-10-02 19:41:18', '2025-10-02 19:41:18'),
(201, 47, '70285712', 'RODRIGUEZ VIZARRETA', 'JOSE LUIS', 'GESTION ADMINISTRATIVA', 'Gestión Administrativa', 'RED DE SALUD ICA', '2025-10-02 20:20:50', '2025-10-02 20:20:50'),
(204, 48, '41854412', 'CAMPOS BARZENA', 'ERNESTO ALFONSO', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 20:47:38', '2025-10-02 20:47:38'),
(205, 48, '21423684', 'PUZA MENDOZA', 'ROSANA AMPARO', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 20:47:38', '2025-10-02 20:47:38'),
(209, 40, '70280474', 'HERNANDEZ ALBUJAR ALBUJAR', 'MAYRA', 'MEDICO', NULL, 'RED DE SALUD ICA', '2025-10-02 21:22:51', '2025-10-02 21:22:51'),
(210, 40, '72492439', 'MALLMA CAÑAHUARAY', 'MIRELLY GUADALUPE', 'TECNICO DE ENFERMERIA', 'Triaje', 'DIRESA ICA', '2025-10-02 21:22:51', '2025-10-02 21:22:51'),
(211, 49, '73265256', 'LAZO MARTINEZ', 'JORGE MAURICIO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-02 21:24:33', '2025-10-02 21:24:33'),
(212, 34, '42860433', 'ALTAMIRANO VELASQUEZ', 'BRENDA MARGOT', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 21:26:30', '2025-10-02 21:26:30'),
(213, 34, '21564335', 'MONGE REYES', 'ALDO MARCELO', 'MEDICO', NULL, 'RED DE SALUD ICA', '2025-10-02 21:26:30', '2025-10-02 21:26:30'),
(214, 32, '21407088', 'VELASQUEZ SALCEDO', 'LIDA FLOR', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-02 21:31:47', '2025-10-02 21:31:47'),
(215, 41, '21493750', 'ALVITES ALFARO', 'LOURDES PILAR', 'JEFE PS', NULL, 'RED DE SALUD ICA', '2025-10-02 21:35:04', '2025-10-02 21:35:04'),
(217, 50, '41674632', 'FLORES MOYANO', 'LUIS ALBERTO', 'ADMISION', 'Citas', NULL, '2025-10-03 00:10:30', '2025-10-03 00:10:30'),
(251, 51, '47840719', 'MORON BERNAOLA', 'JORGE LUIS', 'ESTADISTICO', NULL, 'RED DE SALUD ICA', '2025-10-06 02:21:46', '2025-10-06 02:21:46'),
(252, 51, '46797679', 'HERNANDEZ FERNANDEZ', 'KEVIN', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-06 02:21:46', '2025-10-06 02:21:46'),
(253, 51, '70549870', 'SAENZ PUMA', 'DESSIRE ALISON', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-06 02:21:46', '2025-10-06 02:21:46'),
(254, 51, '40954728', 'CHECNES GARCIA', 'ROXANA ELIZABETH', 'ESTADISTICO', NULL, 'RED DE SALUD ICA', '2025-10-06 02:21:46', '2025-10-06 02:21:46'),
(255, 51, '21546338', 'AGUADO MOQUILLAZA', 'RAUL RAFAEL', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-06 02:21:46', '2025-10-06 02:21:46'),
(256, 51, '70352284', 'ZAVALA PANOIRA', 'MILAGROS', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-06 02:21:46', '2025-10-06 02:21:46'),
(257, 51, '70114641', 'QUISPE CCAMA', 'KAREN', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-06 02:21:47', '2025-10-06 02:21:47'),
(260, 54, '21418556', 'GARCIA MINAYA', 'CECILIA ERNESTINA', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-06 03:58:31', '2025-10-06 03:58:31'),
(261, 55, '71536326', 'PEÑA PINEDA', 'OLENKA', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-06 04:11:43', '2025-10-06 04:11:43'),
(262, 56, '76437697', 'ANGULO ARCE', 'LESLIE ANTONELLA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-06 16:05:30', '2025-10-06 16:05:30'),
(263, 57, '48187801', 'TATAJE CARPIO', 'ANA YAQUELINE', 'ADMISION', 'Citas', NULL, '2025-10-06 17:55:54', '2025-10-06 17:55:54'),
(293, 62, '43465226', 'PISCONTE DOMINGUEZ', 'ANALI JANELLY', 'ENFERMERA', 'Cred', NULL, '2025-10-07 17:07:45', '2025-10-07 17:07:45'),
(294, 63, '48292674', 'VILLAMARES RAMOS', 'EDWIN JESUS', 'ENFERMERA', 'Cred', NULL, '2025-10-07 17:16:12', '2025-10-07 17:16:12'),
(295, 64, '43590944', 'CASTAÑEDA ALARCON', 'MIRELLA MAVEL', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-09 18:09:03', '2025-10-09 18:09:03'),
(297, 66, '21504642', 'pineda', 'C', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-09 20:18:44', '2025-10-09 20:18:44'),
(298, 67, '21504642', 'PM', 'C', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-09 20:19:35', '2025-10-09 20:19:35'),
(321, 72, '41060794', 'TRILLO NEGRI', 'MARLENY YOLANDA', 'ADMISION', 'Citas', NULL, '2025-10-09 21:28:46', '2025-10-09 21:28:46'),
(322, 72, '08504119', 'JANAMPA DE LA CRUZ', 'MARIA LUZ', 'ADMISION', 'Citas', NULL, '2025-10-09 21:28:46', '2025-10-09 21:28:46'),
(323, 73, '41060794', 'TRILLO NEGRI', 'MARLENY YOLANDA', 'TRIAJE', 'Triaje', NULL, '2025-10-09 21:30:58', '2025-10-09 21:30:58'),
(324, 73, '08504119', 'JANAMPA DE LA CRUZ', 'MARIA LUZ', 'TRIAJE', 'Triaje', NULL, '2025-10-09 21:30:58', '2025-10-09 21:30:58'),
(337, 61, '40212151', 'ONOFRE AVALOS', 'JOSE CARLOS', 'ESTADISTICO', 'Gestión Administrativa', NULL, '2025-10-10 16:05:59', '2025-10-10 16:05:59'),
(350, 82, '75336465', 'ACUÑA VEGA', 'SHEYLA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 01:45:08', '2025-10-11 01:45:08'),
(351, 75, '41464497', 'OYOLA CAHUANA', 'MIGUEL ANGEL', 'JEFE DE ESTADISTICA U.E. PALPA', 'Consulta Externa: Medicina', 'HOSPITAL DE APOYO PALPA', '2025-10-11 01:55:18', '2025-10-11 01:55:18'),
(357, 65, '74061966', 'LOPEZ ALVA', 'AMELIA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 01:57:33', '2025-10-11 01:57:33'),
(358, 45, '21436461', 'HUAMANCOLI TORRES', 'FREDDY RICARDO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 01:57:57', '2025-10-11 01:57:57'),
(359, 38, '21436461', 'HUAMANCOLI TORRES', 'FREDDY RICARDO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 01:58:20', '2025-10-11 01:58:20'),
(360, 27, '41464497', 'OYOLA CAHUANA', 'MIGUEL ANGEL', 'JEFE DE ESTADISTICA U.E. PALPA', 'Cred', 'HOSPITAL DE APOYO PALPA', '2025-10-11 01:59:00', '2025-10-11 01:59:00'),
(361, 13, '40404029', 'SALDAÑA MEDINA', 'CAROLA GIOVANNA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 01:59:36', '2025-10-11 01:59:36'),
(362, 12, '75336465', 'ACUÑA VEGA', 'SHEYLA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 01:59:57', '2025-10-11 01:59:57'),
(363, 8, '71883058', 'DONAYRE SALINAS', 'ROBERTO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 02:00:29', '2025-10-11 02:00:29'),
(364, 7, '40433665', 'TORRES PEÑA', 'ELIZABETH MARIA', 'JEFE DE ESTABLECIMIENTO', 'Gestión Administrativa', NULL, '2025-10-11 02:00:50', '2025-10-11 02:00:50'),
(365, 4, '40441400', 'HUAMAN CORDOVA', 'DANIEL ALDO', 'TECNICO ENF.', 'Citas', NULL, '2025-10-11 02:01:12', '2025-10-11 02:01:12'),
(366, 1, '21562585', 'DONAYRE SALINAS', 'MARIN', 'ENFERMERO', 'Citas', NULL, '2025-10-11 02:01:37', '2025-10-11 02:01:37'),
(373, 84, '72525437', 'REBATTA ZEGARRA', 'ALEXANDRA JANET', 'MEDICO', 'Gestión Administrativa', NULL, '2025-10-11 15:33:44', '2025-10-11 15:33:44'),
(374, 84, '75092578', 'MARTINEZ PEÑA', 'ALEJANDRA YSABEL', 'OBSTETRA', 'Gestión Administrativa', NULL, '2025-10-11 15:33:44', '2025-10-11 15:33:44'),
(375, 83, '72079522', 'YUPANQUI ZAMORA', 'ROCIO ELIZABETH', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 15:59:23', '2025-10-11 15:59:23'),
(376, 85, '76665988', 'MURRIETA QUINTEROS', 'CROBER', 'SOPORTE INFORMATICO', 'Gestión Administrativa', NULL, '2025-10-11 17:46:52', '2025-10-11 17:46:52'),
(377, 86, '47840719', 'MORON BERNAOLA', 'JORGE LUIS', 'SOPORTE INFORMATICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 18:31:24', '2025-10-11 18:31:24'),
(380, 88, '72525437', 'REBATTA ZEGARRA', 'ALEXANDRA JANET', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-11 19:20:06', '2025-10-11 19:20:06'),
(381, 87, '72525437', 'REBATTA ZEGARRA', 'ALEXANDRA JANET', 'MEDICO', 'Triaje', NULL, '2025-10-11 19:20:32', '2025-10-11 19:20:32'),
(382, 87, '75092578', 'MARTINEZ PEÑA', 'ALEJANDRA YSABEL', 'OBSTETRA', 'Triaje', NULL, '2025-10-11 19:20:32', '2025-10-11 19:20:32'),
(387, 53, '70359591', 'VASQUEZ VIDALON', 'NIKOLL ISABEL', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 00:22:09', '2025-10-12 00:22:09'),
(388, 53, '21571287', 'ORE YANAYAYE', 'ANAMELBA', 'ESTADISTICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 00:22:09', '2025-10-12 00:22:09'),
(391, 58, '48292674', 'CORDOVA BARRANTES', 'SHEYLA JAZMIN', 'BIOLOGA', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 00:34:19', '2025-10-12 00:34:19'),
(392, 58, '46410373', 'RUA TITO', 'FLOR NATHALY', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 00:34:19', '2025-10-12 00:34:19'),
(393, 52, '21447944', 'CARRION SALAZAR', 'CARMEN DEL ROSARIO', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 00:46:30', '2025-10-12 00:46:30'),
(394, 52, '45236427', 'CHANGO GARCIA', 'LUIS ENRIQUE', 'INTERNO - MEDICINAME', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 00:46:30', '2025-10-12 00:46:30'),
(395, 59, '29715551', 'TAIRO MENDOZA', 'KATHERINE ZULLY', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 00:57:23', '2025-10-12 00:57:23'),
(396, 59, '41008792', 'CLEMENTE MORALES', 'KARINA TANIA', 'ESTADISTICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 00:57:23', '2025-10-12 00:57:23'),
(397, 68, '21546014', 'VELASQUEZ DE LA ROCA', 'CARMEN ROSA', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 01:20:34', '2025-10-12 01:20:34'),
(398, 68, '41722448', 'PORTUGAL MENDOZA', 'RIVELINO SAUL', 'ESTADISTICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 01:20:34', '2025-10-12 01:20:34'),
(407, 69, '21520465', 'CAYAMPE CHOQUEHUANCA', 'EFRAIN', 'ESTADISTICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 01:57:26', '2025-10-12 01:57:26'),
(408, 69, '21545789', 'OLIVERA RAMOS', 'LUIS CARLOS', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 01:57:26', '2025-10-12 01:57:26'),
(409, 69, '76212079', 'MORON ORMEÑO', 'EDWARD ADRIAN', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 01:57:26', '2025-10-12 01:57:26'),
(410, 69, '21423684', 'PUZA MENDOZA', 'ROSANA AMPARO', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 01:57:26', '2025-10-12 01:57:26'),
(412, 71, '21548656', 'ECHEGARAY BERNAOLA', 'PERCY HUBER', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 02:59:15', '2025-10-12 02:59:15'),
(413, 71, '41876685', 'CHECNES GUTIERREZ', 'DEYSI', 'OBSTETRA', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 02:59:15', '2025-10-12 02:59:15'),
(414, 71, '21532919', 'CALDERON MARTINEZ', 'CARLOS ALBERTO', 'JEFE', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 02:59:15', '2025-10-12 02:59:15'),
(415, 71, '40321281', 'MORON MENDOZA', 'SILVIA KARINA', 'TEC ENFERMERIA', 'Triaje', 'RED DE SALUD ICA', '2025-10-12 02:59:15', '2025-10-12 02:59:15'),
(416, 71, '25682508', 'CABRERA PALIZA', 'SUSANA MIRIAN', 'TEC ENFERMERIA', 'Triaje', 'RED DE SALUD ICA', '2025-10-12 02:59:15', '2025-10-12 02:59:15'),
(417, 76, '73039179', 'SALINAS LAOS', 'ARANTZA', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 03:32:20', '2025-10-12 03:32:20'),
(420, 70, '74860947', 'MAGALLANES CABRERA', 'DAVID MICHELL', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 04:00:39', '2025-10-12 04:00:39'),
(421, 77, '71203104', 'SERIDA RAMIREZ', 'GABRIEL', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 04:04:14', '2025-10-12 04:04:14'),
(422, 77, '21505284', 'MARTINEZ REBATTA', 'KARINA', 'ESTADISTICO', 'Gestión Administrativa', 'RED DE SALUD ICA', '2025-10-12 04:04:14', '2025-10-12 04:04:14'),
(425, 78, '22319140', 'AURIS HERNANDEZ', 'EDGAR ANTONIO', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 04:30:42', '2025-10-12 04:30:42'),
(426, 78, '21504760', 'GARCIA MORAN', 'CARLOS', 'ESTADISTICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 04:30:42', '2025-10-12 04:30:42'),
(429, 79, '40432213', 'CABRERA CALDERON', 'IVAN FRANCISCO', 'ESTADISTICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 04:54:58', '2025-10-12 04:54:58'),
(430, 79, '21562459', 'SALAS CAHUA', 'SONIA FABIOLA', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 04:54:58', '2025-10-12 04:54:58'),
(431, 80, '22194238', 'HERNANDEZ ALVITES', 'YRENE', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 05:07:53', '2025-10-12 05:07:53'),
(432, 81, '42031905', 'COTITO VELARDE', 'NILS MANUEL', 'MEDICO', 'Consulta Externa: Medicina', 'RED DE SALUD ICA', '2025-10-12 05:27:06', '2025-10-12 05:27:06'),
(433, 89, '21447944', 'CARRION SALAZAR', 'CARMEN DEL ROSARIO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 02:48:47', '2025-10-13 02:48:47'),
(434, 89, '45236427', 'CHANGO GARCIA', 'LUIS ENRIQUE', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 02:48:47', '2025-10-13 02:48:47'),
(435, 90, '70359591', 'VASQUEZ VIDALON', 'NIKOLL ISABEL', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 02:50:59', '2025-10-13 02:50:59'),
(436, 90, '21571287', 'ORE YANAYAYE', 'ANAMELBA', 'ESTADISTICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 02:50:59', '2025-10-13 02:50:59'),
(437, 91, '48292674', 'CORDOVA BARRANTES', 'SHEYLA JAZMIN', 'BIOLOGA', 'Consulta Externa: Medicina', NULL, '2025-10-13 02:53:19', '2025-10-13 02:53:19'),
(438, 91, '46410373', 'RUA TITO', 'FLOR NATHALY', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 02:53:19', '2025-10-13 02:53:19'),
(439, 92, '29715551', 'TAIRO MENDOZA', 'KATHERINE ZULLY', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 02:55:58', '2025-10-13 02:55:58'),
(440, 92, '41008792', 'CLEMENTE MORALES', 'KARINA TNIA', 'ESTADISTICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 02:55:58', '2025-10-13 02:55:58'),
(457, 93, '21546014', 'VELASQUEZ DE LA ROCA', 'CARMEN ROSA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:02:49', '2025-10-13 03:02:49'),
(458, 93, '41722448', 'PORTUGAL MENDOZA', 'RIVELINO SAUL', 'ESTADISTICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:02:49', '2025-10-13 03:02:49'),
(459, 94, '21520465', 'CAYAMPE CHOQUEHUANCA', 'EFRAIN', 'ESTADISTICO', 'Gestión Administrativa', NULL, '2025-10-13 03:06:41', '2025-10-13 03:06:41'),
(460, 94, '21545789', 'OLIVERA RAMOS', 'LUIS CARLOS', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:06:41', '2025-10-13 03:06:41'),
(461, 94, '76212079', 'MORON ORMEÑO', 'EDWARD ADRIAN', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:06:41', '2025-10-13 03:06:41'),
(462, 94, '21423684', 'PUZA MENDOZA', 'ROSANA AMPARO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:06:41', '2025-10-13 03:06:41'),
(463, 95, '74860947', 'MAGALLANES CABRERA', 'DAVID MICHELL', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:09:21', '2025-10-13 03:09:21'),
(464, 96, '21548656', 'ECHEGARAY BERNAOLA', 'PERCY HUBER', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:12:43', '2025-10-13 03:12:43'),
(465, 96, '41876685', 'CHECNES GUTIERREZ', 'DEYSI', 'OBSTETRA', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:12:43', '2025-10-13 03:12:43'),
(466, 96, '21532919', 'CALDERON MARTINEZ', 'CARLOS ALBERTO', 'JEFE', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:12:43', '2025-10-13 03:12:43'),
(467, 96, '40321281', 'MORON MENDOZA', 'SILVIA KARINA', 'TEC. ENFERMERIA', 'Triaje', NULL, '2025-10-13 03:12:43', '2025-10-13 03:12:43'),
(468, 96, '25682508', 'CABRERA PALIZA', 'SUSANA MIRIAN', 'TEC. ENFERMERIA', 'Triaje', NULL, '2025-10-13 03:12:43', '2025-10-13 03:12:43'),
(469, 97, '73039179', 'SALINAS LAOS', 'ARANTZA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:14:31', '2025-10-13 03:14:31'),
(470, 98, '71203104', 'SERIDA RAMIREZ', 'GABRIEL', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:16:01', '2025-10-13 03:16:01'),
(471, 98, '21505284', 'MARTINEZ REBATTA', 'KARINA', 'ESTADISTICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:16:01', '2025-10-13 03:16:01'),
(472, 99, '22319140', 'AURIS HERNANDEZ', 'EDGAR ANTONIO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:21:02', '2025-10-13 03:21:02'),
(473, 99, '21504760', 'GARCIA MORAN', 'CARLOS', 'ESTADISTICO', 'Gestión Administrativa', NULL, '2025-10-13 03:21:02', '2025-10-13 03:21:02'),
(474, 100, '40432213', 'CABRERA CALDERON', 'IVAN FRANCISCO', 'ESTADISTICO', 'Gestión Administrativa', NULL, '2025-10-13 03:22:31', '2025-10-13 03:22:31'),
(475, 100, '21562459', 'SALAS CAHUA', 'SONIA FABIOLA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:22:31', '2025-10-13 03:22:31'),
(476, 101, '22194238', 'HERNANDEZ ALVITES', 'YRENE YRIS', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:24:48', '2025-10-13 03:24:48'),
(477, 102, '42031905', 'COTITO VELARDE', 'NILS MANUEL', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 03:26:00', '2025-10-13 03:26:00'),
(478, 103, '21498355', 'HERNANDEZ MORENO', 'ROSA MARIA', 'GESTION ADMINISTRATIVA', 'Gestión Administrativa', NULL, '2025-10-13 16:55:11', '2025-10-13 16:55:11'),
(479, 104, '77799883', 'VELIZ DEL ARCA', 'ARIANA NICOLLE', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 20:29:03', '2025-10-13 20:29:03'),
(480, 104, '73268890', 'UCEDA AGUILAR', 'YADIRA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 20:29:03', '2025-10-13 20:29:03'),
(481, 105, '21569844', 'BANDA SILVA', 'JIMMY GERALDO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 22:47:05', '2025-10-13 22:47:05'),
(482, 74, '21534440', 'VELASQUEZ CAMPANA', 'ANIBAL WENCESLAO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(483, 74, '70090340', 'ESCRIBA SALCEDO', 'ANGEL MARTIN', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(484, 74, '72079522', 'YUPANQUI ZAMORA', 'ROCIO ELIZABETH', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(485, 74, '46483008', 'ARBIETO CRUZ', 'NIVESA', 'ING. DE SISTEMAS', 'Consulta Externa: Medicina', NULL, '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(486, 74, '21404530', 'VENTURA MENDOZA', 'DORIS NATIVIDAD', 'TEC. ENFERMERIA', 'Triaje', NULL, '2025-10-13 23:33:51', '2025-10-13 23:33:51'),
(488, 106, '72908246', 'LEON GODOY', 'SOLANGE TAIS', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-14 00:39:54', '2025-10-14 00:39:54'),
(510, 113, '21424103', 'CUBA TAQUIRI', 'JUVENCIO', 'TECNICO DE ENFERMERIA', 'Triaje', NULL, '2025-10-14 20:38:36', '2025-10-14 20:38:36'),
(513, 116, '28806793', 'LOPEZ ALDERETE', 'DOMITILA', 'TECNICA DE ENFERMERIA', 'Citas', NULL, '2025-10-14 23:41:37', '2025-10-14 23:41:37'),
(514, 116, '45528095', 'SANTAMARIA CHAQUIRAY', 'AGNES NORA', 'TECNICO DE ENFERMERIA', 'Triaje', NULL, '2025-10-14 23:41:37', '2025-10-14 23:41:37'),
(516, 109, '21531773', 'PARIONA HUAMANI', 'EUSSEBIA TEOLINDA', 'TECNICA DE ENFERMERIA', 'Citas', NULL, '2025-10-15 01:23:15', '2025-10-15 01:23:15'),
(517, 110, '21424103', 'CUBA TAQUIRI', 'JUVENCIO', 'TECNICO DE ENFERMERIA', 'Triaje', NULL, '2025-10-15 01:50:56', '2025-10-15 01:50:56'),
(518, 111, '21410853', 'VELASQUEZ SALCEDO', 'CELIA ISIDORA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-15 02:13:08', '2025-10-15 02:13:08'),
(519, 112, '74298709', 'PACHECO MELENDEZ', 'JESUS FRANCISCO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-15 02:20:29', '2025-10-15 02:20:29'),
(521, 117, '46985834', 'APOLAYA MEZA', 'EVA ROXANA', 'ADMISION', 'Citas', NULL, '2025-10-15 14:43:52', '2025-10-15 14:43:52'),
(523, 119, '40685888', 'REYES HUAMANI', 'LIZETH LUCIA', 'JEFE DE ENFERMERIA', 'Triaje', NULL, '2025-10-15 16:27:04', '2025-10-15 16:27:04'),
(524, 120, '41854411', 'CAMPOS BARCENA', 'ERNESTO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-15 16:53:24', '2025-10-15 16:53:24'),
(525, 121, '72724890', 'VARGAS CAJO', 'KENYI PABLO JESÚS', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-15 17:20:30', '2025-10-15 17:20:30'),
(526, 122, '22275446', 'DE LA CRUZ URIBE', 'SYLVIA ELOISA', 'ESTADISTICA', 'Gestión Administrativa', NULL, '2025-10-15 20:14:02', '2025-10-15 20:14:02'),
(527, 123, '22275446', 'DE LA CRUZ URIBE', 'SYLVIA ELOISA', 'ESTADISTICA', 'Citas', NULL, '2025-10-15 20:46:23', '2025-10-15 20:46:23'),
(528, 124, '21532239', 'MENESES VICENCIO', 'LEONEL', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-16 00:42:14', '2025-10-16 00:42:14'),
(529, 125, '41995189', 'QUISPE JANAMPA', 'ANGELICA', 'TECNICA DE ENFERMERIA', 'Gestión Administrativa', NULL, '2025-10-16 00:53:44', '2025-10-16 00:53:44'),
(530, 108, '21531773', 'PARIONA HUAMANI', 'EUSEBIA TEOLINDA', 'TECNICA DE ENFERMERIA', 'Citas', NULL, '2025-10-16 03:07:18', '2025-10-16 03:07:18'),
(531, 114, '21410853', 'VELASQUEZ SALCEDO', 'CELIA ISIDORA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-16 03:12:19', '2025-10-16 03:12:19'),
(532, 115, '74298709', 'PACHECO MELENDEZ', 'JESUS FRANCISCO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-16 03:15:22', '2025-10-16 03:15:22'),
(548, 134, '21418556', 'GARCIA MINAYA', 'CECILIA ERNESTINA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-17 15:47:36', '2025-10-17 15:47:36'),
(549, 135, '70197029', 'IZQUIERDO CASTRO', 'ANAIS', 'ADMISION', 'Citas', NULL, '2025-10-17 15:58:38', '2025-10-17 15:58:38'),
(550, 135, '42199402', 'MARTINEZ QUISPE', 'JENNY', 'ADMISION', 'Citas', NULL, '2025-10-17 15:58:38', '2025-10-17 15:58:38'),
(551, 136, '70197029', 'IZQUIERDO CASTRO', 'ANAIS', 'ADMISION', 'Citas', NULL, '2025-10-17 15:59:40', '2025-10-17 15:59:40'),
(552, 136, '42199402', 'MARTINEZ QUISPE', 'JENNY', 'ADMISION', 'Citas', NULL, '2025-10-17 15:59:40', '2025-10-17 15:59:40'),
(553, 126, '70394520', 'ROJAS VASQUEZ', 'MIRIAM LUZ', 'TECNICA DE ENFERMERIA', 'Citas', NULL, '2025-10-17 16:56:16', '2025-10-17 16:56:16'),
(554, 126, '41274042', 'RAYMONDI VELASQUEZ', 'ARACELLI NOEMI', 'TECNICO DE ENFERMERIA', 'Citas', NULL, '2025-10-17 16:56:16', '2025-10-17 16:56:16'),
(555, 126, '21505277', 'MACHADO QUINCHO', 'PATRICIA FRANCISCA', 'TECNICO DE ENFERMERIA', 'Citas', NULL, '2025-10-17 16:56:16', '2025-10-17 16:56:16'),
(556, 127, '28806308', 'TAIPE AZURZA', 'JESUS', 'TECNICA DE ENFERMERIA', 'Triaje', NULL, '2025-10-17 16:56:44', '2025-10-17 16:56:44'),
(557, 127, '70057718', 'CABANO AMADO', 'ROSARIO', 'TECNICO DE ENFERMERIA', 'Triaje', NULL, '2025-10-17 16:56:44', '2025-10-17 16:56:44'),
(558, 128, '40538567', 'SUICA GONZALES', 'BENEDICTA MODESTA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-17 16:57:41', '2025-10-17 16:57:41'),
(559, 129, '41274042', 'RAYMONDI VELASQUEZ', 'ARACELLI NOEMI', 'TECNICA DE ENFERMERIA', 'Gestión Administrativa', NULL, '2025-10-17 16:59:54', '2025-10-17 16:59:54'),
(562, 137, '22319140', 'AURIS HERNANDEZ', 'EDGAR ANTONIO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-20 00:25:47', '2025-10-20 00:25:47'),
(572, 130, '41274042', 'RAYMONDI VELASQUEZ', 'ARACELLI NOEMI', 'TECNICA DE ENFERMERIA', 'Gestión Administrativa', NULL, '2025-10-20 01:41:47', '2025-10-20 01:41:47'),
(576, 131, '70394520', 'ROJAS VASQUEZ', 'MIRIAM LUZ', 'TECNICA DE ENFERMERIA', 'Citas', NULL, '2025-10-20 01:56:14', '2025-10-20 01:56:14'),
(577, 131, '41274042', 'RAYMONDI VELASQUEZ', 'ARACELLI NOEMI', 'TECNICA DE ENFERMERIA', 'Citas', NULL, '2025-10-20 01:56:14', '2025-10-20 01:56:14'),
(578, 131, '21505277', 'MACHADO QUINCHO', 'PATRICIA FRANCISCA', 'TECNICA DE ENFERMERIA', 'Citas', NULL, '2025-10-20 01:56:14', '2025-10-20 01:56:14'),
(579, 132, '28806308', 'TAIPE AZURZA', 'JESUS', 'TECNICO DE ENFERMERIA', 'Triaje', NULL, '2025-10-20 04:01:02', '2025-10-20 04:01:02'),
(580, 132, '70057718', 'CABANO AMADO', 'ROSARIO', 'TECNICO DE ENFERMERIA', 'Triaje', NULL, '2025-10-20 04:01:02', '2025-10-20 04:01:02'),
(582, 133, '40538567', 'SUICA GONZALES', 'BENEDICTA MODESTA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-20 04:48:50', '2025-10-20 04:48:50'),
(583, 138, '21505284', 'MARTINEZ REBATTA', 'KARINA', 'ESTADISTICO', 'Gestión Administrativa', NULL, '2025-10-21 14:26:59', '2025-10-21 14:26:59'),
(596, 60, '21868214', 'HIDALGO YATACO', 'WILLIAM DAVID', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-22 17:19:23', '2025-10-22 17:19:23'),
(597, 141, '21885643', 'YATACO RAMOS', 'DIANA MAGALY', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-22 22:09:19', '2025-10-22 22:09:19'),
(598, 142, '71754646', 'HUAHUATICO MONSERRATE', 'GRECIA CELESTE', 'ADMINISTRATIVO', 'Gestión Administrativa', NULL, '2025-10-22 22:21:54', '2025-10-22 22:21:54'),
(600, 144, '78716566', 'HERTRAMPF VICENTE', 'RICARDO ALBERTO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-22 22:35:55', '2025-10-22 22:35:55'),
(602, 146, '40212151', 'ONOFRE AVALOS', 'JOSE CARLOS', 'ESTADISTICO', 'Gestión Administrativa', NULL, '2025-10-22 22:45:55', '2025-10-22 22:45:55'),
(603, 139, '45854893', 'ALAVE PAREDES', 'DIANA ROSA', 'TEC. ENFERMERIA', 'Citas', NULL, '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(604, 139, '46836706', 'CASO SALVA', 'CINTHIA', 'TEC. ENFERMERIA', 'Triaje', NULL, '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(605, 139, '70002704', 'VIVANCO GOMEZ', 'SHESKA ESTEFANNY', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(606, 139, '45575234', 'MEDINA PURCA', 'JOSE NILSON', 'ENFERMERO', 'Cred', NULL, '2025-10-23 14:17:57', '2025-10-23 14:17:57'),
(609, 148, '76691124', 'PEREZ RAMIREZ', 'AMPARO YOMIRA', 'CITAS', 'Citas', NULL, '2025-10-23 16:51:15', '2025-10-23 16:51:15'),
(610, 149, '76691124', 'PEREZ RAMIREZ', 'AMPARO YOMIRA', 'TRIAJE', 'Triaje', NULL, '2025-10-23 16:54:43', '2025-10-23 16:54:43'),
(611, 140, '40912816', 'QUELCA NAVARRETE', 'NILTON', 'TEC. ENFERMERIA', 'Gestión Administrativa', NULL, '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(612, 140, '41035610', 'SARMIENTO QUISPE', 'MARIA ROSALYNN', 'LIC. ENFERMERIA', 'Citas', NULL, '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(613, 140, '41035610', 'SARMIENTO QUISPE', 'MARIA ROSALYNN', 'LIC. ENFERMERIA', 'Triaje', NULL, '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(614, 140, '71477639', 'ATAHUA MUNARES', 'JAIRO ESTHEFANO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-23 17:02:06', '2025-10-23 17:02:06'),
(616, 151, '22304767', 'ROMAN SALAS', 'REGINA MAGDALENA', 'TEC. ENFERMERIA', 'Citas', NULL, '2025-10-24 21:44:22', '2025-10-24 21:44:22'),
(617, 150, '21526886', 'FUENTES TANG', 'WALTER MILTON', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-24 21:51:04', '2025-10-24 21:51:04'),
(620, 153, '41329379', 'NOA ESPINO', 'JANETT', 'TRIAJE', 'Triaje', NULL, '2025-10-27 21:33:47', '2025-10-27 21:33:47'),
(621, 152, '22301498', 'HUARANCA', 'ROJAS', 'NANCY BEATRIZ', 'Citas', NULL, '2025-10-27 21:34:38', '2025-10-27 21:34:38'),
(622, 152, '22313228', 'SAENZ', 'FUENTES', 'LUIS OMAR', 'Citas', NULL, '2025-10-27 21:34:38', '2025-10-27 21:34:38'),
(623, 154, '21528079', 'ALGONIER FELIPA', 'OSCAR MARTIN', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-27 21:39:13', '2025-10-27 21:39:13'),
(624, 155, '45232880', 'TORRES SEBASTIAN', 'JULIO  CESAR', 'JEFE DE PERSONAL', 'Gestión Administrativa', NULL, '2025-10-28 16:30:31', '2025-10-28 16:30:31'),
(625, 143, '003130917', 'RINCONES VARELA', 'JESUS EDUARDO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-28 16:45:27', '2025-10-28 16:45:27'),
(627, 145, '46671810', 'AVALOS MELO', 'DEICY EDITH', 'ADMINISTRATIVO', 'Citas', NULL, '2025-10-28 16:50:00', '2025-10-28 16:50:00'),
(629, 156, '42408412', 'FERNANDEZ AYBAR', 'GUILLERMO FRANS', 'JEFE DE PERSONAL', 'Gestión Administrativa', NULL, '2025-10-28 23:13:23', '2025-10-28 23:13:23'),
(631, 157, '73207259', 'ESPINO VIVANCO', 'STEFANY FABIOLA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-29 00:23:38', '2025-10-29 00:23:38'),
(634, 158, '70280474', 'REYES BRICEÑO', 'MARJORIE SAID', 'SIHCE - GESTION ADMINISTRATIVA Y CITAS', 'Gestión Administrativa', NULL, '2025-10-29 20:51:29', '2025-10-29 20:51:29'),
(636, 159, '73078674', 'GARIBAY QUIJAITE', 'NELLY MARIA', 'ENFERMERA', 'Triaje', NULL, '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(637, 159, '7198025', 'ESPINOZA ECHEGARAY', 'SANDRA KAROLINA', 'ENFERMERA', 'Triaje', NULL, '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(638, 159, '41003313', 'MENDOZA LEGUA', 'ZOILA MARITZA', 'TECNICA', 'Citas', NULL, '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(639, 159, '21547833', 'ALMORA PEREZ', 'LUCIA MERCEDES', 'TEC. ENFERMERIA', 'Citas', NULL, '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(640, 159, '41978731', 'HUAMANI PALOMINO', 'DEYSI LIZBETH', 'ENFERMERA', 'Triaje', NULL, '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(641, 159, '21576960', 'ACHAMIZO DE LA CRUZ', 'LUIS JAVIER', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(642, 159, '42111649', 'HUARANCCA GAVILAN', 'MERY LISET', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(643, 159, '21410152', 'ZEVALLOS TORRES', 'PEDRO PABLO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(644, 159, '21535606', 'PECHO CHAVEZ', 'DANY ELOIZA', 'TECNICA EN LABORATORIO', 'Gestión Administrativa', NULL, '2025-10-29 21:02:44', '2025-10-29 21:02:44'),
(645, 147, '45978508', 'DIAZ VALDIVIA', 'LUCERO', 'TECNICO DE ENFERMERIA', 'Triaje', NULL, '2025-10-29 21:06:31', '2025-10-29 21:06:31'),
(647, 118, '21551510', 'FLORES SALCEDO', 'ROBERTA', 'TECNICA DE ENFERMERIA', 'Citas', NULL, '2025-10-30 02:09:47', '2025-10-30 02:09:47'),
(650, 160, '46410373', 'RUA TITO', 'FLOR NATHALY', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-10-30 15:54:06', '2025-10-30 15:54:06'),
(652, 161, '75336465', 'ACUÑA VEGA', 'SHEYLA YOMARA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-05 15:39:58', '2025-11-05 15:39:58'),
(653, 162, '21871033', 'HERRERA PASACHE', 'JORGE LUIS', 'ADMINISTRATIVO', 'Gestión Administrativa', NULL, '2025-11-13 23:12:38', '2025-11-13 23:12:38'),
(654, 163, '40212151', 'ONOFRE AVALOS', 'JOSE CARLOS', 'ESTADISTICO', 'Gestión Administrativa', NULL, '2025-11-13 23:52:16', '2025-11-13 23:52:16'),
(655, 164, '21783777', 'LAU CHAN', 'ANITA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-14 00:19:32', '2025-11-14 00:19:32'),
(656, 165, '22184309', 'GAMONAL RAMOS', 'LUZ TEREZA', 'MEDICO', 'Gestión Administrativa', NULL, '2025-11-15 16:04:57', '2025-11-15 16:04:57'),
(658, 166, '72492737X', 'CHANG PEÑA', 'GLADYS BRIGGITH', 'TECNICA DE ENFERMERIA', 'Gestión Administrativa', NULL, '2025-11-18 17:20:21', '2025-11-18 17:20:21'),
(663, 168, '40563145', 'HUAMAN CORDOVA', 'VICTOR ANGEL', 'TECNICO ASISTENCIAL', 'Triaje', NULL, '2025-11-18 18:07:34', '2025-11-18 18:07:34'),
(664, 169, '72492737X', 'CHANG PEÑA', 'GLADYS BRIGGITH', 'TECNICA EN ENFERMERIA', 'Gestión Administrativa', NULL, '2025-11-18 18:11:59', '2025-11-18 18:11:59'),
(666, 170, '21578846', 'LOPEZ GOMEZ', 'ELIZABETH', 'JEFE PS', 'Gestión Administrativa', NULL, '2025-11-18 18:26:16', '2025-11-18 18:26:16'),
(667, 171, '41995189', 'QUISPE JANAMPA', 'ANGELICA', 'TECNICA DE ENFERMERIA', 'Gestión Administrativa', NULL, '2025-11-18 18:33:06', '2025-11-18 18:33:06'),
(668, 172, '21556157', 'PARDO ALEJO', 'ANA CAROLINA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-18 18:41:03', '2025-11-18 18:41:03'),
(669, 172, '40638878', 'VASSALLO QUIROZ', 'GIUSEPPE', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-18 18:41:03', '2025-11-18 18:41:03'),
(670, 173, '74036297', 'MENDOZA MOTTA', 'MANUELA DEL ROSARIO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-18 18:46:47', '2025-11-18 18:46:47'),
(674, 175, '21451214', 'ANGULO LEGUA', 'JOSE ESTEBAN', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-18 23:31:47', '2025-11-18 23:31:47'),
(675, 176, '72525437', 'REBATTA ZEGARRA', 'ALEXANDRA JANET', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-20 16:20:47', '2025-11-20 16:20:47'),
(676, 177, '45276501', 'HUANCA CAMPOS', 'KATHERINE VIRGINIA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-20 22:38:53', '2025-11-20 22:38:53'),
(677, 178, '40441410', 'PEÑA FLORES', 'AUGUSTO ALEXANDER', 'MEDICO', 'Gestión Administrativa', NULL, '2025-11-21 20:36:00', '2025-11-21 20:36:00'),
(679, 179, '42863986', 'URIBE CANALES', 'MARIE ANN', 'MEDICO', 'Gestión Administrativa', NULL, '2025-11-21 21:06:59', '2025-11-21 21:06:59'),
(680, 180, '21519949', 'SOLIS DONAYRE', 'EDITH NOEMI', 'JEFE DE PERSONAL', 'Gestión Administrativa', NULL, '2025-11-25 02:22:43', '2025-11-25 02:22:43'),
(681, 181, '40521874', 'CASAS SOTELO', 'DEIVIS OMAR', 'ESTADISTICO', 'Citas', NULL, '2025-11-25 16:44:23', '2025-11-25 16:44:23'),
(682, 182, '21534440', 'VELASQUEZ CAMPANA', 'ANIBAL WENCESLAO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-26 19:08:54', '2025-11-26 19:08:54'),
(684, 167, '70280474', 'REYES BRICEÑO', 'MARJORIE', 'TECNICA DE ENFERMERIA', 'Gestión Administrativa', NULL, '2025-11-26 21:30:39', '2025-11-26 21:30:39'),
(686, 174, '43023964', 'CCACCYA RUNTAY', 'MARY LUZ', 'TECNICA EN FARMACIA', 'Consulta Externa: Medicina', NULL, '2025-11-26 21:44:21', '2025-11-26 21:44:21'),
(687, 183, '41482346', 'ORE MAYORGA', 'LESLIE PAOLA', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-27 14:31:26', '2025-11-27 14:31:26'),
(688, 184, '21528079', 'ALGONIER FELIPA', 'OSCAR MARTIN', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-11-27 15:56:26', '2025-11-27 15:56:26'),
(690, 185, '40402973', 'GHEZZI TORRES', 'JULISSA FIORELLA', 'ODONTOLOGO', 'Consulta Externa: Odontologia', NULL, '2025-11-27 19:59:28', '2025-11-27 19:59:28'),
(691, 107, '72708935', 'RAMOS HUAMAN', 'LUIS GUILLERMO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-12-01 19:23:32', '2025-12-01 19:23:32'),
(692, 186, '28272855', 'ROJAS DOMINGUEZ', 'HAYDEE', 'ODONTOLOGO', 'Consulta Externa: Odontologia', NULL, '2025-12-01 23:50:40', '2025-12-01 23:50:40'),
(693, 186, '21829355', 'CONISLLA ARCE', 'JENNY YNES', 'ODONTOLOGO', 'Consulta Externa: Odontologia', NULL, '2025-12-01 23:50:40', '2025-12-01 23:50:40'),
(694, 187, '40030869', 'CORREA PEREZ', 'CYNTHIA YACORI', 'GERENTE', 'Gestión Administrativa', NULL, '2025-12-02 15:30:01', '2025-12-02 15:30:01'),
(695, 187, '21505284', 'MARTINEZ REBATTA', 'KARINA FIORELLA', 'ESTADISTICA', 'Gestión Administrativa', NULL, '2025-12-02 15:30:01', '2025-12-02 15:30:01'),
(696, 188, '43421235', 'BAUTISTA CASTRO', 'AMPARO EDITH', 'GESTION', 'Gestión Administrativa', NULL, '2025-12-04 04:04:17', '2025-12-04 04:04:17'),
(697, 189, '46573023', 'PALOMINO CASAVILCA', 'ESTER MARGARITA', 'CITAS', 'Citas', NULL, '2025-12-05 13:45:31', '2025-12-05 13:45:31'),
(698, 190, '46573023', 'PALOMINO CASAVILCA', 'ESTER MARGARITA', 'TRIAJE', 'Triaje', NULL, '2025-12-05 13:49:25', '2025-12-05 13:49:25'),
(703, 191, '21519936', 'SILVA QUISPE', 'LUIS ENRIQUE', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-12-11 17:43:51', '2025-12-11 17:43:51'),
(705, 192, '72420377', 'LOPEZ OLMOS', 'KEREN ESTHER', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-12-11 18:51:14', '2025-12-11 18:51:14'),
(708, 194, '71883058', 'DONAYRE SALINAS', 'ROBERTO', 'MEDICO', 'Consulta Externa: Nutricion', NULL, '2025-12-20 02:02:17', '2025-12-20 02:02:17'),
(712, 196, '71883058', 'ALBUJAR CORNEJO', 'ROSARIO DEL CARMEN', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-12-21 13:40:52', '2025-12-21 13:40:52'),
(713, 195, '71883058', 'DONAYRE SALINAS', 'ROBERTO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-12-21 13:43:59', '2025-12-21 13:43:59'),
(714, 197, '71883058', 'DONAYRE SALINAS', 'ROBERTO', 'MEDICO', 'Consulta Externa: Medicina', NULL, '2025-12-21 13:44:49', '2025-12-21 13:44:49'),
(720, 198, '71883058', 'ALBUJAR CORNEJO', 'ROSARIO DEL CARMEN', 'MEDICO', 'FUA', NULL, '2025-12-22 22:35:56', '2025-12-22 22:35:56'),
(732, 199, '71883058', 'ALBUJAR CORNEJO', 'ROSARIO DEL CARMEN', 'MEDICO', 'FUA', 'DIRESA ICA', '2025-12-26 12:55:47', '2025-12-26 12:55:47'),
(733, 199, '71883059', 'ALBUJAR CORNEJO', 'ROSARIO DEL CARMEN', 'TRIAJE', 'Parto', NULL, '2025-12-26 12:55:47', '2025-12-26 12:55:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `apellido_paterno` varchar(255) DEFAULT NULL,
  `apellido_materno` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(8) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `apellido_paterno`, `apellido_materno`, `name`, `username`, `email`, `email_verified_at`, `password`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '0', '1', 'Administrador', '71883059', 'jordands140995@gmail.com', NULL, '$2y$12$P7uG.W/GZqih9i2PwBvuwuNiCTpLH9pmr7caW3e6fdraP315liWhi', 'admin', 'active', NULL, '2025-12-17 13:55:21', '2025-12-22 17:51:50'),
(2, 'Yañez', 'Medina', 'Lida Graciela', '72762954', 'ligra2096@gmail.com', NULL, '$2y$12$EuK4G5RdBJgiHy2.Vg8uo.YT64XRB5gEI8qkCJQyMldDFop4QH/bS', 'user', 'active', NULL, '2025-12-17 20:42:13', '2025-12-24 20:38:53'),
(3, 'Muñante', 'Medina', 'Ernesto Javier', '70398441', 'ernestojmm97@outlook.com', NULL, '$2y$12$oEuBO8QNUqHjT2FdBRbjUueUL65s2hlWoBWgm8qFNaTYzC59kyxye', 'user', 'active', NULL, '2025-12-20 14:16:18', '2025-12-20 14:16:18'),
(4, 'Melgar', 'Mesias', 'Jairo', '70314306', 'melgarmesiasj@gmail.com', NULL, '$2y$12$bRpnosLilMrLykHKpQuTy.03WXoaxC24TrtBSbelUVC.nxDguzkFy', 'user', 'active', NULL, '2025-12-20 14:17:06', '2025-12-20 14:17:06'),
(5, 'Gutierrez', 'Hilario', 'Juan Carlos', '70073797', 'carlosgutierrezh0@gmail.com', NULL, '$2y$12$cgEdaUGjSfI8/8DJ.bvyG.1L068jxnRB2jwvdnoGYX1qEqllPefry', 'user', 'active', NULL, '2025-12-20 14:17:46', '2025-12-24 20:37:58'),
(6, 'Donayre', 'Salinas', 'Jordan Roberto', '71883058', 'JORDAN_DS14@HOTMAIL.COM', NULL, '$2y$12$xyFYzc1r0SlefVBOFcx2/Ow68M5pYH5E1OymzHor5sY0ZiWHpASdu', 'user', 'active', NULL, '2025-12-22 17:28:36', '2025-12-24 20:51:16');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actas`
--
ALTER TABLE `actas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actas_establecimiento_id_foreign` (`establecimiento_id`),
  ADD KEY `actas_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actividades_acta_id_foreign` (`acta_id`);

--
-- Indices de la tabla `acuerdos`
--
ALTER TABLE `acuerdos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `acuerdos_acta_id_foreign` (`acta_id`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `establecimientos`
--
ALTER TABLE `establecimientos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `establecimientos_codigo_unique` (`codigo`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `monitoreo_detalles`
--
ALTER TABLE `monitoreo_detalles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monitoreo_detalles_acta_id_foreign` (`acta_id`);

--
-- Indices de la tabla `observaciones`
--
ALTER TABLE `observaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `observaciones_acta_id_foreign` (`acta_id`);

--
-- Indices de la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participantes_acta_id_foreign` (`acta_id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actas`
--
ALTER TABLE `actas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=203;

--
-- AUTO_INCREMENT de la tabla `actividades`
--
ALTER TABLE `actividades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=543;

--
-- AUTO_INCREMENT de la tabla `acuerdos`
--
ALTER TABLE `acuerdos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=501;

--
-- AUTO_INCREMENT de la tabla `establecimientos`
--
ALTER TABLE `establecimientos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `monitoreo_detalles`
--
ALTER TABLE `monitoreo_detalles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `observaciones`
--
ALTER TABLE `observaciones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=493;

--
-- AUTO_INCREMENT de la tabla `participantes`
--
ALTER TABLE `participantes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=734;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actas`
--
ALTER TABLE `actas`
  ADD CONSTRAINT `actas_establecimiento_id_foreign` FOREIGN KEY (`establecimiento_id`) REFERENCES `establecimientos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `actas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `actividades`
--
ALTER TABLE `actividades`
  ADD CONSTRAINT `actividades_acta_id_foreign` FOREIGN KEY (`acta_id`) REFERENCES `actas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `acuerdos`
--
ALTER TABLE `acuerdos`
  ADD CONSTRAINT `acuerdos_acta_id_foreign` FOREIGN KEY (`acta_id`) REFERENCES `actas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `monitoreo_detalles`
--
ALTER TABLE `monitoreo_detalles`
  ADD CONSTRAINT `monitoreo_detalles_acta_id_foreign` FOREIGN KEY (`acta_id`) REFERENCES `actas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `observaciones`
--
ALTER TABLE `observaciones`
  ADD CONSTRAINT `observaciones_acta_id_foreign` FOREIGN KEY (`acta_id`) REFERENCES `actas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `participantes`
--
ALTER TABLE `participantes`
  ADD CONSTRAINT `participantes_acta_id_foreign` FOREIGN KEY (`acta_id`) REFERENCES `actas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
