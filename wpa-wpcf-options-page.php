<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div id="ms-options" class="wrap options-page">
	<h2><?php echo esc_html( $title ); ?></h2>

<div id="notifications">
<?php if ( isset($_GET['message']) && isset($messages[$_GET['message']]) ) { ?>
<div id="message" class="updated fade"><p><?php echo esc_html( $messages[$_GET['message']] ); ?></p></div>
<?php } ?>
<?php if ( isset($_GET['error']) && isset($errors[$_GET['error']]) ) { ?>
<div id="message" class="error fade"><p><?php echo esc_html( $errors[$_GET['error']] ); ?></p></div>
<?php } ?>
</div><!-- /notifications -->

<div class="options-wrap">
<form method="post">

<?php foreach ($options as $field) {
switch ( $field['type'] ) {

	case 'section': ?>

<div class="section-title-wrap">
	<h3 id="<?php echo sanitize_title( $field['name'] ); ?>" class="section-title"><?php echo esc_html( $field['label'] ); ?></h3>
</div>

<?php break;
	case 'open': ?>
<!-- Section [Start] -->
<div class="settings-wrap">

<?php break;
	case 'close': ?>

<div class="submit-wrap">
	<?php submit_button( 'Save Changes', 'primary', 'save', false ); ?>
</div>

</div><!-- settings-wrap -->
<!-- Section [End] -->
<?php break;

	case 'paragraph': ?>

<div class="ms-options-paragraph clearfix">
	<?php echo wp_kses_post( $field['desc'] ); ?>
</div>

<?php
break;

	case 'text': ?>

<div class="ms-options-input ms-options-text clearfix">
	<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['name'] ); ?></label>
 	<input name="<?php echo esc_attr( $field['id'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" type="<?php echo esc_attr( $field['type'] ); ?>" value="<?php if ( isset($current[ $field['id'] ]) && $current[ $field['id'] ] != "") { echo esc_html(stripslashes( $current[ $field['id'] ] ) ); } ?>" />
</div>

<?php
break;

	case 'number': ?>

<div class="ms-options-input ms-options-text clearfix">
	<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_attr( $field['name'] ); ?></label>
 	<input name="<?php echo esc_attr( $field['id'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" type="number" value="<?php if ( isset($current[ $field['id'] ]) && $current[ $field['id'] ] != "") { echo esc_html(stripslashes($current[ $field['id'] ] ) ); } ?>" />
</div>

<?php
break;

case 'textarea':
?>

<div class="ms-options-input ms-options-textarea clearfix">
	<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_attr( $field['name'] ); ?></label>
 	<textarea name="<?php echo esc_attr( $field['id'] ); ?>" type="<?php echo esc_attr( $field['type'] ); ?>" cols="" rows=""><?php if ( $current[ $field['id'] ] != "" ) { echo esc_html( $current[ $field['id'] ] ); } else { echo esc_html( $field['std'] ); } ?></textarea>

 </div>

<?php
break;

case 'select':
?>

<div class="ms-options-input ms-options-select clearfix">
	<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_attr( $field['name'] ); ?></label>

<select name="<?php echo esc_attr( $field['id'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>">
<?php foreach ( $field['options'] as $key=>$name ) { ?>
		<option <?php if ( isset($current[ $field['id'] ]) && $current[ $field['id'] ] == $key) { echo 'selected="selected"'; } ?> value="<?php echo esc_attr( $key );?>"><?php echo esc_html( $name ); ?></option><?php } ?>
</select>

</div>
<?php
break;

case "checkbox":
?>

<div class="ms-options-input ms-options-checkbox clearfix">
	<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_attr(  $field['name'] ); ?></label>

	<input type="checkbox" name="<?php echo esc_attr( $field['id'] ); ?>" id="<?php echo esc_attr( $field['id'] ); ?>" value="on" <?php checked($current[ $field['id'] ], "on") ?> />

 </div>

<?php break;

case 'picker':
?>
	<div id="picker"></div>

<?php break;

}
}
?>

<input type="hidden" name="action" value="save" />
</form>

<div class="danger">
<h3 class="inline">Danger Zone</h3><small>This will remove all your saved settings.</small>
<form method="post">
<?php submit_button( 'Reset Options', 'secondary', 'reset', false ); ?> 
<input type="hidden" name="action" value="reset" /> 
</form>
</div>

</div><!-- /options-wrap -->

<?php if( WP_DEBUG ){ ?>
<div class="debug">
  <h3>Debug information</h3>
  <p>You are seeing this because your <code>WP_DEBUG</code> variable is set to true.</p>
  <pre><?php print_r( $current ) ?></pre>
</div><!-- /debug-info -->
<?php } ?>

</div> <!-- /wrap options-page -->
