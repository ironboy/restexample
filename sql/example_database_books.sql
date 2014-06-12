# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Värd: 127.0.0.1 (MySQL 5.1.44)
# Databas: books
# Genereringstid: 2013-11-21 07:42:26 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Tabelldump booktitles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `booktitles`;

CREATE TABLE `booktitles` (
  `isbn` varchar(13) NOT NULL DEFAULT '',
  `title` text,
  `author` text,
  PRIMARY KEY (`isbn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `booktitles` WRITE;
/*!40000 ALTER TABLE `booktitles` DISABLE KEYS */;

INSERT INTO `booktitles` (`isbn`, `title`, `author`)
VALUES
  ('9780816679638','Humanesis','David Cecchetto'),
  ('9780262519571','The Power of Narrative in Environmental Networks','Raul Lejano'),
  ('9781597097161','The Palace of Contemplating Departure','Brynn Saito'),
  ('9780299293406','Scattered','Diana Howansky Reilly'),
  ('9780268020408','The Way','Antoine Arjakovsky'),
  ('9781611172553','Understanding Marcel Proust','Allen Thiher'),
  ('9780807152317','The Martyrdom of Abolitionist Charles Torrey','E. Fuller Torrey'),
  ('9780816680382','George Cukor','Patrick McGilligan'),
  ('9780253010001','A Phenomenology of Christian Life','Felix Murchadha'),
  ('9780819573452','End of the Line','Markham Starr'),
  ('9789058679079','Handheld XRF for Art and Archaeology','Aaron N. Shugar, Jennifer L. Mass'),
  ('9780520275126','Killer Tapes and Shattered Screens','Caetlin Benson-Allott'),
  ('9780813560717','Zapotecs on the Move','Adriana Cruz-Manjarrez'),
  ('9780810152335','The World Is Round','Nikky Finney'),
  ('9780817317959','Frank Norris Remembered','Edited by Jesse S. Crisler and Joseph R. McElrath Jr.'),
  ('9780520273528','Bone Histology of Fossil Tetrapods','Kevin Padian'),
  ('9780822353973','Centering Animals in Latin American History','edited by Martha Few and Zeb Tortorici'),
  ('9781611860702','In the Kingdom of the Ditch','Todd Davis'),
  ('9780823254200','Committing the Future to Memory','Sarah Clift'),
  ('9781469607580','Trials of Laura Fair: Sex, Murder, and Insanity in the Victorian West','Carole Haber'),
  ('9780253008824','Plato on the Limits of Human Life','Sara Brill'),
  ('9781623490263','Identifying and Interpreting Animal Bones','April M. Beisaw'),
  ('9781623490133','When Things Went Right','Chase Untermeyer'),
  ('9781621039693','Chester Brown','Dominick Grace'),
  ('9780816681402','Degraded works','Marc Doussard'),
  ('9780813935126','The Poetics of Ethnography in Martinican Narratives','Christina Kullberg'),
  ('9781611172850','Palmetto Profiles','Paul Taylor'),
  ('9780814336038','Old Slow Town','Paul Taylor'),
  ('9781623490072','I Cannot Forget','Judith Fenner Gentry'),
  ('9781574415261','Tracking the Texas Rangers','Edited by Bruce A. Glasrud and Harold J. Weiss, Jr.'),
  ('9781617037740','Kathryn Bigelow','Peter Keough'),
  ('9780814745311','How To Watch Television','Ethan Thompson'),
  ('9780813144269','I Cannot Forget','Christina Rice'),
  ('9780262019156','The Ringtone Dialectic','Sumanth Gopinath'),
  ('9780520273870','New Orleans Suite','Lewis Watts'),
  ('9780253008145','Veiling in Africa','Edited by Elisha P. Renne');

/*!40000 ALTER TABLE `booktitles` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
