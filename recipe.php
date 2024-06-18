<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recipes</title>
</head>
<body>
    <header>
        <div>
            <img id="logo" src="IMG/logo.png" alt="Recipe Logo">
            <h1 id="title">RECIPE</h1>
            <nav id="btn">
                <a href="index.php"><button class="buttons">Home</button></a>
                <a href="recipe.php"><button class="buttons">Recipe</button></a>
                <a href="about.php"><button class="buttons">About</button></a>
                <a href="add_recipe.php"><button class="buttons">Add Recipe</button></a>
                <a href="index.php"><button class="buttons">Logout <b>&#x2398;</b></button></a>
                <a href="profile.php"><button class="profile"><img id="prof" src="IMG/char.png" alt="Profile"></button></a>
            </nav>
        </div>
    </header>
    <main class="recipehead">
        <div class="contents">
            <h1>Recipes</h1>

            <?php
            session_start(); // Start the session

            // Display success message if set
            if (isset($_SESSION['success_message'])) {
                echo "<p style='color: green;'>{$_SESSION['success_message']}</p>";
                unset($_SESSION['success_message']); // Clear the message after displaying
            }

            // Establish database connection
            $conn = new mysqli("localhost", "root", "", "recipe_app");

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $sql = "SELECT recipes.id, recipes.recipe_name, users.username AS recipe_owner, recipes.recipe_image, recipes.ingredients, recipes.instructions, categories.name AS category_name 
                    FROM recipes 
                    JOIN categories ON recipes.category_id = categories.id
                    JOIN users ON recipes.recipe_owner = users.id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<ul>";
                while ($row = $result->fetch_assoc()) {
                    // Output each recipe item with anchor links
                    echo "<li id='recipe{$row['id']}'>
                        <h2>{$row['recipe_name']}</h2>
                        <p>by {$row['recipe_owner']}</p>
                        <img class='recipe-image' src='uploads/{$row['recipe_image']}' alt='{$row['recipe_name']}'>
                        <p>Category: {$row['category_name']}</p>
                        <p>Ingredients: " . (!empty($row['ingredients']) ? $row['ingredients'] : "No ingredients provided") . "</p>
                        <p>Instructions: " . (!empty($row['instructions']) ? $row['instructions'] : "No instructions provided") . "</p>
                    </li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No recipes found.</p>";
            }

            // Close database connection
            $conn->close();
            ?>
        </div>
    </main>
    <footer></footer>
</body>
</html>
