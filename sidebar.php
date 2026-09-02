<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['PHP_SELF']);
$inEmployee = (strpos($_SERVER['PHP_SELF'], '/employee/') !== false);
$logoutHref = $inEmployee ? '../logout.php' : 'logout.php';
?>
<aside class="sidebar">
    <div class="brand">Starlight Soirées</div>
    <nav aria-label="Employee navigation">
        <ul class="sidebar-nav">
        <?php
        $items = [
            ['label' => 'Home', 'file' => 'employee_dashboard.php'],
            ['label' => 'Events', 'file' => 'admin_events.php'],
            ['label' => 'Venue', 'file' => 'admin_venue.php'],
            ['label' => 'Food & Catering', 'file' => 'admin_food.php'],
            ['label' => 'Payment', 'file' => 'admin_payment.php'],
            ['label' => 'Report', 'file' => 'admin_report.php'],
        ];

        $groups = [
            'employee_dashboard.php' => ['employee_dashboard.php'],
            'admin_events.php' => ['admin_events.php','add_event.php','edit_event.php','view_event.php'],
            'admin_venue.php' => ['admin_venue.php','add_venue.php','edit_venue.php','view_venue.php'],
            'admin_food.php' => ['admin_food.php','add_catering.php','edit_catering.php','view_catering.php'],
            'admin_payment.php' => ['admin_payment.php','add_payment.php','edit_payment.php','view_payment.php'],
            'admin_report.php' => ['admin_report.php'],
        ];

        foreach ($items as $item) {
            $href = $inEmployee ? $item['file'] : ('employee/' . $item['file']);
            $isActive = isset($groups[$item['file']]) && in_array($current, $groups[$item['file']], true);
            $activeClass = $isActive ? 'active' : '';
            $ariaCurrent = $isActive ? ' aria-current="page"' : '';
            echo '<li><a class="' . $activeClass . '" href="' . htmlspecialchars($href) . '"' . $ariaCurrent . '>'
                . htmlspecialchars($item['label']) . '</a></li>';
        }
        ?>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a class="logout-link" href="<?= htmlspecialchars($logoutHref) ?>">Logout</a>
    </div>
</aside>
