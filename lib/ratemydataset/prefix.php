<?php

function get_namespace($uri)
{
	$ns = preg_replace("|^(.+)([/#])([^/#]*)$|", "$1$2", $uri);
	if(strcmp($ns, $uri) == 0) { return(""); }
	return($ns);
}

function get_redirect_target($url)
{
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_NOBODY, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$headers = curl_exec($ch);
	curl_close($ch);

	if (preg_match('/^Location: (.+)$/im', $headers, $matches)) { return trim($matches[1]); }
	return("");
}

function get_prefix($namespace)
{
	global $cfg;

	foreach($cfg['namespaces'] as $ns)
	{
		if(strcmp($ns['uri'], $namespace) != 0) { continue; }

		return($ns);
	}

	$prefix = "";
	$url = get_redirect_target("http://prefix.cc/?q=" . urlencode($namespace));
	if(strlen($url) > 0)
	{
		$prefix = preg_replace("|^(.*)/([^/]+)$|", "$2", $url);
	}

	$g = new Graphite();
	$g->load($namespace);
	$res = $g->resource($namespace);
	$label = "" . $res->label();
	if(strcmp($label, "[NULL]") == 0) { $label = ""; }

	$ret = array();
	if(strlen($label) > 0) { $ret['label'] = $label; }
	if(strlen($prefix) > 0) { $ret['prefix'] = $prefix; }
	$ret['uri'] = $namespace;
	return($ret);
}
