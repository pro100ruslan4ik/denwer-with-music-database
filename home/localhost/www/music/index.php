<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Музыкальная База Данных</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Музыкальная База Данных</h1>
            <p class="subtitle">Система управления музыкальным каталогом</p>
        </header>

        <nav class="main-nav">
            <a href="index.php" class="nav-link active">Главная</a>
            <a href="tracks.php" class="nav-link">CRUD Треки</a>
            <a href="queries.php" class="nav-link">Запросы</a>
        </nav>

        <main>
            <div class="welcome-section">
                <h2>Добро пожаловать в систему управления музыкальной базой данных</h2>
                <p>Эта система позволяет управлять музыкальным каталогом:</p>
                
                <div class="features">
                    <div class="feature-card">
                        <h3>CRUD операции</h3>
                        <p>Создание, чтение, обновление и удаление треков</p>
                        <a href="tracks.php" class="btn">Перейти к трекам</a>
                    </div>
                    
                    <div class="feature-card">
                        <h3>Поиск и фильтрация</h3>
                        <p>Различные запросы для поиска музыки по разным критериям</p>
                        <a href="queries.php" class="btn">Перейти к запросам</a>
                    </div>
                </div>
            </div>

            <div class="stats-section">
                <h2>Статистика базы данных</h2>
                <div class="stats-grid">
                    <?php
                    // Получаем статистику
                    $artists_count = mysqli_fetch_array(mysqli_query($id, "SELECT COUNT(*) as count FROM artist"))[0];
                    $albums_count = mysqli_fetch_array(mysqli_query($id, "SELECT COUNT(*) as count FROM album"))[0];
                    $tracks_count = mysqli_fetch_array(mysqli_query($id, "SELECT COUNT(*) as count FROM track"))[0];
                    $genres_count = mysqli_fetch_array(mysqli_query($id, "SELECT COUNT(*) as count FROM genre"))[0];
                    ?>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $artists_count; ?></span>
                        <span class="stat-label">Исполнителей</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $albums_count; ?></span>
                        <span class="stat-label">Альбомов</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $tracks_count; ?></span>
                        <span class="stat-label">Треков</span>
                    </div>
                    
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $genres_count; ?></span>
                        <span class="stat-label">Жанров</span>
                    </div>
                </div>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 Музыкальная База Данных</p>
        </footer>
    </div>
</body>
</html>
