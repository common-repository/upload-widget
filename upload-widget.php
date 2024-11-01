<?php
/**
 * Plugin Name: Upload Widget
 * Plugin URI: http://wordpress.org/extend/plugins/upload-widget/
 * Description: The simple way to upload files.
 * Version: 1.5
 * Author: Monpelaud
 * Author URI: 
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'load_upload_widget' );

/**
 * Register our widget.
 * 'Upload_Widget' is the widget class used below.
 *
 * @since 0.1
 */

	/**
	 * Set default max size allowed for upload files
	 */

function load_upload_widget() {
	load_plugin_textdomain('upload-widget', PLUGINDIR .'/'.dirname(plugin_basename (__FILE__)).'/languages');
	register_widget( 'Upload_Widget' );
}

/**
 * Upload_Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Upload_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */

	function upload_widget_role() {
		global $wp_roles;
	 
		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters('editable_roles', $all_roles);
		return $editable_roles;
	}

	function upload_widget_clean($label) {
		/**
		 * Regular expressions to the change some characters.
		 */

		$search = array ('@[éèêëÊË]@i','@[àâäÂÄ]@i','@[îïÎÏ]@i','@[ûùüÛÜ]@i','@[ôöÔÖ]@i',
		'@[ç]@i','@[^a-zA-Z0-9._]@');	 
		$replace = array ('e','a','i','u','o','c','-');
		$label =  preg_replace($search, $replace, $label);
		$label = strtolower($label); // Convert in lower case
		return $label;
	}

	 function upload_widget_default_size() {
		$upload_default_size = '10'; //Set default upload_max_size
		return $upload_default_size;
	}

	function upload_widget_full_path( $instance ) {
		if ($instance['upload_path']) {
			$full_upload_path = WP_CONTENT_DIR.'/'.$instance['upload_path'].'/'; }
		else {
			$full_upload_path = WP_CONTENT_DIR.'/';
		}
		return $full_upload_path;
	}

	function upload_widget_wildcard_to_preg($pattern) {
		return '/^' . str_replace(array('\*', '\?', '\[', '\]'), array('.*', '.', '[', ']+'), preg_quote($pattern)) . '$/is';
	}

	function upload_widget_wildcard_match($pattern, $str) {
		$pattern = $this->upload_widget_wildcard_to_preg($pattern);
		return preg_match($pattern, $str);
	}

	function Upload_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'upload_widget', 'description' => __('The simple way to upload files', 'upload-widget') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'upload-widget' ); //Attribute name of wp_options.option_name

		/* Create the widget. */
		$this->WP_Widget( 'upload-widget', __('Upload Widget', 'upload-widget'), $widget_ops, $control_ops ); //Display name in wp-widgets admin page
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$upload_title = apply_filters('widget_title', $instance['upload_title'] ); //Option: upload_title
		$upload_wp_role = $instance['upload_wp_role']; //Option: upload_wp_role
		$upload_path = $instance['upload_path']; //Option: upload_path
		$upload_patterns = $instance['upload_patterns']; //Option: upload_patterns
		$allowed_patterns = explode(",",$upload_patterns);
		$upload_max_size = $instance['upload_max_size']; //Option: upload_max_size

		$widget_number = @$widget_id;
		$uploadedfile = 'uploadedfile_'.$widget_number;
		$upfile = 'upfile_'.@$widget_id;
		$upload = 'upload_'.@$widget_id;
		$user = wp_get_current_user();

		if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
			/* Go through the array of the roles of the current user */
			foreach ( $user->roles as $widget_upload_role )
			/* If one role of the current user matches to the role allowed to upload */
			if ( $widget_upload_role == $upload_wp_role || $widget_upload_role == 'administrator' ) {
				/*  We affect this role to current user */
				$widget_upload_user_role = $widget_upload_role;
				break;
			}
			else {
				/* We affect the 'visitor' role to current user */
				$widget_upload_user_role = 'visitor';
			}
		}
		else {
			$widget_upload_user_role = 'visitor';
		}		
		
		if ( $widget_upload_user_role != $upload_wp_role && $widget_upload_user_role != 'administrator' ) {
			return;
		}		

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $upload_title ) {
			echo $before_title . $upload_title . $after_title;
		}

		echo '<div align="center" >'.__('Allowed Files:', 'upload-widget').'</div><div>';
		$count_allowed_patterns = count($allowed_patterns);
		$i=0;
		echo '<p align="center" >';
		foreach ($allowed_patterns as $one_allowed_pattern) {
			$i++;

			if ( $one_allowed_pattern == 'NONE' ) {
				echo  '<font color="red"><strong>'.$one_allowed_pattern.'</strong></font>';
			}
			else {
				echo  '<font <strong>'.$one_allowed_pattern.'</strong></font>';
			}

			if ( $i < $count_allowed_patterns ) {
				echo ' - ';
			}
		}
		echo '</p>';
		?>
		</div><br />
		<ul>  
			<form name="form_upload" method="post" enctype="multipart/form-data">		
				<input type="file" name="<?php echo $uploadedfile ; ?>" id="<?php echo $upfile ; ?>" size="10" tabindex="1" />
				<input align="center" type="submit" name="<?php echo $upload; ?>" value="<?php echo _e('Upload File', 'upload-widget'); ?>" />
			</form>
		</ul>

		<?php
		$upload_path_ok = false;
		$allowed_file_ok = false;
		$size_file_ok = false;
		
		/* Get uploaded file size in Mbytes */
		$upload_file_size = filesize($_FILES[$uploadedfile]['tmp_name']) / 1024 /1024;

		if ( $upload_file_size > 0 ) {
		
			/* Check if upload path exist */
			if ( is_dir( $this->upload_widget_full_path($instance) ) ) {		
				$upload_path_ok = true;
			}

			/* File name control */
			foreach ($allowed_patterns as $allowed_pattern) {
				if ( $this->upload_widget_wildcard_match( $allowed_pattern, $_FILES[$uploadedfile]['name']) ) {
					$allowed_file_ok = true;
					break ;
				}
			}

			/* File size control */
			if ( $upload_file_size <= $upload_max_size ) {
				$size_file_ok = true;
			}
			
			if ( !$upload_path_ok or !$allowed_file_ok or !$size_file_ok ) {
				echo  '<font color="red"><strong>'.__("Upload failed !", "upload-widget").'</strong></font>';
				
				if ( !$upload_path_ok ) {
					echo  '<font color="red"><strong>'.'<br />'.__("&#9658; Targer folder &#171; ", "upload-widget").$this->upload_widget_full_path($instance).__(" &#187; doesn't exist !", "upload-widget").'</strong></font>';
				}
				
				if ( !$allowed_file_ok ) {
					echo  '<font color="red"><strong>'.'<br />'.__("&#9658; File not allowed", "upload-widget").'</strong></font>';
				}

				if ( !$size_file_ok ) {
					echo  '<font color="red"><strong>'.'<br />'.__("&#9658; File size > ", "upload-widget").$upload_max_size.__(" Mb", "upload-widget").'</strong></font>';
				}
			}
		}	

		if ( $upload_path_ok and $allowed_file_ok and $size_file_ok ) {
		
			if ( is_uploaded_file($_FILES[$uploadedfile]['tmp_name']) ) {
			
				if ( $allowed_file_ok and $size_file_ok ) {
					$source_path = $_FILES[$uploadedfile]['tmp_name'];
					$target_path = $this->upload_widget_full_path($instance).$this->upload_widget_clean( $_FILES[$uploadedfile]['name'] );
					if ($source_path) {
						copy($source_path,$target_path);
					}
					echo '<font color="green"><strong>'.__("File &#171; ", "upload-widget").$this->upload_widget_clean( $_FILES[$uploadedfile]['name'] ).__(" &#187; uploaded with success...", "upload-widget").'</strong></font>';
					/* Delete temporary file (in "/xampp/tmp/") */
					unlink($source_path);			
				}
			}
			else {
				echo  '<font color="red"><strong>'.__("Upload failed !<br />Unknown error.").'</strong></font>';
			}
		}

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['upload_title'] = strip_tags( $new_instance['upload_title'] ); //Options: upload_title
		$instance['upload_wp_role'] = strip_tags( $new_instance['upload_wp_role'] ); //Options: upload_wp_role
		$instance['upload_path'] = trim( strip_tags( $new_instance['upload_path'] ), "/\\"); //Options: upload_path

		$instance['upload_patterns'] = strtolower( strip_tags( str_replace(CHR(32),"",$new_instance['upload_patterns']) ) ); //Options: upload_patterns

		if ( $instance['upload_patterns'] == "") {
			$instance['upload_patterns'] = 'NONE';
		}

		$instance['upload_max_size'] = strip_tags( $new_instance['upload_max_size'] ); //Options: upload_max_size
		if ( !is_numeric($instance['upload_max_size']) ) {
			$instance['upload_max_size'] = $this->upload_widget_default_size();
		}

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
		
		/* Set up some default widget settings. */
		$defaults = array( 'upload_title' => __('Upload a file', 'upload-widget'), 'upload_wp_role' => 'administrator', 'upload_path' => '', 'upload_patterns' => '*.pdf', 'upload_max_size' => $this->upload_widget_default_size() );
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>

		<!-- Input Title -->
		<p>
			<label for="<?php echo $this->get_field_id( 'upload_title' ); ?>"><?php _e('Title:', 'upload-widget'); ?></label>
			<input id="<?php echo $this->get_field_id( 'upload_title' ); ?>" name="<?php echo $this->get_field_name( 'upload_title' ); ?>" value="<?php echo $instance['upload_title']; ?>" style="width:100%;" />
		</p>

		<!-- Input Authorized role -->
		<p>
			<label for="<?php echo $this->get_field_id( 'upload_wp_role' ); ?>"><?php _e('Authorized WP Role:', 'upload-widget'); ?></label>		
	    	<select id="<?php echo $this->get_field_id( 'upload_wp_role' ); ?>" name="<?php echo $this->get_field_name( 'upload_wp_role' ); ?>" >
				<option value='visitor' <?php if ($instance['upload_wp_role'] == 'visitor') echo 'selected="selected"'; ?> ><?php _e('Visitor', 'upload-widget')?></option>
				<?php
				$role_name = $this->upload_widget_role();
				foreach( $role_name as $role => $details ) {
					$role_value = $role;
					$name = translate_user_role( $details['name']);
					if ( $role_value == $instance['upload_wp_role'] ) {
						echo '<option value="'.$role_value.'" selected="selected" >'.$name.'</option>';
					}
					else {
						echo '<option value="'.$role_value.'" >'.$name.'</option><br />';
					}
				}
				?>
			</select>
		</p>

		<!-- Input upload path -->
		<p>
			<label for="<?php echo $this->get_field_id( 'upload_path' ); ?>"><?php _e('Upload Path:', 'upload-widget'); ?></label>
			<input id="<?php echo $this->get_field_id( 'upload_path' ); ?>" name="<?php echo $this->get_field_name( 'upload_path' ); ?>" value="<?php echo $instance['upload_path']; ?>" style="width:100%;" />
		</p>

		<?php
		/* Check if upload path exist */
		if ( !is_dir( $this->upload_widget_full_path($instance) ) ) {		
			echo  '<font color="red"><strong>'.__("Folder &#171; ", "upload-widget").$this->upload_widget_full_path($instance).__(" &#187; doesn't exist !", "upload-widget").'</strong></font>';
		}
		?>

		<!-- Input Allowed files -->
		<p>
			<label for="<?php echo $this->get_field_id( 'upload_patterns' ); ?>"><?php _e('Allowed Files Separated by a Comma:<br />( Use wildcard characters "*" and "?" )', 'upload-widget'); ?></label>
			<input id="<?php echo $this->get_field_id( 'upload_patterns' ); ?>" name="<?php echo $this->get_field_name( 'upload_patterns' ); ?>" value="<?php echo $instance['upload_patterns']; ?>" style="width:100%;" />
		</p>

		<!-- Input Max size file -->
		<p>
			<label for="<?php echo $this->get_field_id( 'upload_max_size' ); ?>"><?php _e('Max Size File (in Mb):', 'upload-widget'); ?></label>
			<input id="<?php echo $this->get_field_id( 'upload_max_size' ); ?>" name="<?php echo $this->get_field_name( 'upload_max_size' ); ?>" value="<?php echo $instance['upload_max_size']; ?>" style="width:100%;" />
		</p>
	
	<?php	
	}
}
?>