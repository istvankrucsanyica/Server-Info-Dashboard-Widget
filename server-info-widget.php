<?php

  /**
   *
   * Plugin Name:       Server Info Dashboard Widget
   * Plugin URI:        https://github.com/istvankrucsanyica
   * Description:       Server Info Dashboard Widget
   * Version:           1.0.0
   * Author:            Istvan Krucsanyica at Kreatív Vonalak
   * Author URI:        https://github.com/istvankrucsanyica
   * Text Domain:       server-info-widget
   * Domain Path:       /languages
   * License:           GPLv2
   * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
   *
   * 2018, Istvan Krucsanyica at Kreatív Vonalak (email : istvan.krucsanyica@gmail.com)
   *
   * This program is free software; you can redistribute it and/or modify
   * it under the terms of the GNU General Public License version 2,
   * as published by the Free Software Foundation.
   *
   * ou may NOT assume that you can use any other version of the GPL.
   *
   * This program is distributed in the hope that it will be useful,
   * but WITHOUT ANY WARRANTY; without even the implied warranty of
   * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   * GNU General Public License for more details.
   *
   * The license for this software can likely be found here:
   * http://www.gnu.org/licenses/gpl-2.0.html
   */

  // If this file is called directly, abort.
  if ( ! defined( 'WPINC' ) ) {

    die;

  }

  if ( ! class_exists( 'Serverinfowidget_class' ) ) {

    class Serverinfowidget_class {

      public function __construct() {

        if ( is_admin() ) {

          add_action( 'wp_dashboard_setup', array( $this, 'add_to_dashboard_widgets' ) );
          add_action( 'admin_footer', array( $this, 'render_style' ), 10, 1 );

        }

      }


      public function add_to_dashboard_widgets() {

        global $wp_meta_boxes;

        wp_add_dashboard_widget( 'serverinfo_widget', __('Szerver információk', 'server-info-widget' ), array( $this, 'render_serverinfo_dashboard' ) );

      }

      public function render_serverinfo_dashboard() {

        global $wpdb;

        echo '
        <table id="server-info-table">
          <tr>
            <td><strong>'. __('Apache verzió', 'server-info-widget') .':</strong><br/><small>'.$_SERVER['SERVER_SOFTWARE'].'</small></td>
            <td><strong>'. __('PHP verzió', 'server-info-widget') .':</strong><br/><small>'.phpversion().'</small></td>
          </tr>
          <tr>
            <td><strong>'. __('Max feltöltési méret', 'server-info-widget') .':</strong><br/><small>'.$this->convert( $this->let_to_num( ini_get( 'post_max_size' ) ) ).'</small></td>
            <td><strong>'. __('PHP Időkorlát', 'server-info-widget') .':</strong><br/><small>'.ini_get( 'max_execution_time' ).' '. __('mp', 'server-info-widget') .'</small></td>
          </tr>
          <tr>
            <td><strong>'. __('PHP max. bemeneti változók', 'server-info-widget') .':</strong><br/><small>'.ini_get( 'max_input_vars' ).'</small></td>
            <td><strong>'. __('Feltölthető file max. mérete', 'server-info-widget') .':</strong><br/><small>'.size_format( wp_max_upload_size() ).'</small></td>
          </tr>
          <tr>
            <td><strong>'. __('Alapértelmezett időzóna', 'server-info-widget') .':</strong><br/><small>'.date_default_timezone_get().'</small></td>
            <td><strong>'. __('Gzip', 'server-info-widget') .':</strong><br/><small>'.$this->enable_disable( is_callable( 'gzopen' ) ).'</small></td>
          </tr>
          <tr>
            <td><strong>'. __('Multibyte String', 'server-info-widget') .':</strong><br/><small>'.$this->enable_disable( extension_loaded( 'mbstring' ) ).'</small></td>
            <td><strong>'. __('MySQL verzió', 'server-info-widget') .':</strong><br/><small>'.$wpdb->db_version().'</small></td>
          </tr>
        </table>';

      }

      public function render_style() {

        echo '<style>
          #serverinfo_widget .inside { padding: 0; margin-top: 0;}
          #serverinfo_widget .hndle { border-bottom: 1px solid #e5e5e5; }
          #server-info-table { width: 100%; border: 0; border-collapse: collapse; }
          #server-info-table td { padding: 5px 7px; font-size: 13px; width: 50%; vertical-align: top; }
          #server-info-table td strong { font-weight: 700; color: #2e4053; }
          #server-info-table td small { color: #d35400; }
          #server-info-table tr td { border-bottom: 1px solid #e5e5e5; }
          #server-info-table tr td:nth-child(1) { border-right: 1px solid #e5e5e5; }
          #server-info-table tr:last-child td { border-bottom: 0; }
        </style>';

      }

      private function convert( $size ) {

        $unit=array('B','KB','MB','GB','TB','PB');
        return @round( $size / pow( 1024, ( $i = floor( log( $size, 1024 ) ) ) ),2 ).' '.$unit[$i];

      }

      private function let_to_num( $size ) {

        $l     = substr( $size, -1 );
        $ret   = substr( $size, 0, -1 );

        switch( strtoupper( $l ) ) {
          case 'P':
            $ret *= 1024;
          case 'T':
            $ret *= 1024;
          case 'G':
            $ret *= 1024;
          case 'M':
            $ret *= 1024;
          case 'K':
            $ret *= 1024;
        }

        return $ret;

      }

      private function enable_disable( $input ) {

        return ( $input == 1 ) ? __('Elérhető', 'server-info-widget') : __('Nem elérhető', 'server-info-widget');

      }

    }

  }

  add_action('plugins_loaded', 'server_info_widget_init');

  function server_info_widget_init() {

    if ( current_user_can( 'administrator' ) ) {

      new Serverinfowidget_class();

    }

  }


