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

// Add author to book
if (isset($_POST['action']) && $_POST['action'] == 'add_author') {
    $stmt = $pdo->prepare('INSERT INTO book_authors (book_id, author_id) VALUES (:book_id, :author_id)');
    $stmt->execute(['book_id' => $id, 'author_id' => $_POST['author_id']]);
}

// Add custom author
if (isset($_POST['action']) && $_POST['action'] == 'add_custom_author') {
    // Insert the new author into the authors table
    $stmt = $pdo->prepare('INSERT INTO authors (first_name, last_name) VALUES (:first_name, :last_name)');
    $stmt->execute(['first_name' => $_POST['first_name'], 'last_name' => $_POST['last_name']]);
    
    // Get the newly inserted author ID
    $author_id = $pdo->lastInsertId();

    // Associate the new author with the current book
    $stmt = $pdo->prepare('INSERT INTO book_authors (book_id, author_id) VALUES (:book_id, :author_id)');
    $stmt->execute(['book_id' => $id, 'author_id' => $author_id]);

    header("Location: ./book.php?id={$id}");
}

// Get book data
$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

// Get book authors
$stmt = $pdo->prepare('SELECT * FROM book_authors ba LEFT JOIN authors a ON ba.author_id = a.id WHERE ba.book_id = :id');
$stmt->execute(['id' => $id]);

// Get all authors for the dropdown
$authorsStmt = $pdo->prepare('SELECT * FROM authors');
$authorsStmt->execute();
$authors = $authorsStmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 font-sans">

<div class="container mx-auto p-8">
    <h1 class="text-3xl font-semibold text-center text-blue-500 mb-6">Edit Book: <?= htmlspecialchars($book['title']); ?></h1>

    <!-- Edit Book Form -->
    <form action="./edit.php?id=<?= $id; ?>" method="post" class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <div class="mb-4">
            <label for="title" class="block text-gray-600">Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($book['title']); ?>" class="w-full mt-2 p-3 bg-gray-50 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-4">
            <label for="price" class="block text-gray-600">Price:</label>
            <input type="text" name="price" value="<?= htmlspecialchars($book['price']); ?>" class="w-full mt-2 p-3 bg-gray-50 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="text-center">
            <input type="submit" name="action" value="Salvesta" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
        </div>
    </form>

    <!-- Authors List -->
    <div class="mt-8">
        <h2 class="text-2xl font-semibold text-center text-blue-500 mb-4">Authors:</h2>
        <ul class="list-none space-y-3">
            <?php while ($author = $stmt->fetch()) { ?>
                <li class="flex justify-between items-center bg-white p-4 rounded-lg shadow-md">
                    <span class="text-gray-700"><?= htmlspecialchars($author['first_name']); ?> <?= htmlspecialchars($author['last_name']); ?></span>
                    <form action="./edit.php?id=<?= $id; ?>" method="post" class="inline-block">
                        <input type="hidden" name="author_id" value="<?= $author['id']; ?>">
                        <button type="submit" name="action" value="remove_author" class="text-red-500 hover:text-red-600">Remove</button>
                    </form>
                </li>
            <?php } ?>
        </ul>
    </div>

    <!-- Add Author Form -->
    <div class="mt-8">
        <h3 class="text-xl font-semibold text-center text-blue-500 mb-4">Add Author to Book</h3>
        <form action="./edit.php?id=<?= $id; ?>" method="post" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
            <div class="mb-4">
                <label for="author" class="block text-gray-600">Select Author:</label>
                <select name="author_id" required class="w-full mt-2 p-3 bg-gray-50 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">Select an Author</option>
                    <?php foreach ($authors as $author) { ?>
                        <option value="<?= $author['id']; ?>"><?= htmlspecialchars($author['first_name']); ?> <?= htmlspecialchars($author['last_name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            
            <div class="text-center">
                <input type="submit" name="action" value="add_author" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
            </div>
        </form>
    </div>

    <!-- Add Custom Author Form -->
    <div class="mt-8">
        <h3 class="text-xl font-semibold text-center text-blue-500 mb-4">Add Custom Author</h3>
        <form action="./edit.php?id=<?= $id; ?>" method="post" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
            <div class="mb-4">
                <label for="first_name" class="block text-gray-600">First Name:</label>
                <input type="text" name="first_name" required class="w-full mt-2 p-3 bg-gray-50 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="mb-4">
                <label for="last_name" class="block text-gray-600">Last Name:</label>
                <input type="text" name="last_name" required class="w-full mt-2 p-3 bg-gray-50 text-gray-800 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div class="text-center">
                <input type="submit" name="action" value="add_custom_author" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
            </div>
        </form>
    </div>

</div>

</body>
</html>
