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
    // First, remove all authors associated with the book
    $stmt = $pdo->prepare('DELETE FROM book_authors WHERE book_id = :book_id');
    $stmt->execute(['book_id' => $id]);

    // Then delete the book itself
    $stmt = $pdo->prepare('DELETE FROM books WHERE id = :id');
    $stmt->execute(['id' => $id]);

    header("Location: ./index.php"); // Redirect to the main page after deletion
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
    <title>Edit Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-6 bg-white shadow-lg rounded-lg mt-10">
    <h1 class="text-2xl font-semibold text-center mb-6">Book Details: <?= htmlspecialchars($book['title']); ?></h1>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Title:</label>
        <p class="text-lg"><?= htmlspecialchars($book['title']); ?></p>
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Price:</label>
        <p class="text-lg"><?= htmlspecialchars($book['price']); ?></p>
    </div>

    <div class="mb-6">
        <a href="edit.php?id=<?= $id; ?>" class="w-full bg-yellow-500 text-white py-2 rounded-md hover:bg-yellow-600 text-center inline-block">
            Edit Book
        </a>
    </div>

    <div>
        <h2 class="text-xl font-semibold mb-4">Authors:</h2>
        <ul class="space-y-4">
            <?php while ($author = $stmt->fetch()) { ?>
                <li class="flex justify-between items-center bg-gray-50 p-4 rounded-md shadow-sm">
                    <span class="text-lg"><?= htmlspecialchars($author['first_name']); ?> <?= htmlspecialchars($author['last_name']); ?></span>
                    <form action="./book.php?id=<?= $id; ?>" method="post" class="inline">
                        <input type="hidden" name="author_id" value="<?= $author['id']; ?>">
                        <button type="submit" name="action" value="remove_author" class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" class="mr-2">
                                <path d="M 10.806641 2 C 10.289641 2 9.7956875 2.2043125 9.4296875 2.5703125 L 9 3 L 4 3 A 1.0001 1.0001 0 1 0 4 5 L 20 5 A 1.0001 1.0001 0 1 0 20 3 L 15 3 L 14.570312 2.5703125 C 14.205312 2.2043125 13.710359 2 13.193359 2 L 10.806641 2 z M 4.3652344 7 L 5.8925781 20.263672 C 6.0245781 21.253672 6.877 22 7.875 22 L 16.123047 22 C 17.121047 22 17.974422 21.254859 18.107422 20.255859 L 19.634766 7 L 4.3652344 7 z"></path>
                            </svg>
                            Remove
                        </button>
                    </form>
                </li>
            <?php } ?>
        </ul>
    </div>

    <!-- Delete Book Button -->
    <div class="mt-6 text-center">
        <form action="./book.php?id=<?= $id; ?>" method="post">
            <input type="hidden" name="action" value="delete_book">
            <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-md hover:bg-red-700">
                Delete Book
            </button>
        </form>
    </div>
</div>

</body>
</html>
