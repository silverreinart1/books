<?php

require_once('./connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $price = floatval($_POST['price']);
    $release_date = $_POST['release_date'];

    $stmt = $pdo->prepare("INSERT INTO books (title, price, release_date) VALUES (:title, :price, :release_date)");
    $stmt->execute([
        ':title' => $title,
        ':price' => $price,
        ':release_date' => $release_date,
    ]);

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$sortOption = $_GET['sort'] ?? '';
$typeFilter = $_GET['type'] ?? '';

$query = 'SELECT * FROM books';
$params = [];

if ($typeFilter) {
    $query .= ' WHERE type = :type';
    $params[':type'] = $typeFilter;
}

if ($sortOption === 'price_asc') {
    $query .= ' ORDER BY price ASC';
} elseif ($sortOption === 'price_desc') {
    $query .= ' ORDER BY price DESC';
} elseif ($sortOption === 'release_date_asc') {
    $query .= ' ORDER BY release_date ASC';
} elseif ($sortOption === 'release_date_desc') {
    $query .= ' ORDER BY release_date DESC';
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .bg-dark { background-color: #222; }
        .bg-medium { background-color: #777; }
        .text-light { color: #f5f5f5; }
        .text-muted { color: #bbb; }
        .text-highlight { color: #ddd; }
    </style>
</head>
<body class="bg-dark font-sans antialiased text-light">

<div class="container mx-auto p-6">
    <h1 class="text-4xl font-bold text-center text-highlight mb-8">Book List</h1>

    <div class="mb-8">
        <form method="POST" class="bg-medium p-6 rounded-lg shadow-lg space-y-4 max-w-lg mx-auto">
            <h2 class="text-2xl font-bold text-light mb-4">Add a New Book</h2>
            <div>
                <label class="block text-muted">Title</label>
                <input type="text" name="title" required class="w-full mt-1 border border-gray-600 bg-dark text-light rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-gray-500">
            </div>
            <div>
                <label class="block text-muted">Price</label>
                <input type="number" name="price" step="0.01" required class="w-full mt-1 border border-gray-600 bg-dark text-light rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-gray-500">
            </div>
            <div>
                <label class="block text-muted">Release Date</label>
                <input type="date" name="release_date" required class="w-full mt-1 border border-gray-600 bg-dark text-light rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-gray-500">
            </div>
            <button type="submit" class="w-full bg-dark text-light py-2 rounded-lg font-semibold hover:bg-gray-600">Add Book</button>
        </form>
    </div>

    <div class="flex justify-between mb-6">
        <form method="GET" class="flex space-x-4">
            <div>
                <select name="sort" onchange="this.form.submit()" class="border border-gray-600 bg-dark text-light rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <option value="" class="text-muted">Sort by</option>
                    <option value="price_asc" <?= $sortOption === 'price_asc' ? 'selected' : '' ?>>Price (Low to High)</option>
                    <option value="price_desc" <?= $sortOption === 'price_desc' ? 'selected' : '' ?>>Price (High to Low)</option>
                    <option value="release_date_desc" <?= $sortOption === 'release_date_desc' ? 'selected' : '' ?>>Release Date (Newest)</option>
                    <option value="release_date_asc" <?= $sortOption === 'release_date_asc' ? 'selected' : '' ?>>Release Date (Oldest)</option>
                </select>
            </div>
            <div>
                <select name="type" onchange="this.form.submit()" class="border border-gray-600 bg-dark text-light rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <option value="" class="text-muted">Filter by Type</option>
                    <option value="new" <?= $typeFilter === 'new' ? 'selected' : '' ?>>New</option>
                    <option value="old" <?= $typeFilter === 'old' ? 'selected' : '' ?>>Old</option>
                    <option value="discount" <?= $typeFilter === 'discount' ? 'selected' : '' ?>>Discount</option>
                    <option value="not in stock" <?= $typeFilter === 'not in stock' ? 'selected' : '' ?>>Not in Stock</option>
                    <option value="ebook" <?= $typeFilter === 'ebook' ? 'selected' : '' ?>>Ebook</option>
                </select>
            </div>
        </form>
    </div>

    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php while ($row = $stmt->fetch()) { ?>
            <li class="bg-medium rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                <a href="./book.php?id=<?= $row['id']; ?>" class="text-lg font-semibold text-highlight hover:underline">
                    <?= htmlspecialchars($row['title']); ?>
                </a>
                <p class="text-muted mt-2">Price: $<?= number_format($row['price'], 2); ?></p>
                <p class="text-muted">Released on: <?= date("F j, Y", strtotime($row['release_date'])); ?></p>
                <p class="text-muted">Type: <?= htmlspecialchars($row['type']); ?></p>
            </li>
        <?php } ?>
    </ul>
</div>

</body>
</html>
