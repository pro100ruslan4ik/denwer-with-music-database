
--
-- Data for table `artist`
--

INSERT INTO `artist` (`ArtistId`, `Name`) VALUES
(1, 'The Beatles'),
(2, 'Pink Floyd'),
(3, 'Led Zeppelin'),
(4, 'Queen'),
(5, 'The Rolling Stones'),
(6, 'AC/DC'),
(7, 'Metallica'),
(8, 'Nirvana'),
(9, 'Bob Dylan'),
(10, 'Elvis Presley'),
(11, 'Michael Jackson'),
(12, 'Madonna'),
(13, 'U2'),
(14, 'Radiohead'),
(15, 'The Who');

--
-- Data for table `genre`
--

INSERT INTO `genre` (`GenreId`, `Name`) VALUES
(1, 'Rock'),
(2, 'Pop'),
(3, 'Metal'),
(4, 'Alternative'),
(5, 'Classic Rock'),
(6, 'Progressive Rock'),
(7, 'Hard Rock'),
(8, 'Folk'),
(9, 'Blues'),
(10, 'Grunge');

--
-- Data for table `composer`
--

INSERT INTO `composer` (`ComposerId`, `Name`) VALUES
(1, 'John Lennon'),
(2, 'Paul McCartney'),
(3, 'George Harrison'),
(4, 'Roger Waters'),
(5, 'David Gilmour'),
(6, 'Jimmy Page'),
(7, 'Robert Plant'),
(8, 'Freddie Mercury'),
(9, 'Brian May'),
(10, 'Mick Jagger'),
(11, 'Keith Richards'),
(12, 'Angus Young'),
(13, 'Malcolm Young'),
(14, 'James Hetfield'),
(15, 'Lars Ulrich'),
(16, 'Kurt Cobain'),
(17, 'Bob Dylan'),
(18, 'Elvis Presley'),
(19, 'Michael Jackson'),
(20, 'Madonna'),
(21, 'Bono'),
(22, 'The Edge'),
(23, 'Thom Yorke'),
(24, 'Jonny Greenwood'),
(25, 'Pete Townshend');

--
-- Data for table `mediatype`
--

INSERT INTO `mediatype` (`MediaTypeId`, `Name`) VALUES
(1, 'MPEG audio file'),
(2, 'Protected AAC audio file'),
(3, 'Protected MPEG-4 video file'),
(4, 'Purchased AAC audio file'),
(5, 'AAC audio file');

--
-- Data for table `album`
--

INSERT INTO `album` (`AlbumId`, `Title`, `ArtistId`) VALUES
(1, 'Abbey Road', 1),
(2, 'Sgt. Pepper\'s Lonely Hearts Club Band', 1),
(3, 'The Dark Side of the Moon', 2),
(4, 'The Wall', 2),
(5, 'Led Zeppelin IV', 3),
(6, 'Physical Graffiti', 3),
(7, 'A Night at the Opera', 4),
(8, 'News of the World', 4),
(9, 'Let It Bleed', 5),
(10, 'Sticky Fingers', 5),
(11, 'Back in Black', 6),
(12, 'Highway to Hell', 6),
(13, 'Master of Puppets', 7),
(14, 'Metallica (Black Album)', 7),
(15, 'Nevermind', 8),
(16, 'In Utero', 8),
(17, 'Highway 61 Revisited', 9),
(18, 'Blonde on Blonde', 9),
(19, 'Elvis Presley', 10),
(20, 'Thriller', 11),
(21, 'Like a Virgin', 12),
(22, 'The Joshua Tree', 13),
(23, 'OK Computer', 14),
(24, 'Who\'s Next', 15);

--
-- Data for table `track`
--

