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
    <style>
        .bg-light { background-color: #f7fafc; }
        .text-highlight { color: #4a90e2; }
        .btn { display: inline-block; padding: 0.5rem 1rem; font-weight: 500; text-align: center; }
        .btn-green { background-color: #48bb78; color: white; }
        .btn-red { background-color: #e53e3e; color: white; }
        .btn-blue { background-color: #4299e1; color: white; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body class="bg-light text-gray-800 font-sans">

<div class="container mx-auto p-8">
    <h1 class="text-4xl font-bold text-center text-highlight mb-8">Edit Book: <?= htmlspecialchars($book['title']); ?></h1>

    <!-- Edit Book Form -->
    <form action="./edit.php?id=<?= $id; ?>" method="post" class="bg-white shadow-lg rounded-lg p-6 max-w-xl mx-auto">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 font-medium">Title:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($book['title']); ?>" 
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-4">
            <label for="price" class="block text-gray-700 font-medium">Price:</label>
            <input type="text" name="price" value="<?= htmlspecialchars($book['price']); ?>" 
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="text-center">
            <button type="submit" name="action" value="Salvesta" 
                    class="btn btn-green rounded-lg shadow-md transition duration-200">Save Changes</button>
        </div>
    </form>

    <!-- Authors Section -->
    <div class="mt-12">
        <h2 class="text-3xl font-semibold text-center text-highlight mb-6">Authors</h2>
        <ul class="space-y-4">
            <?php while ($author = $stmt->fetch()) { ?>
                <li class="bg-white p-4 rounded-lg shadow-md flex justify-between items-center">
                    <span class="text-lg font-medium"><?= htmlspecialchars($author['first_name']) . " " . htmlspecialchars($author['last_name']); ?></span>
                    <form action="./edit.php?id=<?= $id; ?>" method="post">
                        <input type="hidden" name="author_id" value="<?= $author['id']; ?>">
                        <button type="submit" name="action" value="remove_author" 
                                class="btn btn-red rounded-md shadow-md">Remove</button>
                    </form>
                </li>
            <?php } ?>
        </ul>
    </div>

    <!-- Add Existing Author -->
    <div class="mt-12">
        <h3 class="text-2xl font-semibold text-center text-highlight mb-4">Add Author</h3>
        <form action="./edit.php?id=<?= $id; ?>" method="post" class="bg-white shadow-lg p-6 rounded-lg max-w-xl mx-auto">
            <label for="author" class="block text-gray-700 font-medium">Select Author:</label>
            <select name="author_id" required 
                    class="w-full mt-2 border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Choose an author</option>
                <?php foreach ($authors as $author) { ?>
                    <option value="<?= $author['id']; ?>"><?= htmlspecialchars($author['first_name']) . " " . htmlspecialchars($author['last_name']); ?></option>
                <?php } ?>
            </select>
            <button type="submit" name="action" value="add_author" 
                    class="btn btn-blue rounded-md shadow-md mt-4 w-full">Add Author</button>
        </form>
    </div>

    <!-- Add Custom Author -->
    <div class="mt-12">
        <h3 class="text-2xl font-semibold text-center text-highlight mb-4">Add New Author</h3>
        <form action="./edit.php?id=<?= $id; ?>" method="post" class="bg-white shadow-lg p-6 rounded-lg max-w-xl mx-auto">
            <label for="first_name" class="block text-gray-700 font-medium">First Name:</label>
            <input type="text" name="first_name" required 
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 mb-4">
            
            <label for="last_name" class="block text-gray-700 font-medium">Last Name:</label>
            <input type="text" name="last_name" required 
                   class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 mb-4">

            <button type="submit" name="action" value="add_custom_author" 
                    class="btn btn-blue rounded-md shadow-md w-full">Add Author</button>
        </form>
    </div>
</div>

</body>
</html>
