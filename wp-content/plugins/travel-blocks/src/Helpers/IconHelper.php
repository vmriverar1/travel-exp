<?php

namespace Travel\Blocks\Helpers;

/**
 * Icon Helper
 *
 * Provides SVG icons for use across blocks and templates.
 * Supports 30+ common icons with customizable size and color.
 *
 * @since 1.0.0
 */
class IconHelper {

    /**
     * Get SVG icon markup
     *
     * @param string $icon_name Icon name (e.g., 'check', 'clock', 'star')
     * @param int    $size      Icon size in pixels (default: 24)
     * @param string $color     Icon color (default: 'currentColor')
     * @return string SVG markup or empty string if icon not found
     */
    public static function get_icon_svg($icon_name, $size = 24, $color = 'currentColor') {
        $icons = self::get_icons();

        if (!isset($icons[$icon_name])) {
            return '';
        }

        $path = $icons[$icon_name];
        $class = 'icon icon-' . esc_attr($icon_name);

        return sprintf(
            '<svg class="%s" width="%d" height="%d" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="%s" fill="%s"/></svg>',
            $class,
            absint($size),
            absint($size),
            esc_attr($path),
            esc_attr($color)
        );
    }

    /**
     * Get all available icons
     *
     * Returns an array of icon names and their SVG path data.
     * Icons are organized by category for easy reference.
     *
     * @return array Associative array of icon_name => svg_path
     */
    private static function get_icons() {
        return [
            // ===== STATUS & ACTIONS =====
            'check' => 'M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z',
            'check-circle' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z',
            'x' => 'M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z',
            'x-circle' => 'M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z',
            'plus' => 'M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z',
            'minus' => 'M19 13H5v-2h14v2z',

            // ===== TIME & CALENDAR =====
            'clock' => 'M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z',
            'calendar' => 'M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8h14V6H5v2zm2 4h10v2H7z',

            // ===== PEOPLE =====
            'user' => 'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z',
            'users' => 'M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z',

            // ===== TRAVEL & LOCATION =====
            'map-pin' => 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z',
            'compass' => 'M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-9c0-2.76 2.24-5 5-5s5 2.24 5 5-2.24 5-5 5-5-2.24-5-5zm8 0l-3-3v6l3-3z',
            'plane' => 'M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z',
            'bus' => 'M4 16c0 .88.39 1.67 1 2.22V20c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h8v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1.78c.61-.55 1-1.34 1-2.22V6c0-3.5-3.58-4-8-4s-8 .5-8 4v10zm3.5 1c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11V6h14v5H5z',
            'home' => 'M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z',

            // ===== ACCOMMODATION & MEALS =====
            'bed' => 'M20 9.557V4h-2v2H6V4H4v12h2v-2h12v2h2v-6.443zM18 11H6V8h12v3z M7 14c.55 0 1-.45 1-1s-.45-1-1-1-1 .45-1 1 .45 1 1 1zm10 0c.55 0 1-.45 1-1s-.45-1-1-1-1 .45-1 1 .45 1 1 1z',
            'utensils' => 'M8.1 13.34l2.83-2.83L3.91 3.5c-1.56 1.56-1.56 4.09 0 5.66l4.19 4.18zm6.78-1.81c1.53.71 3.68.21 5.27-1.38 1.91-1.91 2.28-4.65.81-6.12-1.46-1.46-4.2-1.1-6.12.81-1.59 1.59-2.09 3.74-1.38 5.27L3.7 19.87l1.41 1.41L12 14.41l6.88 6.88 1.41-1.41L13.41 13z',

            // ===== ACTIVITIES =====
            'backpack' => 'M20 8v12c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2V8c0-1.86 1.28-3.41 3-3.86V2h3v2h4V2h3v2.14c1.72.45 3 2 3 3.86zM6 12v2h12v-2H6zm10-4H8V6h8v2z',
            'camera' => 'M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z',
            'heart' => 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z',
            'star' => 'M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z',

            // ===== FEATURES & STATUS =====
            'shield' => 'M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z',
            'award' => 'M12 2L9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27 18.18 21l-1.63-7.03L22 9.24l-7.19-.61L12 2zm0 4.3l1.71 3.47 3.82.32-2.91 2.52.87 3.74L12 14.77l-3.49 1.58.87-3.74-2.91-2.52 3.82-.32L12 6.3z',
            'briefcase' => 'M20 6h-4V4c0-1.11-.89-2-2-2h-4c-1.11 0-2 .89-2 2v2H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2zm-6 0h-4V4h4v2z',

            // ===== MEDIA & SHARING =====
            'play' => 'M8 5v14l11-7z',
            'download' => 'M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z',
            'share' => 'M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z',
            'arrow-right' => 'M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z',

            // ===== WEATHER =====
            'sun' => 'M6.76 4.84l-1.8-1.79-1.41 1.41 1.79 1.79 1.42-1.41zM4 10.5H1v2h3v-2zm9-9.95h-2V3.5h2V.55zm7.45 3.91l-1.41-1.41-1.79 1.79 1.41 1.41 1.79-1.79zm-3.21 13.7l1.79 1.8 1.41-1.41-1.8-1.79-1.4 1.4zM20 10.5v2h3v-2h-3zm-8-5c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm-1 16.95h2V19.5h-2v2.95zm-7.45-3.91l1.41 1.41 1.79-1.8-1.41-1.41-1.79 1.8z',
            'cloud' => 'M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96z',
            'snowflake' => 'M22 11h-4.17l3.24-3.24-1.41-1.42L15 11h-2V9l4.66-4.66-1.42-1.41L13 6.17V2h-2v4.17L7.76 2.93 6.34 4.34 11 9v2H9L4.34 6.34 2.93 7.76 6.17 11H2v2h4.17l-3.24 3.24 1.41 1.42L9 13h2v2l-4.66 4.66 1.42 1.41L11 17.83V22h2v-4.17l3.24 3.24 1.42-1.41L13 15v-2h2l4.66 4.66 1.41-1.42L17.83 13H22v-2z',
            'droplet' => 'M12 2.69l5.66 5.66c1.13 1.13 1.13 2.98 0 4.24L12 18.31l-5.66-5.66c-1.13-1.13-1.13-2.98 0-4.24L12 2.69zM12 0L4.93 7.07c-2.34 2.34-2.34 6.14 0 8.49L12 22.63l7.07-7.07c2.34-2.34 2.34-6.14 0-8.49L12 0z',

            // ===== ADDITIONAL =====
            'lock' => 'M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zM9 8V6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9z',
            'thumbs-up' => 'M1 21h4V9H1v12zm22-11c0-1.1-.9-2-2-2h-6.31l.95-4.57.03-.32c0-.41-.17-.79-.44-1.06L14.17 1 7.59 7.59C7.22 7.95 7 8.45 7 9v10c0 1.1.9 2 2 2h9c.83 0 1.54-.5 1.84-1.22l3.02-7.05c.09-.23.14-.47.14-.73v-2z',
        ];
    }

