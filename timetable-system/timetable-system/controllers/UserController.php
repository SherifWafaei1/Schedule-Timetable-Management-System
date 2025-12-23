<?php
/**
 * User Controller Class
 */
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class UserController extends BaseController {
    private $userModel;

    public function __construct($db) {
        parent::__construct($db);
        $this->userModel = new User($db);
    }

    /**
     * Show users list
     */
    public function index() {
        if (!$this->isAdmin()) {
            $this->flash->set('Access denied. Only administrators can manage users.', 'danger');
            $this->redirect('../admin/dashboard.php');
        }

        $page = (int)($_GET['page'] ?? 1);
        $search = $this->input->sanitize($_GET['search'] ?? '');
        $role_filter = $this->input->sanitize($_GET['role'] ?? '');
        $from_record_num = ($page - 1) * RECORDS_PER_PAGE;

        $filters = [];
        if (!empty($search)) $filters['search'] = "%" . $search . "%";
        if (!empty($role_filter)) $filters['role'] = $role_filter;

        try {
            $users = $this->userModel->readAll($filters, $from_record_num, RECORDS_PER_PAGE);
            $total_users = $this->userModel->countAll($filters);
            $pagination = paginate($total_users, $page, RECORDS_PER_PAGE, ['search' => $search, 'role' => $role_filter]);
            $flash = $this->flash->get();

            $this->render('admin/users/index', [
                'page_title' => 'User Management',
                'current_user' => $this->session->getUser(),
                'users' => $users,
                'total_users' => $total_users,
                'pagination' => $pagination,
                'search' => $search,
                'role_filter' => $role_filter,
                'flash' => $flash
            ]);
        } catch (Exception $e) {
            error_log("User index error: " . $e->getMessage());
            $this->flash->set('An error occurred loading users.', 'danger');
            $this->redirect('../admin/dashboard.php');
        }
    }

    /**
     * Handle user creation
     */
    public function create() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');
        
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request. Please try again.', 'danger');
            $this->redirect('users.php');
        }

        $this->userModel->username = $this->input->sanitize($_POST['username']);
        $this->userModel->email = $this->input->sanitize($_POST['email']);
        $this->userModel->password_hash = $_POST['password']; 
        $this->userModel->first_name = $this->input->sanitize($_POST['first_name']);
        $this->userModel->last_name = $this->input->sanitize($_POST['last_name']);
        $this->userModel->role = $this->input->sanitize($_POST['role']);
        $this->userModel->is_active = isset($_POST['is_active']) ? 1 : 0;
        $this->userModel->created_by = $this->session->getUser()['id'];

        if ($this->userModel->usernameExists()) {
            $this->flash->set('Username already exists.', 'danger');
        } elseif ($this->userModel->emailExists()) {
            $this->flash->set('Email already exists.', 'danger');
        } else {
            if ($this->userModel->create()) {
                $this->flash->set('User created successfully.', 'success');
            } else {
                $this->flash->set('Failed to create user.', 'danger');
            }
        }
        $this->redirect('users.php');
    }

    /**
     * Handle user update
     */
    public function update() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('users.php');
        }

        $this->userModel->id = (int)$_POST['user_id'];
        $this->userModel->username = $this->input->sanitize($_POST['username']);
        $this->userModel->email = $this->input->sanitize($_POST['email']);
        $this->userModel->first_name = $this->input->sanitize($_POST['first_name']);
        $this->userModel->last_name = $this->input->sanitize($_POST['last_name']);
        $this->userModel->role = $this->input->sanitize($_POST['role']);
        $this->userModel->is_active = isset($_POST['is_active']) ? 1 : 0;

        if ($this->userModel->update()) {
            $this->flash->set('User updated successfully.', 'success');
        } else {
            $this->flash->set('Failed to update user.', 'danger');
        }
        $this->redirect('users.php');
    }

    /**
     * Handle user deletion
     */
    public function delete() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('users.php');
        }

        $user_id = (int)$_POST['user_id'];
        if ($user_id == $this->session->getUser()['id']) {
            $this->flash->set('You cannot delete your own account.', 'danger');
        } else {
            $this->userModel->id = $user_id;
            if ($this->userModel->delete()) {
                $this->flash->set('User deleted successfully.', 'success');
            } else {
                $this->flash->set('Failed to delete user.', 'danger');
            }
        }
        $this->redirect('users.php');
    }

    /**
     * Reset password
     */
    public function resetPassword() {
        if (!$this->isAdmin()) $this->redirect('dashboard.php');

        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!verifyCSRFToken($csrf_token)) {
            $this->flash->set('Invalid request.', 'danger');
            $this->redirect('users.php');
        }

        $user_id = (int)$_POST['user_id'];
        $new_password = $_POST['new_password'];

        $this->userModel->id = $user_id;
        if ($this->userModel->updatePassword($new_password)) {
            $this->flash->set('Password reset successfully.', 'success');
        } else {
            $this->flash->set('Failed to reset password.', 'danger');
        }
        $this->redirect('users.php');
    }
}
?>
