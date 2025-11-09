<?php
/**
 * Package Itinerary PDF Template
 *
 * Generates HTML template for package itinerary PDF
 *
 * @package Travel\Components\PdfTemplates
 * @since 1.0.0
 */

namespace Travel\Components\PdfTemplates;

class PackageItineraryTemplate
{
    /**
     * Package data
     *
     * @var array
     */
    private $package_data;

    /**
     * Logo path
     *
     * @var string
     */
    private $logo_path;

    /**
     * Constructor
     *
     * @param array $package_data Package data from WordPress
     */
    public function __construct(array $package_data)
    {
        $this->package_data = $package_data;
        $this->logo_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'assets/images/logo-color.png';
    }

    /**
     * Generate complete HTML for PDF
     *
     * @return string
     */
    public function generate(): string
    {
        // First page
        $title = $this->package_data['title'] ?? 'Package Tour';
        $image_url = $this->package_data['thumbnail'] ?? '';
        $logo_base64 = $this->get_logo_base64();

        // Convert thumbnail to base64
        $thumbnail_base64 = '';
        if (!empty($image_url)) {
            $thumbnail_base64 = $this->get_image_base64($image_url);
        }

        // Second page data
        $days = $this->package_data['days'] ?? 1;
        if ($days == 1) {
            $duration = 'Full Day';
        } else {
            $nights = $days - 1;
            $duration = $days . ' Days / ' . $nights . ' Night' . ($nights > 1 ? 's' : '');
        }

        $gallery_images = $this->get_gallery_images_base64(3);
        $image1_html = !empty($gallery_images[0])
            ? '<img src="' . $gallery_images[0] . '" style="width: 120px; height: 90px; display: block;" />'
            : '<div style="width: 120px; height: 90px; background: #ccc;"></div>';

        $image2_html = !empty($gallery_images[1])
            ? '<img src="' . $gallery_images[1] . '" style="width: 80px; height: 60px; display: block;" />'
            : '<div style="width: 80px; height: 60px; background: #ccc;"></div>';

        $image3_html = !empty($gallery_images[2])
            ? '<img src="' . $gallery_images[2] . '" style="width: 80px; height: 60px; display: block;" />'
            : '<div style="width: 80px; height: 60px; background: #ccc;"></div>';

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin-top: 30mm;
            margin-bottom: 10mm;
        }

        @page :first {
            margin-top: 0;
            margin-bottom: 0;
        }

        body {
            font-family: "Helvetica", "Arial", sans-serif;
            margin: 0;
            padding: 0;
            color: #333333;
            font-size: 12px;
            line-height: 1.6;
        }

        .first-page {
            background-color: #E6F5FF;
            padding: 80px 0;
            text-align: center;
            height: 270mm;
        }

        .first-page__logo {
            width: 180px;
            margin: 0 auto 40px;
        }

        .first-page__subtitle {
            font-size: 10pt;
            color: #1E66B1;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 15px;
            font-weight: normal;
        }

        .first-page__title {
            font-size: 26pt;
            font-weight: bold;
            color: #333333;
            margin-bottom: 40px;
            line-height: 1.3;
            padding: 0 40px;
        }

        .first-page__image {
            width: 90%;
            max-width: 550px;
            height: auto;
            margin: 30px auto;
            display: block;
        }

        .first-page__disclaimer {
            font-size: 10pt;
            color: #555555;
            line-height: 1.6;
            max-width: 520px;
            margin: 40px auto 0;
            padding: 0 40px;
        }

        .page-break {
            page-break-after: always;
        }

        .second-page {
            padding: 0;
        }

        .second-page__content {
            padding: 0 20px 20px 20px;
        }

        /* Header */
        .pdf-header {
            background-color: white;
            padding: 8px 20px;
            border-bottom: 2px solid #1E66B1;
            margin-bottom: 0;
        }

        .pdf-header__left {
            float: left;
            width: 50%;
        }

        .pdf-header__logo {
            height: 30px;
        }

        .pdf-header__right {
            float: right;
            width: 50%;
            text-align: right;
            font-size: 10pt;
            color: #333333;
            line-height: 1.3;
        }

        .pdf-header:after {
            content: "";
            display: table;
            clear: both;
        }

        /* Footer */
        .pdf-footer {
            background-color: white;
            padding: 10px 20px 15px 20px;
            font-size: 8px;
            color: #333333;
            border-top: 1px solid #000000;
        }

        .pdf-footer__grid {
            width: 100%;
            white-space: nowrap;
        }

        .pdf-footer__grid:after {
            content: "";
            display: table;
            clear: both;
        }

        .pdf-footer__column {
            float: left;
            width: 19.5%;
            padding-right: 5px;
            box-sizing: border-box;
        }

        .pdf-footer__column:last-child {
            text-align: right;
            padding-right: 0;
        }

        .pdf-footer__label {
            font-size: 10px;
            font-weight: normal;
            margin: 0 0 2px 0;
            color: #333333;
        }

        .pdf-footer__value {
            font-size: 10px;
            font-weight: bold;
            margin: 0;
            color: #333333;
        }

        .pdf-footer__link {
            color: #333333;
            text-decoration: none;
            font-weight: bold;
        }

        /* Itinerary Page - Two column layout */
        .itinerary-page {
            padding: 0 140px 20px 140px;
        }

        .itinerary-content {
            column-count: 2;
            column-gap: 3px;
            font-size: 12px;
            line-height: 1.6;
        }

        .itinerary-content h2 {
            color: #1E66B1;
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 15px 0;
        }

        .itinerary-content h3 {
            color: #1E66B1;
            font-size: 13px;
            margin: 15px 0 10px 0;
            font-weight: bold;
        }

        .itinerary-content img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .itinerary-content ul {
            margin: 10px 0 15px 20px;
            padding: 0;
        }

        .itinerary-content li {
            margin-bottom: 6px;
            line-height: 1.5;
            font-size: 12px;
        }

