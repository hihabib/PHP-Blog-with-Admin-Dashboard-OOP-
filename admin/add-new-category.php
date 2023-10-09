<?php
// Initialize necessary script
include_once('inc/init.php');

// Include the Category class
include_once("classes/Category.php");

// Check if the user is logged in. If not, redirect them to the login page.
if (false == Session::checkLogin()) {
    header("Location: login.php");
    die(); // Terminate script execution after redirect.
}

// Define main menu and submenu names for display purposes.
$mainMenu = "Category";
$subMenu = "Add New Category";

// Include the header file, which likely contains HTML structure up to this point.
include_once('inc/header.php');

$error = ""; // Initialize an error message variable.

// Check if the HTTP request method is POST (form submission).
if ("POST" == $_SERVER['REQUEST_METHOD']) {
    // Retrieve and sanitize the 'categoryName' and 'categoryStatus' POST data.
    $categoryName = $_POST['categoryName'] ?? ""; // Using the null coalescing operator to provide a default value.
    $categoryStatus = CategoryStatus::tryFrom($_POST['categoryStatus'] ?? "inactive") ?? CategoryStatus::tryFrom('inactive'); // Setting a default value if 'categoryStatus' is not provided.

    // Check if the 'categoryName' is empty. If so, set an error message.
    if (strlen($categoryName) <= 0) {
        $error = "Please enter category name";
    } else {
        // Create an instance of the Category class.
        $category = new Category();

        // Attempt to add a new category with the provided information.
        $addedCategory = $category->addCategory($categoryName, $categoryStatus);

        // Check the result of adding the category.
        if (is_object($addedCategory)) {
            // If an object is returned, it likely contains an error message.
            $error = $addedCategory->message;
        } else if (!$addedCategory) {
            // If the result is false, something went wrong during category addition.
            $error = "Something is wrong. Please try again";
        }
    }
}
?>
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Category/</span> Add new category</h4>
<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <?php if(strlen($error) > 0): ?>
                <div class="alert alert-danger alert-dismissible my-3 mx-4" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if( isset($addedCategory) && is_bool($addedCategory) && true == $addedCategory) : ?>
                <div class="alert alert-success alert-dismissible my-3 mx-4" role="alert">
                    Category Added Successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">New Category Information</h5>
                <!-- <small class="text-muted float-end">Merged input group</small> -->
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="category-name">Category Name</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="category-name2" class="input-group-text"><i class='bx bx-category'></i></span>
                                <input name="categoryName" type="text" class="form-control" id="category-name" placeholder="Example: News" aria-label="Example: News" aria-describedby="category-name2" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="category-status">Category Status</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="category-status2" class="input-group-text"><i class='bx bxs-circle'></i></span>
                                <select name="categoryStatus" class="form-select" id="category-status" aria-label="Category Status">
                                    <?php foreach(CategoryStatus::cases() as $enum): ?>
                                        <option value="<?php echo $enum->value; ?>"><?php echo $enum->value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once('inc/footer.php'); ?>