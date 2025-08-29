<div class="card">
    <div class="card-header">
        <h3>All Subscriptions</h3>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Gateway</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($subscriptions as $sub): ?>
                <tr>
                    <td><?php echo $sub['id']; ?></td>
                    <td><?php echo htmlspecialchars($sub['username']); ?></td>
                    <td><?php echo htmlspecialchars($sub['plan_name']); ?></td>
                    <td><span class="badge status-<?php echo strtolower($sub['status']); ?>"><?php echo ucfirst($sub['status']); ?></span></td>
                    <td><?php echo date('M j, Y', strtotime($sub['start_date'])); ?></td>
                    <td><?php echo date('M j, Y', strtotime($sub['end_date'])); ?></td>
                    <td><?php echo htmlspecialchars($sub['payment_gateway']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Pagination would go here -->
    </div>
</div>