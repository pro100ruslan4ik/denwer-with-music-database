<?php
require_once 'config.php';

// Обработка CRUD операций
$message = '';
$error = '';

// CREATE - Добавление трека
if (isset($_POST['add_track'])) {
    $name = mysqli_real_escape_string($id, $_POST['name']);
    $album_id = (int)$_POST['album_id'];
    $mediatype_id = (int)$_POST['mediatype_id'];
    $genre_id = (int)$_POST['genre_id'];
    $milliseconds = (int)$_POST['milliseconds'];
    $bytes = (int)$_POST['bytes'];
    $unit_price = (float)$_POST['unit_price'];
    $composer_id = $_POST['composer_id'] ? (int)$_POST['composer_id'] : NULL;
    
    $sql = "INSERT INTO track (Name, AlbumId, MediaTypeId, GenreId, Milliseconds, Bytes, UnitPrice, ComposerId) 
            VALUES ('$name', $album_id, $mediatype_id, $genre_id, $milliseconds, $bytes, $unit_price, " . 
            ($composer_id ? $composer_id : 'NULL') . ")";
    
    if (mysqli_query($id, $sql)) {
        $message = "Трек успешно добавлен!";
    } else {
        $error = "Ошибка при добавлении трека: " . mysqli_error($id);
    }
}

// UPDATE - Обновление трека
if (isset($_POST['update_track'])) {
    $track_id = (int)$_POST['track_id'];
    $name = mysqli_real_escape_string($id, $_POST['name']);
    $album_id = (int)$_POST['album_id'];
    $mediatype_id = (int)$_POST['mediatype_id'];
    $genre_id = (int)$_POST['genre_id'];
    $milliseconds = (int)$_POST['milliseconds'];
    $bytes = (int)$_POST['bytes'];
    $unit_price = (float)$_POST['unit_price'];
    $composer_id = $_POST['composer_id'] ? (int)$_POST['composer_id'] : NULL;
    
    $sql = "UPDATE track SET 
            Name = '$name',
            AlbumId = $album_id,
            MediaTypeId = $mediatype_id,
            GenreId = $genre_id,
            Milliseconds = $milliseconds,
            Bytes = $bytes,
            UnitPrice = $unit_price,
            ComposerId = " . ($composer_id ? $composer_id : 'NULL') . "
            WHERE TrackId = $track_id";
    
    if (mysqli_query($id, $sql)) {
        $message = "Трек успешно обновлен!";
    } else {
        $error = "Ошибка при обновлении трека: " . mysqli_error($id);
    }
}

// DELETE - Удаление трека
if (isset($_POST['delete_track'])) {
    $track_id = (int)$_POST['track_id'];
    
    $sql = "DELETE FROM track WHERE TrackId = $track_id";
    
    if (mysqli_query($id, $sql)) {
        $message = "Трек успешно удален!";
    } else {
        $error = "Ошибка при удалении трека: " . mysqli_error($id);
    }
}

// Получение данных для селектов
$albums = mysqli_query($id, "SELECT AlbumId, Title, artist.Name as ArtistName FROM album JOIN artist ON album.ArtistId = artist.ArtistId ORDER BY artist.Name, Title");
$genres = mysqli_query($id, "SELECT * FROM genre ORDER BY Name");
$mediatypes = mysqli_query($id, "SELECT * FROM mediatype ORDER BY Name");
$composers = mysqli_query($id, "SELECT * FROM composer ORDER BY Name");

// Получение треков с JOIN
$tracks_query = "SELECT t.TrackId, t.Name as TrackName, t.Milliseconds, t.Bytes, t.UnitPrice,
                        al.Title as AlbumTitle, ar.Name as ArtistName, g.Name as GenreName, 
                        mt.Name as MediaTypeName, c.Name as ComposerName
                 FROM track t
                 JOIN album al ON t.AlbumId = al.AlbumId
                 JOIN artist ar ON al.ArtistId = ar.ArtistId
                 JOIN genre g ON t.GenreId = g.GenreId
                 JOIN mediatype mt ON t.MediaTypeId = mt.MediaTypeId
                 LEFT JOIN composer c ON t.ComposerId = c.ComposerId
                 ORDER BY t.Name, ar.Name, al.Title";

$tracks = mysqli_query($id, $tracks_query);

// Получение трека для редактирования
$edit_track = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_result = mysqli_query($id, "SELECT * FROM track WHERE TrackId = $edit_id");
    if ($edit_result && mysqli_num_rows($edit_result) > 0) {
        $edit_track = mysqli_fetch_assoc($edit_result);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление Треками - Музыкальная База Данных</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Управление Треками</h1>
            <p class="subtitle">Добавление, редактирование и удаление треков</p>
        </header>

        <nav class="main-nav">
            <a href="index.php" class="nav-link">Главная</a>
            <a href="tracks.php" class="nav-link active">CRUD Треки</a>
            <a href="queries.php" class="nav-link">Запросы</a>
        </nav>

        <main>
            <?php if ($message): ?>
                <div class="query-info" style="background: #d4edda; border-color: #c3e6cb; color: #155724;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="query-info" style="background: #f8d7da; border-color: #f5c6cb; color: #721c24;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Форма добавления/редактирования трека -->
            <div class="form-container">
                <h2><?php echo $edit_track ? 'Редактировать трек' : 'Добавить новый трек'; ?></h2>
                
                <form method="POST" action="">
                    <?php if ($edit_track): ?>
                        <input type="hidden" name="track_id" value="<?php echo $edit_track['TrackId']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Название трека:</label>
                            <input type="text" id="name" name="name" class="form-control" 
                                   value="<?php echo $edit_track ? htmlspecialchars($edit_track['Name']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="album_id">Альбом:</label>
                            <select id="album_id" name="album_id" class="form-control" required>
                                <option value="">Выберите альбом</option>
                                <?php 
                                mysqli_data_seek($albums, 0);
                                while ($album = mysqli_fetch_assoc($albums)): ?>
                                    <option value="<?php echo $album['AlbumId']; ?>"
                                            <?php echo ($edit_track && $edit_track['AlbumId'] == $album['AlbumId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($album['ArtistName'] . ' - ' . $album['Title']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="genre_id">Жанр:</label>
                            <select id="genre_id" name="genre_id" class="form-control" required>
                                <option value="">Выберите жанр</option>
                                <?php 
                                mysqli_data_seek($genres, 0);
                                while ($genre = mysqli_fetch_assoc($genres)): ?>
                                    <option value="<?php echo $genre['GenreId']; ?>"
                                            <?php echo ($edit_track && $edit_track['GenreId'] == $genre['GenreId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($genre['Name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="mediatype_id">Тип медиа:</label>
                            <select id="mediatype_id" name="mediatype_id" class="form-control" required>
                                <option value="">Выберите тип медиа</option>
                                <?php 
                                mysqli_data_seek($mediatypes, 0);
                                while ($mediatype = mysqli_fetch_assoc($mediatypes)): ?>
                                    <option value="<?php echo $mediatype['MediaTypeId']; ?>"
                                            <?php echo ($edit_track && $edit_track['MediaTypeId'] == $mediatype['MediaTypeId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($mediatype['Name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="composer_id">Композитор:</label>
                            <select id="composer_id" name="composer_id" class="form-control">
                                <option value="">Не указан</option>
                                <?php 
                                mysqli_data_seek($composers, 0);
                                while ($composer = mysqli_fetch_assoc($composers)): ?>
                                    <option value="<?php echo $composer['ComposerId']; ?>"
                                            <?php echo ($edit_track && $edit_track['ComposerId'] == $composer['ComposerId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($composer['Name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="unit_price">Цена ($):</label>
                            <input type="number" id="unit_price" name="unit_price" class="form-control" 
                                   step="0.01" min="0" 
                                   value="<?php echo $edit_track ? $edit_track['UnitPrice'] : '0.99'; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="milliseconds">Продолжительность (мс):</label>
                            <input type="number" id="milliseconds" name="milliseconds" class="form-control" 
                                   min="1000" 
                                   value="<?php echo $edit_track ? $edit_track['Milliseconds'] : '180000'; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="bytes">Размер файла (байт):</label>
                            <input type="number" id="bytes" name="bytes" class="form-control" 
                                   min="1000" 
                                   value="<?php echo $edit_track ? $edit_track['Bytes'] : '5000000'; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <?php if ($edit_track): ?>
                            <button type="submit" name="update_track" class="btn btn-warning">Обновить трек</button>
                            <a href="tracks.php" class="btn">Отмена</a>
                        <?php else: ?>
                            <button type="submit" name="add_track" class="btn btn-success">Добавить трек</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Список треков -->
            <div class="table-container">
                <h2>Список треков</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Исполнитель</th>
                            <th>Альбом</th>
                            <th>Жанр</th>
                            <th>Композитор</th>
                            <th>Длительность</th>
                            <th>Цена</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($track = mysqli_fetch_assoc($tracks)): ?>
                            <tr>
                                <td><?php echo $track['TrackId']; ?></td>
                                <td><?php echo htmlspecialchars($track['TrackName']); ?></td>
                                <td><?php echo htmlspecialchars($track['ArtistName']); ?></td>
                                <td><?php echo htmlspecialchars($track['AlbumTitle']); ?></td>
                                <td><?php echo htmlspecialchars($track['GenreName']); ?></td>
                                <td><?php echo $track['ComposerName'] ? htmlspecialchars($track['ComposerName']) : '-'; ?></td>
                                <td><?php echo gmdate("i:s", $track['Milliseconds']/1000); ?></td>
                                <td>$<?php echo number_format($track['UnitPrice'], 2); ?></td>
                                <td>
                                    <a href="tracks.php?edit=<?php echo $track['TrackId']; ?>" class="btn btn-warning" style="padding: 5px 10px; margin-right: 5px;">Редактировать</a>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Вы уверены, что хотите удалить этот трек?');">
                                        <input type="hidden" name="track_id" value="<?php echo $track['TrackId']; ?>">
                                        <button type="submit" name="delete_track" class="btn btn-danger" style="padding: 5px 10px;">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 Музыкальная База Данных</p>
        </footer>
    </div>
</body>
</html>
