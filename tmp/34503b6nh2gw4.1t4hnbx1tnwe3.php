<?php echo $this->render('app/views/header.html',$this->mime,get_defined_vars(),0); ?>
This is the project page for <b><?php echo $client_full; ?>'s</b> projects</b>
<hr />

<?php echo $this->raw($project_html); ?>

<?php echo $this->render('app/views/footer.html',$this->mime,get_defined_vars(),0); ?>