<?php

require_once('./connection.php');

$id = $_GET['id'];

// Update book data
if (isset($_POST['action']) && $_POST['action'] == 'Salvesta') {
    $stmt = $pdo->prepare('UPDATE books SET title = :title, price = :price WHERE id = :id');
    $stmt->execute(['id' => $id, 'title' => $_POST['title'], 'price' => $_POST['price']]);

    header("Location: ./book.php?id={$id}");
}

// Remove author from book
if (isset($_POST['action']) && $_POST['action'] == 'remove_author') {
    $stmt = $pdo->prepare('DELETE FROM book_authors WHERE book_id = :book_id AND author_id = :author_id');
    $stmt->execute(['book_id' => $id, 'author_id' => $_POST['author_id']]);

    header("Location: ./book.php?id={$id}");
}

// Delete book
if (isset($_POST['action']) && $_POST['action'] == 'delete_book') {
    $stmt = $pdo->prepare('DELETE FROM book_authors WHERE book_id = :book_id');
    $stmt->execute(['book_id' => $id]);

    $stmt = $pdo->prepare('DELETE FROM books WHERE id = :id');
    $stmt->execute(['id' => $id]);

    header("Location: ./index.php");
}

// Get book data
$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

// Get book authors
$stmt = $pdo->prepare('SELECT * FROM book_authors ba LEFT JOIN authors a ON ba.author_id = a.id WHERE ba.book_id = :id');
$stmt->execute(['id' => $id]);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }
        .hover-scale {
            transition: transform 0.2s ease-in-out;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

<div class="container mx-auto p-6 bg-white shadow-lg rounded-lg mt-10">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">
        📖 Book Details: <span class="text-blue-500"><?= htmlspecialchars($book['title']); ?></span>
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Book Info -->
        <div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Title:</label>
                <p class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($book['title']); ?></p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Price:</label>
                <p class="text-lg text-green-600 font-semibold">$<?= number_format($book['price'], 2); ?></p>
            </div>

            <div class="mt-6">
                <a href="edit.php?id=<?= $id; ?>" class="w-full bg-yellow-500 text-white py-2 px-6 rounded-lg hover:bg-yellow-600 focus:outline-none hover-scale">
                    ✏️ Edit Book
                </a>
            </div>
        </div>

        <!-- Authors Section -->
        <div>
            <h2 class="text-xl font-bold mb-4">Authors:</h2>
            <ul class="space-y-4">
                <?php while ($author = $stmt->fetch()) { ?>
                    <li class="flex justify-between items-center bg-gray-50 p-4 rounded-md shadow-sm">
                        <span class="text-lg"><?= htmlspecialchars($author['first_name']); ?> <?= htmlspecialchars($author['last_name']); ?></span>
                        <form action="./book.php?id=<?= $id; ?>" method="post" class="inline">
                            <input type="hidden" name="author_id" value="<?= $author['id']; ?>">
                            <button type="submit" name="action" value="remove_author" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 flex items-center hover-scale">
                                🗑️ Remove
                            </button>
                        </form>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <!-- Delete Book Button -->
    <div class="mt-10 text-center">
        <form action="./book.php?id=<?= $id; ?>" method="post">
            <input type="hidden" name="action" value="delete_book">
            <button type="submit" class="bg-red-600 text-white py-3 px-8 rounded-lg hover:bg-red-700 hover-scale">
                🗑️ Delete Book
            </button>
        </form>
    </div>

    <!-- Back to Index Button -->
    <div class="mt-6 text-center">
        <a href="index.php" class="bg-blue-600 text-white py-3 px-8 rounded-lg hover:bg-blue-700 hover-scale">
            🔙 Back to Books
        </a>
    </div>

</div>

</body>
</html>
