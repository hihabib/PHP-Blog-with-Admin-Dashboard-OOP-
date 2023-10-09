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
     * Category table name
     *
     * @var string
     */
    public string $tableName;

    public function __construct()
    {
        $this -> tableName = TBL_CATEGORY;
    }

    /**
     * Category details by category name
     *
     * @param string $catName Category name
     * 
     * @return object|bool Return false if no category found. Return object if found. Methods for the returned object are `isAvailable`
     * 
     */
    public function getCategory(string $catName):object|bool{
        global $connection;
        $result = $connection -> select(
            "SELECT * FROM {$this -> tableName} WHERE category_name = ?",
            "s",
            [$catName]
        );
        if(is_object($result)){
            return new class($result){
                private object $result;
                public function __construct($result)
                {
                    $this -> result = $result;
                }
                /**
                 * Get category availability
                 *
                 * @return bool true if category is available. Otherwise false
                 * 
                 */
                public function isAvailable():bool{
                    return $this -> result -> num_rows > 0;
                }
            };
        } else {
            return false;
        }
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
        $isAvailable = $this -> getCategory($catName) -> isAvailable();
        //check if the category is not exists
        if(false == $isAvailable) {
            global $connection;
            return $connection -> insert(
                "INSERT INTO {$this -> tableName} (category_name, category_status) VALUES(?, ?)",
                "ss",
                [$catName, $catStatus->value]
            );

        } else {
            return new Class {
                public $message = "Category Already Exists";
                public $errCode = 401;
            };
        }
    }

    /**
     * Edit existing category name
     *
     * @param string $oldCatName Old category name to select the category which one to be edited
     * @param string $newCatName Category new name
     * @param Status $newCatStatus Category new status
     * 
     * @return bool If the update successful, then return true, otherwise false
     * 
     */
    public function editCategory(string $oldCatName, string $newCatName, Status $newCatStatus):bool{
        global $connection;    
        $isUpdated = $connection -> insert(
                "UPDATE {$this -> tableName} SET category_name = ?, category_status = ? WHERE category_name = ?",
                "sss",
                [$newCatName,$newCatStatus -> value, $oldCatName]
            );
            return $isUpdated;
    }

    /**
     * Delete category with category name
     *
     * @param string $catName Category Name
     * 
     * @return bool Return true if deletion success, otherfalse false
     * 
     */
    public function deleteCategory(string $catName):bool{
        global $connection;
        $category = $connection -> select(
            "SELECT id FROM {$this -> tableName} WHERE category_name = ?",
            "s",
            [$catName]
        ) -> fetch_assoc();
        global $connection;
        return $connection -> deleteWithId($this -> tableName, $category['id']);
    }
  
    /**
     * List of all categories
     * 
     * @return array|false Returns Array of categories if query successfully executes. Otherwise return false
     */
    public function getAllCategories(){
        global $connection;
        $result = $connection -> select("SELECT category_name, category_status FROM {$this -> tableName}");
        if(false != $result){
            return $result -> fetch_all(MYSQLI_ASSOC) ?? [];
        } else {
            return false;
        }
    }
 }