<?php
class NileEventsSettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
	private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Events"
		add_submenu_page(
            'edit.php?post_type=a_events',
            'Events Settings',
            'Events Settings',
            'manage_options', 
            'settings-submenu-page', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'event_option' );
        ?>
        <div class="wrap">
            <h1>Events Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'events_setting' );
                do_settings_sections( 'events-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'events_setting', // Option group
            'event_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Custom Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'events-setting-admin' // Page
        );  

        add_settings_field(
            'num_of_posts', // ID
            'Number of events to display', // Title 
            array( $this, 'num_of_posts_callback' ), // Callback
            'events-setting-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'events_order', 
            'Order of the events', 
            array( $this, 'events_order_callback' ), 
            'events-setting-admin', 
            'setting_section_id'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['num_of_posts'] ) )
            $new_input['num_of_posts'] = absint( $input['num_of_posts'] );

        if( isset( $input['events_order'] ) )
            $new_input['events_order'] = sanitize_text_field( $input['events_order'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function num_of_posts_callback()
    {
		$options = get_option( 'event_option' );
		$value = 10;
		if(!empty($options['num_of_posts'])){
			$value = $options['num_of_posts']; 
		}
	?>
	<input type="number" id="num_of_posts" class="events-number-input" name="event_option[num_of_posts]" value="<?php echo $value; ?>" min="1" />
	<?php

    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function events_order_callback()
    {

		$options = get_option( 'event_option' );
		$value = 'bydate';
		if(!empty($options['events_order'])){
			$value = $options['events_order'];
		}
	?>
		<input type="radio"  name="event_option[events_order]" value="bydate" <?php checked( 'bydate' == $value ); ?> />by date<br/>
        <input type="radio"  name="event_option[events_order]" value="alphabetical"<?php checked( 'alphabetical' == $value ); ?> />alphabetical
	<?php

    }
}

if( is_admin() )
    $nile_events_settings_page = new NileEventsSettingsPage();