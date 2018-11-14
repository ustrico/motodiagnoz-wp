<?php

class WP_Widget_Countdown extends WP_Widget {


	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_countdown',
			'description' => __( 'Обратный отсчет' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array( 'width' => 400, 'height' => 350 );
		parent::__construct( 'countdown', __( 'Обратный отсчет' ), $widget_ops, $control_ops );
	}


	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		$date = ! empty( $instance['date'] ) ? $instance['date'] : '0000-00-00';
		$time = ! empty( $instance['time'] ) ? $instance['time'] : '00:00';

		$timeend = strtotime($date . ' ' . $time);
		$timenow = current_time('timestamp');
		$counter = array(0,0,0,0);
		$days = 0; $hours = 0;
		$timeleft = $timeend - $timenow;
		if ($timeleft > 0){
			$days = intval($timeleft / 86400);
			$hours = intval(($timeleft - $days * 86400) / 3600);
			$counter[0] = intval($days / 10);
			$counter[1] = $days - $counter[0] * 10;
			$counter[2] = intval($hours / 10);
			$counter[3] = $hours - $counter[2] * 10;

			echo $args['before_widget'];
			if ( ! empty( $title ) ) {
				echo '<div class="countdown-title">' . $title . '</div>';
			} ?>
			<div class="countdown-date">
				<div class="countdown-date0"><?=$counter[0]?></div><div class="countdown-date1"><?=$counter[1]?></div>
				<div class="countdown-date2"><?=$counter[2]?></div><div class="countdown-date3"><?=$counter[3]?></div>
			</div>
			<div class="countdown-legend">
				<div class="countdown-legend0"><?=$this->plural('день', 'дня', 'дней', $counter[1], $counter[0])?></div><div class="countdown-legend1"><?=$this->plural('час', 'часа', 'часов', $counter[3], $counter[2])?></div>
			</div>
			<?php
			echo $args['after_widget'];
		}

	}

	private function plural( $one, $two, $five, $number, $number1 ) {
		if ($number1===1){
			return $five;
		} else {
			if ($number===1){
				return $one;
			} elseif ( ($number>1) and ($number<5) ){
				return $two;
			} else {
				return $five;
			}	
		}				
	}


	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['date'] = $new_instance['date'];
		$instance['time'] = $new_instance['time'];
		return $instance;
	}


	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'date' => '', 'time' => '' ) );
		$title = sanitize_text_field( $instance['title'] );
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'date' ); ?>"><?php _e( 'Date:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>" type="date" value="<?=$instance['date']?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'time' ); ?>"><?php _e( 'Time:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('time'); ?>" name="<?php echo $this->get_field_name('time'); ?>" type="time" value="<?=$instance['time']?>" /></p>

		<?php
	}
}
