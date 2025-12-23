<?php
/**
 * User Model
 * Schedule Time Table Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/functions.php';

class User {
    private $conn;
    private $table_name = 'users';
    
    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $first_name;
    public $last_name;
    public $role;
    public $is_active;
    public $created_at;
    public $updated_at;
    public $last_login;
    public $reset_token;
    public $reset_token_expires;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create new user
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (username, email, password_hash, first_name, last_name, role, is_active)
                  VALUES (:username, :email, :password_hash, :first_name, :last_name, :role, :is_active)";
        
        $stmt = $this->conn->prepare($query);
        
        // Sanitize and hash password
        $this->password_hash = hashPassword($this->password_hash);
        
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':is_active', $this->is_active);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            logAuditTrail('CREATE_USER', $this->table_name, $this->id, null, [
                'username' => $this->username,
                'email' => $this->email,
                'role' => $this->role
            ]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Authenticate user login
     */
    public function authenticate($username, $password) {
        $query = "SELECT id, username, email, password_hash, first_name, last_name, role, is_active, last_login
                  FROM " . $this->table_name . " 
                  WHERE (username = :username OR email = :email) AND is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (verifyPassword($password, $row['password_hash'])) {
                // Update last login
                $this->updateLastLogin($row['id']);
                
                // Set user properties
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->first_name = $row['first_name'];
                $this->last_name = $row['last_name'];
                $this->role = $row['role'];
                $this->is_active = $row['is_active'];
                $this->last_login = $row['last_login'];
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get user by ID
     */
    public function readOne() {
        $query = "SELECT id, username, email, first_name, last_name, role, is_active, 
                         created_at, updated_at, last_login
                  FROM " . $this->table_name . " 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->role = $row['role'];
            $this->is_active = $row['is_active'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->last_login = $row['last_login'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get all users with pagination
     */
    public function readAll($filters = [], $from_record_num = 0, $records_per_page = RECORDS_PER_PAGE) {
        $where_conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            // Tokenize search term and require each token to match one of the name/username/email fields.
            // Controller provides wildcards (e.g. "%term%"), strip them for tokenization.
            $raw = $filters['search'];
            $term = trim($raw, "%");
            $tokens = preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY);

            if (count($tokens) === 1) {
                // Single token: use distinct placeholders for each column to avoid repeating the same named parameter
                $where_conditions[] = "(username LIKE :s_username OR email LIKE :s_email OR first_name LIKE :s_first OR last_name LIKE :s_last OR CONCAT(first_name, ' ', last_name) LIKE :s_full)";
                $params[':s_username'] = $filters['search'];
                $params[':s_email'] = $filters['search'];
                $params[':s_first'] = $filters['search'];
                $params[':s_last'] = $filters['search'];
                $params[':s_full'] = $filters['search'];
            } else {
                // Multiple tokens: require each token to appear in any of the searchable columns
                $tokenClauses = [];
                foreach ($tokens as $i => $tok) {
                    $key = ':tok' . $i;
                    $tokenClauses[] = "(username LIKE $key OR email LIKE $key OR first_name LIKE $key OR last_name LIKE $key OR CONCAT(first_name, ' ', last_name) LIKE $key)";
                    $params[$key] = '%' . $tok . '%';
                }
                $where_conditions[] = '(' . implode(' AND ', $tokenClauses) . ')';
            }
        }

        if (!empty($filters['role'])) {
            $where_conditions[] = "role = :role";
            $params[':role'] = $filters['role'];
        }

        $where_clause = !empty($where_conditions) ? " WHERE " . implode(' AND ', $where_conditions) : '';

         $query = "SELECT id, username, email, first_name, last_name, role, is_active, 
                    created_at, last_login
                FROM " . $this->table_name . $where_clause . " 
                ORDER BY first_name, last_name 
                LIMIT :from_record_num, :records_per_page";
        
        $stmt = $this->conn->prepare($query);
        // Debug logging: log query and params when search is used
        if (!empty($filters['search'])) {
            try {
                error_log('User::readAll SQL: ' . $query . ' -- params: ' . json_encode($params));
            } catch (Exception $e) {
                // ignore logging errors
            }
        }
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindParam(':from_record_num', $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(':records_per_page', $records_per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Count total users
     */
    public function countAll($filters = []) {
        $where_conditions = [];
        $params = [];

        if (!empty($filters['search'])) {
            $raw = $filters['search'];
            $term = trim($raw, "%");
            $tokens = preg_split('/\s+/', $term, -1, PREG_SPLIT_NO_EMPTY);

            if (count($tokens) === 1) {
                $where_conditions[] = "(username LIKE :s_username OR email LIKE :s_email OR first_name LIKE :s_first OR last_name LIKE :s_last OR CONCAT(first_name, ' ', last_name) LIKE :s_full)";
                $params[':s_username'] = $filters['search']; // The wildcard is now added in the controller
                $params[':s_email'] = $filters['search'];
                $params[':s_first'] = $filters['search'];
                $params[':s_last'] = $filters['search'];
                $params[':s_full'] = $filters['search'];
            } else {
                $tokenClauses = [];
                foreach ($tokens as $i => $tok) {
                    $key = ':tok' . $i;
                    $tokenClauses[] = "(username LIKE $key OR email LIKE $key OR first_name LIKE $key OR last_name LIKE $key OR CONCAT(first_name, ' ', last_name) LIKE $key)";
                    $params[$key] = '%' . $tok . '%';
                }
                $where_conditions[] = '(' . implode(' AND ', $tokenClauses) . ')';
            }
        }

        if (!empty($filters['role'])) {
            $where_conditions[] = "role = :role";
            $params[':role'] = $filters['role'];
        }

        $where_clause = '';
        if (!empty($where_conditions)) {
            $where_clause = " WHERE " . implode(' AND ', $where_conditions);
        }

        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . $where_clause;
        $stmt = $this->conn->prepare($query);

        // Debug logging: log count query and params when search is used
        if (!empty($filters['search'])) {
            try {
                error_log('User::countAll SQL: ' . $query . ' -- params: ' . json_encode($params));
            } catch (Exception $e) {}
        }

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    
    /**
     * Update user
     */
    public function update() {
        // Get old data for audit before updating
        $oldData = [];
        $oldUser = new User($this->conn);
        $oldUser->id = $this->id;
        if ($oldUser->readOne()) {
            $oldData = [
                'username' => $oldUser->username,
                'email' => $oldUser->email,
                'role' => $oldUser->role,
            ];
        }
        
        $query = "UPDATE " . $this->table_name . " 
                  SET username = :username, email = :email, first_name = :first_name, 
                      last_name = :last_name, role = :role, is_active = :is_active
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':is_active', $this->is_active);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            logAuditTrail('UPDATE_USER', $this->table_name, $this->id, $oldData, [
                'username' => $this->username,
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'role' => $this->role,
                'is_active' => $this->is_active
            ]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Update user profile information
     */
    public function updateProfile() {
        $oldData = [];
        $currentUser = new User($this->conn);
        $currentUser->id = $this->id;
        $currentUser->readOne();
        $oldData = [
            'username' => $currentUser->username,
            'email' => $currentUser->email,
            'first_name' => $currentUser->first_name,
            'last_name' => $currentUser->last_name
        ];
        
        $query = "UPDATE " . $this->table_name . " 
                  SET username = :username, email = :email, first_name = :first_name, 
                      last_name = :last_name
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            logAuditTrail('UPDATE_PROFILE', $this->table_name, $this->id, $oldData, [
                'username' => $this->username,
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name
            ]);
            return true;
        }
        
        return false;
    }
    /**
     * Update user password
     */
    public function updatePassword($newPassword) {
        $query = "UPDATE " . $this->table_name . " 
                  SET password_hash = :password_hash 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $password_hash = hashPassword($newPassword);
        
        $stmt->bindParam(':password_hash', $password_hash);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            logAuditTrail('UPDATE_PASSWORD', $this->table_name, $this->id);
            return true;
        }
        
        return false;
    }
    
    /**
     * Delete user
     */
    public function delete() {
        $oldData = [];
        $this->readOne();
        $oldData = [
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role
        ];
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        if ($stmt->execute()) {
            logAuditTrail('DELETE_USER', $this->table_name, $this->id, $oldData);
            return true;
        }
        
        return false;
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username AND id != :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $this->username);
        // Bind the ID to exclude the current user from the check
        $id = $this->id ?? 0;
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists() {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email AND id != :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $this->email);
        // Bind the ID to exclude the current user from the check
        $id = $this->id ?? 0;
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Generate password reset token
     */
    public function generateResetToken() {
        $this->reset_token = generateToken();
        $this->reset_token_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $query = "UPDATE " . $this->table_name . " 
                  SET reset_token = :reset_token, reset_token_expires = :reset_token_expires 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':reset_token', $this->reset_token);
        $stmt->bindParam(':reset_token_expires', $this->reset_token_expires);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Verify password reset token
     */
    public function verifyResetToken($token) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE reset_token = :token AND reset_token_expires > NOW()";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            return true;
        }
        
        return false;
    }
    
    /**
     * Clear password reset token
     */
    public function clearResetToken() {
        $query = "UPDATE " . $this->table_name . " 
                  SET reset_token = NULL, reset_token_expires = NULL 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        $query = "UPDATE " . $this->table_name . " SET last_login = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
    }
    
    /**
     * Get users by role
     */
    public function getUsersByRole($role) {
        $query = "SELECT id, username, email, first_name, last_name, is_active, created_at, last_login
                  FROM " . $this->table_name . " 
                  WHERE role = :role AND is_active = 1
                  ORDER BY first_name, last_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
