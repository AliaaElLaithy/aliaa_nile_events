<?php
 function nile_events_shortcode_ar() { 

    $options = get_option( 'event_option' );
    $num_of_posts = $options['num_of_posts'];
    $events_order = $options['events_order'];
    
    $loop_counter = 1;
 ?>      

    <div id="e-shortcode-ar">
        <h2>الأحداث</h2>
        <?php
                $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                if($events_order=='bydate'){
                    $args = array(
                        'post_type'=>'a_events',
                        'orderby' => 'date',
                        'order' => 'DESC', 
                        'posts_per_page' => $num_of_posts,
                        'paged' => $paged,
                    );
                    
                }elseif($events_order=='alphabetical'){
                    $args = array(
                        'post_type'=>'a_events',
                        'orderby' => 'title',
                        'order' => 'ASC' ,
                        'posts_per_page' => $num_of_posts,
                        'paged' => $paged,
                    );
                    
                }
                $loop=new WP_Query($args); 
        ?>
        <div class="e-shortcode-container">
            <?php while($loop->have_posts() ) : $loop->the_post();
             $id = get_the_ID();
            ?>
                <div class="e-shortcode-col">
                    <div class="e-shortcode-item">
                        <div>
                            <?php 
                                    if(has_post_thumbnail()){// check for feature image
                            ?>
                                        <a href="<?php echo  get_the_permalink();?>">
                                            <img src="<?php echo get_the_post_thumbnail_url(); ?>"/>
                                        </a>

                            <?php   }else{  ?>
                                        <a href="<?php echo  get_the_permalink();?>">
                                            <img src="<?php echo get_site_url(); ?>/wp-content/plugins/aliaa_nile_events/assets/images/default.jpg"/>
                                        </a>
                            <?php   } ?>
                        </div>
                        <div>
                            <h4><a href="<?php echo  get_the_permalink();?>"><?php echo get_the_title();?></a></h4>
                        </div>
                        <div>
                            <?php 
                                $start_date = get_post_meta( $id , 'start_date',true);
                            ?>
                            <p> <?php echo $start_date; ?>
                             
                        </div>
                        <div>
                            <?php 
                                $event_excerpt = get_post_meta( $id , 'event_excerpt',true);
                            ?>
                            <p><?php echo $event_excerpt; ?> </p>
                        </div>
                        <div>
                            <p><a href="<?php echo  get_the_permalink();?>" class="event-perma-link">إقرأ المزيد</a></p>
                        </div>
                    </div>
                </div>
            <?php 
                $loop_counter += 1;
                endwhile; wp_reset_query();
            ?>
        </div>
    </div>
            
 <?php } ?>
<?php 
    add_shortcode( 'nile_events_ar', 'nile_events_shortcode_ar' );
?>