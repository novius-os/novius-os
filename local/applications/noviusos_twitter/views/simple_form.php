<div id="<?= $uniqid = uniqid('temp_') ?>">
<?php

$nugget_intent  = $item->get_catcher_nuggets($catcher_name)->content_data;
$nugget_default = $item->get_default_nuggets();

echo \View::forge('nos::admin/data_catcher/form', array(
    'action' => 'admin/noviusos_twitter/share/save',
    'item' => $item,
    'catcher_name' => $catcher_name,
    // The plus operator allow a merge without reindexing
    'nugget' => $nugget_intent + $nugget_default,
    'nugget_db' => $nugget_intent,
    'filter' => array(
        \Nos\DataCatcher::TYPE_TITLE,
        \Nos\DataCatcher::TYPE_URL,
    ),
));

?>
</div>

<button id="<?= $uniqid_tweet = uniqid('tweet_') ?>" style="display: none;" data-icon-url="static/apps/noviusos_twitter/img/twitter.png">
    <a href="#" onclick="return false;"><?= __('Tweet') ?></a>
</button>

<script type="text/javascript">
require(
    ['jquery-nos'],
    function($) {
        $(function() {
            var $container = $("#<?= $uniqid ?>").nosFormUI(),
                $form = $container.find('form'),
                $tweet = $('#<?= $uniqid_tweet ?>'),
                intent_url = false;

            $container.nosToolbar('create');

            $container.nosToolbar('add', $container.find('.nos-datacatchers-buttons')).click(function(e) {
                var $target = $(e.target);
                log($target);

                if ($target.parent().is(':button')) {
                    $form.submit();
                } elseif ($target.is('a')) {
                    $target.nosTabs('close');
                }

                return false;
            });
            $container.nosToolbar('add', $('#<?= $uniqid_tweet ?>').show());

            $form.bind('ajax_success', function(e, json) {
                intent_url = json.intent_url || false;
            });

            $tweet.click(function() {
                $form.one('ajax_success', function(json) {
                    intent_url && window.open(intent_url);
                });
                $form.submit();
            })

        });
    });
</script>
