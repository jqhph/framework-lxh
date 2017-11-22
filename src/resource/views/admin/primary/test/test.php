<h1><?php echo $title?></h1>
<div>
    <?php foreach ($list as & $v): ?>
        <p><?php echo $v; ?></p>
    <?php endforeach;?>
</div>