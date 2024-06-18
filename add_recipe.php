<?php
session_start(); // Start the session

// Establish database connection
$conn = new mysqli("localhost", "root", "", "recipe_app");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$recipe_name = "";
$recipe_owner = "";
$category_id = "";
$new_category = "";
$ingredients = "";
$instructions = "";
$error_message = "";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize inputs
    $recipe_name = trim($_POST['recipe_name']);
    $recipe_owner = intval($_POST['recipe_owner']);
    $category_id = intval($_POST['category_id']);
    $new_category = trim($_POST['new_category']);
    $ingredients = trim($_POST['ingredients']);
    $instructions = trim($_POST['instructions']);

    // Check if a new category was provided
    if (!empty($new_category)) {
        // Insert new category into the database
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $new_category);
        if ($stmt->execute()) {
            $category_id = $stmt->insert_id; // Get the ID of the newly inserted category
        } else {
            $error_message = "Error adding new category: " . $stmt->error;
        }
        $stmt->close();
    }

    // If no errors, proceed to handle file upload and insert recipe
    if (empty($error_message)) {
        // Handle file upload
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["recipe_image"]["name"]);

        // Check file upload success
        if (move_uploaded_file($_FILES["recipe_image"]["tmp_name"], $target_file)) {
            $recipe_image = basename($_FILES["recipe_image"]["name"]);

            // Use prepared statements to prevent SQL injection
            $sql = "INSERT INTO recipes (recipe_name, recipe_owner, recipe_image, category_id, ingredients, instructions) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $recipe_name, $recipe_owner, $recipe_image, $category_id, $ingredients, $instructions);

            // Execute the statement
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Recipe added successfully!";
                header('Location: recipe.php');
                exit();
            } else {
                $error_message = "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error_message = "Error uploading file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Recipe</title>
    <link rel="stylesheet" href="style.css">
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
            <h1>Add Recipe</h1>
            <?php
            if (!empty($error_message)) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            ?>
            <form action="add_recipe.php" method="post" enctype="multipart/form-data">
                <label for="recipe_name">Recipe Name:</label>
                <input type="text" id="recipe_name" name="recipe_name" value="<?php echo htmlspecialchars($recipe_name); ?>" required>
                
                <label for="recipe_owner">Recipe Owner:</label>
                <select id="recipe_owner" name="recipe_owner" required>
                    <?php
                    $sql = "SELECT id, username FROM users";
                    $result = $conn->query($sql);

                    while($row = $result->fetch_assoc()) {
                        $selected = ($row['id'] == $recipe_owner) ? "selected" : "";
                        echo "<option value='{$row['id']}' $selected>{$row['username']}</option>";
                    }
                    ?>
                </select>

                <label for="recipe_image">Recipe Image:</label>
                <input type="file" id="recipe_image" name="recipe_image" accept="image/*" required>
                
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <?php
                    $sql = "SELECT id, name FROM categories";
                    $result = $conn->query($sql);

                    while($row = $result->fetch_assoc()) {
                        $selected = ($row['id'] == $category_id) ? "selected" : "";
                        echo "<option value='{$row['id']}' $selected>{$row['name']}</option>";
                    }
                    ?>
                    <option value="new">Add New Category</option>
                </select>

                <div id="new_category_input" style="display: none;">
                    <label for="new_category">New Category:</label>
                    <input type="text" id="new_category" name="new_category" placeholder="Enter new category">
                </div>
                
                <label for="ingredients">Ingredients:</label>
                <textarea id="ingredients" name="ingredients" rows="4" required><?php echo htmlspecialchars($ingredients); ?></textarea>
                
                <label for="instructions">Instructions:</label>
                <textarea id="instructions" name="instructions" rows="6" required><?php echo htmlspecialchars($instructions); ?></textarea>
                
                <button type="submit" id="submitbtn">Submit Recipe</button>
            </form>
        </div>
    </main>
    <footer></footer>

    <script>
        document.getElementById('category_id').addEventListener('change', function () {
            var newCategoryInput = document.getElementById('new_category_input');
            if (this.value === 'new') {
                newCategoryInput.style.display = 'block';
            } else {
                newCategoryInput.style.display = 'none';
            }
        });
    </script>
</body>
</html>
