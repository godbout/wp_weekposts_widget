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
    function WeekpostWidget()
    {
        $widget_ops = array('classname' => 'WeekpostWidget', 'description' => 'Displays all of the current week posts, or last week posts' );
        $this->WP_Widget('WeekpostWidget', 'Weekpost', $widget_ops);
    }
 
    function form($instance)
    {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'type' => '' ) );
        $title = $instance['title'];
        $type = $instance['type'];
?>
    <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"></label>
        Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" />
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
<?php
    }
 
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = $new_instance['title'];
        $instance['type'] = $new_instance['type'];
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

        if ($the_query->have_posts()) {
            echo '<ul>';
            while ($the_query->have_posts()) {
                $the_query->the_post();

                if (mysql2date('d m o', get_the_date()) === date('d m o')) {
                    $date = 'Today';
                } elseif (mysql2date('d m o', get_the_date()) === date('d m o', strtotime('-1 days'))) {
                    $date = 'Yesterday';
                } else {
                    $date = mysql2date('l', get_the_date());
                }

                echo '<li><a href="' .get_permalink() .'">[' .$date . '] ' .get_the_title() .'</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<a href="https://twitter.com/dailycuckoo"</a>Nothing yet here, so go ask your question @DailyCuckoo!';
        }
        wp_reset_query();
 
        echo $after_widget;
    }
}

add_action( 'widgets_init', create_function('', 'return register_widget("WeekpostWidget");') );
?>