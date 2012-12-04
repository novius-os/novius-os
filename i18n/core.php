<?php


// dictionnary_name => rules
return array(
	'page' => array(
		'^((classes|config)/controller|views)/admin/page/',
		'^(classes|config)/model/page',
	),
	'media' => array(
		'^((classes|config)/controller|views)/admin/media/',
		'^(classes|config)/model/media',
	),
	'user' => array(
		'^((classes|config)/controller|views)/admin/user/',
		'^(classes|config)/model/user',
	),
    'orm' => array(
        '^classes/orm/',
        '^classes/model',
        //'(publishable|sharable|sortable|translatable|tree|virtualname|virtualpath)', // tree
    ),
    'application' => array(
        //'^views/(crud|form)',
        '^views/crud',
        '\Wappdesk[./]|\Wcrud[./]',
    ),
	// false == default dict
	'generic' => false,
);
