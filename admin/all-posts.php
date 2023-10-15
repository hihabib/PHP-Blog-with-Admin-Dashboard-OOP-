<?php
// initialize necessary script
include_once('inc/init.php');

// include post
include_once('classes/Post.php');

// check login. If the user is not logged in user, then redirect the user to the login page
if (false == Session::checkLogin()) {
    header("Location: login.php");
    die();
}

// Menu and submenu name
$mainMenu = "Post";
$subMenu = "All Posts";

include_once('inc/header.php');
?>
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Category/</span> All Categories</h4>
<div class="card">
    <?php if(false): ?>
    <div class="alert alert-danger alert-dismissible my-3 mx-4" role="alert">
        '<?php echo $categoryName; ?>' category is not available.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>
    <h5 class="card-header">Category List</h5>
    <div class="table-responsive text-nowrap">
        <table class="table">
            <thead>
                <tr class="text-nowrap">
                    <th>#</th>
                    <th>Thumbnail</th>
                    <th>Title</th>
                    <th>Tags</th>
                    <th>Status</th>
                    <th>Slug</th>
                    <th>Created</th>
                    <th>Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (Post::getAllPost(Session::get('id')) as $index => $post) :
                    $thumbnailUrl = HOMEPAGE . "/" . UPLOADS . "/"  . POST_THUMBNAIL . "/"  . "tiny" . "/" . $post['thumbnail']; 
                    ?>
                    <tr>
                        <th scope="row"><?php echo $index + 1; ?></th>
                        <td><img width="60" height="60" src="<?php echo $thumbnailUrl; ?>" alt="<?php echo $post['title']; ?>"></td>
                        <td><?php echo $post['title']; ?></td>
                        <td><?php echo $post['tags']; ?></td>
                        <td><?php echo $post['status']; ?></td>
                        <td><?php echo $post['slug']; ?></td>
                        <td><?php echo date("F jS, Y", strtotime($post['created_at'])); ?></td>
                        <td><?php echo date("F jS, Y", strtotime($post['updated_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include_once('inc/footer.php'); ?>