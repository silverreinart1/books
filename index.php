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
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .bg-light { background-color: #f7fafc; }
        .text-highlight { color: #4a90e2; }
        .hover-scale {
            transition: transform 0.2s ease-in-out;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
        
        /* Slide-in animation */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800">

<div class="container mx-auto p-6 text-center">
    <h1 class="text-5xl font-bold text-white mb-8">📚 Explore Your Next Read</h1>

    <!-- Search Bar Section -->
    <div class="mb-6 flex justify-center">
        <form method="GET" class="flex space-x-4 w-full max-w-lg">
            <input type="text" name="search" value="<?= htmlspecialchars($searchQuery); ?>" placeholder="Search by title..." class="w-full border border-gray-300 bg-white text-gray-800 rounded-lg py-2 px-4 focus:outline-none focus:ring-2 focus:ring-blue-400 shadow-lg">
            <button type="submit" class="bg-green-500 text-white py-2 px-6 rounded-lg hover:bg-green-600 focus:outline-none transition">Search</button>
            <a href="?" class="bg-red-500 text-white py-2 px-6 rounded-lg hover:bg-red-600 focus:outline-none transition">Clear</a>
        </form>
    </div>

    <!-- Add Book Section -->
    <div class="flex justify-center mb-6">
        <a href="addbook.php" class="bg-gradient-to-r from-green-400 to-blue-500 text-white py-3 px-8 rounded-lg hover:from-green-500 hover:to-blue-600 focus:outline-none transition-transform transform hover:scale-105">+ Add a New Book</a>
    </div>

    <!-- Book List Section -->
    <div>
        <?php if ($stmt->rowCount() > 0) { ?>
            <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ($row = $stmt->fetch()) { ?>
                    <li class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 slide-in">
                        <a href="./book.php?id=<?= $row['id']; ?>" class="text-xl font-semibold text-highlight hover:underline">
                            <?= htmlspecialchars($row['title']); ?>
                        </a>
                        <div class="mt-2">
                            <span class="bg-blue-100 text-blue-800 text-sm font-medium mr-2 px-2.5 py-0.5 rounded">Price: $<?= number_format($row['price'], 2); ?></span>
                            <span class="bg-gray-100 text-gray-800 text-sm font-medium px-2.5 py-0.5 rounded">Released: <?= date("F j, Y", strtotime($row['release_date'])); ?></span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Find out more details about this book.</p>
                    </li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <div class="flex flex-col items-center mt-10">
                <p class="text-lg text-white bg-red-500 p-4 rounded-lg shadow-md">
                    No books found matching your search.
                </p>
                <a href="addbook.php" class="bg-gradient-to-r from-green-400 to-blue-500 text-white py-3 px-8 mt-4 rounded-lg hover:from-green-500 hover:to-blue-600 focus:outline-none transition-transform transform hover:scale-105">
                    + Add Your First Book
                </a>
            </div>
        <?php } ?>
    </div>
</div>

</body>
</html>
