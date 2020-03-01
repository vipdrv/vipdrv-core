<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'FusionRedux_VendorURL' ) ) {
    class FusionRedux_VendorURL {
        static public $url;
        static public $dir;

        public static function get_url( $handle ) {
            $min    = FusionRedux_Functions::isMin();
            $ace    = self::$dir . 'vendor/ace_editor/ace.js';
            $s2js   = self::$dir . 'vendor/select3/select3' . $min . '.js';
            $s2css  = self::$dir . 'vendor/select3/select3.css';

            if ( $handle == 'ace-editor-js' && file_exists( $ace ) ) {
                return self::$url . 'vendor/ace_editor/ace.js';
            } elseif ( $handle == 'select3-js' && file_exists( $s2js ) ) {
                return self::$url . 'vendor/select3/select3.js';
            } elseif ( $handle == 'select3-css' && file_exists( $s2css ) ) {
                return self::$url . 'vendor/select3/select3.css';
            }
        }
    }
}