        .info-box {
            margin-top: 15px;
            padding: 12px;
            background-color: #F5F5F5;
            border-left: 3px solid #1E66B1;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <!-- Define Header for ODD pages -->
    <htmlpageheader name="MainHeaderOdd">
        <div class="pdf-header">
            <div class="pdf-header__left">
                <img src="' . $logo_base64 . '" alt="Valencia Travel" class="pdf-header__logo">
            </div>
            <div class="pdf-header__right">
                Portal Panes #123<br>
                C.C. Rucceros Office #306-307<br>
                Cusco - Peru
            </div>
        </div>
    </htmlpageheader>

    <!-- Define Header for EVEN pages -->
    <htmlpageheader name="MainHeaderEven">
        <div class="pdf-header">
            <div class="pdf-header__left">
                <img src="' . $logo_base64 . '" alt="Valencia Travel" class="pdf-header__logo">
            </div>
            <div class="pdf-header__right">
                Portal Panes #123<br>
                C.C. Rucceros Office #306-307<br>
                Cusco - Peru
            </div>
        </div>
    </htmlpageheader>

    <!-- Define Footer for ODD pages -->
    <htmlpagefooter name="MainFooterOdd">
        <div class="pdf-footer">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">USA/Canada:</p>
                        <p class="pdf-footer__value">1-(888)803-8004</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">E-mail:</p>
                        <p class="pdf-footer__value">info@valenciatravel.com</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">Peru:</p>
                        <p class="pdf-footer__value">(+51)84-255097</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">24/7:</p>
                        <p class="pdf-footer__value">(+51)979706464</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top; text-align: right;">
                        <p class="pdf-footer__label">Web:</p>
                        <p class="pdf-footer__value">valenciatravel.com</p>
                    </td>
                </tr>
            </table>
        </div>
    </htmlpagefooter>

    <!-- Define Footer for EVEN pages -->
    <htmlpagefooter name="MainFooterEven">
        <div class="pdf-footer">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">USA/Canada:</p>
                        <p class="pdf-footer__value">1-(888)803-8004</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">E-mail:</p>
                        <p class="pdf-footer__value">info@valenciatravel.com</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">Peru:</p>
                        <p class="pdf-footer__value">(+51)84-255097</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">24/7:</p>
                        <p class="pdf-footer__value">(+51)979706464</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top; text-align: right;">
                        <p class="pdf-footer__label">Web:</p>
                        <p class="pdf-footer__value">valenciatravel.com</p>
                    </td>
                </tr>
            </table>
        </div>
    </htmlpagefooter>

    <!-- First Page -->
    <div class="first-page">
        <img src="' . $logo_base64 . '" alt="Valencia Travel" class="first-page__logo">

        <div class="first-page__subtitle">DETAILED ITINERARY</div>
        <h1 class="first-page__title">' . esc_html($title) . '</h1>

        ' . ($thumbnail_base64 ? '<img src="' . $thumbnail_base64 . '" alt="' . esc_attr($title) . '" class="first-page__image">' : '') . '

        <div class="first-page__disclaimer">
            Congratulations on choosing your adventure with Valencia Travel!
            Please note that this itinerary may change depending on local conditions.
            Do not schedule your flights until we confirm your reservation.
        </div>
    </div>

    <pagebreak margin-header="0" margin-footer="0" margin-top="10" margin-bottom="10" margin-left="0" margin-right="0" />
    <sethtmlpageheader name="MainHeaderOdd" page="O" value="1" show-this-page="1" />
    <sethtmlpageheader name="MainHeaderEven" page="E" value="1" show-this-page="1" />
    <sethtmlpagefooter name="MainFooterOdd" page="O" value="1" show-this-page="1" />
    <sethtmlpagefooter name="MainFooterEven" page="E" value="1" show-this-page="1" />

    <!-- Second Page -->
    <div class="second-page">
        <div class="second-page__content">
            <div style="width: 100%;">
            <!-- Columna izquierda: 38% -->
            <div style="float: left; width: 38%; margin-right: 4%;">
                <!-- Mitad superior: celeste -->
                <div style="background-color: #E6F5FF; min-height: 250px; padding: 20px; box-sizing: border-box; text-align: center;">
                    <!-- Layout de fotos estilo polaroid -->
                    <div style="margin-bottom: 20px;">
                        <!-- Foto superior centrada -->
                        <div style="width: 80px; margin: 0 auto 10px auto; background: white; padding: 8px 8px 20px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                            ' . $image1_html . '
                        </div>

                        <!-- Dos fotos inferiores -->
                        <div style="margin-top: 10px; overflow: hidden;">
                            <!-- Foto izquierda -->
                            <div style="float: left; width: 80px; background: white; padding: 8px 8px 20px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); margin-left: 20px;">
                                ' . $image2_html . '
                            </div>
                            <!-- Foto derecha -->
                            <div style="float: right; width: 80px; background: white; padding: 8px 8px 20px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); margin-right: 20px;">
                                ' . $image3_html . '
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial -->
                    <div style="margin-top: 25px; text-align: center;">
                        <p style="font-size: 12px; font-style: italic; color: #1E66B1; margin: 0 0 10px 0; line-height: 1.4;">
                            "The tour through Peru even exceeded<br>the high expectations I had."
                        </p>
                        <p style="font-size: 12px; color: #333; margin: 0 0 10px 0; font-weight: bold;">
                            Evans Ma66 | Gladstone, Australia
                        </p>
                        <p style="font-size: 12px; color: #1E66B1; margin: 0;">
                            Click <strong>HERE</strong> to read more reviews
                        </p>
                    </div>
                </div>
                <!-- Mitad inferior: azul -->
                <div style="background-color: #1E66B1; min-height: 250px; padding: 20px; box-sizing: border-box; text-align: center;">
                    <div style="color: white;">
                        <!-- Texto superior -->
                        <p style="font-size: 14px; font-weight: bold; margin: 0 0 15px 0; color: white;">
                            ' . esc_html($duration) . '
                        </p>
                        <p style="font-size: 12px; margin: 0 0 15px 0; color: white; line-height: 1.4;">
                            Click <strong>HERE</strong> for departure dates and<br>pricing details
                        </p>
                        <p style="font-size: 12px; margin: 0 0 25px 0; color: white; line-height: 1.5;">
                            <strong>To Reserve Your Trip</strong><br>
                            click <strong>HERE</strong> or call<br>
                            (USA / Canada) 1 - (860) 856 5858<br>
                            or Sales 1 - (917) 9832727
                        </p>

                        <!-- Logo blanco -->
                        <img src="' . $this->get_logo_white_base64() . '" alt="Valencia Travel" style="width: 150px; height: auto;" />
                    </div>
                </div>
            </div>

            <!-- Columna derecha: 56% -->
            <div style="float: left; width: 56%; padding: 15px; box-sizing: border-box; font-size: 12px;">
                <h2 style="color: #1E66B1; font-size: 16px; margin-bottom: 10px; font-weight: bold;">Why Valencia Travel?</h2>

                <h3 style="color: #1E66B1; font-size: 13px; margin: 10px 0 5px 0; font-weight: bold;">Extraordinary Life Experiences</h3>
                <p style="font-size: 12px; margin: 0 0 8px 0; line-height: 1.4;">We believe that travel is more than sightseeing—it\'s about transformation, connection, and freedom. Our journeys are designed to create extraordinary life experiences that stay with you long after you return home.</p>

                <h3 style="color: #1E66B1; font-size: 13px; margin: 10px 0 5px 0; font-weight: bold;">Reconnection with The Planet</h3>
                <p style="font-size: 12px; margin: 0 0 8px 0; line-height: 1.4;">Experience profound wellness through immersion in local culture and pristine natural environments. Our tours help you reconnect with the planet and discover the healing power of authentic travel experiences.</p>

                <h3 style="color: #1E66B1; font-size: 13px; margin: 10px 0 5px 0; font-weight: bold;">Profound Local Experience</h3>
                <p style="font-size: 12px; margin: 0 0 8px 0; line-height: 1.4;">Go beyond tourist attractions with deeply immersive experiences that reveal the true heart of Peru. Engage with local communities, traditions, and history in meaningful ways that create lasting connections.</p>

                <h3 style="color: #1E66B1; font-size: 13px; margin: 10px 0 5px 0; font-weight: bold;">Attention 24/7</h3>
                <p style="font-size: 12px; margin: 0 0 8px 0; line-height: 1.4;">Our dedicated team provides round-the-clock support throughout your journey. Whether you\'re in Cusco or exploring remote destinations, we\'re always available to ensure your comfort and safety.</p>

                <h3 style="color: #1E66B1; font-size: 13px; margin: 10px 0 5px 0; font-weight: bold;">100% Guaranteed Departures</h3>
                <p style="font-size: 12px; margin: 0 0 8px 0; line-height: 1.4;">Book with confidence knowing that every scheduled departure is guaranteed. Once you confirm your reservation, your adventure is set—no cancellations, no postponements.</p>

                <h3 style="color: #1E66B1; font-size: 13px; margin: 10px 0 5px 0; font-weight: bold;">Guarantee of Best Quality</h3>
                <p style="font-size: 12px; margin: 0 0 8px 0; line-height: 1.4;">We maintain the highest international standards in every aspect of our service. Our guides are expertly trained, our accommodations carefully selected, and our itineraries meticulously crafted to ensure excellence.</p>

                <h3 style="color: #1E66B1; font-size: 13px; margin: 10px 0 5px 0; font-weight: bold;">Responsible Travel</h3>
                <p style="font-size: 12px; margin: 0 0 8px 0; line-height: 1.4;">We are committed to sustainable tourism that creates positive impact. Our practices support local communities, protect natural environments, and preserve cultural heritage for future generations.</p>
            </div>

                <div style="clear: both;"></div>
            </div>
        </div>
    </div>';

        // Add itinerary pages
        $itinerary = $this->package_data['itinerary'] ?? [];
        if (!empty($itinerary)) {
            $first_itinerary_day = true;
            foreach ($itinerary as $index => $day) {
                // Skip inactive days
                if (isset($day['active']) && !$day['active']) {
                    continue;
                }

                $day_number = $day['order'] ?? ($index + 1);
                $title = $day['title'] ?? '';
                $content = $day['content'] ?? '';
                $accommodation = $day['accommodation'] ?? '';
                $altitude = $day['altitude'] ?? '';
                $items = $day['items'] ?? [];
                $day_gallery = $day['gallery'] ?? [];

                // Get first image from day's gallery
                $image_url = '';
                if (!empty($day_gallery)) {
                    $image_id = null;

                    if (is_array($day_gallery[0])) {
                        $image_id = $day_gallery[0]['ID'] ?? $day_gallery[0]['id'] ?? null;
                    } elseif (is_numeric($day_gallery[0])) {
                        $image_id = $day_gallery[0];
                    }

                    if ($image_id) {
                        $image_array = wp_get_attachment_image_src($image_id, 'medium_large');
                        if (!$image_array) {
                            $image_array = wp_get_attachment_image_src($image_id, 'large');
                        }

                        if ($image_array && isset($image_array[0])) {
                            $image_url = $this->get_image_base64($image_array[0]);
                        }
                    }
                }

                // Clean HTML
                $content_html = wp_kses($content, [
                    'p' => [],
                    'br' => [],
                    'strong' => [],
                    'b' => [],
                    'em' => [],
                    'i' => [],
                    'ul' => [],
                    'ol' => [],
                    'li' => [],
                ]);

                // Only add pagebreak and activate columns/headers for the first day
                if ($first_itinerary_day) {
                    $html .= '
    <pagebreak />
    <sethtmlpageheader name="MainHeaderOdd" page="O" value="1" show-this-page="1" />
    <sethtmlpageheader name="MainHeaderEven" page="E" value="1" show-this-page="1" />
    <sethtmlpagefooter name="MainFooterOdd" page="O" value="1" show-this-page="1" />
    <sethtmlpagefooter name="MainFooterEven" page="E" value="1" show-this-page="1" />

    <!-- Itinerary Pages - Continuous Flow -->
    <div class="itinerary-page">
        <columns column-count="2" vAlign="J" column-gap="0" />
        <div style="padding: 0 5mm;">';
                    $first_itinerary_day = false;
                }

                $html .= '
        <h2>Day ' . $day_number . ' - ' . esc_html($title) . '</h2>

        ' . ($image_url ? '<img src="' . $image_url . '" alt="Day ' . $day_number . '" style="max-width: 100%;">' : '') . '

        <div>' . $content_html . '</div>

        ' . (!empty($items) ? '
        <div>
            <h3>Services & Activities</h3>
            <ul>
                ' . implode('', array_map(function($item) {
                    $type_service = '';
                    if (!empty($item['type_service'])) {
                        if (is_numeric($item['type_service'])) {
                            $term = get_term($item['type_service'], 'type_service');
                            if ($term && !is_wp_error($term)) {
                                $type_service = $term->name;
                            }
                        } else {
                            $type_service = $item['type_service'];
                        }
                    }
                    $text = $item['text'] ?? '';
                    if ($type_service && $text) {
                        return '<li><strong>' . esc_html($type_service) . ':</strong> ' . esc_html($text) . '</li>';
                    } elseif ($text) {
                        return '<li>' . esc_html($text) . '</li>';
                    }
                    return '';
                }, $items)) . '
            </ul>
        </div>
        ' : '') . '

