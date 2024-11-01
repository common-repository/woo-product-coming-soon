<?php
/*

* Plugin Name: Woo Product Coming Soon

* Description: This plugin will add a new functionality to display when the product will be back in stock if it is out of stock.

* Version: 1.0.0

* Author: Webman Technologies

* Requires at least: 4.4

* Tested up to: 4.9

* WC requires at least: 2.5

* WC tested up to: 3.3.5

* Text Domain: WMAMC-woo-coming-soon-product

* License: GPLv2 or later



Woo Product Coming Soon is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

Woo Product Coming Soon is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>


 Copyright (C) 2018  Orem Technologies


 */
 
 defined( 'ABSPATH' ) or exit;

/*	woocommerce check 	*/
$active_plugins = get_option( 'active_plugins', array() );
if( !in_array( 'woocommerce/woocommerce.php',$active_plugins ) )
{
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
	deactivate_plugins('woo-product-coming-soon/woo-product-coming-soon.php');
	if( isset( $_GET['activate'] ))
      unset( $_GET['activate'] );
}

class WMAMC_woo_product_coming_soon
{
	protected static $instance;
	protected $adminpage;
	
	public function __construct()
	{
		register_activation_hook(__FILE__, array( $this, 'WMAMC_woo_product_coming_soon_plugin_activate') );
		
		register_deactivation_hook(__FILE__, array( $this, 'WMAMC_woo_product_coming_soon_plugin_deactivate') );
		
		add_action( 'admin_init', array( $this, 'WMAMC_woo_product_coming_soon_version_check' ) );

		add_action( 'woocommerce_process_product_meta', array($this,'WMAMC_woo_product_coming_soon_product_custom_fields_save') );
		
		add_filter('woocommerce_get_availability', array($this,'WMAMC_woo_product_coming_soon_availability_check'));
		
		add_action('woocommerce_product_options_stock_status', array($this,'WMAMC_woo_product_coming_soon_product_custom_fields'));
		
	
	}
	
	public function WMAMC_woo_product_coming_soon_plugin_activate()
	{
		
	}
	
	
	
	public function WMAMC_woo_product_coming_soon_version_check()
	{
		global $woocommerce; 
					
		if ( version_compare( $woocommerce->version, '2.5', '<=' ) ) {
			
			add_action( 'admin_notices', array($this,'WMAMC_woo_product_coming_soon_admin_notice_msg') );
			
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
			
			deactivate_plugins( plugin_basename( __FILE__ ) );
			
			if( isset( $_GET['activate'] ))
			unset( $_GET['activate'] );
		
			return false;
		}
	}
	
	
 
	
	
