<?php

// Menggunakan __DIR__ agar path selalu akurat
require_once __DIR__ . '/config/config.php';

// Kategori berita
$categories = [
    "general" => "General",
    "business" => "Business",
    "entertainment" => "Entertainment",
    "health" => "Health",
    "science" => "Science",
    "sports" => "Sports",
    "technology" => "Technology"
];

// Search
$keyword = trim($_GET["q"] ?? "");

// Filter kategori
$category = $_GET["category"] ?? "general";

if (!array_key_exists($category, $categories)) {
    $category = "general";
}

//pagination
$page = max(1, (int) ($_GET["page"] ?? 1));

// Parameter API
$params = [
    "country" => "us",
    "category" => $category,
    "pageSize" => 30,
    "page" => $page,
    "apiKey" => NEWS_API_KEY
];

if ($keyword !== "") {
    $params["q"] = $keyword;
}

// Request API
$url = "https://newsapi.org/v2/top-headlines?"
    . http_build_query($params);

$response = http_request_get($url);

$articles = [];
$error = null;

if ($response["success"]) {
    $articles = $response["data"]["articles"] ?? [];
} else {
    $error = $response["error"];
}

//Menghitung total halaman
$totalResults = 0;

if ($response["success"]) {
    $articles = $response["data"]["articles"] ?? [];
    $totalResults = $response["data"]["totalResults"] ?? 0;
}

$totalPages = min(ceil($totalResults / 30), 5);

// Format tanggal
function formatDate($date)
{
    if (!$date) {
        return "Date unavailable";
    }

    return date("d M Y, H:i", strtotime($date));
}

