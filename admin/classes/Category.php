<?php
 include_once("../lib/Database.php");

 /**
  * enum Status
  *
  */
  enum Status:string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
  }

/**
 * class Category
 * 
 * The class provide methods for manupulate categories of blog post
 */

 class Category {
    /**
     * @var Database The Database object
     */
    protected $database;

    public function __construct()
    {
        $this -> database = new Database();
    }

    /**
     * Add new category to the database
     * 
     * @param string $catName New category name
     * @param Status $catStatus new category status (ACCEPT "active" or "inactive" only)
     * 
     * @return bool If category added successfully, then returns true. Otherwise false.
     */
    public function addCategory($catName, Status $catStatus){
        return $this -> database -> insert(
            "INSERT INTO categories (category_name, category_status) VALUES(?, ?)",
            "ss",
            [$catName, $catStatus->value]
        );
    }
 }