<?php
require_once 'config.php';

$query_result = null;
$executed_query = '';
$query_description = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Поиск треков по названию
    if (isset($_POST['search_tracks'])) {
        $search_term = mysqli_real_escape_string($id, $_POST['search_term']);
        $executed_query = "SELECT t.TrackId, t.Name as TrackName, ar.Name as ArtistName, al.Title as AlbumTitle, g.Name as GenreName
                          FROM track t
                          JOIN album al ON t.AlbumId = al.AlbumId
                          JOIN artist ar ON al.ArtistId = ar.ArtistId
                          JOIN genre g ON t.GenreId = g.GenreId
                          WHERE t.Name LIKE '%$search_term%'
                          ORDER BY ar.Name, al.Title, t.Name";
        $query_description = "Поиск треков по названию содержащему: '$search_term'";
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 2. Фильтрация по жанрам (чекбоксы)
    if (isset($_POST['filter_by_genres']) && !empty($_POST['selected_genres'])) {
        $selected_genres = array_map('intval', $_POST['selected_genres']);
        $genre_ids = implode(',', $selected_genres);
        
        $executed_query = "SELECT t.TrackId, t.Name as TrackName, ar.Name as ArtistName, al.Title as AlbumTitle, g.Name as GenreName, t.UnitPrice
                          FROM track t
                          JOIN album al ON t.AlbumId = al.AlbumId
                          JOIN artist ar ON al.ArtistId = ar.ArtistId
                          JOIN genre g ON t.GenreId = g.GenreId
                          WHERE g.GenreId IN ($genre_ids)
                          ORDER BY g.Name, ar.Name, t.Name";
        $query_description = "Треки выбранных жанров";
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 3. Статистика по исполнителям (выпадающий список)
    if (isset($_POST['artist_stats'])) {
        $artist_id = (int)$_POST['artist_id'];
        
        if ($artist_id > 0) {
            $executed_query = "SELECT ar.Name as ArtistName,
                              COUNT(t.TrackId) as TracksCount,
                              COUNT(DISTINCT al.AlbumId) as AlbumsCount,
                              AVG(t.UnitPrice) as AvgPrice,
                              SUM(t.Milliseconds) as TotalDuration,
                              MIN(t.UnitPrice) as MinPrice,
                              MAX(t.UnitPrice) as MaxPrice
                              FROM artist ar
                              JOIN album al ON ar.ArtistId = al.ArtistId
                              JOIN track t ON al.AlbumId = t.AlbumId
                              WHERE ar.ArtistId = $artist_id
                              GROUP BY ar.ArtistId, ar.Name";
            $query_description = "Статистика по выбранному исполнителю";
        } else {
            $executed_query = "SELECT ar.Name as ArtistName,
                              COUNT(t.TrackId) as TracksCount,
                              COUNT(DISTINCT al.AlbumId) as AlbumsCount,
                              AVG(t.UnitPrice) as AvgPrice
                              FROM artist ar
                              JOIN album al ON ar.ArtistId = al.ArtistId
                              JOIN track t ON al.AlbumId = t.AlbumId
                              GROUP BY ar.ArtistId, ar.Name
                              ORDER BY TracksCount DESC";
            $query_description = "Статистика по всем исполнителям";
        }
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 4. Фильтрация по цене (ползунки)
    if (isset($_POST['filter_by_price'])) {
        $min_price = (float)$_POST['min_price'];
        $max_price = (float)$_POST['max_price'];
        
        $executed_query = "SELECT t.Name as TrackName, ar.Name as ArtistName, al.Title as AlbumTitle, 
                          t.UnitPrice, g.Name as GenreName
                          FROM track t
                          JOIN album al ON t.AlbumId = al.AlbumId
                          JOIN artist ar ON al.ArtistId = ar.ArtistId
                          JOIN genre g ON t.GenreId = g.GenreId
                          WHERE t.UnitPrice BETWEEN $min_price AND $max_price
                          ORDER BY t.UnitPrice DESC, ar.Name";
        $query_description = "Треки с ценой от $$min_price до $$max_price";
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 5. Сложный запрос с группировкой
    if (isset($_POST['genre_analysis'])) {
        $executed_query = "SELECT g.Name as GenreName,
                          COUNT(t.TrackId) as TracksCount,
                          COUNT(DISTINCT ar.ArtistId) as ArtistsCount,
                          COUNT(DISTINCT al.AlbumId) as AlbumsCount,
                          AVG(t.UnitPrice) as AvgPrice,
                          AVG(t.Milliseconds/1000/60) as AvgDurationMinutes,
                          SUM(t.Bytes)/1024/1024 as TotalSizeMB
                          FROM genre g
                          JOIN track t ON g.GenreId = t.GenreId
                          JOIN album al ON t.AlbumId = al.AlbumId
                          JOIN artist ar ON al.ArtistId = ar.ArtistId
                          GROUP BY g.GenreId, g.Name
                          ORDER BY TracksCount DESC";
        $query_description = "Анализ жанров: количество треков, исполнителей, средняя цена и длительность";
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 6. Топ треков по длительности
    if (isset($_POST['longest_tracks'])) {
        $limit = (int)$_POST['tracks_limit'];
        if ($limit < 1) $limit = 10;
        
        $executed_query = "SELECT t.Name as TrackName, ar.Name as ArtistName, al.Title as AlbumTitle,
                          t.Milliseconds, (t.Milliseconds/1000/60) as DurationMinutes,
                          g.Name as GenreName, c.Name as ComposerName
                          FROM track t
                          JOIN album al ON t.AlbumId = al.AlbumId
                          JOIN artist ar ON al.ArtistId = ar.ArtistId
                          JOIN genre g ON t.GenreId = g.GenreId
                          LEFT JOIN composer c ON t.ComposerId = c.ComposerId
                          ORDER BY t.Milliseconds DESC
                          LIMIT $limit";
        $query_description = "Топ $limit самых длинных треков";
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 7. Поиск альбомов по году (условный поиск)
    if (isset($_POST['expensive_tracks'])) {
        $executed_query = "SELECT t.Name as TrackName, ar.Name as ArtistName, al.Title as AlbumTitle,
                          t.UnitPrice, g.Name as GenreName,
                          CASE 
                              WHEN t.UnitPrice >= 1.50 THEN 'Дорогой'
                              WHEN t.UnitPrice >= 1.00 THEN 'Средний'
                              ELSE 'Дешевый'
                          END as PriceCategory
                          FROM track t
                          JOIN album al ON t.AlbumId = al.AlbumId
                          JOIN artist ar ON al.ArtistId = ar.ArtistId
                          JOIN genre g ON t.GenreId = g.GenreId
                          WHERE t.UnitPrice > (SELECT AVG(UnitPrice) FROM track)
                          ORDER BY t.UnitPrice DESC";
        $query_description = "Треки дороже средней цены с категоризацией";
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 8. Поиск треков по композитору
    if (isset($_POST['search_by_composer'])) {
        $composer_name = mysqli_real_escape_string($id, $_POST['composer_name']);
        $executed_query = "SELECT t.Name as TrackName, ar.Name as ArtistName, al.Title as AlbumTitle,
                          c.Name as ComposerName, g.Name as GenreName, t.UnitPrice
                          FROM track t
                          JOIN album al ON t.AlbumId = al.AlbumId
                          JOIN artist ar ON al.ArtistId = ar.ArtistId
                          JOIN genre g ON t.GenreId = g.GenreId
                          LEFT JOIN composer c ON t.ComposerId = c.ComposerId
                          WHERE c.Name LIKE '%$composer_name%'
                          ORDER BY c.Name, ar.Name, t.Name";
        $query_description = "Поиск треков по композитору: '$composer_name'";
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 9. Топ альбомов по количеству треков
    if (isset($_POST['top_albums'])) {
        $limit = (int)$_POST['albums_limit'];
        if ($limit < 1) $limit = 10;
        
        $executed_query = "SELECT al.Title as AlbumTitle, ar.Name as ArtistName,
                          COUNT(t.TrackId) as TracksCount,
                          AVG(t.UnitPrice) as AvgPrice,
                          SUM(t.Milliseconds)/1000/60 as TotalDurationMinutes
                          FROM album al
                          JOIN artist ar ON al.ArtistId = ar.ArtistId
                          JOIN track t ON al.AlbumId = t.AlbumId
                          GROUP BY al.AlbumId, al.Title, ar.Name
                          ORDER BY TracksCount DESC
                          LIMIT $limit";
        $query_description = "Топ $limit альбомов по количеству треков";
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 10. Анализ популярности жанров по цене
    if (isset($_POST['genre_popularity'])) {
        $executed_query = "SELECT g.Name as GenreName,
                          COUNT(t.TrackId) as TracksCount,
                          AVG(t.UnitPrice) as AvgPrice,
                          MIN(t.UnitPrice) as MinPrice,
                          MAX(t.UnitPrice) as MaxPrice,
                          CASE 
                              WHEN AVG(t.UnitPrice) > 1.20 THEN 'Премиум'
                              WHEN AVG(t.UnitPrice) > 0.99 THEN 'Средний'
                              ELSE 'Бюджетный'
                          END as PriceCategory
                          FROM genre g
                          JOIN track t ON g.GenreId = t.GenreId
                          GROUP BY g.GenreId, g.Name
                          ORDER BY AvgPrice DESC, TracksCount DESC";
        $query_description = "Анализ популярности жанров по ценовым категориям";
        $query_result = mysqli_query($id, $executed_query);
    }
    
    // 11. Поиск треков по размеру файла
    if (isset($_POST['filter_by_size'])) {
        $min_size = (int)$_POST['min_size'];
        $max_size = (int)$_POST['max_size'];
        
        $executed_query = "SELECT t.Name as TrackName, ar.Name as ArtistName, al.Title as AlbumTitle,
                          t.Bytes, (t.Bytes/1024/1024) as SizeMB, g.Name as GenreName, t.UnitPrice
                          FROM track t
                          JOIN album al ON t.AlbumId = al.AlbumId
                          JOIN artist ar ON al.ArtistId = ar.ArtistId
                          JOIN genre g ON t.GenreId = g.GenreId
                          WHERE t.Bytes BETWEEN $min_size AND $max_size
                          ORDER BY t.Bytes DESC, ar.Name";
        $query_description = "Треки с размером файла от " . number_format($min_size/1024/1024, 2) . " до " . number_format($max_size/1024/1024, 2) . " МБ";
        $query_result = mysqli_query($id, $executed_query);
    }
}

// Получение данных для виджетов
$genres_for_filter = mysqli_query($id, "SELECT * FROM genre ORDER BY Name");
$artists_for_select = mysqli_query($id, "SELECT * FROM artist ORDER BY Name");
$price_range = mysqli_query($id, "SELECT MIN(UnitPrice) as min_price, MAX(UnitPrice) as max_price FROM track");
$price_data = mysqli_fetch_assoc($price_range);

$composers_for_search = mysqli_query($id, "SELECT DISTINCT c.Name FROM composer c JOIN track t ON c.ComposerId = t.ComposerId WHERE c.Name IS NOT NULL ORDER BY c.Name");
$size_range = mysqli_query($id, "SELECT MIN(Bytes) as min_size, MAX(Bytes) as max_size FROM track WHERE Bytes > 0");
$size_data = mysqli_fetch_assoc($size_range);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск Музыки - Музыкальная База Данных</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Поиск Музыки</h1>
            <p class="subtitle">Различные способы поиска и фильтрации треков</p>
        </header>

        <nav class="main-nav">
            <a href="index.php" class="nav-link">Главная</a>
            <a href="tracks.php" class="nav-link">CRUD Треки</a>
            <a href="queries.php" class="nav-link active">Запросы</a>
        </nav>

        <main>
            <div class="filters-section">
                <h2>Поиск и фильтрация</h2>
                
                <!-- 1. Поиск по названию трека -->
                <div class="filter-group">
                    <h3>🔍 Поиск треков по названию</h3>
                    <form method="POST" class="form-row">
                        <div class="form-group">
                            <input type="text" name="search_term" class="form-control" 
                                   placeholder="Введите часть названия трека..." 
                                   value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <button type="submit" name="search_tracks" class="btn">Найти треки</button>
                        </div>
                    </form>
                </div>

                <!-- 2. Фильтрация по жанрам (чекбоксы) -->
                <div class="filter-group">
                    <h3>🎵 Фильтр по жанрам (множественный выбор)</h3>
                    <form method="POST">
                        <div class="checkbox-group">
                            <?php while ($genre = mysqli_fetch_assoc($genres_for_filter)): ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" name="selected_genres[]" value="<?php echo $genre['GenreId']; ?>"
                                           id="genre_<?php echo $genre['GenreId']; ?>"
                                           <?php echo (isset($_POST['selected_genres']) && in_array($genre['GenreId'], $_POST['selected_genres'])) ? 'checked' : ''; ?>>
                                    <label for="genre_<?php echo $genre['GenreId']; ?>"><?php echo htmlspecialchars($genre['Name']); ?></label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <button type="submit" name="filter_by_genres" class="btn mt-2">Показать треки выбранных жанров</button>
                    </form>
                </div>

                <!-- 3. Статистика по исполнителям (выпадающий список) -->
                <div class="filter-group">
                    <h3>📊 Статистика по исполнителям</h3>
                    <form method="POST" class="form-row">
                        <div class="form-group">
                            <select name="artist_id" class="form-control">
                                <option value="0">Все исполнители</option>
                                <?php while ($artist = mysqli_fetch_assoc($artists_for_select)): ?>
                                    <option value="<?php echo $artist['ArtistId']; ?>"
                                            <?php echo (isset($_POST['artist_id']) && $_POST['artist_id'] == $artist['ArtistId']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($artist['Name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="artist_stats" class="btn">Показать статистику</button>
                        </div>
                    </form>
                </div>

                <!-- 4. Фильтрация по цене (ползунки) -->
                <div class="filter-group">
                    <h3>💰 Фильтр по цене</h3>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="min_price">Минимальная цена ($):</label>
                                <input type="number" id="min_price" name="min_price" class="form-control"
                                       min="<?php echo $price_data['min_price']; ?>" 
                                       max="<?php echo $price_data['max_price']; ?>" 
                                       step="0.01"
                                       value="<?php echo isset($_POST['min_price']) ? $_POST['min_price'] : $price_data['min_price']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="max_price">Максимальная цена ($):</label>
                                <input type="number" id="max_price" name="max_price" class="form-control"
                                       min="<?php echo $price_data['min_price']; ?>" 
                                       max="<?php echo $price_data['max_price']; ?>" 
                                       step="0.01"
                                       value="<?php echo isset($_POST['max_price']) ? $_POST['max_price'] : $price_data['max_price']; ?>">
                            </div>
                        </div>
                        <button type="submit" name="filter_by_price" class="btn">Фильтровать по цене</button>
                    </form>
                </div>

                <!-- 5. Другие запросы -->
                <div class="filter-group">
                    <h3>📈 Другие запросы</h3>
                    <div class="form-row">
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="genre_analysis" class="btn">Анализ жанров</button>
                        </form>
                        
                        <form method="POST" style="display: inline;">
                            <button type="submit" name="expensive_tracks" class="btn">Дорогие треки</button>
                        </form>
                        
                        <form method="POST" style="display: inline; margin-left: 10px;">
                            <input type="number" name="tracks_limit" placeholder="Лимит" min="1" max="50" 
                                   value="<?php echo isset($_POST['tracks_limit']) ? $_POST['tracks_limit'] : '10'; ?>" style="width: 80px; padding: 5px;">
                            <button type="submit" name="longest_tracks" class="btn">Самые длинные треки</button>
                        </form>
                    </div>
                </div>

                <!-- 6. Поиск по композитору -->
                <div class="filter-group">
                    <h3>🎼 Поиск треков по композитору</h3>
                    <form method="POST" class="form-row">
                        <div class="form-group">
                            <input type="text" name="composer_name" class="form-control" 
                                   placeholder="Введите имя композитора..." 
                                   value="<?php echo isset($_POST['composer_name']) ? htmlspecialchars($_POST['composer_name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <button type="submit" name="search_by_composer" class="btn">Найти треки композитора</button>
                        </div>
                    </form>
                </div>

                <!-- 7. Топ альбомов по количеству треков -->
                <div class="filter-group">
                    <h3>🏆 Топ альбомов по количеству треков</h3>
                    <form method="POST" class="form-row">
                        <div class="form-group">
                            <input type="number" name="albums_limit" placeholder="Количество альбомов" min="1" max="50" 
                                   value="<?php echo isset($_POST['albums_limit']) ? $_POST['albums_limit'] : '10'; ?>" 
                                   style="width: 150px; padding: 5px;">
                        </div>
                        <div class="form-group">
                            <button type="submit" name="top_albums" class="btn">Показать топ альбомов</button>
                        </div>
                    </form>
                </div>

                <!-- 8. Анализ популярности жанров -->
                <div class="filter-group">
                    <h3>📊 Анализ популярности жанров по цене</h3>
                    <form method="POST">
                        <button type="submit" name="genre_popularity" class="btn">Показать анализ жанров</button>
                    </form>
                </div>

                <!-- 9. Фильтрация по размеру файла -->
                <div class="filter-group">
                    <h3>💾 Фильтр по размеру файла</h3>
                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="min_size">Минимальный размер (байт):</label>
                                <input type="number" id="min_size" name="min_size" class="form-control"
                                       min="<?php echo $size_data['min_size']; ?>" 
                                       max="<?php echo $size_data['max_size']; ?>" 
                                       value="<?php echo isset($_POST['min_size']) ? $_POST['min_size'] : $size_data['min_size']; ?>">
                            </div>
                            <div class="form-group">
                                <label for="max_size">Максимальный размер (байт):</label>
                                <input type="number" id="max_size" name="max_size" class="form-control"
                                       min="<?php echo $size_data['min_size']; ?>" 
                                       max="<?php echo $size_data['max_size']; ?>" 
                                       value="<?php echo isset($_POST['max_size']) ? $_POST['max_size'] : $size_data['max_size']; ?>">
                            </div>
                        </div>
                        <button type="submit" name="filter_by_size" class="btn">Фильтровать по размеру</button>
                    </form>
                </div>
            </div>

            <!-- Результаты запросов -->
            <?php if ($query_result): ?>
                <div class="query-results">
                    <h2>Результаты запроса</h2>
                    
                    <div class="query-info">
                        <strong>Описание:</strong> <?php echo $query_description; ?>
                    </div>
                    
                    <div class="query-sql">
                        <strong>SQL запрос:</strong><br>
                        <?php echo htmlspecialchars($executed_query); ?>
                    </div>
                    
                    <?php if (mysqli_num_rows($query_result) > 0): ?>
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <?php
                                        // Получаем информацию о полях
                                        $fields = mysqli_fetch_fields($query_result);
                                        foreach ($fields as $field):
                                        ?>
                                            <th><?php echo htmlspecialchars($field->name); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($query_result)): ?>
                                        <tr>
                                            <?php foreach ($row as $value): ?>
                                                <td>
                                                    <?php 
                                                    if (is_numeric($value) && strpos($value, '.') !== false) {
                                                        echo number_format($value, 2);
                                                    } else {
                                                        echo htmlspecialchars($value);
                                                    }
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="query-info" style="background: #fff3cd; border-color: #ffeaa7; color: #856404;">
                            Запрос не вернул результатов.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>


        </main>

        <footer>
            <p>&copy; 2025 Музыкальная База Данных</p>
        </footer>
    </div>
</body>
</html>
