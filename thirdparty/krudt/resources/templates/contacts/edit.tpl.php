<h2>Edit <?php e($contact->display_name()); ?></h2>
<?php print $viewhelper->this->html_form_tag('put', url('', array('edit'))); ?>
<?php print $viewhelper->errors($contact); ?>
<?php include('form.tpl.php'); ?>
</form>
