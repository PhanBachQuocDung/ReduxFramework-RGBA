<?php

    /**
     * Redux Framework is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 2 of the License, or
     * any later version.
     * Redux Framework is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU General Public License for more details.
     * You should have received a copy of the GNU General Public License
     * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
     *
     * @package     Redux Framework
     * @subpackage  Spectrum Color Picker
     * @author      Kevin Provance (kprovance)
     * @version     1.0.0
     */

    // Exit if accessed directly
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    // Don't duplicate me!
    if ( ! class_exists( 'ReduxFramework_color_rgba' ) ) {

        /**
         * Main ReduxFramework_color_rgba class
         *
         * @since       1.0.0
         */
        class ReduxFramework_color_rgba {

            /**
             * Class Constructor. Defines the args for the extions class
             *
             * @since       1.0.0
             * @access      public
             *
             * @param       array $field  Field sections.
             * @param       array $value  Values.
             * @param       array $parent Parent object.
             *
             * @return      void
             */
            public function __construct( $field = array(), $value = '', $parent ) {

                // Set required variables
                $this->parent = $parent;
                $this->field  = (array) $field;
                $this->value  = $value;

                $defaults = array(
                    'color' => '',
                    'alpha' => 1,
                    'rgba'  => ''
                );

                $option_defaults = array(
                    "show_input"             => true,
                    "show_initial"           => false,
                    "show_alpha"             => true,
                    "show_palette"           => false,
                    "show_palette_only"      => false,
                    "max_palette_size"       => 10,
                    "show_selection_palette" => false,
                    "allow_empty"            => true,
                    "clickout_fires_change"  => false,
                    "choose_text"            => __( 'Choose', 'redux-framework' ),
                    "cancel_text"            => __( 'Cancel', 'redux-framework' ),
                    "show_buttons"           => true,
                    "input_text"             => __( 'Select Color', 'redux-framework' ),
                    "palette"                => null,
                );

                $this->value = wp_parse_args( $this->value, $defaults );

                $this->field['options'] = isset( $this->field['options'] ) ? wp_parse_args( $this->field['options'], $option_defaults ) : $option_defaults;

                // Convert empty array to null, if there.
                $this->field['options']['palette'] = empty( $this->field['options']['palette'] ) ? null : $this->field['options']['palette'];

                $this->field['output_transparent'] = isset( $this->field['output_transparent'] ) ? $this->field['output_transparent'] : false;
            }


            /**
             * Field Render Function.
             * Takes the vars and outputs the HTML for the field in the settings
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function render() {

                $field_id = $this->field['id'];

                // Color picker container
                echo '<div 
                      class="redux-color-rgba-container ' . $this->field['class'] . '" 
                      data-id="' . $field_id . '"
                  >';

                // Colour picker layout
                $opt_name = $this->parent->args['opt_name'];

                if ( '' == $this->value['color'] || 'transparent' == $this->value['color'] ) {
                    $color = '';
                } else {
                    $color = Redux_Helpers::hex2rgba( $this->value['color'], $this->value['alpha'] );
                }

                if ( $this->value['rgba'] == '' && $this->value['color'] != '' ) {
                    $this->value['rgba'] = Redux_Helpers::hex2rgba( $this->value['color'], $this->value['alpha'] );
                }

                echo '<input
                        name="' . $this->field['name'] . $this->field['name_suffix'] . '[color]"
                        id="' . $field_id . '-color"
                        class="redux-color-rgba redux-color-init color-picker"
                        type="text"
                        data-alpha="true"
                        value="' . $this->value['color'] . '"
                        data-color="' . $color . '"
                        data-id="' . $field_id . '"
                        data-current-color="' . $this->value['color'] . '"
                        data-block-id="' . $field_id . '"
                        data-output-transparent="' . $this->field['output_transparent'] . '"
                      />';

                echo '<input
                        type="hidden"
                        class="redux-hidden-color"
                        data-id="' . $field_id . '-color"
                        id="' . $field_id . '-color"
                        value="' . $this->value['color'] . '"
                      />';

                // Hidden input for alpha channel
                echo '<input
                        type="hidden"
                        class="redux-hidden-alpha"
                        data-id="' . $field_id . '-alpha"
                        name="' . $this->field['name'] . $this->field['name_suffix'] . '[alpha]' . '"
                        id="' . $field_id . '-alpha"
                        value="' . $this->value['alpha'] . '"
                      />';

                // Hidden input for rgba
                echo '<input
                        type="hidden"
                        class="redux-hidden-rgba"
                        data-id="' . $field_id . '-rgba"
                        name="' . $this->field['name'] . $this->field['name_suffix'] . '[rgba]' . '"
                        id="' . $field_id . '-rgba"
                        value="' . $this->value['rgba'] . '"
                      />';

                echo '</div>';
            }

            /**
             * Enqueue Function.
             * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function enqueue() {

                // Set up min files for dev_mode = false.
                $min = Redux_Functions::isMin();

                // Field dependent JS
                if ( ! wp_script_is( 'redux-field-color-rgba-js' ) ) {
                    wp_enqueue_script( 'wp-color-picker-alpha', ReduxFramework::$_url . 'inc/fields/color/wp-color-picker-alpha.min.js', array( 'jquery' , 'wp-color-picker' ) );
                    wp_enqueue_script(
                        'redux-field-color-rgba-js',
                        ReduxFramework::$_url . 'inc/fields/color_rgba/field_color_rgba' . Redux_Functions::isMin() . '.js',
                        array( 'jquery', 'wp-color-picker' ),
                        time(),
                        true
                    );
                }

                // Spectrum CSS
                // if ( ! wp_style_is( 'redux-spectrum-css' ) ) {
                //     wp_enqueue_style( 'redux-spectrum-css' );
                // }

                if ( $this->parent->args['dev_mode'] ) {
                    if ( ! wp_style_is( 'redux-field-color-rgba-css' ) ) {
                        wp_enqueue_style(
                            'redux-field-color-rgba-css',
                            ReduxFramework::$_url . 'inc/fields/color_rgba/field_color_rgba.css',
                            array(),
                            time(),
                            'all'
                        );
                    }
                }
            }

            /**
             * getColorVal.  Returns formatted color val in hex or rgba.
             * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
             *
             * @since       1.0.0
             * @access      private
             * @return      string
             */
            private function getColorVal() {

                // No notices
                $color = '';
                $alpha = 1;
                $rgba  = '';

                // Must be an array
                if ( is_array( $this->value ) ) {

                    // Enum array to parse values
                    foreach ( $this->value as $id => $val ) {

                        // Sanitize alpha
                        if ( $id == 'alpha' ) {
                            $alpha = ! empty( $val ) ? $val : 1;
                        } elseif ( $id == 'color' ) {
                            $color = ! empty( $val ) ? $val : '';
                        } elseif ( $id == 'rgba' ) {
                            $rgba = ! empty( $val ) ? $val : '';
                            $rgba = Redux_Helpers::hex2rgba( $color, $alpha );
                        }
                    }

                    // Only build rgba output if alpha ia less than 1
                    if ( $alpha < 1 && $alpha <> '' ) {
                        $color = $rgba;
                    }
                }

                return $color;
            }

            /**
             * Output Function.
             * Used to enqueue to the front-end
             *
             * @since       1.0.0
             * @access      public
             * @return      void
             */
            public function output() {
                if ( ! empty( $this->value ) ) {
                    $style = '';

                    $mode = ( isset( $this->field['mode'] ) && ! empty( $this->field['mode'] ) ? $this->field['mode'] : 'color' );

                    $color_val = $this->getColorVal();

                    $style .= $mode . ':' . $color_val . ';';

                    if ( ! empty( $this->field['output'] ) && is_array( $this->field['output'] ) ) {
                        if ( ! empty( $color_val ) ) {
                            $css = Redux_Functions::parseCSS( $this->field['output'], $style, $color_val );
                            $this->parent->outputCSS .= $css;
                        }
                    }

                    if ( ! empty( $this->field['compiler'] ) && is_array( $this->field['compiler'] ) ) {
                        if ( ! empty( $color_val ) ) {
                            $css = Redux_Functions::parseCSS( $this->field['compiler'], $style, $color_val );
                            $this->parent->compilerCSS .= $css;
                        }
                    }
                }
            }
        }
    }
