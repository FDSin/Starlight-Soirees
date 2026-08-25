<?php
$p = $p ?? ['event_id'=>'','amount'=>'','date'=>'','status'=>'Pending'];
?>
<div class="form-card">
    <form method="POST" action="<?= htmlspecialchars($actionUrl) ?>">
        <div class="form-row">
            <label for="event_id">Event ID</label>
            <input id="event_id" name="event_id" type="text" value="<?= htmlspecialchars($p['event_id']) ?>" required>
        </div>
        <div class="form-row">
            <label for="amount">Amount</label>
            <input id="amount" name="amount" type="text" value="<?= htmlspecialchars($p['amount']) ?>">
        </div>
        <div class="form-row">
            <label for="date">Date</label>
            <input id="date" name="date" type="date" value="<?= htmlspecialchars($p['date']) ?>">
        </div>
        <div class="form-row">
            <label for="status">Status</label>
            <select id="status" name="status">
                <?php $s = $p['status'] ?? 'Pending'; ?>
                <option value="Pending" <?= $s==='Pending' ? 'selected' : '' ?>>Pending</option>
                <option value="Paid" <?= $s==='Paid' ? 'selected' : '' ?>>Paid</option>
                <option value="Refunded" <?= $s==='Refunded' ? 'selected' : '' ?>>Refunded</option>
            </select>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Save</button>
            <a href="admin_payment.php">Cancel</a>
        </div>
    </form>
</div>