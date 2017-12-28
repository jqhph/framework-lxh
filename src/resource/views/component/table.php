<table class="table" <?php echo $attributes?>>
    <thead>
    <tr><?php echo $headers?></tr>
    </thead>
    <tbody><?php echo $rows ?: $nodata; ?></tbody>
</table>