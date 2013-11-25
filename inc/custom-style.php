<?php
if(!function_exists('get_post_templates')) {
function get_post_templates() {
$themes = get_themes();
$theme = get_current_theme();
$templates = $themes[$theme]['Template Files'];
$post_templates = array();
$base = array(trailingslashit(get_template_directory()), trailingslashit(get_stylesheet_directory()));
foreach ((array)$templates as $template) {
$template = WP_CONTENT_DIR . str_replace(WP_CONTENT_DIR, '', $template); 
$basename = str_replace($base, '', $template);
if (false !== strpos($basename, '/'))
continue;
$template_data = implode('', file( $template ));
$name = '';
if (preg_match( '|Style : (.*)$|mi', $template_data, $name))
$name = _cleanup_header_comment($name[1]);
if (!empty($name)) {
if(basename($template) != basename(__FILE__))
$post_templates[trim($name)] = $basename;}
}return $post_templates;}}

if(!function_exists('post_templates_dropdown')) {
function post_templates_dropdown() {
global $post;
$post_templates = get_post_templates();
foreach ($post_templates as $template_name => $template_file) { 
if ($template_file == get_post_meta($post->ID, '_wp_post_template', true)) { $selected = ' selected="selected"'; } else { $selected = ''; }
$opt = '<option value="' . $template_file . '"' . $selected . '>' . $template_name . '</option>';
echo $opt;}}}
add_filter('single_template', 'get_post_template');
if(!function_exists('get_post_template')) {
function get_post_template($template) {
global $post;
$custom_field = get_post_meta($post->ID, '_wp_post_template', true);
if(!empty($custom_field) && file_exists(get_template_directory() . "/{$custom_field}")) { 
$template = get_template_directory() . "/{$custom_field}"; }
return $template;}}
add_action('admin_menu', 'pt_add_custom_box');
function pt_add_custom_box() {
if(get_post_templates() && function_exists( 'add_meta_box' )) {
add_meta_box( 'pt_post_templates', __( 'Style Replacement', 'pt' ), 
'pt_inner_custom_box', 'post', 'normal', 'high' ); }}
function pt_inner_custom_box() {
global $post;
echo '<input type="hidden" name="pt_noncename" id="pt_noncename" value="' . wp_create_nonce( basename(__FILE__) ) . '" />';
echo '<p>' . __("Note : Change the style of your posts with combo box options. You can also implement a blogazine with a different style in every post. You can choose the style you've created. You must create a new template with a different style. Insert the following code at the very top of the file :<br /><br /><code>&lt;?php<br />/*<br />Style : [Your Template Name]<br />*/<br />?&gt;</code><br /><br />Download sample template from <a href=\"http://www.nurulimam.com/plugin-style-replacement/\">Blogazine Template Collections</a>", 'pt' ) . '</p>';
echo '<label class="hidden" for="post_template">' . __("Post Template", 'pt' ) . '</label><br />';
echo '<select name="_wp_post_template" id="post_template" class="dropdown">';
echo '<option value="">Default Style</option>';
post_templates_dropdown();
echo '</select><br /><br />';}
add_action('save_post', 'pt_save_postdata', 1, 2); 
function pt_save_postdata($post_id, $post) {
if ( !wp_verify_nonce( $_POST['pt_noncename'], basename(__FILE__) )) {
return $post->ID;}
if ( 'page' == $_POST['post_type'] ) {
if ( !current_user_can( 'edit_page', $post->ID ))
return $post->ID;
} else {
if ( !current_user_can( 'edit_post', $post->ID ))
return $post->ID;}
$mydata['_wp_post_template'] = $_POST['_wp_post_template'];
foreach ($mydata as $key => $value) { 
if( $post->post_type == 'revision' ) return; 
$value = implode(',', (array)$value); 
if(get_post_meta($post->ID, $key, FALSE)) { 
update_post_meta($post->ID, $key, $value); 
} else { 
add_post_meta($post->ID, $key, $value);}
if(!$value) delete_post_meta($post->ID, $key);}}
?>
<?php
function custom_style_post_sheets() {
global $post;
if (is_single() ) {
$file = '/style-'.$post->ID.'.css';
$web = get_template_directory_uri().$file;
if ( file_exists(get_template_directory().$file) )
echo "<link rel='stylesheet' type=text/css' href='$web' media='screen' />"."\n";}}
add_action('wp_head', 'custom_style_post_sheets');
ob_start('blank_save');
function blank_save($artd_buffer) {
global $single_styles;
$data = "\n".$single_styles;
$artd_buffer = str_replace('</head>', $data."\n</head>", $artd_buffer);
return $artd_buffer;}
add_action('the_content', 'blank_inline');
function blank_inline($data) {
global $post, $single_styles;
if(is_single() or is_page()) 
$single_styles .= str_replace( '#postid', $post->ID, get_post_meta($post->ID, 'blank_custom_single', true) )."\n";
return $data;}
add_action('publish_page','blank_save_postdata');
add_action('publish_post','blank_save_postdata');
add_action('save_post','blank_save_postdata');
add_action('edit_post','blank_save_postdata');
function blank_save_postdata( $post_id ) {
if ( !wp_verify_nonce( $_POST['blank-custom-nonce'], basename(__FILE__) ) )
return $post_id;
if ( 'page' == $_POST['post_type'] ) {
if ( !current_user_can( 'edit_page', $post_id ) )
return $post_id;
} else {
if ( !current_user_can( 'edit_post', $post_id ) )
return $post_id;}
delete_post_meta( $post_id, 'blank_custom_single' );
if(trim($_POST['custom-single']) != '')
add_post_meta( $post_id, 'blank_custom_single', stripslashes($_POST['custom-single']) );
return true;}
add_action('admin_menu', 'blank_add_meta_box');
add_action('admin_head', 'blank_admin_head');
function blank_admin_head() { ?>	
<style type="text/css">	.clear { clear: both; }#custom-single {width: 100%;height: 500px;font-family:"Courier New", Courier, monospace;font-size:10px;}.box {width:100%;}.blank-submit {clear: both;}
</style>
<?php }
function blank_add_meta_box() {
if( function_exists( 'add_meta_box' ) ) {
if( current_user_can('edit_posts') )
add_meta_box( 'kotak', __( 'Custom Style, Script, & Meta Tags', 'blank-custom' ), 
'blank_meta_box', 'post', 'normal' );
if( current_user_can('edit_pages') )
add_meta_box( 'kotak', __( 'Custom Style, Script, & Meta Tags', 'blank-custom' ), 
'blank_meta_box', 'page', 'normal' );}}
function blank_meta_box() {
global $post; ?>
<form action="blank-custom_submit" method="get" accept-charset="utf-8">
<?php echo '<input type="hidden" name="blank-custom-nonce" id="blank-custom-nonce" value="' . wp_create_nonce(basename(__FILE__) ) . '" />'; ?>
<script type="text/javascript" charset="utf-8">
/* <![CDATA[ */
jQuery(document).ready(function() {
jQuery('#kotak textarea').focus(function() {
jQuery('#location').attr('class', this.id);
var location = jQuery('#location').attr('class');
});
jQuery('#insert-style').click(function() {
var location = jQuery('#location').attr('class');
edInsertContent(location, '<' + 'style type="text/css"'+'>'+"\n\n"+'<'+'/style'+'>');
});
jQuery('#insert-script').click(function() {
var location = jQuery('#location').attr('class');
edInsertContent(location, '<'+'script type="text/javascript"'+'>'+"\n\n"+'<'+'/script'+'>');
});
jQuery('#meta-desc').click(function() {
var location = jQuery('#location').attr('class');
edInsertContent(location, '<'+'meta name="description" content="Insert your meta description" />');
});
jQuery('#meta-key').click(function() {
var location = jQuery('#location').attr('class');
edInsertContent(location, '<'+'meta name="keywords" content="Insert your keywords separated by comma" />');
});
function edInsertContent(which, myValue) {
myField = document.getElementById(which);
if (document.selection) {
myField.focus();
sel = document.selection.createRange();
sel.text = myValue;
myField.focus();
}
else if (myField.selectionStart || myField.selectionStart == '0') {
var startPos = myField.selectionStart;
var endPos = myField.selectionEnd;
var scrollTop = myField.scrollTop;
myField.value = myField.value.substring(0, startPos)
+ myValue 
+ myField.value.substring(endPos, myField.value.length);
myField.focus();
myField.selectionStart = startPos + myValue.length;
myField.selectionEnd = startPos + myValue.length;
myField.scrollTop = scrollTop;
} else {
myField.value += myValue;
myField.focus();}}
});
/* ]]> */
</script>
<p>Note : You can insert CSS, JavaScript, Favicon, Meta desription &amp; keywords here.<br /><br />Example CSS : <code>&lt;link rel='stylesheet' href='http://url-css-here/style.css' type='text/css' media='all' /&gt;</code><br /><br />Example JS : <code>&lt;script type='text/javascript' src='http://url-js-here/style.css'&gt;&lt;/script&gt;</code><br /><br />Example Meta Description : <code>&lt;meta name="description" content="Insert Description Here" /&gt;</code><br /><br />
Example Meta Keywords : <code>&lt;meta name="keywords" content="Insert Keywords Here" /&gt;</code></p>
<input type="hidden" name="location" value="" id="location" />
<p><input type="button" name="insert-style" class="button" value="Insert CSS" id="insert-style" /> 
<input type="button" name="insert-script" class="button" value="Insert JavaScript" id="insert-script" />
<input type="button" name="meta-desc" class="button" value="Insert Meta Description" id="meta-desc" />
<input type="button" name="meta-key" class="button" value="Insert Meta Keywords" id="meta-key" /></p>
<div class="box"><textarea id="custom-single" name="custom-single" rows="10" cols="40"><?php echo esc_attr( get_post_meta( $post->ID,'blank_custom_single', true ) ); ?></textarea></div><div class="clear"></div></form>
<?php }?>