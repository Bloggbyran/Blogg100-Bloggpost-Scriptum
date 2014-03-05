<?php
/*
Plugin Name: Blogg100 - Bloggpost scriptum
Description: En plugin som lägger till en liten textsnutt om <a href='http://bisonblog.se/2014/02/blogg100-tredje-gangen-gillt'/>Blogg100</a> i slutet av varje inlägg <strong>taggat med Blogg100</strong>.
Plugin URI: http://bloggbyran.se/bloggpost-scriptum-en-plugin-blogg100-skribenter/
Author: Bloggbyrån
Author URI: http://bloggbyran.se
Version: 1.01
License: GPL2
*/

function set_blogg100_default_options(){
    $blogg100_options = array(  'ps_text'       => 'Detta är inlägg {{antal dagar med inlägg}}, dag {{antal dagar in i blogg100}} i intitiativet #blogg100 som går ut på att skriva ett blogginlägg om dagen med start den 1 mars 2014. Detta är inlägg {{antal poster totalt}} av 100.',
                                'count_option'  => 'daily',
                                'logo_bottom'   => '1'
    );                      
    add_option('blogg100_options', $blogg100_options);
}
register_activation_hook( __FILE__, 'set_blogg100_default_options' );

// Create an admin screen 
class Blogg100_settings
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
        // This page will be under "Settings"
        add_options_page(
            'Blogg 100 Bloggpost scriptum', 
            'Blogg 100 Bloggpost scriptum', 
            'manage_options', 
            'blogg100-post-scriptum-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'blogg100_options' );
        ?>
        <div class="wrap">
            <img style="float:left; margin-right: 10px; width: 50px;" src="<?php echo plugin_dir_url( __FILE__ ); ?>/Blogg100-logo-82x70.png">          
            <h2>Blogg 100 - Post Scriptum</h2> 
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'my_option_group' );   
                do_settings_sections( 'blogg100-post-scriptum-admin' );
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
            'my_option_group', // Option group
            'blogg100_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Inställningar för text i slutet av posten.', // Title
            array( $this, 'print_section_info' ), // Callback
            'blogg100-post-scriptum-admin' // Page
        );  

        add_settings_field(
            'id_number', // ID
            'Hur räknar du?', // Title 
            array( $this, 'counting' ), // Callback
            'blogg100-post-scriptum-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'ps_text', 
            'Post Scriptum', 
            array( $this, 'ps_callback' ), 
            'blogg100-post-scriptum-admin', 
            'setting_section_id'
        );

        add_settings_field(
            'logo_bottom', 
            'Blogg100-logon', 
            array( $this, 'logo_bottom_callback' ), 
            'blogg100-post-scriptum-admin', 
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

        if( isset( $input['count_option'] ) )
            $new_input['count_option'] = $input['count_option'];

        if( isset( $input['ps_text'] ) )
            $new_input['ps_text'] = $input['ps_text'];

        if( isset( $input['logo_bottom'] ) )
            $new_input['logo_bottom'] = $input['logo_bottom'];

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Anpassa texten som visas under varje inlägg som du taggat <em>blogg100</em> från och med 1 mars 2014. Lite beroende på hur du räknar, så kan vi föreslå några formuleringar:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function counting()
    {
        isset( $this->options['count_option'] ) ? $val = $this->options['count_option'] : $val = 'daily';
       
        $text_suggestion1 = 'Detta är inlägg {{antal dagar med inlägg}} av 100 i intitiativet <a rel=\'nofollow\' href=\'http://bisonblog.se/2014/02/blogg100-tredje-gangen-gillt/\'>#Blogg100</a> som går ut på att skriva ett blogginlägg om dagen med start den 1 mars 2014.';
        print '<input data-text_suggestion="' . $text_suggestion1 . '" type="radio" id="count_type_day" name="blogg100_options[count_option]" value="daily" ' . checked( 'daily', $val, false) . '/> <label for="count_type_day">Minst ett inlägg per dag i 100 dagar.</label><br>';
       
        $text_suggestion2 = 'Detta är inlägg {{antal poster totalt}}, i intitiativet <a rel=\'nofollow\' href=\'http://bisonblog.se/2014/02/blogg100-tredje-gangen-gillt/\'>#Blogg100</a> som går ut på att skriva ett blogginlägg i snitt per dag i 100 dagar från den 1 mars 2014.';
        print '<input data-text_suggestion="' . $text_suggestion2 . '" type="radio" id="count_type_total" name="blogg100_options[count_option]" value="total" ' . checked( 'total', $val, false) . '/> <label for="count_type_total">100 inlägg totalt under 100 dagar.</label><br>';
       
        $text_suggestion3 = 'Detta här inlägget är postat dag {{antal dagar in i blogg100}} i intitiativet <a rel=\'nofollow\' href=\'http://bisonblog.se/2014/02/blogg100-tredje-gangen-gillt/\'>#Blogg100</a> som går ut på att försöka skriva ett blogginlägg per dag i 100 dagar.';
        print '<input data-text_suggestion="' . $text_suggestion3 . '" type="radio" id="count_type_happygolucky" name="blogg100_options[count_option]" value="happygolucky" ' . checked( 'happygolucky', $val, false) . '/> <label for="count_type_happygolucky">Räknar? Är glad om jag skriver på rätt dag.</label><br>';

        print '<input data-text_suggestion="" type="radio" id="count_type_custom" name="blogg100_options[count_option]" value="custom" ' . checked( 'custom', $val, false) . '/> <label for="count_type_custom">Egen formulering.</label>';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function ps_callback()
    {   
        $helptext = 'Använd variablerna {{antal dagar med inlägg}}, {{antal poster totalt}} och {{antal dagar in i blogg100}} för att skapa ditt personliga meddelande.';
        printf(
            '<textarea id="ps_text" style="width: 500px; height: 100px;" name="blogg100_options[ps_text]">%s</textarea>' .
            '<p class="description" style="width: 500px;">'. $helptext .'<p>',
                isset( $this->options['ps_text'] ) ? esc_attr( $this->options['ps_text']) : 'Detta är inlägg {{antal dagar med inlägg}}, dag {{antal dagar in i blogg100}} i intitiativet #blogg100 som går ut på att skriva ett blogginlägg om dagen med start den 1 mars 2014. Detta är inlägg {{antal poster totalt}} av 100.'
        );
    }

    public function logo_bottom_callback()
    {  
        isset( $this->options['logo_bottom'] ) ? $val = $this->options['logo_bottom'] : $val = 0;
        print '<input type="checkbox" id="logo_bottom"   name="blogg100_options[logo_bottom]" value="1" ' . checked( 1, $val, false) . '/> <label for="logo_bottom">Visa Blogg100-logon ihop med ditt Post Scriptum.</label><br>';
    }
}

if( is_admin() ){
    $my_settings_page = new Blogg100_settings();
}


// Create a filter that outputs the text the post content
function add_post_scriptum($content)
{
    global $post;
    $terms = 'blogg100';
    
    $start_year     = 2014;
    $start_month    = 3;
    $start_day      = 1; 

    if(has_tag(  $terms , $post->ID) && is_single())
    {   
        $blogg100_options = get_option('blogg100_options');

        $args = array(
            'date_query' => array(
                'after'     => array(
                    'year'  => $start_year,
                    'month' => $start_month,
                    'day'   => $start_day,
                    'hour'  => 0,
                    'minute'=> 1
                ),
                'before'    => $post->post_date,
                'inclusive' => true,
            ),
            'tag' => $terms, 
            'posts_per_page'=> -1
        );
        $query = new WP_Query( $args );

        foreach ($query->posts as $key => $tag_post) 
        {
            $date = new DateTime($tag_post->post_date);
            $date_daily = $date->format('Y-m-d');
            $date_counter[$date_daily] = $date_daily;
        }
        
        $post_number_total  = count($query->posts);
        $post_number_daily  = count($date_counter);
        
        $unixtime_postdate  = strtotime($post->post_date);
        $unixtime_startdate = strtotime($start_year . '-' . $start_month . '-' .  $start_day);
        $day_number         = ceil(($unixtime_postdate - $unixtime_startdate)/(60*60*24));
        
        $ps_text            = $blogg100_options['ps_text'];
        $ps_text            = preg_replace('/{{antal dagar med inlägg}}/'   , $post_number_daily, $ps_text);
        $ps_text            = preg_replace('/{{antal poster totalt}}/'      , $post_number_total, $ps_text);
        $ps_text            = preg_replace('/{{antal dagar in i blogg100}}/', $day_number, $ps_text);

        $post_scriptum = '<p class="blogg100-ps">' . $ps_text .'</p>';
        if($blogg100_options['logo_bottom'] === '1'){
            $img = '<img style="float:left; margin-right: 10px;" src="' . plugin_dir_url( __FILE__ ) . '/Blogg100-logo-82x70.png">';
            $post_scriptum = $img . $post_scriptum;
        }
        $content = $content . '<div class="blogg100-ps-wrapper" style="clear: both; overflow: hidden; margin: 10px 0px;">' .  $post_scriptum . '</div>';
    }

    return $content;
}
add_filter('the_content', 'add_post_scriptum');

function enqueue_admin_UI_script($hook) {
    wp_enqueue_script( 'blogg100-admin', plugin_dir_url( __FILE__ ) . '/blogg100-admin.js' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_admin_UI_script' );
