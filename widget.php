<?php
/*** COMIENZAN LOS WIDGETS  ******/
class WPeMyAds extends WP_Widget {
	/**
	* Declares the WPeMyAds class.
	*
	*/
	function WPeMyAds(){
		$widget_ops = array('classname' => 'widget_WPeMyAds', 'description' => __( "Muestra los Adserve por zona en cada widget.") );
		$control_ops = array('width' => 270, 'height' => 300);
		$this->WP_Widget('WPeMyAds', __('My Ads') , $widget_ops, $control_ops);
//		$this->WP_Widget('WPeMyAds', __('My Ads'), $widget_ops, $control_ops);
	}
	
	/**
	* Displays the Widget
	*
	*/
	function widget($args, $instance){
		extract($args);
		// Control logico de $WPeMyAdsPage
		$WPeMyAdsPage = (empty($instance['WPeMyAdsPage'])) ? 'true' : stripslashes($instance['WPeMyAdsPage']);
		$WPeMyAdsPage = (stristr( $WPeMyAdsPage,"return")) ? $WPeMyAdsPage : "return (" . $WPeMyAdsPage . ");";
		$URL_logic=(eval($WPeMyAdsPage));
		if ( $URL_logic ) {
			$show_title = htmlspecialchars($instance['show_title']);
			$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
			# Before the widget
			echo $before_widget;
			
			# The title
			if ( $title && $show_title )
				echo $before_title . $title . $after_title;
			
			# Make the widget content
			AdServe(trim($instance['zone']));
			AdServe(trim($instance['zone2']));
				
			# After the widget
			echo $after_widget;
		}
	}
	
	/**
	* Saves the widgets settings.
	*
	*/
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		$instance['show_title'] = intval($new_instance['show_title']);
		$instance['zone'] = strip_tags(stripslashes($new_instance['zone']));
		$instance['zone2'] = strip_tags(stripslashes($new_instance['zone2']));
		$instance['WPeMyAdsPage'] = strip_tags(stripslashes($new_instance['WPeMyAdsPage']));
		return $instance;
	}
	
	/**
	* Creates the edit form for the widget.
	*
	*/
	function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title'=>'My Ads', 'show_title'=>'1', 'zone'=>'','zone2'=>'', 'WPeMyAdsPage'=>'' ) );
		
		$title = htmlspecialchars($instance['title']);
		$show_title = htmlspecialchars($instance['show_title']);
		$zone = htmlspecialchars($instance['zone']);
		$zone2 = htmlspecialchars($instance['zone2']);
		$WPeMyAdsPage = htmlspecialchars($instance['WPeMyAdsPage']);
		//$valid = ($instance['zone_status']['valid'] == 'YES'); 

		$xzones=get_etruel_AdServe_zones();
		$doSelect= "<select name='" . $this->get_field_name('zone') . "' id='" . $this->get_field_id('zone') . "'>\n";
		$doSelect.="\t\t\t<option value=''> </option>\n";
		$doSelect2= "<select name='" . $this->get_field_name('zone2') . "' id='" . $this->get_field_id('zone2') . "'>\n";
		$doSelect2.="\t\t\t<option value=''> </option>\n";
		foreach ($xzones as $rk) {
			$doSelect.="\t\t\t<option value='{$rk->keywords}' ";
			$doSelect.= ($rk->keywords == $zone) ? " selected":"";
			$doSelect.=">".$rk->keywords."</option>\n";
			$doSelect2.="\t\t\t<option value='{$rk->keywords}' ";
			$doSelect2.= ($rk->keywords == $zone2) ? " selected":"";
			$doSelect2.=">".$rk->keywords."</option>\n";
		}
		$doSelect.="\t\t\t</select>";
		$doSelect2.="\t\t\t</select>";

		
		# Output the options
		echo '<p><label for="show_title" style="float:Right;"><input id="' . $this->get_field_id('show_title') . '" name="' . $this->get_field_name('show_title') . '" type="checkbox" value="' . $show_title . '"'.((!$show_title)?'':'checked').'/> ' . __('Show Title') . '</label><label for="' . $this->get_field_name('title') . '">' . __('Title:') . '<br /><input style="width: 100%;" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></label></p>';

		# zone
		echo '<p><label for="' . $this->get_field_name('zone') . '">' . __('1ra Zona:') . '  ';
		echo $doSelect;
		echo '</label></p>';
		
		# zone2
		echo '<p><label for="' . $this->get_field_name('zone2') . '">' . __('2da Zona:');
		echo $doSelect2;
		echo '</label></p>';
		
		# WPeMyAdsPage = Condicion para mostrar el widget Ej: is_page('algo') o is_front_page()
		echo '<p style="margin-bottom: 0;"><label for="' . $this->get_field_name('WPeMyAdsPage') . '">' . __('Logic URL:') . ' <input style="width: 200px;" id="' . $this->get_field_id('WPeMyAdsPage') . '" name="' . $this->get_field_name('WPeMyAdsPage') . '" type="text" value="' . $WPeMyAdsPage . '" /></label><br /><small><a href="Javascript:void(0);" onclick="jQuery(this).parent().parent().next().toggle();">' . __('Click here to see/hide examples') . '</a></small></p><div class="wpeexa" style="display:none;"><table style="width:100%;display:block;margin-left:20px;font: smaller \'Courier New\', Courier, mono;"> <tr><td>is_front_page()</td><td>is_page(\'slug\')</td></tr><tr><td>is_single(\'slug\')</td><td>is_author(\'slug\')</td></tr><tr><td>is_tag(\'slug\')</td><td>is_category(\'slug\')</td></tr></table></div>';
		echo '<p></p>';
	}

}// END class
	
?>
