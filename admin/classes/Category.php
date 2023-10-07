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
     * @return bool|Object If category added successfully, then returns true. If cannot
     * add category in database, then it will return false. If the category is already exists, it will return a error object which contain $messsage and $errCode property
     */
    public function addCategory($catName, Status $catStatus){
        $result = $this -> database -> select(
            "SELECT * FROM categories WHERE category_name = ?",
            "s",
            [$catName]
        );
        //check if the category is not exists
        if($result -> num_rows < 0) {
            return $this -> database -> insert(
                "INSERT INTO categories (category_name, category_status) VALUES(?, ?)",
                "sss",
                [$catName, $catStatus->value, $catName]
            );

        } else {
            return new Class {
                public $message = "Category Already Exists";
                public $errCode = 401;
            };
        }
    }

    /**
     * List of all categories
     * 
     * @return array|false Returns Array of categories if query successfully executes. Otherwise return false
     */
    public function getAllCategories(){
        $result = $this -> database -> select("SELECT category_name, category_status FROM categories");
        if(false != $result){
            return $result -> fetch_all(MYSQLI_ASSOC) ?? [];
        } else {
            return false;
        }
    }
 }