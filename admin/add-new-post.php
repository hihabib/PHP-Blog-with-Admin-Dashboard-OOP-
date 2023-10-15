<?php
// Initialize necessary script
include_once('inc/init.php');

// Include necessary classes
include_once('classes/Category.php');
include_once('classes/Post.php');
include_once('../lib/Utility.php');

// Check if the user is logged in; if not, redirect to the login page
if (false == Session::checkLogin()) {
    header("Location: login.php");
    die();
}

// Create a new Category object (assuming the Category class is defined elsewhere)
$category = new Category();

// Check if the form has been submitted using the POST method and if the 'submitPost' field is set
if ("POST" == $_SERVER['REQUEST_METHOD'] && isset($_POST['submitPost'])) {
    // Initialize a variable to store errors related to the post thumbnail
    $thumbnailError = "";

    // Get and sanitize the post title from the form data
    $title = htmlspecialchars($_POST['postTitle']) ?? "";

    // Get and sanitize the post content from the form data
    $content = htmlspecialchars($_POST['postDetailsContainer']) ?? "";

    // Get and sanitize the post slug from the form data
    $slug = htmlspecialchars($_POST['postSlug']);

    // Get and sanitize the post tags from the form data (if provided)
    $tags = htmlspecialchars($_POST['postTags'] ?? "");

    // Get the selected category ID from the form data and ensure it's an integer
    $categoryId = (int)htmlspecialchars($_POST['postCategory'] ?? "");

    // Determine the post status from the form data, defaulting to 'Draft'
    $postStatus = PostStatus::tryFrom($_POST["postStatus"] ?? 'Draft') ?? PostStatus::tryFrom("Draft");

    // Get the author's ID from the session (assuming a Session class is used)
    $authorID = (int)Session::get('id');

    // Get the temporary location of the uploaded thumbnail image
    $thumbnail = $_FILES['thumbnail']['tmp_name'];

    // Split the file name by dots to extract the file extension
    $splitedName = explode(".", $_FILES['thumbnail']['name']);
    $extName = strtolower(end($splitedName));

    // Define an array of allowed image file extensions
    $allowedExtension = ['png', 'jpg', 'jpeg', 'webp'];

    // Check if the file extension is in the list of allowed extensions
    if (!in_array($extName, $allowedExtension)) {
        $thumbnailError = "File can only contain" . implode(", ", $allowedExtension);
    }
    // Check if the file is a valid image
    else if (!getimagesize($thumbnail)) {
        $thumbnailError = "Please upload an image file only.";
    }

    // If there are no thumbnail errors
    if (!strlen($thumbnailError) > 0) {
        // Initialize a variable to store potential errors related to post data
        $postDataError = "";

        // Generate a new unique serial number for the thumbnail
        $newThumbnailSerial = Post::getLastThumbnailSerial() + 1;

        // Construct the thumbnail file name with the serial number and extension
        $thumbnailName = $newThumbnailSerial . "." . $extName;

        // Define the directory path where the thumbnail will be saved temporary
        $tempDirectoryPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . UPLOADS . DIRECTORY_SEPARATOR . POST_THUMBNAIL . DIRECTORY_SEPARATOR . 'temp';

        // Check if the directory exists, and if not, create it
        if (!file_exists($tempDirectoryPath)) {
            mkdir($tempDirectoryPath, 0777, true); // The 'true' parameter creates nested directories if they don't exist
        }

        // Now, construct the complete path to the thumbnail
        $tempThumbnailDir = $tempDirectoryPath . DIRECTORY_SEPARATOR . $thumbnailName;
        // If the slug field is empty, set an error message
        if (!strlen($slug) > 0) {
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

                    // Path for different sizes
                    $featureThumbnailDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . UPLOADS . DIRECTORY_SEPARATOR . POST_THUMBNAIL . DIRECTORY_SEPARATOR . 'feature' . DIRECTORY_SEPARATOR . $thumbnailName;

                    if (!file_exists(dirname($featureThumbnailDir))) {
                        mkdir(dirname($featureThumbnailDir), 0777, true); // The 'true' parameter creates nested directories if they don't exist
                    }

                    $blogThumbnailDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . UPLOADS . DIRECTORY_SEPARATOR . POST_THUMBNAIL . DIRECTORY_SEPARATOR . 'blog' . DIRECTORY_SEPARATOR . $thumbnailName;

                    if (!file_exists(dirname($blogThumbnailDir))) {
                        mkdir(dirname($blogThumbnailDir), 0777, true); // The 'true' parameter creates nested directories if they don't exist
                    }


                    $bannerThumbnailDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . UPLOADS . DIRECTORY_SEPARATOR . POST_THUMBNAIL . DIRECTORY_SEPARATOR . 'banner' . DIRECTORY_SEPARATOR . $thumbnailName;

                    if (!file_exists(dirname($bannerThumbnailDir))) {
                        mkdir(dirname($bannerThumbnailDir), 0777, true); // The 'true' parameter creates nested directories if they don't exist
                    }


                    $tinyThumbnailDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . UPLOADS . DIRECTORY_SEPARATOR . POST_THUMBNAIL . DIRECTORY_SEPARATOR . 'tiny' . DIRECTORY_SEPARATOR . $thumbnailName;

                    if (!file_exists(dirname($tinyThumbnailDir))) {
                        mkdir(dirname($tinyThumbnailDir), 0777, true); // The 'true' parameter creates nested directories if they don't exist
                    }

                    // compreess image
                    if (file_exists($tempThumbnailDir)) {

                        // create Blog Thumbnail with width: 730px; height: 322px
                        Utility::compressAndResizeImage($tempThumbnailDir, $blogThumbnailDir, 730, 322);
                        
                        // create Feature Thumbnail with width: 408px; height: 353px
                        Utility::compressAndResizeImage($tempThumbnailDir,  $featureThumbnailDir, 408, 353);
                        
                        // create Banner Thumbnail with width: 1580px; height: 300px
                        Utility::compressAndResizeImage($tempThumbnailDir,  $bannerThumbnailDir, 1580, 300);
                        
                        // create Tiny Thumbnail with width: 150px; height: 150px
                        Utility::compressAndResizeImage($tempThumbnailDir,  $tinyThumbnailDir, 150, 150);

                        // delete user uploaded thumbnail
                        unlink($tempThumbnailDir);
                    }
                }
            }
        }
    }
}

// Menu and submenu names
$mainMenu = "Post";
$subMenu = "Add New Post";

// include header
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