<?php
/**
 * Listing search box
 *
 */
global $jobsearch_post_job_types, $jobsearch_plugin_options, $jobsearch_shortcode_jobs_frontend;

$user_id = $user_company = '';
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_company = get_user_meta($user_id, 'jobsearch_company', true);
}

$locations_view_type = isset($atts['job_loc_listing']) ? $atts['job_loc_listing'] : '';
$quick_apply_job = isset($atts['quick_apply_job']) ? $atts['quick_apply_job'] : '';
$quick_apply_job_btn = $quick_apply_job == 'on' && !wp_is_mobile() ? 'jobsearch-quick-apply-show' : '';
if (!is_array($locations_view_type)) {

    $loc_types_arr = $locations_view_type != '' ? explode(',', $locations_view_type) : '';
} else {
    $loc_types_arr = $locations_view_type;
}
$loc_view_country = $loc_view_state = $loc_view_city = false;
if (!empty($loc_types_arr)) {
    if (is_array($loc_types_arr) && in_array('country', $loc_types_arr)) {
        $loc_view_country = true;
    }
    if (is_array($loc_types_arr) && in_array('state', $loc_types_arr)) {
        $loc_view_state = true;
    }
    if (is_array($loc_types_arr) && in_array('city', $loc_types_arr)) {
        $loc_view_city = true;
    }
}

$job_types_switch = isset($jobsearch_plugin_options['job_types_switch']) ? $jobsearch_plugin_options['job_types_switch'] : '';
$all_location_allow = isset($jobsearch_plugin_options['all_location_allow']) ? $jobsearch_plugin_options['all_location_allow'] : '';
$default_job_no_custom_fields = isset($jobsearch_plugin_options['jobsearch_job_no_custom_fields']) ? $jobsearch_plugin_options['jobsearch_job_no_custom_fields'] : '';
if (function_exists('jobsearch_get_transient_obj') && false === ($job_view = jobsearch_get_transient_obj('jobsearch_job_view' . $job_short_counter))) {
    $job_view = isset($atts['job_view']) ? $atts['job_view'] : '';
}
$jobs_excerpt_length = isset($atts['jobs_excerpt_length']) ? $atts['jobs_excerpt_length'] : '18';
$jobsearch_split_map_title_limit = '10';

$job_no_custom_fields = isset($atts['job_no_custom_fields']) ? $atts['job_no_custom_fields'] : $default_job_no_custom_fields;
if ($job_no_custom_fields == '' || !is_numeric($job_no_custom_fields)) {
    $job_no_custom_fields = 3;
}
$job_filters = isset($atts['job_filters']) ? $atts['job_filters'] : '';
$jobsearch_jobs_title_limit = isset($atts['jobs_title_limit']) ? $atts['jobs_title_limit'] : '5';
// start ads script
$job_ads_switch = isset($atts['job_ads_switch']) ? $atts['job_ads_switch'] : 'no';
if ($job_ads_switch == 'yes') {
    $job_ads_after_list_series = isset($atts['job_ads_after_list_count']) ? $atts['job_ads_after_list_count'] : '5';
    if ($job_ads_after_list_series != '') {
        $job_ads_list_array = explode(",", $job_ads_after_list_series);
    }
    $job_ads_after_list_array_count = sizeof($job_ads_list_array);
    $job_ads_after_list_flag = 0;
    $i = 0;
    $array_i = 0;
    $job_ads_after_list_array_final = '';
    while ($job_ads_after_list_array_count > $array_i) {
        if (isset($job_ads_list_array[$array_i]) && $job_ads_list_array[$array_i] != '') {
            $job_ads_after_list_array[$i] = $job_ads_list_array[$array_i];
            $i++;
        }
        $array_i++;
    }
    // new count 
    $job_ads_after_list_array_count = sizeof($job_ads_after_list_array);
}

$jobs_ads_array = array();
if ($job_ads_switch == 'yes' && $job_ads_after_list_array_count > 0) {
    $list_count = 0;
    for ($i = 0; $i <= $job_loop_obj->found_posts; $i++) {
        if ($list_count == $job_ads_after_list_array[$job_ads_after_list_flag]) {
            $list_count = 1;
            $jobs_ads_array[] = $i;
            $job_ads_after_list_flag++;
            if ($job_ads_after_list_flag >= $job_ads_after_list_array_count) {
                $job_ads_after_list_flag = $job_ads_after_list_array_count - 1;
            }
        } else {
            $list_count++;
        }
    }
}
$paging_var = 'job_page';
$job_page = isset($_REQUEST[$paging_var]) && $_REQUEST[$paging_var] != '' ? $_REQUEST[$paging_var] : 1;
$job_ad_banners_rep = isset($atts['job_ad_banners_rep']) ? $atts['job_ad_banners_rep'] : '';
$job_per_page = isset($atts['job_per_page']) ? $atts['job_per_page'] : '-1';
$job_per_page = isset($_REQUEST['per-page']) ? $_REQUEST['per-page'] : $job_per_page;
$counter = 1;
if ($job_page >= 2) {
    $counter = (
            ($job_page - 1) *
            $job_per_page) +
        1;
}
// end ads script

