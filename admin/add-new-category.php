<?php
// initialize necessary script
include_once('inc/init.php');

// check login. If the user is not logged in user, then redirect the user to the login page
if (false == Session::checkLogin()) {
    header("Location: login.php");
    die();
}

// Menu and submenu name
$mainMenu = "Category";
$subMenu = "Add New";

include_once('inc/header.php');
?>
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Category/</span> Add new category</h4>
<div class="row">
    <!-- Basic with Icons -->
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">New Category Information</h5>
                <!-- <small class="text-muted float-end">Merged input group</small> -->
            </div>
            <div class="card-body">
                <form>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="category-name">Category Name</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="category-name2" class="input-group-text"><i class='bx bx-category'></i></span>
                                <input type="text" class="form-control" id="category-name" placeholder="Example: News" aria-label="Example: News" aria-describedby="category-name2" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="category-status">Category Status</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <span id="category-status2" class="input-group-text"><i class='bx bxs-circle'></i></span>
                                <select class="form-select" id="category-status" aria-label="Active">
                                    <option selected="" value="active">Active</option>
                                    <option value="inactive">Inactive</option>
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