<button type="button" class="btn btn-duplicate" id="<?php echo $field->id(); ?>" data-fieldname="<?php echo $field->name(); ?>"><i class="icon fa fa-clone"></i><?php e($buttontext, $buttontext, 'Clone page') ?></button>
<input type="text" class="input input-duplicate" id="<?php echo $field->id(); ?>" name="<?php echo $field->name(); ?>" value="<?php echo $field->value(); ?>" placeholder="<?php e($placeholder, $placeholder, 'Enter page title and press Enter …') ?>">
<div class="message-duplicate"></div>
