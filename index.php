<?php

require_once('./connection.php');

$searchQuery = $_GET['search'] ?? '';

$query = 'SELECT * FROM books';
$params = [];

if ($searchQuery) {
    $query .= ' WHERE title LIKE :search';
    $params[':search'] = "%" . $searchQuery . "%";
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
        .bg-light { background-color: #f7fafc; }
        .bg-medium { background-color: #e2e8f0; }
        .text-dark { color: #2d3748; }
        .text-muted { color: #718096; }
        .text-highlight { color: #4a90e2; }
    </style>
</head>
<body class="bg-light font-sans antialiased text-dark">

<div class="container mx-auto p-6">
    <h1 class="text-4xl font-bold text-center text-highlight mb-8">Book List</h1>

    <!-- Search Bar Section -->
    <div class="mb-6 flex justify-center">
        <form method="GET" class="flex space-x-4 w-full max-w-lg">
            <input type="text" name="search" value="<?= htmlspecialchars($searchQuery); ?>" placeholder="Search by title..." class="w-full border border-gray-300 bg-white text-dark rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 focus:outline-none">Search</button>
        </form>
    </div>

    <div class="flex justify-center mb-6">
    <a href="addbook.php" class="bg-green-500 text-white py-2 px-6 rounded-lg hover:bg-green-600 focus:outline-none">Add a New Book</a>
</div>


    <!-- Book List Section -->
    <div>
        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if ($stmt->rowCount() > 0) { ?>
                <?php while ($row = $stmt->fetch()) { ?>
                    <li class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <a href="./book.php?id=<?= $row['id']; ?>" class="text-lg font-semibold text-highlight hover:underline">
                            <?= htmlspecialchars($row['title']); ?>
                        </a>
                        <p class="text-muted mt-2">Price: $<?= number_format($row['price'], 2); ?></p>
                        <p class="text-muted">Released on: <?= date("F j, Y", strtotime($row['release_date'])); ?></p>
                    </li>
                <?php } ?>
            <?php } else { ?>
                <p class="text-muted text-center">No books found matching your search.</p>
            <?php } ?>
        </ul>
    </div>
</div>

</body>
</html>
