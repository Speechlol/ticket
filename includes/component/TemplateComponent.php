<?php

namespace SmartcatSupport\component;

use smartcat\core\AbstractComponent;
use SmartcatSupport\descriptor\Option;
use const SmartcatSupport\PLUGIN_ID;

class TemplateComponent extends AbstractComponent {

    public function swap_template( $template ) {
        if( is_page( get_option( Option::TEMPLATE_PAGE_ID ) ) ) {
            $template = $this->plugin->template_dir . '/app.php';
        }

        return $template;
    }

    public function restore_template( $val ) {
        if( $val == 'on' ) {
            $this->setup_template();
        }

        return '';
    }

    public function setup_template() {
        $post_id = null;
        $post = get_post( get_option( Option::TEMPLATE_PAGE_ID ) ) ;

        if( empty( $post ) ) {
            $post_id = wp_insert_post(
                array(
                    'post_type' =>  'page',
                    'post_status' => 'publish',
                    'post_title' => __( 'Support', PLUGIN_ID )
                )
            );
        } else if( $post->post_status == 'trash' ) {
            wp_untrash_post( $post->ID );

            $post_id = $post->ID;
        } else {
            $post_id = $post->ID;
        }

        if( !empty( $post_id ) ) {
            update_option( Option::TEMPLATE_PAGE_ID, $post_id );
        }
    }

    public function subscribed_hooks() {
        return array(
            'template_include' => array( 'swap_template' ),
            $this->plugin->id() . '_setup' => array( 'setup_template' ),
            'pre_update_option_' . Option::RESTORE_TEMPLATE => array( 'restore_template' )
        );
    }
}