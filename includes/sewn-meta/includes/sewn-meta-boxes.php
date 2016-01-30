<?php

/**
 * The meta box class.
 *
 * Add a simple action for creating metaboxes with custom fields easily.
 *
 * @since      1.0.0
 * @package    Sewn_Meta
 * @subpackage Sewn_Meta/includes
 * @author     Jake Snyder <jake@jcow.com>
 */

if (! class_exists('Sewn_Meta_Box') ) :

class Sewn_Meta_Box
{
	/**
	 * The unique prefix for Sewn In.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $prefix         The string used to uniquely prefix for Sewn In.
	 */
	protected $prefix;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version        The current version of the plugin.
	 */
	protected $version;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $settings       The array used for settings.
	 */
	protected $settings;

	/**
	 * All of the meta boxes
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $settings       The array used for settings.
	 */
	protected $boxes;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $prefix, $plugin_name, $version )
	{
		$this->prefix      = $prefix;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->settings    = array(
			'post_types' => array('post','page'),
			'nonce'      => array(
				'action'     => $this->plugin_name,
				'name'       => "{$this->plugin_name}_nonce",
			),
		);
		$this->boxes = array();

		add_action( "{$this->prefix}/meta/create_box",            array($this, 'create_box') );

		add_action( 'add_meta_boxes',                             array($this, 'add_boxes') );
		add_action( "{$this->prefix}/meta/register_field_group",  array($this, 'register_field_group') );

		add_action( 'save_post',                                  array($this, 'save_boxes') );
	}

	/**
	 * Add the meta box.
	 *
	 * @since	1.1.0
	 * @return	void
	 */
	public function register_field_group( $field_group )
	{
		if (! $field_group['id'] ) { return; }

		$this->boxes[$field_group['id']] = $field_group;

		// add fields
		foreach ( $field_group['fields'] as &$field )
		{
			if ( empty($field['parent']) ) {
				$field['parent'] = $field_group['id'];
			}

			// add field
			do_action( "{$this->prefix}/meta/add_field", $field, $field_group );
		}
	}

	/**
	 * Add the meta box.
	 *
	 * @since	1.1.0
	 * @return	void
	 */
	public function add_boxes()
	{
		if ( is_array($this->boxes) ) {
			foreach ( $this->boxes as $box )
			{
				do_action( "{$this->prefix}/meta/create_box", $box );
			}
		}
	}

	/**
	 * Add the meta box.
	 *
	 * @since	1.1.0
	 * @return	void
	 */
	public function create_box( $args )
	{
		// defaults
		$args = wp_parse_args( $args, array(
			'id'          => '',
			'title'       => '',
			'context'     => 'normal',
			'priority'    => 'default',
			'post_types'  => $this->settings['post_types'],
			'fields'      => array(),
			'before'      => '',
			'after'       => '',
		) );
		if ( ! $args['id'] ) { $args['id'] = uniqid(); }

		// Setup callbacks
		$callback      = array($this, 'inside');
		$callback_args = $args;

		extract($args);

		// Create metaboxes for post_types
		foreach ( $post_types as $post_type ) {
			add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
		}
	}

	/**
	 * Build the fields and content for the metabox.
	 *
	 * @since	1.1.0
	 * @return	void
	 */
	public function inside( $post, $args )
	{
		extract( $args ); // all variables from the add_meta_box function

		// Content before the fields
		if ( $args['before'] ) {
			echo esc_html($args['before']);
		}

		// Add the values and render the fields
		foreach ( $args['fields'] as $field ) {
			$field['value'] = get_post_meta( $post->ID, $field['name'], true );
			do_action( "{$this->prefix}/meta/create_field", $field );
		}

		// Javascript to add our Sewn classes
?>
		<?php // Nonce
		#wp_nonce_field( $this->settings['nonce']['action'] . "_{$id}", $this->settings['nonce']['name'] . "_{$id}" ); ?>
		<div class="<?php echo $this->prefix; ?>-hidden">
			<input type="hidden" name="<?php echo $this->prefix; ?>_nonce[<?php echo $id; ?>]" value="<?php echo wp_create_nonce( $this->settings['nonce']['action'] . "_{$id}" ); ?>" />
			<script type="text/javascript">
			(function($) {
				$('#<?php echo $id; ?>').addClass('<?php echo $this->prefix; ?>-postbox').removeClass('hide-if-js');
				$('#<?php echo $id; ?> > .inside').addClass('<?php echo $this->prefix; ?>-fields <?php echo $this->prefix; ?>-cf');
			})(jQuery);	
			</script>
		</div>
<?php
		// Content after the fields
		if ( $args['after'] ) {
			echo esc_html($args['after']);
		}
	}

	/**
	 * Save the meta box.
	 *
	 * @since	1.1.0
	 * @return	void
	 */
	public function save_boxes( $post_id )
	{
		// Checks save status
		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		if ( $is_autosave || $is_revision ) { return; }

		if ( ! empty($_POST["{$this->prefix}_nonce"]) )
		{
			foreach ( $_POST["{$this->prefix}_nonce"] as $group_id => $nonce_value )
			{
				// Exits script if nonce is not valid
				$is_valid_nonce = ( wp_verify_nonce($nonce_value, $this->settings['nonce']['action'] . "_{$group_id}") ) ? 'true' : 'false';
				if ( ! $is_valid_nonce ) { return; }

				// Make sure the box is registered
				if ( ! empty($this->boxes[$group_id]) )
				{
					$field_group = $this->boxes[$group_id];
					foreach ( $field_group['fields'] as $field )
					{
						$name  = $field['name'];
						$value = ( ! empty($_POST[$this->prefix][$name]) ) ? $_POST[$this->prefix][$name] : false;

						do_action( "{$this->prefix}/meta/update_field", $post_id, $name, $field_group, $value );
					}
				}
			}
		}
	}

}

endif;