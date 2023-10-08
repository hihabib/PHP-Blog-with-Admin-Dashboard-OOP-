<?php
// initialize necessary script
include_once('inc/init.php');

// include category
include_once('classes/Category.php');

// check login. If the user is not logged in user, then redirect the user to the login page
if (false == Session::checkLogin()) {
    header("Location: login.php");
    die();
}
// category object to access all its methods
$category = new Category();

// Edit category on post request
if ("POST" == $_SERVER['REQUEST_METHOD'] && isset($_POST['editCategory'])) {
    $categoryOldName = $_POST['categoryOldName'] ?? "";
    $categoryNewName = $_POST['categoryNewName'] ?? "";
    $categoryStatus = Status::tryFrom($_POST['categoryStatus'] ?? "") ?? Status::tryFrom('inactive');
    $isUpdated = $category->editCategory($categoryOldName, $categoryNewName, $categoryStatus);
}

// delete category if $_GET['delete'] is set
if("GET" == $_SERVER['REQUEST_METHOD'] && isset($_GET['delete'])){
    $categoryName = $_GET['delete'];
    $isAvailable = $category -> getCategory($categoryName) -> isAvailable();
    if(true == $isAvailable){
        $isDeleted = $category -> deleteCategory($categoryName);
    }
}
// Menu and submenu name
$mainMenu = "Category";
$subMenu = "All Caregories";

include_once('inc/header.php');
?>
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Category/</span> All Categories</h4>
<div class="card">
    <?php if(isset($isAvailable) && false == $isAvailable): ?>
    <div class="alert alert-danger alert-dismissible my-3 mx-4" role="alert">
        '<?php echo $categoryName; ?>' category is not available.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    <?php if(isset($isDeleted) && true == $isDeleted): ?>
    <div class="alert alert-success alert-dismissible my-3 mx-4" role="alert">
        Category is deleted successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php elseif(isset($isDeleted) && false == $isDeleted): ?>
    <div class="alert alert-danger alert-dismissible my-3 mx-4" role="alert">
        Category deletion failed.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    <?php if(isset($isUpdated) && true == $isUpdated) : ?>
    <div class="alert alert-success alert-dismissible my-3 mx-4" role="alert">
        Category is updated successfully.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php elseif(isset($isUpdated) && false == $isUpdated): ?>
    <div class="alert alert-danger alert-dismissible my-3 mx-4" role="alert">
        Category updated failed.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    <h5 class="card-header">Category List</h5>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr class="text-nowrap">
                    <th>#</th>
                    <th>Category Name</th>
                    <th>Category Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($category->getAllCategories() as $index => $singleCategory) : ?>
                    <tr>
                        <th scope="row"><?php echo $index + 1; ?></th>
                        <td><?php echo $singleCategory['category_name']; ?></td>
                        <td><?php echo $singleCategory['category_status']; ?></td>
                        <td>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#editModal-<?php echo $index + 1; ?>" class="btn btn-sm btn-info">Edit</button>
                            <div class="modal fade" id="editModal-<?php echo $index + 1; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalTitle-<?php echo $index + 1; ?>">Edit Category</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <div class="row mb-3">
                                                    <label class="col-sm-2 col-form-label" for="category-name">Name</label>
                                                    <div class="col-sm-10">
                                                        <div class="input-group input-group-merge">
                                                            <span id="category-<?php echo $index + 1; ?>-name2" class="input-group-text"><i class='bx bx-category'></i></span>
                                                            <input value="<?php echo $singleCategory['category_name']; ?>" name="categoryNewName" type="text" class="form-control" id="category-<?php echo $index + 1; ?>-name" placeholder="Example: News" aria-label="Example: News" aria-describedby="category-name2" />
                                                            <input value="<?php echo $singleCategory['category_name']; ?>" name="categoryOldName" type="hidden" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label class="col-sm-2 col-form-label" for="category-status">Status</label>
                                                    <div class="col-sm-10">
                                                        <div class="input-group input-group-merge">
                                                            <span id="category-<?php echo $index + 1; ?>-status2" class="input-group-text"><i class='bx bxs-circle'></i></span>
                                                            <select name="categoryStatus" class="form-select" id="category-<?php echo $index + 1; ?>-status" aria-label="Category Status">
                                                                <?php foreach (Status::cases() as $enum) : ?>
                                                                    <option <?php echo $singleCategory['category_status'] == $enum->value ? "selected" : ""; ?> value="<?php echo $enum->value; ?>"><?php echo $enum->value; ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                    Close
                                                </button>
                                                <button type="submit" name="editCategory" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <a href="?delete=<?php echo $singleCategory['category_name']; ?>" class="btn btn-sm btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once('inc/footer.php'); ?>