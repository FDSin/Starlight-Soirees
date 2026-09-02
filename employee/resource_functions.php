<?php

function validateVenue(array $venue): string
{
    if ($venue['venue_name'] === '') {
        return 'Venue name is required.';
    }
    if ($venue['max_capacity'] < 1) {
        return 'Maximum capacity must be at least 1.';
    }
    if (!is_numeric($venue['venue_price']) || (float)$venue['venue_price'] < 0) {
        return 'Venue price must be a valid non-negative amount.';
    }

    return '';
}

function validateMenuPackage(array $menu): string
{
    if ($menu['package_name'] === '') {
        return 'Package name is required.';
    }
    if (!is_numeric($menu['price_per_person']) || (float)$menu['price_per_person'] < 0) {
        return 'Price per guest must be a valid non-negative amount.';
    }

    return '';
}
