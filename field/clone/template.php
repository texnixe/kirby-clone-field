  <button type="button" class="btn btn-clone" id="<?php echo $field->id(); ?>" data-page="<?php echo $page->uid() ?>" data-parent="<?php e($page->parent()->uri() !== "", $page->parent()->uri(), 'site') ?>" data-fieldname="<?php echo $field->name(); ?>"><i class="icon fa fa-clone"></i>Clone page</button>
  <input type="text" class="input input-clone" id="<?php echo $field->id(); ?>" name="<?php echo $field->name(); ?>" value="<?php echo $field->value(); ?>" placeholder="Enter a page title and press return">
  <div class="clone-message"></div>
