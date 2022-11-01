<?php
class Svgavatars{
    protected $plugin_url;
    protected $version;

    function __construct(){
        if(defined(SVGAVATARS_URL)){
            $this->plugin_url = SVGAVATARS_URL;
        }
        
        if(defined(SVGAVATARS_VERSION)){
            $this->version = SVGAVATARS_VERSION;
        }else{
            $this->version = '1.0.2';
        }
    }

    function run(){
        add_shortcode( 'svgavatars', [$this, 'svgavatars_public_view'] );
        add_action( 'wp_enqueue_scripts', [$this, 'enqueue_styles'] );
		add_action( 'wp_ajax_save_avatar', [$this, 'save_avatar'] );
		add_action( 'wp_ajax_nopriv_save_avatar', [$this, 'save_avatar'] );
		add_action( 'wp_ajax_downlaod_temp_image', [$this, 'downlaod_temp_image'] );
		add_action( 'wp_ajax_nopriv_downlaod_temp_image', [$this, 'downlaod_temp_image'] );
		add_action( 'wp_ajax_delete_downloaded_file', [$this, 'delete_downloaded_file'] );
		add_action( 'wp_ajax_nopriv_delete_downloaded_file', [$this, 'delete_downloaded_file'] );
    }

    function enqueue_styles(){
        wp_enqueue_style( 'normalize', plugin_dir_url( dirname( __FILE__ ) ).'css/normalize.css', array(), $this->version, 'all' );
        wp_enqueue_style( 'spectrum', plugin_dir_url( dirname( __FILE__ ) ).'css/spectrum.css', array('normalize'), $this->version, 'all' );
        wp_enqueue_style( 'RobotoFonts', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:400,300,700|Roboto:400,300,500,700&subset=latin,cyrillic-ext,cyrillic,latin-ext', array('normalize'), $this->version, 'all' );
        wp_enqueue_style( 'svgavatars', plugin_dir_url( dirname( __FILE__ ) ).'css/svgavatars.css', array('spectrum','RobotoFonts'), $this->version, 'all' );

        wp_enqueue_script('jquery');
        wp_enqueue_script( 'svgavatars.tools', plugin_dir_url( dirname( __FILE__ ) ).'js/svgavatars.tools.js', array('jquery'), $this->version, true );
        wp_enqueue_script( 'svgavatars.defaults', plugin_dir_url( dirname( __FILE__ ) ).'js/svgavatars.defaults.js', array('svgavatars.tools'), $this->version, true );
        wp_enqueue_script( 'svgavatars.en.js', plugin_dir_url( dirname( __FILE__ ) ).'js/languages/svgavatars.en.js', array('svgavatars.defaults'), $this->version, true );
        wp_enqueue_script( 'svgavatars.core', plugin_dir_url( dirname( __FILE__ ) ).'js/svgavatars.core.js', array('svgavatars.en.js'), microtime(), true );
        wp_localize_script('svgavatars.defaults', "svgavatars_ex", array(
			'sitepath' 		=> SVGAVATARS_URL
		));
		wp_localize_script('svgavatars.core', "svgavatars", array(
			'ajaxurl' => admin_url("admin-ajax.php")
		));
    }
	
	function save_avatar(){
		// require validation functions
		require_once(SVGAVATARS_PATH."php/validate-avatar-data.php");

		// getting and validating file name and image data from POST
		// returned $file will be an array with name and type
		$file = svgAvatars_validate_filename( $_POST['filename'] );
		if ( $file['name'] === 'invalid' ) {
			die( "Received file name doesn't match required pattern." );
		}
		if ( $file['type'] === 'invalid' ) {
			die( 'Received file type is not PNG or SVG.' );
		}
		$valid_data =  svgAvatars_validate_imagedata( $_POST['imgdata'], $file['type'] );

		if ( $file['type'] === 'png' ) {
			// check that validated image data is not empty
			if ( $valid_data !== false ) {
				$dir = SVGAVATARS_PATH.'temp-avatars/';
				if ( is_dir( $dir ) && is_writable( $dir ) ) {
					$valid_data = base64_decode( $valid_data );
					file_put_contents( $dir . $file['name'] . '.png', $valid_data );
					echo json_encode(array('saved' => 'saved'));
					die;
				} else {
					die( "The 'temp-avatars' directory does not exist or is not writable." );
				}
			} else {
				die( 'Received file PNG or SVG data is not valid.' );
			}
		} elseif ( $file['type'] === 'svg' ) {
			// check that validated code is SVG
			if ( strpos( $valid_data, '<svg xmlns="http://www.w3.org/2000/svg" version="1.1"' ) !== false && strrpos($valid_data, '</svg>', -6) !== false ) {
				$valid_data = stripcslashes( $valid_data );
				$dir = SVGAVATARS_PATH.'temp-avatars/';
				if ( is_dir( $dir ) && is_writable( $dir ) ) {
					file_put_contents( $dir . $file['name'] . '.svg', $valid_data );
					echo json_encode(array('saved' => 'saved'));
					die;
				} else {
					die( "The 'temp-avatars' directory does not exist or is not writable." );
				}
			} else {
				die( 'Received file PNG or SVG data is not valid.' );
			}
		} else {
			die( 'Received file type is not PNG or SVG.' );
		}
	}

	function delete_downloaded_file(){
		$path = $_POST['filePath'];
		if(readfile( $path )){
			unlink($path);
		}

		die;
	}

	function downlaod_temp_image(){
		// require validation functions
		require_once( SVGAVATARS_PATH.'php/validate-avatar-data.php' );

		// getting file name and downloading name from GET
		$dlname = svgAvatars_sanitize_downloading_name( $_GET['downloadingname'] );
		$filename = svgAvatars_validate_filename( $_GET['filename'] );
		$fileUrl = SVGAVATARS_URL.'temp-avatars/' . $filename['name'] . '.' . $filename['type'];
		$filePath = SVGAVATARS_PATH.'temp-avatars/' . $filename['name'] . '.' . $filename['type'];
		$type = $filename['type'];

		echo json_encode(array(
			"fileUrl" => $fileUrl,
			"filePath" => $filePath
		));
		die;
	}

    function svgavatars_public_view(){
        ob_start();
        $output = '<div style="min-width: 100%;" id="svgAvatars"></div>';
        ob_get_clean();
        return $output;
    }
}