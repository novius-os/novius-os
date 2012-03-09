<table class="fieldset">
    <?php
    foreach ($fields as $field) {
        echo $fieldset->field($field)->build();
    }
    ?>
</table>