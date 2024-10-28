<?php

require_once('./connection.php');

$id = $_GET['id'];

$stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
$stmt->execute(['id' => $id]);
$book = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['new_price'])) {
        $new_price = floatval($_POST['new_price']);
        $updateStmt = $pdo->prepare('UPDATE books SET price = :price WHERE id = :id');
        $updateStmt->execute(['price' => $new_price, 'id' => $id]);

        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }

    if (isset($_POST['update_details'])) {
        $new_pages = intval($_POST['pages']);
        $new_summary = $_POST['summary'];
        $new_type = $_POST['type'];

        $updateStmt = $pdo->prepare('UPDATE books SET pages = :pages, summary = :summary, type = :type WHERE id = :id');
        $updateStmt->execute(['pages' => $new_pages, 'summary' => $new_summary, 'type' => $new_type, 'id' => $id]);

        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $id);
        exit;
    }

    if (isset($_POST['delete'])) {
        $deleteStmt = $pdo->prepare('DELETE FROM books WHERE id = :id');
        $deleteStmt->execute(['id' => $id]);

        header("Location: books.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-lg rounded-lg p-8 max-w-lg w-full">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 text-center">
            <?= htmlspecialchars($book['title']); ?>
        </h1>
        
        <?php if (!empty($book['cover_path'])): ?>
            <div class="mb-6">
                <img src="<?= htmlspecialchars($book['cover_path']); ?>" alt="Book Cover" class="w-full h-auto rounded-lg">
            </div>
        <?php endif; ?>

        <div class="space-y-4">
            <p><span class="font-semibold text-gray-700">Language:</span> <?= htmlspecialchars($book['language']); ?></p>            
            <p><span class="font-semibold text-gray-700">Release Date:</span> <?= htmlspecialchars($book['release_date']); ?></p>
            <p><span class="font-semibold text-gray-700">Price:</span> $<?= htmlspecialchars($book['price']); ?></p>
            <p><span class="font-semibold text-gray-700">Stock Available:</span> <?= htmlspecialchars($book['stock_saldo']); ?></p>
            <p><span class="font-semibold text-gray-700">Pages:</span> <?= htmlspecialchars($book['pages']); ?></p>
            <p><span class="font-semibold text-gray-700">Type:</span> <?= htmlspecialchars($book['type']); ?></p>
            <p><span class="font-semibold text-gray-700">Summary:</span> <?= htmlspecialchars($book['summary']); ?></p>
        </div>
        
        <div class="mt-6">
            <form method="POST" class="flex items-center space-x-3">
                <input type="number" name="new_price" step="0.01" placeholder="New Price" required
                       class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg font-semibold hover:bg-blue-700">
                    Update Price
                </button>
            </form>
        </div>

        <div class="mt-6">
            <form method="POST" class="space-y-4">
                <input type="number" name="pages" value="<?= htmlspecialchars($book['pages']); ?>" placeholder="Pages"
                       class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">

                <textarea name="summary" placeholder="Summary" required
                          class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($book['summary']); ?></textarea>

                <select name="type" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="new" <?= $book['type'] === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="discount" <?= $book['type'] === 'discount' ? 'selected' : ''; ?>>Discount</option>
                    <option value="old" <?= $book['type'] === 'old' ? 'selected' : ''; ?>>Old</option>
                    <option value="not in stock" <?= $book['type'] === 'not in stock' ? 'selected' : ''; ?>>Not in Stock</option>
                </select>

                <button type="submit" name="update_details" class="w-full bg-green-600 text-white py-2 rounded-lg font-semibold hover:bg-green-700">
                    Update Details
                </button>
            </form>
        </div>

        <div class="mt-6">
            <form method="POST">
                <input type="hidden" name="delete" value="true">
                <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg font-semibold hover:bg-red-700">
                    Delete Book
                </button>
            </form>
        </div>
    </div>

</body>
</html>