    /**
     * Get list of all available icon names
     *
     * @return array List of icon names
     */
    public static function get_available_icons() {
        return array_keys(self::get_icons());
    }

    /**
     * Get icon choices for ACF select fields
     *
     * Returns an associative array formatted for ACF field 'choices',
     * with emoji representations for better UX in the WordPress admin.
     *
     * @return array Associative array of icon_name => label
     */
    public static function get_icon_choices() {
        return [
            // Time & Calendar
            'clock' => '🕒 Clock',
            'calendar' => '📅 Calendar',

            // People
            'user' => '👤 User',
            'users' => '👥 Users/Group',

            // Travel & Location
            'map-pin' => '📍 Map Pin',
            'compass' => '🧭 Compass',
            'plane' => '✈️ Plane',
            'bus' => '🚌 Bus',
            'home' => '🏠 Home',

            // Accommodation & Meals
            'bed' => '🛏️ Bed/Accommodation',
            'utensils' => '🍴 Utensils/Meals',

            // Activities
            'backpack' => '🎒 Backpack/Trekking',
            'camera' => '📷 Camera/Photography',
            'heart' => '❤️ Heart/Favorite',
            'star' => '⭐ Star/Rating',

            // Status & Features
            'check' => '✅ Check/Included',
            'check-circle' => '✔️ Check Circle',
            'x' => '❌ X/Not Included',
            'x-circle' => '⊗ X Circle',
            'shield' => '🛡️ Shield/Protection',
            'award' => '🏆 Award/Excellence',
            'briefcase' => '💼 Briefcase/Business',
            'lock' => '🔒 Lock/Secure',
            'thumbs-up' => '👍 Thumbs Up',
            'plus' => '➕ Plus/Add',
            'minus' => '➖ Minus/Remove',

            // Media & Sharing
            'play' => '▶️ Play',
            'download' => '⬇️ Download',
            'share' => '🔗 Share',
            'arrow-right' => '→ Arrow Right',

            // Weather
            'sun' => '☀️ Sun/Sunny',
            'cloud' => '☁️ Cloud/Cloudy',
            'snowflake' => '❄️ Snowflake/Cold',
            'droplet' => '💧 Droplet/Rain',
        ];
    }
}