INSERT INTO `track` (`TrackId`, `Name`, `AlbumId`, `MediaTypeId`, `GenreId`, `Milliseconds`, `Bytes`, `UnitPrice`, `ComposerId`) VALUES
(1, 'Come Together', 1, 1, 5, 259000, 8396743, 1.29, 1),
(2, 'Something', 1, 1, 5, 183000, 5934205, 0.99, 3),
(3, 'Here Comes the Sun', 1, 1, 5, 185000, 6004032, 1.39, 3),
(4, 'Sgt. Pepper\'s Lonely Hearts Club Band', 2, 1, 5, 122000, 3952234, 1.49, 2),
(5, 'With a Little Help from My Friends', 2, 1, 5, 164000, 5321234, 0.89, 2),
(6, 'Money', 3, 1, 6, 382000, 12384756, 1.09, 4),
(7, 'Time', 3, 1, 6, 421000, 13654321, 1.39, 4),
(8, 'Breathe', 3, 1, 6, 163000, 5287654, 0.99, 4),
(9, 'Another Brick in the Wall (Part 2)', 4, 1, 6, 238000, 7712345, 1.19, 4),
(10, 'Comfortably Numb', 4, 1, 6, 382000, 12384756, 1.49, 5),
(11, 'Stairway to Heaven', 5, 1, 7, 482000, 15618234, 1.29, 6),
(12, 'Black Dog', 5, 1, 7, 296000, 9594321, 1.09, 6),
(13, 'Rock and Roll', 5, 1, 7, 220000, 7132456, 0.89, 6),
(14, 'Kashmir', 6, 1, 7, 516000, 16722345, 1.39, 6),
(15, 'Bohemian Rhapsody', 7, 1, 1, 355000, 11502345, 1.49, 8),
(16, 'We Are the Champions', 8, 1, 1, 179000, 5805432, 0.89, 8),
(17, 'We Will Rock You', 8, 1, 1, 122000, 3952234, 0.99, 9),
(18, 'Gimme Shelter', 9, 1, 1, 270000, 8753456, 1.19, 10),
(19, 'You Can\'t Always Get What You Want', 9, 1, 1, 447000, 14485321, 1.39, 10),
(20, 'Brown Sugar', 10, 1, 1, 220000, 7132456, 0.99, 10),
(21, 'Back in Black', 11, 1, 7, 255000, 8267543, 1.19, 12),
(22, 'Hells Bells', 11, 1, 7, 312000, 10116432, 1.29, 12),
(23, 'Highway to Hell', 12, 1, 7, 208000, 6743521, 0.89, 12),
(24, 'Master of Puppets', 13, 1, 3, 515000, 16689876, 1.49, 14),
(25, 'Battery', 13, 1, 3, 312000, 10116432, 1.29, 14),
(26, 'Enter Sandman', 14, 1, 3, 331000, 10732567, 1.39, 14),
(27, 'The Unforgiven', 14, 1, 3, 387000, 12546789, 1.09, 14),
(28, 'Smells Like Teen Spirit', 15, 1, 10, 301000, 9756432, 1.29, 16),
(29, 'Come As You Are', 15, 1, 10, 219000, 7099876, 0.99, 16),
(30, 'Lithium', 15, 1, 10, 256000, 8300123, 1.09, 16),
(31, 'Heart-Shaped Box', 16, 1, 10, 281000, 9110987, 1.19, 16),
(32, 'Like a Rolling Stone', 17, 1, 8, 373000, 12092345, 1.49, 17),
(33, 'Desolation Row', 17, 1, 8, 653000, 21165432, 1.39, 17),
(34, 'Visions of Johanna', 18, 1, 8, 458000, 14841234, 1.19, 17),
(35, 'Hound Dog', 19, 1, 1, 135000, 4376543, 0.69, 18),
(36, 'Billie Jean', 20, 1, 2, 294000, 9529876, 1.29, 19),
(37, 'Beat It', 20, 1, 2, 258000, 8363210, 1.09, 19),
(38, 'Like a Virgin', 21, 1, 2, 218000, 7067432, 0.99, 20),
(39, 'With or Without You', 22, 1, 1, 296000, 9594321, 1.09, 21),
(40, 'Paranoid Android', 23, 1, 4, 383000, 12417654, 1.49, 23);


--
-- Data for table `playlist`
--

INSERT INTO `playlist` (`PlaylistId`, `Name`) VALUES
(1, 'Classic Rock Hits'),
(2, 'Metal Essentials'),
(3, '90s Alternative'),
(4, 'Greatest Hits'),
(5, 'Acoustic Sessions');

--
-- Data for table `playlisttrack`
--

INSERT INTO `playlisttrack` (`PlaylistId`, `TrackId`) VALUES
(1, 1),
(1, 11),
(1, 15),
(1, 18),
(1, 21),
(1, 32),
(1, 39),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 21),
(2, 22),
(2, 23),
(3, 28),
(3, 29),
(3, 30),
(3, 31),
(3, 40),
(4, 1),
(4, 6),
(4, 11),
(4, 15),
(4, 28),
(4, 32),
(4, 36),
(4, 39),
(5, 2),
(5, 3),
(5, 8),
(5, 32),
(5, 34);
