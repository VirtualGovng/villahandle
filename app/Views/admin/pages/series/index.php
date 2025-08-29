<div class="card">
    <div class="card-header">
        <h3>All Series</h3>
        <a href="/admin/series/create" class="btn btn-primary">Add New Series</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead><tr><th>ID</th><th>Title</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($series as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo htmlspecialchars($item['title']); ?></td>
                    <td><span class="badge status-<?php echo strtolower($item['status']); ?>"><?php echo ucfirst($item['status']); ?></span></td>
                    <td><a href="/admin/series/<?php echo $item['id']; ?>/manage" class="btn btn-sm btn-primary">Manage</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>