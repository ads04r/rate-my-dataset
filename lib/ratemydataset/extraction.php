<?php

function extract_metadata($g)
{

	global $cfg;

	$subjects = array();
	$predicates = array();
	$types = array();

	foreach($g->allSubjects() as $res)
	{
		$uri = "" . $res;
		if(in_array($uri, $subjects)) { continue; }

		$subjects[] = $uri;
		foreach($res->relations() as $rel)
		{
			$uri = "" . $rel;
			if(in_array($uri, $predicates)) { continue; }

			$predicates[] = $uri;
		}
	}


	foreach($subjects as $uri)
	{
		$res = $g->resource($uri);
		foreach($res->all("rdf:type") as $type)
		{
			$type_uri = "" . $type;
			if(in_array($type_uri, $types)) { continue; }

			$types[] = $type_uri;
		}
	}

	sort($types);
	sort($subjects);
	sort($predicates);

	return(array(
		"types" => $types,
		"predicates" => $predicates,
		"subjects" => $subjects
	));
}


function extract_vocabularies($uri_list)
{
	$namespaces = array();
	$prefixes = array();
	foreach($uri_list as $type)
	{
		$ns = get_namespace($type);
		if(strlen($ns) == 0) { continue; }
		if(in_array($ns, $namespaces)) { continue; }

		$namespaces[] = $ns;
	}

	foreach($namespaces as $uri)
	{
		$prefix = get_prefix($uri);
		if(!(array_key_exists("prefix", $prefix))) { continue; }

		$prefixes[] = $prefix;
	}

	return($prefixes);
}

function extract_classes($type_list)
{
	$ret = array();
	foreach($type_list as $uri)
	{
		$g = new Graphite();
		$g->load($uri);
		$res = $g->resource($uri);
		$label = "" . $res->label();
		if(strcmp($label, "[NULL]") == 0)
		{
			$label = preg_replace("|^(.*)([/#])([^/#]+)$|", "$3", $uri);
		}

		$item = array();
		$item['label'] = $label;
		$item['uri'] = $uri;
		$ret[] = $item;
	}
	return($ret);
}
