<div class="card">
    <div class="card-header">
        <h3>Edit User: <?php echo htmlspecialchars($userToEdit['username']); ?></h3>
    </div>
    <div class="card-body">
        <form action="/admin/users/<?php echo $userToEdit['id']; ?>/update" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($userToEdit['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($userToEdit['email']); ?>" required>
                </div>
            </div>
             <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($userToEdit['first_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($userToEdit['last_name'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control">
                        <option value="user" <?php echo ($userToEdit['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo ($userToEdit['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="creator" <?php echo ($userToEdit['role'] === 'creator') ? 'selected' : ''; ?>>Creator</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control">
                        <option value="active" <?php echo ($userToEdit['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($userToEdit['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        <option value="banned" <?php echo ($userToEdit['status'] === 'banned') ? 'selected' : ''; ?>>Banned</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/admin/users" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>