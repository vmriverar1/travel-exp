<?php
/**
 * REST Routes for Package PDF Generation
 *
 * Handles API endpoint for generating package PDFs
 *
 * @package Travel\Components
 * @since 1.0.0
 */

namespace Travel\Components;

use Travel\Components\Database\PackagePdfLeadsTable;
use Travel\Components\PdfTemplates\PackageItineraryTemplate;
use Mpdf\Mpdf;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

class RestRoutesPackagePdf
{
    /**
     * Leads table instance
     *
     * @var PackagePdfLeadsTable
     */
    private $leads_table;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->leads_table = new PackagePdfLeadsTable();
    }

    /**
     * Register REST routes
     */
    public function register_routes(): void
    {
        register_rest_route('travel/v1', '/generate-package-pdf', [
            'methods' => 'POST',
            'callback' => [$this, 'generate_pdf'],
            'permission_callback' => '__return_true', // Public endpoint
            'args' => [
                'package_id' => [
                    'required' => true,
                    'type' => 'integer',
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0;
                    },
                ],
                'user_name' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'validate_callback' => function($param) {
                        return !empty($param) && strlen($param) >= 2;
                    },
                ],
                'user_email' => [
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_email',
                    'validate_callback' => 'is_email',
                ],
            ],
        ]);

        // Preview endpoint (for testing in browser)
        register_rest_route('travel/v1', '/preview-package-pdf/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'preview_pdf'],
            'permission_callback' => '__return_true',
            'args' => [
                'id' => [
                    'required' => true,
                    'type' => 'integer',
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0;
                    },
                ],
            ],
        ]);

        // Debug HTML endpoint
        register_rest_route('travel/v1', '/debug-package-html/(?P<id>\d+)', [
            'methods' => 'GET',
            'callback' => [$this, 'debug_html'],
            'permission_callback' => '__return_true',
            'args' => [
                'id' => [
                    'required' => true,
                    'type' => 'integer',
                    'validate_callback' => function($param) {
                        return is_numeric($param) && $param > 0;
                    },
                ],
            ],
        ]);
    }

    /**
     * Debug HTML endpoint callback
     *
     * @param WP_REST_Request $request Request object
     * @return void
     */
    public function debug_html(WP_REST_Request $request)
    {
        try {
            $package_id = $request->get_param('id');

            // Verify package exists and is published
            $package = get_post($package_id);

            if (!$package || $package->post_type !== 'package') {
                wp_die('Package not found', 'Package Error', ['response' => 404]);
            }

            // Get package data
            $package_data = $this->get_package_data($package_id);

            // Generate HTML
            $template = new PackageItineraryTemplate($package_data);
            $html = $template->generate();

            // Output HTML directly
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
            exit;

        } catch (\Exception $e) {
            error_log('Package HTML debug error: ' . $e->getMessage());
            wp_die('Failed to generate HTML: ' . $e->getMessage());
        }
    }

    /**
     * Preview PDF endpoint callback (for browser testing)
     *
     * @param WP_REST_Request $request Request object
     * @return void
     */
    public function preview_pdf(WP_REST_Request $request)
    {
        try {
            $package_id = $request->get_param('id');

            // Verify package exists and is published
            $package = get_post($package_id);

            if (!$package || $package->post_type !== 'package' || $package->post_status !== 'publish') {
                wp_die('Package not found or not available', 'Package Error', ['response' => 404]);
            }

            // Get package data
            $package_data = $this->get_package_data($package_id);

            // Generate PDF
            $pdf_content = $this->create_pdf($package_data);

            // Output PDF directly to browser
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . sanitize_file_name($package->post_title) . '-preview.pdf"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            echo $pdf_content;
            exit;

        } catch (\Exception $e) {
            error_log('Package PDF preview error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            wp_die('Failed to generate PDF preview: ' . $e->getMessage(), 'PDF Generation Error', ['response' => 500]);
        }
    }

    /**
     * Generate PDF endpoint callback
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response|WP_Error
     */
    public function generate_pdf(WP_REST_Request $request)
    {
        try {
            $package_id = $request->get_param('package_id');
            $user_name = $request->get_param('user_name');
            $user_email = $request->get_param('user_email');

            // Verify package exists and is published
            $package = get_post($package_id);

            if (!$package || $package->post_type !== 'package' || $package->post_status !== 'publish') {
                return new WP_Error(
                    'invalid_package',
                    'Package not found or not available',
                    ['status' => 404]
                );
            }

            // Get package data
            $package_data = $this->get_package_data($package_id);

            // Save lead to database
            $lead_id = $this->leads_table->insert_lead($package_id, $user_name, $user_email);

            if (!$lead_id) {
                error_log('Failed to save PDF lead for package ' . $package_id);
            }

            // Generate PDF
            $pdf_content = $this->create_pdf($package_data);

            // Return PDF as base64 (for AJAX handling)
            return new WP_REST_Response([
                'success' => true,
                'pdf' => base64_encode($pdf_content),
                'filename' => sanitize_file_name($package->post_title) . '-itinerary.pdf',
                'message' => 'PDF generated successfully',
            ], 200);

        } catch (\Exception $e) {
            error_log('Package PDF generation error: ' . $e->getMessage());

            return new WP_Error(
                'pdf_generation_failed',
                'Failed to generate PDF. Please try again later.',
                ['status' => 500]
            );
        }
    }

    /**
     * Get package data from WordPress
     *
     * @param int $package_id Package post ID
     * @return array
     */
    private function get_package_data(int $package_id): array
    {
        $package = get_post($package_id);

        // Get featured image (convert to base64 for PDF compatibility)
        $thumbnail_id = get_post_thumbnail_id($package_id);
        $thumbnail_url = '';
        if ($thumbnail_id) {
            $thumbnail_array = wp_get_attachment_image_src($thumbnail_id, 'large');
            $thumbnail_url = $thumbnail_array[0] ?? '';
        }

        // Get gallery with IDs
        $gallery_field = get_field('gallery', $package_id);
        $gallery = [];
        if (is_array($gallery_field)) {
            foreach ($gallery_field as $image) {
                if (is_array($image) && isset($image['url'])) {
                    $gallery[] = [
                        'ID' => $image['ID'] ?? $image['id'] ?? null,
                        'ID' => $image['ID'] ?? $image['id'] ?? null,
                        'url' => $image['url'],
                        'alt' => $image['alt'] ?? '',
                    ];
                }
            }
        }

        // Get itinerary
        $itinerary_field = get_field('itinerary', $package_id);
        $itinerary = is_array($itinerary_field) ? $itinerary_field : [];

        // Get inclusions/exclusions (HTML format from wizard)
        $included = get_post_meta($package_id, 'included', true);
        $not_included = get_post_meta($package_id, 'not_included', true);

        // Get days for duration calculation
        $days = get_field('days', $package_id);
        $days = is_numeric($days) ? (int)$days : 1;

        // Get days for duration calculation
        $days = get_field('days', $package_id);
        $days = is_numeric($days) ? (int)$days : 1;

        return [
            'title' => $package->post_title,
            'thumbnail' => $thumbnail_url,
            'gallery' => $gallery,
            'itinerary' => $itinerary,
            'included' => $included,
            'not_included' => $not_included,
            'days' => $days,
        ];
    }

    /**
     * Create PDF using mPDF
     *
     * @param array $package_data Package data
     * @return string PDF content
     */
    private function create_pdf(array $package_data): string
    {
        // Generate HTML from template
        $template = new PackageItineraryTemplate($package_data);
        $html = $template->generate();

        // Use WordPress upload directory for temporary files
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . '/mpdf-tmp';

        // Create temp directory if it doesn't exist
        if (!file_exists($temp_dir)) {
            wp_mkdir_p($temp_dir);
        }

        // Configure mPDF - first page has no margins (no header/footer)
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,       // First page has no header
            'margin_bottom' => 0,    // First page has no footer
            'margin_header' => 0,    // Space between header and content
            'margin_footer' => 0,    // Space between footer and content
            'default_font' => 'helvetica',
            'tempDir' => $temp_dir,
        ]);

        // Write HTML to PDF (headers/footers defined in HTML with htmlpageheader/htmlpagefooter tags)
        $mpdf->WriteHTML($html);

        // Get PDF content as string
        return $mpdf->Output('', 'S');
    }
}