// Validasi gambar
function imageUrl($url)
{
    if (
        filter_var($url, FILTER_VALIDATE_URL) &&
        in_array(parse_url($url, PHP_URL_SCHEME), ["http", "https"])
    ) {
        return $url;
    }

    return "https://placehold.co/600x400?text=No+Image";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>NewsHub - Latest News</title>

    <!-- Bootstrap 5.3 -->
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">

        <div class="container">

            <a class="navbar-brand fw-bold fs-4" href="index.php">
                News<span class="text-info">Hub.</span>
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">

                <ul class="navbar-nav ms-auto gap-lg-3">

                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#news">
                            Latest News
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            About
                        </a>
                    </li>

                </ul>

            </div>

        </div>

    </nav>


    <!-- HERO -->
    <section class="hero-section text-white">

        <div class="container py-5">

            <div class="row justify-content-center text-center">

                <div class="col-lg-8">

                    <span class="badge bg-info text-dark mb-3 px-3 py-2">
                        YOUR DAILY NEWS SOURCE
                    </span>

                    <h1 class="display-4 fw-bold mb-3">
                        Stay Informed.<br>
                        Stay <span class="text-info">Ahead.</span>
                    </h1>

                    <p class="text-white-50 mb-4">
                        Discover the latest news and insights
                        from around the world.
                    </p>


                    <!-- SEARCH -->
                    <form action="index.php" method="GET">

                        <div class="input-group input-group-lg search-box">

                            <input
                                type="text"
                                name="q"
                                class="form-control border-0"
                                placeholder="Search news..."
                                value="<?= e($keyword) ?>">

                            <input
                                type="hidden"
                                name="category"
                                value="<?= e($category) ?>">

                            <button
                                type="submit"
                                class="btn btn-info fw-semibold px-4">
                                Search
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </section>


    <!-- NEWS CONTENT -->
    <main class="container py-5" id="news">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

            <div>

                <span class="text-info fw-bold small">
                    EXPLORE
                </span>

                <h2 class="fw-bold mb-1">
                    Latest News
                </h2>

                <p class="text-secondary mb-0">
                    Discover what's happening around the world.
                </p>

            </div>

            <span class="badge bg-primary fs-6">
                <?= count($articles) ?> Articles
            </span>

        </div>


        <!-- FILTER -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <form action="index.php" method="GET">

                    <input
                        type="hidden"
                        name="q"
                        value="<?= e($keyword) ?>">

                    <div class="row align-items-center g-3">

                        <div class="col-md-auto">

                            <label class="form-label fw-semibold mb-0">
                                Filter Category:
                            </label>

                        </div>

                        <div class="col-md-4">

                            <select
                                name="category"
                                class="form-select"
                                onchange="this.form.submit()">

                                <?php foreach ($categories as $key => $value): ?>

                                    <option
                                        value="<?= e($key) ?>"
                                        <?= $category === $key ? "selected" : "" ?>>
                                        <?= e($value) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <?php if ($keyword !== ""): ?>

                            <div class="col-md-auto">

                                <a
                                    href="index.php?category=<?= e($category) ?>"
                                    class="btn btn-outline-danger btn-sm">
                                    Clear Search &times;
                                </a>

                            </div>

                        <?php endif; ?>

                    </div>

                </form>

            </div>

        </div>


        <!-- ERROR -->
        <?php if ($error): ?>

            <div class="alert alert-danger" role="alert">

                <h5 class="alert-heading">
                    Failed to Load News
                </h5>

                <?= e($error) ?>

            </div>


            <!-- EMPTY -->
        <?php elseif (empty($articles)): ?>

            <div class="text-center bg-white rounded shadow-sm p-5">

                <h4 class="fw-bold">
                    No News Found
                </h4>

                <p class="text-secondary">
                    Try another keyword or category.
                </p>

                <a href="index.php" class="btn btn-primary">
                    Back to Home
                </a>

            </div>


            <!-- NEWS CARDS -->
        <?php else: ?>

            <div class="row g-4">

                <?php foreach ($articles as $row): ?>

                    <?php

                    $title = $row["title"] ?? "Untitled News";

                    $author = $row["author"] ?? "Unknown Author";

                    $source = $row["source"]["name"] ?? "Unknown Source";

                    $description = $row["description"]
                        ?? "No description available.";

                    $articleUrl = $row["url"] ?? "#";

                    if (
                        !filter_var($articleUrl, FILTER_VALIDATE_URL) ||
                        !in_array(
                            parse_url($articleUrl, PHP_URL_SCHEME),
                            ["http", "https"]
                        )
                    ) {
                        $articleUrl = "#";
                    }

                    ?>

                    <div class="col-12 col-md-6 col-lg-4">

                        <div class="card news-card h-100 border-0 shadow-sm">

                            <!-- IMAGE -->
                            <div class="news-image">

                                <img
                                    src="<?= e(imageUrl($row["urlToImage"] ?? "")) ?>"
                                    class="card-img-top"
                                    alt="<?= e($title) ?>"
                                    loading="lazy">

                                <span class="badge bg-info text-dark category-badge">
                                    <?= e($categories[$category]) ?>
                                </span>

                            </div>


                            <!-- CARD BODY -->
                            <div class="card-body d-flex flex-column">

                                <div class="d-flex justify-content-between gap-2 mb-2">

                                    <small class="text-primary fw-semibold">
                                        <?= e($source) ?>
                                    </small>

                                    <small class="text-secondary">
                                        <?= e(formatDate($row["publishedAt"] ?? null)) ?>
                                    </small>

                                </div>

                                <h5 class="card-title fw-bold news-title">
                                    <?= e($title) ?>
                                </h5>

                                <p class="card-text text-secondary small news-description">
                                    <?= e($description) ?>
                                </p>

                                <div class="mt-auto">

                                    <hr>

                                    <div class="d-flex justify-content-between align-items-center">

                                        <small class="text-secondary">
                                            By <?= e($author) ?>
                                        </small>

                                        <a
                                            href="<?= e($articleUrl) ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="btn btn-sm btn-outline-primary">
                                            Read More &rarr;
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>

                <nav class="mt-5" aria-label="News pagination">

                    <ul class="pagination justify-content-center">

                        <!-- Previous -->
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">

                            <a class="page-link"
                                href="?<?= http_build_query([
                                            'q' => $keyword,
                                            'category' => $category,
                                            'page' => $page - 1
                                        ]) ?>">
                                Previous
                            </a>

                        </li>

                        <!-- Number -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">

                                <a class="page-link"
                                    href="?<?= http_build_query([
                                                'q' => $keyword,
                                                'category' => $category,
                                                'page' => $i
                                            ]) ?>">
                                    <?= $i ?>
                                </a>

                            </li>

                        <?php endfor; ?>

                        <!-- Next -->
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">

                            <a class="page-link"
                                href="?<?= http_build_query([
                                            'q' => $keyword,
                                            'category' => $category,
                                            'page' => $page + 1
                                        ]) ?>">
                                Next
                            </a>

                        </li>

                    </ul>

                </nav>

            <?php endif; ?>
        <?php endif; ?>

    </main>


    <!-- FOOTER -->
    <footer class="bg-dark text-white py-4" id="about">

        <div class="container text-center">

            <h4 class="fw-bold">
                News<span class="text-info">Hub.</span>
            </h4>

            <p class="text-white-50 small">
                A simple news portal powered by NewsAPI.
            </p>

            <hr class="border-secondary">

            <small class="text-white-50">
                &copy; <?= date("Y") ?> NewsHub. All rights reserved.
            </small>

        </div>

    </footer>


    <!-- Bootstrap JS -->
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>