$sectors_enable_switch = isset($jobsearch_plugin_options['sectors_onoff_switch']) ? $jobsearch_plugin_options['sectors_onoff_switch'] : '';

$columns_class = 'col-md-12';

$has_featured_posts = false;
if (isset($featjobs_posts) && !empty($featjobs_posts)) {
    $has_featured_posts = true;
    $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';
    ?>
    <div class="careerfy-job-listing careerfy-joblisting-sten">
        <ul class="row">
            <?php
            foreach ($featjobs_posts as $fjobs_post) {
                $job_id = $fjobs_post;
                $job_random_id = rand(1111111, 9999999);
                $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-job-medium');
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_no_image_placeholder();
                $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                $company_name = function_exists('jobsearch_job_get_company_name') ? jobsearch_job_get_company_name($job_id, '@ ') : '';
                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, 'careerfy-joblisting-sten-status careerfy-part-status', '', '', '', '', 'a', 'border_fill') : '';
                $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="careerfy-icon careerfy-filter-tool-black-shape"></i>', '</li>') : '';

                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

                $job_salary = jobsearch_job_offered_salary($job_id);

                $job_city_title = '';
                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                    $job_city_title = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city);
                }
                ?>
                <li class="<?php echo($columns_class); ?>">

                    <div class="careerfy-joblisting-sten-wrap">
                        <?php
                        echo apply_filters('jobsearch_jobs_listing_befor_urgent_html', '', $job_id, 'listin_2');
                        if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                        }
                        ?>
                        <?php if ($post_thumbnail_src != '') { ?>
                            <figure>
                                <a href="<?php echo $quick_apply_job == 'on' && !wp_is_mobile() ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"
                                   data-job-id="<?php echo esc_html($job_id); ?>"
                                   class="<?php echo($quick_apply_job_btn) ?>">
                                    <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                </a>
                            </figure>
                        <?php } ?>
                        <div class="careerfy-joblisting-sten-text">
                            <div class="careerfy-joblisting-sten-left">
                                <h2 class="<?php echo($quick_apply_job_btn) ?>"
                                    data-job-id="<?php echo esc_html($job_id); ?>">
                                    <a href="<?php echo $quick_apply_job == 'on' && !wp_is_mobile() ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"
                                       title="<?php echo get_the_title($job_id); ?>">
                                        <?php
                                        $job_title = esc_html(wp_trim_words(get_the_title($job_id), 5));
                                        echo apply_filters('jobsearch_joblisting_title_text', $job_title, $job_id, 'view2');
                                        ?>
                                    </a>
                                    <?php
                                    if ($jobsearch_job_featured == 'on') {
                                        ?>
                                        <span><i class="fa fa-star"></i><?php echo esc_html__('Featured', 'careerfy'); ?></span>
                                        <?php
                                    }
                                    ?>
                                </h2>
                                <?php
                                do_action('jobsearch_job_listingitm_after_title_con', $job_id, 'listing-10');
                                
                                ob_start();
                                ?>
                                <ul>
                                    <?php
                                    if ($job_city_title != '' && $all_location_allow == 'on') {
                                        ?>
                                        <li>
                                            <i class="careerfy-icon careerfy-map-marker"></i> <?php echo($job_city_title) ?>
                                        </li>
                                        <?php
                                    }
                                    
                                    if ($job_post_date != '' && $job_views_publish_date == 'on') {
                                        ob_start();
                                        ?>
                                        <li>
                                            <i class="careerfy-icon careerfy-calendar"></i><?php printf(esc_html__('Published %s', 'careerfy'), jobsearch_time_elapsed_string($job_post_date)); ?>
                                        </li>
                                        <?php
                                        $publishdte_html = ob_get_clean();
                                        echo apply_filters('jobsearch_job_listingitm_publish_date_html', $publishdte_html, $job_id, $job_post_date, 'listing-10');
                                    }
                                    ?>
                                </ul>
                                <?php
                                $info_ul_html = ob_get_clean();
                                echo apply_filters('jobsearch_job_listingitm_ulinfo_html', $info_ul_html, $job_id, 'listing-10');

                                do_action('jobsearch_job_listingitm_after_ulinfo_con', $job_id, 'listing-10');
                                
                                $jcus_fields = isset($job_arg['custom_fields']) ? $job_arg['custom_fields'] : '';
                                do_action('jobsearch_job_listing_custom_fields', $atts, $job_id, $jcus_fields);
                                ?>
                            </div>
                            <div class="careerfy-joblisting-sten-right">
                                <div class="careerfy-joblisting-job-types">
                                    <?php if ($job_type_str != '' && $job_types_switch != 'off') {
                                        echo($job_type_str);
                                    }
                                    ?>
                                </div>
                                <?php
                                $book_mark_args = array(
                                    'job_id' => $job_id,
                                    'before_icon' => 'careerfy-icon careerfy-heart',
                                    'after_icon' => 'careerfy-icon careerfy-heart-o',
                                    'container_class' => 'careerfy-like-list10',
                                    'anchor_class' => 'careerfy-job-like10',
                                );
                                do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                ?>
                            </div>
                            <?php
                            if (jobsearch_excerpt(0, $job_id) != '') {
                                ?>
                                <div class="jobsearch-list-excerpt">
                                    <p><?php echo jobsearch_esc_html(jobsearch_excerpt(0, $job_id)) ?></p>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>
    <?php
}
?>
<div class="careerfy-job-listing careerfy-joblisting-sten"
     id="jobsearch-job-<?php echo absint($job_short_counter) ?>">

    <ul class="row">
        <?php
        if ($job_loop_obj->have_posts()) {
            $flag_number = 1;

            $job_views_publish_date = isset($jobsearch_plugin_options['job_views_publish_date']) ? $jobsearch_plugin_options['job_views_publish_date'] : '';

            $ads_rep_counter = 1;
            foreach ($job_loop_obj->posts as $job_id) {
                global $jobsearch_member_profile;

                $job_random_id = rand(1111111, 9999999);
                $post_thumbnail_id = function_exists('jobsearch_job_get_profile_image') ? jobsearch_job_get_profile_image($job_id) : 0;
                $post_thumbnail_image = wp_get_attachment_image_src($post_thumbnail_id, 'careerfy-job-medium');
                $post_thumbnail_src = isset($post_thumbnail_image[0]) && esc_url($post_thumbnail_image[0]) != '' ? $post_thumbnail_image[0] : jobsearch_no_image_placeholder();
                $post_thumbnail_src = apply_filters('jobsearch_jobemp_image_src', $post_thumbnail_src, $job_id);
                $jobsearch_job_featured = get_post_meta($job_id, 'jobsearch_field_job_featured', true);
                $job_post_date = get_post_meta($job_id, 'jobsearch_field_job_publish_date', true);
                $company_name = function_exists('jobsearch_job_get_company_name') ? jobsearch_job_get_company_name($job_id, '@ ') : '';
                $get_job_location = get_post_meta($job_id, 'jobsearch_field_location_address', true);
                $job_type_str = function_exists('jobsearch_job_get_all_jobtypes') ? jobsearch_job_get_all_jobtypes($job_id, 'careerfy-joblisting-sten-status careerfy-part-status', '', '', '', '', 'a', 'border_fill') : '';
                $sector_str = function_exists('jobsearch_job_get_all_sectors') ? jobsearch_job_get_all_sectors($job_id, '', '', '', '<li><i class="careerfy-icon careerfy-filter-tool-black-shape"></i>', '</li>') : '';
                $job_salary = jobsearch_job_offered_salary($job_id);

                $postby_emp_id = get_post_meta($job_id, 'jobsearch_field_job_posted_by', true);

                $job_city_title = '';
                if (function_exists('jobsearch_post_city_contry_txtstr')) {
                    $job_city_title = jobsearch_post_city_contry_txtstr($job_id, $loc_view_country, $loc_view_state, $loc_view_city);
                }

                ?>
                <li class="<?php echo esc_html($columns_class); ?>">

                    <div class="careerfy-joblisting-sten-wrap">
                        <?php
                        echo apply_filters('jobsearch_jobs_listing_befor_urgent_html', '', $job_id, 'listin_2');
                        if (function_exists('jobsearch_empjobs_urgent_pkg_iconlab')) {
                            jobsearch_empjobs_urgent_pkg_iconlab($postby_emp_id, $job_id, 'job_listv1');
                        }
                        ?>
                        <?php if ($post_thumbnail_src != '') { ?>
                            <figure>
                                <a href="<?php echo $quick_apply_job == 'on' && !wp_is_mobile() ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"
                                   data-job-id="<?php echo esc_html($job_id); ?>">
                                    <img src="<?php echo esc_url($post_thumbnail_src) ?>" alt="">
                                </a>
                            </figure>
                        <?php } ?>
                        <div class="careerfy-joblisting-sten-text">
                            <div class="careerfy-joblisting-sten-left">
                                <h2 class="<?php echo($quick_apply_job_btn) ?>"
                                    data-job-id="<?php echo esc_html($job_id); ?>">
                                    <a href="<?php echo $quick_apply_job == 'on' && !wp_is_mobile() ? 'javascript:void(0)' : esc_url(get_permalink($job_id)); ?>"
                                       title="<?php echo get_the_title($job_id); ?>">
                                        <?php
                                        $job_title = esc_html(wp_trim_words(get_the_title($job_id), 5));
                                        echo apply_filters('jobsearch_joblisting_title_text', $job_title, $job_id, 'view2');
                                        ?>
                                    </a>
                                    <?php
                                    if ($jobsearch_job_featured == 'on') {
                                        ?>
                                        <span><i class="fa fa-star"></i><?php echo esc_html__('Featured', 'careerfy'); ?></span>
                                        <?php
                                    }
                                    ?>
                                </h2>
                                <?php
                                do_action('jobsearch_job_listingitm_after_title_con', $job_id, 'listing-10');
                                
                                ob_start();
                                ?>
                                <ul>
                                    <?php
                                    if ($job_city_title != '' && $all_location_allow == 'on') {
                                        ?>
                                        <li>
                                            <i class="careerfy-icon careerfy-map-marker"></i> <?php echo($job_city_title) ?>
                                        </li>
                                        <?php
                                    }
                                    
                                    if ($job_post_date != '' && $job_views_publish_date == 'on') {
                                        ob_start();
                                        ?>
                                        <li>
                                            <i class="careerfy-icon careerfy-calendar"></i><?php printf(esc_html__('Published %s', 'careerfy'), jobsearch_time_elapsed_string($job_post_date)); ?>
                                        </li>
                                        <?php
                                        $publishdte_html = ob_get_clean();
                                        echo apply_filters('jobsearch_job_listingitm_publish_date_html', $publishdte_html, $job_id, $job_post_date, 'listing-10');
                                    }
                                    ?>
                                </ul>
                                <?php
                                $info_ul_html = ob_get_clean();
                                echo apply_filters('jobsearch_job_listingitm_ulinfo_html', $info_ul_html, $job_id, 'listing-10');

                                do_action('jobsearch_job_listingitm_after_ulinfo_con', $job_id, 'listing-10');
                                
                                $jcus_fields = isset($job_arg['custom_fields']) ? $job_arg['custom_fields'] : '';
                                do_action('jobsearch_job_listing_custom_fields', $atts, $job_id, $jcus_fields);
                                ?>
                            </div>
                            <div class="careerfy-joblisting-sten-right">
                                <div class="careerfy-joblisting-job-types">
                                    <?php if ($job_type_str != '' && $job_types_switch != 'off') {
                                        echo($job_type_str);
                                    }
                                    ?>
                                </div>
                                <?php
                                $book_mark_args = array(
                                    'job_id' => $job_id,
                                    'before_icon' => 'careerfy-icon careerfy-heart',
                                    'after_icon' => 'careerfy-icon careerfy-heart-o',
                                    'container_class' => 'careerfy-like-list10',
                                    'anchor_class' => 'careerfy-job-like10',
                                );
                                do_action('jobsearch_job_shortlist_button_frontend', $book_mark_args);
                                ?>
                            </div>
                            <?php
                            if (jobsearch_excerpt(0, $job_id) != '') {
                                ?>
                                <div class="jobsearch-list-excerpt">
                                    <p><?php echo jobsearch_esc_html(jobsearch_excerpt(0, $job_id)) ?></p>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </li>
                <?php
                if ($job_ad_banners_rep == 'no') {
                    ob_start();
                    do_action('jobsearch_random_ad_banners', $atts, $job_loop_obj, $counter, 'job_listing');
                    $baner_html = ob_get_clean();
                    if ($baner_html != '' && $ads_rep_counter == 1) {
                        echo($baner_html);
                        $ads_rep_counter++;
                    }
                } else {
                    do_action('jobsearch_random_ad_banners', $atts, $job_loop_obj, $counter, 'job_listing');
                }
                $counter++;
                $flag_number++; // number variable for job
            }
            wp_reset_postdata();
        } else {
            if (!$has_featured_posts) {
                $reset_link = get_permalink(get_the_ID());
                echo '
                <li class="' . esc_html($columns_class) . '">
                    <div class="no-candidate-match-error">
                        <strong>' . esc_html__('No Record', 'careerfy') . '</strong>
                        <span>' . esc_html__('Sorry!', 'careerfy') . '&nbsp; ' . esc_html__('Does not match record with your keyword', 'careerfy') . ' </span>
                        <span>' . esc_html__('Change your filter keywords to re-submit', 'careerfy') . '</span>
                        <em>' . esc_html__('OR', 'careerfy') . '</em>
                        <a href="' . esc_url($reset_link) . '">' . esc_html__('Reset Filters', 'careerfy') . '</a>
                    </div>
                </li>';
            }
        }
        ?>
    </ul>
</div>