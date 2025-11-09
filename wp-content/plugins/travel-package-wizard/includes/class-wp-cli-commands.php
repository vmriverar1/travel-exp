<?php
/**
 * WP-CLI Commands for Package Builder
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_CLI')) {
    return;
}

class Aurora_Package_CLI_Commands
{
    /**
     * Generate mock package data
     *
     * ## EXAMPLES
     *
     *     wp package generate_mock
     *
     * @when after_wp_load
     */
    public function generate_mock($args, $assoc_args)
    {
        WP_CLI::line('Generating mock packages...');

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_packages();

        if ($result['success']) {
            WP_CLI::success(sprintf(
                'Created %d of %d packages!',
                $result['created'],
                $result['total']
            ));

            if (!empty($result['errors'])) {
                WP_CLI::warning('Some errors occurred:');
                foreach ($result['errors'] as $error) {
                    WP_CLI::line('  - ' . $error);
                }
            }
        } else {
            WP_CLI::error('Failed to generate packages');
        }
    }

    /**
     * Delete all packages
     *
     * ## EXAMPLES
     *
     *     wp package delete_all
     *
     * @when after_wp_load
     */
    public function delete_all($args, $assoc_args)
    {
        WP_CLI::confirm('Are you sure you want to delete ALL packages?', $assoc_args);

        WP_CLI::line('Deleting all packages...');

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->delete_all_packages();

        if ($result['success']) {
            WP_CLI::success(sprintf('Deleted %d packages!', $result['deleted']));
        } else {
            WP_CLI::error('Failed to delete packages');
        }
    }

    /**
     * Show package statistics
     *
     * ## EXAMPLES
     *
     *     wp package stats
     *
     * @when after_wp_load
     */
    public function stats($args, $assoc_args)
    {
        $packages = get_posts([
            'post_type' => 'package',
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);

        $total = count($packages);
        $published = count(array_filter($packages, function($p) { return $p->post_status === 'publish'; }));
        $draft = count(array_filter($packages, function($p) { return $p->post_status === 'draft'; }));

        WP_CLI::line('Package Statistics:');
        WP_CLI::line('-------------------');
        WP_CLI::line('Total: ' . $total);
        WP_CLI::line('Published: ' . $published);
        WP_CLI::line('Draft: ' . $draft);
    }

    /**
     * Generate footer menus
     *
     * ## EXAMPLES
     *
     *     wp package generate_footer_menus
     *
     * @when after_wp_load
     */
    public function generate_footer_menus($args, $assoc_args)
    {
        WP_CLI::line('Generating footer menus...');

        $generator = Aurora_Mock_Data_Generator::get_instance();
        $result = $generator->generate_footer_menus();

        if ($result['success']) {
            WP_CLI::success('Footer menus created successfully! 6 menus created: Top Experiences, Treks & Adventure, Culture & History, Destinations, About Machu Picchu Peru, Extra Information.');
        } else {
            WP_CLI::error('Failed to generate footer menus');
        }
    }
}

WP_CLI::add_command('package', 'Aurora_Package_CLI_Commands');