        ' . (($accommodation || $altitude) ? '
        <div class="info-box">
            ' . ($accommodation ? '<p style="margin: 0 0 8px 0;"><strong style="color: #1E66B1;">Accommodation:</strong> ' . esc_html($accommodation) . '</p>' : '') . '
            ' . ($altitude ? '<p style="margin: 0;"><strong style="color: #1E66B1;">Maximum Altitude:</strong> ' . esc_html($altitude) . ' meters above sea level</p>' : '') . '
        </div>
        ' : '') . '
';
            }

            // Add inclusions/exclusions section (continues in two columns)
            $included = $this->package_data['included'] ?? '';
            $not_included = $this->package_data['not_included'] ?? '';

            if (!empty($included) || !empty($not_included)) {
                $html .= '

        <!-- Inclusions/Exclusions Section -->
        <div style="margin-top: 30px;">
            ' . (!empty($included) ? '
            <div style="margin-bottom: 20px;">
                <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 0 0 10px 0;">✅ What is Included</h2>
                ' . $included . '
            </div>
            ' : '') . '

            ' . (!empty($not_included) ? '
            <div>
                <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 0 0 10px 0;">❌ What is NOT Included</h2>
                ' . $not_included . '
            </div>
            ' : '') . '
        </div>';
            }

            // Add additional information section
            $html .= '

        <!-- Additional Information Section -->
        <div style="margin-top: 30px;">
            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 20px 0 10px 0;">Extending Your Peru Vacation</h2>
            <p style="margin: 0 0 15px 0;">Although Machu Picchu is the country\'s signature attraction, Peru boasts a wealth of fascinating archaeological sites, towns, and natural wonders that you can visit before or after your Peru trip. Among the most popular destinations are the Colca Canyon, to see the giant Andean condor; Lake Titicaca, the highest navigable lake in the world; Arequipa, a stunning colonial city in the south; and the Nazca lines, ancient geoglyphs etched into the desert. Please note that if you decide to depart early or extend your stay, Valencia Travel cannot reschedule your train travel from Aguas Calientes back to Cusco. You are responsible for coordinating your own transportation to connect with your departing flight. For assistance with making these arrangements, we recommend arranging this with your travel advisor.</p>

            <div style="background-color: #E6F5FF; padding: 12px; margin: 15px 0; border-radius: 5px;">
                <h2 style="color: #333; font-size: 16px; font-weight: bold; margin: 0 0 8px 0;">Transparency</h2>
                <p style="margin: 0; color: #333;">We are always keen to improve this Peru itinerary. We\'ll be certain to notify you of any important changes prior to your departure and other changes may be communicated by your guide during the tour. We also appreciate your honest feedback throughout the tour and booking process, to constantly improve our service.</p>
            </div>

            <div style="background-color: #E6F5FF; padding: 12px; margin: 15px 0; border-radius: 5px;">
                <h2 style="color: #333; font-size: 16px; font-weight: bold; margin: 0 0 8px 0;">Dedication to Fast or Slow Travel!</h2>
                <p style="margin: 0; color: #333;">Our trips are designed with flexibility in mind. We include the freedom to experience the best of the region the way YOU prefer. Are you on a tight travel schedule? We can show you the way to see everything, in the quickest way possible! Want some free time, or take things slower? Not a problem! Your guide will make sure you know when its best to enjoy some down time, without missing out on anything. Our aim is to travel at YOUR preferred pace, so you can fit everything in, without</p>
            </div>

            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 20px 0 10px 0;">Arrival and Departure</h2>
            <p style="margin: 0 0 15px 0;">After reviewing the following information, please make sure you have told us your arrival and departure plans at the time of booking. All prices are in US dollars and subject to change.</p>
            <p style="margin: 0 0 15px 0;"><strong>Additional Information:</strong> Meet your Valencia Travel Guide in the hotel lobby at the above-mentioned time. Please let the hotel staff know if you cannot make it for any reason and they will inform your guide.</p>

            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 20px 0 10px 0;">Clothing and Luggage</h2>
            <p style="margin: 0 0 15px 0;">Please be in the lobby ready to go! Dressed in your hiking/tour clothes and bring your luggage and a daypack with your rain gear and anything else you may want for today\'s hike or tour. Your guide will inform you at the briefing what luggage to take with you and what luggage to leave at the hotel, during the trip briefing.</p>

            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 20px 0 10px 0;">Flight Details</h2>
            <p style="margin: 0 0 15px 0;">We recommend that you fly into Jorge Chávez International Airport in Lima, Peru (airport code: LIM; www.lima-airport.com). Alejandro Velasco Astete International Airport is the airport in Cusco (CUZ). You may need to purchase tickets for these flights separately. Airlines flying to Lima include American, LATAM, United, Copa, JetBlue and Delta. Airlines flying to Cusco include LATAM, Avianca, Star Peru and Peruvian Airlines. Depending on your flight schedule, you may need to stay overnight in Lima before catching your connection to Cusco. We recommend booking a flight into Cusco that arrives prior to 10 a.m.; we\'ve found that earlier flights are generally subject to fewer delays from afternoon weather in the mountains. We also recommend reconfirming your Lima-Cusco flight before you depart. Please be aware that all passengers arriving in Lima, even those continuing on to a connecting flight (regardless of the airline), need to collect their baggage in Lima Airport and pass through customs. Luggage will not be automatically transferred to the domestic flight, even if the tags indicate a final destination other than Lima. If you are connecting to a flight elsewhere in Peru (such as Cusco) on the same day as your arrival in- country, you will need to pass through customs and then head to the Departures zone and re-check your luggage at the airline desk. This may require you to exit and re-enter the airport building. For this reason, we strongly recommend you allow a connection time of at least 3 hours in Lima airport between any two flights. For help arranging air transportation for your Peru tour please work with your travel advisor, at Valencia Travel. Our professional expert travel advisors are ready to assist you with any of your travel needs, including the domestic flights necessary for your trip. You can also book directly with the airline, or through a travel website.</p>

            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 20px 0 10px 0;">Flight Arrangements</h2>
            <p style="margin: 0 0 15px 0;">If you have reserved domestic flights with Valencia Travel, your flight details will be sent to you at time of booking or soon after. Do not schedule your flights until your reservation is confirmed.</p>

            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 20px 0 10px 0;">Departing Early or Extending your Stay in Peru</h2>
            <p style="margin: 0 0 15px 0;">If you opt to depart early or stay in Peru beyond your Valencia Travel trip, please be aware that you are responsible for coordinating your own transportation to connect with your departing flight.</p>

            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 20px 0 10px 0;">Land Transportation in Cusco</h2>
            <p style="margin: 0 0 15px 0;">Valencia Travel has its own fleet of vehicles or the comfort and safety of our passengers. Most of our Peru Tours include an airport pick-up in Lima and Cusco. Please check the details of your particular itinerary to make sure your airport transfer is included in your tour, or ask your travel advisor. A Taxi service from the Cusco airport to the hotel costs about $10.</p>

            <div style="background-color: #E6F5FF; padding: 12px; margin: 15px 0; border-radius: 5px;">
                <h2 style="color: #333; font-size: 16px; font-weight: bold; margin: 0 0 8px 0;">PRICES & SCHEDULES</h2>
                <p style="margin: 0; color: #333;">All prices and schedules of third-party services were current at the time of printing, but are subject to change at any time.</p>
            </div>

            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 20px 0 10px 0;">During Your Trip</h2>

            <h3 style="color: #1E66B1; font-size: 13px; font-weight: bold; margin: 15px 0 8px 0;">Valencia Travel Guides</h3>
            <p style="margin: 0 0 15px 0;">Your guide on your Valenca Travel Peru Trip plays many roles during your travel packages. They are a knowledgeable guide, host, caretaker, naturalist, chef, historian, trouble-shooter, interpreter and photographer! These remarkable individuals have highly developed talents for making people comfortable, handling the travel logistics and for successfully navigating a wide range of unexpected situations. They are hard-working, committed and passionate about Peru to ensure you enjoy an exceptional Peru vacation, that is seamless, flexible and tailored to your needs. With their knowledge, professionalism, enthusiasm and service ethic, they\'re the number- one reason people return to Peru to travel with Valencia Travel. We are happy that you will be able to meet them in person!</p>

            <h3 style="color: #1E66B1; font-size: 13px; font-weight: bold; margin: 15px 0 8px 0;">Meals</h3>
            <p style="margin: 0 0 15px 0;">Food is an important part of a Peru Travel experience. For lunch we will look for the best way to capture the essence of the region that you are visiting by sampling local dishes. It may be a a boxed lunch, a meal together at a favorite restaurant; a lunch prepared by a traditional local family or the opportunity to have lunch on your own. Our dinners feature regional specialties, including "New Andean" cuisine, a distinctive blend of Andean, coastal Peruvian and international dishes. We will often not include dinners, especially when there are numerous dinner options available. This will add the element of freedom to choose where you dine on your Peru tours. All breakfasts are included in the trip price.</p>

            <h3 style="color: #1E66B1; font-size: 13px; font-weight: bold; margin: 15px 0 8px 0;">Public Holiday Travel Considerations</h3>
            <p style="margin: 0 0 15px 0;">If your trip is during a national holiday or during the holiday season, be prepared for higher costs, increased traffic, bigger crowds and slower service in many regions of Peru. Not only travelers are transiting through the country but Peruvians are also traveling, meaning an increase in hotel bookings, flight booking, archaeological site visits etc. We do our best to avoid the crowds wherever possible to avoid long queues. This is the main reason we ask for a deposit on booking your Peru tour to secure entrance tickets, transport and hotel bookings ahead of time.</p>
        </div>';

            // Close columns and divs after all content
            $html .= '
        </div>
        <columns column-count="1" />
    </div>';
        }

        // Final page - Contact Information (inverse of first page)
        $html .= '
    <pagebreak />

    <!-- Final Page - Contact Info -->
    <div style="min-height: 270mm; padding: 0; box-sizing: border-box;">
        <div style="width: 100%;">
            <!-- Left column: Contact Information (56%) - WHITE BACKGROUND -->
            <div style="float: left; width: 56%; margin-right: 4%; min-height: 270mm; padding: 40px 30px; background-color: white; box-sizing: border-box; font-size: 12px;">
                <h1 style="color: #1E66B1; font-size: 24px; font-weight: bold; margin: 0 0 20px 0;">Contact Us</h1>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td colspan="2" style="padding: 10px 0; border-bottom: 2px solid #1E66B1;">
                            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 0;">Phone Numbers</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; width: 45%;">
                            <strong style="color: #333;">Toll Free (USA/Canada):</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            1-(888)-803-8004
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;">
                            <strong style="color: #333;">Peru Office:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            +51 84 255907
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;">
                            <strong style="color: #333;">24/7 Support:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            +51 992 236 677<br>+51 979 706 446
                        </td>
                    </tr>
                </table>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td colspan="2" style="padding: 10px 0; border-bottom: 2px solid #1E66B1;">
                            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 0;">Email & Address</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; width: 45%;">
                            <strong style="color: #333;">Email:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            info@valenciatravel.com
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; vertical-align: top;">
                            <strong style="color: #333;">Address:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            Portal Panes #123<br>
                            Centro Comercial Ruiseñores<br>
                            Office #306-307<br>
                            Cusco — Peru
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0;">
                            <strong style="color: #333;">RUC:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            20490568957
                        </td>
                    </tr>
                </table>

                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                    <tr>
                        <td colspan="2" style="padding: 10px 0; border-bottom: 2px solid #1E66B1;">
                            <h2 style="color: #1E66B1; font-size: 16px; font-weight: bold; margin: 0;">Office Hours</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; width: 45%; vertical-align: top;">
                            <strong style="color: #333;">Sales & Admin:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            Mon - Sat: 8:00 AM - 1:30 PM, 3:00 PM - 5:30 PM<br>
                            Sunday: 8:00 AM - 1:30 PM
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; vertical-align: top;">
                            <strong style="color: #333;">Operations:</strong>
                        </td>
                        <td style="padding: 8px 0;">
                            24/7 - Every day
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Right column: Photos (40%) - BLUE BACKGROUND -->
            <div style="float: left; width: 40%; background-color: #E6F5FF; min-height: 270mm; padding: 60px 30px; box-sizing: border-box;">
                <!-- Top section with photos -->
                <div style="margin-bottom: 40px;">
                    <!-- Top photo centered -->
                    <div style="width: 120px; margin: 0 auto 20px auto; background: white; padding: 10px 10px 25px 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
                        ' . $image1_html . '
                    </div>

                    <!-- Two bottom photos -->
                    <div style="overflow: hidden;">
                        <!-- Left photo -->
                        <div style="float: left; width: 120px; background: white; padding: 10px 10px 25px 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.2); margin-left: 10px;">
                            ' . $image2_html . '
                        </div>
                        <!-- Right photo -->
                        <div style="float: right; width: 120px; background: white; padding: 10px 10px 25px 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.2); margin-right: 10px;">
                            ' . $image3_html . '
                        </div>
                    </div>
                </div>

                <!-- Logo at bottom -->
                <div style="text-align: center; margin-top: 120px;">
                    <img src="' . $logo_base64 . '" alt="Valencia Travel" style="width: 180px; height: auto;">
                    <p style="color: #1E66B1; font-size: 14px; font-style: italic; margin: 20px 0 0 0;">
                        "I am guide, I am guardian, I am bridge."
                    </p>
                </div>
            </div>

            <div style="clear: both;"></div>
        </div>
    </div>

