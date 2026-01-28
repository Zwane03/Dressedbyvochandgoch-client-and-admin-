<?php
// designs.php - Designs management
require_once 'config.php';

class Design {
    private $conn;
    private $table_name = "designs";

    public $id;
    public $name;
    public $description;
    public $price;
    public $category;
    public $image_path;
    public $is_active;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create design
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET name=:name, description=:description, price=:price, 
                      category=:category, image_path=:image_path";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->image_path = htmlspecialchars(strip_tags($this->image_path));
        
        // Bind parameters
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":image_path", $this->image_path);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get all designs
    public function read($active_only = true) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if ($active_only) {
            $query .= " WHERE is_active = 1";
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get active designs count
    public function getActiveCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Update design
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name=:name, description=:description, price=:price, 
                      category=:category, image_path=:image_path, is_active=:is_active
                  WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->image_path = htmlspecialchars(strip_tags($this->image_path));
        $this->is_active = htmlspecialchars(strip_tags($this->is_active));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":image_path", $this->image_path);
        $stmt->bindParam(":is_active", $this->is_active);
        $stmt->bindParam(":id", $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Delete design (soft delete)
    public function delete() {
        $query = "UPDATE " . $this->table_name . " SET is_active = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

// Handle file upload
function uploadImage($file) {
    $target_dir = "uploads/designs/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $file_name = "design_" . time() . "_" . rand(1000, 9999) . "." . $file_extension;
    $target_file = $target_dir . $file_name;
    
    // Check if image file is actual image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return ['success' => false, 'message' => 'File is not an image'];
    }
    
    // Check file size (5MB max)
    if ($file["size"] > 5000000) {
        return ['success' => false, 'message' => 'File is too large'];
    }
    
    // Allow certain file formats
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed'];
    }
    
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['success' => true, 'file_path' => $target_file];
    } else {
        return ['success' => false, 'message' => 'Error uploading file'];
    }
}

// API endpoints for designs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $design = new Design($db);
    
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                // Handle file upload
                if (isset($_FILES['image'])) {
                    $upload_result = uploadImage($_FILES['image']);
                    if (!$upload_result['success']) {
                        echo json_encode($upload_result);
                        exit;
                    }
                    $design->image_path = $upload_result['file_path'];
                }
                
                $design->name = $_POST['name'];
                $design->description = $_POST['description'];
                $design->price = $_POST['price'];
                $design->category = $_POST['category'];
                
                if ($design->create()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Design uploaded successfully'
                    ]);
                } else {
                    // Delete uploaded file if database operation failed
                    if (isset($design->image_path)) {
                        unlink($design->image_path);
                    }
                    echo json_encode([
                        'success' => false,
                        'message' => 'Failed to upload design'
                    ]);
                }
                break;
                
            case 'get_active_count':
                $count = $design->getActiveCount();
                echo json_encode([
                    'success' => true,
                    'count' => $count
                ]);
                break;
        }
    }
}

// GET endpoint to retrieve designs
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $database = new Database();
    $db = $database->getConnection();
    $design = new Design($db);
    
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_all':
            $active_only = $_GET['active_only'] ?? true;
            $stmt = $design->read($active_only);
            $designs = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $designs[] = $row;
            }
            
            echo json_encode([
                'success' => true,
                'designs' => $designs
            ]);
            break;
    }
}
?>