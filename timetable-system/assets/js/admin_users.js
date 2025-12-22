document.addEventListener('DOMContentLoaded', function() {
    // Edit user
    document.querySelectorAll('.edit-user-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            try {
                var user = JSON.parse(this.getAttribute('data-user'));
            } catch (e) {
                console.error('Invalid user data', e);
                return;
            }
            document.getElementById('edit_user_id').value = user.id || '';
            document.getElementById('edit_username').value = user.username || '';
            document.getElementById('edit_email').value = user.email || '';
            document.getElementById('edit_first_name').value = user.first_name || '';
            document.getElementById('edit_last_name').value = user.last_name || '';
            document.getElementById('edit_role').value = user.role || '';
            var isActiveInput = document.getElementById('edit_is_active');
            if (isActiveInput) isActiveInput.checked = !!user.is_active;

            var editModalEl = document.getElementById('editUserModal');
            var modal = new bootstrap.Modal(editModalEl);
            modal.show();
        });
    });

    // Reset password
    document.querySelectorAll('.reset-password-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var userId = this.getAttribute('data-user-id');
            var username = this.getAttribute('data-username');
            document.getElementById('reset_user_id').value = userId || '';
            document.getElementById('reset_username').textContent = username || '';

            var modalEl = document.getElementById('resetPasswordModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    });

    // Delete user
    document.querySelectorAll('.delete-user-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var userId = this.getAttribute('data-user-id');
            var username = this.getAttribute('data-username');
            document.getElementById('delete_user_id').value = userId || '';
            document.getElementById('delete_username').textContent = username || '';

            var modalEl = document.getElementById('deleteUserModal');
            var modal = new bootstrap.Modal(modalEl);
            modal.show();
        });
    });
});