</body>
</html>';

        return $html;
    }

    /**
     * Get CSS styles
     *
     * @return string
     */
    private function get_styles(): string
    {
        return '
        <style>
            /* A4 Portrait: 210mm x 297mm */
            @page {
                margin: 0;
                size: A4 portrait;
            }

            body {
                font-family: "Helvetica", "Arial", sans-serif;
                margin: 0;
                padding: 0;
                color: #333333;
                font-size: 11pt;
                line-height: 1.6;
            }

            /* First Page */
            .first-page {
                background-color: #E6F5FF;
                padding: 60px 50px;
                text-align: center;
                page-break-after: always;
                min-height: 1000px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .first-page__content {
                width: 100%;
                max-width: 550px;
                margin: 0 auto;
            }

            .first-page__logo {
                width: 200px;
                margin: 0 auto 40px;
            }

            .first-page__subtitle {
                font-size: 11pt;
                color: #1E66B1;
                text-transform: uppercase;
                letter-spacing: 3px;
                margin-bottom: 15px;
                font-weight: normal;
            }

            .first-page__title {
                font-size: 26pt;
                font-weight: bold;
                color: #333333;
                margin-bottom: 40px;
                line-height: 1.3;
            }

            .first-page__image {
                width: 100%;
                max-width: 550px;
                height: auto;
                margin: 40px auto;
                display: block;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .first-page__disclaimer {
                font-size: 10pt;
                color: #555555;
                line-height: 1.6;
                max-width: 500px;
                margin: 40px auto 0;
            }

            /* Header (pages 2+) - Fixed position */
            .pdf-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                background-color: white;
                padding: 15px 30px;
                border-bottom: 2px solid #1E66B1;
                z-index: 1000;
            }

            .pdf-header__left {
                float: left;
                width: 50%;
            }

            .pdf-header__logo {
                height: 35px;
            }

            .pdf-header__est {
                font-size: 8pt;
                color: #777777;
                margin-top: 5px;
            }

            .pdf-header__right {
                float: right;
                width: 50%;
                text-align: right;
                font-size: 10pt;
                color: #333333;
                line-height: 1.3;
            }

            .pdf-header:after {
                content: "";
                display: table;
                clear: both;
            }

            /* Footer - Fixed position */
            .pdf-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background-color: white;
                padding: 10px 30px 15px 30px;
                font-size: 9px;
                color: #333333;
                border-top: 1px solid #000000;
                z-index: 1000;
            }

            .pdf-footer__grid {
                width: 100%;
            }

            .pdf-footer__grid:after {
                content: "";
                display: table;
                clear: both;
            }

            .pdf-footer__column {
                float: left;
                width: 20%;
                padding-right: 10px;
            }

            .pdf-footer__column:last-child {
                text-align: right;
                padding-right: 0;
            }

            .pdf-footer__label {
                font-size: 9px;
                font-weight: normal;
                margin: 0 0 3px 0;
                color: #333333;
            }

            .pdf-footer__value {
                font-size: 9px;
                font-weight: bold;
                margin: 0;
                color: #333333;
            }

            .pdf-footer__link {
                color: #333333;
                text-decoration: none;
                font-weight: bold;
            }

            /* Content Page */
            .content-page {
                padding: 70px 30px;
            }

            .content-page h2 {
                font-size: 18pt;
                color: #1E66B1;
                margin: 0 0 20px 0;
                border-bottom: 3px solid #1E66B1;
                padding-bottom: 10px;
            }

            .content-page h3 {
                font-size: 14pt;
                color: #1A2C45;
                margin: 20px 0 10px 0;
            }

            .content-page p {
                margin: 0 0 12px 0;
            }

            /* Two Column Layout */
            .two-columns {
                width: 100%;
                margin: 20px 0;
            }

            .two-columns:after {
                content: "";
                display: table;
                clear: both;
            }

            .column {
                float: left;
                width: 48%;
                padding-right: 20px;
            }

            .column:last-child {
                padding-right: 0;
                padding-left: 20px;
            }

            /* Itinerary Day - Two column layout */
            .itinerary-container {
                /* column-count: 2; TEMPORALMENTE DESHABILITADO - causando 11k páginas */
                /* column-gap: 30px; */
                font-size: 12pt;
            }

            .itinerary-day__title {
                font-size: 13pt;
                font-weight: bold;
                color: #1E66B1;
                margin: 0 0 10px 0;
            }

            .itinerary-day__image {
                width: 100%;
                margin: 10px 0;
            }

            .itinerary-day__image img {
                width: 100%;
                height: auto;
                border-radius: 8px;
            }

            .itinerary-day__text {
                font-size: 12pt;
                line-height: 1.6;
                margin-bottom: 15px;
            }

            .itinerary-day__info {
                margin-top: 15px;
                padding: 12px;
                background-color: #F5F5F5;
                border-left: 3px solid #1E66B1;
                font-size: 12px;
            }

            .itinerary-day__info p {
                margin: 0 0 8px 0;
                font-size: 12px;
            }

            .itinerary-day__info p:last-child {
                margin-bottom: 0;
            }

            .itinerary-day__info strong {
                color: #1E66B1;
                font-weight: bold;
            }

            /* Lists */
            ul {
                margin: 10px 0;
                padding-left: 20px;
            }

            li {
                margin-bottom: 8px;
                line-height: 1.5;
            }

            /* Highlights */
            .highlights-box {
                background-color: #F5F5F5;
                border-left: 4px solid #1E66B1;
                padding: 15px;
                margin: 20px 0;
            }
        </style>
        ';
    }

    /**
     * Get first page (cover)
     *
     * @return string
     */
    private function get_first_page(): string
    {
        $title = $this->package_data['title'] ?? 'Package Tour';
        $image_url = $this->package_data['thumbnail'] ?? '';
        $logo_base64 = $this->get_logo_base64();

        return '
        <div class="first-page">
            <div class="first-page__content">
                <img src="' . $logo_base64 . '" alt="Valencia Travel" class="first-page__logo">

                <div class="first-page__subtitle">DETAILED ITINERARY</div>
                <h1 class="first-page__title">' . esc_html($title) . '</h1>

                ' . ($image_url ? '<img src="' . $image_url . '" alt="' . esc_attr($title) . '" class="first-page__image">' : '') . '

                <div class="first-page__disclaimer">
                    Congratulations on choosing your adventure with Valencia Travel!
                    Please note that this itinerary may change depending on local conditions.
                    Do not schedule your flights until we confirm your reservation.
                </div>
            </div>
        </div>
        ';
    }

    /**
     * Get second page (Why Valencia Travel)
     *
     * @return string
     */
    private function get_second_page(): string
    {
        // Calculate duration from days field
        $days = $this->package_data['days'] ?? 1;
        if ($days == 1) {
            $duration = 'Full Day';
        } else {
            $nights = $days - 1;
            $duration = $days . ' Days / ' . $nights . ' Night' . ($nights > 1 ? 's' : '');
        }

        // Get first 3 gallery images
        $gallery_images = $this->get_gallery_images_base64(3);

        // Build image HTML for polaroids
        $image1_html = !empty($gallery_images[0])
            ? '<img src="' . $gallery_images[0] . '" style="width: 120px; height: 90px; display: block;" />'
            : '<div style="width: 120px; height: 90px; background: #ccc;"></div>';

        $image2_html = !empty($gallery_images[1])
            ? '<img src="' . $gallery_images[1] . '" style="width: 80px; height: 60px; display: block;" />'
            : '<div style="width: 80px; height: 60px; background: #ccc;"></div>';

        $image3_html = !empty($gallery_images[2])
            ? '<img src="' . $gallery_images[2] . '" style="width: 80px; height: 60px; display: block;" />'
            : '<div style="width: 80px; height: 60px; background: #ccc;"></div>';

        return '
        <div class="content-page">
            <div style="width: 100%;">
                <!-- Columna izquierda: 40% -->
                <div style="float: left; width: 38%; margin-right: 4%;">
                    <!-- Mitad superior: celeste -->
                    <div style="background-color: #E6F5FF; min-height: 450px; padding: 30px 20px; box-sizing: border-box; text-align: center;">
                        <!-- Layout de fotos estilo polaroid -->
                        <div style="margin-bottom: 20px;">
                            <!-- Foto superior centrada -->
                            <div style="display: inline-block; background: white; padding: 8px 8px 20px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); margin-bottom: 10px;">
                                ' . $image1_html . '
                            </div>

                            <!-- Dos fotos inferiores -->
                            <div style="margin-top: 10px;">
                                <!-- Foto izquierda -->
                                <div style="display: inline-block; background: white; padding: 8px 8px 20px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); margin-right: 10px;">
                                    ' . $image2_html . '
                                </div>
                                <!-- Foto derecha -->
                                <div style="display: inline-block; background: white; padding: 8px 8px 20px 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                    ' . $image3_html . '
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial -->
                        <div style="margin-top: 25px; text-align: center;">
                            <p style="font-size: 11px; font-style: italic; color: #1E66B1; margin: 0 0 10px 0; line-height: 1.4;">
                                "The tour through Peru even exceeded<br>the high expectations I had."
                            </p>
                            <p style="font-size: 9px; color: #333; margin: 0 0 10px 0; font-weight: bold;">
                                Evans Ma66 | Gladstone, Australia
                            </p>
                            <p style="font-size: 9px; color: #1E66B1; margin: 0;">
                                Click <strong>HERE</strong> to read more reviews
                            </p>
                        </div>
                    </div>
                    <!-- Mitad inferior: azul -->
                    <div style="background-color: #1E66B1; min-height: 450px; padding: 30px 20px; box-sizing: border-box; text-align: center;">
                        <div style="color: white;">
                            <!-- Texto superior -->
                            <p style="font-size: 14px; font-weight: bold; margin: 0 0 15px 0; color: white;">
                                ' . esc_html($duration) . '
                            </p>
                            <p style="font-size: 10px; margin: 0 0 15px 0; color: white; line-height: 1.4;">
                                Click <strong>HERE</strong> for departure dates and<br>pricing details
                            </p>
                            <p style="font-size: 10px; margin: 0 0 25px 0; color: white; line-height: 1.5;">
                                <strong>To Reserve Your Trip</strong><br>
                                click <strong>HERE</strong> or call<br>
                                (USA / Canada) 1 - (860) 856 5858<br>
                                or Sales 1 - (917) 9832727
                            </p>

                            <!-- Logo blanco -->
                            <img src="' . $this->get_logo_white_base64() . '" alt="Valencia Travel" style="width: 150px; height: auto;" />
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: 56% -->
                <div style="float: left; width: 56%; padding: 20px; box-sizing: border-box; font-size: 12px;">
                    <h2 style="color: #1E66B1; font-size: 13px; margin-bottom: 15px; font-weight: bold;">Why Valencia Travel?</h2>

                    <h3 style="color: #1E66B1; font-size: 13px; margin: 15px 0 8px 0; font-weight: bold;">Extraordinary Life Experiences</h3>
                    <p style="font-size: 12px; margin: 0 0 10px 0;">We believe that travel is more than sightseeing—it\'s about transformation, connection, and freedom. Our journeys are designed to create extraordinary life experiences that stay with you long after you return home.</p>

                    <h3 style="color: #1E66B1; font-size: 13px; margin: 15px 0 8px 0; font-weight: bold;">Reconnection with The Planet</h3>
                    <p style="font-size: 12px; margin: 0 0 10px 0;">Experience profound wellness through immersion in local culture and pristine natural environments. Our tours help you reconnect with the planet and discover the healing power of authentic travel experiences.</p>

                    <h3 style="color: #1E66B1; font-size: 13px; margin: 15px 0 8px 0; font-weight: bold;">Profound Local Experience</h3>
                    <p style="font-size: 12px; margin: 0 0 10px 0;">Go beyond tourist attractions with deeply immersive experiences that reveal the true heart of Peru. Engage with local communities, traditions, and history in meaningful ways that create lasting connections.</p>

                    <h3 style="color: #1E66B1; font-size: 13px; margin: 15px 0 8px 0; font-weight: bold;">Attention 24/7</h3>
                    <p style="font-size: 12px; margin: 0 0 10px 0;">Our dedicated team provides round-the-clock support throughout your journey. Whether you\'re in Cusco or exploring remote destinations, we\'re always available to ensure your comfort and safety.</p>

                    <h3 style="color: #1E66B1; font-size: 13px; margin: 15px 0 8px 0; font-weight: bold;">100% Guaranteed Departures</h3>
                    <p style="font-size: 12px; margin: 0 0 10px 0;">Book with confidence knowing that every scheduled departure is guaranteed. Once you confirm your reservation, your adventure is set—no cancellations, no postponements.</p>

                    <h3 style="color: #1E66B1; font-size: 13px; margin: 15px 0 8px 0; font-weight: bold;">Guarantee of Best Quality</h3>
                    <p style="font-size: 12px; margin: 0 0 10px 0;">We maintain the highest international standards in every aspect of our service. Our guides are expertly trained, our accommodations carefully selected, and our itineraries meticulously crafted to ensure excellence.</p>

                    <h3 style="color: #1E66B1; font-size: 13px; margin: 15px 0 8px 0; font-weight: bold;">Responsible Travel</h3>
                    <p style="font-size: 12px; margin: 0 0 10px 0;">We are committed to sustainable tourism that creates positive impact. Our practices support local communities, protect natural environments, and preserve cultural heritage for future generations.</p>
                </div>

                <div style="clear: both;"></div>
            </div>
        </div>
        ';
    }

    /**
     * Get itinerary pages
     *
     * @return string
     */
    private function get_itinerary_pages(): string
    {
        $itinerary = $this->package_data['itinerary'] ?? [];

        if (empty($itinerary)) {
            return '';
        }

        // Build all itinerary content that will flow in two columns
        $itinerary_content = '';

        foreach ($itinerary as $index => $day) {
            // Get image for this day from itinerary gallery
            $image_url = '';
            if (!empty($day['gallery']) && is_array($day['gallery']) && !empty($day['gallery'][0])) {
                $image_url = is_array($day['gallery'][0]) ? $day['gallery'][0]['url'] : $day['gallery'][0];
            }

            // Get day data with correct field names
            $day_number = $day['order'] ?? ($index + 1);
            $title = $day['title'] ?? '';
            $content = $day['content'] ?? '';
            $accommodation = $day['accommodation'] ?? '';
            $activities = $day['activities'] ?? [];

            // Build content that will flow naturally
            $itinerary_content .= '
                <h3 class="itinerary-day__title">DAY ' . $day_number . ' - ' . esc_html($title) . '</h3>

                ' . ($image_url ? '
                <div class="itinerary-day__image">
                    <img src="' . esc_url($image_url) . '" alt="Day ' . $day_number . '">
                </div>
                ' : '') . '

                <div class="itinerary-day__text">
                    <p>' . nl2br(esc_html($description)) . '</p>

                    ' . (!empty($activities) ? '
                    <h4>Activities</h4>
                    <ul>
                        ' . implode('', array_map(function($activity) {
                            return '<li>' . esc_html($activity) . '</li>';
                        }, $activities)) . '
                    </ul>
                    ' : '') . '

                    <div class="itinerary-day__info">
                        ' . ($meals ? '<p><strong>Meals:</strong> ' . esc_html($meals) . '</p>' : '') . '
                        ' . ($accommodation ? '<p><strong>Accommodation:</strong> ' . esc_html($accommodation) . '</p>' : '') . '
                    </div>
                </div>
            ';
        }

        // Wrap all content in a page with two-column container
        $html = '
        <div class="content-page">
            ' . $this->get_header() . '

            <div class="itinerary-container">
                ' . $itinerary_content . '
            </div>

            ' . $this->get_footer() . '
        </div>
        ';

        return $html;
    }

    /**
     * Get recommendations page
     *
     * @return string
     */
    private function get_recommendations_page(): string
    {
        $inclusions = $this->package_data['inclusions'] ?? [];
        $exclusions = $this->package_data['exclusions'] ?? [];

        return '
        <div class="content-page">
            <div class="two-columns">
                <div class="column">
                    <h3 style="color: #1E66B1; font-size: 14pt;">Extending Your Peru Vacation</h3>
                    <p>Although Machu Picchu is the country\'s signature attraction, Peru boasts a wealth of fascinating archeological sites, towns, and natural wonders that you can visit before or after your Peru trip. Among the most popular destinations are the Colca Canyon, to see the giant Andean condor; Lake Titicaca, the highest navigable lake in the world; Arequipa, a stunning colonial city in the south; and the Nazca lines, ancient geoglyphs etched into the desert.</p>

                    <div style="background-color: #E6F5FF; padding: 15px; margin: 15px 0; border-radius: 4px;">
                        <h4 style="color: #1E66B1; margin: 0 0 10px 0;">Transparency</h4>
                        <p style="margin: 0;">We are always keen to improve this Peru itinerary. We\'ll be certain to notify you of any important changes prior to your departure and other changes may be communicated by your guide during the tour. We also appreciate your honest feedback throughout the tour and booking process, to constantly improve our service.</p>
                    </div>

                    <div style="background-color: #E6F5FF; padding: 15px; margin: 15px 0; border-radius: 4px;">
                        <h4 style="color: #1E66B1; margin: 0 0 10px 0;">Dedication to Fast or Slow Travel!</h4>
                        <p style="margin: 0;">Our trips are designed with flexibility in mind. We include the freedom to experience the best of the region the way YOU prefer. Are you on a tight travel schedule? We can show you the way to see everything, in the quickest way possible! Want some free time, or take things slower? Not a problem!</p>
                    </div>

                    <h3 style="color: #1E66B1; font-size: 14pt; margin-top: 20px;">Arrival and Departure</h3>
                    <p>After reviewing the following information, please make sure you have told us your arrival and departure plans at the time of booking. All prices are in US dollars and subject to change.</p>
                    <p>Additional Information: Meet your Valencia Travel Guide in the hotel lobby at the above-mentioned time. Please let the hotel staff know if you cannot make it for any reason and they will inform your guide.</p>
                </div>

                <div class="column">
                    <h3 style="color: #1E66B1; font-size: 14pt;">Clothing and Luggage</h3>
                    <p>Please be in the lobby ready to go! Dressed in your hiking tour clothes and bring your luggage and a daypack with your rain gear and anything else you may want for today\'s hike or tour. Your guide will inform you at the briefing what luggage to take with you and what luggage to leave at the hotel, during the trip briefing.</p>

                    <h3 style="color: #1E66B1; font-size: 14pt; margin-top: 20px;">Flight Details</h3>
                    <p>We recommend that you fly into Jorge Chávez International Airport in Lima, Peru (airport code: LIM). Alejandro Velasco Astete International Airport is the airport in Cusco (CUZ). You may need to purchase tickets for these flights separately.</p>
                    <p>Depending on your flight schedule, you may need to stay overnight in Lima before catching your connection to Cusco. We recommend booking a flight into Cusco that arrives prior to 10 a.m.; we\'ve found that earlier flights are generally subject to fewer delays from afternoon weather in the mountains.</p>
                    <p>Please be aware that all passengers arriving in Lima, even those continuing on to a connecting flight, need to collect their baggage in Lima Airport and pass through customs. For this reason, we strongly recommend you allow a connection time of at least 3 hours in Lima airport between any two flights.</p>
                </div>
            </div>
        </div>
        <div style="page-break-after: always;"></div>
        ';
    }

    /**
     * Get contact page
     *
     * @return string
     */
    private function get_contact_page(): string
    {
        return '
        <div class="content-page">
            <h2>Contact Us</h2>

            <p style="font-size: 12pt; margin-bottom: 30px;">
                We\'re here to help you plan the perfect Peruvian adventure.
                Contact us anytime with questions or to customize your itinerary.
            </p>

            <h2 style="color: #1E66B1; font-size: 15pt;">Valencia Travel Contact Details</h2>

            <div class="two-columns">
                <div class="column" style="width: 50%;">
                    <h3 style="color: #1E66B1; font-size: 13pt;">Our Address:</h3>
                    <p>Portal Panes #123<br>
                    Centro Comercial Ruiseñores<br>
                    Office #: 306-307<br>
                    Cusco - Peru</p>

                    <h3 style="color: #1E66B1; font-size: 13pt;">Hours of Operation:</h3>
                    <p>Monday through Friday: 8:00 am to 6:00 pm<br>
                    Saturday: 8 to 12 pm<br>
                    Sunday: Closed</p>

                    <h3 style="color: #1E66B1; font-size: 13pt;">Email addresses:</h3>
                    <p>
                        <span style="color: #1E66B1; text-decoration: underline;">info@valenciatravelcusco.com</span><br>
                        <span style="color: #1E66B1; text-decoration: underline;">gestion@valenciatravelcusco.com</span><br>
                        <span style="color: #1E66B1; text-decoration: underline;">sales@valenciatravelcusco.com</span><br>
                        <span style="color: #1E66B1; text-decoration: underline;">rene@valenciatravelcusco.com</span>
                    </p>

                    <h3 style="color: #1E66B1; font-size: 13pt;">Phone Numbers:</h3>
                    <p>
                        <strong>USA and Canada Toll Free:</strong> 1 - (888) 803 - 8004<br>
                        <strong>Peru:</strong> (+51) 84 255097<br>
                        <strong>24/7 Assistant:</strong> (+51) 979706464
                    </p>
                </div>

                <div class="column" style="width: 25%; background-color: #1A2C45; color: white; padding: 20px;">
                    <div style="text-align: center;">
                        <p style="font-weight: bold; font-size: 14pt; margin-bottom: 20px;">Thank you for choosing</p>
                        <p style="font-size: 16pt; font-weight: bold; margin-bottom: 20px;">VALENCIA TRAVEL</p>
                        <p style="font-size: 11pt;">We look forward to creating unforgettable memories with you in Peru.</p>
                    </div>
                </div>
            </div>
        </div>
        ';
    }

    /**
     * Get header HTML
     *
     * @return string
     */
    private function get_header(): string
    {
        $logo_base64 = $this->get_logo_base64();

        return '
        <div class="pdf-header">
            <div class="pdf-header__left">
                <img src="' . $logo_base64 . '" alt="Valencia Travel" class="pdf-header__logo">
            </div>
            <div class="pdf-header__right">
                Portal Panes #123<br>
                C.C. Rucceros Office #306-307<br>
                Cusco - Peru
            </div>
        </div>
        ';
    }

    /**
     * Get footer HTML
     *
     * @return string
     */
    private function get_footer(): string
    {
        return '
        <div class="pdf-footer">
            <div class="pdf-footer__grid">
                <div class="pdf-footer__column">
                    <p class="pdf-footer__label">USA and Canada Toll Free:</p>
                    <p class="pdf-footer__value">1 - (888) 803 - 8004</p>
                </div>
                <div class="pdf-footer__column">
                    <p class="pdf-footer__label">E-mail:</p>
                    <p class="pdf-footer__value pdf-footer__link">info@valenciatravelcusco.com</p>
                </div>
                <div class="pdf-footer__column">
                    <p class="pdf-footer__label">Peru:</p>
                    <p class="pdf-footer__value">(+51) 84 255097</p>
                </div>
                <div class="pdf-footer__column">
                    <p class="pdf-footer__label">24/7 Assistant:</p>
                    <p class="pdf-footer__value">(+51) 979706464</p>
                </div>
                <div class="pdf-footer__column">
                    <p class="pdf-footer__label">Web:</p>
                    <p class="pdf-footer__value pdf-footer__link">www.valenciatravelcusco.com</p>
                </div>
            </div>
        </div>
        ';
    }

    /**
     * Get logo as base64
     *
     * @return string
     */
    private function get_logo_base64(): string
    {
        if (!file_exists($this->logo_path)) {
            return '';
        }

        $image_data = file_get_contents($this->logo_path);
        $base64 = base64_encode($image_data);

        return 'data:image/png;base64,' . $base64;
    }

    /**
     * Convert image URL to base64
     *
     * @param string $url Image URL
     * @return string Base64 data URI or empty string
     */
    private function get_image_base64(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        // Convert URL to local path if it's a local WordPress URL
        $upload_dir = wp_upload_dir();
        $base_url = $upload_dir['baseurl'];
        $base_path = $upload_dir['basedir'];

        // Check if this is a local WordPress upload
        if (strpos($url, $base_url) === 0) {
            // Convert URL to local file path
            $file_path = str_replace($base_url, $base_path, $url);

            if (file_exists($file_path)) {
                $image_data = file_get_contents($file_path);
            } else {
                return '';
            }
        } else {
            // Remote URL - use WordPress HTTP API
            $response = wp_remote_get($url, ['timeout' => 30]);

            if (is_wp_error($response)) {
                return '';
            }

            $image_data = wp_remote_retrieve_body($response);
        }

        if (empty($image_data)) {
            return '';
        }

        // Detect image type from URL or content
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));

        $mime_types = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];

        $mime_type = $mime_types[$extension] ?? 'image/jpeg';
        $base64 = base64_encode($image_data);

        return 'data:' . $mime_type . ';base64,' . $base64;
    }

    /**
     * Get white logo as base64
     *
     * @return string
     */
    private function get_logo_white_base64(): string
    {
        $logo_white_path = plugin_dir_path(dirname(dirname(__FILE__))) . 'assets/images/logo-white.png';

        // If white logo doesn't exist, use color logo as fallback
        if (!file_exists($logo_white_path)) {
            $logo_white_path = $this->logo_path;
        }

        if (!file_exists($logo_white_path)) {
            return '';
        }

        $image_data = file_get_contents($logo_white_path);
        $base64 = base64_encode($image_data);

        return 'data:image/png;base64,' . $base64;
    }

    /**
     * Get gallery images as base64
     *
     * @param int $limit Number of images to get
     * @return array
     */
    private function get_gallery_images_base64(int $limit = 3): array
    {
        $gallery = $this->package_data['gallery'] ?? [];
        $images = [];

        if (empty($gallery)) {
            return $images;
        }

        $count = 0;
        foreach ($gallery as $image_data) {
            if ($count >= $limit) {
                break;
            }

            $image_url = '';
            $image_id = null;
            $source_url = null;

            // Handle different formats
            if (is_array($image_data)) {
                $image_id = $image_data['ID'] ?? $image_data['id'] ?? null;
                $source_url = $image_data['url'] ?? null;
            } elseif (is_numeric($image_data)) {
                $image_id = $image_data;
            }

            // Try to get image by ID first
            if ($image_id) {
                // Get medium size for polaroids
                $image_array = wp_get_attachment_image_src($image_id, 'medium');
                if (!$image_array) {
                    $image_array = wp_get_attachment_image_src($image_id, 'thumbnail');
                }

                if ($image_array && isset($image_array[0])) {
                    $source_url = $image_array[0];
                }
            }

            // Convert URL to base64
            if ($source_url) {
                $image_url = $this->get_image_base64($source_url);
            }

            if (!empty($image_url)) {
                $images[] = $image_url;
                $count++;
            }
        }

        return $images;
    }

    /**
     * Get header HTML for mPDF
     *
     * @return string
     */
    public function get_header_html(): string
    {
        $logo_base64 = $this->get_logo_base64();

        return '
        <div class="pdf-header">
            <div class="pdf-header__left">
                <img src="' . $logo_base64 . '" alt="Valencia Travel" class="pdf-header__logo">
            </div>
            <div class="pdf-header__right">
                Portal Panes #123<br>
                C.C. Rucceros Office #306-307<br>
                Cusco - Peru
            </div>
        </div>';
    }

    /**
     * Get footer HTML for mPDF
     *
     * @return string
     */
    public function get_footer_html(): string
    {
        return '
        <div class="pdf-footer">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">USA/Canada:</p>
                        <p class="pdf-footer__value">1-(888)803-8004</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">E-mail:</p>
                        <p class="pdf-footer__value">info@valenciatravel.com</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">Peru:</p>
                        <p class="pdf-footer__value">(+51)84-255097</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top;">
                        <p class="pdf-footer__label">24/7:</p>
                        <p class="pdf-footer__value">(+51)979706464</p>
                    </td>
                    <td style="width: 20%; padding: 0 5px; vertical-align: top; text-align: right;">
                        <p class="pdf-footer__label">Web:</p>
                        <p class="pdf-footer__value">valenciatravel.com</p>
                    </td>
                </tr>
            </table>
        </div>';
    }
}