	public function WMAMC_woo_product_coming_soon_product_custom_fields()
	{
		global $woocommerce, $post;

		$user_permission = current_user_can( 'update_core' );
		if ($user_permission == true)
		{
			
				echo '<div class="stock_fields">'; 
				
				woocommerce_wp_checkbox(array(
										'id' => 'WMAMC__set_coming_soon',
										'label' => __('Set for Coming Soon?', 'WMAMC-woo-coming-soon-product'),
										'desc_tip' => true,
										'description' => __('Make sure that you have set the stock to "Out of Stock".', 'WMAMC-woo-coming-soon-product'),
										'wrapper_class' => 'stock_fields')
										);
										
				woocommerce_wp_text_input(array(
										'id' => 'WMAMC__coming_soon_label',
										'label' => __('Coming Soon Label', 'WMAMC-woo-coming-soon-product'),
										'placeholder' => __('Coming Soon', 'WMAMC-woo-coming-soon-product'),
										'desc_tip' => true, 
										'type' =>'text',
										'description' => __('Enter the label you want to show if coming soon is set. Default: Coming Soon', 'WMAMC-woo-coming-soon-product'),
										'wrapper_class' => 'stock_fields ')
										); 
										
				 woocommerce_wp_text_input(array(
										 'id' => 'WMAMC__coming_soon_date',
										 'label' => __('Coming Soon Date', 'WMAMC-woo-coming-soon-product'),
										 'placeholder' => __('yyyy-mm-dd', 'WMAMC-woo-coming-soon-product'),
										 'desc_tip' => 'true',
										 'description' => __('Enter product coming back date.', 'WMAMC-woo-coming-soon-product'), 
										 'type' => 'date',
										 'wrapper_class' => 'stock_fields '
										 ));
										
										
			
				
				echo '</div>';
			
		}
	}
	
	


	
	public function WMAMC_woo_product_coming_soon_product_custom_fields_save($post_id)
	{
		$user_permission = current_user_can( 'update_core' );
		if ($user_permission == true)
		{
			// WMAMC__set_coming_soon Field
			
			$WMAMC__set_coming_soon = $_POST['WMAMC__set_coming_soon'];
			
			if (!empty($WMAMC__set_coming_soon))
			{
				update_post_meta($post_id, 'WMAMC__set_coming_soon', esc_attr($WMAMC__set_coming_soon));
			}
			else
			{
				update_post_meta($post_id, 'WMAMC__set_coming_soon','no');
			}
		
			// WMAMC__coming_soon_label Field
		
			$WMAMC__coming_soon_label = $_POST['WMAMC__coming_soon_label'];
			
			if (!empty($WMAMC__coming_soon_label))
			{
				update_post_meta($post_id, 'WMAMC__coming_soon_label', esc_attr($WMAMC__coming_soon_label));
			}
			else
			{
				update_post_meta($post_id, 'WMAMC__coming_soon_label', '');
			} 
		
			// WMAMC__coming_soon_date Field
			
			$WMAMC__coming_soon_date = $_POST['WMAMC__coming_soon_date'];
			
			if (!empty($WMAMC__coming_soon_date))
			{
				update_post_meta($post_id, 'WMAMC__coming_soon_date', esc_attr($WMAMC__coming_soon_date));
			}
			else
			{
				update_post_meta($post_id, 'WMAMC__coming_soon_date', '');
			}
		
	
		}

	}
	
	
	

	
	public function WMAMC_woo_product_coming_soon_availability_check($availability)
	{
		global $post, $woocommerce;
		$_product = wc_get_product($post->ID);
		$manage_stock = get_post_meta($post->ID,'_manage_stock',true);
		
		if( !$_product->is_in_stock() )
			{
				if($manage_stock == 'yes')
					{ 	
						$coming_soon_enabled = get_post_meta($post->ID,'WMAMC__set_coming_soon',true);
						
						$WMAMC__coming_soon_date = date_create(get_post_meta($post->ID,'WMAMC__coming_soon_date',true));
						$WMAMC__coming_soon_date_act = get_post_meta($post->ID,'WMAMC__coming_soon_date',true);
						
						$comin_soon_date = date_format($WMAMC__coming_soon_date,'d M Y');
						
						$WMAMC__coming_soon_label = get_post_meta($post->ID,'WMAMC__coming_soon_label',true);
						
						if($WMAMC__coming_soon_label)
						{
							$note_text = "<b>NOTE: </b>".$WMAMC__coming_soon_label;
						} 
						
						if($coming_soon_enabled == 'yes' && !empty($WMAMC__coming_soon_date))
						{
							$current_date = date('Y-m-d');
							
							if($current_date < $WMAMC__coming_soon_date_act)
							{
								$availability['availability'] = str_ireplace('Out of stock','Out Of Stock .This product will be available on '.$comin_soon_date.'.'.$note_text.'', $availability['availability']);
								return $availability;
							}
							else
							{
								$availability['availability'] = str_ireplace('Out of stock','Out Of Stock .', $availability['availability']);
								return $availability;
							}
							
						}
						
						if($coming_soon_enabled == 'yes' && empty($WMAMC__coming_soon_date))
						{
							$availability['availability'] = str_ireplace('Out of stock', 'Out Of Stock', $availability['availability']);
							return $availability;
						}
						
						if($coming_soon_enabled == 'no')
						{
							$availability['availability'] = str_ireplace('Out of stock', 'Out Of Stock', $availability['availability']);
							return $availability;
						}
				
					}
					else
					{
						
						$availability['availability'] = str_ireplace('Out of stock', 'Out Of Stock', $availability['availability']);
						return $availability;
						
					} 
			
			}
			else
			{
				$availability['availability'] = __('In Stock', 'WMAMC-woo-coming-soon-product');
				return $availability;
			}
			
	}
	
	

	
	public function WMAMC_woo_product_coming_soon_plugin_deactivate()
	{
		
	}
	
	
	
	public function WMAMC_woo_product_coming_soon_admin_notice_msg()
	{
		global $woocommerce;
?>
		<div class="notice notice-error is-dismissible">
				<p><?php   _e("<b>Woo product coming soon is inactive</b>. Woo product coming soon requires a minimum of WooCommerce v2.5 ","WMAMC-woo-coming-soon-product"); ?></p>
		</div>
<?php
	}
	
	public function WMAMC_woo_product_coming_soon_instance()
	{
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
}


function WMAMC_woo_product_coming_soon() 
{
	return WMAMC_woo_product_coming_soon::WMAMC_woo_product_coming_soon_instance();
}
WMAMC_woo_product_coming_soon();
 

?>