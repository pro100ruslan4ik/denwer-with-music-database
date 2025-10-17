DROP DATABASE IF EXISTS `music`;
CREATE DATABASE IF NOT EXISTS `music`;
USE `music`;

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE `album` (
  `AlbumId` int(11) NOT NULL,
  `Title` varchar(160) NOT NULL,
  `ArtistId` int(11) NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `artist`
--

CREATE TABLE `artist` (
  `ArtistId` int(11) NOT NULL,
  `Name` varchar(120) DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `composer`
--

CREATE TABLE `composer` (
  `ComposerId` int(11) NOT NULL,
  `Name` varchar(120) DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `GenreId` int(11) NOT NULL,
  `Name` varchar(120) DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `mediatype`
--

CREATE TABLE `mediatype` (
  `MediaTypeId` int(11) NOT NULL,
  `Name` varchar(120) DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE `playlist` (
  `PlaylistId` int(11) NOT NULL,
  `Name` varchar(120) DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `playlisttrack`
--

CREATE TABLE `playlisttrack` (
  `PlaylistId` int(11) NOT NULL,
  `TrackId` int(11) NOT NULL
);

-- --------------------------------------------------------

--
-- Table structure for table `track`
--

CREATE TABLE `track` (
  `TrackId` int(11) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `AlbumId` int(11) DEFAULT NULL,
  `MediaTypeId` int(11) NOT NULL,
  `GenreId` int(11) DEFAULT NULL,
  `Milliseconds` int(11) NOT NULL,
  `Bytes` int(11) DEFAULT NULL,
  `UnitPrice` decimal(10,2) NOT NULL,
  `ComposerId` int(11) DEFAULT NULL
);

ALTER TABLE `album`
  ADD PRIMARY KEY (`AlbumId`),
  ADD KEY `IFK_AlbumArtistId` (`ArtistId`);

ALTER TABLE `artist`
  ADD PRIMARY KEY (`ArtistId`);

ALTER TABLE `composer`
  ADD PRIMARY KEY (`ComposerId`);

ALTER TABLE `genre`
  ADD PRIMARY KEY (`GenreId`);

ALTER TABLE `mediatype`
  ADD PRIMARY KEY (`MediaTypeId`);

ALTER TABLE `playlist`
  ADD PRIMARY KEY (`PlaylistId`);

ALTER TABLE `playlisttrack`
  ADD PRIMARY KEY (`PlaylistId`,`TrackId`),
  ADD KEY `IFK_PlaylistTrackPlaylistId` (`PlaylistId`),
  ADD KEY `IFK_PlaylistTrackTrackId` (`TrackId`);

ALTER TABLE `track`
  ADD PRIMARY KEY (`TrackId`),
  ADD KEY `IFK_TrackAlbumId` (`AlbumId`),
  ADD KEY `IFK_TrackGenreId` (`GenreId`),
  ADD KEY `IFK_TrackMediaTypeId` (`MediaTypeId`),
  ADD KEY `fk_track_composer` (`ComposerId`);

--
-- AUTO_INCREMENT
--
ALTER TABLE `album`
  MODIFY `AlbumId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

ALTER TABLE `artist`
  MODIFY `ArtistId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

ALTER TABLE `composer`
  MODIFY `ComposerId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

ALTER TABLE `genre`
  MODIFY `GenreId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `mediatype`
  MODIFY `MediaTypeId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `playlist`
  MODIFY `PlaylistId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `track`
  MODIFY `TrackId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;


--
-- Constraints for table `album`
--
ALTER TABLE `album`
  ADD CONSTRAINT `FK_AlbumArtistId` FOREIGN KEY (`ArtistId`) REFERENCES `artist` (`ArtistId`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `playlisttrack`
--
ALTER TABLE `playlisttrack`
  ADD CONSTRAINT `FK_PlaylistTrackPlaylistId` FOREIGN KEY (`PlaylistId`) REFERENCES `playlist` (`PlaylistId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_PlaylistTrackTrackId` FOREIGN KEY (`TrackId`) REFERENCES `track` (`TrackId`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `track`
--
ALTER TABLE `track`
  ADD CONSTRAINT `FK_TrackAlbumId` FOREIGN KEY (`AlbumId`) REFERENCES `album` (`AlbumId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_TrackGenreId` FOREIGN KEY (`GenreId`) REFERENCES `genre` (`GenreId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_TrackMediaTypeId` FOREIGN KEY (`MediaTypeId`) REFERENCES `mediatype` (`MediaTypeId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_track_composer` FOREIGN KEY (`ComposerId`) REFERENCES `composer` (`ComposerId`);

