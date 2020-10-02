<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'restricted access' );
}

/*
 * This is a function that add network admin menu.
 * Menus are User Sync and Settings.
 */
if ( ! function_exists( 'wmus_add_network_admin_menu' ) ) {
    add_action( 'network_admin_menu', 'wmus_add_network_admin_menu' );
    function wmus_add_network_admin_menu() {

        add_menu_page( 'WordPress Multisite User Sync', _x( 'User Sync', 'User sync', 'gctheme' ), 'manage_options', 'wordpress-multisite-user-sync', 'wmus_wordpress_multisite_user_sync', 'dashicons-update' );
        add_submenu_page( 'wordpress-multisite-user-sync', 'User Sync: Bulk Sync', _x( 'Bulk Sync', 'User sync', 'gctheme' ), 'manage_options', 'wordpress-multisite-user-sync', 'wmus_wordpress_multisite_user_sync' );
        add_submenu_page( 'wordpress-multisite-user-sync', 'User Sync: Settings', _x( 'Settings', 'User sync', 'gctheme' ), 'manage_options', 'wmus_settings', 'wmus_settings' );

    }
}

/*
 * This is a function that add network page.
 * Also call bulk sync/unsync functionality.
 * Sync/Unsync bulk users.
 */
if ( ! function_exists( 'wmus_wordpress_multisite_user_sync' ) ) {
    function wmus_wordpress_multisite_user_sync() {

        global $wpdb;
        $current_blog_id = get_current_blog_id();
        $page_url = network_admin_url( '/admin.php?page=wordpress-multisite-user-sync' );
        $wmus_source_blog = ( isset( $_REQUEST['wmus_source_blog'] ) ? $_REQUEST['wmus_source_blog'] : '' );
        $wmus_record_per_page = ( isset( $_REQUEST['wmus_record_per_page'] ) ? $_REQUEST['wmus_record_per_page'] : 10 );
        $wmus_records = ( isset( $_REQUEST['wmus_records'] ) ? $_REQUEST['wmus_records'] : array() );
        $wmus_destination_blogs = ( isset( $_REQUEST['wmus_destination_blogs'] ) ? $_REQUEST['wmus_destination_blogs'] : array() );
        $wmus_sync_unsync = ( isset( $_REQUEST['wmus_sync_unsync'] ) ? $_REQUEST['wmus_sync_unsync'] : 1 );

        if ( $wmus_source_blog && $wmus_destination_blogs != null && $wmus_records != null && isset( $_REQUEST['wmus_submit'] ) ) {
            $blogs = $wmus_destination_blogs;
            $current_blog_id = get_current_blog_id();
            $source_blog_id = (int) $wmus_source_blog;
            foreach ( $wmus_records as $wmus_record ) {
                if ( $blogs != null ) {

                    if ( $source_blog_id != $current_blog_id ) {
                        switch_to_blog( $source_blog_id );
                    }

                    $user_info = get_userdata( $wmus_record );

                    if ( $source_blog_id != $current_blog_id ) {
                        restore_current_blog();
                    }

                    $user_id = $wmus_record;
                    $role = reset( $user_info->roles );
                    if ( ! $role ) {
                        $role = 'subscriber';
                    }

                    foreach ( $blogs as $blog ) {
                        $blog_id = $blog;
                        if ( $wmus_sync_unsync ) {
                            add_user_to_blog( $blog_id, $user_id, $role );
                        } else {
                            remove_user_from_blog( $user_id, $blog_id );
                        }
                    }
                }
            }

            ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e( 'Users successfully synced.', 'User sync', 'gctheme' ); ?></p>
                </div>
            <?php
        }


        ?>
        <div class="wrap">
            <h2><?php _e( 'Bulk Sync', 'User sync', 'gctheme' ); ?></h2>
            <hr>
                        <form method="post" action="<?php echo $page_url; ?>">
                            <table class="form-table">
                                <tbody>
                                    <tr>
                                        <th scope="row"><?php _e( 'Source Site' ); ?></th>
                                        <td>
                                            <select name="wmus_source_blog" required="required">
                                            <?php
                                                $sites = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."blogs" );
                                                $blog_list = array();
                                                if ( $sites != null ) {
                                                    ?><option value=""><?php _e( 'Select source site' ); ?></option><?php
                                                    foreach ( $sites as $key => $value ) {
                                                        $blog_list[$value->blog_id] = $value->domain;
                                                        $selected = '';
                                                        if ( $wmus_source_blog == $value->blog_id ) {
                                                            $selected = ' selected="$selected"';
                                                        }

                                                        $blog_details = get_blog_details( $value->blog_id );
                                                        ?>
                                                            <option value="<?php echo $value->blog_id; ?>"<?php echo $selected; ?>><?php echo $value->domain; echo $value->path; echo ' ('.$blog_details->blogname.')'; ?></option>
                                                        <?php
                                                    }
                                                }
                                            ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><?php _e( 'Number of users per page', 'User sync', 'gctheme' ); ?></th>
                                        <td>
                                            <input type="number" name="wmus_record_per_page" min="1" value="<?php echo $wmus_record_per_page; ?>" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <p class="submit">
                                <input name="submit" class="button button-secondary" value="<?php _e( 'Filter', 'User sync', 'gctheme' ); ?>" type="submit">
                                &nbsp;&nbsp;&nbsp;&nbsp;<a class="button button-secondary" href="<?php echo $page_url; ?>"><?php _ex( 'Clear', 'User sync', 'gctheme' ); ?></a>
                            </p>
                        </form>
                    <?php


                if ( $wmus_source_blog ) {
                    if ( $wmus_source_blog != get_current_blog_id() ) {
                        $wmus_source_blog = (int) $wmus_source_blog;
                        switch_to_blog( $wmus_source_blog );
                    }

                    ?>
                    <form method="post">
                        <p class="search-box wmus-search-box">
                            <label class="screen-reader-text" for="post-search-input"><?php _e( 'Search Users:', 'User sync', 'gctheme' ); ?></label>
                            <input id="post-search-input" name="s" value="<?php echo ( isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : ''  ); ?>" type="search">
                            <input id="search-submit" class="button" value="<?php _e( 'Search Users', 'User sync', 'gctheme' ); ?>" type="submit">
                        </p>
                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <td class="manage-column column-cb check-column"><input type="checkbox"></td>
                                    <th><?php _e( 'Title', 'User sync', 'gctheme' ); ?></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <td class="manage-column column-cb check-column"><input type="checkbox"></td>
                                    <th><?php _e( 'Title', 'User sync', 'gctheme' ); ?></th>
                                </tr>
                            </tfoot>
                            <tbody>
                            <?php
                            $paged = ( isset( $_REQUEST['paged'] ) ) ? $_REQUEST['paged'] : 1;
                            $add_args = array(
                                'wmus_source_blog'      => $wmus_source_blog,
                                'wmus_record_per_page'  => $wmus_record_per_page,
                            );

                            $args = array(
                                'number'    => $wmus_record_per_page,
                                'paged'     => $paged,
                            );

                            if ( isset( $_REQUEST['s'] ) ) {
                                $args['search'] = $_REQUEST['s'];
                                $args['search_columns'] = array(
                                    'ID',
                                    'user_login',
                                    'user_nicename',
                                    'user_email',
                                    'user_url',
                                );
                                $add_args['s'] = $_REQUEST['s'];
                            }

                            $user_query = new WP_User_Query( $args );
                            $records = $user_query->get_results();
                            if ( $records != null ) {
                                $sites = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."blogs" );
                                foreach ( $records as $record ) {
                                    ?>
                                        <tr>
                                            <th class="check-column"><input type="checkbox" name="wmus_records[]" value="<?php echo $record->ID; ?>"></th>
                                            <td class="title column-title page-title">
                                                <strong><a href="<?php echo get_edit_user_link( $record->ID ); ?>"><?php echo $record->data->display_name; ?></a></strong>
                                                <?php
                                                    if ( $sites != null ) {
                                                        $user_synced = array();
                                                        foreach ( $sites as $user_site ) {
                                                            if ( is_user_member_of_blog( $record->ID, $user_site->blog_id ) && $wmus_source_blog != $user_site->blog_id ) {
                                                                $user_synced[] = $user_site->blog_id;
                                                            }
                                                        }

                                                        if ( $user_synced != null ) {
                                                            echo '<b>'; _e( 'Synced:', 'User sync', 'gctheme' ); echo '</b>';
                                                            $count_blog_list = count( $user_synced );
                                                            $count_blog = 0;
                                                            foreach ( $user_synced as $user_synced_value ) {
                                                                $blog_details = get_blog_details( $user_synced_value );
                                                                echo $blog_list[$user_synced_value]; echo $blog_details->path; echo ' ('.$blog_details->blogname.')';
                                                                if ( $count_blog != ( $count_blog_list - 1) ) {
                                                                    echo ', ';
                                                                }
                                                                $count_blog ++;
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                    <tr class="no-items">
                                        <td class="colspanchange" colspan="2"><?php _e( 'No records found.', 'User sync', 'gctheme' ); ?></td>
                                    </tr>
                                <?php
                            }
                            $big = 999999999;
                            ?>
                            </tbody>
                        </table>
                        <div class="wmus-pagination">
                            <span class="pagination-links">
                                <?php
                                $total = ceil( $user_query->get_total() / $wmus_record_per_page );

                                $paginate_url = network_admin_url( '/admin.php?page=wordpress-multisite-user-sync&paged=%#%' );
                                echo paginate_links( array(
                                    'base'      => str_replace( $big, '%#%', $paginate_url ),
                                    'format'    => '?paged=%#%',
                                    'current'   => max( 1, $paged ),
                                    'total'     => $total,
                                    'add_args'  => $add_args,
                                    'prev_text' => __( '&laquo;' ),
                                    'next_text' => __( '&raquo;' ),
                                ) );
                                ?>
                            </span>
                        </div>
                        <br class="clear">
                        <input type="hidden" name="wmus_source_blog" value="<?php echo $wmus_source_blog; ?>">
                        <input type="hidden" name="wmus_record_per_page" value="<?php echo $wmus_record_per_page; ?>">
                        <?php wp_reset_postdata(); ?>
                        <table class="form-table">
                            <tbody>
                                 <tr>
                                    <th><label><?php _e( 'Sync/Unsync?', 'User sync', 'gctheme' ); ?></label></th>
                                    <td>
                                        <fieldset>
                                            <label>
                                                <input type="radio" name="wmus_sync_unsync" value="1" checked="checked" /><?php _e( 'Sync', 'User sync', 'gctheme' ); ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="wmus_sync_unsync" value="0" /><?php _e( 'Unsync', 'User sync', 'gctheme' ); ?>
                                            </label>
                                        </fieldset>
                                        <p class="description"><?php _e( 'Select sync/unsync.', 'User sync', 'gctheme' ); ?></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e( 'Destination Sites', 'User sync', 'gctheme' ); ?></th>
                                    <td>
                                        <label><input class="wmus-check-uncheck" type="checkbox" /><?php _e( 'All', 'User sync', 'gctheme' ); ?></label>
                                        <p class="description"><?php _e( 'Select/Deselect all sites.', 'User sync', 'gctheme' ); ?></p>
                                        <br>
                                        <fieldset class="wmus-sites">
                                            <?php
                                                if ( $sites != null ) {
                                                    foreach ( $sites as $key => $value ) {
                                                        if ( $wmus_source_blog != $value->blog_id ) {
                                                            $blog_details = get_blog_details( $value->blog_id );
                                                            ?>
                                                                <label><input name="wmus_destination_blogs[]" type="checkbox" value="<?php echo $value->blog_id; ?>"><?php echo $value->domain; echo $value->path; echo ' ('.$blog_details->blogname.')'; ?></label><br>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            ?>
                                        </fieldset>
                                        <p class="description"><?php _e( 'Select destination sites you want to sync/unsync.', 'User sync', 'gctheme' ); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit"><input name="wmus_submit" class="button button-primary" value="<?php _e( 'Sync/Unsync', 'User sync', 'gctheme' ); ?>" type="submit"></p>
                    </form>
                    <?php
                    if ( $wmus_source_blog != get_current_blog_id() ) {
                        restore_current_blog();
                    }
                }
            ?>
        </div>
        <?php
    }
}

/*
 * This is a function that call plugin settings.
 * User Roles: Set specific user role to access copier.
 * Sites: Set specif site to manage copier.
 * Auto Sync: Auto users sync/unsync.
 */
if ( ! function_exists( 'wmus_settings' ) ) {
    function wmus_settings() {

        global $wpdb;
        if ( isset( $_REQUEST['wmus_submit'] ) ) {
            $request = $_REQUEST;
            unset( $request['page'] );
            unset( $request['wmus_submit'] );
            if ( $request != null ) {
                foreach ( $request as $key => $value ) {
                    update_site_option( $key, $value );
                }
            }

            ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e( 'Settings saved.', 'User sync', 'gctheme' ); ?></p>
                </div>
            <?php
        }

        $sync_type = get_site_option( 'wmus_auto_sync' );
        if ( $sync_type == '1' ) {
            $sync_type = 'auto';
        } else if ( $sync_type == '0' ) {
            $sync_type = 'manual';
        } else {
            //
        }

        $wmus_auto_unsync = get_site_option( 'wmus_auto_unsync' );
        $wmus_auto_sync_type = get_site_option( 'wmus_auto_sync_type' );
        $wmus_auto_sync_main_blog = get_site_option( 'wmus_auto_sync_main_blog' );
        $wmus_auto_sync_sub_blogs = get_site_option( 'wmus_auto_sync_sub_blogs' );
        if ( ! $wmus_auto_sync_sub_blogs || $wmus_auto_sync_sub_blogs == null ) {
            $wmus_auto_sync_sub_blogs = array();
        }
        $wmus_user_roles = get_site_option( 'wmus_user_roles' );


        ?>
            <div class="wrap">
                <h2><?php _e( 'Settings', 'User sync', 'gctheme' ); ?></h2>
                <hr>
                       <form method="post">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row"><?php _e( 'Sync Type', 'User sync', 'gctheme' ); ?></th>
                                    <td>
                                        <fieldset>
                                            <label><input type="radio" name="wmus_auto_sync" value="auto"<?php echo ( $sync_type == 'auto' ? ' checked="checked"' : '' ); ?> /> <?php _e( 'Auto Sync', 'User sync', 'gctheme' ); ?></label><br>
                                            <label><input type="radio" name="wmus_auto_sync" value="manual"<?php echo ( $sync_type == 'manual' ? ' checked="checked"' : '' ); ?> /> <?php _e( 'Manual Sync', 'User sync', 'gctheme' ); ?></label>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label><?php _e( 'Auto Sync Type', 'User sync', 'gctheme' ); ?></label></th>
                                    <td>
                                        <fieldset>
                                            <label>
                                                <input type="radio" name="wmus_auto_sync_type" value="all-sites"<?php echo ( $wmus_auto_sync_type == 'all-sites' ? ' checked="checked"' : '' ); ?> /><?php _e( 'All sites', 'User sync', 'gctheme' ); ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="wmus_auto_sync_type" value="main-site-to-sub-sites"<?php echo ( $wmus_auto_sync_type == 'main-site-to-sub-sites' ? ' checked="checked"' : '' ); ?> /><?php _e( 'Main site to sub sites', 'User sync', 'gctheme' ); ?>
                                            </label>
                                            <label>
                                                <input type="radio" name="wmus_auto_sync_type" value="sub-sites-to-main-site"<?php echo ( $wmus_auto_sync_type == 'sub-sites-to-main-site' ? ' checked="checked"' : '' ); ?> /><?php _e( 'Sub site to main site', 'User sync', 'gctheme' ); ?>
                                            </label>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr class="wmus-hide-show"<?php echo ( $wmus_auto_sync_type == 'sub-sites-to-main-site' || $wmus_auto_sync_type == 'all-sites' ? ' style="display:none"' : '' );?>>
                                    <th scope="row"></th>
                                    <td>
                                        <?php _e( 'Sub Sites', 'User sync', 'gctheme' ); ?><br><br>
                                        <label><input class="wmus-check-uncheck" type="checkbox" /><?php _e( 'All', 'User sync', 'gctheme' ); ?></label>
                                        <p class="description"><?php _e( 'Select/Deselect all sites.', 'User sync', 'gctheme' ); ?></p>
                                        <br>
                                        <fieldset class="wmus-sites">
                                            <input type="hidden" name="wmus_auto_sync_sub_blogs" value="0" />
                                            <?php
                                                $sites = $wpdb->get_results( "SELECT * FROM ".$wpdb->base_prefix."blogs" );
                                                if ( $sites != null ) {
                                                    foreach ( $sites as $key => $value ) {
                                                        if ( ! is_main_site( $value->blog_id ) ) {
                                                            $blog_details = get_blog_details( $value->blog_id );
                                                            ?>
                                                                <label><input name="wmus_auto_sync_sub_blogs[]" type="checkbox" value="<?php echo $value->blog_id; ?>"<?php echo ( in_array( $value->blog_id, $wmus_auto_sync_sub_blogs ) ? ' checked="checked"' : '' ); ?>><?php echo $value->domain; echo $value->path; echo ' ('.$blog_details->blogname.')'; ?></label><br>
                                                            <?php
                                                        } else {
                                                            ?><input type="hidden" name="wmus_auto_sync_main_blog" value="<?php echo $value->blog_id; ?>"/><?php
                                                        }
                                                    }
                                                }
                                            ?>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><?php _e( 'Auto Unsync?', 'User sync', 'gctheme' ); ?></th>
                                    <td>
                                        <input type="hidden" name="wmus_auto_unsync" value="0" />
                                        <input type="checkbox" name="wmus_auto_unsync" value="1"<?php echo ( $wmus_auto_unsync ? ' checked="checked"' : '' ); ?> />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="submit"><input name="wmus_submit" class="button button-primary" value="<?php _e( 'Save Changes', 'User sync', 'gctheme' ); ?>" type="submit"></p>
                        </form>
                    <?php

                ?>
            </div>
        <?php
    }
}
