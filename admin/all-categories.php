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
$category = new Category();

// Menu and submenu name
$mainMenu = "Category";
$subMenu = "All Caregories";

include_once('inc/header.php');
?>
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Category/</span> All Categories</h4>
<div class="card">
    <h5 class="card-header">Category List</h5>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr class="text-nowrap">
                    <th>#</th>
                    <th>Category Name</th>
                    <th>Category Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($category -> getAllCategories() as $index => $singleCategory) : ?>
                <tr>
                    <th scope="row"><?php echo $index + 1; ?></th>
                    <td><?php echo $singleCategory['category_name']; ?></td>
                    <td><?php echo $singleCategory['category_status']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once('inc/footer.php'); ?>