<?php
/**
 * GitHub Theme Updater
 * 
 * Handles checking for updates from GitHub Releases and installing them correctly.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Election_Awareness_Updater {
    private $slug;
    private $repo;
    private $github_response;

    public function __construct( $slug, $repo ) {
        $this->slug = $slug;
        $this->repo = $repo;

        // Phase 4: Intercept the Update Routine
        add_filter( 'site_transient_update_themes', array( $this, 'check_for_update' ) );
        
        // Phase 5: The Installation Intercept (Rename extracted folder)
        add_filter( 'upgrader_post_install', array( $this, 'after_install' ), 10, 3 );
    }

    /**
     * Phase 2 & 3: Ping API and Compare Logic
     */
    public function check_for_update( $transient ) {
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        // 1. Target the API & Implement Caching
        $remote = $this->get_github_release();

        if ( ! $remote ) {
            return $transient;
        }

        // 2. Read Local Version
        $theme = wp_get_theme( $this->slug );
        $local_version = $theme->get( 'Version' );

        // 3. Compare with Remote (e.g., v1.2.3 vs 1.2.2)
        $remote_version = ltrim( $remote->tag_name, 'v' );

        if ( version_compare( $local_version, $remote_version, '<' ) ) {
            $res = new stdClass();
            $res->slug = $this->slug;
            $res->new_version = $remote_version;
            $res->url = "https://github.com/{$this->repo}";
            
            // Find the zip asset created by the GitHub Action
            $package_url = '';
            if ( ! empty( $remote->assets ) ) {
                foreach ( $remote->assets as $asset ) {
                    if ( strpos( $asset->name, '.zip' ) !== false ) {
                        $package_url = $asset->browser_download_url;
                        break;
                    }
                }
            }

            // Fallback to zipball_url if no asset found (though build files will be missing)
            $res->package = ! empty( $package_url ) ? $package_url : $remote->zipball_url;

            // Phase 4: Inject the Data
            $transient->response[ $this->slug ] = (array) $res;
        }

        return $transient;
    }

    /**
     * Phase 2: GitHub API Communication
     */
    private function get_github_release() {
        $transient_key = 'gh_update_' . $this->slug;
        $remote = get_transient( $transient_key );

        if ( false !== $remote ) {
            return $remote;
        }

        $url = "https://api.github.com/repos/{$this->repo}/releases/latest";
        
        $args = array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' )
            )
        );

        // Optional: If private repo, add Personal Access Token
        // $args['headers']['Authorization'] = 'token YOUR_GITHUB_TOKEN';

        $response = wp_remote_get( $url, $args );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return false;
        }

        $remote = json_decode( wp_remote_retrieve_body( $response ) );
        
        // Cache for 12 hours
        set_transient( $transient_key, $remote, 12 * HOUR_IN_SECONDS );

        return $remote;
    }

    /**
     * Phase 5: The Installation Intercept
     * GitHub zips extract to 'repo-name-tagname', we need it to be 'election-awareness'
     */
    public function after_install( $response, $hook_extra, $result ) {
        global $wp_filesystem;

        // Only run for our theme
        if ( ! isset( $hook_extra['theme'] ) || $hook_extra['theme'] !== $this->slug ) {
            return $response;
        }

        $install_directory = $result['destination'];
        $proper_directory = trailingslashit( $result['remote_destination'] ) . $this->slug;

        // If the extracted folder name is different from the slug
        if ( $install_directory !== $proper_directory ) {
            $wp_filesystem->move( $install_directory, $proper_directory );
            $result['destination'] = $proper_directory;
        }

        return $result;
    }
}

// Initialize the updater
// REPLACE 'YOUR_USERNAME/YOUR_REPO' with your actual repository path
new Election_Awareness_Updater( 'election-awareness', 'khalidARB/ElectionAwareness' );
