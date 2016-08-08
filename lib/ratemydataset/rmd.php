<?php

function generate_rmd($g)
{
	$data = extract_metadata($g);
	$data['vocabularies'] = extract_vocabularies(array_merge($data['predicates'], $data['types']));
	$data['classes'] = extract_classes($data['types']);
	$data['link_targets'] = extract_domains($data['objects']);

	return($data);
}
