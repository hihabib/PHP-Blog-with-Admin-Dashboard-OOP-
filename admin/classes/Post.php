<?php

enum PostStatus:string {
    case PUBLISHED = 'Published';
    case DRAFT = 'Draft';
    case PROTECTED = 'Protected';
}
class Post {
    

    /**
     * Get last serial of user uploaded post's thumbnail
     *
     * @return int
     * 
     */
    public static function getLastThumbnailSerial():int{
        global $connection;
        $tableName = TBL_POST;
        $result = $connection -> select("SELECT thumbnail from {$tableName} ORDER BY id DESC LIMIT 1");
        $thumbnail = $result -> fetch_assoc()['thumbnail'] ?? "0.png";
        $splitedThumnailName = explode(".", $thumbnail);
        $thumbnailSerial = (int)$splitedThumnailName[0];
        return $thumbnailSerial;
    }

    /**
     * Create Blog Post which save data in database
     *
     * @param string $title Post title
     * @param string $content Post content
     * @param string $tags Post tags
     * @param PostStatus $status Post Status
     * @param string $thumbnailName Post thumbnail name with extension
     * @param string $slug Post slug
     * @param int $categoryId Post's category ID
     * @param int $authorId Author ID
     * 
     * @return bool Return true if post created successfully. Otherwise false
     * 
     */
    public static function createPost(string $title, string $content, string $tags, PostStatus $status, string $thumbnailName, string $slug, int $categoryId, int $authorId) : bool{
        global $connection;
        $tableName = TBL_POST;
        $result = $connection -> insert(
            "INSERT INTO $tableName(title, content, tags, status, thumbnail, slug, category_id, author_id) VALUES(?, ?, ?, ?, ?, ?, ?, ?)",
            "ssssssii",
            [$title, $content, $tags, $status->value, $thumbnailName, $slug, $categoryId, $authorId]
        );
        return $result;
    }

    /**
     * Check if the slug is available or not
     *
     * @param string $slug Slug to be checked
     * 
     * @return bool returns true if slug is available, returns false if the slug is not available
     * 
     */
    public static function isSlugAvailable(string $slug):bool{
        global $connection;
        $tableName = TBL_POST;
        $result = $connection -> select(
            "SELECT slug FROM $tableName WHERE slug = ?",
            "s",
            [$slug]
        );
        return !($result->num_rows > 0);
    }
}