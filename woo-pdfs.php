
<?php
/**
 * Plugin Name: Bowe E-mærket Extension
 * Description: Attach PDFs to WooCommerce customer emails.
 * Plugin URI:  https://bo-we.dk/
 * Author:      Bo-we
 * Author URI:  https://bo-we.dk/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Version:     1.0
 * Text Domain: bowe-emaerket
 *
 * @package bowe-emaerket
 */

class Woo_PDFS {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('woocommerce_email_attachments', array($this, 'attach_pdfs_to_email'), 10, 3);
    }

    public function add_menu() {
        add_submenu_page(
            'tools.php',
            'woo-pdfs',
            'Woo Pdfs',
            'manage_options',
            'woo-pdfs',
            array($this, 'options_page')
        );
    }

    public function options_page() {
        ?>
        <div class="wrap">
            <h1>Woo PDF's</h1>
            <form method="post" action="options.php" novalidate="novalidate">
                <table class="form-table">
                    <tbody>
                    <?php
                        settings_fields("woo_pdfs");
                        do_settings_sections("woo_pdfs");
                    ?>

                    <tr>
                        <th scope="row">PDF's ID's</th>
                        <td>
                            <input 
                                value="<?php echo get_option('woo_pdfs'); ?>" 
                                name="woo_pdfs" 
                                type="text" 
                                id="woo_pdfs" 
                                placeholder="Comma seperated ID list" 
                                class="regular-text">
                                <p class="description" id="tagline-description">
                                Please ensure to paste in the direct ID and comma seperated
                            </p>
                        </td>

                    </tr>
          
                      <!-- 
                        Save
                        -->
                        <tr>
                            <th colspan="2">
                                <?php submit_button(); ?>
                            </th>
                        </tr>

                    </tbody>
                </table>
            </form>
        </div>
        <?php
    }

    public function register_settings() {
        register_setting('woo_pdfs', 'woo_pdfs');
    }

    public function attach_pdfs_to_email($attachments, $email_id, $order) {
        $pdf_ids = get_option('woo_pdfs');

        if(is_array($pdf_ids) && !empty($pdf_ids)) {
            foreach($pdf_ids as $pdf_id) {
                $pdf_path = $this->get_media_file_path($pdf_id);
                if(file_exists($pdf_path)) {
                    $attachments[] = $pdf_path;
                }
            }
        }

        // Assuming $terms_pdf and $other_pdf are defined elsewhere in your class
        if(isset($terms_pdf) && file_exists($terms_pdf)) {
            $attachments[] = $terms_pdf;
        }

        if(isset($other_pdf) && file_exists($other_pdf)) {
            $attachments[] = $other_pdf;
        }

        return $attachments;
    }


    private function get_media_file_path($media_id) {
        $file_url = wp_get_attachment_url($media_id);
        if ($file_url) {
            $upload_dir = wp_upload_dir();
            $file_path = str_replace($upload_dir['baseurl'] . '/', '', $file_url);
            $file_path = $upload_dir['basedir'] . '/' . $file_path;
            return $file_path;
        }
        return null;
    }
}

new Woo_PDFS();
