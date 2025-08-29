<div class="card">
    <div class="card-header">
        <h3>All Movies</h3>
        <a href="/admin/movies/create" class="btn btn-primary">Add New Movie</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Genre</th>
                    <th>Status</th>
                    <th>Uploaded By</th>
                    <th>Premium</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($movies as $movie): ?>
                <tr>
                    <td><?php echo $movie['id']; ?></td>
                    <td><?php echo htmlspecialchars($movie['title']); ?></td>
                    <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                    <td><span class="badge status-<?php echo strtolower($movie['status']); ?>"><?php echo ucfirst($movie['status']); ?></span></td>
                    <td>
                        <?php // THE FIX: Display the creator's username or 'Admin' ?>
                        <?php if (!empty($movie['creator_username'])): ?>
                            <span class="badge role-creator"><?php echo htmlspecialchars($movie['creator_username']); ?></span>
                        <?php else: ?>
                            <span class="badge role-admin">Admin</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $movie['is_premium'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="/admin/movies/<?php echo $movie['id']; ?>/edit" class="btn btn-sm btn-primary">Edit</a>
                         <form action="/admin/movies/<?php echo $movie['id']; ?>/delete" method="POST" class="d-inline delete-form">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php 
            // Include the pagination partial, which we already built for the public site.
            // We just need to ensure the base URL is correct for the admin area.
            $baseUrl = '/admin/movies';
            include VIEWS_PATH . '/partials/pagination.php'; 
        ?>
    </div>
</div>