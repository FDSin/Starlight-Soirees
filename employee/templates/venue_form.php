<?php
$venue = $venue ?? ['name'=>'','address'=>'','capacity'=>''];
?>
<div class="form-card">
    <form method="POST" action="<?= htmlspecialchars($actionUrl) ?>">
        <div class="form-row">
            <label for="name">Venue Name</label>
            <input id="name" name="name" type="text" value="<?= htmlspecialchars($venue['name']) ?>" required>
        </div>
        <div class="form-row">
            <label for="address">Address</label>
            <input id="address" name="address" type="text" value="<?= htmlspecialchars($venue['address']) ?>">
        </div>
        <div class="form-row">
            <label for="capacity">Capacity</label>
            <input id="capacity" name="capacity" type="text" value="<?= htmlspecialchars($venue['capacity']) ?>">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn-primary">Save</button>
            <a href="admin_venue.php">Cancel</a>
        </div>
    </form>
</div>