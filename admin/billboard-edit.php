<?php d(maybe_unserialize(get_option(UDS_BILLBOARD_OPTION))); ?>
<div class="wrap">
	<h2>Edit uBillboard</h2>
	<form id="billboard_update_form" method="post" action="">
		<div class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
			
			</div>
			<div class="editor-wrapper">
				<div class="editor-body">
					<div id="titlediv">
						<div id="titlewrap">
							<label for="name">uBillboard Name</label>
							<input type="text" name="name" id="title" value="billboard" maxlength="255" size="40" />
						</div>
					</div>
					<div class="slides">
						<div class="postbox slide">
							<div class="handlediv" title="Click to toggle">&nbsp;</div>
							<h3 class="hndle"><span>Slide 1</span></h3>
							<div class="inside">
								aaa
							</div>
						</div>
					</div> <!-- END Slides -->
				</div> <!-- END editor body -->
			</div> <!-- END editor wrapper -->
		</div> <!-- END metabox holder -->
	</form> <!-- END billboard update form -->
</div> <!-- END Wrap -->