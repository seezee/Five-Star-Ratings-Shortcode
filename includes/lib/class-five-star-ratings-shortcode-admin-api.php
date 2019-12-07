<?php
/**
 * Post type Admin API file.
 *
 * @package Five Star Ratings Shortcode/Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin API class.
 */
class Five_Star_Ratings_Shortcode_Admin_API {

	/**
	 * Generate HTML for displaying fields.
	 *
	 * @param  array   $data Data array.
	 * @param  object  $post Post object.
	 * @param  boolean $echo  Whether to echo the field HTML or return it.
	 * @return string
	 */
	public function display_field( $data = array(), $post = null, $echo = true ) {

		// Get field info.
		if ( isset( $data['field'] ) ) {
			$field = $data['field'];
		} else {
			$field = $data;
		}
		

		// Check for prefix on option name.
		$option_name = '';
		if ( isset( $data['prefix'] ) ) {
			$option_name = $data['prefix'];
		}

		// Get saved data.
		$data = '';
		if ( $post ) {

			// Get saved field data.
			$option_name .= $field['id'];
			$option       = get_post_meta( $post->ID, $field['id'], true );

			// Get data to display in field.
			if ( isset( $option ) ) {
				$data = $option;
			}
		} else {

			// Get saved option.
			$option_name .= $field['id'];
			$option       = get_option( $option_name );

			// Get data to display in field.
			if ( isset( $option ) ) {
				$data = $option;
			}
		}

		// Show default data if no option saved and default is supplied.
		if ( false === $data && isset( $field['default'] ) ) {
			$data = $field['default'];
		} elseif ( false === $data ) {
			$data = '';
		}

		$html = '';

		switch ( $field['type'] ) {

			case 'radio':
				foreach ( $field['options'] as $k => $v ) {
					$checked = false;
					if ( $k === $data ) {
						$checked = true;
					}
					$html .= '<label class="radio-label" for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input class="radio" type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
				break;
			case 'select':
				$html .= '<select class="select" name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach ( $field['options'] as $k => $v ) {
					$selected = false;
					if ( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'range':
				$min = '';
				if ( isset( $field['min'] ) ) {
					$min = ' min="' . esc_attr( $field['min'] ) . '"';
				}

				$max = '';
				if ( isset( $field['max'] ) ) {
					$max = ' max="' . esc_attr( $field['max'] ) . '"';
				}

				$html .= '<input class="input input-range" id="' . esc_attr( $field['id'] ) . '" type="range" name="' . esc_attr( $option_name ) .'" value="' . esc_attr( $data ) . '"  onchange="updateTextInput(this.value);" ' . $min . '' . $max . '/>
				<input class="input input-display" id="' . esc_attr( $field['id'] ) . 'Value" type="text" value="' . esc_attr( $data ) . '" ' . $min . '' . $max . ' size="2" disabled="disabled" /> 
<div class="ticks">
  <span class="tick">3</span>
  <span class="tick">4</span>
  <span class="tick">5</span>
  <span class="tick">6</span>
  <span class="tick">7</span>
  <span class="tick">8</span>
  <span class="tick">9</span>
  <span class="tick">10</span>
</div>' . "\n";
			break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" id="<?php esc_attr_e( $field['id'] ) ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

		}

		switch ( $field['type'] ) {

			case 'radio':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
				break;

			default:
				if ( ! $post ) {
					$html .= '<label for="' . esc_attr( $field['id'] ) . '">' . chr( 0x0D ) . chr( 0x0A );
				}

				$html .= '<span class="description">' . $field['description'] . '</span>' . chr( 0x0D ) . chr( 0x0A );

				if ( ! $post ) {
					$html .= '</label>' . chr( 0x0D ) . chr( 0x0A );
				}
				break;
		}

		if ( ! $echo ) {
			return $html;
		}

		echo $html; // phpcs:ignore
	}

	/**
	 * Validate form field.
	 *
	 * @param  string $data Submitted value.
	 * @param  string $type Type of field to validate.
	 * @return string       Validated value
	 */
	public function validate_field( $data = '', $type = 'text' ) {

		switch ( $type ) {
			case 'text':
				$data = esc_attr( $data );
				break;
		}

		return $data;
	}

}
