<table <?php echo $attributes?>>
    <thead>
    <tr>
        <?php foreach($headers as &$header) { ?>
        <th><?php echo $header;?></th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($rows as &$row) { ?>
    <tr>
        <?php foreach($row as &$item) { ?>
        <td><?php echo $item?></td>
        <?php } ?>
    </tr>
    <?php } ?>
    </tbody>
</table>