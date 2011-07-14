<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php do_action('admin_xml_ns'); ?> <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php echo get_option('blog_charset'); ?>" />
	<title>uBillboard Preview</title>
	<script type="text/javascript">
	//<![CDATA[
	var ajaxurl = '<?php echo admin_url() ?>admin-ajax.php',
		pagenow = 'ubillboard_page_uds_billboard_edit',
		typenow = '',
		adminpage = 'ubillboard_page_uds_billboard_edit',
		thousandsSeparator = ',',
		decimalPoint = '.',
		isRtl = 0; 
	//]]>
	</script>
	<?php wp_head() ?>
</head>
<body>
	<div id="preview-wrapper">
		<?php the_uds_billboard('_uds_temp_billboard') ?>
	</div>
	<?php wp_footer() ?>
	<script language="JavaScript" type="text/javascript">
		jQuery(document).ready(function($){
			jQuery('#preview-wrapper').css({
				width: jQuery('#uds-bb-0').css('width')
			});
			var resize = function(){
				if(jQuery('#TB_window').hasClass('uds-preview')) {
					var correctionX = 30,
						correctionY = 40;
					
					if($('#uds-billboard-thumbnails-inside').is(':not(:checked)')) {
						if($('#uds-billboard-thumbnails-position').val() == 'left' || $('#uds-billboard-thumbnails-position').val() == 'right') {
							correctionX = 200;
						} else {
							correctionY = 200;
						}
					}
					
					jQuery('#TB_window').css({
						width: parseInt(jQuery('#uds-bb-0').css('width'),10) + correctionX + 'px',
						height: parseInt(jQuery('#uds-bb-0').css('height'),10) + correctionY + 'px',
						marginLeft: -1000 / 2 + 'px'
					});
				}
			};
			resize();
			jQuery(window).resize(resize);
		});
	</script>
</body>
</html>
<?php exit; ?>