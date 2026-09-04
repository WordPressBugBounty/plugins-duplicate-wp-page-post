<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$opt = get_option( 'dpp_wpp_page_options', array() );
if ( ! is_array( $opt ) ) {
	$opt = array();
}

$defaults = array(
	'dpp_post_status'     => 'draft',
	'dpp_post_redirect'   => 'to_list',
	'dpp_post_suffix'     => '',
	'dpp_posteditor'      => 'classic',
	'dpp_post_link_title' => '',
);
$opt = wp_parse_args( $opt, $defaults );

$instruct = isset( $_GET['instruct'] ) ? absint( $_GET['instruct'] ) : 0;
$settings_saved = false;

if ( isset( $_POST['submit_dpp_wpp_page'] ) ) {
	$nonce = isset( $_POST['dpp_nonce_field'] ) ? sanitize_text_field( wp_unslash( $_POST['dpp_nonce_field'] ) ) : '';

	if ( ! wp_verify_nonce( $nonce, 'dpp_page_action' ) ) {
		wp_die( esc_html__( 'Security check failed. Please try again.', 'dpp_wpp_page' ) );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to change these settings.', 'dpp_wpp_page' ) );
	}

	$allowed_statuses = array( 'draft', 'publish', 'private', 'pending' );
	$post_status      = isset( $_POST['dpp_post_status'] ) ? sanitize_key( wp_unslash( $_POST['dpp_post_status'] ) ) : $defaults['dpp_post_status'];
	$post_redirect    = isset( $_POST['dpp_post_redirect'] ) ? sanitize_key( wp_unslash( $_POST['dpp_post_redirect'] ) ) : $defaults['dpp_post_redirect'];
	$post_editor      = isset( $_POST['dpp_posteditor'] ) ? sanitize_key( wp_unslash( $_POST['dpp_posteditor'] ) ) : $defaults['dpp_posteditor'];
	$post_suffix      = isset( $_POST['dpp_post_suffix'] ) ? sanitize_text_field( wp_unslash( $_POST['dpp_post_suffix'] ) ) : $opt['dpp_post_suffix'];
	$link_title       = isset( $_POST['dpp_post_link_title'] ) ? sanitize_text_field( wp_unslash( $_POST['dpp_post_link_title'] ) ) : $opt['dpp_post_link_title'];

	if ( ! in_array( $post_status, $allowed_statuses, true ) ) {
		$post_status = $defaults['dpp_post_status'];
	}
	if ( ! in_array( $post_redirect, array( 'to_list', 'to_page' ), true ) ) {
		$post_redirect = $defaults['dpp_post_redirect'];
	}
	if ( ! in_array( $post_editor, array( 'classic', 'gutenberg' ), true ) ) {
		$post_editor = $defaults['dpp_posteditor'];
	}

	/* Update only known settings; preserve every existing/unknown option. */
	$opt['dpp_post_status']     = $post_status;
	$opt['dpp_post_redirect']   = $post_redirect;
	$opt['dpp_posteditor']      = $post_editor;
	$opt['dpp_post_suffix']     = $post_suffix;
	$opt['dpp_post_link_title'] = $link_title;

	update_option( 'dpp_wpp_page_options', $opt );
	$settings_saved = true;
	$instruct = 1;
}
?>
<div class="wrap dpp_page_settings">
	<h1><?php esc_html_e( 'Plugin Settings', 'dpp_wpp_page' ); ?></h1>
	<?php if ( $settings_saved || 1 === $instruct ) : ?>
		<div id="message" class="updated notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Changes Saved!', 'dpp_wpp_page' ); ?></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Ignore this notice.', 'dpp_wpp_page' ); ?></span></button>
		</div>
	<?php elseif ( 2 === $instruct ) : ?>
		<div id="message" class="error notice notice-error is-dismissible">
			<p><?php esc_html_e( 'Changes not saved!', 'dpp_wpp_page' ); ?></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Ignore this notice.', 'dpp_wpp_page' ); ?></span></button>
		</div>
	<?php endif; ?>
	<div id="dpp-stuff">
		<div id="dpp-post-body" class="metabox-holder columns-2">
			<div id="dpp-post-body-content" style="position: relative;">
				<form style="padding: 10px; border: 1px solid #333;" action="" method="post" name="dpp_wpp_page_form">
					<?php wp_nonce_field( 'dpp_page_action', 'dpp_nonce_field' ); ?>
					<table class="form-table">
						<tbody>
						<tr>
							<th scope="row"><label for="dpp_posteditor">Select Editor<br><em><?php esc_html_e( 'Default: Classic Editor', 'dpp_wpp_page' ); ?></em></label></th>
							<td>
								<select id="dpp_posteditor" name="dpp_posteditor">
									<option value="classic" <?php selected( $opt['dpp_posteditor'], 'classic' ); ?>><?php esc_html_e( 'Classic Editor', 'dpp_wpp_page' ); ?></option>
									<option value="gutenberg" <?php selected( $opt['dpp_posteditor'], 'gutenberg' ); ?>><?php esc_html_e( 'Gutenberg Editor', 'dpp_wpp_page' ); ?></option>
								</select>
								<p><?php esc_html_e( 'Please select which editor you are using.', 'dpp_wpp_page' ); ?><br><?php esc_html_e( 'If you are using Gutenberg, select Gutenberg editor otherwise it will not show Duplicate button on edit screen.', 'dpp_wpp_page' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="dpp_post_status">Post Status<br><em><?php esc_html_e( 'Default: Draft', 'dpp_wpp_page' ); ?></em></label></th>
							<td>
								<select id="dpp_post_status" name="dpp_post_status">
									<option value="draft" <?php selected( $opt['dpp_post_status'], 'draft' ); ?>><?php esc_html_e( 'Draft', 'dpp_wpp_page' ); ?></option>
									<option value="publish" <?php selected( $opt['dpp_post_status'], 'publish' ); ?>><?php esc_html_e( 'Publish', 'dpp_wpp_page' ); ?></option>
									<option value="private" <?php selected( $opt['dpp_post_status'], 'private' ); ?>><?php esc_html_e( 'Private', 'dpp_wpp_page' ); ?></option>
									<option value="pending" <?php selected( $opt['dpp_post_status'], 'pending' ); ?>><?php esc_html_e( 'Pending', 'dpp_wpp_page' ); ?></option>
								</select>
								<p><?php esc_html_e( 'Please select any post status you want to assign for duplicate post.', 'dpp_wpp_page' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="dpp_post_redirect">Redirect<br><em><?php esc_html_e( 'Default: To current list.', 'dpp_wpp_page' ); ?></em><br>(<?php esc_html_e( 'After click on', 'dpp_wpp_page' ); ?> <strong><?php esc_html_e( 'Duplicate', 'dpp_wpp_page' ); ?></strong>)</label></th>
							<td>
								<select id="dpp_post_redirect" name="dpp_post_redirect">
									<option value="to_list" <?php selected( $opt['dpp_post_redirect'], 'to_list' ); ?>><?php esc_html_e( 'All Post List', 'dpp_wpp_page' ); ?></option>
									<option value="to_page" <?php selected( $opt['dpp_post_redirect'], 'to_page' ); ?>><?php esc_html_e( 'Direct Edit', 'dpp_wpp_page' ); ?></option>
								</select>
								<p><?php esc_html_e( 'Please select the page to redirect to after clicking duplicate.', 'dpp_wpp_page' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="dpp_post_suffix">Duplicate Post Suffix<br><em><?php esc_html_e( 'Default: Empty', 'dpp_wpp_page' ); ?></em></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php echo esc_attr( $opt['dpp_post_suffix'] ); ?>" id="dpp_post_suffix" name="dpp_post_suffix">
								<p><?php esc_html_e( 'Add a suffix for duplicate page and post. It will show after title.', 'dpp_wpp_page' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="dpp_post_link_title">Duplicate Link Text<br><em><?php esc_html_e( 'Default: Duplicate', 'dpp_wpp_page' ); ?></em></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php echo esc_attr( $opt['dpp_post_link_title'] ); ?>" id="dpp_post_link_title" name="dpp_post_link_title">
								<p><?php esc_html_e( 'It will show above text on duplicate page/post link button instead of default (Duplicate).', 'dpp_wpp_page' ); ?></p>
							</td>
						</tr>
						</tbody>
					</table>
					<p class="submit"><input type="submit" value="<?php echo esc_attr__( 'Save Settings', 'dpp_wpp_page' ); ?>" class="button button-primary" id="submit" name="submit_dpp_wpp_page"></p>
				</form>
			</div>
		</div>
		<div>
			<h3><a href="https://wordpress.org/support/plugin/duplicate-wp-page-post/reviews/?filter=5#new-post"><?php esc_html_e( 'Please review us if you like the plugin.', 'dpp_wpp_page' ); ?></a></h3>
		</div>
	</div>
</div>