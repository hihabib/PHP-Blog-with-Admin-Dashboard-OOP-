<?php
// Include necessary scripts and classes
include_once('inc/init.php');
include_once('classes/Category.php');
include_once('classes/Post.php');
include_once('../lib/Utility.php');

// Check if the user is logged in; if not, redirect to the login page
if (false == Session::checkLogin()) {
    header("Location: login.php");
    die();
}

// Create a new Category object
$category = new Category();

// Check if the form has been submitted using the POST method and if the 'submitPost' field is set
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submitPost'])) {
    // Initialize a variable to store errors related to the post thumbnail
    $thumbnailError = "";

    // Sanitize and retrieve the post data
    $title = htmlspecialchars($_POST['postTitle']) ?? "";
    $content = htmlspecialchars($_POST['postDetailsContainer']) ?? "";
    $slug = htmlspecialchars($_POST['postSlug']);
    $tags = htmlspecialchars($_POST['postTags'] ?? "");
    $categoryId = (int)htmlspecialchars($_POST['postCategory'] ?? "");
    $postStatus = PostStatus::tryFrom($_POST["postStatus"] ?? 'Draft') ?? PostStatus::tryFrom("Draft");
    $authorID = (int)Session::get('id');
    $thumbnail = $_FILES['thumbnail']['tmp_name'];
    $extName = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));


    // Define an array of allowed image file extensions
    $allowedExtension = ['png', 'jpg', 'jpeg', 'webp'];

    // Check if the file extension is in the list of allowed extensions
    if (!in_array($extName, $allowedExtension)) {
        $thumbnailError = "File can only contain " . implode(", ", $allowedExtension);
    }
    // Check if the file is a valid image
    else if (!getimagesize($thumbnail)) {
        $thumbnailError = "Please upload an image file only.";
    }

    // If there are no thumbnail errors
    if (empty($thumbnailError)) {
        // Initialize a variable to store potential errors related to post data
        $postDataError = "";

        // Generate a new unique serial number for the thumbnail
        $newThumbnailSerial = Post::getLastThumbnailSerial() + 1;

        // Construct the thumbnail file name with the serial number and extension
        $thumbnailName = $newThumbnailSerial . "." . $extName;

        // Define the directory path where the thumbnail will be saved temporarily
        $tempDirectoryPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . UPLOADS . DIRECTORY_SEPARATOR . POST_THUMBNAIL . DIRECTORY_SEPARATOR . 'temp';

        // Check if the directory exists, and if not, create it
        if (!file_exists($tempDirectoryPath)) {
            mkdir($tempDirectoryPath, 0777, true); // The 'true' parameter creates nested directories if they don't exist
        }

        // Now, construct the complete path to the thumbnail
        $tempThumbnailDir = $tempDirectoryPath . DIRECTORY_SEPARATOR . $thumbnailName;

        // If the slug field is empty, set an error message
        if (empty($slug)) {
            $postDataError = "Slug must not be empty";
        } else {
            // Check slug availability
            if (!Post::isSlugAvailable($slug)) {
                $postDataError = "Slug not available. Please try with another slug";
            } else {
                // Attempt to create the post using the provided data
                $isPostCreated = Post::createPost($title, $content, $tags, $postStatus, $newThumbnailSerial . ".webp", $slug, $categoryId, $authorID);

                // If the post creation was unsuccessful, set an error message
                if (!$isPostCreated) {
                    $postDataError = "Something went wrong. Please try again.";
                } else {
                    // Move the uploaded thumbnail to the specified directory
                    move_uploaded_file($thumbnail, $tempThumbnailDir);

                    // Construct the thumbnail file name with the serial number and webp extension
                    $thumbnailName = $newThumbnailSerial . ".webp";

                    // Paths for different thumbnail sizes
                    $thumbnailSizes = [
                        'blog' => [730, 322], 
                        'feature' => [408, 353], 
                        'banner' => [1580, 300], 
                        'tiny' => [150, 150]
                    ];

                    // Create directories for different thumbnail sizes if they don't exist
                    foreach ($thumbnailSizes as $size => $dimensions) {
                        $thumbnailDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . UPLOADS . DIRECTORY_SEPARATOR . POST_THUMBNAIL . DIRECTORY_SEPARATOR . $size . DIRECTORY_SEPARATOR . $thumbnailName;
                        if (!file_exists(dirname($thumbnailDir))) {
                            mkdir(dirname($thumbnailDir), 0777, true); // The 'true' parameter creates nested directories if they don't exist
                        }
                        Utility::compressAndResizeImage($tempThumbnailDir, $thumbnailDir, $dimensions[0], $dimensions[1]);
                    }

                    // Delete the user uploaded thumbnail
                    unlink($tempThumbnailDir);
                }
            }
        }
    }
}

// Menu and submenu names
$mainMenu = "Post";
$subMenu = "Add New Post";

// Include the header
include_once('inc/header.php');
?>

<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Post/</span> Add new post</h4>
<div class="row">
    <div class="col-xxl">
        <div class="card mb-4">
            <?php if (isset($thumbnailError) && strlen($thumbnailError) > 0) : ?>
                <div class="alert alert-danger alert-dismissible my-3 mx-4" role="alert">
                    <?php echo $thumbnailError; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($postDataError) && strlen($postDataError) > 0) : ?>
                <div class="alert alert-danger alert-dismissible my-3 mx-4" role="alert">
                    <?php echo $postDataError; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($isPostCreated) && false == $isPostCreated) : ?>
                <div class="alert alert-danger alert-dismissible my-3 mx-4" role="alert">
                    <?php echo $postDataError; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (isset($isPostCreated) && true == $isPostCreated) : ?>
                <div class="alert alert-success alert-dismissible my-3 mx-4" role="alert">
                    Post created succeessfully. <strong><a href="<?php echo HOMEPAGE; ?>/<?php echo $slug; ?>">View Post</a></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">New Post Information</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-title">Title</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <input value="<?php echo isset($title) ? $title : ""; ?>" name="postTitle" type="text" class="form-control" id="post-title" placeholder="Post title" aria-label="Post title" />
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
                                <div id="postDetails"><?php echo isset($content) ? html_entity_decode($content) : ""; ?></div>
                                <textarea name="postDetailsContainer" id="postDetailsContainer"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-slug">Slug</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <input value="<?php echo isset($slug) ? $slug : ""; ?>" name="postSlug" type="text" class="form-control" id="post-slug" placeholder="Post slug" aria-label="Post slug" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-tags">tags</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <input value="<?php echo isset($tags) ? $tags : ""; ?>" name="postTags" type="text" class="form-control" id="post-tags" placeholder="Post tags have to saparated by comma" aria-label="Post tags have to saparated by comma" />
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-category">Category</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <select name="postCategory" id="post-category" class="form-select">
                                    <?php foreach ($category->getAllCategories() as $singleCategory) : ?>
                                        <option <?php echo isset($categoryId) && $categoryId == $singleCategory['id'] ? "selected" : ""; ?> value="<?php echo $singleCategory['id']; ?>"><?php echo $singleCategory['category_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="post-status">Post status</label>
                        <div class="col-sm-10">
                            <div class="input-group input-group-merge">
                                <select id="post-status" name="postStatus" class="form-select">
                                    <?php foreach (PostStatus::cases() as $status) : ?>
                                        <option <?php echo isset($postStatus) && $postStatus == $status ? "selected" : ""; ?> value="<?php echo $status->value; ?>"><?php echo $status->value; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-end">
                        <div class="col-sm-10">
                            <button type="submit" name="submitPost" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once('inc/footer.php'); ?>