<?php

require_once('./connection.php');

// Handle form submission to add a new book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $price = floatval($_POST['price']);
    $release_date = $_POST['release_date'];
    $type = htmlspecialchars($_POST['type']);  // Optional field for book type


    // Redirect to the main book list page after adding
    header('Location: index.php'); 
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
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

<div class="container mx-auto p-6 max-w-xl">
    <h1 class="text-4xl font-bold text-center text-highlight mb-8">Add a New Book</h1>

    <!-- Add Book Form -->
    <form method="POST" class="bg-medium p-6 rounded-lg shadow-lg space-y-4">
        <div>
            <label for="title" class="block text-muted">Title:</label>
            <input type="text" name="title" id="title" required class="w-full mt-1 border border-gray-300 bg-white text-dark rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label for="price" class="block text-muted">Price:</label>
            <input type="number" name="price" id="price" step="0.01" required class="w-full mt-1 border border-gray-300 bg-white text-dark rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
            <label for="release_date" class="block text-muted">Release Date:</label>
            <input type="date" name="release_date" id="release_date" required class="w-full mt-1 border border-gray-300 bg-white text-dark rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>


        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 focus:outline-none">Add Book</button>
    </form>

    <div class="mt-6 text-center">
        <a href="index.php" class="bg-red-500 text-white py-2 px-6 rounded-lg hover:bg-red-800 focus:outline-none">🔙Cancel</a>
    </div>
</div>

</body>
</html>
