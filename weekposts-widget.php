<?php
/**
 * Plugin Name: Weekpost Plugin
 * Plugin URI: https://github.com/godbout/wp_weekposts_widget/blob/master/weekposts-widget.php
 * Description: Shows all the current week's posts, or all of last week's posts
 * Version: 1.0
 * Author: Guill Lo
 * Author URI: https://github.com/godbout
 * License: The good one that you can do everything with
 */

class WeekpostWidget extends WP_Widget
{
    function __construct()
    {
        $widget_ops = array('classname' => 'WeekpostWidget', 'description' => 'Displays all of the current week posts, or last week posts' );
        parent::__construct('WeekpostWidget', 'Weekpost', $widget_ops);
    }

    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'type' => '', 'show_date' ) );
        $title = $instance['title'];
        $type = $instance['type'];
        $show_date = $instance['show_date'];
?>
    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"></label>
        Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('type'); ?>"></label>
        <select name="<?php echo $this->get_field_name('type'); ?>" id="<?php echo $this->get_field_id('type'); ?>" class="widefat">
<?php
    $options = array('This week', 'Last week');
    foreach ($options as $option) {
        echo '<option value="' .$option .'" id="' .$option .'"', $type == $option ? ' selected="selected"' : '', '>', $option, '</option>';
    }
?>
        </select>
    </p>
    <p>
        <label for="<?php echo $this->get_field_id('show_date'); ?>"></label>
        Show date: <input id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" type="checkbox" value="1" <?php checked( '1', $show_date ); ?> />
    </p>
<?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['type'] = $new_instance['type'];
        $instance['show_date'] = $new_instance['show_date'];

        return $instance;
    }

    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);

        echo $before_widget;
        $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);

        if (!empty($title)) {
            echo $before_title . $title . $after_title;;
        }

        if ($instance['type'] === 'This week') {
            $week = date('W');
        } else {
            $week = date('W') - 1;
        }

        $year = date('Y');
        $the_query = new WP_Query( 'year=' . $year . '&w=' . $week );

        echo '<ul>';
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();

                if ($instance['show_date'] === '1') {
                    if (mysql2date('d m o', get_the_date()) === date('d m o')) {
                        $date = 'Today';
                    } elseif (mysql2date('d m o', get_the_date()) === date('d m o', strtotime('-1 days'))) {
                        $date = 'Yesterday';
                    } else {
                        $date = mysql2date('l', get_the_date());
                    }

                    $date = '[' .$date . '] ';
                }

                echo '<li><a href="' .get_permalink() .'">' .$date .get_the_title() .'</a></li>';
            }
        } else {
            echo '<li><a href="https://twitter.com/dailycuckoo">Nothing yet here, so go ask your question @DailyCuckoo!</a></li>';
        }
        echo '</ul>';
        wp_reset_query();

        echo $after_widget;
    }
}

add_action(
    'widgets_init', function () {
        register_widget('WeekpostWidget');
    }
);
?>
