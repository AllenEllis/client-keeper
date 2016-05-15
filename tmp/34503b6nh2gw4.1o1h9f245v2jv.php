<h5>Version Summary</h5>
<li>Version ID: <?php echo $version['version_id']; ?></li>
<li>Project ID: <?php echo $version['project_id']; ?></li>
<li>Version: <?php echo $version['version']; ?></li>
<li>Datetime: <?php echo $version['datetime']; ?></li>
<li>Thumbnail: <?php echo $version['thumb']; ?></li>
<li>Files:

    <?php foreach (($files?:array()) as $file=>$val): ?>
        <ul>
        <li>File ID: <?php echo $val['file_id']; ?></li>
        <li>Version ID: <?php echo $val['version_id']; ?></li>
        <li>Quality: <?php echo $val['quality']; ?></li>
        <li>Complete: <?php echo $val['complete']; ?></li>
        <li>Is master: <?php echo $val['is_master']; ?></li>
        <li>Path: <?php echo $val['path']; ?></li>
        </ul>
    <?php endforeach; ?>

</li>