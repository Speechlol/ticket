<?php

use SmartcatSupport\descriptor\Option;

$attachments = get_attached_media( 'image', $ticket->ID );
$attachment_count = count( $attachments );

$status = get_post_meta( $ticket->ID, 'status', true );

$user = wp_get_current_user();

?>
<div class="loader-mask"></div>

<div class="row ticket-detail" style="display: none">

    <div class="sidebar col-sm-4 col-sm-push-8"><p class="text-center"><?php _e( 'Loading...', \SmartcatSupport\PLUGIN_ID ); ?></p></div>

    <div class="discussion-area col-sm-8 col-sm-pull-4">

        <div class="ticket panel panel-default ">

            <div class="panel-heading">

                <p class="panel-title"><?php esc_html_e( $ticket->post_title ); ?></p>

            </div>

            <div class="panel-body">

                <p><?php echo $ticket->post_content; ?></p>

            </div>

        </div>

        <div class="comments"></div>

        <div class="comment-reply-wrapper">

            <div class="comment comment-reply panel panel-default">

                <div class="panel-heading nav-header">

                  <ul class="nav nav-tabs">

                      <li class="tab editor-tab active">
                          <a class="nav-link" data-toggle="tab" href="#<?php echo $ticket->ID; ?>-editor"><?php _e( 'Write', \SmartcatSupport\PLUGIN_ID ); ?></a>
                      </li>

                      <li class="tab editor-tab preview">
                          <a class="nav-link" data-toggle="tab" href="#<?php echo $ticket->ID; ?>-preview"><?php _e( 'Preview', \SmartcatSupport\PLUGIN_ID ); ?></a>
                      </li>

                  </ul>

                </div>

                <div class="panel-body editor-area">

                    <form class="comment-form">

                        <div class="tab-content">

                            <div id="<?php echo $ticket->ID; ?>-editor" class="editor-pane tab-pane active">

                                <textarea class="editor-content form-control" name="content" rows="5"></textarea>

                            </div>

                            <div id="<?php echo $ticket->ID; ?>-preview" class="tab-pane preview">

                                <div class="rendered"></div>

                            </div>

                        </div>

                        <input type="hidden" name="id" value="<?php echo $ticket->ID; ?>">

                        <div class="bottom">

                            <span class="text-right">

                                <?php if( $status != 'closed' && !current_user_can( 'manage_support_tickets' ) ) : ?>

                                    <button id="close-ticket-<?php echo $ticket->ID; ?>"
                                            type="button"
                                            class="close-ticket button"
                                            data-toggle="modal"
                                            data-target="#close-ticket-modal-<?php echo $ticket->ID; ?>">

                                        <span class="glyphicon glyphicon-ok-sign button-icon"></span>

                                        <span><?php _e( 'Close Ticket', \SmartcatSupport\PLUGIN_ID ); ?></span>

                                    </button>

                                <?php endif; ?>

                                    <button type="submit" class="button button-submit" disabled="true">

                                        <span class="glyphicon glyphicon-send button-icon"></span>

                                        <span><?php _e( get_option( Option::REPLY_BTN_TEXT, Option\Defaults::REPLY_BTN_TEXT ) ); ?></span>

                                    </button>

                                </span>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<?php if( $status != 'closed' && !current_user_can( 'manage_support_tickets' ) ) : ?>

    <div id="close-ticket-modal-<?php echo $ticket->ID; ?>"
         data-ticket_id="<?php echo $ticket->ID; ?>"
         class="modal close-ticket-modal fade">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal">&times;</button>

                    <h4 class="modal-title"><?php _e( 'Close Ticket', \SmartcatSupport\PLUGIN_ID ); ?></h4>

                </div>

                <div class="modal-body">

                    <p><?php _e( 'This operation cannot be undone! Are you sure you want to do this?', \SmartcatSupport\PLUGIN_ID ); ?></p>

                </div>

                <div class="modal-footer">

                    <button type="button" class="button confirm-close-ticket" data-ticket_id="<?php echo $ticket->ID; ?>">

                        <span class="glyphicon glyphicon-ok button-icon"></span>

                        <span><?php _e( 'Yes', \SmartcatSupport\PLUGIN_ID ); ?></span>

                    </button>


                    <button type="button" class="button button-submit close-modal"
                            data-target="#close-ticket-modal-<?php echo $ticket->ID; ?>"
                            data-toggle="modal">

                        <span class="glyphicon glyphicon-ban-circle button-icon"></span>

                        <span><?php _e( 'Cancel', \SmartcatSupport\PLUGIN_ID ); ?></span>

                    </button>

                </div>

            </div>

        </div>

    </div>

<?php endif; ?>

<div id="attachment-modal-<?php echo $ticket->ID; ?>"
     data-ticket_id="<?php echo $ticket->ID; ?>"
     class="modal attachment-modal fade">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">&times;</button>

                <h4 class="modal-title"><?php _e( 'Attach Images', \SmartcatSupport\PLUGIN_ID ); ?></h4>

            </div>

            <div class="modal-body">

                <form id="attachment-dropzone-<?php echo $ticket->ID; ?>" class="dropzone">

                    <?php wp_nonce_field( 'support_ajax', '_ajax_nonce' ); ?>

                    <input type="hidden" name="ticket_id" value="<?php echo $ticket->ID; ?>" />

                </form>

            </div>

            <div class="modal-footer">

                <button type="button" class="button button-submit close-modal"
                        data-target="#attachment-modal-<?php echo $ticket->ID; ?>"
                        data-toggle="modal">

                    <?php _e( 'Done', \SmartcatSupport\PLUGIN_ID ); ?>

                </button>

            </div>

        </div>

    </div>

</div>
