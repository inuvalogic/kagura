--
-- Database Name: `kagura`
--

CREATE TABLE IF NOT EXISTS `config` (
`config_id` int(11) NOT NULL,
  `config_name` varchar(100) NOT NULL,
  `config_value` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

INSERT INTO `config` (`config_id`, `config_name`, `config_value`) VALUES
(1, 'default_width', '300'),
(2, 'default_height', '300'),
(3, 'small_width', '100'),
(4, 'small_height', '150'),
(5, 'medium_width', '300'),
(6, 'medium_height', '480'),
(7, 'big_width', '500'),
(8, 'big_height', '800'),
(9, 'thumb_width', '80'),
(10, 'thumb_height', '80'),
(11, 'gallery_width', '500'),
(12, 'gallery_height', '626'),
(13, 'landscape_width', '500'),
(14, 'landscape_height', '300');

ALTER TABLE `config`
 ADD PRIMARY KEY (`config_id`);

ALTER TABLE `config`
MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=15;