-- MySQL dump 10.14  Distrib 5.5.43-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: bostoncamerata_wordpress
-- ------------------------------------------------------
-- Server version	5.5.43-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ghs_gigpress_artists`
--

DROP TABLE IF EXISTS `ghs_gigpress_artists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ghs_gigpress_artists` (
  `artist_id` int(4) NOT NULL AUTO_INCREMENT,
  `artist_name` varchar(255) NOT NULL,
  `artist_alpha` varchar(255) NOT NULL,
  `artist_url` varchar(255) DEFAULT NULL,
  `artist_order` int(4) DEFAULT '0',
  `program_notes` text,
  PRIMARY KEY (`artist_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ghs_gigpress_artists`
--

LOCK TABLES `ghs_gigpress_artists` WRITE;
/*!40000 ALTER TABLE `ghs_gigpress_artists` DISABLE KEYS */;
INSERT INTO `ghs_gigpress_artists` VALUES (1,'Daniel: A Medieval Masterpiece Revisited','daniel: a medieval masterpiece revisited','/programs/daniel.html',4,'<img src=\'/images/daniel.jpg\' class=\"alignleft\" width=\'30%\'>The themes of justice and of truth spoken to power are once again front and center as the Jewish captive Daniel confronts the tyrannical Belshazzar. The magnificent musical play of Daniel, composed eight centuries ago in Beauvais, France, was newly transcribed from the original manuscript source  and powerfully staged for modern audiences by Anne Azéma; it was premiered in Boston in 2014 to  public and critical acclaim. \r\n<br>\r\nA translation of the play can be <a href=\"/programs//DanielTranslation.html\">viewed here</a> and <a href=\"/download/DanielTranslation.pdf\">downloaded here</a>.');
INSERT INTO `ghs_gigpress_artists` VALUES (2,'An American Christmas','an american christmas','/programs/americanchristmas.html',1,'From the early years of the American republic, and from a wide range of early tune books and manuscripts, a generous selection of carols, New England anthems, Southern folk hymns and religious ballads for the season.');
INSERT INTO `ghs_gigpress_artists` VALUES (3,'Portes du Ciel (Gates of Heaven): Spiritual Songs from Medieval France','portes du ciel (gates of heaven): spiritual songs from medieval france','/programs/HeavensGatePortesDuCiel.html',13,'Close to Reims, the regions of Champagne, Picardy, and Lorraine brought forth an abundant harvest of song in French, both secular and sacred.  The subject of “Heaven\'s Gate” is the magnificent repertoire, composed in both the courtly and popularizing manners, in praise of the Virgin Mary.  Noble songs in the refined trouvère style, narrations in word and song, and dance music with sacred texts, are all included in this production. The prior of Vic-sur-Aisne, Gauthier de Coincy (1177/8-1236), a passionate and prolix musician-poet, recounts the miracles of the Virgin that took place in his parish; Thibault de Champagne (1201-1253), count of Champagne and king of Navarra, praises the Queen of Heaven in the most elegant and subtle style; while anonymous minstrels transform the worldly songs of the day into vigorous, toe-tapping spirituals.');
INSERT INTO `ghs_gigpress_artists` VALUES (4,'Puer Natus Est: A Medieval Christmas','puer natus est: a medieval christmas','',14,'<img src=\'/images/puerNatusEst.jpg\' class=\"alignleft\" width=\'20%\'>A glimpse of Christmas spirituality from Medieval France, Italy, England, and Provence, including music of the church and songs of private devotion around the joyous theme of the Nativity.  Included are songs to the Virgin Mary, processionals from Saint Martial of Limoges, hymns, lyrics, and miracle ballads sung in Latin, Old French, Old Provençal, and Saxon, interlaced with Medieval English texts of the Nativity. Our cast features an extraordinary trio of women\'s voices with harp and vielle.\r\n\r\nAnne Azéma, Camila Parias, Deborah Rentz-Moore, voices; Christa Patton, winds, harp; Jacob Mariani, vielle.');
INSERT INTO `ghs_gigpress_artists` VALUES (5,'Of All the Flowers: Sacred and Secular Song of the Later Middle Ages','of all the flowers: sacred and secular song of the later middle ages','',11,'The constantly evolving and inventive musical minds of Italian and French masters during the fourteenth century has left us with repertoires, both sacred and secular, that successfully unite the search for new and different creative paths with astonishing lyricism and sensual beauty. In this specially commissioned program for the Massachusetts Institute of Technology, you will hear music spanning the worlds of God and Man, by the greatest composers of their day: Machaut, Landini, da Bologna, and others, performed by Camerata\'s virtuoso soloists and instrumentalists.');
INSERT INTO `ghs_gigpress_artists` VALUES (6,'The Sacred Bridge','sacred bridge','/sacredbridge.html',16,'Back by popular demand! An interfaith celebration unlike any other. Discover with us the common musical roots of Judaism, Islam, and Christianity, and the astonishing and beautiful interactions among these traditions. Our program includes elements of Jewish liturgy, Gregorian and Koranic chant, songs and texts of Jewish minstrels, Sephardic folksong, medieval Spanish Cantigas, and Judaeo-Islamic music from the ancient Andalusian tradition.');
INSERT INTO `ghs_gigpress_artists` VALUES (7,'Carmina Burana','carmina burana','/programs/carminaburana.html',2,'Drawing on the original 13th-century manuscript, The Boston Camerata’s Carmina Burana presents a panoramic portrait of student and clerical life in medieval Europe: paeans to the Goddess Fortune, funny and ferocious critiques of Church and State, earnest meditations on truth and righteousness, and a generous serving of songs about drinking, gambling and amorous adventure. With its usual verve and vivacity, the Camerata gives a deepened, in turn exuberant and contemplative reading of this manuscript, under the direction of vocalist Anne Azéma.');
INSERT INTO `ghs_gigpress_artists` VALUES (8,'Highlights from the Medieval Carmina Burana','highlights from the medieval carmina burana','',6,'As a prelude to Carl Orff’s extravagant Carmina Burana, Boston Camerata presents a vivacious portrait of student and clerical life in medieval Europe.');
INSERT INTO `ghs_gigpress_artists` VALUES (9,'Patriots and Heroes: Music of the Young Republic','patriots and heroes: music of the young republic','',12,'Who are our heroes? How did they go to battle and with what songs? Whose side were they on?\r\nA medley of early American music featuring a portrait gallery of eminent Americans, but also high spirited celebrations, of the new nation, and around the quintessential American themes of freedom and independence. This program constitutes a chapter in the Boston 2015 celebrations of the Marquis de Lafayette and the historic return of his rebuilt 18th century \"freedom frigate\", the Hermione.\r\n \r\nDrawing on original print and manuscript sources, we will include songs in celebration of Lafayette\'s friends and associates, Washington and Jefferson, as well as both American and French compositions reflecting the social and political climate of the turbulent years 1775-1830. Liberty, martial glory, loyalty, and a healthy dose of satire and irreverence are all present in the lively ballads and broadsides of that crucial time.');
INSERT INTO `ghs_gigpress_artists` VALUES (10,'Nueva España: Close Encounters in the New World','nueva españa: close encounters in the new world','',10,'Latin American Baroque music at its best! This program calls attention to \"the meeting places of light and beauty that did indeed exist in those terrible, hard centuries\"--the Age of Exploration in the New World. Here we show the fruitful intercultural exchanges that transpired between indigenous American cultures, the Spanish, and the Africans, and indeed, this is beautiful music. Lively and driven at times by the sunny strumming of the baroque guitar and the maracas, tambourine, and claves, at other times stately with the grandeur of voices with organ.');
INSERT INTO `ghs_gigpress_artists` VALUES (11,'A Mediterranean Christmas','a mediterranean christmas','',8,'The Christmas narrative retold using songs, chants, and instrumental pieces from the countries of the Mediterranean basin: Spain, Italy, and southern France, as well as north Africa and the Holy Land.  Works are drawn from medieval manuscripts and more recent, though still archaic, folklore and oral traditions. With voices, early instruments of Europe and the Middle East, and readings of the Christmas story.');
INSERT INTO `ghs_gigpress_artists` VALUES (12,'The Night\'s Tale: A Tournament of Love','night\'s tale: a tournament of love','/programs/legacy-programs/the-nights-tale-a-tournament-of-love/',9,'Another astonishing music-theater production by Artistic Director Anne Azéma. Based on an authentic, colorful narrative of festivity, tournaments, and love games in a medieval French castle, our performance captures the day’s celebrations through song and gesture. Daylight is the domain of men, who joust and fight in ritual encounters, as the women shout encouragement; when night falls, the women converse in music and dance, far from the masculine violence of the daytime. Mutual longing aroused during the day culminates in the evening’s rites, as the sexes come together in courtship, both playful and passionate.');
INSERT INTO `ghs_gigpress_artists` VALUES (13,'The American Vocalist','american vocalist','',0,'Camerata\'s pioneering exploration of folk hymnody in the young Republic includes spiritual songs, hymns, and anthems in a vigorous and authentic homegrown manner. This style, recalling many elements of European early music, grew up in the singing schools of colonial New England, travelled South and West in the 19th century, and continues to live on thanks to a new generation of motivated singers in all parts of the country.');
INSERT INTO `ghs_gigpress_artists` VALUES (14,'City of Fools: Medieval Songs of Rule and Misrule','city of fools: medieval songs of rule and misrule','',3,'<img src=\'/images/cityOfFools.jpg\' class=\"alignleft\" width=\'30%\'>Shortly before an important American election, this new program of songs and poems from the Middle Ages evokes the age-old themes of justice and corruption in the public sphere. Minstrel songs from medieval France, Provençe, and Germany, amazingly contemporary in their language, provide an amusing and sharply-etched perspective on our current  travails. Includes pungent selections from the Play of Daniel, Carmina Burana, and Roman de Fauvel; works by gifted musican-poets Philippe le Chancelier, Bertran de Born, and Thibault de Champagne; and a very American ending.');
INSERT INTO `ghs_gigpress_artists` VALUES (16,'In Dulci Jubilo: A German Christmas','in dulci jubilo: a german christmas','',7,'<img src=\'/images/inDulceJublio.jpg\' class=\"alignleft\" width=\'30%\'>In the European North, the forests are deep; the nights are dark and long. Perhaps this is why, in reaction, the early Christmas music of the German-speaking peoples is so intensely joyful, so profoundly rich. Our program explores the marvelous music of German Christmas festivity through chants and chorales, simple carols, grandiose polyphony, and instrumental fantasias of the 15th to early 17th centuries.');
INSERT INTO `ghs_gigpress_artists` VALUES (17,'Treasures of Devotion: Spiritual Song in Northern Europe 1500-1540','treasures of devotion: spiritual song in northern europe 1500-1540','',17,'<img src=\'/images/treasuresOfDevotion.jpg\' class=\"alignleft\" width=\'30%\'>\r\nMusic of personal devotion in the early Renaissance reflects the spirituality of homes and small chapels in an age of intense religious renewal. Prayers, songs, and chants accompany music for the Virgin, meditations on the cross, and astonishing reworkings of the day\'s popular melodies set to sacred texts.\r\n<p>\r\n<div class=\"aligncenter\">\r\nAnne Azéma, voice, hurdy gurdy<br>Michael Barrett, voice, lute<br>Daniel Hershey, voice<br>Joel Frederiksen, voice, lute<br>Andrew Arceci, viola da gamba<br>Shira Kammen, vielle, harp<br>Carol Lewis, viola da gamba</div>');
INSERT INTO `ghs_gigpress_artists` VALUES (18,'Roman de Fauvel','roman de fauvel','',15,NULL);
INSERT INTO `ghs_gigpress_artists` VALUES (19,'Dante Festival at the Gardner','dante festival at the gardner','',5,NULL);
INSERT INTO `ghs_gigpress_artists` VALUES (21,'Tristan & Iseult: A Medieval Romance in Poetry and Music','tristan & iseult: a medieval romance in poetry and music','/tristanandiseault.html',0,'<img class=\"alignleft\" width=\"30%\" src=\'/covers/tristan.jpg\'>Camerata\'s most honored production of recent seasons was originally conceived as a recording project. At the request of Erato records, intense literary and musical research took place during winter and spring 1987. The recording sessions were held in September, 1987 at the Church of the Covenant, Boston.');
INSERT INTO `ghs_gigpress_artists` VALUES (20,'Liberty and Love: A Summer Sampler','liberty and love: a summer sampler','',0,'<img class=\"alignleft\" width=\"30%\" src=\"/images/patriotsHeroes.jpg\">We offer a summer buffet of music for our Maine friends, plus a sneak preview of our 2017-18 season! An opening set of love songs, chants, and spirituals from medieval France is followed by a feast of home-cooked ballads, Revolutionary partsongs, and paeans to American heroes, including pieces from the earliest Maine songbooks. \r\n<p>\r\n<div class=\"aligncenter\">\r\nAnne Azéma, director, voice<br>Michael Barrett, Lawson Daves, Daniel Hershey, Camila Parias, Deborah Rentz-Moore, voices<br>Joel Cohen, voice, guitar</div>');
/*!40000 ALTER TABLE `ghs_gigpress_artists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-01 10:28:34
