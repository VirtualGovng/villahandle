<div class="card">
    <div class="card-header">
        <h3>All Users</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $userItem): ?>
                <tr>
                    <td><?php echo $userItem['id']; ?></td>
                    <td><?php echo htmlspecialchars($userItem['username']); ?></td>
                    <td><?php echo htmlspecialchars($userItem['email']); ?></td>
                    <td><span class="badge role-<?php echo strtolower($userItem['role']); ?>"><?php echo ucfirst($userItem['role']); ?></span></td>
                    <td><span class="badge status-<?php echo strtolower($userItem['status']); ?>"><?php echo ucfirst($userItem['status']); ?></span></td>
                    <td><?php echo date('M j, Y', strtotime($userItem['created_at'])); ?></td>
                    <td>
                        <a href="/admin/users/<?php echo $userItem['id']; ?>/edit" class="btn btn-sm btn-primary">Edit</a>
                        <form action="/admin/users/<?php echo $userItem['id']; ?>/delete" method="POST" class="d-inline delete-form">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Pagination would be included here -->
    </div>
</div>