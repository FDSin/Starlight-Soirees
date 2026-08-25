<?php
// Expects $actionUrl and optional $event associative array (keys: name, date, venue, status)
$event = $event ?? ['name'=>'','date'=>'','venue'=>'','status'=>''];
?>
<div class="form-card">
    <form method="POST" action="<?= htmlspecialchars($actionUrl) ?>">
        <div class="form-row">
            <label for="name">Event Name</label>
            <input id="name" name="name" type="text" value="<?= htmlspecialchars($event['name']) ?>" required>
        </div>
        <div class="form-row">
            <label for="date">Date</label>
            <input id="date" name="date" type="date" value="<?= htmlspecialchars($event['date']) ?>">
        </div>
        <div class="form-row">
            <label for="venue">Venue</label>
            <input id="venue" name="venue" type="text" value="<?= htmlspecialchars($event['venue']) ?>">
        </div>
        <div class="form-row">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php $s = $event['status'] ?? '';?>
                <option value="Active" <?= $s==='Active' ? 'selected' : '' ?>>Active</option>
                <option value="Pending" <?= $s==='Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Cancelled" <?= $s==='Cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save</button>
            <a href="admin_events.php">Cancel</a>
        </div>
    </form>
</div>