<?php

namespace SmartcatSupport\admin;

use smartcat\admin\MenuPageTab;

class ReportsOverviewTab extends MenuPageTab {

    private $start;
    private $end;

    private $date_range_options;

    private $opened_tickets = array();
    private $closed_tickets = array();

    public function __construct() {

        parent::__construct( array(
            'title' => __( 'Overview', \SmartcatSupport\PLUGIN_ID )
        ) );

        $this->date_range_options = array(
            'last_week'     => __( 'Last 7 Days', \SmartcatSupport\PLUGIN_ID ),
            'this_month'    => __( 'This Month', \SmartcatSupport\PLUGIN_ID ),
            'last_month'    => __( 'Last Month', \SmartcatSupport\PLUGIN_ID ),
            'this_year'     => __( 'This Year', \SmartcatSupport\PLUGIN_ID ),
            'custom'        => __( 'Custom Range', \SmartcatSupport\PLUGIN_ID ),
        );

        $start_init = '7 days ago';
        $end_init = 'now';

        if( isset( $_GET['start_date'] ) && isset( $_GET['end_date'] ) ) {
            $start = date( 'Y-m-d', strtotime( $_GET['start_date'] ) );
            $end = date( 'Y-m-d', strtotime( $_GET['end_date'] ) );

            if( $start < $end ) {
                $min = date( 'Y-m-d', strtotime( '-2 years' ) );
                $max = date( 'Y-m-d', strtotime( 'now' ) );

                $start_init = $start >= $min && $start <= $max ? $start : $start_init;
                $end_init = $end >= $min && $end > $start && $end < $max ? $end : $end_init;
            }
        }

        $this->start = new \DateTimeImmutable( $start_init );
        $this->end = new \DateTimeImmutable( $end_init );

        $ticket_stats = \SmartcatSupport\statprocs\tickets_overview_by_range(  $this->start, $this->end );

        foreach( $ticket_stats as $stat ) {
            $this->opened_tickets[ $stat['date_formatted'] ] = $stat['opened'];
            $this->closed_tickets[ $stat['date_formatted'] ] = $stat['closed'];
        }

    }

    private function graph_data() { ?>

        <script>

            jQuery(document).ready( function () {

                new Chartist.Line('#ticket-overview-chart', {
                    labels: <?php echo wp_json_encode( array_keys( $this->opened_tickets ) ); ?>,
                    series: [{
                        name: 'opened-tickets',
                        data: <?php echo wp_json_encode( array_values( $this->opened_tickets ) ); ?>
                    }, {
                        name: 'closed-tickets',
                        data: <?php echo wp_json_encode( array_values( $this->closed_tickets ) ); ?>
                    }]
                }, {
                    fullWidth: true,
                    series: {
                        'opened-tickets': {
                            lineSmooth: false,
                            showArea: true
                        },
                        'closed-tickets': {
                            lineSmooth: false,
                            showArea: true
                        }
                    },

                    axisY: {
                        onlyInteger: true
                    }
                });

            });

        </script>

        <div class="stats-chart-wrapper"><div id="ticket-overview-chart" class="ct-chart ct-golden-section"></div></div>

    <?php }

    public function render() { ?>

        <div class="stats-overview stat-section">

            <form method="get">

                <input type="hidden" name="page" value="<?php echo $this->page; ?>" />
                <input type="hidden" name="tab" value="<?php echo $this->slug; ?>" />

                <div class="stats-header">

                    <div class="form-inline">

                        <div class="control-group">

                            <select name="date_range" class="date-range-select form-control">

                                <?php foreach( $this->date_range_options as $option => $label ) : ?>

                                    <option value="<?php echo $option; ?>"
                                        <?php selected( $option, isset( $_GET['date_range'] ) ? $_GET['date_range'] : '' ); ?>><?php echo $label; ?></option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="date-range control-group <?php echo isset( $_GET['date_range'] ) && $_GET['date_range'] == 'custom' ? '' : 'hidden'; ?>">

                            <input name="start_date" class="date start-date" type="text" value="<?php echo $this->start->format( 'd-m-Y' ); ?>" />

                            <span><?php _e( 'to', \SmartcatSupport\PLUGIN_ID ); ?></span>

                            <input name="end_date" class="date end-date" type="text" value="<?php echo $this->end->format( 'd-m-Y' ); ?>"/>

                        </div>

                        <div class="control-group">

                            <button type="submit" class="form-control button button-secondary"><?php _e( 'Go', \SmartcatSupport\PLUGIN_ID ); ?></button>

                        </div>

                    </div>

                </div>

                <?php $this->graph_data(); ?>

            </form>

        </div>

    <?php }
}