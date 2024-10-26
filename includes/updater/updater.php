<?php

namespace MVC\Updater;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Updater {

    private $plugin_version;
    private $plugin_name;
    private $plugin_slug;
    private $response_transient_key;

    public function __construct() {
        $this->plugin_version = MVC_VERSION;
        $this->plugin_name = MVC_PLUGIN_BASENAME;
        $this->plugin_slug = MVC_PLUGIN_SLUG;
        $this->response_transient_key = md5( sanitize_key( $this->plugin_name ) . '_response_transient' );

        $this->setup_hooks();
    }

    private function setup_hooks() {
        add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update' ], 50 );
        add_action( 'delete_site_transient_update_plugins', [ $this, 'delete_transients' ] );
        add_filter( 'plugins_api', [ $this, 'plugins_api_filter' ], 10, 3 );
        add_filter( 'wp_mail_content_type', [ $this, 'set_mail_content_type' ] );
    }

    public function check_update( $_transient_data ) {
        if ( ! is_object( $_transient_data ) ) {
            $_transient_data = new \stdClass();
        }

        // Fetch the version info from GitHub
        $version_info = $this->get_github_version_info();

        if ( is_wp_error( $version_info ) ) {
            $this->log_debug( 'GitHub version info error: ' . $version_info->get_error_message() );
            return $_transient_data; // Return if there's an error
        }

        // Compare versions and add to update response if needed
        if ( version_compare( $this->plugin_version, $version_info['new_version'], '<' ) ) {
            $_transient_data->response[ $this->plugin_name ] = (object) $version_info;
            $this->log_debug( 'Update available: ' . $version_info['new_version'] );
        } else {
            $_transient_data->no_update[ $this->plugin_name ] = (object) $version_info;
            $this->log_debug( 'No update available. Current version: ' . $this->plugin_version );
        }

        return $_transient_data;
    }

    public function plugins_api_filter( $_data, $_action = '', $_args = null ) {
        if ( 'plugin_information' !== $_action ) {
            return $_data;
        }

        if ( ! isset( $_args->slug ) || ( $_args->slug !== $this->plugin_slug ) ) {
            return $_data;
        }

        $this->log_debug( 'Returning plugin information from GitHub' );
        return (object) $this->get_github_version_info();
    }

    private function get_github_version_info() {
        // Get the cached version info from transients
        $version_info = get_transient( $this->response_transient_key );
        if ( false === $version_info ) {
            $api_url = 'https://api.github.com/repos/ezraaym/multisite-version-control/releases/latest'; // Replace with your repo's API URL
            $response = wp_remote_get( $api_url );

            if ( is_wp_error( $response ) ) {
                $this->log_debug( 'GitHub API request failed: ' . $response->get_error_message() );
                return $response; // Return error if request fails
            }

            $release_data = json_decode( wp_remote_retrieve_body( $response ), true );

            if ( ! is_array( $release_data ) || empty( $release_data['tag_name'] ) ) {
                $this->log_debug( 'Invalid version information from GitHub' );
                return new \WP_Error( 'mvc_updater_error', 'Invalid version information from GitHub' );
            }

            // Extract version info
            $version_info = [
                'new_version'    => ltrim( $release_data['tag_name'], 'v' ), // GitHub tags are usually prefixed with 'v'
                'requires'       => '5.8', // Set a default or fetch from release data if available
                'tested'         => '6.0', // Set a default or fetch from release data if available
                'download_link'  => $release_data['zipball_url'], // GitHub's ZIP download URL
                'slug'           => $this->plugin_slug,
                'plugin'         => $this->plugin_name,
                'last_updated'   => $release_data['published_at'],
                'sections'       => [
                    'description' => $release_data['body'] ?? 'Update available.'
                ]
            ];

            // Cache the version info for 12 hours
            set_transient( $this->response_transient_key, $version_info, 12 * HOUR_IN_SECONDS );
        }

        return $version_info;
    }

    public function delete_transients() {
        delete_transient( $this->response_transient_key );
    }

    public function set_mail_content_type() {
    return 'text/plain';
}


    // Debug logging function
    private function log_debug( $message ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            // Log to the WordPress debug.log file
            error_log( '[MVC Updater] ' . $message );

            // Send error log to email
            $this->send_log_email( $message );
        }
    }

    // Send error logs via email
    private function send_log_email( $message ) {
        // Set the recipient email address
        $to = 'aym82449@gmail.com'; 

        // Set the subject of the email
        $subject = 'MVC Plugin Error Log - ' . get_site_url();

        // Set the body of the email
        $body = "Site: " . get_site_url() . "\n";
        $body .= "Timestamp: " . current_time( 'mysql' ) . "\n";
        $body .= "Message: " . $message;

        // Set the email headers
        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'From: MVC Debug log <ezra@aymscores.com>', 
        ];

        // Use wp_mail to send the email
        wp_mail( $to, $subject, $body, $headers );

        // filter for headers to avoid common issues
        add_filter( 'wp_mail_content_type', function() {
            return 'text/plain';
        });
        
    }
}

