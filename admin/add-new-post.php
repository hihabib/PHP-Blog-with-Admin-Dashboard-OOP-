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
$mainMenu = "Post";
$subMenu = "Add New Post";

include_once('inc/header.php');
?>
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Post/</span> Add new post</h4>
<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">New Post Information</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-title">Title</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <input name="postTitle" type="text" class="form-control" id="post-title" placeholder="Post title" aria-label="Post title" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="formFile">Feature image</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <input name="thumbnail" class="form-control" type="file" id="formFile" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="postDetails">Post Details</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <div id="postDetails"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-slug">Slug</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <input name="postSlug" type="text" class="form-control" id="post-slug" placeholder="Post slug" aria-label="Post slug" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-tags">tags</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <input name="posttags" type="text" class="form-control" id="post-tags" placeholder="Post tags have to saparated by comma" aria-label="Post tags have to saparated by comma" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-category">Category</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <select id="post-category" class="form-select">
                                    <?php foreach($category -> getAllCategories() as $singleCategory) : ?>
                                        <option value="<?php echo $singleCategory['category_name']; ?>"><?php echo $singleCategory['category_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-status">Post status</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <select id="post-status" class="form-select">
                                        <option value="published">Published</option>
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