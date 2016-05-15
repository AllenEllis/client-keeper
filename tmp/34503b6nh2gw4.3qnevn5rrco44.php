<?php echo $this->render('header.html',$this->mime,get_defined_vars(),0); ?>
This is the project page for <b><?php echo $client['client_full']; ?>'s</b> project <b><?php echo $project['project_full']; ?></b></b>
<hr />

<?php echo $this->raw($version_summaries); ?>

<?php echo $this->render('footer.html',$this->mime,get_defined_vars(),0); ?>